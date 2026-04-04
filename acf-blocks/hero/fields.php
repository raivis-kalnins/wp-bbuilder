<?php
if (!defined('ABSPATH') || !function_exists('acf_add_local_field_group')) exit;

acf_add_local_field_group([
    'key' => 'group_wpbb_hero',
    'title' => 'Hero',
    'fields' => [
        ['key' => 'field_wpbb_hero_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text'],
        ['key' => 'field_wpbb_hero_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea'],
        ['key' => 'field_wpbb_hero_button_text', 'label' => 'Button Text', 'name' => 'button_text', 'type' => 'text'],
        ['key' => 'field_wpbb_hero_button_url', 'label' => 'Button URL', 'name' => 'button_url', 'type' => 'url'],
        ['key' => 'field_wpbb_hero_bg_image', 'label' => 'Background Image', 'name' => 'background_image', 'type' => 'image', 'return_format' => 'array'],
        ['key' => 'field_wpbb_hero_theme', 'label' => 'Theme', 'name' => 'theme', 'type' => 'select', 'choices' => ['light' => 'Light', 'dark' => 'Dark'], 'default_value' => 'light'],
        ['key' => 'field_wpbb_hero_title_size', 'label' => 'Title Size', 'name' => 'title_size', 'type' => 'select', 'choices' => ['display-2' => 'Display 2', 'display-3' => 'Display 3', 'display-4' => 'Display 4', 'h1' => 'H1', 'h2' => 'H2'], 'default_value' => 'display-3'],
        ['key' => 'field_wpbb_hero_text_size', 'label' => 'Text Size', 'name' => 'text_size', 'type' => 'select', 'choices' => ['lead' => 'Lead', 'fs-5' => 'fs-5', 'fs-6' => 'fs-6'], 'default_value' => 'lead'],
        ['key' => 'field_wpbb_hero_title_color', 'label' => 'Title Color', 'name' => 'title_color', 'type' => 'color_picker'],
        ['key' => 'field_wpbb_hero_text_color', 'label' => 'Text Color', 'name' => 'text_color', 'type' => 'color_picker'],
    ],
    'location' => [[['param' => 'block', 'operator' => '==', 'value' => 'acf/wpbb-hero']]],
]);
