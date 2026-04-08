<?php
if (!defined('ABSPATH')) exit;

final class WPBBuilder_Bootstrap {
    private static $components = [];
    private static $registered = false;

    public static function needs($components) {
        foreach ((array) $components as $component) {
            $component = sanitize_key($component);
            if ($component !== '') self::$components[$component] = true;
        }
    }

    public static function register_assets() {
        if (self::$registered) return;
        $version = '5.3.3';
        $local_css = WPBB_PLUGIN_DIR . 'assets/bootstrap/5.3/css/bootstrap.min.css';
        $local_js = WPBB_PLUGIN_DIR . 'assets/bootstrap/5.3/js/bootstrap.bundle.min.js';

        wp_register_style('wpbb-critical', false, [], WPBB_VERSION);
        wp_register_style('wpbb-bootstrap-full', file_exists($local_css) ? WPBB_PLUGIN_URL . 'assets/bootstrap/5.3/css/bootstrap.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/css/bootstrap.min.css', [], $version);
        wp_register_style('wpbb-bootstrap-grid', 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/css/bootstrap-grid.min.css', [], $version);
        wp_register_style('wpbb-bootstrap-utilities', 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/css/bootstrap-utilities.min.css', [], $version);
        wp_register_style('wpbb-bootstrap-reboot', 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/css/bootstrap-reboot.min.css', [], $version);
        wp_register_script('wpbb-bootstrap-bundle', file_exists($local_js) ? WPBB_PLUGIN_URL . 'assets/bootstrap/5.3/js/bootstrap.bundle.min.js' : 'https://cdn.jsdelivr.net/npm/bootstrap@' . $version . '/dist/js/bootstrap.bundle.min.js', [], $version, true);

        self::$registered = true;
    }

    public static function enqueue_critical() {
        self::register_assets();
        wp_enqueue_style('wpbb-critical');
        $critical_path = WPBB_PLUGIN_DIR . 'assets/css/bb-critical.css';
        if (file_exists($critical_path)) {
            $critical = trim((string) file_get_contents($critical_path));
            if ($critical !== '') wp_add_inline_style('wpbb-critical', $critical);
        }
    }

    public static function enqueue_css() {
        self::register_assets();
        self::enqueue_critical();
        if (!wpbb_get_option('load_bootstrap_css', 1)) return;

        $mode = wpbb_get_option('bootstrap_css_mode', 'full');
        if ($mode === 'custom' || $mode === 'grid') {
            wp_enqueue_style('wpbb-bootstrap-reboot');
            wp_enqueue_style('wpbb-bootstrap-grid');
        } elseif ($mode === 'utilities') {
            wp_enqueue_style('wpbb-bootstrap-reboot');
            wp_enqueue_style('wpbb-bootstrap-utilities');
        } else {
            wp_enqueue_style('wpbb-bootstrap-full');
        }
    }

    public static function enqueue_js_if_needed() {
        self::register_assets();
        if (!wpbb_get_option('load_bootstrap_js', 0) && empty(self::$components)) return;
        wp_enqueue_script('wpbb-bootstrap-bundle');
    }
}
