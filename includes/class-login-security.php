<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Login_Security {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_filter('login_url', [$this, 'filter_login_url'], 20, 3);
        add_filter('lostpassword_url', [$this, 'filter_lostpassword_url'], 20, 2);
        add_filter('site_url', [$this, 'filter_site_url'], 20, 4);
        add_action('init', [$this, 'handle_login_security'], 1);
    }

    public function custom_login_enabled() {
        return (bool) wpbb_get_option('enable_custom_login_slug', 0);
    }

    public function wp_admin_redirect_enabled() {
        return (bool) wpbb_get_option('redirect_wp_admin_home', 0);
    }

    public function get_login_slug() {
        $slug = (string) wpbb_get_option('custom_login_slug', 'tfa-admin');
        $slug = trim($slug, "/ \t\n\r\0\x0B");
        $slug = sanitize_title($slug);
        return $slug ?: 'tfa-admin';
    }

    public function custom_login_url() {
        return home_url('/' . $this->get_login_slug() . '/');
    }

    private function is_direct_wp_login_request() {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        return strpos($request_uri, 'wp-login.php') !== false;
    }

    private function is_custom_login_request() {
        $request_path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
        $request_path = trim((string) $request_path, '/');
        return $request_path === trim((string) wp_parse_url($this->custom_login_url(), PHP_URL_PATH), '/');
    }

    public function filter_login_url($login_url, $redirect, $force_reauth) {
        if (!$this->custom_login_enabled()) {
            return $login_url;
        }
        $url = $this->custom_login_url();
        if ($redirect) {
            $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
        }
        if ($force_reauth) {
            $url = add_query_arg('reauth', '1', $url);
        }
        return $url;
    }

    public function filter_lostpassword_url($lostpassword_url, $redirect) {
        if (!$this->custom_login_enabled()) {
            return $lostpassword_url;
        }
        $url = add_query_arg('action', 'lostpassword', $this->custom_login_url());
        if ($redirect) {
            $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
        }
        return $url;
    }

    public function filter_site_url($url, $path, $scheme, $blog_id) {
        if (!$this->custom_login_enabled()) {
            return $url;
        }
        if (is_string($path) && strpos($path, 'wp-login.php') !== false) {
            $custom = $this->custom_login_url();
            $query = wp_parse_url($url, PHP_URL_QUERY);
            if ($query) {
                $custom .= (strpos($custom, '?') === false ? '?' : '&') . $query;
            }
            return $custom;
        }
        return $url;
    }

    public function handle_login_security() {
        if (is_user_logged_in()) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $request_path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
        $request_path = trim($request_path, '/');
        $custom_login = $this->custom_login_enabled();

        if ($request_path !== '' && strpos($request_path, 'wp-admin') === 0) {
            $allowed = [
                'wp-admin/admin-ajax.php',
                'wp-admin/admin-post.php',
                'wp-admin/async-upload.php',
            ];
            if (!in_array($request_path, $allowed, true) && ($this->wp_admin_redirect_enabled() || $custom_login) && !wp_doing_ajax()) {
                wp_safe_redirect(home_url('/'));
                exit;
            }
        }

        if (!$custom_login) {
            return;
        }

        if ($this->is_custom_login_request()) {
            require_once ABSPATH . 'wp-login.php';
            exit;
        }

        if ($this->is_direct_wp_login_request()) {
            wp_safe_redirect(home_url('/'));
            exit;
        }
    }
}

if (!function_exists('wpbb_custom_login_url')) {
    function wpbb_custom_login_url() {
        return WPBB_Login_Security::instance()->custom_login_url();
    }
}
