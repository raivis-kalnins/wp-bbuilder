<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Blocks {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_assets']);
        add_action('init', [$this, 'register_blocks']);
        add_filter('block_categories_all', [$this, 'register_category'], 10, 1);
        add_filter('allowed_block_types_all', [$this, 'filter_allowed_blocks'], 20, 2);
        add_action('enqueue_block_assets', [$this, 'enqueue_frontend_assets']);
    }

    public function register_post_type() {
        register_post_type('wpbb_entry', [
            'labels' => [
                'name' => __('Form Entries', 'wp-bbuilder'),
                'singular_name' => __('Form Entry', 'wp-bbuilder'),
            ],
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'menu_icon' => 'dashicons-feedback',
            'supports' => ['title', 'custom-fields'],
        ]);
    }

    public function register_assets() {
        wp_register_script('wpbb-editor', WPBB_PLUGIN_URL . 'assets/editor.js', ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data'], WPBB_VERSION, true);
        wp_register_script('wpbb-form-view', WPBB_PLUGIN_URL . 'assets/form.js', [], WPBB_VERSION, true);
        wp_localize_script('wpbb-form-view', 'wpbbForm', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpbb_form_nonce'),
            'error' => wpbb_get_option('default_error_message', __('Something went wrong. Please try again.', 'wp-bbuilder')),
            'validationText' => wpbb_get_option('default_validation_text', __('Please fill in all required fields correctly.', 'wp-bbuilder')),
        ]);
        wp_register_style('wpbb-shared', WPBB_PLUGIN_URL . 'assets/shared.css', [], WPBB_VERSION);
        wp_register_style('wpbb-editor-style', WPBB_PLUGIN_URL . 'assets/editor.css', ['wpbb-shared'], WPBB_VERSION);
    }

    public function register_category($categories) {
        $categories[] = [
            'slug' => 'wpbb',
            'title' => __('BBuilder', 'wp-bbuilder'),
            'icon' => null,
        ];
        return $categories;
    }

    public function register_blocks() {
        foreach (wpbb_get_blocks_list() as $slug) {
            if (!wpbb_is_block_enabled($slug)) continue;

            $args = [
                'api_version' => 3,
                'title' => ucwords(str_replace('-', ' ', $slug)),
                'category' => 'wpbb',
                'icon' => $this->icon_for($slug),
                'editor_script' => 'wpbb-editor',
                'editor_style' => 'wpbb-editor-style',
                'style' => 'wpbb-shared',
                'attributes' => $this->attributes_for($slug),
                'supports' => ['anchor' => true, 'html' => false],
            ];

            if ($slug === 'dynamic-form') {
                $args['script'] = 'wpbb-form-view';
                $args['render_callback'] = [$this, 'render_dynamic_form'];
            } elseif ($slug === 'table') {
                $args['render_callback'] = [$this, 'render_table_block'];
            } else {
                $args['render_callback'] = [$this, 'render_generic_block'];
            }

            if ($slug === 'column') $args['parent'] = ['wpbb/row'];
            if ($slug === 'accordion-item') $args['parent'] = ['wpbb/accordion'];
            if ($slug === 'tab-item') $args['parent'] = ['wpbb/tabs'];

            register_block_type('wpbb/' . $slug, $args);
        }
    }

    private function icon_for($slug) {
        $map = [
            'accordion' => 'menu',
            'accordion-item' => 'excerpt-view',
            'button' => 'button',
            'card' => 'id',
            'cards' => 'grid-view',
            'column' => 'columns',
            'dynamic-form' => 'feedback',
            'row' => 'grid-view','row-section' => 'cover-image','cta-card' => 'megaphone','cta-section' => 'cover-image','google-map' => 'location-alt','menu-option' => 'menu','sitemap' => 'networking','soc-follow-block' => 'share','soc-share' => 'share-alt2',
            'tab-item' => 'editor-table',
            'tabs' => 'index-card',
            'table' => 'table-col-after',
            'row-section' => 'cover-image',
        ];
        return $map[$slug] ?? 'screenoptions';
    }

    private function attributes_for($slug) {
        switch ($slug) {
            case 'row':
                return [
                    'gutterX' => ['type' => 'string', 'default' => 'gx-3'],
                    'gutterY' => ['type' => 'string', 'default' => 'gy-3'],
                    'align' => ['type' => 'string', 'default' => ''],
                    'paddingClass' => ['type' => 'string', 'default' => ''],
                    'marginClass' => ['type' => 'string', 'default' => ''],
                    'backgroundClass' => ['type' => 'string', 'default' => ''],
                    'animationClass' => ['type' => 'string', 'default' => ''],
                    'displayClass' => ['type' => 'string', 'default' => ''],
                    'textUtilityClass' => ['type' => 'string', 'default' => ''],
                    'roundedClass' => ['type' => 'string', 'default' => ''],
                    'shadowClass' => ['type' => 'string', 'default' => ''],
                    'bootstrapClasses' => ['type' => 'string', 'default' => ''],
                    'paddingTop' => ['type' => 'number', 'default' => 0],
                    'paddingTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingRight' => ['type' => 'number', 'default' => 0],
                    'paddingRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingBottom' => ['type' => 'number', 'default' => 0],
                    'paddingBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingLeft' => ['type' => 'number', 'default' => 0],
                    'paddingLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginTop' => ['type' => 'number', 'default' => 0],
                    'marginTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginRight' => ['type' => 'number', 'default' => 0],
                    'marginRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginBottom' => ['type' => 'number', 'default' => 0],
                    'marginBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginLeft' => ['type' => 'number', 'default' => 0],
                    'marginLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'backgroundColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                    'customStyle' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'column':
                return [
                    'xs' => ['type' => 'number', 'default' => 12],
                    'sm' => ['type' => 'number', 'default' => 0],
                    'md' => ['type' => 'number', 'default' => 6],
                    'lg' => ['type' => 'number', 'default' => 0],
                    'xl' => ['type' => 'number', 'default' => 0],
                    'xxl' => ['type' => 'number', 'default' => 0],
                    'orderClass' => ['type' => 'string', 'default' => ''],'visibilityClass' => ['type' => 'string', 'default' => ''],'animationClass' => ['type' => 'string', 'default' => ''],
                    'paddingClass' => ['type' => 'string', 'default' => ''],
                    'marginClass' => ['type' => 'string', 'default' => ''],
                    'backgroundClass' => ['type' => 'string', 'default' => ''],
                    'animationClass' => ['type' => 'string', 'default' => ''],
                    'displayClass' => ['type' => 'string', 'default' => ''],
                    'textUtilityClass' => ['type' => 'string', 'default' => ''],
                    'roundedClass' => ['type' => 'string', 'default' => ''],
                    'shadowClass' => ['type' => 'string', 'default' => ''],
                    'bootstrapClasses' => ['type' => 'string', 'default' => ''],
                    'paddingTop' => ['type' => 'number', 'default' => 0],
                    'paddingTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingRight' => ['type' => 'number', 'default' => 0],
                    'paddingRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingBottom' => ['type' => 'number', 'default' => 0],
                    'paddingBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingLeft' => ['type' => 'number', 'default' => 0],
                    'paddingLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginTop' => ['type' => 'number', 'default' => 0],
                    'marginTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginRight' => ['type' => 'number', 'default' => 0],
                    'marginRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginBottom' => ['type' => 'number', 'default' => 0],
                    'marginBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginLeft' => ['type' => 'number', 'default' => 0],
                    'marginLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'backgroundColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                    'customStyle' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'button':
                return [
                    'text' => ['type' => 'string', 'default' => 'Button'],
                    'url' => ['type' => 'string', 'default' => '#'],
                    'btnClass' => ['type' => 'string', 'default' => 'btn btn-primary'],
                    'variant' => ['type' => 'string', 'default' => 'primary'],
                    'size' => ['type' => 'string', 'default' => ''],
                    'fullWidth' => ['type' => 'boolean', 'default' => false],
                    'backgroundColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                ];
            case 'cards':
                return [
                    'columnsMd' => ['type' => 'number', 'default' => 3],
                    'gap' => ['type' => 'number', 'default' => 3],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'accordion-item':
            case 'tab-item':
                return [
                    'title' => ['type' => 'string', 'default' => ucfirst(str_replace('-', ' ', $slug))],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'cta-card':
                $title = esc_html($attributes['title'] ?? __('CTA Card', 'wp-bbuilder'));
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Learn more', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-card card h-100' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><p class=\"card-text\">{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></div>";

            case 'cta-section':
                $title = esc_html($attributes['title'] ?? __('CTA Section', 'wp-bbuilder'));
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Get started', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-section text-center py-5' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<section {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"container-fluid\"><h2>{$title}</h2><p>{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></section>";

            case 'google-map':
                $url = esc_url($attributes['embedUrl'] ?? '');
                $height = esc_attr($attributes['height'] ?? '380px');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-google-map' . $extra]);
                if (!$url) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add embed URL', 'wp-bbuilder') . "</div></div>";
                $style = !empty($attributes["mapFilter"]) ? "filter:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["mapFilter"]) . ";" : ""; return "<div {$wrapper}><iframe src=\"{$url}\" style=\"width:100%;height:{$height};border:0;{$style}\" loading=\"lazy\" allowfullscreen></iframe></div>";

            case 'menu-option':
                $title = esc_html($attributes['title'] ?? __('Menu Item', 'wp-bbuilder'));
                $badge = esc_html($attributes['badge'] ?? '');
                $text = esc_html($attributes['text'] ?? '');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-menu-option d-flex justify-content-between align-items-start gap-3 py-2' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div><strong>{$title}</strong><div>{$text}</div></div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>";

            case 'sitemap':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-sitemap' . $extra]);
                $title = esc_html($attributes["title"] ?? __("Sitemap", "wp-bbuilder")); $pages = !empty($attributes["showPages"]) ? wp_list_pages(["echo"=>0,"title_li"=>""]) : ""; $posts = ""; if (!empty($attributes["showPosts"])) { $items = get_posts(["numberposts"=>10,"post_status"=>"publish"]); if ($items) { $posts .= "<ul>"; foreach ($items as $p) $posts .= "<li><a href=\"" . esc_url(get_permalink($p)) . "\">" . esc_html(get_the_title($p)) . "</a></li>"; $posts .= "</ul>"; } } return "<div {$wrapper}><h3>{$title}</h3>" . ($pages ? "<ul>{$pages}</ul>" : "") . $posts . "</div>";

            case 'soc-follow-block':
                $title = esc_html($attributes['title'] ?? __('Follow Us', 'wp-bbuilder'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-follow d-flex gap-2 align-items-center' . $extra]);
                $facebook = esc_url($attributes["facebook"] ?? ""); $instagram = esc_url($attributes["instagram"] ?? ""); $linkedin = esc_url($attributes["linkedin"] ?? ""); $x = esc_url($attributes["x"] ?? ""); $html = "<div {$wrapper}><strong>{$title}</strong>"; if ($facebook) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$facebook}\">FB</a>"; if ($instagram) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$instagram}\">IG</a>"; if ($linkedin) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$linkedin}\">LI</a>"; if ($x) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$x}\">X</a>"; $html .= "</div>"; return $html;

            case 'soc-share':
                $title = esc_html($attributes['title'] ?? __('Share', 'wp-bbuilder'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-share d-flex gap-2 align-items-center' . $extra]);
                $title = esc_html($attributes["title"] ?? __("Share", "wp-bbuilder")); $iconStyle = $attributes["iconStyle"] ?? "buttons"; $wrapper = get_block_wrapper_attributes(["class" => "wpbb-soc-share d-flex gap-2 align-items-center" . $extra]); $shareUrl = rawurlencode(get_permalink()); $shareTitle = rawurlencode(get_the_title()); $btnClass = $iconStyle === "icons" ? "btn btn-light btn-sm rounded-circle" : "btn btn-outline-secondary btn-sm"; return "<div {$wrapper}><strong>{$title}</strong><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://www.facebook.com/sharer/sharer.php?u={$shareUrl}\">FB</a><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://twitter.com/intent/tweet?url={$shareUrl}&text={$shareTitle}\">X</a><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://www.linkedin.com/sharing/share-offsite/?url={$shareUrl}\">LI</a></div>";

            case 'video':
                $url = esc_url($attributes['videoUrl'] ?? '');
                $ratio = !empty($attributes['ratioClass']) ? esc_attr($attributes['ratioClass']) : 'ratio ratio-16x9';
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-video' . $extra]);
                if (!$url) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add video URL', 'wp-bbuilder') . "</div></div>"; $poster = !empty($attributes["poster"]) ? "poster=\"" . esc_url($attributes["poster"]) . "\"" : ""; if (preg_match('~(youtube|youtu\.be|vimeo)~', $url)) { return "<div {$wrapper}><div class=\"{$ratio}\"><iframe src=\"{$url}\" allowfullscreen loading=\"lazy\"></iframe></div></div>"; } return "<div {$wrapper}><video controls {$poster} style=\"width:100%;height:auto\"><source src=\"{$url}\"></video></div>";

            case 'whatsapp-chat':
                $label = esc_html($attributes['label'] ?? __('Chat on WhatsApp', 'wp-bbuilder'));
                $phone = preg_replace('/[^0-9]/', '', (string) ($attributes['phone'] ?? wpbb_get_option('whatsapp_phone', '')));
                $message = rawurlencode((string) ($attributes['message'] ?? wpbb_get_option('whatsapp_message', 'Hi, I would like to chat.')));
                $position = $attributes['position'] ?? wpbb_get_option('whatsapp_position', 'bottom-right');
                $bg = $attributes['bgColor'] ?: wpbb_get_option('whatsapp_bg', '#25D366');
                $textColor = $attributes['textColor'] ?: wpbb_get_option('whatsapp_text', '#ffffff');
                $posStyle = 'bottom:20px;right:20px;';
                if ($position === 'bottom-left') $posStyle = 'bottom:20px;left:20px;';
                if ($position === 'top-right') $posStyle = 'top:20px;right:20px;';
                if ($position === 'top-left') $posStyle = 'top:20px;left:20px;';
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-whatsapp-chat position-fixed' . $extra]);
                if (!$phone) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add WhatsApp phone number in block or admin settings.', 'wp-bbuilder') . "</div></div>";
                return "<div {$wrapper} style=\"" . esc_attr($posStyle) . "z-index:50;\"><a class=\"btn\" style=\"background:" . esc_attr($bg) . ";color:" . esc_attr($textColor) . ";border-radius:999px;padding:12px 16px;box-shadow:0 8px 20px rgba(0,0,0,.12);\" target=\"_blank\" rel=\"noopener\" href=\"https://wa.me/{$phone}?text={$message}\">{$label}</a></div>";

            case 'row-section':
                return [
                    'sectionClass' => ['type' => 'string', 'default' => 'py-5'],
                    'containerClass' => ['type' => 'string', 'default' => 'container-fluid'],
                    'backgroundClass' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'table':
                return [
                    'csvText' => ['type' => 'string', 'default' => 'Name,Role\nJohn,Designer\nAnna,Developer'],
                    'csvFileName' => ['type' => 'string', 'default' => ''],
                    'delimiter' => ['type' => 'string', 'default' => ','],
                    'datatable' => ['type' => 'boolean', 'default' => true],
                    'datatableSearch' => ['type' => 'boolean', 'default' => true],
                    'datatablePaging' => ['type' => 'boolean', 'default' => true],
                    'datatableOrdering' => ['type' => 'boolean', 'default' => true],
                    'datatableInfo' => ['type' => 'boolean', 'default' => true],
                    'datatableLengthChange' => ['type' => 'boolean', 'default' => true],
                    'useFirstRowHeader' => ['type' => 'boolean', 'default' => true],
                    'tableClass' => ['type' => 'string', 'default' => 'table table-striped table-hover'],
                    'responsive' => ['type' => 'boolean', 'default' => true],
                    'small' => ['type' => 'boolean', 'default' => false],
                    'bordered' => ['type' => 'boolean', 'default' => false],
                    'className' => ['type' => 'string', 'default' => ''],
                ];

            case 'cta-card':
                return ['title'=>['type'=>'string','default'=>'CTA Card'],'text'=>['type'=>'string','default'=>'Call to action text'],'buttonText'=>['type'=>'string','default'=>'Learn more'],'buttonUrl'=>['type'=>'string','default'=>'#'],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'cta-section':
                return ['title'=>['type'=>'string','default'=>'CTA Section'],'text'=>['type'=>'string','default'=>'Call to action text'],'buttonText'=>['type'=>'string','default'=>'Get started'],'buttonUrl'=>['type'=>'string','default'=>'#'],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'backgroundImage'=>['type'=>'string','default'=>''],'parallax'=>['type'=>'boolean','default'=>false],'className'=>['type'=>'string','default'=>'']];
            case 'google-map':
                return ['embedUrl'=>['type'=>'string','default'=>''],'height'=>['type'=>'string','default'=>'380px'],'mapFilter'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'menu-option':
                return ['title'=>['type'=>'string','default'=>'Menu Item'],'price'=>['type'=>'string','default'=>''],'text'=>['type'=>'string','default'=>''],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'sitemap':
                return ['title'=>['type'=>'string','default'=>'Sitemap'],'showPages'=>['type'=>'boolean','default'=>true],'showPosts'=>['type'=>'boolean','default'=>false],'className'=>['type'=>'string','default'=>'']];
            case 'soc-follow-block':
            case 'soc-share':
                return ['title'=>['type'=>'string','default'=>ucfirst(str_replace('-', ' ', $slug))],'className'=>['type'=>'string','default'=>'']];
            case 'video':
                return ['videoUrl'=>['type'=>'string','default'=>''],'ratioClass'=>['type'=>'string','default'=>'ratio ratio-16x9'],'poster'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];


            case 'whatsapp-chat':
                return [
                    'label' => ['type' => 'string', 'default' => 'Chat on WhatsApp'],
                    'phone' => ['type' => 'string', 'default' => ''],
                    'message' => ['type' => 'string', 'default' => ''],
                    'position' => ['type' => 'string', 'default' => ''],
                    'bgColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];

            case 'dynamic-form':
                return [
                    'formTitle' => ['type' => 'string', 'default' => 'Contact form'],
                    'recipient' => ['type' => 'string', 'default' => ''],
                    'emailSubject' => ['type' => 'string', 'default' => 'New form submission'],
                    'successMessage' => ['type' => 'string', 'default' => 'Thank you for your submission!'],
                    'submitText' => ['type' => 'string', 'default' => 'Submit'],
                    'showTitle' => ['type' => 'boolean', 'default' => true],
                    'formClass' => ['type' => 'string', 'default' => 'wpbb-form'],
                    'buttonClass' => ['type' => 'string', 'default' => 'btn btn-primary'],
                    'stylePreset' => ['type' => 'string', 'default' => 'default'],
                    'labelPosition' => ['type' => 'string', 'default' => 'top'],
                    'columnsMd' => ['type' => 'number', 'default' => 2],
                    'gap' => ['type' => 'number', 'default' => 3],
                    'fieldsJson' => ['type' => 'string', 'default' => ''],
                ];
            default:
                return ['className' => ['type' => 'string', 'default' => '']];
        }
    }


    private function build_spacing_style($attributes) {
        $map = [
            'paddingTop' => 'padding-top',
            'paddingRight' => 'padding-right',
            'paddingBottom' => 'padding-bottom',
            'paddingLeft' => 'padding-left',
            'marginTop' => 'margin-top',
            'marginRight' => 'margin-right',
            'marginBottom' => 'margin-bottom',
            'marginLeft' => 'margin-left',
        ];
        $styles = [];
        foreach ($map as $attr => $css) {
            if (isset($attributes[$attr]) && $attributes[$attr] !== '' && $attributes[$attr] !== null) {
                $num = is_numeric($attributes[$attr]) ? $attributes[$attr] : '';
                if ($num !== '') {
                    $unitKey = $attr . 'Unit';
                    $unit = !empty($attributes[$unitKey]) ? preg_replace('/[^%a-z]/', '', (string) $attributes[$unitKey]) : 'px';
                    if (!in_array($unit, ['px','%','em','rem'], true)) $unit = 'px';
                    $styles[] = $css . ':' . $num . $unit;
                }
            }
        }
        if (!empty($attributes['backgroundColor'])) $styles[] = 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string) $attributes['backgroundColor']);
        if (!empty($attributes['textColor'])) $styles[] = 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string) $attributes['textColor']);
        if (!empty($attributes['customStyle'])) $styles[] = trim((string) $attributes['customStyle']);
        return implode(';', array_filter($styles));
    }

    private function collect_bootstrap_classes($attributes, $base = []) {
        $classes = $base;
        foreach (['paddingClass','marginClass','backgroundClass','animationClass','displayClass','textUtilityClass','roundedClass','shadowClass','orderClass'] as $field) {
            if (!empty($attributes[$field])) {
                foreach (preg_split('/\s+/', trim((string) $attributes[$field])) as $c) {
                    if ($c !== '') $classes[] = sanitize_html_class($c);
                }
            }
        }
        if (!empty($attributes['bootstrapClasses'])) {
            foreach (preg_split('/\s+/', trim((string) $attributes['bootstrapClasses'])) as $c) {
                if ($c !== '') $classes[] = sanitize_html_class($c);
            }
        }
        return $classes;
    }

    public function filter_allowed_blocks($allowed_blocks, $context) {
        $disallowed = [];
        if (wpbb_get_option('disable_core_group', 1)) $disallowed[] = 'core/group';
        if (wpbb_get_option('disable_core_table', 1)) $disallowed[] = 'core/table';
        if (wpbb_get_option('disable_core_embed', 0)) $disallowed[] = 'core/embed';
        if (wpbb_get_option('disable_core_gallery', 0)) $disallowed[] = 'core/gallery';
        if (wpbb_get_option('disable_core_image', 0)) $disallowed[] = 'core/image';
        if (wpbb_get_option('disable_core_cover', 0)) $disallowed[] = 'core/cover';
        if (wpbb_get_option('disable_core_media_text', 0)) $disallowed[] = 'core/media-text';
        if (wpbb_get_option('disable_core_buttons', 0)) $disallowed[] = 'core/buttons';
        if (wpbb_get_option('disable_core_button', 0)) $disallowed[] = 'core/button';
        if (wpbb_get_option('disable_core_columns', 1)) $disallowed[] = 'core/columns';
        if (wpbb_get_option('disable_core_column', 1)) $disallowed[] = 'core/column';

        if ($allowed_blocks === true) {
            $registry = WP_Block_Type_Registry::get_instance()->get_all_registered();
            $allowed_blocks = array_keys($registry);
        }
        if (!is_array($allowed_blocks)) return $allowed_blocks;
        return array_values(array_diff($allowed_blocks, $disallowed));
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('wpbb-shared');
        if (wpbb_get_option('load_bootstrap_css', 1)) {
            wp_enqueue_style('wpbb-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        }
        wp_enqueue_style('wpbb-datatables', 'https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css', [], '2.0.8');
        wp_enqueue_script('wpbb-datatables', 'https://cdn.datatables.net/2.0.8/js/dataTables.js', [], '2.0.8', true);
        wp_enqueue_script('wpbb-datatables-bs5', 'https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js', ['wpbb-datatables'], '2.0.8', true);
        wp_enqueue_script('wpbb-table-init', WPBB_PLUGIN_URL . 'assets/table-init.js', ['wpbb-datatables-bs5'], WPBB_VERSION, true);
        if (wpbb_get_option('load_bootstrap_js', 0)) {
            wp_enqueue_script('wpbb-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
        }

        $inline = ':root{
            --wpbb-label-color:' . wpbb_hex_color(wpbb_get_option('default_label_color', '#334155')) . ';
            --wpbb-input-border:' . wpbb_hex_color(wpbb_get_option('default_input_border_color', '#cbd5e1')) . ';
            --wpbb-button-bg:' . wpbb_hex_color(wpbb_get_option('default_button_bg', '#2563eb')) . ';
            --wpbb-button-text:' . wpbb_hex_color(wpbb_get_option('default_button_text', '#ffffff')) . ';
        }';
        wp_add_inline_style('wpbb-shared', $inline);
    }

    public function render_generic_block($attributes, $content, $block) {
        $name = str_replace('wpbb/', '', $block->name);
        $extra = !empty($attributes['className']) ? ' ' . sanitize_html_class($attributes['className']) : '';

        switch ($name) {
            case 'row':
                $classes = $this->collect_bootstrap_classes($attributes, ['wpbb-row', 'row', sanitize_html_class($attributes['gutterX'] ?? 'gx-3'), sanitize_html_class($attributes['gutterY'] ?? 'gy-3')]);
                if (!empty($attributes['align'])) $classes[] = 'justify-content-' . sanitize_html_class($attributes['align']);
                $style = $this->build_spacing_style($attributes); if (!empty($attributes["maxWidth"])) $style .= ";max-width:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["maxWidth"]) . ";margin-left:auto;margin-right:auto";
                $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_filter($classes)) . $extra, 'style' => $style]);
                if (!empty($attributes['containerClass'])) { return "<div {$wrapper}><div class=\"" . esc_attr($attributes['containerClass']) . "\">{$content}</div></div>"; } return "<div {$wrapper}>{$content}</div>";

            case 'column':
                $classes = ['wpbb-column'];
                foreach (['xs','sm','md','lg','xl','xxl'] as $bp) {
                    if (!empty($attributes[$bp])) {
                        $classes[] = $bp === 'xs' ? 'col-' . intval($attributes[$bp]) : 'col-' . $bp . '-' . intval($attributes[$bp]);
                    }
                }
                $classes = $this->collect_bootstrap_classes($attributes, $classes); if (!empty($attributes['visibilityClass'])) $classes[] = sanitize_html_class($attributes['visibilityClass']); if (!empty($attributes['animationClass'])) $classes[] = sanitize_html_class($attributes['animationClass']);
                if (count($classes) === 1) $classes[] = 'col-12';
                $style = $this->build_spacing_style($attributes);
                $wrapper = get_block_wrapper_attributes(['class' => implode(' ', $classes) . $extra, 'style' => $style]);
                return "<div {$wrapper}>{$content}</div>";

            case 'button':
                $text = esc_html($attributes['text'] ?? __('Button', 'wp-bbuilder'));
                $url = esc_url($attributes['url'] ?? '#');
                $variant = !empty($attributes['variant']) ? sanitize_html_class($attributes['variant']) : 'primary';
                $size = !empty($attributes['size']) ? ' btn-' . sanitize_html_class($attributes['size']) : '';
                $full = !empty($attributes['fullWidth']) ? ' w-100' : '';
                $base_class = !empty($attributes['btnClass']) ? trim((string)$attributes['btnClass']) : ('btn btn-' . $variant . $size . $full);
                $style = '';
                if (!empty($attributes['backgroundColor'])) $style .= 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';border-color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
                if (!empty($attributes['textColor'])) $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-button-wrap']);
                return "<div {$wrapper}><a class=\"" . esc_attr($base_class) . "\" style=\"" . esc_attr($style) . "\" href=\"{$url}\">{$text}</a></div>";

            case 'cards':
                $cols = max(1, intval($attributes['columnsMd'] ?? 3));
                $gap = max(0, intval($attributes['gap'] ?? 3));
                $wrapper = get_block_wrapper_attributes(['class' => "wpbb-cards row row-cols-1 row-cols-md-{$cols} g-{$gap}{$extra}"]);
                return "<div {$wrapper}>{$content}</div>";

            case 'card':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-card-item card h-100' . $extra]);
                return "<div {$wrapper}><div class=\"card-body\">{$content}</div></div>";

            case 'accordion':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-accordion accordion' . $extra]);
                return "<div {$wrapper}>{$content}</div>";

            case 'accordion-item':
                $title = esc_html($attributes['title'] ?? __('Accordion item', 'wp-bbuilder'));
                $id = 'wpbb-acc-' . wp_unique_id();
                $wrapper = get_block_wrapper_attributes(['class' => 'accordion-item' . $extra]);
                return "<div {$wrapper}><h2 class=\"accordion-header\"><button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#{$id}\">{$title}</button></h2><div id=\"{$id}\" class=\"accordion-collapse collapse\"><div class=\"accordion-body\">{$content}</div></div></div>";

            case 'cta-card':
                $title = esc_html($attributes['title'] ?? __('CTA Card', 'wp-bbuilder'));
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Learn more', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-card card h-100' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><p class=\"card-text\">{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></div>";

            case 'cta-section':
                $title = esc_html($attributes['title'] ?? __('CTA Section', 'wp-bbuilder'));
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Get started', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-section text-center py-5' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<section {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"container-fluid\"><h2>{$title}</h2><p>{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></section>";

            case 'google-map':
                $url = esc_url($attributes['embedUrl'] ?? '');
                $height = esc_attr($attributes['height'] ?? '380px');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-google-map' . $extra]);
                if (!$url) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add embed URL', 'wp-bbuilder') . "</div></div>";
                $style = !empty($attributes["mapFilter"]) ? "filter:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["mapFilter"]) . ";" : ""; return "<div {$wrapper}><iframe src=\"{$url}\" style=\"width:100%;height:{$height};border:0;{$style}\" loading=\"lazy\" allowfullscreen></iframe></div>";

            case 'menu-option':
                $title = esc_html($attributes['title'] ?? __('Menu Item', 'wp-bbuilder'));
                $badge = esc_html($attributes['badge'] ?? '');
                $text = esc_html($attributes['text'] ?? '');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-menu-option d-flex justify-content-between align-items-start gap-3 py-2' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div><strong>{$title}</strong><div>{$text}</div></div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>";

            case 'sitemap':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-sitemap' . $extra]);
                $title = esc_html($attributes["title"] ?? __("Sitemap", "wp-bbuilder")); $pages = !empty($attributes["showPages"]) ? wp_list_pages(["echo"=>0,"title_li"=>""]) : ""; $posts = ""; if (!empty($attributes["showPosts"])) { $items = get_posts(["numberposts"=>10,"post_status"=>"publish"]); if ($items) { $posts .= "<ul>"; foreach ($items as $p) $posts .= "<li><a href=\"" . esc_url(get_permalink($p)) . "\">" . esc_html(get_the_title($p)) . "</a></li>"; $posts .= "</ul>"; } } return "<div {$wrapper}><h3>{$title}</h3>" . ($pages ? "<ul>{$pages}</ul>" : "") . $posts . "</div>";

            case 'soc-follow-block':
                $title = esc_html($attributes['title'] ?? __('Follow Us', 'wp-bbuilder'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-follow d-flex gap-2 align-items-center' . $extra]);
                $facebook = esc_url($attributes["facebook"] ?? ""); $instagram = esc_url($attributes["instagram"] ?? ""); $linkedin = esc_url($attributes["linkedin"] ?? ""); $x = esc_url($attributes["x"] ?? ""); $html = "<div {$wrapper}><strong>{$title}</strong>"; if ($facebook) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$facebook}\">FB</a>"; if ($instagram) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$instagram}\">IG</a>"; if ($linkedin) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$linkedin}\">LI</a>"; if ($x) $html .= "<a class=\"btn btn-outline-secondary btn-sm\" href=\"{$x}\">X</a>"; $html .= "</div>"; return $html;

            case 'soc-share':
                $title = esc_html($attributes['title'] ?? __('Share', 'wp-bbuilder'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-share d-flex gap-2 align-items-center' . $extra]);
                $title = esc_html($attributes["title"] ?? __("Share", "wp-bbuilder")); $iconStyle = $attributes["iconStyle"] ?? "buttons"; $wrapper = get_block_wrapper_attributes(["class" => "wpbb-soc-share d-flex gap-2 align-items-center" . $extra]); $shareUrl = rawurlencode(get_permalink()); $shareTitle = rawurlencode(get_the_title()); $btnClass = $iconStyle === "icons" ? "btn btn-light btn-sm rounded-circle" : "btn btn-outline-secondary btn-sm"; return "<div {$wrapper}><strong>{$title}</strong><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://www.facebook.com/sharer/sharer.php?u={$shareUrl}\">FB</a><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://twitter.com/intent/tweet?url={$shareUrl}&text={$shareTitle}\">X</a><a class=\"{$btnClass}\" target=\"_blank\" rel=\"noopener\" href=\"https://www.linkedin.com/sharing/share-offsite/?url={$shareUrl}\">LI</a></div>";

            case 'video':
                $url = esc_url($attributes['videoUrl'] ?? '');
                $ratio = !empty($attributes['ratioClass']) ? esc_attr($attributes['ratioClass']) : 'ratio ratio-16x9';
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-video' . $extra]);
                if (!$url) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add video URL', 'wp-bbuilder') . "</div></div>"; $poster = !empty($attributes["poster"]) ? "poster=\"" . esc_url($attributes["poster"]) . "\"" : ""; if (preg_match('~(youtube|youtu\.be|vimeo)~', $url)) { return "<div {$wrapper}><div class=\"{$ratio}\"><iframe src=\"{$url}\" allowfullscreen loading=\"lazy\"></iframe></div></div>"; } return "<div {$wrapper}><video controls {$poster} style=\"width:100%;height:auto\"><source src=\"{$url}\"></video></div>";

            case 'row-section':
                $classes = ['wpbb-row-section'];
                if (!empty($attributes['sectionClass'])) $classes[] = $attributes['sectionClass'];
                if (!empty($attributes['backgroundClass'])) $classes[] = $attributes['backgroundClass'];
                $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_filter($classes)) . $extra]);
                $container = !empty($attributes['containerClass']) ? sanitize_html_class($attributes['containerClass']) : 'container-fluid'; if (!empty($attributes["maxWidth"])) { $style = 'max-width:' . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["maxWidth"]) . ';margin-left:auto;margin-right:auto;'; return "<section {$wrapper}><div class=\"{$container}\" style=\"" . esc_attr($style) . "\">{$content}</div></section>"; } return "<section {$wrapper}><div class=\"{$container}\">{$content}</div></section>";

            case 'tabs':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-tabs' . $extra]);
                return "<div {$wrapper}>{$content}</div>";

            case 'tab-item':
                $title = esc_html($attributes['title'] ?? __('Tab', 'wp-bbuilder'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-tab-item' . $extra]);
                return "<div {$wrapper}><div class=\"wpbb-tab-title\">{$title}</div><div class=\"wpbb-tab-panel\">{$content}</div></div>";

            default:
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-' . sanitize_html_class($name) . $extra]);
                return "<div {$wrapper}>{$content}</div>";
        }
    }

    public function render_table_block($attributes, $content, $block) {
        $csv = (string) ($attributes['csvText'] ?? '');
        $delimiter = !empty($attributes['delimiter']) ? $attributes['delimiter'] : ',';
        $rows = preg_split('/\r\n|\r|\n/', trim($csv));
        $parsed = [];
        foreach ($rows as $row) {
            if ($row === '') continue;
            $parsed[] = str_getcsv($row, $delimiter);
        }
        if (empty($parsed)) return '';
        $useHeader = !empty($attributes['useFirstRowHeader']);
        $headers = $useHeader ? array_shift($parsed) : [];
        $table_classes = trim((string) ($attributes['tableClass'] ?? 'table'));
        if (!empty($attributes['small'])) $table_classes .= ' table-sm';
        if (!empty($attributes['bordered'])) $table_classes .= ' table-bordered';
        $table_html = '<table class="' . esc_attr(trim($table_classes)) . '">';
        if ($useHeader && !empty($headers)) {
            $table_html .= '<thead><tr>';
            foreach ($headers as $header) $table_html .= '<th>' . esc_html($header) . '</th>';
            $table_html .= '</tr></thead>';
        }
        $table_html .= '<tbody>';
        foreach ($parsed as $row) {
            $table_html .= '<tr>';
            foreach ($row as $cell) $table_html .= '<td>' . esc_html($cell) . '</td>';
            $table_html .= '</tr>';
        }
        $table_html .= '</tbody></table>';
        if (!empty($attributes['responsive'])) {
            $table_html = '<div class="table-responsive">' . $table_html . '</div>';
        }
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-table-block']);
        return '<div ' . $wrapper . '>' . $table_html . '</div>';
    }

    public function render_dynamic_form($attributes, $content, $block) {
        $title = esc_html($attributes['formTitle'] ?? __('Contact form', 'wp-bbuilder'));
        $recipient = esc_attr($attributes['recipient'] ?? wpbb_get_option('default_recipient_email', get_option('admin_email')));
        $subject = esc_attr($attributes['emailSubject'] ?? __('New form submission', 'wp-bbuilder'));
        $success = esc_attr($attributes['successMessage'] ?? wpbb_get_option('default_success_message', __('Thank you for your submission!', 'wp-bbuilder')));
        $validation = esc_attr(wpbb_get_option('default_validation_text', __('Please fill in all required fields correctly.', 'wp-bbuilder')));
        $submit_text = esc_html($attributes['submitText'] ?? __('Submit', 'wp-bbuilder'));
        $btn_class = esc_attr($attributes['buttonClass'] ?? wpbb_get_option('button_class', 'btn btn-primary'));
        $form_class = esc_attr($attributes['formClass'] ?? wpbb_get_option('form_class', 'wpbb-form'));
        $style = sanitize_html_class($attributes['stylePreset'] ?? 'default');
        $label_pos = sanitize_html_class($attributes['labelPosition'] ?? 'top');
        $gap = max(0, intval($attributes['gap'] ?? 3));
        $fields = wpbb_parse_fields_json($attributes['fieldsJson'] ?? '');
        $hcaptcha_site_key = wpbb_get_option('hcaptcha_site_key', '');
        $recaptcha_site_key = wpbb_get_option('recaptcha_site_key', '');

        if (empty($fields)) {
            $fields = [
                ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true, 'width' => 6, 'placeholder' => ''],
                ['type' => 'email', 'name' => 'email', 'label' => 'Email', 'required' => true, 'width' => 6, 'placeholder' => ''],
                ['type' => 'phone', 'name' => 'phone', 'label' => 'Phone', 'required' => false, 'width' => 6, 'placeholder' => ''],
                ['type' => 'select', 'name' => 'language', 'label' => 'Language', 'required' => false, 'width' => 6, 'placeholder' => '', 'options' => "English\nLatvian\nRussian"],
                ['type' => 'textarea', 'name' => 'message', 'label' => 'Message', 'required' => true, 'width' => 12, 'placeholder' => ''],
            ];
        }

        ob_start(); ?>
        <div <?php echo get_block_wrapper_attributes(['class' => "wpbb-dynamic-form-wrap style-{$style} labels-{$label_pos}"]); ?>>
            <?php if (!empty($attributes['showTitle'])): ?>
                <h3 class="wpbb-form-title"><?php echo $title; ?></h3>
            <?php endif; ?>
            <form class="<?php echo $form_class; ?> wpbb-dynamic-form" data-recipient="<?php echo $recipient; ?>" data-subject="<?php echo $subject; ?>" data-success="<?php echo $success; ?>" data-validation="<?php echo $validation; ?>">
                <div class="row g-<?php echo $gap; ?>">
                    <?php foreach ($fields as $field):
                        $type = sanitize_key($field['type'] ?? 'text');
                        $name = sanitize_key($field['name'] ?? 'field');
                        $label = esc_html($field['label'] ?? $name);
                        $required = !empty($field['required']);
                        $placeholder = esc_attr($field['placeholder'] ?? '');
                        $width = max(1, min(12, intval($field['width'] ?? 6)));
                        $input_id = 'wpbb-' . $name . '-' . wp_unique_id();
                        $options = isset($field['options']) ? preg_split('/\r\n|\r|\n/', (string) $field['options']) : [];
                    ?>
                    <div class="col-12 col-md-<?php echo $width; ?>">
                        <div class="wpbb-field wpbb-field--<?php echo esc_attr($type); ?>">
                            <?php if ($label_pos !== 'hidden'): ?>
                                <label class="form-label" for="<?php echo esc_attr($input_id); ?>"><?php echo $label; ?><?php echo $required ? ' *' : ''; ?></label>
                            <?php endif; ?>

                            <?php if ($type === 'textarea'): ?>
                                <textarea id="<?php echo esc_attr($input_id); ?>" class="form-control" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo $placeholder; ?>" rows="4" <?php echo $required ? 'required' : ''; ?>></textarea>

                            <?php elseif ($type === 'select'): ?>
                                <select id="<?php echo esc_attr($input_id); ?>" class="form-select" name="<?php echo esc_attr($name); ?>" <?php echo $required ? 'required' : ''; ?>>
                                    <option value=""><?php echo esc_html($placeholder ?: __('Select option', 'wp-bbuilder')); ?></option>
                                    <?php foreach ($options as $option):
                                        $option = trim($option);
                                        if ($option === '') continue; ?>
                                        <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                    <?php endforeach; ?>
                                </select>

                            <?php else: ?>
                                <input id="<?php echo esc_attr($input_id); ?>" class="form-control" type="<?php echo esc_attr($type === 'email' ? 'email' : ($type === 'phone' ? 'tel' : 'text')); ?>" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required ? 'required' : ''; ?>>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if ($hcaptcha_site_key || $recaptcha_site_key): ?>
                    <div class="col-12">
                        <div class="wpbb-field wpbb-field--captcha">
                            <div class="wpbb-captcha-note">
                                <?php
                                if ($hcaptcha_site_key && $recaptcha_site_key) {
                                    esc_html_e('hCaptcha and reCAPTCHA configured in admin settings.', 'wp-bbuilder');
                                } elseif ($hcaptcha_site_key) {
                                    esc_html_e('hCaptcha configured in admin settings.', 'wp-bbuilder');
                                } else {
                                    esc_html_e('reCAPTCHA configured in admin settings.', 'wp-bbuilder');
                                }
                                ?>
                            </div>
                            <input type="hidden" name="wpbb_captcha_enabled" value="1">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="wpbb-form-message mt-3" aria-live="polite"></div>
                <button type="submit" class="<?php echo $btn_class; ?> mt-3"><?php echo $submit_text; ?></button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
