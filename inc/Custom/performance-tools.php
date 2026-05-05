<?php
if (!defined('ABSPATH')) { exit; }

if (!function_exists('wp_theme_perf_get')) {
    function wp_theme_perf_get($key, $default = '') {
        return function_exists('get_field') ? wp_theme_acf_get($key, 'option', $default) : $default;
    }
}

if (!function_exists('wp_theme_performance_actions_markup')) {
    function wp_theme_performance_actions_markup() {
        return '<p><a class="button button-secondary" href="' . esc_url(admin_url('admin-post.php?action=wp_theme_purge_cache')) . '">' . esc_html__('Clean Cache', 'wp-theme') . '</a> <a class="button button-secondary" href="' . esc_url(admin_url('admin-post.php?action=wp_theme_generate_critical_css')) . '">' . esc_html__('Generate Critical CSS', 'wp-theme') . '</a> <a class="button button-secondary" href="' . esc_url(admin_url('admin-post.php?action=wp_theme_optimize_options')) . '">' . esc_html__('Optimize Options', 'wp-theme') . '</a></p>';
    }
}

function wp_theme_perf_output_cache_headers() {
    if (is_admin() || is_user_logged_in() || !wp_theme_perf_get('perf_cache_headers', false)) {
        return;
    }
    if (!headers_sent()) {
        header('Cache-Control: public, max-age=300, stale-while-revalidate=60');
    }
}
add_action('send_headers', 'wp_theme_perf_output_cache_headers');

function wp_theme_perf_safe_minify_buffer($html) {
    if (stripos($html, '<pre') !== false || stripos($html, '<textarea') !== false) {
        return $html;
    }
    $html = preg_replace('/>\s+</', '><', $html);
    $html = preg_replace('/\s{2,}/', ' ', $html);
    return trim($html);
}
function wp_theme_perf_maybe_start_buffer() {
    if (is_admin() || is_user_logged_in() || !wp_theme_perf_get('perf_html_minify', false)) {
        return;
    }
    ob_start('wp_theme_perf_safe_minify_buffer');
}
add_action('template_redirect', 'wp_theme_perf_maybe_start_buffer', 0);

function wp_theme_generate_critical_css_file() {
    $css = ":root{--wp-theme-critical:1}.wp-theme-site-header{position:sticky;top:0;z-index:1000}.wp-theme-header-nav .dropdown-menu{display:none}.wp-theme-demo-homepage .wp-theme-demo-card,.wp-theme-demo-homepage .wp-theme-demo-stat{border-radius:20px}";
    $target = get_template_directory() . '/assets/css/critical-auto.css';
    wp_mkdir_p(dirname($target));
    file_put_contents($target, $css);
}

function wp_theme_perf_enqueue_critical_css() {
    $file = get_template_directory() . '/assets/css/critical-auto.css';
    if (wp_theme_perf_get('perf_auto_critical_css', false) && file_exists($file)) {
        wp_enqueue_style('wp-theme-critical-auto', get_template_directory_uri() . '/assets/css/critical-auto.css', ['wp-theme-style'], filemtime($file));
    }
}
add_action('wp_enqueue_scripts', 'wp_theme_perf_enqueue_critical_css', 5);

function wp_theme_perf_cache_key($suffix) {
    $lang = function_exists('pll_current_language') ? pll_current_language('slug') : 'default';
    return 'wp_theme_' . $lang . '_' . md5($suffix);
}

function wp_theme_purge_all_theme_cache() {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wp_theme_%' OR option_name LIKE '_transient_timeout_wp_theme_%'");
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    do_action('wp_theme_cache_purged');
}

function wp_theme_perf_admin_action_guard() {
    if (!current_user_can('manage_options')) {
        wp_die('Not allowed');
    }
}
add_action('admin_post_wp_theme_purge_cache', function(){ wp_theme_perf_admin_action_guard(); wp_theme_purge_all_theme_cache(); wp_safe_redirect(wp_get_referer() ?: admin_url()); exit; });
add_action('admin_post_wp_theme_generate_critical_css', function(){ wp_theme_perf_admin_action_guard(); wp_theme_generate_critical_css_file(); wp_safe_redirect(wp_get_referer() ?: admin_url()); exit; });
add_action('admin_post_wp_theme_optimize_options', function(){ wp_theme_perf_admin_action_guard(); global $wpdb; $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()"); wp_safe_redirect(wp_get_referer() ?: admin_url()); exit; });

add_action('save_post', 'wp_theme_purge_all_theme_cache');
add_action('wp_update_nav_menu', 'wp_theme_purge_all_theme_cache');
