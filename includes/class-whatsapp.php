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
        if (!wpbb_get_option('whatsapp_enabled', 0)) return;

        $phone = preg_replace('/[^0-9]/', '', (string) wpbb_get_option('whatsapp_phone', ''));
        if (!$phone) return;

        $message_raw = (string) wpbb_get_option('whatsapp_message', 'Hi, I would like to chat.');
        $message = rawurlencode($message_raw);
        $profile = trim((string) wpbb_get_option('whatsapp_profile_name', 'WhatsApp'));
        $position = (string) wpbb_get_option('whatsapp_position', 'bottom-right');
        $bg = wpbb_get_option('whatsapp_bg', '#25D366');
        $text = wpbb_get_option('whatsapp_text', '#ffffff');

        $pos = 'bottom:22px;right:22px;';
        if ($position === 'bottom-left') $pos = 'bottom:22px;left:22px;';
        if ($position === 'top-right') $pos = 'top:22px;right:22px;';
        if ($position === 'top-left') $pos = 'top:22px;left:22px;';

        $icon = '<svg viewBox="0 0 32 32" aria-hidden="true" focusable="false" role="img"><path fill="currentColor" d="M16.04 3.2A12.73 12.73 0 0 0 5.2 22.63L3.5 28.8l6.32-1.66A12.75 12.75 0 1 0 16.04 3.2Zm0 23.2c-2.1 0-4.05-.62-5.7-1.7l-.4-.26-3.75.98 1-3.64-.27-.42a10.42 10.42 0 1 1 9.12 5.04Zm5.72-7.8c-.31-.16-1.84-.9-2.12-1-.28-.1-.49-.16-.7.16-.2.31-.8 1-.98 1.2-.18.2-.36.23-.67.08-.31-.16-1.32-.49-2.51-1.55-.93-.83-1.56-1.86-1.74-2.17-.18-.31-.02-.48.14-.64.14-.14.31-.36.47-.54.16-.18.2-.31.31-.52.1-.2.05-.39-.03-.54-.08-.16-.7-1.68-.95-2.3-.25-.6-.5-.52-.7-.53h-.6c-.2 0-.54.08-.82.39-.28.31-1.08 1.06-1.08 2.58 0 1.52 1.1 2.99 1.26 3.2.16.2 2.18 3.32 5.28 4.66.74.32 1.32.51 1.77.65.74.23 1.42.2 1.95.12.6-.09 1.84-.75 2.1-1.48.26-.73.26-1.36.18-1.49-.08-.13-.28-.2-.6-.36Z"/></svg>';

        echo '<div class="wpbb-global-whatsapp wpbb-global-whatsapp--chat" style="' . esc_attr($pos) . '--wpbb-wa-bg:' . esc_attr($bg) . ';--wpbb-wa-text:' . esc_attr($text) . ';">';
        echo '<button class="wpbb-wa-trigger" type="button" aria-expanded="false" aria-label="' . esc_attr__('Open WhatsApp chat', 'wp-bbuilder') . '">' . $icon . '</button>';
        echo '<div class="wpbb-wa-card" role="dialog" aria-label="' . esc_attr__('WhatsApp chat', 'wp-bbuilder') . '">';
        echo '<div class="wpbb-wa-card__head"><span class="wpbb-wa-card__avatar">' . $icon . '</span><span><strong>' . esc_html($profile ?: __('WhatsApp', 'wp-bbuilder')) . '</strong><small>' . esc_html__('Usually replies soon', 'wp-bbuilder') . '</small></span><button type="button" class="wpbb-wa-close" aria-label="' . esc_attr__('Close WhatsApp chat', 'wp-bbuilder') . '">×</button></div>';
        echo '<div class="wpbb-wa-card__body"><p>' . esc_html__('Hi, how can we help with your project?', 'wp-bbuilder') . '</p><small>' . esc_html($message_raw) . '</small></div>';
        echo '<a class="wpbb-wa-card__cta" href="https://wa.me/' . esc_attr($phone) . '?text=' . esc_attr($message) . '" target="_blank" rel="noopener">' . esc_html__('Start WhatsApp chat', 'wp-bbuilder') . '</a>';
        echo '</div></div>';
        echo '<script>(function(){var w=document.currentScript.previousElementSibling;if(!w)return;var t=w.querySelector(".wpbb-wa-trigger"),c=w.querySelector(".wpbb-wa-close");function o(v){w.classList.toggle("is-open",v);if(t)t.setAttribute("aria-expanded",v?"true":"false")}if(t)t.addEventListener("click",function(){o(!w.classList.contains("is-open"))});if(c)c.addEventListener("click",function(){o(false)});document.addEventListener("keydown",function(e){if(e.key==="Escape")o(false)});})();</script>';
    }
}
