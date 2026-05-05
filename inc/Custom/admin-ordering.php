<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('wp_theme_drag_order_enabled')) {
    function wp_theme_drag_order_enabled() {
        return (bool) wp_theme_acf_get('theme_enable_drag_drop_ordering', 'option', 0);
    }
}

if (!function_exists('wp_theme_orderable_post_types')) {
    function wp_theme_orderable_post_types() {
        $objects = get_post_types(['show_ui' => true], 'objects');
        $blocked = ['attachment','revision','nav_menu_item','custom_css','customize_changeset','oembed_cache','user_request','wp_navigation'];
        $types = [];
        foreach ($objects as $name => $object) {
            if (in_array($name, $blocked, true)) {
                continue;
            }
            if (empty($object->public) && !in_array($name, ['page', 'post'], true)) {
                continue;
            }
            $types[] = $name;
        }
        return array_values(array_unique($types));
    }
}

if (!function_exists('wp_theme_current_list_post_type')) {
    function wp_theme_current_list_post_type() {
        if (!is_admin()) {
            return 'post';
        }
        $post_type = isset($_GET['post_type']) ? sanitize_key((string) $_GET['post_type']) : 'post';
        return $post_type ?: 'post';
    }
}

add_action('init', function () {
    foreach (wp_theme_orderable_post_types() as $post_type) {
        add_post_type_support($post_type, 'page-attributes');
    }
}, 30);

add_action('pre_get_posts', function ($query) {
    if (!$query instanceof WP_Query || !$query->is_main_query()) {
        return;
    }

    $supported = wp_theme_orderable_post_types();

    if (is_admin()) {
        if (!wp_theme_drag_order_enabled()) {
            return;
        }
        global $pagenow;
        if ($pagenow !== 'edit.php') {
            return;
        }
        if ($query->get('orderby') || $query->get('s')) {
            return;
        }
        $post_type = $query->get('post_type') ?: 'post';
        if (!in_array($post_type, $supported, true)) {
            return;
        }
        $query->set('orderby', 'menu_order title');
        $query->set('order', 'ASC');
        return;
    }

    $post_type = $query->get('post_type');
    if (empty($post_type)) {
        $post_type = 'post';
    }
    if (is_array($post_type)) {
        if (!array_intersect($post_type, $supported)) {
            return;
        }
    } elseif (!in_array($post_type, $supported, true)) {
        return;
    }
    $query->set('orderby', 'menu_order date');
    $query->set('order', 'ASC');
}, 20);

if (!function_exists('wp_theme_add_order_column')) {
    function wp_theme_add_order_column($columns, $post_type = 'post') {
        if (!wp_theme_drag_order_enabled() || !in_array($post_type, wp_theme_orderable_post_types(), true)) {
            return $columns;
        }
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'cb') {
                $new['wp_theme_order'] = __('Order', 'wp-theme');
            }
        }
        if (!isset($new['wp_theme_order'])) {
            $new = ['wp_theme_order' => __('Order', 'wp-theme')] + $new;
        }
        return $new;
    }
}
add_filter('manage_posts_columns', 'wp_theme_add_order_column', 10, 2);
add_filter('manage_pages_columns', function ($columns) {
    return wp_theme_add_order_column($columns, 'page');
}, 10, 1);

if (!function_exists('wp_theme_render_order_column')) {
    function wp_theme_render_order_column($column, $post_id) {
        $post_type = get_post_type($post_id);
        if ($column !== 'wp_theme_order' || !wp_theme_drag_order_enabled() || !in_array($post_type, wp_theme_orderable_post_types(), true)) {
            return;
        }
        echo '<span class="wp-theme-order-handle" title="' . esc_attr__('Drag to reorder', 'wp-theme') . '">↕</span>';
    }
}
add_action('manage_posts_custom_column', 'wp_theme_render_order_column', 10, 2);
add_action('manage_pages_custom_column', 'wp_theme_render_order_column', 10, 2);

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'edit.php' || !wp_theme_drag_order_enabled()) {
        return;
    }
    $post_type = wp_theme_current_list_post_type();
    if (!in_array($post_type, wp_theme_orderable_post_types(), true)) {
        return;
    }
    wp_enqueue_script('jquery-ui-sortable');
    $file = get_template_directory() . '/assets/js/admin-ordering.js';
    if (file_exists($file)) {
        wp_enqueue_script('wp-theme-admin-ordering', get_template_directory_uri() . '/assets/js/admin-ordering.js', ['jquery', 'jquery-ui-sortable'], filemtime($file), true);
        wp_localize_script('wp-theme-admin-ordering', 'WPThemeOrdering', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_theme_save_order'),
            'postType' => $post_type,
            'messages' => [
                'saving' => __('Saving order…', 'wp-theme'),
                'saved' => __('Order saved.', 'wp-theme'),
                'error' => __('Could not save order.', 'wp-theme'),
            ],
        ]);
    }
});

add_action('admin_notices', function () {
    global $pagenow;
    if ($pagenow !== 'edit.php' || !wp_theme_drag_order_enabled()) {
        return;
    }
    $post_type = wp_theme_current_list_post_type();
    if (!in_array($post_type, wp_theme_orderable_post_types(), true)) {
        return;
    }
    echo '<div class="notice notice-info inline"><p>' . esc_html__('Drag rows with the grip handle to save a custom admin and frontend order.', 'wp-theme') . '</p></div>';
});

add_action('wp_ajax_wp_theme_save_menu_order', function () {
    check_ajax_referer('wp_theme_save_order', 'nonce');

    $post_type = sanitize_key((string) ($_POST['post_type'] ?? 'post'));
    if (!in_array($post_type, wp_theme_orderable_post_types(), true)) {
        wp_send_json_error(['message' => __('Invalid post type.', 'wp-theme')], 400);
    }

    $ptype_object = get_post_type_object($post_type);
    $capability = $ptype_object && !empty($ptype_object->cap->edit_posts) ? $ptype_object->cap->edit_posts : 'edit_posts';
    if (!current_user_can($capability)) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-theme')], 403);
    }

    $ids = isset($_POST['ids']) ? (array) $_POST['ids'] : [];
    $ids = array_values(array_filter(array_map('absint', $ids)));
    if (!$ids) {
        wp_send_json_error(['message' => __('No posts received.', 'wp-theme')], 400);
    }

    foreach ($ids as $menu_order => $post_id) {
        if (get_post_type($post_id) !== $post_type) {
            continue;
        }
        wp_update_post([
            'ID' => $post_id,
            'menu_order' => (int) $menu_order,
        ]);
        clean_post_cache($post_id);
    }

    wp_send_json_success(['message' => __('Order saved.', 'wp-theme')]);
});
