<?php
/**
 * Plugin Name: WP BBuilder
 * Description: Lightweight Bootstrap-oriented Gutenberg blocks optimized for Core Web Vitals and modular front-end loading.
 * Version: 5.2.0
 * Author: Raivis Kalnins
 * Text Domain: wp-bbuilder
 */

if (!defined('ABSPATH')) exit;

define('WPBB_VERSION', '5.2.0');
define('WPBB_PLUGIN_FILE', __FILE__);
define('WPBB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPBB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPBB_TEXTDOMAIN', 'wp-bbuilder');

require_once WPBB_PLUGIN_DIR . 'includes/helpers.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-bootstrap.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-settings.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-admin.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-blocks.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-form-handler.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-acf.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-whatsapp.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-cookie-consent.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-spellcheck.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-ordering.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-login-security.php';

final class WP_BBuilder {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        WPBB_Settings::instance();
        WPBB_Admin::instance();
        WPBB_Blocks::instance();
        WPBB_Form_Handler::instance();
        WPBB_ACF::instance();
        WPBB_WhatsApp::instance();
        WPBB_Cookie_Consent::instance();
        WPBB_Spellcheck::instance();
        WPBB_Ordering::instance();
        WPBB_Login_Security::instance();
    }

    public function load_textdomain() {
        load_plugin_textdomain('wp-bbuilder', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
}

WP_BBuilder::instance();

if (!function_exists('wpbb_render_compiled_css')) {
    function wpbb_render_compiled_css() {
        $css = (string) wpbb_get_option('compiled_css', '');
        if ($css !== '') {
            echo '<style id="wpbb-compiled-css">' . $css . '</style>';
        }
    }
    add_action('wp_head', 'wpbb_render_compiled_css', 99);

    function wpbb_render_meta_header_code() {
        $code = (string) wpbb_get_option('meta_header_code', '');
        if ($code !== '') echo $code;
    }
    add_action('wp_head', 'wpbb_render_meta_header_code', 100);

    function wpbb_render_global_footer_code() {
        $code = (string) wpbb_get_option('global_footer_code', '');
        if ($code !== '') echo $code;
    }
    add_action('wp_footer', 'wpbb_render_global_footer_code', 100);
}


if (!function_exists('wpbb_render_aggregated_block_css')) {
    function wpbb_render_aggregated_block_css() {
        global $wpbb_inline_block_css_buffer;
        if (empty($wpbb_inline_block_css_buffer) || !is_array($wpbb_inline_block_css_buffer)) {
            return;
        }
        $css = trim(implode('', array_unique(array_filter($wpbb_inline_block_css_buffer))));
        if ($css !== '') {
            echo '<style id="wpbb-inline-block-css">' . $css . '</style>';
        }
    }
    add_action('wp_head', 'wpbb_render_aggregated_block_css', 98);
}


if (!function_exists('wpbb_render_frontend_container_width')) {
    function wpbb_render_frontend_container_width() {
        $max = wpbb_get_theme_container_width('1400px');
        if ($max === '') {
            return;
        }

        $css = '.wpbb-row > .container{max-width:' . $max . ';}';

        if (wpbb_get_option('aggregate_inline_block_css', 1)) {
            global $wpbb_inline_block_css_buffer;
            if (!isset($wpbb_inline_block_css_buffer) || !is_array($wpbb_inline_block_css_buffer)) {
                $wpbb_inline_block_css_buffer = [];
            }
            $wpbb_inline_block_css_buffer[] = $css;
            return;
        }

        echo '<style id="wpbb-container-width">' . $css . '</style>';
    }
    add_action('wp_head', 'wpbb_render_frontend_container_width', 97);
}


if (!function_exists('wpbb_render_admin_compiled_css')) {
    function wpbb_render_admin_compiled_css() {
        if (!is_admin()) return;
        $css = (string) wpbb_get_option('admin_compiled_css', '');
        if ($css !== '') {
            echo '<style id="wpbb-admin-compiled-css">' . $css . '</style>';
        }
    }
    add_action('admin_head', 'wpbb_render_admin_compiled_css', 99);
}


if (!function_exists('wpbb_force_bootstrap_enqueue')) {
    function wpbb_force_bootstrap_enqueue() {
        if (is_admin()) return;
        if (!wpbb_get_option('force_bootstrap_enqueue', 0)) return;
        WPBBuilder_Bootstrap::enqueue_css();
        WPBBuilder_Bootstrap::enqueue_js_if_needed();
    }
    add_action('wp_enqueue_scripts', 'wpbb_force_bootstrap_enqueue', 1);
}



if (!function_exists('wpbb_bootstrap_debug_feature_enabled')) {
    function wpbb_bootstrap_debug_feature_enabled() {
        return !is_admin() && !wp_doing_ajax() && !wp_is_json_request() && !empty(wpbb_get_option('show_bootstrap_debug', 0));
    }
}

if (!function_exists('wpbb_bootstrap_debug_visible_for_current_user')) {
    function wpbb_bootstrap_debug_visible_for_current_user() {
        if (!wpbb_bootstrap_debug_feature_enabled()) return false;
        if (!is_user_logged_in() || !current_user_can('manage_options')) return false;
        $hidden = get_user_meta(get_current_user_id(), 'wpbb_bootstrap_debug_hidden', true);
        return $hidden !== '1';
    }
}

if (!function_exists('wpbb_handle_bootstrap_debug_toggle')) {
    function wpbb_handle_bootstrap_debug_toggle() {
        if (!wpbb_bootstrap_debug_feature_enabled()) return;
        if (!is_user_logged_in() || !current_user_can('manage_options')) return;
        if (empty($_GET['wpbb_bootstrap_debug_toggle'])) return;
        if (empty($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wpbb_bootstrap_debug_toggle')) return;

        $action = sanitize_key(wp_unslash($_GET['wpbb_bootstrap_debug_toggle']));
        $user_id = get_current_user_id();
        $hidden = get_user_meta($user_id, 'wpbb_bootstrap_debug_hidden', true) === '1';

        if ($action === 'on') {
            update_user_meta($user_id, 'wpbb_bootstrap_debug_hidden', '0');
        } elseif ($action === 'off') {
            update_user_meta($user_id, 'wpbb_bootstrap_debug_hidden', '1');
        } else {
            update_user_meta($user_id, 'wpbb_bootstrap_debug_hidden', $hidden ? '0' : '1');
        }

        $target = remove_query_arg(['wpbb_bootstrap_debug_toggle', '_wpnonce']);
        wp_safe_redirect($target ?: home_url('/'));
        exit;
    }
    add_action('template_redirect', 'wpbb_handle_bootstrap_debug_toggle', 1);
}

if (!function_exists('wpbb_add_bootstrap_debug_admin_bar')) {
    function wpbb_add_bootstrap_debug_admin_bar($wp_admin_bar) {
        if (!wpbb_bootstrap_debug_feature_enabled()) return;
        if (!is_user_logged_in() || !current_user_can('manage_options')) return;

        $is_visible = wpbb_bootstrap_debug_visible_for_current_user();
        $toggle_url = wp_nonce_url(add_query_arg('wpbb_bootstrap_debug_toggle', $is_visible ? 'off' : 'on'), 'wpbb_bootstrap_debug_toggle');
        $settings_url = admin_url('admin.php?page=bbbuilder-settings#core');

        $wp_admin_bar->add_node([
            'id' => 'wpbb-bootstrap-debug',
            'title' => sprintf('%s: %s', __('Bootstrap Debug', 'wp-bbuilder'), $is_visible ? __('On', 'wp-bbuilder') : __('Off', 'wp-bbuilder')),
            'href' => $toggle_url,
            'meta' => ['title' => __('Toggle Bootstrap debug panel', 'wp-bbuilder')],
        ]);

        $wp_admin_bar->add_node([
            'id' => 'wpbb-bootstrap-debug-toggle',
            'parent' => 'wpbb-bootstrap-debug',
            'title' => $is_visible ? __('Hide debug panel', 'wp-bbuilder') : __('Show debug panel', 'wp-bbuilder'),
            'href' => $toggle_url,
        ]);

        $wp_admin_bar->add_node([
            'id' => 'wpbb-bootstrap-debug-settings',
            'parent' => 'wpbb-bootstrap-debug',
            'title' => __('Open BbBuilder settings', 'wp-bbuilder'),
            'href' => $settings_url,
        ]);
    }
    add_action('admin_bar_menu', 'wpbb_add_bootstrap_debug_admin_bar', 999);
}

if (!function_exists('wpbb_render_bootstrap_debug_panel')) {
    function wpbb_render_bootstrap_debug_panel() {
        if (!wpbb_bootstrap_debug_visible_for_current_user()) return;

        $report = WPBBuilder_Bootstrap::get_debug_report();
        $mapping = $report['mapping'] ?? [];
        $render_list = static function ($items, $empty = 'none') {
            $items = array_values(array_filter(array_map('strval', (array) $items)));
            if (empty($items)) {
                return $empty;
            }
            return implode(', ', $items);
        };

        $style = '#wpbb-bootstrap-debug{position:fixed;right:16px;bottom:16px;z-index:99999;max-width:440px;width:min(440px,calc(100vw - 32px));font:13px/1.45 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;color:#e5e7eb}';
        $style .= '#wpbb-bootstrap-debug details{background:#111827;border:1px solid #374151;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.35);overflow:hidden}';
        $style .= '#wpbb-bootstrap-debug summary{cursor:pointer;list-style:none;padding:12px 14px;font-weight:600;background:#0f172a;color:#fff}';
        $style .= '#wpbb-bootstrap-debug summary::-webkit-details-marker{display:none}';
        $style .= '#wpbb-bootstrap-debug .wpbb-bootstrap-debug__body{padding:12px 14px;max-height:60vh;overflow:auto}';
        $style .= '#wpbb-bootstrap-debug .wpbb-bootstrap-debug__row{margin:0 0 10px}';
        $style .= '#wpbb-bootstrap-debug .wpbb-bootstrap-debug__label{display:block;font-size:11px;letter-spacing:.04em;text-transform:uppercase;color:#93c5fd;margin-bottom:2px}';
        $style .= '#wpbb-bootstrap-debug code{color:#fef3c7;font-size:12px}';
        $style .= '#wpbb-bootstrap-debug .wpbb-bootstrap-debug__meta{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}';

        echo '<style id="wpbb-bootstrap-debug-style">' . $style . '</style>';
        echo '<div id="wpbb-bootstrap-debug">';
        echo '<details open>';
        echo '<summary>' . esc_html__('Bootstrap debug', 'wp-bbuilder') . '</summary>';
        echo '<div class="wpbb-bootstrap-debug__body">';
        echo '<div class="wpbb-bootstrap-debug__meta">';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('CSS mode', 'wp-bbuilder') . '</span><code>' . esc_html((string) ($report['css_mode'] ?? 'auto')) . '</code></div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('JS mode', 'wp-bbuilder') . '</span><code>' . esc_html((string) ($report['js_mode'] ?? 'auto')) . '</code></div>';
        echo '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Detection engine', 'wp-bbuilder') . '</span>' . esc_html((string) ($mapping['engine'] ?? 'per-block-map')) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Loaded CSS', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['final_css'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Loaded JS', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['final_js'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Detected blocks', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['detected']['blocks'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Mapped CSS', 'wp-bbuilder') . '</span>' . esc_html($render_list($mapping['mapped_css'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Mapped JS', 'wp-bbuilder') . '</span>' . esc_html($render_list($mapping['mapped_js'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Mapped blocks', 'wp-bbuilder') . '</span>' . esc_html($render_list($mapping['mapped_blocks'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Extra CSS from settings', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['selected_css'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Extra JS from settings', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['selected_js'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Runtime JS requested by blocks', 'wp-bbuilder') . '</span>' . esc_html($render_list($report['runtime_js'] ?? [])) . '</div>';
        echo '<div class="wpbb-bootstrap-debug__row"><span class="wpbb-bootstrap-debug__label">' . esc_html__('Flags', 'wp-bbuilder') . '</span>' . esc_html(sprintf('load_css=%s, load_js=%s, force_all=%s', !empty($report['load_bootstrap_css']) ? 'yes' : 'no', !empty($report['load_bootstrap_js']) ? 'yes' : 'no', !empty($report['force_bootstrap_enqueue']) ? 'yes' : 'no')) . '</div>';
        echo '</div>';
        echo '</details>';
        echo '</div>';

        echo "
<!-- WP BBuilder Bootstrap Debug
";
        echo 'CSS mode: ' . esc_html((string) ($report['css_mode'] ?? 'auto')) . "
";
        echo 'JS mode: ' . esc_html((string) ($report['js_mode'] ?? 'auto')) . "
";
        echo 'Detection engine: ' . esc_html((string) ($mapping['engine'] ?? 'per-block-map')) . "
";
        echo 'Loaded CSS: ' . esc_html($render_list($report['final_css'] ?? [])) . "
";
        echo 'Loaded JS: ' . esc_html($render_list($report['final_js'] ?? [])) . "
";
        echo 'Detected blocks: ' . esc_html($render_list($report['detected']['blocks'] ?? [])) . "
";
        echo 'Mapped CSS: ' . esc_html($render_list($mapping['mapped_css'] ?? [])) . "
";
        echo 'Mapped JS: ' . esc_html($render_list($mapping['mapped_js'] ?? [])) . "
";
        echo 'Mapped blocks: ' . esc_html($render_list($mapping['mapped_blocks'] ?? [])) . "
";
        echo 'Extra CSS from settings: ' . esc_html($render_list($report['selected_css'] ?? [])) . "
";
        echo 'Extra JS from settings: ' . esc_html($render_list($report['selected_js'] ?? [])) . "
";
        echo 'Runtime JS requested by blocks: ' . esc_html($render_list($report['runtime_js'] ?? [])) . "
";
        echo '-->' . "
";
    }
    add_action('wp_footer', 'wpbb_render_bootstrap_debug_panel', 999);
}

if (!function_exists('wpbb_normalize_redirect_path')) {
    function wpbb_normalize_redirect_path($value) {
        $value = trim((string) $value);
        if ($value === '') return '/';

        $path = (string) wp_parse_url($value, PHP_URL_PATH);
        if ($path === '') {
            $path = $value;
        }

        $path = '/' . ltrim(urldecode($path), '/');
        $path = preg_replace('#/+#', '/', $path);
        $path = untrailingslashit($path);

        return $path === '' ? '/' : $path;
    }
}

if (!function_exists('wpbb_handle_page_redirects')) {
    function wpbb_handle_page_redirects() {
        if (is_admin()) return;

        $rules = json_decode((string) wpbb_get_option('page_redirect_rules', '[]'), true);
        if (!is_array($rules) || empty($rules)) return;

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '';
        $current_path = wpbb_normalize_redirect_path($request_uri);
        $current_url  = home_url($request_uri);

        foreach ($rules as $rule) {
            $from = isset($rule['from']) ? trim((string) $rule['from']) : '';
            $to = isset($rule['to']) ? trim((string) $rule['to']) : '';
            if ($from === '' || $to === '') continue;

            $from_path = wpbb_normalize_redirect_path($from);
            $from_url  = preg_match('#^https?://#i', $from) ? $from : home_url('/' . ltrim($from, '/'));
            $status    = in_array((int) ($rule['code'] ?? 301), [301, 302], true) ? (int) $rule['code'] : 301;
            $target    = preg_match('#^https?://#i', $to) ? $to : home_url('/' . ltrim($to, '/'));

            $path_match = ($from_path === $current_path);
            $url_match  = (untrailingslashit($from_url) === untrailingslashit($current_url));

            if (!$path_match && !$url_match) {
                continue;
            }

            if (untrailingslashit($target) === untrailingslashit($current_url)) {
                continue;
            }

            nocache_headers();
            wp_redirect($target, $status);
            exit;
        }
    }
    add_action('template_redirect', 'wpbb_handle_page_redirects', 0);
}

if (function_exists('register_block_style')) {
    register_block_style('core/list', ['name' => 'tick-list', 'label' => __('Tick List', 'wp-theme')]);
    register_block_style('core/list', ['name' => 'contact-details-white', 'label' => __('Contact White', 'wp-theme')]);
    register_block_style('core/list', ['name' => 'no-bullets', 'label' => __('No bullets', 'wp-theme')]);
}

if (!function_exists('wpbb_quick_edit_feature_enabled')) {
    function wpbb_quick_edit_feature_enabled() {
        return !is_admin() && !wp_doing_ajax() && !wp_is_json_request() && !empty(wpbb_get_option('show_quick_edit_toggle', 0));
    }
}

if (!function_exists('wpbb_get_quick_edit_post_id')) {
    function wpbb_get_quick_edit_post_id() {
        if (!is_singular()) {
            return 0;
        }
        $post_id = function_exists('get_queried_object_id') ? (int) get_queried_object_id() : 0;
        if (!$post_id) {
            global $post;
            $post_id = isset($post->ID) ? (int) $post->ID : 0;
        }
        return $post_id;
    }
}

if (!function_exists('wpbb_can_show_quick_edit_toggle')) {
    function wpbb_can_show_quick_edit_toggle() {
        if (!wpbb_quick_edit_feature_enabled()) {
            return false;
        }
        if (!is_user_logged_in()) {
            return false;
        }
        $post_id = wpbb_get_quick_edit_post_id();
        if (!$post_id) {
            return false;
        }
        $post_type = get_post_type($post_id);
        if (!$post_type) {
            return false;
        }
        $obj = get_post_type_object($post_type);
        if (!$obj || empty($obj->public)) {
            return false;
        }
        return current_user_can('edit_post', $post_id);
    }
}

if (!function_exists('wpbb_render_quick_edit_toggle')) {
    function wpbb_render_quick_edit_toggle() {
        if (!wpbb_can_show_quick_edit_toggle()) {
            return;
        }

        $post_id = wpbb_get_quick_edit_post_id();
        $edit_url = get_edit_post_link($post_id, '');
        if (!$edit_url) {
            return;
        }

        $post_type = get_post_type($post_id);
        $post_type_obj = $post_type ? get_post_type_object($post_type) : null;
        $singular_label = $post_type_obj && !empty($post_type_obj->labels->singular_name)
            ? $post_type_obj->labels->singular_name
            : __('Content', 'wp-bbuilder');

        $target = !empty(wpbb_get_option('quick_edit_new_tab', 1)) ? '_blank' : '_self';
        $rel = $target === '_blank' ? 'noopener noreferrer' : 'noopener';
        $style = '#wpbb-quick-edit-toggle{position:fixed;left:0;top:50%;transform:translateY(-50%);z-index:99998;font:13px/1.45 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif}';
        $style .= '#wpbb-quick-edit-toggle details{display:flex;align-items:center}';
        $style .= '#wpbb-quick-edit-toggle summary{list-style:none;cursor:pointer;background:#111827;color:#fff;padding:14px 10px;border-radius:0 12px 12px 0;box-shadow:0 8px 24px rgba(0,0,0,.22);font-weight:700;letter-spacing:.04em;writing-mode:vertical-rl;text-orientation:mixed}';
        $style .= '#wpbb-quick-edit-toggle summary::-webkit-details-marker{display:none}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__panel{margin-left:10px;min-width:220px;background:#fff;border:1px solid #d1d5db;border-radius:14px;box-shadow:0 12px 28px rgba(15,23,42,.22);padding:14px}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__eyebrow{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;margin-bottom:6px}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__title{display:block;font-size:14px;font-weight:700;color:#111827;margin-bottom:10px}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__button{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:999px;background:#2563eb;color:#fff;text-decoration:none;font-weight:700}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__button:hover{background:#1d4ed8;color:#fff}';
        $style .= '#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__hint{margin-top:8px;font-size:12px;color:#6b7280}';
        $style .= '@media (max-width:782px){#wpbb-quick-edit-toggle{top:auto;bottom:18px;transform:none}#wpbb-quick-edit-toggle summary{writing-mode:horizontal-tb;padding:12px 14px;border-radius:0 999px 999px 0}#wpbb-quick-edit-toggle .wpbb-quick-edit-toggle__panel{min-width:200px}}';

        echo '<style id="wpbb-quick-edit-toggle-style">' . $style . '</style>';
        echo '<div id="wpbb-quick-edit-toggle">';
        echo '<details>';
        echo '<summary>' . esc_html__('Edit', 'wp-bbuilder') . '</summary>';
        echo '<div class="wpbb-quick-edit-toggle__panel">';
        echo '<span class="wpbb-quick-edit-toggle__eyebrow">' . esc_html__('Quick edit', 'wp-bbuilder') . '</span>';
        echo '<span class="wpbb-quick-edit-toggle__title">' . esc_html(sprintf(__('Edit this %s', 'wp-bbuilder'), $singular_label)) . '</span>';
        echo '<a class="wpbb-quick-edit-toggle__button" href="' . esc_url($edit_url) . '" target="' . esc_attr($target) . '" rel="' . esc_attr($rel) . '">' . esc_html__('Open editor', 'wp-bbuilder') . '</a>';
        echo '<div class="wpbb-quick-edit-toggle__hint">' . esc_html($target === '_blank' ? __('Opens in a new tab.', 'wp-bbuilder') : __('Opens in the current tab.', 'wp-bbuilder')) . '</div>';
        echo '</div>';
        echo '</details>';
        echo '</div>';
    }
    add_action('wp_footer', 'wpbb_render_quick_edit_toggle', 998);
}
