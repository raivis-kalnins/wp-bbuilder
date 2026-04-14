<?php
if (!defined('ABSPATH')) exit;
final class WPBB_Settings {
    private static $instance = null;
    public static function instance() { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { add_action('admin_init', [$this, 'register_settings']); }
    public function register_settings() {
        register_setting('wpbb_settings_group', 'wpbb_settings', ['type'=>'array','default'=>wpbb_defaults(),'sanitize_callback'=>[$this,'sanitize']]);
    }
    public function sanitize($input) {
        $out = wpbb_defaults();
        $enabled = [];
        foreach (wpbb_get_blocks_list() as $slug) $enabled[$slug] = !empty($input['enabled_blocks'][$slug]) ? 1 : 0;
        $out['enabled_blocks'] = $enabled;
        foreach (['disable_core_group','disable_core_columns','disable_core_column','disable_core_table','disable_core_embed','disable_core_gallery','disable_core_image','disable_core_cover','disable_core_media_text','disable_core_buttons','disable_core_button','disable_core_query','load_bootstrap_css','load_bootstrap_js','load_shared_css','load_bootstrap_editor_css','force_bootstrap_enqueue','save_entries','bootstrap_optimize_frontend','bootstrap_enable_utilities','bootstrap_allow_custom_classes','cookie_consent_enabled','google_analytics_enabled'] as $flag) {
            $out[$flag] = !empty($input[$flag]) ? 1 : 0;
        }

        $out['bootstrap_css_mode'] = in_array(($input['bootstrap_css_mode'] ?? 'full'), ['full','custom','grid','utilities'], true) ? $input['bootstrap_css_mode'] : 'full';
        $allowed_bootstrap_css_components = ['reboot','grid','utilities'];
        $out['bootstrap_css_components'] = [];
        if (!empty($input['bootstrap_css_components']) && is_array($input['bootstrap_css_components'])) {
            foreach ($input['bootstrap_css_components'] as $component) {
                $component = sanitize_key($component);
                if (in_array($component, $allowed_bootstrap_css_components, true)) $out['bootstrap_css_components'][] = $component;
            }
            $out['bootstrap_css_components'] = array_values(array_unique($out['bootstrap_css_components']));
        }

        foreach (['default_recipient_email','default_success_message','default_error_message','default_validation_text','button_class','form_class','admin_max_width','hcaptcha_site_key','hcaptcha_secret_key','recaptcha_site_key','recaptcha_secret_key','whatsapp_phone','whatsapp_message','whatsapp_position','cookie_consent_text','cookie_accept_text','cookie_reject_text','cookie_policy_url','cookie_position'] as $field) {
            $out[$field] = sanitize_text_field($input[$field] ?? ($out[$field] ?? ''));
        }
        foreach (['google_analytics_head','custom_scss','compiled_css','meta_header_code','global_footer_code','page_redirect_rules'] as $field) {
            $out[$field] = isset($input[$field]) ? (string) wp_unslash($input[$field]) : ($out[$field] ?? '');
        }
        foreach (['default_label_color','default_input_border_color','default_button_bg','default_button_text','whatsapp_bg','whatsapp_text','cookie_bg','cookie_text_color','cookie_button_bg','cookie_button_text'] as $field) {
            $out[$field] = sanitize_hex_color($input[$field] ?? $out[$field]) ?: $out[$field];
        }
        $out['show_entries_menu'] = 0;
        return $out;
    }
}
