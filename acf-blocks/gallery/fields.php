<?php
if (!defined('ABSPATH') || !function_exists('acf_add_local_field_group')) exit;

acf_add_local_field_group([
    'key' => 'group_wpbb_gallery',
    'title' => 'Gallery',
    'fields' => [
        ['key' => 'field_wpbb_gallery_images', 'label' => 'Images', 'name' => 'images', 'type' => 'gallery'],
        ['key' => 'field_wpbb_gallery_columns', 'label' => 'Columns', 'name' => 'columns', 'type' => 'select', 'choices' => ['2'=>'2','3'=>'3','4'=>'4'], 'default_value' => '3'],
        ['key' => 'field_wpbb_gallery_gap', 'label' => 'Gap class', 'name' => 'gap_class', 'type' => 'text'],
    ],
    'location' => [[['param' => 'block', 'operator' => '==', 'value' => 'acf/wpbb-gallery']]],
]);
