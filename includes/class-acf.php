<?php
if (!defined('ABSPATH')) exit;

final class WPBB_ACF {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('acf/init', [$this, 'register_blocks']);
        add_action('acf/init', [$this, 'register_field_groups']);
    }

    private function get_block_dirs() {
        return [
            'hero' => WPBB_PLUGIN_DIR . 'acf-blocks/hero',
            'boot-card' => WPBB_PLUGIN_DIR . 'acf-blocks/boot-card',
            'gallery' => WPBB_PLUGIN_DIR . 'acf-blocks/gallery',
        ];
    }

    public function register_blocks() {
        if (!function_exists('acf_register_block_type')) return;

        $defs = [
            'hero' => ['name' => 'wpbb-hero', 'title' => 'Hero', 'icon' => 'cover-image'],
            'boot-card' => ['name' => 'wpbb-card', 'title' => 'Boot Card', 'icon' => 'id'],
            'gallery' => ['name' => 'wpbb-gallery', 'title' => 'Gallery', 'icon' => 'format-gallery'],
        ];

        foreach ($this->get_block_dirs() as $slug => $dir) {
            if (!is_dir($dir) || empty($defs[$slug])) continue;
            $def = $defs[$slug];
            acf_register_block_type([
                'name' => $def['name'],
                'title' => __($def['title'], 'wp-bbuilder'),
                'description' => __('ACF ' . $def['title'] . ' block', 'wp-bbuilder'),
                'category' => 'wpbb',
                'icon' => $def['icon'],
                'mode' => 'preview',
                'render_template' => $dir . '/render.php',
                'supports' => ['align' => ['wide', 'full'], 'anchor' => true, 'jsx' => true],
                'enqueue_style' => WPBB_PLUGIN_URL . 'assets/shared.css',
            ]);
        }
    }

    public function register_field_groups() {
        if (!function_exists('acf_add_local_field_group')) return;

        foreach ($this->get_block_dirs() as $dir) {
            $fields_file = $dir . '/fields.php';
            if (file_exists($fields_file)) {
                include $fields_file;
            }
        }
    }
}
