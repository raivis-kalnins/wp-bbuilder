<?php
if (!defined('ABSPATH')) exit;

function wpbb_defaults() {
    return [
        'enabled_blocks' => [
            'accordion' => 1,'accordion-item' => 1,'button' => 1,'card' => 1,'cards' => 1,'column' => 1,
            'cta-card' => 1,'cta-section' => 1,'dynamic-form' => 1,'google-map' => 1,'menu-option' => 1,
            'row' => 1,'row-section' => 1,'sitemap' => 1,'soc-follow-block' => 1,'soc-share' => 1,
            'tab-item' => 1,'table' => 1,'tabs' => 1,'video' => 1,
        ],
        'disable_core_group' => 1,'disable_core_columns' => 1,'disable_core_column' => 1,
        'disable_core_table' => 1,'disable_core_embed' => 0,'disable_core_gallery' => 0,
        'disable_core_image' => 0,'disable_core_cover' => 0,'disable_core_media_text' => 0,
        'disable_core_buttons' => 0,'disable_core_button' => 0,
        'load_bootstrap_css' => 1,'load_bootstrap_js' => 0,'save_entries' => 1,'show_entries_menu' => 0,
        'default_recipient_email' => get_option('admin_email'),
        'default_success_message' => __('Thank you for your submission!', 'wp-bbuilder'),
        'default_error_message' => __('Something went wrong. Please try again.', 'wp-bbuilder'),
        'default_validation_text' => __('Please fill in all required fields correctly.', 'wp-bbuilder'),
        'button_class' => 'btn btn-primary','form_class' => 'wpbb-form','admin_max_width' => '1400px',
        'hcaptcha_site_key' => '','hcaptcha_secret_key' => '','recaptcha_site_key' => '','recaptcha_secret_key' => '',
        'default_label_color' => '#334155','default_input_border_color' => '#cbd5e1','default_button_bg' => '#2563eb','default_button_text' => '#ffffff',
        'bootstrap_optimize_frontend' => 1,'bootstrap_enable_utilities' => 1,'bootstrap_allow_custom_classes' => 1,
        'whatsapp_phone' => '',
        'whatsapp_message' => 'Hi, I would like to chat.',
        'whatsapp_position' => 'bottom-right',
        'whatsapp_bg' => '#25D366',
        'whatsapp_text' => '#ffffff',
        'cookie_consent_enabled' => 0,
        'cookie_consent_text' => 'We use cookies to improve your experience.',
        'cookie_accept_text' => 'Accept',
        'cookie_reject_text' => 'Reject',
        'cookie_policy_url' => '',
        'cookie_position' => 'bottom',
        'cookie_bg' => '#111827',
        'cookie_text_color' => '#ffffff',
        'cookie_button_bg' => '#2563eb',
        'cookie_button_text' => '#ffffff',
        'google_analytics_enabled' => 0,
        'google_analytics_head' => '',
    ];
}
function wpbb_get_option($key, $default = null) {
    $opts = get_option('wpbb_settings', []);
    $defaults = wpbb_defaults();
    return isset($opts[$key]) ? $opts[$key] : ($defaults[$key] ?? $default);
}
function wpbb_is_block_enabled($slug) {
    $enabled = wpbb_get_option('enabled_blocks', []);
    return empty($enabled) || !empty($enabled[$slug]);
}
function wpbb_get_blocks_list() {
    return ['accordion','accordion-item','button','card','cards','column','cta-card','cta-section','dynamic-form','google-map','menu-option','row','row-section','sitemap','soc-follow-block','soc-share','tab-item','table','tabs','video'];
}
function wpbb_get_acf_blocks_list() { return ['wpbb-hero','wpbb-card','wpbb-gallery']; }
function wpbb_parse_fields_json($json) {
    $decoded = json_decode((string) $json, true);
    return is_array($decoded) ? $decoded : [];
}
function wpbb_hex_color($value, $fallback = '#000000') {
    $value = sanitize_hex_color($value);
    return $value ?: $fallback;
}
