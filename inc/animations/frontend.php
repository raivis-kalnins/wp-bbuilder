<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('bbtheme_enqueue_animation_assets')) {
    function bbtheme_enqueue_animation_assets() {
        $settings = bbtheme_get_animation_settings();
        if (empty($settings['enabled'])) {
            return;
        }

        wp_enqueue_style(
            'bbtheme-animate-css',
            'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
            [],
            '4.1.1'
        );
    }
}
add_action('wp_enqueue_scripts', 'bbtheme_enqueue_animation_assets', 25);

if (!function_exists('bbtheme_output_animation_variables')) {
    function bbtheme_output_animation_variables() {
        $settings = bbtheme_get_animation_settings();
        if (empty($settings['enabled'])) {
            return;
        }

        $css = ':root{'
            . '--animate-duration:' . esc_html($settings['default_duration']) . ';'
            . '--animate-delay:' . esc_html($settings['default_delay']) . ';'
            . '--animate-repeat:' . (esc_html((string) $settings['default_repeat']) === 'infinite' ? 'infinite' : (int) $settings['default_repeat']) . ';'
            . '}';

        if (!empty($settings['disable_on_mobile'])) {
            $css .= '@media (max-width: 767px){.animate__animated{animation:none !important;}}';
        }

        if (!empty($settings['respect_reduced_motion'])) {
            $css .= '@media (prefers-reduced-motion: reduce){.animate__animated{animation:none !important;transition:none !important;}}';
        }

        echo "<style id=\"bbtheme-animation-vars\">{$css}</style>";
    }
}
add_action('wp_head', 'bbtheme_output_animation_variables', 99);


if (!function_exists('bbtheme_enqueue_optional_motion_assets')) {
    function bbtheme_enqueue_optional_motion_assets() {
        if (!function_exists('wp_theme_style_tokens')) {
            return;
        }

        $tokens = wp_theme_style_tokens();

        if (!empty($tokens['theme_motion_enable_lottie'])) {
            wp_enqueue_script(
                'bbtheme-dotlottie-player',
                'https://unpkg.com/@lottiefiles/dotlottie-wc@latest/dist/dotlottie-wc.js',
                [],
                null,
                true
            );
        }

        if (!empty($tokens['theme_motion_enable_svg_motion'])) {
            $css = '.' . sanitize_html_class($tokens['theme_motion_svg_class']) . '{will-change:transform,opacity;}';
            wp_register_style('bbtheme-svg-motion-inline', false, [], null);
            wp_enqueue_style('bbtheme-svg-motion-inline');
            wp_add_inline_style('bbtheme-svg-motion-inline', $css);
        }
    }
}
add_action('wp_enqueue_scripts', 'bbtheme_enqueue_optional_motion_assets', 30);
