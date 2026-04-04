<?php
if (!defined('ABSPATH')) exit;
final class WPBB_ACF {
    private static $instance = null;
    public static function instance() { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { add_action('acf/init', [$this,'register_blocks']); add_action('acf/init', [$this,'register_field_groups']); }
    public function register_blocks() {
        if (!function_exists('acf_register_block_type')) return;
        foreach ([
            ['name'=>'wpbb-hero','title'=>'Hero','icon'=>'cover-image','render'=>'render_hero'],
            ['name'=>'wpbb-card','title'=>'Boot Card','icon'=>'id','render'=>'render_card'],
            ['name'=>'wpbb-gallery','title'=>'Gallery','icon'=>'format-gallery','render'=>'render_gallery'],
        ] as $block) {
            acf_register_block_type([
                'name'=>$block['name'],'title'=>__($block['title'],'wp-bbuilder'),'description'=>__('ACF ' . $block['title'] . ' block','wp-bbuilder'),
                'category'=>'wpbb','icon'=>$block['icon'],'mode'=>'preview','render_callback'=>[$this,$block['render']],
                'supports'=>['align'=>['wide','full'],'anchor'=>true,'jsx'=>true],
            ]);
        }
    }
    public function register_field_groups() {
        if (!function_exists('acf_add_local_field_group')) return;
        acf_add_local_field_group([
            'key'=>'group_wpbb_hero','title'=>'Hero','fields'=>[
                ['key'=>'field_wpbb_hero_title','label'=>'Title','name'=>'title','type'=>'text'],
                ['key'=>'field_wpbb_hero_text','label'=>'Text','name'=>'text','type'=>'textarea'],
                ['key'=>'field_wpbb_hero_button_text','label'=>'Button Text','name'=>'button_text','type'=>'text'],
                ['key'=>'field_wpbb_hero_button_url','label'=>'Button URL','name'=>'button_url','type'=>'url'],
                ['key'=>'field_wpbb_hero_bg_image','label'=>'Background Image','name'=>'background_image','type'=>'image','return_format'=>'array'],
                ['key'=>'field_wpbb_hero_theme','label'=>'Theme','name'=>'theme','type'=>'select','choices'=>['light'=>'Light','dark'=>'Dark'],'default_value'=>'light'],
                ['key'=>'field_wpbb_hero_title_size','label'=>'Title Size','name'=>'title_size','type'=>'select','choices'=>['display-2'=>'Display 2','display-3'=>'Display 3','display-4'=>'Display 4','h1'=>'H1','h2'=>'H2'],'default_value'=>'display-3'],
                ['key'=>'field_wpbb_hero_text_size','label'=>'Text Size','name'=>'text_size','type'=>'select','choices'=>['lead'=>'Lead','fs-5'=>'fs-5','fs-6'=>'fs-6'],'default_value'=>'lead'],
                ['key'=>'field_wpbb_hero_title_color','label'=>'Title Color','name'=>'title_color','type'=>'color_picker'],
                ['key'=>'field_wpbb_hero_text_color','label'=>'Text Color','name'=>'text_color','type'=>'color_picker'],
            ],
            'location'=>[[['param'=>'block','operator'=>'==','value'=>'acf/wpbb-hero']]],
        ]);
        acf_add_local_field_group([
            'key'=>'group_wpbb_card','title'=>'Boot Card','fields'=>[
                ['key'=>'field_wpbb_card_title','label'=>'Title','name'=>'title','type'=>'text'],
                ['key'=>'field_wpbb_card_text','label'=>'Text','name'=>'text','type'=>'textarea'],
                ['key'=>'field_wpbb_card_image','label'=>'Image','name'=>'image','type'=>'image','return_format'=>'array'],
                ['key'=>'field_wpbb_card_button_text','label'=>'Button Text','name'=>'button_text','type'=>'text'],
                ['key'=>'field_wpbb_card_button_url','label'=>'Button URL','name'=>'button_url','type'=>'url'],
                ['key'=>'field_wpbb_card_classes','label'=>'Card Classes','name'=>'card_classes','type'=>'text'],
            ],
            'location'=>[[['param'=>'block','operator'=>'==','value'=>'acf/wpbb-card']]],
        ]);
        acf_add_local_field_group([
            'key'=>'group_wpbb_gallery','title'=>'Gallery','fields'=>[
                ['key'=>'field_wpbb_gallery_images','label'=>'Images','name'=>'images','type'=>'gallery'],
                ['key'=>'field_wpbb_gallery_columns','label'=>'Columns','name'=>'columns','type'=>'select','choices'=>['2'=>'2','3'=>'3','4'=>'4'],'default_value'=>'3'],
                ['key'=>'field_wpbb_gallery_gap','label'=>'Gap class','name'=>'gap_class','type'=>'text'],
            ],
            'location'=>[[['param'=>'block','operator'=>'==','value'=>'acf/wpbb-gallery']]],
        ]);
    }
    public function render_hero($block, $content = '', $is_preview = false, $post_id = 0) {
        $title = get_field('title') ?: ''; $text = get_field('text') ?: ''; $button_text = get_field('button_text') ?: '';
        $button_url = get_field('button_url') ?: ''; $background_image = get_field('background_image'); $theme = get_field('theme') ?: 'light';
        $title_size = get_field('title_size') ?: 'display-3'; $text_size = get_field('text_size') ?: 'lead';
        $title_color = get_field('title_color') ?: ''; $text_color = get_field('text_color') ?: '';
        $style = !empty($background_image['url']) ? 'background-image:url(' . esc_url($background_image['url']) . ');' : '';
        echo '<section class="wpbb-hero wpbb-hero--' . esc_attr($theme) . ' alignfull" style="' . esc_attr($style) . '"><div class="container-fluid py-5">';
        if ($title) echo '<h1 class="wpbb-hero__title ' . esc_attr($title_size) . '" style="' . esc_attr($title_color ? 'color:' . $title_color . ';' : '') . '">' . esc_html($title) . '</h1>';
        if ($text) echo '<div class="wpbb-hero__text ' . esc_attr($text_size) . '" style="' . esc_attr($text_color ? 'color:' . $text_color . ';' : '') . '">' . wp_kses_post(wpautop($text)) . '</div>';
        if ($button_text && $button_url) echo '<p><a class="btn btn-primary" href="' . esc_url($button_url) . '">' . esc_html($button_text) . '</a></p>';
        echo '</div></section>';
    }
    public function render_card($block, $content = '', $is_preview = false, $post_id = 0) {
        $title = get_field('title') ?: ''; $text = get_field('text') ?: ''; $image = get_field('image');
        $button_text = get_field('button_text') ?: ''; $button_url = get_field('button_url') ?: ''; $card_classes = get_field('card_classes') ?: 'card h-100';
        echo '<div class="' . esc_attr($card_classes) . '">';
        if (!empty($image['url'])) echo '<img class="card-img-top" src="' . esc_url($image['url']) . '" alt="">';
        echo '<div class="card-body">';
        if ($title) echo '<h3 class="card-title">' . esc_html($title) . '</h3>';
        if ($text) echo '<div class="card-text">' . wp_kses_post(wpautop($text)) . '</div>';
        if ($button_text && $button_url) echo '<p><a class="btn btn-primary" href="' . esc_url($button_url) . '">' . esc_html($button_text) . '</a></p>';
        echo '</div></div>';
    }
    public function render_gallery($block, $content = '', $is_preview = false, $post_id = 0) {
        $images = get_field('images'); $columns = get_field('columns') ?: '3'; $gap = get_field('gap_class') ?: 'g-3';
        if (empty($images) || !is_array($images)) return;
        echo '<div class="row row-cols-2 row-cols-md-' . esc_attr($columns) . ' ' . esc_attr($gap) . '">';
        foreach ($images as $image) echo '<div class="col"><img class="img-fluid rounded" src="' . esc_url($image['url']) . '" alt=""></div>';
        echo '</div>';
    }
}
