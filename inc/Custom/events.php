<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('wp_theme_event_enabled')) {
    function wp_theme_event_enabled() {
        return (bool) wp_theme_acf_get('theme_enable_event_cpt', 'option', 0);
    }
}

if (!function_exists('wp_theme_register_event_cpt')) {
    function wp_theme_register_event_cpt() {
        if (!wp_theme_event_enabled()) {
            return;
        }

        $labels = [
            'name' => __('Events', 'wp-theme'),
            'singular_name' => __('Event', 'wp-theme'),
            'menu_name' => __('Events', 'wp-theme'),
            'add_new' => __('Add Event', 'wp-theme'),
            'add_new_item' => __('Add New Event', 'wp-theme'),
            'edit_item' => __('Edit Event', 'wp-theme'),
            'new_item' => __('New Event', 'wp-theme'),
            'view_item' => __('View Event', 'wp-theme'),
            'search_items' => __('Search Events', 'wp-theme'),
            'not_found' => __('No events found', 'wp-theme'),
        ];

        register_post_type('event', [
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
            'rewrite' => ['slug' => 'events', 'with_front' => false],
        ]);

        register_taxonomy('event_category', ['event'], [
            'labels' => [
                'name' => __('Event Categories', 'wp-theme'),
                'singular_name' => __('Event Category', 'wp-theme'),
            ],
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'event-category', 'with_front' => false],
        ]);
    }
}
add_action('init', 'wp_theme_register_event_cpt', 19);

if (!function_exists('wp_theme_register_event_fields')) {
    function wp_theme_register_event_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_wp_theme_event_details',
            'title' => 'Event Details',
            'fields' => [
                [
                    'key' => 'field_wp_theme_event_name',
                    'label' => 'Event Name',
                    'name' => 'event_name',
                    'type' => 'text',
                    'instructions' => 'Optional custom display name for the single event template. Leave empty to use the post title.',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_wp_theme_event_location',
                    'label' => 'Location',
                    'name' => 'event_location',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_wp_theme_event_short_description',
                    'label' => 'Short Description',
                    'name' => 'event_short_description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'new_lines' => 'br',
                    'instructions' => 'Short event summary shown near the top of the single event page.',
                    'wrapper' => ['width' => '100'],
                ],
                [
                    'key' => 'field_wp_theme_event_date',
                    'label' => 'Event Date',
                    'name' => 'event_date',
                    'type' => 'date_picker',
                    'display_format' => 'F j, Y',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_wp_theme_event_time',
                    'label' => 'Event Time',
                    'name' => 'event_time',
                    'type' => 'time_picker',
                    'display_format' => 'g:i a',
                    'return_format' => 'g:i a',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_wp_theme_event_details',
                    'label' => 'Event Details',
                    'name' => 'event_details',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'wrapper' => ['width' => '100'],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'event',
                    ],
                ],
            ],
            'position' => 'acf_after_title',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'show_in_rest' => 1,
        ]);
    }
}
add_action('acf/init', 'wp_theme_register_event_fields');

if (!function_exists('wp_theme_format_event_date')) {
    function wp_theme_format_event_date($raw_date) {
        if (!$raw_date) {
            return '';
        }
        $timestamp = strtotime((string) $raw_date);
        return $timestamp ? wp_date(get_option('date_format') ?: 'F j, Y', $timestamp) : (string) $raw_date;
    }
}

if (!function_exists('wp_theme_event_template_include')) {
    function wp_theme_event_template_include($template) {
        if (!is_singular('event')) {
            return $template;
        }

        $child = get_stylesheet_directory() . '/single-event.php';
        if (file_exists($child)) {
            return $child;
        }

        $theme_template = get_template_directory() . '/inc/templates/single-event.php';
        if (file_exists($theme_template)) {
            return $theme_template;
        }

        return $template;
    }
}
add_filter('template_include', 'wp_theme_event_template_include', 99);
