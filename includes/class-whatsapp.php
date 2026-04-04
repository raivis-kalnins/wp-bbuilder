<?php
if (!defined('ABSPATH')) exit;

final class WPBB_WhatsApp {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_footer', [$this, 'render_global_button'], 99);
    }

    public function render_global_button() {
        $phone = preg_replace('/[^0-9]/', '', (string) wpbb_get_option('whatsapp_phone', ''));
        if (!$phone) return;

        $message = rawurlencode((string) wpbb_get_option('whatsapp_message', 'Hi, I would like to chat.'));
        $position = (string) wpbb_get_option('whatsapp_position', 'bottom-right');
        $bg = wpbb_get_option('whatsapp_bg', '#25D366');
        $text = wpbb_get_option('whatsapp_text', '#ffffff');

        $pos = 'bottom:20px;right:20px;';
        if ($position === 'bottom-left') $pos = 'bottom:20px;left:20px;';
        if ($position === 'top-right') $pos = 'top:20px;right:20px;';
        if ($position === 'top-left') $pos = 'top:20px;left:20px;';

        echo '<div class="wpbb-global-whatsapp" style="' . esc_attr($pos) . '">';
        echo '<a href="https://wa.me/' . esc_attr($phone) . '?text=' . esc_attr($message) . '" target="_blank" rel="noopener" style="background:' . esc_attr($bg) . ';color:' . esc_attr($text) . ';">';
        echo esc_html__('Talk on WhatsApp', 'wp-bbuilder');
        echo '</a>';
        echo '</div>';
    }
}
