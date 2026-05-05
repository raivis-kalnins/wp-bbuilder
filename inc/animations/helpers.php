<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('bbtheme_get_animation_registry')) {
    function bbtheme_get_animation_registry() {
        static $registry = null;

        if ($registry === null) {
            $file = get_template_directory() . '/inc/animations/registry.php';
            $registry = file_exists($file) ? require $file : [];
        }

        return is_array($registry) ? $registry : [];
    }
}

if (!function_exists('bbtheme_get_animation_choices')) {
    function bbtheme_get_animation_choices($include_empty = false) {
        $choices = [];
        if ($include_empty) {
            $choices[''] = __('None', 'wp-theme');
        }

        foreach (bbtheme_get_animation_registry() as $item) {
            $choices[$item['class']] = sprintf('%s — %s', $item['group'], $item['label']);
        }

        return $choices;
    }
}

if (!function_exists('bbtheme_get_grouped_animation_registry')) {
    function bbtheme_get_grouped_animation_registry() {
        $grouped = [];
        foreach (bbtheme_get_animation_registry() as $item) {
            $group = $item['group'] ?? __('Other', 'wp-theme');
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $item;
        }
        return $grouped;
    }
}

if (!function_exists('bbtheme_get_animation_meta')) {
    function bbtheme_get_animation_meta($class_name) {
        foreach (bbtheme_get_animation_registry() as $item) {
            if (($item['class'] ?? '') === $class_name) {
                return $item;
            }
        }
        return null;
    }
}

if (!function_exists('bbtheme_get_animation_settings_defaults')) {
    function bbtheme_get_animation_settings_defaults() {
        return [
            'enabled' => '1',
            'default_class' => 'animate__fadeInUp',
            'default_duration' => '1s',
            'default_delay' => '0s',
            'default_repeat' => '1',
            'disable_on_mobile' => '',
            'respect_reduced_motion' => '1',
            'custom_class' => '',
            'preview_text' => __('Animation preview', 'wp-theme'),
        ];
    }
}

if (!function_exists('bbtheme_get_animation_settings')) {
    function bbtheme_get_animation_settings() {
        $defaults = bbtheme_get_animation_settings_defaults();
        $settings = get_option('bbtheme_animation_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }
        return wp_parse_args($settings, $defaults);
    }
}

if (!function_exists('bbtheme_sanitize_css_time')) {
    function bbtheme_sanitize_css_time($value, $default = '0s') {
        $value = trim((string) $value);

        if ($value === '') {
            return $default;
        }

        if (preg_match('/^\d+(?:\.\d+)?(?:ms|s)$/', $value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return rtrim(rtrim((string) $value, '0'), '.') . 's';
        }

        return $default;
    }
}

if (!function_exists('bbtheme_sanitize_repeat')) {
    function bbtheme_sanitize_repeat($value) {
        $value = trim((string) $value);

        if ($value === 'infinite') {
            return 'infinite';
        }

        $value = (int) $value;
        if ($value < 1) {
            $value = 1;
        }
        if ($value > 20) {
            $value = 20;
        }
        return (string) $value;
    }
}

if (!function_exists('bbtheme_sanitize_animation_settings')) {
    function bbtheme_sanitize_animation_settings($input) {
        $defaults = bbtheme_get_animation_settings_defaults();
        $input = is_array($input) ? $input : [];

        $available = array_keys(bbtheme_get_animation_choices(true));
        $default_class = sanitize_text_field($input['default_class'] ?? $defaults['default_class']);
        if (!in_array($default_class, $available, true)) {
            $default_class = $defaults['default_class'];
        }

        return [
            'enabled' => empty($input['enabled']) ? '' : '1',
            'default_class' => $default_class,
            'default_duration' => bbtheme_sanitize_css_time($input['default_duration'] ?? $defaults['default_duration'], $defaults['default_duration']),
            'default_delay' => bbtheme_sanitize_css_time($input['default_delay'] ?? $defaults['default_delay'], $defaults['default_delay']),
            'default_repeat' => bbtheme_sanitize_repeat($input['default_repeat'] ?? $defaults['default_repeat']),
            'disable_on_mobile' => empty($input['disable_on_mobile']) ? '' : '1',
            'respect_reduced_motion' => empty($input['respect_reduced_motion']) ? '' : '1',
            'custom_class' => sanitize_text_field($input['custom_class'] ?? ''),
            'preview_text' => sanitize_text_field($input['preview_text'] ?? $defaults['preview_text']),
        ];
    }
}

if (!function_exists('bbtheme_animation_class')) {
    function bbtheme_animation_class($animation = '', $extra_classes = '') {
        $settings = bbtheme_get_animation_settings();

        if (empty($settings['enabled'])) {
            return trim((string) $extra_classes);
        }

        $animation = $animation ?: ($settings['default_class'] ?? '');
        $classes = ['animate__animated'];

        if (!empty($animation)) {
            $classes[] = sanitize_html_class($animation);
        }

        if (!empty($settings['custom_class'])) {
            foreach (preg_split('/\s+/', (string) $settings['custom_class']) as $custom_class) {
                $custom_class = trim($custom_class);
                if ($custom_class !== '') {
                    $classes[] = sanitize_html_class($custom_class);
                }
            }
        }

        if (!empty($extra_classes)) {
            foreach (preg_split('/\s+/', (string) $extra_classes) as $extra_class) {
                $extra_class = trim($extra_class);
                if ($extra_class !== '') {
                    $classes[] = sanitize_html_class($extra_class);
                }
            }
        }

        return trim(implode(' ', array_unique(array_filter($classes))));
    }
}

if (!function_exists('bbtheme_animation_style')) {
    function bbtheme_animation_style($args = []) {
        $settings = bbtheme_get_animation_settings();
        $duration = bbtheme_sanitize_css_time($args['duration'] ?? $settings['default_duration'], $settings['default_duration']);
        $delay = bbtheme_sanitize_css_time($args['delay'] ?? $settings['default_delay'], $settings['default_delay']);
        $repeat = bbtheme_sanitize_repeat($args['repeat'] ?? $settings['default_repeat']);

        $styles = [
            '--animate-duration:' . $duration,
            '--animate-delay:' . $delay,
            '--animate-repeat:' . $repeat,
        ];

        return implode(';', $styles);
    }
}

if (!function_exists('bbtheme_get_animation_attributes')) {
    function bbtheme_get_animation_attributes($args = []) {
        $class = bbtheme_animation_class($args['animation'] ?? '', $args['class'] ?? '');
        $style = bbtheme_animation_style($args);

        return sprintf('class="%s" style="%s"', esc_attr($class), esc_attr($style));
    }
}


if (!function_exists('bbtheme_render_lottie')) {
    function bbtheme_render_lottie($args = []) {
        if (!function_exists('wp_theme_style_tokens')) {
            return '';
        }

        $tokens = wp_theme_style_tokens();
        if (empty($tokens['theme_motion_enable_lottie'])) {
            return '';
        }

        $src = esc_url($args['src'] ?? $tokens['theme_motion_lottie_url'] ?? '');
        if ($src === '') {
            return '';
        }

        $width = esc_attr($args['width'] ?? $tokens['theme_motion_lottie_width'] ?? '240px');
        $height = esc_attr($args['height'] ?? $tokens['theme_motion_lottie_height'] ?? '240px');
        $speed = esc_attr((string) ($args['speed'] ?? $tokens['theme_motion_lottie_speed'] ?? '1'));
        $loop = !empty($args['loop']) || (!isset($args['loop']) && !empty($tokens['theme_motion_lottie_loop']));
        $autoplay = !empty($args['autoplay']) || (!isset($args['autoplay']) && !empty($tokens['theme_motion_lottie_autoplay']));

        return sprintf(
            '<dotlottie-wc src="%s" speed="%s" style="width:%s;height:%s;" %s %s></dotlottie-wc>',
            $src,
            $speed,
            $width,
            $height,
            $loop ? 'loop' : '',
            $autoplay ? 'autoplay' : ''
        );
    }
}
