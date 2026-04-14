<?php
if (!defined('ABSPATH')) exit;

function wpbb_defaults() {
    return [
        'enabled_blocks' => [
            'accordion' => 1,'accordion-item' => 1,'alert' => 1,'badge' => 1,'breadcrumb' => 1,'button' => 1,'card' => 1,'cards' => 1,'column' => 1,
            'cta-card' => 1,'cta-section' => 1,'dynamic-form' => 1,'google-map' => 1,'list-group' => 1,'menu-option' => 1,'navbar' => 1,'progress' => 1,
            'row' => 1,'section' => 1,'sitemap' => 1,'soc-follow-block' => 1,'soc-share' => 1,'social-feeds' => 1,'spinner' => 1,
            'tab-item' => 1,'table' => 1,'tabs' => 1,'video' => 1,'file' => 1,'inline-svg' => 1,'swiper' => 1,
            'weather' => 1,'varda-dienas' => 1,'ajax-search' => 1,'pricecards' => 1,'catalogue' => 1,
            'code-display' => 1,'countdown-timer' => 1,'chart' => 1,'fun-fact' => 1,'mailchimp' => 1,'bootstrap-div' => 1,
            'feature-list' => 1,'timeline' => 1,'custom-embed' => 1,'ai-content' => 1,'login-register' => 1,
            'load-more' => 1,'contact-links' => 1,'events' => 1,'testimonials' => 1,'blog-filter' => 1,'booking-calendar' => 1,
        ],
        'disable_core_group' => 1,'disable_core_columns' => 1,'disable_core_column' => 1,
        'disable_core_table' => 1,'disable_core_embed' => 0,'disable_core_gallery' => 0,
        'disable_core_image' => 0,'disable_core_cover' => 0,'disable_core_media_text' => 0,'disable_core_audio' => 0,'disable_core_file' => 0,
        'disable_core_buttons' => 0,'disable_core_button' => 0,'disable_core_query' => 0,
        'load_bootstrap_css' => 1,'load_bootstrap_js' => 0,'load_shared_css' => 1,'load_bootstrap_editor_css' => 0,'force_bootstrap_enqueue' => 0,'save_entries' => 1,'show_entries_menu' => 0,
        'bootstrap_css_mode' => 'grid',
        'bootstrap_css_components' => ['reboot','grid','utilities'],
        'default_recipient_email' => get_option('admin_email'),
        'default_success_message' => __('Thank you for your submission!', 'wp-bbuilder'),
        'default_error_message' => __('Something went wrong. Please try again.', 'wp-bbuilder'),
        'default_validation_text' => __('Please fill in all required fields correctly.', 'wp-bbuilder'),
        'button_class' => 'btn btn-primary','form_class' => 'wpbb-form','admin_max_width' => '1400px',
        'hcaptcha_site_key' => '','hcaptcha_secret_key' => '','recaptcha_site_key' => '','recaptcha_secret_key' => '',
        'default_label_color' => '#334155','default_input_border_color' => '#cbd5e1','default_button_bg' => '#2563eb','default_button_text' => '#ffffff',
        'bootstrap_optimize_frontend' => 1,'bootstrap_enable_utilities' => 1,'bootstrap_allow_custom_classes' => 1,'aggregate_inline_block_css' => 1,'admin_scss' => '','admin_compiled_css' => '',
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
        'custom_scss' => '',
        'compiled_css' => '',
        'meta_header_code' => '',
        'global_footer_code' => '',
        'weather_api_key' => '',
        'weather_units' => 'metric',
        'page_redirect_rules' => '[]',
        'admin_spellcheck_enabled' => 0,
        'admin_spellcheck_language' => 'en',
    ];
}
function wpbb_get_option($key, $default = null) {
    $opts = get_option('wpbb_settings', []);
    $defaults = wpbb_defaults();
    return isset($opts[$key]) ? $opts[$key] : ($defaults[$key] ?? $default);
}
function wpbb_is_block_enabled($slug) {
    $enabled = wpbb_get_option('enabled_blocks', []);
    $defaults = wpbb_defaults();
    $default_enabled = !empty($defaults['enabled_blocks'][$slug]);
    if (empty($enabled)) return $default_enabled || empty($defaults['enabled_blocks']);
    return array_key_exists($slug, $enabled) ? !empty($enabled[$slug]) : $default_enabled;
}
function wpbb_get_blocks_list() {
    return ['accordion','accordion-item','alert','badge','breadcrumb','button','card','cards','column','cta-card','cta-section','dynamic-form','google-map','list-group','menu-option','navbar','progress','row','section','sitemap','soc-follow-block','soc-share','social-feeds','spinner','tab-item','table','tabs','video','file','inline-svg','swiper','weather','varda-dienas','ajax-search','pricecards','catalogue','code-display','countdown-timer','chart','fun-fact','mailchimp','bootstrap-div','feature-list','timeline','custom-embed','ai-content','login-register','load-more','contact-links','events','testimonials','blog-filter','booking-calendar'];
}
function wpbb_get_acf_blocks_list() { return ['wpbb-hero','wpbb-gallery']; }
function wpbb_parse_fields_json($json) {
    $decoded = json_decode((string) $json, true);
    return is_array($decoded) ? $decoded : [];
}
function wpbb_hex_color($value, $fallback = '#000000') {
    $value = sanitize_hex_color($value);
    return $value ?: $fallback;
}


function wpbb_translate_string($string, $context = 'wp-bbuilder') {
    if (function_exists('pll__')) {
        return pll__($string);
    }
    return $string;
}


if (!function_exists('wpbb_get_theme_settings_url')) {
    function wpbb_get_theme_settings_url() {
        return apply_filters('wpbb_theme_settings_url', admin_url('options-general.php?page=wp-theme-settings'));
    }
}

if (!function_exists('wpbb_get_theme_container_width')) {
    function wpbb_get_theme_container_width($default = '1400px') {
        $value = apply_filters('wpbb_theme_container_width', null);

        if (is_string($value) && trim($value) !== '') {
            $value = trim($value);
        } else {
            $candidates = [
                get_theme_mod('container_width', ''),
                get_theme_mod('container_max_width', ''),
                get_theme_mod('site_container_width', ''),
                get_option('wpbb_theme_container_width', ''),
                get_option('bbtheme_container_width', ''),
            ];

            $value = '';
            foreach ($candidates as $candidate) {
                if (is_string($candidate) && trim($candidate) !== '') {
                    $value = trim($candidate);
                    break;
                }
            }
        }

        if (!is_string($value) || trim($value) === '') {
            $value = $default;
        }

        $value = preg_replace('/[^0-9a-zA-Z\-\.\%\(\), \/]/', '', (string) $value);
        return $value !== '' ? $value : $default;
    }
}
