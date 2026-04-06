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
        add_action('wp_ajax_wpbb_ajax_search', [$this, 'ajax_search']);
        add_action('wp_ajax_nopriv_wpbb_ajax_search', [$this, 'ajax_search']);
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

    
    private function wpbb_svg_icon($name) {
        $icons = [
            'facebook' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.6V4.8c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 4v2.3H8v3h2.7v8h2.8z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2.2A2.8 2.8 0 0 0 4.2 7v10A2.8 2.8 0 0 0 7 19.8h10a2.8 2.8 0 0 0 2.8-2.8V7A2.8 2.8 0 0 0 17 4.2H7zm10.5 1.6a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2.2A2.8 2.8 0 1 0 12 14.8 2.8 2.8 0 0 0 12 9.2z"/></svg>',
            'linkedin' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M6.94 8.5H4V20h2.94V8.5zM5.47 4A1.72 1.72 0 1 0 5.5 7.44 1.72 1.72 0 0 0 5.47 4zM20 12.9c0-3.1-1.66-4.54-3.88-4.54-1.8 0-2.6.99-3.05 1.68V8.5H10.1c.04 1 .04 11.5 0 11.5h2.97v-6.42c0-.34.02-.68.12-.92.27-.68.88-1.38 1.91-1.38 1.35 0 1.89 1.03 1.89 2.54V20H20v-7.1z"/></svg>',
            'x' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M18.9 3H21l-4.6 5.2L21.8 21h-5.7l-4.5-5.8L6.5 21H4.4l5-5.7L2.2 3H8l4.1 5.4L18.9 3zm-2 16h1.6L7 4.9H5.3L16.9 19z"/></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M23 12s0-3.5-.45-5.2a2.7 2.7 0 0 0-1.9-1.9C18.9 4.4 12 4.4 12 4.4s-6.9 0-8.65.5a2.7 2.7 0 0 0-1.9 1.9C1 8.5 1 12 1 12s0 3.5.45 5.2a2.7 2.7 0 0 0 1.9 1.9c1.75.5 8.65.5 8.65.5s6.9 0 8.65-.5a2.7 2.7 0 0 0 1.9-1.9C23 15.5 23 12 23 12zM10 15.5v-7l6 3.5-6 3.5z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M20.5 3.5A11.8 11.8 0 0 0 1.8 17.7L.5 23.5l5.9-1.3A11.8 11.8 0 1 0 20.5 3.5zm-8.7 18a9.7 9.7 0 0 1-4.9-1.3l-.4-.2-3.5.8.8-3.4-.2-.4a9.7 9.7 0 1 1 8.2 4.5zm5.3-7.2c-.3-.1-1.8-.9-2.1-1s-.5-.1-.7.1-.8 1-1 1.1-.4.2-.7 0a7.9 7.9 0 0 1-2.3-1.4 8.8 8.8 0 0 1-1.6-2c-.2-.3 0-.5.1-.7l.5-.6.2-.4a.8.8 0 0 0 0-.5c-.1-.1-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 0 0-.8.4c-.3.3-1 1-1 2.4s1 2.7 1.2 2.9c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.4.2 1.9.1.6-.1 1.8-.8 2.1-1.5.3-.7.3-1.4.2-1.5-.1-.1-.3-.2-.6-.3z"/></svg>',
            'email' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm0 2v.5l9 5.6 9-5.6V7H3zm18 10V9.8l-8.5 5.3a1 1 0 0 1-1 0L3 9.8V17h18z"/></svg>',
        ];
        return $icons[$name] ?? '<span class="wpbb-social-icon__glyph">*</span>';
    }


    private function wpbb_compile_scoped_scss($selector, $scss) {
        $scss = trim((string) $scss);
        if ($scss === '') return '';
        $scss = preg_replace('!/\*.*?\*/!s', '', $scss);
        if (strpos($scss, '{') === false) return $selector . '{' . $scss . '}';
        $result = '';
        if (preg_match_all('/([^{}]+)\{((?:[^{}]|\{[^{}]*\})*)\}/s', $scss, $blocks, PREG_SET_ORDER)) {
            foreach ($blocks as $block) {
                $parent = trim($block[1]);
                $body = trim($block[2]);
                $fullParent = strpos($parent, '&') !== false ? str_replace('&', $selector, $parent) : $selector . ' ' . $parent;
                if (preg_match_all('/([^{}]+)\{([^{}]*)\}/s', $body, $children, PREG_SET_ORDER)) {
                    foreach ($children as $child) {
                        $childSel = trim($child[1]);
                        $childBody = trim($child[2]);
                        $finalSel = strpos($childSel, '&') !== false ? str_replace('&', $fullParent, $childSel) : $fullParent . ' ' . $childSel;
                        $result .= $finalSel . '{' . $childBody . '}';
                    }
                    $plain = trim(preg_replace('/([^{}]+)\{([^{}]*)\}/s', '', $body));
                    if ($plain !== '') $result .= $fullParent . '{' . $plain . '}';
                } else {
                    $result .= $fullParent . '{' . $body . '}';
                }
            }
        }
        return $result ?: $selector . '{' . $scss . '}';
    }


    private function wpbb_build_spacing_inline($attributes) {
        $style = '';
        $pairs = [
            ['paddingTop','paddingTopUnit','padding-top'],
            ['paddingRight','paddingRightUnit','padding-right'],
            ['paddingBottom','paddingBottomUnit','padding-bottom'],
            ['paddingLeft','paddingLeftUnit','padding-left'],
            ['marginTop','marginTopUnit','margin-top'],
            ['marginRight','marginRightUnit','margin-right'],
            ['marginBottom','marginBottomUnit','margin-bottom'],
            ['marginLeft','marginLeftUnit','margin-left'],
        ];
        foreach ($pairs as $pair) {
            $num = isset($attributes[$pair[0]]) ? $attributes[$pair[0]] : null;
            $unit = isset($attributes[$pair[1]]) ? $attributes[$pair[1]] : 'px';
            if ($num !== null && $num !== '' && is_numeric($num) && floatval($num) != 0.0) {
                $style .= $pair[2] . ':' . floatval($num) . preg_replace('/[^a-z%]/i', '', (string)$unit) . ';';
            }
        }
        return $style;
    }

public function register_assets() {
        wp_register_script('wpbb-editor', WPBB_PLUGIN_URL . 'assets/editor.js', ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data'], WPBB_VERSION, true);
        wp_register_script('wpbb-editor-enhancer', WPBB_PLUGIN_URL . 'assets/editor-enhancer.js', ['wp-dom-ready'], WPBB_VERSION, true);
        wp_register_script('wpbb-form-view', WPBB_PLUGIN_URL . 'assets/form.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-copy-code', WPBB_PLUGIN_URL . 'assets/copy-code.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-ajax-search', WPBB_PLUGIN_URL . 'assets/ajax-search.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js', [], '4.4.3', true);
        wp_register_script('wpbb-chart-view', WPBB_PLUGIN_URL . 'assets/chart-view.js', ['wpbb-chartjs'], WPBB_VERSION, true);
        wp_localize_script('wpbb-form-view', 'wpbbForm', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpbb_form_nonce'),
            'error' => wpbb_get_option('default_error_message', __('Something went wrong. Please try again.', 'wp-bbuilder')),
            'validationText' => wpbb_get_option('default_validation_text', __('Please fill in all required fields correctly.', 'wp-bbuilder')),
        ]);
        wp_register_style('wpbb-shared', WPBB_PLUGIN_URL . 'assets/shared.css', [], WPBB_VERSION);
        wp_register_style('wpbb-editor-style', WPBB_PLUGIN_URL . 'assets/editor.css', ['wpbb-shared'], WPBB_VERSION);
        wp_register_style('wpbb-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.1.4');
        wp_register_script('wpbb-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.1.4', true);
        wp_register_script('wpbb-swiper-init', WPBB_PLUGIN_URL . 'assets/swiper-init.js', ['wpbb-swiper'], WPBB_VERSION, true);
    }

    public function register_category($categories) {
        array_unshift($categories, [
            'slug' => 'wpbb',
            'title' => __('BBuilder', 'wp-bbuilder'),
            'icon' => null,
        ]);
        return $categories;
    }

    public function register_blocks() {
        foreach (array_filter(wpbb_get_blocks_list(), function($s){ return $s !== 'row-section'; }) as $slug) {
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
            } elseif ($slug === 'swiper') {
                $args['style'] = ['wpbb-shared','wpbb-swiper'];
                $args['script'] = 'wpbb-swiper-init';
                $args['render_callback'] = [$this, 'render_swiper_block'];
            } elseif ($slug === 'weather') {
                $args['render_callback'] = [$this, 'render_weather_block'];
            } elseif ($slug === 'varda-dienas') {
                $args['render_callback'] = [$this, 'render_varda_dienas_block'];
            } elseif ($slug === 'ajax-search') {
                $args['script'] = 'wpbb-ajax-search';
                $args['render_callback'] = [$this, 'render_ajax_search_block'];
            } elseif ($slug === 'pricecards') {
                $args['render_callback'] = [$this, 'render_pricecards_block'];
            } elseif ($slug === 'catalogue') {
                $args['render_callback'] = [$this, 'render_catalogue_block'];
            } elseif ($slug === 'code-display') {
                $args['script'] = 'wpbb-copy-code';
                $args['render_callback'] = [$this, 'render_code_display_block'];
            } elseif ($slug === 'countdown-timer') {
                $args['script'] = 'wpbb-chart-view';
                $args['render_callback'] = [$this, 'render_countdown_timer_block'];
            } elseif ($slug === 'chart') {
                $args['script'] = 'wpbb-chart-view';
                $args['render_callback'] = [$this, 'render_chart_block'];
            } elseif ($slug === 'fun-fact') {
                $args['render_callback'] = [$this, 'render_fun_fact_block'];
            } elseif ($slug === 'mailchimp') {
                $args['render_callback'] = [$this, 'render_mailchimp_block'];
            } elseif ($slug === 'bootstrap-div') {
                $args['render_callback'] = [$this, 'render_bootstrap_div_block'];
            } else {
                $args['render_callback'] = [$this, 'render_generic_block'];
            }

            if ($slug === 'column') $args['parent'] = ['wpbb/row'];
            if ($slug === 'bootstrap-div') $args['supports']['innerBlocks'] = true;
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
            'row' => 'grid-view','cta-card' => 'megaphone','cta-section' => 'cover-image','google-map' => 'location-alt','menu-option' => 'menu','sitemap' => 'networking','soc-follow-block' => 'share','soc-share' => 'share-alt2',
            'tab-item' => 'editor-table',
            'tabs' => 'index-card',
            'table' => 'table-col-after',
            'swiper' => 'images-alt2','weather' => 'cloud','varda-dienas' => 'calendar-alt','ajax-search' => 'search','pricecards' => 'index-card','catalogue' => 'screenoptions','code-display' => 'editor-code','countdown-timer' => 'clock','chart' => 'chart-bar','fun-fact' => 'star-filled','mailchimp' => 'email','bootstrap-div' => 'screenoptions',
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
                    'bootstrapClasses' => ['type' => 'string', 'default' => ''],'uniqueId' => ['type' => 'string', 'default' => ''],'customCss' => ['type' => 'string', 'default' => ''],'customScss' => ['type' => 'string', 'default' => ''],
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
                    'uniqueId' => ['type' => 'string', 'default' => ''],
                    'customCss' => ['type' => 'string', 'default' => ''],
                    'customScss' => ['type' => 'string', 'default' => ''],
                    'orderClass' => ['type' => 'string', 'default' => ''],'visibilityClass' => ['type' => 'string', 'default' => ''],'visibilityXs' => ['type' => 'boolean', 'default' => true],'visibilitySm' => ['type' => 'boolean', 'default' => true],'visibilityMd' => ['type' => 'boolean', 'default' => true],'visibilityLg' => ['type' => 'boolean', 'default' => true],'visibilityXl' => ['type' => 'boolean', 'default' => true],'animationClass' => ['type' => 'string', 'default' => ''],'paddingTop' => ['type' => 'number', 'default' => 0],'paddingTopUnit' => ['type' => 'string', 'default' => 'px'],'paddingRight' => ['type' => 'number', 'default' => 0],'paddingRightUnit' => ['type' => 'string', 'default' => 'px'],'paddingBottom' => ['type' => 'number', 'default' => 0],'paddingBottomUnit' => ['type' => 'string', 'default' => 'px'],'paddingLeft' => ['type' => 'number', 'default' => 0],'paddingLeftUnit' => ['type' => 'string', 'default' => 'px'],'marginTop' => ['type' => 'number', 'default' => 0],'marginTopUnit' => ['type' => 'string', 'default' => 'px'],'marginRight' => ['type' => 'number', 'default' => 0],'marginRightUnit' => ['type' => 'string', 'default' => 'px'],'marginBottom' => ['type' => 'number', 'default' => 0],'marginBottomUnit' => ['type' => 'string', 'default' => 'px'],'marginLeft' => ['type' => 'number', 'default' => 0],'marginLeftUnit' => ['type' => 'string', 'default' => 'px'],
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
            case 'weather':
                return [
                    'title' => ['type' => 'string', 'default' => 'Laikapstākļi'],
                    'location' => ['type' => 'string', 'default' => 'Rīga'],
                    'lang' => ['type' => 'string', 'default' => 'lv'],
                    'apiKey' => ['type' => 'string', 'default' => ''],
                    'showTemp' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'varda-dienas':
                return [
                    'title' => ['type' => 'string', 'default' => 'Vārda dienas'],
                    'dateText' => ['type' => 'string', 'default' => 'Šodien'],
                    'names' => ['type' => 'string', 'default' => 'Alise, Madara'],'namesJson' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'ajax-search':
                return [
                    'title' => ['type' => 'string', 'default' => 'Meklēšana'],
                    'placeholder' => ['type' => 'string', 'default' => 'Meklēt...'],
                    'resultsLimit' => ['type' => 'number', 'default' => 10],
                    'searchWooBy' => ['type' => 'string', 'default' => 'title'],
                    'sortBy' => ['type' => 'string', 'default' => 'relevance'],
                    'showExcerpt' => ['type' => 'boolean', 'default' => true],
                    'showPrice' => ['type' => 'boolean', 'default' => true],
                    'showButton' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'pricecards':
                return [
                    'title' => ['type' => 'string', 'default' => 'Cenas'],
                    'cardsJson' => ['type' => 'string', 'default' => ''],
                    'styleVariant' => ['type' => 'string', 'default' => 'default'],
                    'showFeatured' => ['type' => 'boolean', 'default' => false],
                    'currency' => ['type' => 'string', 'default' => '€'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'catalogue':
                return [
                    'title' => ['type' => 'string', 'default' => 'Katalogs'],
                    'category' => ['type' => 'string', 'default' => ''],
                    'postsToShow' => ['type' => 'number', 'default' => 6],
                    'postType' => ['type' => 'string', 'default' => 'post'],
                    'taxonomy' => ['type' => 'string', 'default' => 'category'],
                    'sortBy' => ['type' => 'string', 'default' => 'date'],
                    'sortOrder' => ['type' => 'string', 'default' => 'DESC'],
                    'showImage' => ['type' => 'boolean', 'default' => true],
                    'showExcerpt' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'code-display':
                return [
                    'title' => ['type' => 'string', 'default' => 'Code'],
                    'code' => ['type' => 'string', 'default' => ''],
                    'language' => ['type' => 'string', 'default' => 'html'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'countdown-timer':
                return [
                    'title' => ['type' => 'string', 'default' => 'Countdown'],
                    'targetDate' => ['type' => 'string', 'default' => '2030-01-01T00:00:00'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'chart':
                return [
                    'title' => ['type' => 'string', 'default' => 'Chart'],
                    'chartType' => ['type' => 'string', 'default' => 'bar'],
                    'chartDataJson' => ['type' => 'string', 'default' => '{"labels":["Jan","Feb","Mar"],"datasets":[{"label":"Sales","data":[12,19,7]}]}'],
                    'chartOptionsJson' => ['type' => 'string', 'default' => '{"responsive":true,"plugins":{"legend":{"display":true}}}'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'fun-fact':
                return [
                    'number' => ['type' => 'string', 'default' => '100+'],
                    'label' => ['type' => 'string', 'default' => 'Projects'],
                    'icon' => ['type' => 'string', 'default' => '⭐'],
                    'styleVariant' => ['type' => 'string', 'default' => 'default'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'mailchimp':
                return [
                    'title' => ['type' => 'string', 'default' => 'Subscribe'],
                    'text' => ['type' => 'string', 'default' => 'Join our newsletter'],
                    'actionUrl' => ['type' => 'string', 'default' => ''],
                    'audienceFieldName' => ['type' => 'string', 'default' => 'EMAIL'],
                    'showNameField' => ['type' => 'boolean', 'default' => false],
                    'buttonText' => ['type' => 'string', 'default' => 'Subscribe'],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'swiper':
                return [
                    'slidesJson' => ['type' => 'string', 'default' => ''],
                    'slidesPerView' => ['type' => 'number', 'default' => 1],
                    'spaceBetween' => ['type' => 'number', 'default' => 20],
                    'speed' => ['type' => 'number', 'default' => 600],
                    'loop' => ['type' => 'boolean', 'default' => true],
                    'autoplay' => ['type' => 'boolean', 'default' => false],
                    'demoStyle' => ['type' => 'string', 'default' => 'cards'],
                    'showPagination' => ['type' => 'boolean', 'default' => true],
                    'showNavigation' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'accordion-item':
            case 'tab-item':
                return [
                    'title' => ['type' => 'string', 'default' => ucfirst(str_replace('-', ' ', $slug))],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'cta-card':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('CTA Card', 'wp-bbuilder'))); $titleTag = in_array(($attributes['titleTag'] ?? 'h3'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? $attributes['titleTag'] : 'h3';
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Learn more', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $schemaType = !empty($attributes['schemaType']) ? sanitize_html_class($attributes['schemaType']) : 'CreativeWork';
                $schemaAttr = !empty($attributes['schemaEnable']) ? ' itemscope itemtype="https://schema.org/' . esc_attr($schemaType) . '"' : '';
                $schemaPrice = !empty($attributes['schemaPrice']) ? '<meta itemprop="price" content="' . esc_attr($attributes['schemaPrice']) . '">' : '';
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-card card h-100' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";"; return "<div {$wrapper}{$schemaAttr} style=\"" . esc_attr($style) . "\"><div class=\"card-body\">{$schemaPrice}<{$titleTag} class=\"card-title {$titleTag}\">{$title}</{$titleTag}><p class=\"card-text\">{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></div>";

            case 'cta-section':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('CTA Section', 'wp-bbuilder'))); $titleTag = in_array(($attributes['titleTag'] ?? 'h2'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? $attributes['titleTag'] : 'h2';
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Get started', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-section text-center py-5' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";"; return "<section {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"container-fluid\"><h2>{$title}</h2><p>{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></section>";

            case 'google-map':
                $url = esc_url($attributes['embedUrl'] ?? '');
                $height = esc_attr($attributes['height'] ?? '380px');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-google-map' . $extra]);
                if (!$url) return "<div {$wrapper}><div class=\"wpbb-empty-note\">" . esc_html__('Add embed URL', 'wp-bbuilder') . "</div></div>";
                $style = !empty($attributes["mapFilter"]) ? "filter:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["mapFilter"]) . ";" : ""; return "<div {$wrapper}><iframe src=\"{$url}\" style=\"width:100%;height:{$height};border:0;{$style}\" loading=\"lazy\" allowfullscreen></iframe></div>";

            case 'menu-option':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('Menu Item', 'wp-bbuilder'))); $titleTag = in_array(($attributes['titleTag'] ?? 'h4'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'h4') : 'h4';
                $badge = esc_html($attributes['badge'] ?? '');
                $text = esc_html($attributes['text'] ?? '');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-menu-option d-flex justify-content-between align-items-start gap-3 py-2' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";"; $menuSlug = sanitize_text_field($attributes["menuSlug"] ?? ""); $schemaEnable = !empty($attributes["schemaEnable"]); $price = esc_html($attributes["price"] ?? ""); $titleHtml = "<{$titleTag} class=\"{$titleTag}\">{$title}</{$titleTag}>"; $menuHtml = ""; if ($menuSlug) { $menuHtml = wp_nav_menu(["menu" => $menuSlug, "echo" => false, "container" => false, "fallback_cb" => false]); } $body = $titleHtml . "<div>{$text}</div>" . ($menuHtml ?: "") . ($price ? "<div class=\"wpbb-menu-price\">{$price}</div>" : ""); if ($schemaEnable) { return "<div {$wrapper} itemscope itemtype=\"https://schema.org/MenuItem\" style=\"" . esc_attr($style) . "\"><div itemprop=\"name\">{$body}</div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>"; } return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div>{$body}</div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>";

            case 'sitemap':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-sitemap' . $extra]);
                $title = esc_html(wpbb_translate_string($attributes["title"] ?? __("Sitemap", "wp-bbuilder"))); $titleTag = in_array(($attributes["titleTag"] ?? "h3"), ["h1","h2","h3","h4","h5","h6","div","p","span"], true) ? ($attributes["titleTag"] ?: "h3") : "h3"; $pages = !empty($attributes["showPages"]) ? wp_list_pages(["echo"=>0,"title_li"=>""]) : ""; $posts = ""; if (!empty($attributes["showPosts"])) { $items = get_posts(["numberposts"=>10,"post_status"=>"publish"]); if ($items) { $posts .= "<ul>"; foreach ($items as $p) $posts .= "<li><a href=\"" . esc_url(get_permalink($p)) . "\">" . esc_html(get_the_title($p)) . "</a></li>"; $posts .= "</ul>"; } } return "<div {$wrapper}><{$titleTag} class=\"{$titleTag}\">{$title}</{$titleTag}>" . ($pages ? "<ul>{$pages}</ul>" : "") . $posts . "</div>";

            case 'soc-follow-block':
                return [
                    'title' => ['type' => 'string', 'default' => 'Follow Us'],
                    'titleTag' => ['type' => 'string', 'default' => 'span'],
                    'socialStyle' => ['type' => 'string', 'default' => 'icons'], // icons, buttons, minimal
                    'iconSize' => ['type' => 'string', 'default' => 'md'], // sm, md, lg
                    'iconShape' => ['type' => 'string', 'default' => 'rounded'], // square, rounded, circle
                    'iconBgColor' => ['type' => 'string', 'default' => ''],
                    'iconTextColor' => ['type' => 'string', 'default' => ''],
                    'showLabels' => ['type' => 'boolean', 'default' => false],
                    'facebook' => ['type' => 'string', 'default' => ''],
                    'instagram' => ['type' => 'string', 'default' => ''],
                    'linkedin' => ['type' => 'string', 'default' => ''],
                    'x' => ['type' => 'string', 'default' => ''],
                    'youtube' => ['type' => 'string', 'default' => ''],
                    'tiktok' => ['type' => 'string', 'default' => ''],
                    'pinterest' => ['type' => 'string', 'default' => ''],
                    'whatsapp' => ['type' => 'string', 'default' => ''],
                    'email' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => '']
                ];

            case 'soc-share':
                return [
                    'title' => ['type' => 'string', 'default' => 'Share'],
                    'titleTag' => ['type' => 'string', 'default' => 'span'],
                    'iconStyle' => ['type' => 'string', 'default' => 'icons'], // icons, buttons
                    'iconSize' => ['type' => 'string', 'default' => 'md'],
                    'iconShape' => ['type' => 'string', 'default' => 'rounded'],
                    'iconBgColor' => ['type' => 'string', 'default' => ''],
                    'iconColor' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => '']
                ];










            case 'row':
                $classes = ['row', 'wpbb-row'];
                foreach (['gutterX','gutterY','paddingClass','marginClass','backgroundClass','animationClass','displayClass','textUtilityClass','roundedClass','shadowClass','bootstrapClasses','utilityClasses','visibilityClass','className'] as $k) {
                    if (!empty($attributes[$k])) $classes[] = $attributes[$k];
                }
                if (!empty($attributes['align'])) $classes[] = 'justify-content-' . sanitize_html_class($attributes['align']);
                $uid = !empty($attributes['uniqueId']) ? sanitize_html_class($attributes['uniqueId']) : sanitize_html_class('wpbb-row-' . wp_unique_id());
                $style = $this->wpbb_build_spacing_inline($attributes);
                if (!empty($attributes['backgroundColor'])) $style .= 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
                if (!empty($attributes['textColor'])) $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
                if (!empty($attributes['maxWidth'])) $style .= 'max-width:' . preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)$attributes['maxWidth']) . ';margin-left:auto;margin-right:auto;';
                if (!empty($attributes['customStyle'])) $style .= (string)$attributes['customStyle'];
                $cssTag = !empty($attributes['customCss']) ? '<style>#' . $uid . '{' . wp_strip_all_tags((string)$attributes['customCss']) . '}</style>' : '';
                $scssTag = !empty($attributes['customScss']) ? '<style>' . $this->wpbb_compile_scoped_scss('#' . $uid, (string)$attributes['customScss']) . '</style>' : '';
                $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_filter($classes)) . $extra, 'style' => $style, 'id' => $uid]);
                return "{$cssTag}{$scssTag}<div {$wrapper}>{$content}</div>";

            case 'column':
                $classes = ['wpbb-column'];
                $bpMap = ['xs'=>'col','sm'=>'col-sm','md'=>'col-md','lg'=>'col-lg','xl'=>'col-xl','xxl'=>'col-xxl'];
                foreach ($bpMap as $bp => $prefix) {
                    $val = isset($attributes[$bp]) ? intval($attributes[$bp]) : 0;
                    if ($bp === 'xs' && $val <= 0) $val = 12;
                    if ($val > 0) $classes[] = $prefix . '-' . $val;
                }
                foreach (['orderClass','visibilityClass','animationClass','paddingClass','marginClass','backgroundClass','displayClass','textUtilityClass','roundedClass','shadowClass','bootstrapClasses','utilityClasses','className'] as $k) {
                    if (!empty($attributes[$k])) $classes[] = $attributes[$k];
                }
                $uid = !empty($attributes['uniqueId']) ? sanitize_html_class($attributes['uniqueId']) : sanitize_html_class('wpbb-col-' . wp_unique_id());
                $style = $this->wpbb_build_spacing_inline($attributes);
                if (!empty($attributes['backgroundColor'])) $style .= 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
                if (!empty($attributes['textColor'])) $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
                if (!empty($attributes['borderRadius'])) $style .= 'border-radius:' . preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)$attributes['borderRadius']) . ';';
                if (!empty($attributes['customStyle'])) $style .= (string)$attributes['customStyle'];
                $cssTag = !empty($attributes['customCss']) ? '<style>#' . $uid . '{' . wp_strip_all_tags((string)$attributes['customCss']) . '}</style>' : '';
                $scssTag = !empty($attributes['customScss']) ? '<style>' . $this->wpbb_compile_scoped_scss('#' . $uid, (string)$attributes['customScss']) . '</style>' : '';
                $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_filter($classes)) . $extra, 'style' => $style, 'id' => $uid]);
                return "{$cssTag}{$scssTag}<div {$wrapper}>{$content}</div>";

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
                return ['title'=>['type'=>'string','default'=>'CTA Card'],'titleTag'=>['type'=>'string','default'=>'h3'],'text'=>['type'=>'string','default'=>'Call to action text'],'buttonText'=>['type'=>'string','default'=>'Learn more'],'buttonUrl'=>['type'=>'string','default'=>'#'],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'cta-section':
                return ['title'=>['type'=>'string','default'=>'CTA Section'],'titleTag'=>['type'=>'string','default'=>'h2'],'text'=>['type'=>'string','default'=>'Call to action text'],'buttonText'=>['type'=>'string','default'=>'Get started'],'buttonUrl'=>['type'=>'string','default'=>'#'],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'backgroundImage'=>['type'=>'string','default'=>''],'parallax'=>['type'=>'boolean','default'=>false],'className'=>['type'=>'string','default'=>'']];
            case 'google-map':
                return ['embedUrl'=>['type'=>'string','default'=>''],'height'=>['type'=>'string','default'=>'380px'],'mapFilter'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'menu-option':
                return [
                    'title' => ['type' => 'string', 'default' => 'Menu'],
                    'menuSlug' => ['type' => 'string', 'default' => ''],
                    'showMenuTitle' => ['type' => 'boolean', 'default' => false],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'bootstrap-div':
                return [
                    'tagName' => ['type' => 'string', 'default' => 'div'],
                    'maxWidth' => ['type' => 'string', 'default' => ''],
                    'maxHeight' => ['type' => 'string', 'default' => ''],
                    'minHeight' => ['type' => 'string', 'default' => ''],
                    'backgroundColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                    'borderRadius' => ['type' => 'string', 'default' => ''],
                    'padding' => ['type' => 'string', 'default' => ''],
                    'margin' => ['type' => 'string', 'default' => ''],
                    'utilityClasses' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'menu-option':
                return ['title'=>['type'=>'string','default'=>'Menu Item'],'price'=>['type'=>'string','default'=>''],'text'=>['type'=>'string','default'=>''],'bgColor'=>['type'=>'string','default'=>''],'textColor'=>['type'=>'string','default'=>''],'className'=>['type'=>'string','default'=>'']];
            case 'sitemap':
                return ['title'=>['type'=>'string','default'=>'Sitemap'],'titleTag'=>['type'=>'string','default'=>'h3'],'showPages'=>['type'=>'boolean','default'=>true],'showPosts'=>['type'=>'boolean','default'=>false],'className'=>['type'=>'string','default'=>'']];
            case 'soc-follow-block':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('Follow Us', 'wp-bbuilder')));
                $titleTag = in_array(($attributes['titleTag'] ?? 'span'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'span') : 'span';
                $style = $attributes['socialStyle'] ?? 'icons'; // icons, buttons, minimal
                $size = $attributes['iconSize'] ?? 'md'; // sm, md, lg
                $shape = $attributes['iconShape'] ?? 'rounded'; // square, rounded, circle
                $showLabels = !empty($attributes['showLabels']);
                
                // Size configuration
                $sizeMap = ['sm' => '32px', 'md' => '44px', 'lg' => '56px'];
                $iconSize = $sizeMap[$size] ?? '44px';
                $fontSize = $size === 'sm' ? '14px' : ($size === 'lg' ? '20px' : '16px');
                
                $wrapper = get_block_wrapper_attributes([
                    'class' => 'wpbb-soc-follow wpbb-soc-style-' . $style . ' wpbb-soc-' . $size . $extra
                ]);
                
                // SVG Icons (inline for performance - no external files needed)
                $svgs = [
                    'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                    'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                    'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>',
                    'x' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                    'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:65%;height:65%;"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                    'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.213 3.074 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
                    'tiktok' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
                    'pinterest' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>',
                    'email' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M0 3v18h24v-18h-24zm21.518 2l-9.518 7.713-9.518-7.713h19.036zm-19.518 14v-11.817l10 8.104 10-8.104v11.817h-20z"/></svg>'
                ];
                
                // Platform configurations
                $platforms = [
                    'facebook' => ['url' => esc_url($attributes['facebook'] ?? ''), 'label' => 'Facebook', 'color' => '#1877F2'],
                    'instagram' => ['url' => esc_url($attributes['instagram'] ?? ''), 'label' => 'Instagram', 'color' => '#E4405F'],
                    'linkedin' => ['url' => esc_url($attributes['linkedin'] ?? ''), 'label' => 'LinkedIn', 'color' => '#0A66C2'],
                    'x' => ['url' => esc_url($attributes['x'] ?? ''), 'label' => 'X', 'color' => '#000000'],
                    'youtube' => ['url' => esc_url($attributes['youtube'] ?? ''), 'label' => 'YouTube', 'color' => '#FF0000'],
                    'tiktok' => ['url' => esc_url($attributes['tiktok'] ?? ''), 'label' => 'TikTok', 'color' => '#000000'],
                    'pinterest' => ['url' => esc_url($attributes['pinterest'] ?? ''), 'label' => 'Pinterest', 'color' => '#BD081C'],
                    'whatsapp' => ['url' => esc_url($attributes['whatsapp'] ?? ''), 'label' => 'WhatsApp', 'color' => '#25D366'],
                    'email' => ['url' => $attributes['email'] ? 'mailto:' . antispambot(sanitize_email($attributes['email'])) : '', 'label' => 'Email', 'color' => '#34495e']
                ];
                
                $links = '';
                foreach ($platforms as $key => $data) {
                    if (empty($data['url'])) continue;
                    
                    $svg = $svgs[$key] ?? '';
                    $label = $data['label'];
                    
                    // Style: Buttons
                    if ($style === 'buttons') {
                        $btnClass = 'btn btn-outline-primary';
                        if ($size === 'sm') $btnClass .= ' btn-sm';
                        if ($size === 'lg') $btnClass .= ' btn-lg';
                        if ($shape === 'circle') $btnClass .= ' rounded-circle';
                        elseif ($shape === 'square') $btnClass .= ' rounded-0';
                        
                        $links .= '<a href="' . $data['url'] . '" class="' . $btnClass . ' d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer" aria-label="' . $label . '">';
                        $links .= '<span style="width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;">' . $svg . '</span>';
                        if ($showLabels) $links .= $label;
                        $links .= '</a>';
                    } 
                    // Style: Minimal (just colored icons)
                    elseif ($style === 'minimal') {
                        $links .= '<a href="' . $data['url'] . '" class="wpbb-soc-minimal" target="_blank" rel="noopener noreferrer" aria-label="' . $label . '" style="color:' . $data['color'] . ';font-size:' . $iconSize . ';display:inline-flex;align-items:center;justify-content:center;width:' . $iconSize . ';height:' . $iconSize . ';">';
                        $links .= $svg;
                        $links .= '</a>';
                    } 
                    // Style: Icons (default - brand colored circles/squares)
                    else {
                        $bg = !empty($attributes['iconBgColor']) ? $attributes['iconBgColor'] : $data['color'];
                        $fg = !empty($attributes['iconTextColor']) ? $attributes['iconTextColor'] : '#ffffff';
                        $shapeClass = $shape === 'circle' ? '50%' : ($shape === 'square' ? '0' : '8px');
                        
                        $links .= '<a href="' . $data['url'] . '" class="wpbb-soc-icon-link" target="_blank" rel="noopener noreferrer" aria-label="' . $label . '" style="
                            background:' . $bg . '; 
                            color:' . $fg . '; 
                            width:' . $iconSize . '; 
                            height:' . $iconSize . '; 
                            border-radius:' . $shapeClass . '; 
                            display:inline-flex; 
                            align-items:center; 
                            justify-content:center;
                            text-decoration:none;
                            transition:transform 0.2s, box-shadow 0.2s;
                            box-shadow:0 2px 4px rgba(0,0,0,0.1);
                        " onmouseover="this.style.transform=\'translateY(-2px)\';this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.2)\'" onmouseout="this.style.transform=\'none\';this.style.boxShadow=\'0 2px 4px rgba(0,0,0,0.1)\'">';
                        $links .= $svg;
                        $links .= '</a>';
                    }
                }
                
                if (empty($links)) return '';
                
                return '<div ' . $wrapper . '>' . 
                    ($title ? '<' . $titleTag . ' class="wpbb-soc-title" style="margin:0 0 12px 0;font-size:' . $fontSize . ';">' . $title . '</' . $titleTag . '>' : '') .
                    '<div class="wpbb-soc-links d-flex gap-2 flex-wrap align-items-center">' . $links . '</div>' .
                    '</div>';

                        
            case 'soc-share':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('Share', 'wp-bbuilder')));
                $titleTag = in_array(($attributes['titleTag'] ?? 'span'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'span') : 'span';
                $style = $attributes['iconStyle'] ?? 'icons'; // icons, buttons
                $size = $attributes['iconSize'] ?? 'md';
                $shape = $attributes['iconShape'] ?? 'rounded';
                
                $sizeMap = ['sm' => '32px', 'md' => '40px', 'lg' => '48px'];
                $iconSize = $sizeMap[$size] ?? '40px';
                
                $shareUrl = rawurlencode(get_permalink());
                $shareTitle = rawurlencode(get_the_title());
                
                $wrapper = get_block_wrapper_attributes([
                    'class' => 'wpbb-soc-share wpbb-soc-style-' . $style . $extra
                ]);
                
                // Share URLs and icons
                $shares = [
                    'facebook' => [
                        'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl,
                        'label' => 'Share on Facebook',
                        'color' => '#1877F2',
                        'svg' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
                    ],
                    'twitter' => [
                        'url' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle,
                        'label' => 'Share on X',
                        'color' => '#000000',
                        'svg' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
                    ],
                    'linkedin' => [
                        'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl,
                        'label' => 'Share on LinkedIn',
                        'color' => '#0A66C2',
                        'svg' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>'
                    ],
                    'pinterest' => [
                        'url' => 'https://pinterest.com/pin/create/button/?url=' . $shareUrl . '&description=' . $shareTitle,
                        'label' => 'Pin on Pinterest',
                        'color' => '#BD081C',
                        'svg' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%;"><path d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>'
                    ],
                    'email' => [
                        'url' => 'mailto:?subject=' . $shareTitle . '&body=' . $shareUrl,
                        'label' => 'Share via Email',
                        'color' => '#34495e',
                        'svg' => '<svg viewBox="0 0 24 24" fill="currentColor" style="width:60%;height:60%;"><path d="M0 3v18h24v-18h-24zm21.518 2l-9.518 7.713-9.518-7.713h19.036zm-19.518 14v-11.817l10 8.104 10-8.104v11.817h-20z"/></svg>'
                    ]
                ];
                
                $links = '';
                foreach ($shares as $key => $data) {
                    $shapeClass = $shape === 'circle' ? '50%' : ($shape === 'square' ? '0' : '8px');
                    $bg = !empty($attributes['iconBgColor']) ? $attributes['iconBgColor'] : $data['color'];
                    $fg = !empty($attributes['iconColor']) ? $attributes['iconColor'] : '#ffffff';
                    
                    if ($style === 'buttons') {
                        $btnSize = $size === 'sm' ? 'btn-sm' : ($size === 'lg' ? 'btn-lg' : '');
                        $links .= '<a href="' . $data['url'] . '" class="btn btn-outline-secondary ' . $btnSize . ' d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer" aria-label="' . $data['label'] . '" onclick="window.open(this.href,\'share\',\'width=600,height=400\');return false;">';
                        $links .= '<span style="width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;">' . $data['svg'] . '</span> ';
                        $links .= ucfirst($key);
                        $links .= '</a>';
                    } else {
                        // Icon style
                        $links .= '<a href="' . $data['url'] . '" class="wpbb-share-link" target="_blank" rel="noopener noreferrer" aria-label="' . $data['label'] . '" style="
                            background:' . $bg . '; 
                            color:' . $fg . '; 
                            width:' . $iconSize . '; 
                            height:' . $iconSize . '; 
                            border-radius:' . $shapeClass . '; 
                            display:inline-flex; 
                            align-items:center; 
                            justify-content:center;
                            text-decoration:none;
                            transition:transform 0.2s;
                        " onmouseover="this.style.transform=\'scale(1.1)\'" onmouseout="this.style.transform=\'none\'" onclick="window.open(this.href,\'share\',\'width=600,height=400\');return false;">';
                        $links .= $data['svg'];
                        $links .= '</a>';
                    }
                }
                
                return '<div ' . $wrapper . '>' . 
                    ($title ? '<' . $titleTag . ' class="wpbb-share-title" style="margin:0 12px 0 0;display:inline-flex;align-items:center;">' . $title . '</' . $titleTag . '>' : '') .
                    '<div class="wpbb-share-links d-inline-flex gap-2 align-items-center">' . $links . '</div>' .
                    '</div>';
            
            
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
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-table-block', 'data-datatable' => !empty($attributes['datatable']) ? '1' : '0', 'data-searching' => !empty($attributes['datatableSearch']) ? '1' : '0', 'data-paging' => !empty($attributes['datatablePaging']) ? '1' : '0', 'data-ordering' => !empty($attributes['datatableOrdering']) ? '1' : '0', 'data-info' => !empty($attributes['datatableInfo']) ? '1' : '0', 'data-lengthchange' => !empty($attributes['datatableLengthChange']) ? '1' : '0']);
        return '<div ' . $wrapper . '>' . $table_html . '</div>';
    }


    public function render_swiper_block($attributes, $content, $block) {
        wp_enqueue_style('wpbb-swiper');
        wp_enqueue_script('wpbb-swiper');
        wp_enqueue_script('wpbb-swiper-init');
        $slides = wpbb_parse_fields_json($attributes['slidesJson'] ?? '');
        if (!$slides) {
            $slides = [
                ['type' => 'text', 'title' => 'Slide 1', 'text' => 'Demo text'],
                ['type' => 'card', 'title' => 'Slide 2', 'text' => 'Card content'],
                ['type' => 'video', 'title' => 'Slide 3', 'video' => '']
            ];
        }
        $wrapper = get_block_wrapper_attributes([
            'class' => 'wpbb-swiper-block ' . sanitize_html_class($attributes['demoStyle'] ?? 'cards'),
            'data-swiper' => '1',
            'data-slides' => (string) intval($attributes['slidesPerView'] ?? 1),
            'data-space' => (string) intval($attributes['spaceBetween'] ?? 20),
            'data-speed' => (string) intval($attributes['speed'] ?? 600),
            'data-loop' => !empty($attributes['loop']) ? '1' : '0',
            'data-autoplay' => !empty($attributes['autoplay']) ? '1' : '0',
        ]);
        $html = '<div ' . $wrapper . '><div class="swiper"><div class="swiper-wrapper">';
        foreach ($slides as $slide) {
            $type = $slide['type'] ?? 'text';
            $title = esc_html($slide['title'] ?? '');
            $text = wp_kses_post($slide['text'] ?? '');
            $video = esc_url($slide['video'] ?? '');
            $html .= '<div class="swiper-slide"><div class="wpbb-swiper-slide wpbb-swiper-slide--' . esc_attr($type) . '">';
            if ($type === 'video' && $video) {
                $html .= '<div class="ratio ratio-16x9"><iframe src="' . $video . '" allowfullscreen loading="lazy"></iframe></div>';
            } elseif ($type === 'card') {
                $html .= '<div class="card h-100"><div class="card-body">';
                if ($title) $html .= '<h3 class="card-title">' . $title . '</h3>';
                if ($text) $html .= '<div class="card-text">' . $text . '</div>';
                $html .= '</div></div>';
            } else {
                if ($title) $html .= '<h3>' . $title . '</h3>';
                if ($text) $html .= '<div>' . $text . '</div>';
            }
            $html .= '</div></div>';
        }
        $html .= '</div>';
        if (!empty($attributes['showPagination'])) $html .= '<div class="swiper-pagination"></div>';
        if (!empty($attributes['showNavigation'])) $html .= '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
        $html .= '</div></div>';
        return $html;
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

    public function enqueue_frontend_assets() {
        if (wp_style_is('wpbb-shared', 'registered')) {
            wp_enqueue_style('wpbb-shared');
        }
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
        $inline = ':root{'
            . '--wpbb-label-color:' . wpbb_hex_color(wpbb_get_option('default_label_color', '#334155')) . ';'
            . '--wpbb-input-border:' . wpbb_hex_color(wpbb_get_option('default_input_border_color', '#cbd5e1')) . ';'
            . '--wpbb-button-bg:' . wpbb_hex_color(wpbb_get_option('default_button_bg', '#2563eb')) . ';'
            . '--wpbb-button-text:' . wpbb_hex_color(wpbb_get_option('default_button_text', '#ffffff')) . ';'
            . '}';
        if (wp_style_is('wpbb-shared', 'registered')) {
            wp_add_inline_style('wpbb-shared', $inline);
        }
    }


    public function filter_allowed_blocks($allowed_block_types, $editor_context) {
        $allowed = [];
        foreach (wpbb_get_blocks_list() as $slug) {
            if ($slug === 'row-section') continue;
            if (wpbb_is_block_enabled($slug)) {
                $allowed[] = 'wpbb/' . $slug;
            }
        }

        foreach (wpbb_get_acf_blocks_list() as $acf_block) {
            $allowed[] = 'acf/' . $acf_block;
        }

        $core = [
            'core/paragraph','core/heading','core/list','core/list-item','core/quote','core/separator',
            'core/spacer','core/html','core/shortcode','core/code','core/preformatted'
        ];

        if (!wpbb_get_option('disable_core_group', 1)) $core[] = 'core/group';
        if (!wpbb_get_option('disable_core_columns', 1)) $core[] = 'core/columns';
        if (!wpbb_get_option('disable_core_column', 1)) $core[] = 'core/column';
        if (!wpbb_get_option('disable_core_table', 1)) $core[] = 'core/table';
        if (!wpbb_get_option('disable_core_embed', 0)) $core[] = 'core/embed';
        if (!wpbb_get_option('disable_core_gallery', 0)) $core[] = 'core/gallery';
        if (!wpbb_get_option('disable_core_image', 0)) $core[] = 'core/image';
        if (!wpbb_get_option('disable_core_cover', 0)) $core[] = 'core/cover';
        if (!wpbb_get_option('disable_core_media_text', 0)) $core[] = 'core/media-text';
        if (!wpbb_get_option('disable_core_buttons', 0)) $core[] = 'core/buttons';
        if (!wpbb_get_option('disable_core_button', 0)) $core[] = 'core/button';

        return array_values(array_unique(array_merge($allowed, $core)));
    }

    public function ajax_search() {
        $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
        $limit = isset($_GET['limit']) ? max(1, min(20, intval($_GET['limit']))) : 10;
        $mode = isset($_GET['mode']) ? sanitize_text_field(wp_unslash($_GET['mode'])) : 'title';
        $sort = isset($_GET['sort']) ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'relevance';
        $items = [];
        if ($term !== '') {
            $args = ['post_type' => ['post','page','product'], 'posts_per_page' => $limit, 's' => $mode === 'title' ? $term : '', 'post_status' => 'publish'];
            if ($sort === 'date') { $args['orderby'] = 'date'; $args['order'] = 'DESC'; }
            elseif ($sort === 'title') { $args['orderby'] = 'title'; $args['order'] = 'ASC'; }
            if ($mode === 'id' && ctype_digit($term)) {
                $args['post__in'] = [intval($term)];
            } elseif ($mode === 'sku') {
                $args['meta_query'] = [['key' => '_sku', 'value' => $term, 'compare' => 'LIKE']];
            }
            $q = new WP_Query($args);
            if ($q->have_posts()) {
                while ($q->have_posts()) {
                    $q->the_post();
                    $price = '';
                    if (function_exists('wc_get_product') && get_post_type() === 'product') {
                        $product = wc_get_product(get_the_ID());
                        if ($product) $price = wp_strip_all_tags($product->get_price_html());
                    }
                    $items[] = [
                        'title' => get_the_title(),
                        'url' => get_permalink(),
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
                        'type' => get_post_type(),
                        'excerpt' => wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 14),
                        'price' => $price,
                    ];
                }
                wp_reset_postdata();
            }
        }
        wp_send_json_success(['items' => $items]);
    }

    public function render_weather_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-chart-view');
        $title = esc_html($attributes['title'] ?? 'Laikapstākļi');
        $location = esc_attr($attributes['location'] ?? 'Rīga');
        $lang = esc_attr($attributes['lang'] ?? 'lv');
        $apiKey = esc_attr($attributes['apiKey'] ?? wpbb_get_option('weather_api_key', ''));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-weather card', 'data-location' => $location, 'data-lang' => $lang, 'data-api-key' => $apiKey]);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-weather-location\">{$location}</div><div class=\"wpbb-weather-temp\">--°C</div><div class=\"wpbb-weather-note\">Loading weather...</div></div></div>";
    }

    public function render_varda_dienas_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Vārda dienas');
        $dateText = esc_html($attributes['dateText'] ?? 'Šodien');
        $names = esc_html($attributes['names'] ?? 'Vārdi šeit');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-varda-dienas card']);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"small text-muted\">{$dateText}</div><div class=\"wpbb-varda-dienas-names\">{$names}</div></div></div>";
    }

    public function render_ajax_search_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-ajax-search');
        $title = esc_html($attributes['title'] ?? 'Meklēšana');
        $placeholder = esc_attr($attributes['placeholder'] ?? 'Meklēt...');
        $limit = intval($attributes['resultsLimit'] ?? 10);
        $mode = esc_attr($attributes['searchWooBy'] ?? 'title');
        $sortBy = esc_attr($attributes['sortBy'] ?? 'relevance');
        $showButton = !empty($attributes['showButton']);
        $showExcerpt = !empty($attributes['showExcerpt']);
        $showPrice = !empty($attributes['showPrice']);
        $searchUrl = esc_url(home_url('/?s='));
        $wrapper = get_block_wrapper_attributes([
            'class' => 'wpbb-ajax-search card',
            'data-limit' => (string)$limit,
            'data-mode' => $mode,
            'data-sort' => $sortBy,
            'data-show-excerpt' => $showExcerpt ? '1' : '0',
            'data-show-price' => $showPrice ? '1' : '0',
            'data-search-url' => $searchUrl
        ]);
        $button = $showButton ? '<a class="btn btn-outline-secondary wpbb-ajax-search-page-btn" href="#">Atvērt meklēšanas lapu</a>' : '';
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><input type=\"search\" class=\"form-control wpbb-ajax-search-input\" placeholder=\"{$placeholder}\"><div class=\"wpbb-ajax-search-results\"></div>{$button}</div></div>";
    }

    public function render_pricecards_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Cenas');
        $cards = wpbb_parse_fields_json($attributes['cardsJson'] ?? '');
        if (!$cards) $cards = [
            ['title'=>'Basic','price'=>'9','period'=>'/mo','text'=>'Apraksts','button'=>'Izvēlēties','featured'=>false],
            ['title'=>'Pro','price'=>'29','period'=>'/mo','text'=>'Apraksts','button'=>'Izvēlēties','featured'=>true]
        ];
        $variant = sanitize_html_class($attributes['styleVariant'] ?? 'default');
        $currency = esc_html($attributes['currency'] ?? '€');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-pricecards wpbb-pricecards--' . $variant]);
        $html = "<div {$wrapper}><div class=\"row g-3\"><div class=\"col-12\"><h3>{$title}</h3></div>";
        foreach ($cards as $card) {
            $featured = !empty($card['featured']) ? ' wpbb-pricecards__featured' : '';
            $period = !empty($card['period']) ? '<span class="wpbb-pricecards-period">' . esc_html($card['period']) . '</span>' : '';
            $html .= '<div class="col-md-6 col-lg-4"><div class="card h-100' . $featured . '"><div class="card-body"><h4 class="card-title">' . esc_html($card['title'] ?? '') . '</h4><div class="wpbb-pricecards-price">' . $currency . esc_html($card['price'] ?? '') . $period . '</div><div class="card-text">' . esc_html($card['text'] ?? '') . '</div><a href="#" class="btn btn-primary">' . esc_html($card['button'] ?? 'Izvēlēties') . '</a></div></div></div>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function render_catalogue_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Katalogs');
        $postsToShow = max(1, min(24, intval($attributes['postsToShow'] ?? 6)));
        $sortBy = sanitize_text_field($attributes['sortBy'] ?? 'date');
        $sortOrder = strtoupper(sanitize_text_field($attributes['sortOrder'] ?? 'DESC'));
        $showImage = !empty($attributes['showImage']);
        $showExcerpt = !empty($attributes['showExcerpt']);
        $postType = sanitize_text_field($attributes['postType'] ?? 'post');
        $taxonomy = sanitize_text_field($attributes['taxonomy'] ?? 'category');
        $args = ['post_type' => $postType, 'posts_per_page' => $postsToShow, 'post_status' => 'publish', 'orderby' => $sortBy, 'order' => $sortOrder];
        if (!empty($attributes['category'])) {
            if ($taxonomy === 'category') { $args['category_name'] = sanitize_text_field($attributes['category']); }
            else { $args['tax_query'] = [['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => sanitize_text_field($attributes['category'])]]; }
        }
        $q = new WP_Query($args);
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-catalogue']);
        $html = "<div {$wrapper}><div class=\"row g-3\"><div class=\"col-12\"><h3>{$title}</h3></div>";
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                $html .= '<div class="col-md-6 col-lg-4"><div class="card h-100 wpbb-catalogue-card">';
                if ($showImage && $thumb) $html .= '<img class="card-img-top" src="' . esc_url($thumb) . '" alt="">';
                $html .= '<div class="card-body"><h4 class="card-title">' . esc_html(get_the_title()) . '</h4>';
                if ($showExcerpt) $html .= '<div class="card-text">' . esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 20)) . '</div>';
                $html .= '<a class="btn btn-primary" href="' . esc_url(get_permalink()) . '">Open card</a></div></div></div>';
            }
            wp_reset_postdata();
        }
        $html .= '</div></div>';
        return $html;
    }

    public function render_code_display_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-copy-code');
        $title = esc_html($attributes['title'] ?? 'Code');
        $code = esc_html($attributes['code'] ?? '');
        $lang = esc_attr($attributes['language'] ?? 'html');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-code-display']);
        return "<div {$wrapper}><div class=\"wpbb-code-display__head\"><strong>{$title}</strong><button type=\"button\" class=\"button wpbb-copy-code-btn\" aria-label=\"Copy code\">⧉</button></div><pre class=\"wpbb-code-display__pre\"><code class=\"language-{$lang}\">{$code}</code></pre></div>";
    }

    public function render_countdown_timer_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Countdown');
        $target = esc_attr($attributes['targetDate'] ?? '2030-01-01T00:00:00');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-countdown-timer card', 'data-target-date' => $target]);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-countdown-timer__value\">00:00:00</div></div></div>";
    }

    public function render_chart_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-chart-view');
        $title = esc_html($attributes['title'] ?? 'Chart');
        $type = esc_attr($attributes['chartType'] ?? 'bar');
        $json = esc_attr($attributes['chartDataJson'] ?? '');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-chart card', 'data-chart-type' => $type, 'data-chart-json' => $json]);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-chart__canvas\">Chart preview</div></div></div>";
    }

    public function render_fun_fact_block($attributes, $content, $block) {
        $number = esc_html($attributes['number'] ?? '100+');
        $label = esc_html($attributes['label'] ?? 'Projects');
        $icon = esc_html($attributes['icon'] ?? '⭐');
        $variant = sanitize_html_class($attributes['styleVariant'] ?? 'default');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-fun-fact wpbb-fun-fact--' . $variant . ' card']);
        return "<div {$wrapper}><div class=\"card-body text-center\"><div class=\"wpbb-fun-fact__icon\">{$icon}</div><div class=\"wpbb-fun-fact__number\">{$number}</div><div class=\"wpbb-fun-fact__label\">{$label}</div></div></div>";
    }

    public function render_mailchimp_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Subscribe');
        $text = esc_html($attributes['text'] ?? 'Join our newsletter');
        $action = esc_url($attributes['actionUrl'] ?? '');
        $fieldName = esc_attr($attributes['audienceFieldName'] ?? 'EMAIL');
        $showName = !empty($attributes['showNameField']);
        $buttonText = esc_html($attributes['buttonText'] ?? 'Subscribe');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-mailchimp card']);
        $nameField = $showName ? '<input type="text" class="form-control" name="FNAME" placeholder="Name">' : '';
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><p>{$text}</p><form class=\"wpbb-mailchimp-form\" method=\"post\" action=\"{$action}\" target=\"_blank\"><div class=\"row g-2\"><div class=\"col-12\">{$nameField}</div><div class=\"col-12\"><div class=\"input-group\"><input type=\"email\" class=\"form-control\" name=\"{$fieldName}\" placeholder=\"Email\"><button class=\"btn btn-primary\" type=\"submit\">{$buttonText}</button></div></div></div></form></div></div>";
    }


    public function render_bootstrap_div_block($attributes, $content, $block) {
        $tag = in_array(($attributes['tagName'] ?? 'div'), ['div','section','article','aside'], true) ? $attributes['tagName'] : 'div';
        $classes = trim('wpbb-bootstrap-div ' . ($attributes['utilityClasses'] ?? '') . ' ' . ($attributes['className'] ?? ''));
        $style = '';
        foreach ([
            'maxWidth' => 'max-width',
            'maxHeight' => 'max-height',
            'minHeight' => 'min-height',
            'backgroundColor' => 'background',
            'textColor' => 'color',
            'borderRadius' => 'border-radius',
            'padding' => 'padding',
            'margin' => 'margin'
        ] as $key => $css) {
            if (!empty($attributes[$key])) {
                $style .= $css . ':' . preg_replace('/[^#(),.% 0-9a-zA-Z\-]/', '', (string)$attributes[$key]) . ';';
            }
        }
        $wrapper = get_block_wrapper_attributes(['class' => $classes, 'style' => $style]);
        return "<{$tag} {$wrapper}>{$content}</{$tag}>";
    }



    }
