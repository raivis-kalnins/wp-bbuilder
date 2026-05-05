<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('bbtheme_register_animation_settings')) {
    function bbtheme_register_animation_settings() {
        register_setting(
            'bbtheme_animation_settings_group',
            'bbtheme_animation_settings',
            [
                'type' => 'array',
                'sanitize_callback' => 'bbtheme_sanitize_animation_settings',
                'default' => bbtheme_get_animation_settings_defaults(),
            ]
        );
    }
}
add_action('admin_init', 'bbtheme_register_animation_settings');

/* Animation settings are managed from Settings -> Theme Settings -> Animations tab. */
