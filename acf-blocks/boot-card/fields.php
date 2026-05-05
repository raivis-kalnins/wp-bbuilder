<?php
if (!defined('ABSPATH') || !function_exists('acf_add_local_field_group')) exit;

acf_add_local_field_group([
    'key' => 'group_wpbb_card',
    'title' => 'Boot Card',
    'fields' => [
        ['key' => 'field_wpbb_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text'],
        ['key' => 'field_wpbb_card_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea'],
        ['key' => 'field_wpbb_card_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array'],
        ['key' => 'field_wpbb_card_button_text', 'label' => 'Button Text', 'name' => 'button_text', 'type' => 'text'],
        ['key' => 'field_wpbb_card_button_url', 'label' => 'Button URL', 'name' => 'button_url', 'type' => 'url'],
        ['key' => 'field_wpbb_card_classes', 'label' => 'Card Classes', 'name' => 'card_classes', 'type' => 'text'],
    ],
    'location' => [[['param' => 'block', 'operator' => '==', 'value' => 'acf/wpbb-card']]],
]);
