<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Cookie_Consent {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_head', [$this, 'maybe_output_google_analytics'], 1);
        add_action('wp_footer', [$this, 'render_banner'], 100);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
    }

    public function assets() {
        wp_enqueue_script('wpbb-cookie-consent', WPBB_PLUGIN_URL . 'assets/cookie-consent.js', [], WPBB_VERSION, true);
        wp_localize_script('wpbb-cookie-consent', 'wpbbCookieConsent', [
            'enabled' => (bool) wpbb_get_option('cookie_consent_enabled', 0),
        ]);
    }

    public function maybe_output_google_analytics() {
        if (!wpbb_get_option('google_analytics_enabled', 0)) return;
        $code = trim((string) wpbb_get_option('google_analytics_head', ''));
        if (!$code) return;
        echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function render_banner() {
        if (!wpbb_get_option('cookie_consent_enabled', 0)) return;

        $text = wpbb_get_option('cookie_consent_text', 'We use cookies to improve your experience.');
        $accept = wpbb_get_option('cookie_accept_text', 'Accept');
        $reject = wpbb_get_option('cookie_reject_text', 'Reject');
        $policy = wpbb_get_option('cookie_policy_url', '');
        $position = wpbb_get_option('cookie_position', 'bottom');

        $pos_style = 'left:20px;right:20px;bottom:20px;';
        if ($position === 'top') $pos_style = 'left:20px;right:20px;top:20px;';

        $bg = wpbb_get_option('cookie_bg', '#111827');
        $text_color = wpbb_get_option('cookie_text_color', '#ffffff');
        $btn_bg = wpbb_get_option('cookie_button_bg', '#2563eb');
        $btn_text = wpbb_get_option('cookie_button_text', '#ffffff');

        echo '<div class="wpbb-cookie-consent" data-wpbb-cookie-banner="1" style="' . esc_attr($pos_style) . 'background:' . esc_attr($bg) . ';color:' . esc_attr($text_color) . ';">';
        echo '<div class="wpbb-cookie-consent__text">' . esc_html($text) . '</div>';
        echo '<div class="wpbb-cookie-consent__actions">';
        if ($policy) {
            echo '<a class="wpbb-cookie-consent__link" href="' . esc_url($policy) . '">' . esc_html__('Policy', 'wp-bbuilder') . '</a>';
        }
        echo '<button type="button" class="wpbb-cookie-consent__btn wpbb-cookie-consent__btn--reject" data-wpbb-cookie-reject="1">' . esc_html($reject) . '</button>';
        echo '<button type="button" class="wpbb-cookie-consent__btn wpbb-cookie-consent__btn--accept" data-wpbb-cookie-accept="1" style="background:' . esc_attr($btn_bg) . ';color:' . esc_attr($btn_text) . ';">' . esc_html($accept) . '</button>';
        echo '</div></div>';
    }
}
