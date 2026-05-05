<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Ordering {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_ordering_support'], 30);
        add_action('pre_get_posts', [$this, 'apply_admin_order']);
        add_filter('manage_posts_columns', [$this, 'add_order_column']);
        add_filter('manage_pages_columns', [$this, 'add_order_column']);
        add_action('manage_posts_custom_column', [$this, 'render_order_column'], 10, 2);
        add_action('manage_pages_custom_column', [$this, 'render_order_column'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_wpbb_save_sort_order', [$this, 'ajax_save_sort_order']);
    }

    private function enabled() {
        return (bool) wpbb_get_option('ordering_enabled', 1);
    }

    public function get_enabled_post_types() {
        $selected = wpbb_get_option('ordering_post_types', []);
        $available = get_post_types(['show_ui' => true], 'names');

        if (!is_array($selected) || empty($selected)) {
            $selected = [];
            foreach (['page', 'post', 'product'] as $post_type) {
                if (in_array($post_type, $available, true)) {
                    $selected[$post_type] = 1;
                }
            }
        }

        $enabled = [];
        foreach ($selected as $post_type => $is_enabled) {
            $post_type = sanitize_key($post_type);
            if ($is_enabled && in_array($post_type, $available, true)) {
                $enabled[] = $post_type;
            }
        }

        return array_values(array_unique($enabled));
    }

    public function is_enabled_post_type($post_type) {
        return $this->enabled() && in_array($post_type, $this->get_enabled_post_types(), true);
    }

    public function register_ordering_support() {
        if (!$this->enabled()) return;

        foreach ($this->get_enabled_post_types() as $post_type) {
            add_post_type_support($post_type, 'page-attributes');
        }
    }

    public function apply_admin_order($query) {
        if (!is_admin() || !$query->is_main_query()) return;

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $post_type = $screen && !empty($screen->post_type) ? $screen->post_type : ($query->get('post_type') ?: 'post');
        if (is_array($post_type)) return;
        if (!$this->is_enabled_post_type($post_type)) return;
        if (!empty($_GET['orderby'])) return;

        $query->set('orderby', ['menu_order' => 'ASC', 'date' => 'DESC', 'ID' => 'DESC']);
    }

    public function add_order_column($columns) {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || empty($screen->post_type) || !$this->is_enabled_post_type($screen->post_type)) {
            return $columns;
        }

        $new = [];
        foreach ($columns as $key => $label) {
            if ($key === 'cb') {
                $new[$key] = $label;
                $new['wpbb_order_handle'] = '<span class="dashicons dashicons-move" aria-hidden="true"></span>';
                continue;
            }
            $new[$key] = $label;
        }

        return $new;
    }

    public function render_order_column($column, $post_id) {
        if ($column !== 'wpbb_order_handle') return;
        echo '<span class="wpbb-order-drag-handle" title="' . esc_attr__('Drag to reorder', 'wp-bbuilder') . '"><span class="dashicons dashicons-move"></span></span>';
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'edit.php' || !$this->enabled()) return;

        $screen = get_current_screen();
        if (!$screen || empty($screen->post_type) || !$this->is_enabled_post_type($screen->post_type)) return;
        if (!current_user_can('edit_others_posts') && !current_user_can('edit_pages')) return;

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script(
            'wpbb-admin-ordering',
            WPBB_PLUGIN_URL . 'assets/admin-ordering.js',
            ['jquery', 'jquery-ui-sortable'],
            WPBB_VERSION,
            true
        );
        wp_enqueue_style('wpbb-admin-ordering', WPBB_PLUGIN_URL . 'assets/admin-ordering.css', [], WPBB_VERSION);

        wp_localize_script('wpbb-admin-ordering', 'wpbbOrdering', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpbb_ordering_nonce'),
            'postType' => $screen->post_type,
            'saving' => __('Saving order...', 'wp-bbuilder'),
            'saved' => __('Order saved.', 'wp-bbuilder'),
            'error' => __('Could not save the order. Please refresh and try again.', 'wp-bbuilder'),
        ]);
    }

    public function ajax_save_sort_order() {
        if (!current_user_can('edit_others_posts') && !current_user_can('edit_pages')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-bbuilder')], 403);
        }

        check_ajax_referer('wpbb_ordering_nonce', 'nonce');

        $post_type = sanitize_key($_POST['postType'] ?? '');
        if (!$this->is_enabled_post_type($post_type)) {
            wp_send_json_error(['message' => __('Ordering is disabled for this post type.', 'wp-bbuilder')], 400);
        }

        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('absint', $_POST['ids']) : [];
        $ids = array_values(array_filter($ids));

        if (!$ids) {
            wp_send_json_error(['message' => __('No items supplied.', 'wp-bbuilder')], 400);
        }

        foreach ($ids as $index => $post_id) {
            $post = get_post($post_id);
            if (!$post || $post->post_type !== $post_type) continue;
            wp_update_post([
                'ID' => $post_id,
                'menu_order' => $index,
            ]);
        }

        clean_post_cache($ids[0]);
        wp_send_json_success(['message' => __('Order saved.', 'wp-bbuilder')]);
    }
}
