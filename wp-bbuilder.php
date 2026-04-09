<?php
/**
 * Plugin Name: WP BBuilder
 * Description: Lightweight Bootstrap-oriented Gutenberg blocks optimized for Core Web Vitals and modular front-end loading.
 * Version: 4.9.3
 * Author: Raivis Kalnins
 * Text Domain: wp-bbuilder
 */

if (!defined('ABSPATH')) exit;

define('WPBB_VERSION', '4.9.3');
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
        if (wpbb_get_option('load_bootstrap_css', 1)) {
            wp_enqueue_style('wpbb-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        }
        if (wpbb_get_option('load_bootstrap_js', 0)) {
            wp_enqueue_script('wpbb-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
        }
    }
    add_action('wp_enqueue_scripts', 'wpbb_force_bootstrap_enqueue', 1);
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