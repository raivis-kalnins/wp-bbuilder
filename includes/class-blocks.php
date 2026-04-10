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
        add_action('init', ['WPBBuilder_Bootstrap', 'register_assets']);
        add_action('init', [$this, 'register_assets']);
        add_action('init', [$this, 'register_blocks']);
        add_filter('block_categories_all', [$this, 'register_category'], 10, 1);
        add_filter('allowed_block_types_all', [$this, 'filter_allowed_blocks'], 20, 2);
        add_action('enqueue_block_assets', [$this, 'enqueue_frontend_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_code_assets']);
        add_action('wp_ajax_wpbb_ajax_search', [$this, 'ajax_search']);
        add_action('wp_ajax_nopriv_wpbb_ajax_search', [$this, 'ajax_search']);
        add_action('wp_ajax_wpbb_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_nopriv_wpbb_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_wpbb_blog_filter', [$this, 'ajax_blog_filter']);
        add_action('wp_ajax_nopriv_wpbb_blog_filter', [$this, 'ajax_blog_filter']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
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
            'x' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M18.24 2H21l-6.56 7.5L22 22h-5.93l-4.64-6.05L6.13 22H3.36l7.02-8.02L2 2h6.08l4.19 5.53L18.24 2zm-1.04 18h1.64L7.19 3.9H5.48L17.2 20z"/></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M23 12s0-3.5-.45-5.2a2.7 2.7 0 0 0-1.9-1.9C18.9 4.4 12 4.4 12 4.4s-6.9 0-8.65.5a2.7 2.7 0 0 0-1.9 1.9C1 8.5 1 12 1 12s0 3.5.45 5.2a2.7 2.7 0 0 0 1.9 1.9c1.75.5 8.65.5 8.65.5s6.9 0 8.65-.5a2.7 2.7 0 0 0 1.9-1.9C23 15.5 23 12 23 12zM10 15.5v-7l6 3.5-6 3.5z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M16.6 14.3c-.3-.2-1.8-.9-2-.9-.3-.1-.4-.1-.6.2s-.7.9-.8 1c-.1.1-.3.2-.5.1-.3-.2-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.4-1.6-.1-.3 0-.4.1-.5l.4-.5.2-.4c.1-.1.1-.3 0-.4 0-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.3-.8.8-.8 1.9s.8 2.1.9 2.3c.1.2 1.6 2.4 3.8 3.4 2.3 1 2.3.7 2.8.7.4-.1 1.5-.6 1.7-1.1.2-.6.2-1 .1-1.1-.1-.1-.3-.2-.6-.3zM12 2.2A9.8 9.8 0 0 0 3.7 17.3L2.2 21.8l4.6-1.5A9.8 9.8 0 1 0 12 2.2zm0 17.8c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-2.7.9.9-2.6-.2-.3A8 8 0 1 1 12 20z"/></svg>',
            'email' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm0 2v.5l9 5.6 9-5.6V7H3zm18 10V9.8l-8.5 5.3a1 1 0 0 1-1 0L3 9.8V17h18z"/></svg>',
        ];
        return $icons[$name] ?? '<span class="wpbb-social-icon__glyph">*</span>';
    }


    private function wpbb_compile_scoped_scss($selector, $scss) {
        $scss = trim((string) $scss);
        if ($scss === '') return '';

        $scss = preg_replace('!/\*.*?\*/!s', '', $scss);

        $vars = [];
        if (preg_match_all('/\$([a-zA-Z0-9_-]+)\s*:\s*([^;]+);/', $scss, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $row) {
                $vars[$row[1]] = trim($row[2]);
            }
        }
        $scss = preg_replace('/\$[a-zA-Z0-9_-]+\s*:\s*[^;]+;/', '', $scss);
        foreach ($vars as $name => $value) {
            $scss = preg_replace('/\$' . preg_quote($name, '/') . '\b/', $value, $scss);
        }

        $scss = trim(preg_replace('/\s+/', ' ', $scss));
        if ($scss === '') return '';

        if (strpos($scss, '{') === false) {
            return $selector . '{' . trim($scss, '; ') . '}';
        }

        $leading = ltrim($scss);
        $first = substr($leading, 0, 1);
        if (
            $first === '&' ||
            $first === ':' ||
            preg_match('/^[a-z-]+\s*:/i', $leading)
        ) {
            if (strpos($leading, '&') !== false) {
                $scss = str_replace('&', $selector, $scss);
            }
            $scss = $selector . '{' . $scss . '}';
        }

        $normalize_selector = function ($parent, $selector_text) {
            $parts = array_map('trim', explode(',', (string) $selector_text));
            $selectors = [];
            foreach ($parts as $part) {
                if ($part === '') continue;
                if ($parent === '') {
                    $selectors[] = $part;
                } elseif (strpos($part, '&') !== false) {
                    $selectors[] = str_replace('&', $parent, $part);
                } else {
                    $selectors[] = trim($parent . ' ' . $part);
                }
            }
            return implode(',', $selectors);
        };

        $extract_nested_ranges = function ($source) {
            $ranges = [];
            $len = strlen($source);
            $i = 0;
            while ($i < $len) {
                while ($i < $len && ctype_space($source[$i])) $i++;
                $start = $i;
                while ($i < $len && $source[$i] !== '{' && $source[$i] !== '}') $i++;
                if ($i < $len && $source[$i] === '{') {
                    $depth = 1;
                    $i++;
                    while ($i < $len && $depth > 0) {
                        if ($source[$i] === '{') $depth++;
                        if ($source[$i] === '}') $depth--;
                        $i++;
                    }
                    $ranges[] = [$start, $i];
                } else {
                    $i++;
                }
            }
            return $ranges;
        };

        $flatten = function ($source, $parent = '') use (&$flatten, $normalize_selector, $extract_nested_ranges, $selector) {
            $css = '';
            $len = strlen($source);
            $i = 0;

            while ($i < $len) {
                while ($i < $len && ctype_space($source[$i])) $i++;
                if ($i >= $len) break;

                $sel_start = $i;
                while ($i < $len && $source[$i] !== '{' && $source[$i] !== '}') $i++;
                if ($i >= $len || $source[$i] === '}') break;

                $selector_text = trim(substr($source, $sel_start, $i - $sel_start));
                $i++;

                $depth = 1;
                $body_start = $i;
                while ($i < $len && $depth > 0) {
                    if ($source[$i] === '{') $depth++;
                    if ($source[$i] === '}') $depth--;
                    $i++;
                }

                $body = trim(substr($source, $body_start, max(0, $i - $body_start - 1)));
                if ($selector_text === '') continue;

                $full_selector = $normalize_selector($parent, $selector_text);
                if ($full_selector === '') continue;

                $ranges = $extract_nested_ranges($body);
                $plain = '';

                if (!empty($ranges)) {
                    $cursor = 0;
                    foreach ($ranges as $range) {
                        $plain .= substr($body, $cursor, $range[0] - $cursor) . ' ';
                        $cursor = $range[1];
                    }
                    $plain .= substr($body, $cursor);
                } else {
                    $plain = $body;
                }

                $plain = trim((string) $plain);
                $plain = preg_replace('/\s*;\s*/', ';', $plain);
                $plain = preg_replace('/\s*:\s*/', ':', $plain);
                $plain = trim($plain, '; ');

                if ($plain !== '') {
                    if ($parent === '' && strpos($selector_text, '&') !== false) {
                        $css .= str_replace('&', $selector, $selector_text) . '{' . $plain . '}';
                    } else {
                        $css .= $full_selector . '{' . $plain . '}';
                    }
                }

                if (strpos($body, '{') !== false) {
                    $css .= $flatten($body, $full_selector);
                }
            }

            return $css;
        };

        $result = $flatten($scss, '');
        if ($result === '') {
            $result = str_replace('&', $selector, $scss);
        }

        $result = preg_replace('/\s+/', ' ', $result);
        $result = str_replace([' {', '{ ', '; ', ': ', ', ', ' }'], ['{', '{', ';', ':', ',', '}'], $result);

        return trim($result);
    }

    private function wpbb_responsive_spacing_attributes() {
        $attributes = [];
        foreach (['padding', 'margin'] as $prefix) {
            foreach (['Default', 'Sm', 'Md', 'Lg', 'Xl', 'Xxl'] as $bp) {
                foreach (['Top', 'Right', 'Bottom', 'Left'] as $side) {
                    $attributes[$prefix . $bp . $side] = ['type' => 'string', 'default' => ''];
                }
            }
        }
        return $attributes;
    }

    private function wpbb_sanitize_css_value($value) {
        return trim(preg_replace('/[^#(),.% 0-9a-zA-Z\-\+*\/:_]/', '', (string) $value));
    }

    private function wpbb_build_responsive_spacing_css($attributes, $selector) {
        $breakpoints = [
            'Default' => '',
            'Sm' => '576px',
            'Md' => '768px',
            'Lg' => '992px',
            'Xl' => '1200px',
            'Xxl' => '1400px',
        ];
        $css = '';
        foreach ($breakpoints as $bp => $minWidth) {
            $rules = '';
            foreach (['padding', 'margin'] as $prefix) {
                foreach (['Top', 'Right', 'Bottom', 'Left'] as $side) {
                    $key = $prefix . $bp . $side;
                    if (empty($attributes[$key])) continue;
                    $value = $this->wpbb_sanitize_css_value($attributes[$key]);
                    if ($value === '') continue;
                    $rules .= $prefix . '-' . strtolower($side) . ':' . $value . ';';
                }
            }
            if ($rules === '') continue;
            if ($bp === 'Default') {
                $css .= $selector . '{' . $rules . '}';
            } else {
                $css .= '@media (min-width:' . $minWidth . '){' . $selector . '{' . $rules . '}}';
            }
        }
        return $css;
    }

    private function wpbb_class_tokens_from_value($value) {
        $tokens = preg_split('/\s+/', trim((string) $value));
        $classes = [];
        foreach ($tokens as $token) {
            $token = sanitize_html_class($token);
            if ($token !== '') $classes[] = $token;
        }
        return $classes;
    }

    private function wpbb_build_spacing_inline($attributes) {
        $style = '';
        $pairs = [
            ['paddingTop','paddingTopUnit','padding-top','paddingDefaultTop'],
            ['paddingRight','paddingRightUnit','padding-right','paddingDefaultRight'],
            ['paddingBottom','paddingBottomUnit','padding-bottom','paddingDefaultBottom'],
            ['paddingLeft','paddingLeftUnit','padding-left','paddingDefaultLeft'],
            ['marginTop','marginTopUnit','margin-top','marginDefaultTop'],
            ['marginRight','marginRightUnit','margin-right','marginDefaultRight'],
            ['marginBottom','marginBottomUnit','margin-bottom','marginDefaultBottom'],
            ['marginLeft','marginLeftUnit','margin-left','marginDefaultLeft'],
        ];
        foreach ($pairs as $pair) {
            if (!empty($attributes[$pair[3]])) continue;
            $num = isset($attributes[$pair[0]]) ? $attributes[$pair[0]] : null;
            $unit = isset($attributes[$pair[1]]) ? $attributes[$pair[1]] : 'px';
            if ($num !== null && $num !== '' && is_numeric($num) && floatval($num) != 0.0) {
                $style .= $pair[2] . ':' . floatval($num) . preg_replace('/[^a-z%]/i', '', (string)$unit) . ';';
            }
        }
        return $style;
    }


    public function enqueue_block_editor_code_assets() {
        $scss_settings = wp_enqueue_code_editor(['type' => 'text/x-scss']);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
        wp_enqueue_script('wpbb-editor-enhancer');
        if (wpbb_get_option('load_bootstrap_css', 1)) {
            wp_enqueue_style('wpbb-bootstrap-editor', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        }
        if (wpbb_get_option('load_bootstrap_js', 0)) {
            wp_enqueue_script('wpbb-bootstrap-editor', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
        }
        wp_localize_script('wpbb-editor-enhancer', 'wpbbEditorEnhancer', [
            'scss' => $scss_settings,
        ]);
    }

public function register_assets() {
        wp_register_script('wpbb-editor', WPBB_PLUGIN_URL . 'assets/editor.js', ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data'], WPBB_VERSION, true);
        wp_localize_script('wpbb-editor', 'wpbbEditor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpbb_builder_nonce'),
        ]);
        wp_register_script('wpbb-editor-enhancer', WPBB_PLUGIN_URL . 'assets/editor-enhancer.js', ['wp-dom-ready'], WPBB_VERSION, true);
        wp_register_script('wpbb-form-view', WPBB_PLUGIN_URL . 'assets/form.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-copy-code', WPBB_PLUGIN_URL . 'assets/copy-code.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-ajax-search', WPBB_PLUGIN_URL . 'assets/ajax-search.js', [], WPBB_VERSION, true);
        wp_register_script('wpbb-content-filters', WPBB_PLUGIN_URL . 'assets/content-filters.js', [], WPBB_VERSION, true);
        wp_register_style('wpbb-datatables', 'https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css', [], '2.0.8');
        wp_register_script('wpbb-datatables', 'https://cdn.datatables.net/2.0.8/js/dataTables.js', [], '2.0.8', true);
        wp_register_script('wpbb-datatables-bs5', 'https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js', ['wpbb-datatables'], '2.0.8', true);
        wp_register_script('wpbb-table-init', WPBB_PLUGIN_URL . 'assets/table-init.js', ['wpbb-datatables-bs5'], WPBB_VERSION, true);
        wp_register_script('wpbb-chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js', [], '4.4.3', true);
        wp_register_script('wpbb-chart-view', WPBB_PLUGIN_URL . 'assets/chart-view.js', ['wpbb-chartjs'], WPBB_VERSION, true);
        wp_localize_script('wpbb-content-filters', 'wpbbContentFilters', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
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

            if ($slug === 'alert') {
                $args['render_callback'] = [$this, 'render_alert_block'];
            } elseif ($slug === 'badge') {
                $args['render_callback'] = [$this, 'render_badge_block'];
            } elseif ($slug === 'breadcrumb') {
                $args['render_callback'] = [$this, 'render_breadcrumb_block'];
            } elseif ($slug === 'list-group') {
                $args['render_callback'] = [$this, 'render_list_group_block'];
            } elseif ($slug === 'navbar') {
                $args['render_callback'] = [$this, 'render_navbar_block'];
            } elseif ($slug === 'progress') {
                $args['render_callback'] = [$this, 'render_progress_block'];
            } elseif ($slug === 'section') {
                $args['render_callback'] = [$this, 'render_section_block'];
            } elseif ($slug === 'spinner') {
                $args['render_callback'] = [$this, 'render_spinner_block'];
            } elseif ($slug === 'dynamic-form') {
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
            } elseif ($slug === 'feature-list') {
                $args['render_callback'] = [$this, 'render_feature_list_block'];
            } elseif ($slug === 'timeline') {
                $args['render_callback'] = [$this, 'render_timeline_block'];
            } elseif ($slug === 'custom-embed') {
                $args['render_callback'] = [$this, 'render_custom_embed_block'];
            } elseif ($slug === 'ai-content') {
                $args['render_callback'] = [$this, 'render_ai_content_block'];
            } elseif ($slug === 'login-register') {
                $args['render_callback'] = [$this, 'render_login_register_block'];
            } elseif ($slug === 'row') {
                $args['render_callback'] = [$this, 'render_row_block'];
            } elseif ($slug === 'column') {
                $args['render_callback'] = [$this, 'render_column_block'];
            } elseif ($slug === 'button') {
                $args['render_callback'] = [$this, 'render_button_block'];
            } elseif ($slug === 'accordion') {
                $args['render_callback'] = [$this, 'render_accordion_block'];
            } elseif ($slug === 'accordion-item') {
                $args['render_callback'] = [$this, 'render_accordion_item_block'];
            } elseif ($slug === 'row') {
                $args['render_callback'] = [$this, 'render_row_block'];
            } elseif ($slug === 'column') {
                $args['render_callback'] = [$this, 'render_column_block'];
            } elseif ($slug === 'soc-follow-block') {
                $args['render_callback'] = [$this, 'render_social_follow_block'];
            } elseif ($slug === 'soc-share') {
                $args['render_callback'] = [$this, 'render_social_share_block'];
            } elseif ($slug === 'load-more') {
                $args['script'] = 'wpbb-content-filters';
                $args['render_callback'] = [$this, 'render_load_more_block'];
            } elseif ($slug === 'contact-links') {
                $args['render_callback'] = [$this, 'render_contact_links_block'];
            } elseif ($slug === 'events') {
                $args['render_callback'] = [$this, 'render_events_block'];
            } elseif ($slug === 'testimonials') {
                $args['style'] = ['wpbb-shared','wpbb-swiper'];
                $args['script'] = 'wpbb-swiper-init';
                $args['render_callback'] = [$this, 'render_testimonials_block'];
            } elseif ($slug === 'blog-filter') {
                $args['script'] = 'wpbb-content-filters';
                $args['render_callback'] = [$this, 'render_blog_filter_block'];
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
            'alert' => 'warning',
            'badge' => 'tag',
            'breadcrumb' => 'editor-ol',
            'button' => 'button',
            'card' => 'id',
            'cards' => 'grid-view',
            'column' => 'columns',
            'dynamic-form' => 'feedback',
            'list-group' => 'list-view',
            'navbar' => 'menu',
            'progress' => 'performance',
            'row' => 'grid-view','cta-card' => 'megaphone','cta-section' => 'cover-image','google-map' => 'location-alt','menu-option' => 'menu','sitemap' => 'networking','soc-follow-block' => 'share','soc-share' => 'share-alt2',
            'section' => 'cover-image',
            'spinner' => 'update',
            'file' => 'media-document',
            'inline-svg' => 'format-image',
            'tab-item' => 'editor-table',
            'load-more' => 'plus-alt2','contact-links' => 'phone','events' => 'calendar-alt','testimonials' => 'format-quote','blog-filter' => 'filter',
            'tabs' => 'index-card',
            'table' => 'table-col-after',
            'swiper' => 'images-alt2','weather' => 'cloud','varda-dienas' => 'calendar-alt','ajax-search' => 'search','pricecards' => 'index-card','catalogue' => 'screenoptions','code-display' => 'editor-code','countdown-timer' => 'clock','chart' => 'chart-bar','fun-fact' => 'star-filled','mailchimp' => 'email','bootstrap-div' => 'screenoptions',
                    ];
        return $map[$slug] ?? 'screenoptions';
    }

    private function attributes_for($slug) {
        switch ($slug) {
            case 'row':
                return array_merge([
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
                    'customClasses' => ['type' => 'string', 'default' => ''],
                    'utilityClasses' => ['type' => 'string', 'default' => ''],
                    'spacingSm' => ['type' => 'string', 'default' => ''],'spacingMd' => ['type' => 'string', 'default' => ''],'spacingLg' => ['type' => 'string', 'default' => ''],'spacingXl' => ['type' => 'string', 'default' => ''],'spacingXxl' => ['type' => 'string', 'default' => ''],'paddingSm' => ['type' => 'string', 'default' => ''],'paddingMd' => ['type' => 'string', 'default' => ''],'paddingLg' => ['type' => 'string', 'default' => ''],'paddingXl' => ['type' => 'string', 'default' => ''],'paddingXxl' => ['type' => 'string', 'default' => ''],'marginSm' => ['type' => 'string', 'default' => ''],'marginMd' => ['type' => 'string', 'default' => ''],'marginLg' => ['type' => 'string', 'default' => ''],'marginXl' => ['type' => 'string', 'default' => ''],'marginXxl' => ['type' => 'string', 'default' => ''],'uniqueId' => ['type' => 'string', 'default' => ''],'customCss' => ['type' => 'string', 'default' => ''],'customScss' => ['type' => 'string', 'default' => ''],'backgroundImageUrl' => ['type' => 'string', 'default' => ''],'backgroundSize' => ['type' => 'string', 'default' => 'cover'],'backgroundPosition' => ['type' => 'string', 'default' => 'center center'],'overlayColor' => ['type' => 'string', 'default' => ''],'overlayOpacity' => ['type' => 'number', 'default' => 0],'backgroundAttachment' => ['type' => 'string', 'default' => 'scroll'],
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
                    'maxWidth' => ['type' => 'string', 'default' => ''],
                    'maxWidthUnit' => ['type' => 'string', 'default' => 'px'],
                    'containerClass' => ['type' => 'string', 'default' => ''],
                    'visibilityClass' => ['type' => 'string', 'default' => ''],
                    'visibilityXs' => ['type' => 'boolean', 'default' => true],
                    'visibilitySm' => ['type' => 'boolean', 'default' => true],
                    'visibilityMd' => ['type' => 'boolean', 'default' => true],
                    'visibilityLg' => ['type' => 'boolean', 'default' => true],
                    'visibilityXl' => ['type' => 'boolean', 'default' => true],
                ], $this->wpbb_responsive_spacing_attributes());
            case 'column':
                return array_merge([
                    'xs' => ['type' => 'number', 'default' => 12],
                    'sm' => ['type' => 'number', 'default' => 0],
                    'md' => ['type' => 'number', 'default' => 6],
                    'lg' => ['type' => 'number', 'default' => 0],
                    'xl' => ['type' => 'number', 'default' => 0],
                    'xxl' => ['type' => 'number', 'default' => 0],
                    'uniqueId' => ['type' => 'string', 'default' => ''],
                    'maxWidth' => ['type' => 'string', 'default' => ''],
                    'maxWidthUnit' => ['type' => 'string', 'default' => 'px'],
                    'customCss' => ['type' => 'string', 'default' => ''],
                    'customScss' => ['type' => 'string', 'default' => ''],
                    'backgroundImageUrl' => ['type' => 'string', 'default' => ''],
                    'backgroundSize' => ['type' => 'string', 'default' => 'cover'],
                    'backgroundPosition' => ['type' => 'string', 'default' => 'center center'],
                    'backgroundAttachment' => ['type' => 'string', 'default' => 'scroll'],
                    'overlayColor' => ['type' => 'string', 'default' => ''],
                    'overlayOpacity' => ['type' => 'number', 'default' => 0],
                    'boxShadowClass' => ['type' => 'string', 'default' => ''],
                    'boxShadowColor' => ['type' => 'string', 'default' => ''],
                    'orderClass' => ['type' => 'string', 'default' => ''],
                    'verticalAlign' => ['type' => 'string', 'default' => ''],
                    'horizontalAlign' => ['type' => 'string', 'default' => ''],
                    'visibilityClass' => ['type' => 'string', 'default' => ''],
                    'visibilityXs' => ['type' => 'boolean', 'default' => true],
                    'visibilitySm' => ['type' => 'boolean', 'default' => true],
                    'visibilityMd' => ['type' => 'boolean', 'default' => true],
                    'visibilityLg' => ['type' => 'boolean', 'default' => true],
                    'visibilityXl' => ['type' => 'boolean', 'default' => true],
                    'animationClass' => ['type' => 'string', 'default' => ''],
                    'paddingTop' => ['type' => 'number', 'default' => 0], 'paddingTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingRight' => ['type' => 'number', 'default' => 0], 'paddingRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingBottom' => ['type' => 'number', 'default' => 0], 'paddingBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingLeft' => ['type' => 'number', 'default' => 0], 'paddingLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginTop' => ['type' => 'number', 'default' => 0], 'marginTopUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginRight' => ['type' => 'number', 'default' => 0], 'marginRightUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginBottom' => ['type' => 'number', 'default' => 0], 'marginBottomUnit' => ['type' => 'string', 'default' => 'px'],
                    'marginLeft' => ['type' => 'number', 'default' => 0], 'marginLeftUnit' => ['type' => 'string', 'default' => 'px'],
                    'paddingClass' => ['type' => 'string', 'default' => ''],
                    'marginClass' => ['type' => 'string', 'default' => ''],
                    'backgroundClass' => ['type' => 'string', 'default' => ''],
                    'displayClass' => ['type' => 'string', 'default' => ''],
                    'textUtilityClass' => ['type' => 'string', 'default' => ''],
                    'roundedClass' => ['type' => 'string', 'default' => ''],
                    'shadowClass' => ['type' => 'string', 'default' => ''],
                    'bootstrapClasses' => ['type' => 'string', 'default' => ''],
                    'customClasses' => ['type' => 'string', 'default' => ''],
                    'utilityClasses' => ['type' => 'string', 'default' => ''],
                    'backgroundColor' => ['type' => 'string', 'default' => ''],
                    'textColor' => ['type' => 'string', 'default' => ''],
                    'customStyle' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => ''],
                ], $this->wpbb_responsive_spacing_attributes());
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
                    'align' => ['type' => 'string', 'default' => ''],
                    'borderRadius' => ['type' => 'string', 'default' => '12px'],
                ];
            case 'load-more':
                return [
                    'buttonText' => ['type' => 'string', 'default' => 'Load more'],
                    'buttonClass' => ['type' => 'string', 'default' => 'btn btn-primary'],
                    'buttonColor' => ['type' => 'string', 'default' => ''],
                    'visibleItems' => ['type' => 'number', 'default' => 6],
                    'loadItems' => ['type' => 'number', 'default' => 3],
                    'parentClass' => ['type' => 'string', 'default' => 'row'],
                    'itemClass' => ['type' => 'string', 'default' => 'col-md-4'],
                    'queryPostType' => ['type' => 'string', 'default' => 'post'],
                    'queryCategory' => ['type' => 'string', 'default' => ''],
                ];
            case 'contact-links':
                return [
                    'email' => ['type' => 'string', 'default' => 'hello@example.com'],
                    'phone' => ['type' => 'string', 'default' => '+37100000000'],
                    'emailIcon' => ['type' => 'string', 'default' => 'email'],
                    'phoneIcon' => ['type' => 'string', 'default' => 'whatsapp'],
                    'iconColor' => ['type' => 'string', 'default' => ''],
                    'linkColor' => ['type' => 'string', 'default' => ''],
                    'layoutClass' => ['type' => 'string', 'default' => 'd-flex flex-column gap-2'],
                ];
            case 'events':
                return [
                    'postType' => ['type' => 'string', 'default' => 'event'],
                    'postsToShow' => ['type' => 'number', 'default' => 6],
                    'taxonomy' => ['type' => 'string', 'default' => 'event_category'],
                    'showCalendar' => ['type' => 'boolean', 'default' => true],
                    'title' => ['type' => 'string', 'default' => 'Events'],
                ];
            case 'testimonials':
                return [
                    'postType' => ['type' => 'string', 'default' => 'testimonial'],
                    'postsToShow' => ['type' => 'number', 'default' => 9],
                    'slidesDesktop' => ['type' => 'number', 'default' => 3],
                    'slidesTablet' => ['type' => 'number', 'default' => 2],
                    'slidesMobile' => ['type' => 'number', 'default' => 1],
                    'showNavigation' => ['type' => 'boolean', 'default' => true],
                    'showPagination' => ['type' => 'boolean', 'default' => true],
                    'title' => ['type' => 'string', 'default' => 'Testimonials'],
                ];
            case 'blog-filter':
                return [
                    'postType' => ['type' => 'string', 'default' => 'post'],
                    'postsToShow' => ['type' => 'number', 'default' => 6],
                    'taxonomy' => ['type' => 'string', 'default' => 'category'],
                    'title' => ['type' => 'string', 'default' => 'Blog'],
                    'buttonText' => ['type' => 'string', 'default' => 'Filter'],
                    'buttonColor' => ['type' => 'string', 'default' => '#2563eb'],
                ];

case 'alert':
    return [
        'text' => ['type' => 'string', 'default' => 'Heads up! This is a fast, accessible alert block.'],
        'variant' => ['type' => 'string', 'default' => 'primary'],
        'dismissible' => ['type' => 'boolean', 'default' => false],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'badge':
    return [
        'text' => ['type' => 'string', 'default' => 'New'],
        'variant' => ['type' => 'string', 'default' => 'primary'],
        'pill' => ['type' => 'boolean', 'default' => true],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'breadcrumb':
    return [
        'itemsJson' => ['type' => 'string', 'default' => '[{"label":"Home","url":"/"},{"label":"Library","url":"#"},{"label":"Current page","url":""}]'],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'list-group':
    return [
        'itemsJson' => ['type' => 'string', 'default' => '[{"text":"Fast loading","active":true},{"text":"Bootstrap components"},{"text":"Server-side rendering"}]'],
        'flush' => ['type' => 'boolean', 'default' => false],
        'numbered' => ['type' => 'boolean', 'default' => false],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'navbar':
    return [
        'brand' => ['type' => 'string', 'default' => 'BBuilder'],
        'brandUrl' => ['type' => 'string', 'default' => '/'],
        'expand' => ['type' => 'string', 'default' => 'lg'],
        'scheme' => ['type' => 'string', 'default' => 'light'],
        'bgClass' => ['type' => 'string', 'default' => 'bg-light'],
        'itemsJson' => ['type' => 'string', 'default' => '[{"label":"Home","url":"/","active":true},{"label":"Docs","url":"#"},{"label":"Pricing","url":"#"}]'],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'progress':
    return [
        'value' => ['type' => 'number', 'default' => 72],
        'label' => ['type' => 'string', 'default' => 'Performance'],
        'variant' => ['type' => 'string', 'default' => 'success'],
        'striped' => ['type' => 'boolean', 'default' => false],
        'animated' => ['type' => 'boolean', 'default' => false],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'section':
    return [
        'title' => ['type' => 'string', 'default' => 'Section'],
        'lead' => ['type' => 'string', 'default' => 'Use this semantic section wrapper for hero areas, feature strips, and content bands.'],
        'containerClass' => ['type' => 'string', 'default' => 'container'],
        'backgroundClass' => ['type' => 'string', 'default' => 'py-5'],
        'className' => ['type' => 'string', 'default' => ''],
    ];
case 'spinner':
    return [
        'type' => ['type' => 'string', 'default' => 'border'],
        'variant' => ['type' => 'string', 'default' => 'primary'],
        'label' => ['type' => 'string', 'default' => 'Loading'],
        'className' => ['type' => 'string', 'default' => ''],
    ];
            case 'cards':
                return [
                    'columnsMd' => ['type' => 'number', 'default' => 3],
                    'gap' => ['type' => 'number', 'default' => 3],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'weather':
                return [
                    'title' => ['type' => 'string', 'default' => 'Weather'],
                    'location' => ['type' => 'string', 'default' => 'London'],
                    'lang' => ['type' => 'string', 'default' => 'en'],
                    'apiKey' => ['type' => 'string', 'default' => ''],
                    'showTemp' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'varda-dienas':
                return [
                    'title' => ['type' => 'string', 'default' => 'Name Days'],
                    'dateText' => ['type' => 'string', 'default' => ''],
                    'names' => ['type' => 'string', 'default' => ''],
                    'namesJson' => ['type' => 'string', 'default' => ''],
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
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('CTA Card', 'wp-bbuilder')));
                $titleTag = in_array(($attributes['titleTag'] ?? 'h3'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? $attributes['titleTag'] : 'h3';
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Learn more', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $schemaType = !empty($attributes['schemaType']) ? sanitize_html_class($attributes['schemaType']) : 'CreativeWork';
                $schemaEnabled = !empty($attributes['schemaEnable']);
                $schemaAttr = $schemaEnabled ? ' itemscope itemtype="https://schema.org/' . esc_attr($schemaType) . '"' : '';
                $currency = esc_html($attributes['currency'] ?? '€');
                $schemaPrice = '';
                if ($schemaEnabled && $schemaType === 'Product' && !empty($attributes['schemaPrice'])) {
                    $price = esc_attr($attributes['schemaPrice']);
                    $schemaPrice = '<meta itemprop="priceCurrency" content="' . esc_attr($currency) . '"><meta itemprop="price" content="' . $price . '"><div class="wpbb-cta-card-price"><span class="wpbb-cta-card-currency">' . $currency . '</span>' . $price . '</div>';
                }
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-card card h-100' . $extra]);
                $style = "";
                if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";";
                if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";";
                if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";";
                $titleItemprop = $schemaEnabled ? ' itemprop="name"' : '';
                $descItemprop = $schemaEnabled ? ' itemprop="description"' : '';
                return '<div ' . $wrapper . $schemaAttr . ' style="' . esc_attr($style) . '"><div class="card-body">' . $schemaPrice . '<' . $titleTag . ' class="card-title ' . $titleTag . '"' . $titleItemprop . '>' . $title . '</' . $titleTag . '><p class="card-text"' . $descItemprop . '>' . $text . '</p><a class="btn btn-primary" href="' . $buttonUrl . '">' . $buttonText . '</a></div></div>';

            case 'cta-section':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('CTA Section', 'wp-bbuilder'))); $titleTag = in_array(($attributes['titleTag'] ?? 'h2'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? $attributes['titleTag'] : 'h2';
                $text = esc_html($attributes['text'] ?? '');
                $buttonText = esc_html($attributes['buttonText'] ?? __('Get started', 'wp-bbuilder'));
                $buttonUrl = esc_url($attributes['buttonUrl'] ?? '#');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-cta-section text-center py-5' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";"; return "<section {$wrapper} style=\"" . esc_attr($style) . "\"><div class=\"container-fluid\"><h2>{$title}</h2><p>{$text}</p><a class=\"btn btn-primary\" href=\"{$buttonUrl}\">{$buttonText}</a></div></section>";

            case 'google-map':
                $address = sanitize_text_field($attributes['address'] ?? '');
                $legacy_embed = trim((string)($attributes['embedUrl'] ?? ''));
                $height = trim((string)($attributes['height'] ?? '380px'));
                if ($height === '') $height = '380px';
                if (preg_match('/^\d+$/', $height)) $height .= 'px';
                $height_attr = preg_replace('/[^0-9.]/', '', $height);
                if ($height_attr === '') $height_attr = '380';
                $zoom = intval($attributes['zoom'] ?? 14);
                if ($zoom < 1) $zoom = 1;
                if ($zoom > 21) $zoom = 21;
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-google-map' . $extra]);

                $src = '';
                if ($address !== '') {
                    $src = 'https://maps.google.com/maps?width=100%25&height=' . rawurlencode($height_attr) . '&hl=en&q=' . rawurlencode($address) . '&t=&z=' . $zoom . '&ie=UTF8&iwloc=B&output=embed';
                } elseif ($legacy_embed !== '') {
                    if (preg_match('~src=["\']([^"\']+)["\']~i', $legacy_embed, $m)) {
                        $src = $m[1];
                    } else {
                        $src = $legacy_embed;
                    }
                    $src = html_entity_decode($src, ENT_QUOTES, 'UTF-8');
                    $src = preg_replace('~^http://~i', 'https://', $src);
                    if (strpos($src, 'q=') === false && preg_match('~/(place|search)/([^/?#]+)~', $src, $m2)) {
                        $src = 'https://maps.google.com/maps?width=100%25&height=' . rawurlencode($height_attr) . '&hl=en&q=' . rawurlencode(urldecode(str_replace('+', ' ', $m2[2]))) . '&t=&z=' . $zoom . '&ie=UTF8&iwloc=B&output=embed';
                    } elseif (strpos($src, 'output=embed') === false) {
                        $src .= (strpos($src, '?') !== false ? '&' : '?') . 'output=embed';
                    }
                }

                if ($src === '') return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Add address', 'wp-bbuilder') . '</div></div>';

                $overlay_color = trim((string)($attributes['overlayColor'] ?? ''));
                $overlay_opacity = isset($attributes['overlayOpacity']) ? max(0, min(1, floatval($attributes['overlayOpacity']))) : 0;
                if ($overlay_color === '' && !empty($attributes['mapFilter']) && preg_match('/^(#|rgb|rgba|hsl|hsla)/i', trim((string)$attributes['mapFilter']))) {
                    $overlay_color = trim((string)$attributes['mapFilter']);
                    if ($overlay_opacity <= 0) $overlay_opacity = 0.2;
                }

                $html = '<div ' . $wrapper . '>';
                $html .= '<div class="wpbb-google-map__frame" style="position:relative;width:100%;min-height:' . esc_attr($height) . ';overflow:hidden;">';
                $html .= '<iframe class="wpbb-google-map__iframe" src="' . esc_url($src) . '" title="' . esc_attr($address !== '' ? $address : __('Google map', 'wp-bbuilder')) . '" width="100%" height="' . esc_attr($height_attr) . '" style="border:0;width:100%;height:' . esc_attr($height) . ';min-height:' . esc_attr($height) . ';display:block;visibility:visible;opacity:1;background:#f8fafc;" loading="eager" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
                if ($overlay_color !== '' && $overlay_opacity > 0) {
                    $html .= '<span class="wpbb-google-map__overlay" aria-hidden="true" style="position:absolute;inset:0;pointer-events:none;background:' . esc_attr($overlay_color) . ';opacity:' . esc_attr((string)$overlay_opacity) . ';"></span>';
                }
                if ($address !== '') {
                    $html .= '<div class="wpbb-google-map__fallback" style="padding-top:8px;"><a href="' . esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address)) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Open map in Google Maps', 'wp-bbuilder') . '</a></div>';
                }
                $html .= '</div></div>';
                return $html;

            case 'file':
                $file_url = esc_url($attributes['fileUrl'] ?? '');
                $file_name = trim((string)($attributes['fileName'] ?? ''));
                $button_text = trim((string)($attributes['buttonText'] ?? 'Download file'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-file-block' . $extra]);
                if ($file_url === '') return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Add file URL', 'wp-bbuilder') . '</div></div>';
                if ($file_name === '') $file_name = basename(wp_parse_url($file_url, PHP_URL_PATH));
                $target = !empty($attributes['targetBlank']) ? ' target="_blank" rel="noopener"' : '';
                return '<div ' . $wrapper . '><div class="wpbb-file-block__name">' . esc_html($file_name) . '</div><a class="wpbb-file-block__link btn btn-primary" href="' . esc_url($file_url) . '"' . $target . '>' . esc_html($button_text !== '' ? $button_text : 'Download file') . '</a></div>';

            case 'inline-svg':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-inline-svg' . $extra]);
                $svg_code = trim((string)($attributes['svgCode'] ?? ''));
                if ($svg_code === '') return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Paste SVG source code', 'wp-bbuilder') . '</div></div>';
                $allowed = [
                    'svg' => ['class'=>true,'xmlns'=>true,'width'=>true,'height'=>true,'viewBox'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'role'=>true,'aria-hidden'=>true,'focusable'=>true,'preserveAspectRatio'=>true,'style'=>true],
                    'g' => ['fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'path' => ['d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'rect' => ['x'=>true,'y'=>true,'width'=>true,'height'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'circle' => ['cx'=>true,'cy'=>true,'r'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'ellipse' => ['cx'=>true,'cy'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'line' => ['x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'polyline' => ['points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'polygon' => ['points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'style'=>true,'class'=>true],
                    'defs' => ['class'=>true],
                    'linearGradient' => ['id'=>true,'x1'=>true,'x2'=>true,'y1'=>true,'y2'=>true,'gradientUnits'=>true,'gradientTransform'=>true],
                    'radialGradient' => ['id'=>true,'cx'=>true,'cy'=>true,'r'=>true,'fx'=>true,'fy'=>true,'gradientUnits'=>true,'gradientTransform'=>true],
                    'stop' => ['offset'=>true,'stop-color'=>true,'stop-opacity'=>true,'style'=>true],
                    'title' => [],
                    'desc' => [],
                    'symbol' => ['id'=>true,'viewBox'=>true],
                    'use' => ['href'=>true,'xlink:href'=>true,'x'=>true,'y'=>true,'width'=>true,'height'=>true,'transform'=>true],
                    'clipPath' => ['id'=>true],
                    'mask' => ['id'=>true,'x'=>true,'y'=>true,'width'=>true,'height'=>true],
                    'text' => ['x'=>true,'y'=>true,'dx'=>true,'dy'=>true,'fill'=>true,'stroke'=>true,'font-size'=>true,'font-family'=>true,'text-anchor'=>true,'style'=>true,'class'=>true],
                    'tspan' => ['x'=>true,'y'=>true,'dx'=>true,'dy'=>true,'fill'=>true,'stroke'=>true,'font-size'=>true,'font-family'=>true,'text-anchor'=>true,'style'=>true,'class'=>true]
                ];
                return '<div ' . $wrapper . '>' . wp_kses($svg_code, $allowed) . '</div>';

            case 'menu-option':
                $title = esc_html(wpbb_translate_string($attributes['title'] ?? __('Menu Item', 'wp-bbuilder'))); $titleTag = in_array(($attributes['titleTag'] ?? 'h4'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'h4') : 'h4';
                $badge = esc_html($attributes['badge'] ?? '');
                $text = esc_html($attributes['text'] ?? '');
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-menu-option d-flex justify-content-between align-items-start gap-3 py-2' . $extra]);
                $style = ""; if (!empty($attributes["bgColor"])) $style .= "background:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["bgColor"]) . ";"; if (!empty($attributes["textColor"])) $style .= "color:" . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', "", (string)$attributes["textColor"]) . ";"; if (!empty($attributes["borderRadius"])) $style .= "border-radius:" . preg_replace('/[^0-9.%a-zA-Z-]/', "", (string)$attributes["borderRadius"]) . ";"; $menuSlug = sanitize_text_field($attributes["menuSlug"] ?? ""); $schemaEnable = !empty($attributes["schemaEnable"]); $price = esc_html($attributes["price"] ?? ""); $titleHtml = "<{$titleTag} class=\"{$titleTag}\">{$title}</{$titleTag}>"; $menuHtml = ""; if ($menuSlug) { $menuHtml = wp_nav_menu(["menu" => $menuSlug, "echo" => false, "container" => false, "fallback_cb" => false]); } $body = $titleHtml . "<div>{$text}</div>" . ($menuHtml ?: "") . ($price ? "<div class=\"wpbb-menu-price\">{$price}</div>" : ""); if ($schemaEnable) { return "<div {$wrapper} itemscope itemtype=\"https://schema.org/MenuItem\" style=\"" . esc_attr($style) . "\"><div itemprop=\"name\">{$body}</div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>"; } return "<div {$wrapper} style=\"" . esc_attr($style) . "\"><div>{$body}</div>" . ($badge ? "<div class=\"badge text-bg-light\">{$badge}</div>" : "") . "</div>";

            case 'sitemap':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-sitemap' . $extra]);
                $title = esc_html(wpbb_translate_string($attributes["title"] ?? __("Sitemap", "wp-bbuilder")));
                $titleTag = in_array(($attributes["titleTag"] ?? "h3"), ["h1","h2","h3","h4","h5","h6","div","p","span"], true) ? ($attributes["titleTag"] ?: "h3") : "h3";
                $html = "<div {$wrapper}><{$titleTag} class=\"{$titleTag}\">{$title}</{$titleTag}>";
                if (!empty($attributes["showPages"])) {
                    $pages = wp_list_pages(["echo"=>0,"title_li"=>""]);
                    if ($pages) $html .= "<div class=\"wpbb-sitemap__group wpbb-sitemap__pages\"><ul>{$pages}</ul></div>";
                }
                if (!empty($attributes["showPosts"])) {
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $pt_key => $pt_obj) {
                        if (in_array($pt_key, ['attachment','wp_block','wp_template','wp_template_part','nav_menu_item'], true)) continue;
                        $items = get_posts(['numberposts' => -1, 'post_status' => 'publish', 'post_type' => $pt_key, 'orderby' => 'title', 'order' => 'ASC']);
                        if (!$items) continue;
                        $html .= "<div class=\"wpbb-sitemap__group wpbb-sitemap__{$pt_key}\"><strong class=\"wpbb-sitemap__label\">" . esc_html($pt_obj->labels->name ?? $pt_key) . "</strong><ul>";
                        foreach ($items as $p) {
                            $html .= "<li><a href=\"" . esc_url(get_permalink($p)) . "\">" . esc_html(get_the_title($p)) . "</a></li>";
                        }
                        $html .= "</ul></div>";
                    }
                }
                return $html . "</div>";

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
                return array_merge([
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
                    'customClasses' => ['type' => 'string', 'default' => ''],
                    'utilityClasses' => ['type' => 'string', 'default' => ''],
                    'spacingSm' => ['type' => 'string', 'default' => ''],'spacingMd' => ['type' => 'string', 'default' => ''],'spacingLg' => ['type' => 'string', 'default' => ''],'spacingXl' => ['type' => 'string', 'default' => ''],'spacingXxl' => ['type' => 'string', 'default' => ''],'paddingSm' => ['type' => 'string', 'default' => ''],'paddingMd' => ['type' => 'string', 'default' => ''],'paddingLg' => ['type' => 'string', 'default' => ''],'paddingXl' => ['type' => 'string', 'default' => ''],'paddingXxl' => ['type' => 'string', 'default' => ''],'marginSm' => ['type' => 'string', 'default' => ''],'marginMd' => ['type' => 'string', 'default' => ''],'marginLg' => ['type' => 'string', 'default' => ''],'marginXl' => ['type' => 'string', 'default' => ''],'marginXxl' => ['type' => 'string', 'default' => ''],'uniqueId' => ['type' => 'string', 'default' => ''],'customCss' => ['type' => 'string', 'default' => ''],'customScss' => ['type' => 'string', 'default' => ''],
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
                    'maxWidth' => ['type' => 'string', 'default' => ''],
                    'maxWidthUnit' => ['type' => 'string', 'default' => 'px'],
                    'containerClass' => ['type' => 'string', 'default' => ''],
                    'visibilityClass' => ['type' => 'string', 'default' => ''],
                    'visibilityXs' => ['type' => 'boolean', 'default' => true],
                    'visibilitySm' => ['type' => 'boolean', 'default' => true],
                    'visibilityMd' => ['type' => 'boolean', 'default' => true],
                    'visibilityLg' => ['type' => 'boolean', 'default' => true],
                    'visibilityXl' => ['type' => 'boolean', 'default' => true],
                ], $this->wpbb_responsive_spacing_attributes());
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
                if (!empty($attributes['backgroundColor'])) $style .= 'background-color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
        if (!empty($attributes['backgroundImageUrl'])) $style .= 'background-image:url(' . esc_url_raw((string)$attributes['backgroundImageUrl']) . ');background-size:' . preg_replace('/[^a-z% -]/i', '', (string)($attributes['backgroundSize'] ?? 'cover')) . ';background-position:' . preg_replace('/[^a-z% -]/i', '', (string)($attributes['backgroundPosition'] ?? 'center center')) . ';background-repeat:no-repeat;background-attachment:' . preg_replace('/[^a-z-]/i', '', (string)($attributes['backgroundAttachment'] ?? 'scroll')) . ';';
                if (!empty($attributes['textColor'])) $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
                if (!empty($attributes['borderRadius'])) $style .= 'border-radius:' . preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)$attributes['borderRadius']) . ';';
        if (!empty($attributes['boxShadowColor']) && !empty($attributes['boxShadowClass'])) $style .= 'box-shadow:0 10px 28px ' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['boxShadowColor']) . ';';
                if (!empty($attributes['maxWidth'])) { $mwRaw = trim((string)$attributes['maxWidth']); $mwu = preg_replace('/[^a-z%]/i', '', (string)($attributes['maxWidthUnit'] ?? '')); if ($mwu === '') $mwu = 'px'; if ($mwRaw === 'auto') { $style .= 'max-width:auto;'; } else { $mwNum = preg_replace('/[^0-9.\-]/', '', $mwRaw); if ($mwNum !== '') { $style .= 'max-width:' . $mwNum . $mwu . ';'; } } }
        if (!empty($attributes['customStyle'])) $style .= (string)$attributes['customStyle'];
                $cssTag = !empty($attributes['customCss']) ? '<style>#' . $uid . '{' . wp_strip_all_tags((string)$attributes['customCss']) . '}</style>' : '';
                $scssTag = !empty($attributes['customScss']) ? $this->wpbb_capture_style_tag($this->wpbb_compile_scoped_scss('#' . $uid, (string)$attributes['customScss'])) : '';
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
                return [
                    'address'=>['type'=>'string','default'=>''],
                    'zoom'=>['type'=>'number','default'=>14],
                    'height'=>['type'=>'string','default'=>'380px'],
                    'mapFilter'=>['type'=>'string','default'=>''],
                    'overlayColor'=>['type'=>'string','default'=>''],
                    'overlayOpacity'=>['type'=>'number','default'=>0.2],
                    'embedUrl'=>['type'=>'string','default'=>''],
                    'className'=>['type'=>'string','default'=>'']
                ];
            case 'file':
                return [
                    'title' => ['type' => 'string', 'default' => 'File'],
                    'fileUrl' => ['type' => 'string', 'default' => ''],
                    'fileName' => ['type' => 'string', 'default' => ''],
                    'buttonText' => ['type' => 'string', 'default' => 'Download file'],
                    'targetBlank' => ['type' => 'boolean', 'default' => true],
                    'className' => ['type' => 'string', 'default' => '']
                ];
            case 'inline-svg':
                return [
                    'title' => ['type' => 'string', 'default' => 'Inline SVG'],
                    'svgCode' => ['type' => 'string', 'default' => ''],
                    'className' => ['type' => 'string', 'default' => '']
                ];
            case 'menu-option':
                return [
                    'title' => ['type' => 'string', 'default' => 'Menu'],
                    'menuSlug' => ['type' => 'string', 'default' => ''],
                    'showMenuTitle' => ['type' => 'boolean', 'default' => false],
                    'className' => ['type' => 'string', 'default' => ''],
                ];
            case 'feature-list':
                return ['title'=>['type'=>'string','default'=>'Features'],'itemsJson'=>['type'=>'string','default'=>''],'iconColor'=>['type'=>'string','default'=>'#2563eb']];
            case 'timeline':
                return ['title'=>['type'=>'string','default'=>'Timeline'],'layout'=>['type'=>'string','default'=>'vertical'],'itemsJson'=>['type'=>'string','default'=>'']];
            case 'custom-embed':
                return ['title'=>['type'=>'string','default'=>'Embed'],'embedUrl'=>['type'=>'string','default'=>''],'embedHtml'=>['type'=>'string','default'=>''],'height'=>['type'=>'string','default'=>'420px']];
            case 'ai-content':
                return ['title'=>['type'=>'string','default'=>'AI Content'],'shortDescription'=>['type'=>'string','default'=>''],'prompt'=>['type'=>'string','default'=>''],'generatedText'=>['type'=>'string','default'=>''],'provider'=>['type'=>'string','default'=>'custom-api']];
            case 'login-register':
                return ['title'=>['type'=>'string','default'=>'Account Access'],'showRegister'=>['type'=>'boolean','default'=>true],'styleVariant'=>['type'=>'string','default'=>'split']];
            case 'bootstrap-div':
                return [
                    'tagName' => ['type' => 'string', 'default' => 'div'],
                    'maxWidth' => ['type' => 'string', 'default' => ''],
                    'maxWidthUnit' => ['type' => 'string', 'default' => 'px'],
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


public function render_alert_block($attributes, $content, $block) {
    $variant = sanitize_html_class($attributes['variant'] ?? 'primary');
    $text = wp_kses_post($attributes['text'] ?? '');
    $dismissible = !empty($attributes['dismissible']);
    if ($dismissible) WPBBuilder_Bootstrap::needs(['alert']);
    $classes = 'wpbb-alert alert alert-' . $variant . ($dismissible ? ' alert-dismissible fade show' : '');
    $wrapper = get_block_wrapper_attributes(['class' => $classes]);
    $button = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' : '';
    WPBBuilder_Bootstrap::enqueue_js_if_needed();
    return '<div ' . $wrapper . '>' . $text . $button . '</div>';
}

public function render_badge_block($attributes, $content, $block) {
    $variant = sanitize_html_class($attributes['variant'] ?? 'primary');
    $pill = !empty($attributes['pill']) ? ' rounded-pill' : '';
    $text = esc_html($attributes['text'] ?? '');
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-badge']);
    return '<div ' . $wrapper . '><span class="badge text-bg-' . $variant . $pill . '">' . $text . '</span></div>';
}

public function render_breadcrumb_block($attributes, $content, $block) {
    $items = wpbb_parse_fields_json($attributes['itemsJson'] ?? '[]');
    if (empty($items)) return '';
    $out = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';
    $last = count($items) - 1;
    foreach ($items as $i => $item) {
        $label = esc_html($item['label'] ?? 'Item');
        $url = esc_url($item['url'] ?? '');
        if ($i === $last || $url === '') {
            $out .= '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
        } else {
            $out .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $label . '</a></li>';
        }
    }
    $out .= '</ol></nav>';
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-breadcrumb']);
    return '<div ' . $wrapper . '>' . $out . '</div>';
}

public function render_list_group_block($attributes, $content, $block) {
    $items = wpbb_parse_fields_json($attributes['itemsJson'] ?? '[]');
    $tag = !empty($attributes['numbered']) ? 'ol' : 'ul';
    $classes = 'list-group' . (!empty($attributes['flush']) ? ' list-group-flush' : '') . (!empty($attributes['numbered']) ? ' list-group-numbered' : '');
    $html = '<' . $tag . ' class="' . esc_attr($classes) . '">';
    foreach ($items as $item) {
        $text = esc_html($item['text'] ?? 'Item');
        $active = !empty($item['active']) ? ' active' : '';
        $html .= '<li class="list-group-item' . $active . '">' . $text . '</li>';
    }
    $html .= '</' . $tag . '>';
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-list-group']);
    return '<div ' . $wrapper . '>' . $html . '</div>';
}

public function render_navbar_block($attributes, $content, $block) {
    WPBBuilder_Bootstrap::needs(['collapse','navbar']);
    WPBBuilder_Bootstrap::enqueue_js_if_needed();
    $brand = esc_html($attributes['brand'] ?? 'BBuilder');
    $brand_url = esc_url($attributes['brandUrl'] ?? '/');
    $expand = sanitize_html_class($attributes['expand'] ?? 'lg');
    $scheme = sanitize_html_class($attributes['scheme'] ?? 'light');
    $bg = sanitize_html_class($attributes['bgClass'] ?? 'bg-light');
    $items = wpbb_parse_fields_json($attributes['itemsJson'] ?? '[]');
    $id = 'wpbb-navbar-' . wp_generate_password(6, false, false);
    $links = '';
    foreach ($items as $item) {
        $label = esc_html($item['label'] ?? 'Link');
        $url = esc_url($item['url'] ?? '#');
        $active = !empty($item['active']) ? ' active' : '';
        $links .= '<li class="nav-item"><a class="nav-link' . $active . '" href="' . $url . '">' . $label . '</a></li>';
    }
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-navbar']);
    return '<nav ' . $wrapper . '><div class="navbar navbar-expand-' . $expand . ' navbar-' . $scheme . ' ' . $bg . ' rounded-4 px-3 py-2"><div class="container-fluid p-0"><a class="navbar-brand" href="' . $brand_url . '">' . $brand . '</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#' . esc_attr($id) . '" aria-controls="' . esc_attr($id) . '" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="' . esc_attr($id) . '"><ul class="navbar-nav ms-auto mb-2 mb-lg-0">' . $links . '</ul></div></div></div></nav>';
}

public function render_progress_block($attributes, $content, $block) {
    $value = max(0, min(100, intval($attributes['value'] ?? 0)));
    $label = esc_html($attributes['label'] ?? 'Progress');
    $variant = sanitize_html_class($attributes['variant'] ?? 'success');
    $bar_classes = 'progress-bar bg-' . $variant . (!empty($attributes['striped']) ? ' progress-bar-striped' : '') . (!empty($attributes['animated']) ? ' progress-bar-animated' : '');
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-progress']);
    return '<div ' . $wrapper . '><div class="d-flex justify-content-between small mb-2"><span>' . $label . '</span><strong>' . $value . '%</strong></div><div class="progress" role="progressbar" aria-label="' . $label . '" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100"><div class="' . esc_attr($bar_classes) . '" style="width:' . $value . '%">' . $value . '%</div></div></div>';
}

public function render_section_block($attributes, $content, $block) {
    $title = esc_html($attributes['title'] ?? 'Section');
    $lead = wp_kses_post($attributes['lead'] ?? '');
    $container = sanitize_html_class($attributes['containerClass'] ?? 'container');
    $bg = sanitize_html_class($attributes['backgroundClass'] ?? 'py-5');
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-section ' . $bg]);
    return '<section ' . $wrapper . '><div class="' . $container . '"><div class="wpbb-section__intro mb-4"><h2 class="h3 mb-2">' . $title . '</h2><div class="text-secondary">' . $lead . '</div></div>' . $content . '</div></section>';
}

public function render_spinner_block($attributes, $content, $block) {
    $type = ($attributes['type'] ?? 'border') === 'grow' ? 'spinner-grow' : 'spinner-border';
    $variant = sanitize_html_class($attributes['variant'] ?? 'primary');
    $label = esc_html($attributes['label'] ?? 'Loading');
    $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-spinner text-' . $variant]);
    return '<div ' . $wrapper . '><div class="' . $type . '" role="status"><span class="visually-hidden">' . $label . '</span></div></div>';
}

    public function render_table_block($attributes, $content, $block) {
        $csv = trim((string) ($attributes['csvText'] ?? ''));
        if ($csv === '') return '';

        // Normalize all newline formats saved by block attributes
        $csv = str_replace(["\\\\r\\\\n", "\\\\n", "\\\\r"], "\n", $csv);
        $csv = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $csv);
        $csv = str_replace(["\r\n", "\r"], "\n", $csv);

        $delimiter = !empty($attributes['delimiter']) ? (string) $attributes['delimiter'] : ',';
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv);
        $rows = [];
        $stream = fopen('php://temp', 'r+');
        if ($stream) {
            fwrite($stream, $csv);
            rewind($stream);
            while (($data = fgetcsv($stream, 0, $delimiter)) !== false) {
                if ($data === [null]) continue;
                $rows[] = array_map(function($cell) {
                    return is_string($cell) ? trim($cell) : $cell;
                }, $data);
            }
            fclose($stream);
        }
        if (empty($rows)) return '';

        $use_header = !empty($attributes['useFirstRowHeader']);
        $headers = $use_header ? array_shift($rows) : [];
        $col_count = !empty($headers) ? count($headers) : 0;
        foreach ($rows as $row) {
            $col_count = max($col_count, count((array)$row));
        }
        if ($col_count < 1) return '';

        if ($use_header && empty($headers)) {
            $headers = array_fill(0, $col_count, '');
        } elseif (!empty($headers) && count($headers) < $col_count) {
            $headers = array_pad($headers, $col_count, '');
        }

        $table_classes = trim((string) ($attributes['tableClass'] ?? ''));
        if ($table_classes === '') {
            $table_classes = 'table table-striped table-hover align-middle';
        } elseif (strpos(' ' . $table_classes . ' ', ' table ') === false) {
            $table_classes = 'table ' . $table_classes;
        }
        if (!empty($attributes['small']) && strpos($table_classes, 'table-sm') === false) {
            $table_classes .= ' table-sm';
        }
        if (!empty($attributes['bordered']) && strpos($table_classes, 'table-bordered') === false) {
            $table_classes .= ' table-bordered';
        }

        $table_html = '<table class="' . esc_attr(trim($table_classes)) . '">';
        if ($use_header) {
            $table_html .= '<thead><tr>';
            foreach ($headers as $header) {
                $table_html .= '<th scope="col">' . esc_html($header) . '</th>';
            }
            $table_html .= '</tr></thead>';
        } elseif (!empty($attributes['datatable'])) {
            $table_html .= '<thead><tr>';
            for ($i = 0; $i < $col_count; $i++) {
                $table_html .= '<th scope="col">' . esc_html('Column ' . ($i + 1)) . '</th>';
            }
            $table_html .= '</tr></thead>';
        }

        $table_html .= '<tbody>';
        foreach ($rows as $row) {
            $row = array_pad((array)$row, $col_count, '');
            $table_html .= '<tr>';
            foreach ($row as $cell) {
                $table_html .= '<td>' . esc_html($cell) . '</td>';
            }
            $table_html .= '</tr>';
        }
        $table_html .= '</tbody></table>';

        if (!empty($attributes['responsive']) && empty($attributes['datatable'])) {
            $table_html = '<div class="table-responsive">' . $table_html . '</div>';
        }

        $wrapper_args = ['class' => 'wpbb-table-block'];

        if (!empty($attributes['datatable'])) {
            wp_enqueue_style('wpbb-datatables');
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpbb-datatables');
            wp_enqueue_script('wpbb-datatables-bs5');
            wp_enqueue_script('wpbb-table-init');

            $wrapper_args['data-datatable'] = '1';
            $wrapper_args['data-searching'] = !empty($attributes['datatableSearch']) ? '1' : '0';
            $wrapper_args['data-paging'] = !empty($attributes['datatablePaging']) ? '1' : '0';
            $wrapper_args['data-ordering'] = !empty($attributes['datatableOrdering']) ? '1' : '0';
            $wrapper_args['data-info'] = !empty($attributes['datatableInfo']) ? '1' : '0';
            $wrapper_args['data-lengthchange'] = !empty($attributes['datatableLengthChange']) ? '1' : '0';
        }

        $wrapper = get_block_wrapper_attributes($wrapper_args);
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
        if (is_admin()) return;
        if (!$this->page_has_wpbb_blocks()) return;

        WPBBuilder_Bootstrap::enqueue_css();
        if (wp_style_is('wpbb-shared', 'registered')) {
            wp_enqueue_style('wpbb-shared');
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

    private function page_has_wpbb_blocks() {
        if (is_admin()) return false;
        if (is_singular()) {
            global $post;
            if (!$post || empty($post->post_content)) return false;
            return strpos($post->post_content, '<!-- wp:wpbb/') !== false;
        }
        return true;
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
        if (!wpbb_get_option('disable_core_audio', 0)) $core[] = 'core/audio';
        if (!wpbb_get_option('disable_core_file', 0)) $core[] = 'core/file';
        if (!wpbb_get_option('disable_core_buttons', 0)) $core[] = 'core/buttons';
        if (!wpbb_get_option('disable_core_button', 0)) $core[] = 'core/button';
        if (!wpbb_get_option('disable_core_query', 0)) {
            $core = array_merge($core, [
                'core/query','core/post-template','core/query-pagination','core/query-pagination-next',
                'core/query-pagination-previous','core/query-pagination-numbers','core/post-title',
                'core/post-excerpt','core/post-date','core/post-featured-image','core/post-terms','core/read-more'
            ]);
        }

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
        $title = esc_html($attributes['title'] ?? 'Weather');
        $location = esc_attr($attributes['location'] ?? 'London');
        $lang = esc_attr($attributes['lang'] ?? 'en');
        $apiKey = esc_attr($attributes['apiKey'] ?? wpbb_get_option('weather_api_key', ''));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-weather card', 'data-location' => $location, 'data-lang' => $lang, 'data-api-key' => $apiKey]);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-weather-location\">{$location}</div><div class=\"wpbb-weather-temp\">--°C</div><div class=\"wpbb-weather-note\">Loading live weather...</div></div></div>";
    }

    public function render_varda_dienas_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Name Days');
        $today_key = wp_date('m-d');
        $dateTextRaw = trim((string)($attributes['dateText'] ?? ''));
        $dateText = esc_html($dateTextRaw !== '' ? $dateTextRaw : wp_date('j F'));
        $manual_names = trim((string)($attributes['names'] ?? ''));
        $json_names = [];
        $live_names = [];
        $json_raw = (string)($attributes['namesJson'] ?? '');

        if ($json_raw !== '') {
            $decoded = json_decode($json_raw, true);
            if (is_array($decoded) && !empty($decoded[$today_key]) && is_array($decoded[$today_key])) {
                $json_names = array_map('sanitize_text_field', $decoded[$today_key]);
            }
        }

        if (empty($json_names)) {
            $json_file = WPBB_PLUGIN_DIR . 'assets/json/varda-dienas.json';
            if (file_exists($json_file)) {
                $decoded = json_decode((string) file_get_contents($json_file), true);
                if (is_array($decoded) && !empty($decoded[$today_key]) && is_array($decoded[$today_key])) {
                    $json_names = array_map('sanitize_text_field', $decoded[$today_key]);
                }
            }
        }

        $cache_key = 'wpbb_name_days_lv_' . gmdate('Ymd');
        $cached = get_transient($cache_key);
        if (is_array($cached)) {
            $live_names = $cached;
        } else {
            $response = wp_remote_get('https://nameday.abalin.net/api/V2/today?country=lv', [
                'timeout' => 10,
                'headers' => ['Accept' => 'application/json'],
            ]);

            if (!is_wp_error($response) && (int) wp_remote_retrieve_response_code($response) === 200) {
                $body = json_decode((string) wp_remote_retrieve_body($response), true);
                if (!empty($body['data']['namedays']['lv'])) {
                    $raw_names = preg_split('/\s*,\s*/', (string) $body['data']['namedays']['lv']);
                    $live_names = array_values(array_filter(array_map('sanitize_text_field', $raw_names)));
                    if (!empty($live_names)) {
                        set_transient($cache_key, $live_names, DAY_IN_SECONDS);
                    }
                }
            }
        }

        $names_list = !empty($live_names) ? $live_names : (!empty($json_names) ? $json_names : ($manual_names !== '' ? preg_split('/\s*,\s*/', $manual_names) : []));
        $names_list = array_values(array_filter(array_map('sanitize_text_field', (array) $names_list)));
        $names = !empty($names_list) ? implode(', ', $names_list) : 'No Latvian name days found for today.';
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-varda-dienas card']);
        return '<div ' . $wrapper . '><div class="card-body"><h3 class="card-title">' . $title . '</h3><div class="small text-muted">' . $dateText . '</div><div class="wpbb-varda-dienas-names">' . esc_html($names) . '</div></div></div>';
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
        $title = esc_html($attributes['title'] ?? 'Pricing');
        $cards = wpbb_parse_fields_json($attributes['cardsJson'] ?? '');
        if (!$cards) $cards = [
            ['title'=>'Basic','price'=>'9','period'=>'/mo','text'=>'Short plan description','button'=>'Choose plan','featured'=>false],
            ['title'=>'Pro','price'=>'29','period'=>'/mo','text'=>'Short plan description','button'=>'Choose plan','featured'=>true]
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
        wp_enqueue_script('wpbb-chart-view');
        $title = esc_html($attributes['title'] ?? 'Countdown');
        $target = esc_attr($attributes['targetDate'] ?? '2030-01-01T00:00:00');
        $variant = sanitize_html_class($attributes['styleVariant'] ?? 'default');
        $accent = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['accentColor'] ?? '#2563eb'));
        $bg = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['backgroundColor'] ?? ''));
        $text = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['textColor'] ?? ''));
        $shadow = sanitize_html_class($attributes['boxShadowClass'] ?? 'shadow-sm');
        $style = '';
        if ($bg) $style .= 'background:' . $bg . ';';
        if ($text) $style .= 'color:' . $text . ';';
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-countdown-timer wpbb-countdown-timer--' . $variant . ' card ' . $shadow, 'data-target-date' => $target, 'style' => $style]);
        $labels = [
            esc_html($attributes['labelDays'] ?? 'Days'),
            esc_html($attributes['labelHours'] ?? 'Hours'),
            esc_html($attributes['labelMinutes'] ?? 'Minutes'),
            esc_html($attributes['labelSeconds'] ?? 'Seconds')
        ];
        $segments = '';
        foreach ($labels as $lab) {
            $segments .= '<div class="wpbb-countdown-timer__segment" style="border-color:' . esc_attr($accent) . '"><strong>00</strong><span>' . $lab . '</span></div>';
        }
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-countdown-timer__value\">{$segments}</div></div></div>";
    }

    public function render_chart_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-chart-view');
        $title = esc_html($attributes['title'] ?? 'Chart');
        $type = esc_attr($attributes['chartType'] ?? 'bar');
        $json = esc_attr($attributes['chartDataJson'] ?? '');
        $opts = esc_attr($attributes['chartOptionsJson'] ?? '');
        $height = preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)($attributes['height'] ?? '320px'));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-chart card', 'data-chart-type' => $type, 'data-chart-json' => $json, 'data-chart-options' => $opts]);
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><div class=\"wpbb-chart__canvas\" style=\"min-height:{$height}\">Chart preview</div></div></div>";
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
        $variant = sanitize_html_class($attributes['styleVariant'] ?? 'soft');
        $useHcaptcha = !empty($attributes['useHcaptcha']);
        $btnBg = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['submitBg'] ?? '#2563eb'));
        $btnColor = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['submitColor'] ?? '#ffffff'));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-mailchimp card wpbb-mailchimp--' . $variant]);
        $nameField = $showName ? '<div class="col-12"><input type="text" class="form-control" name="FNAME" placeholder="Name"></div>' : '';
        $hcaptcha = $useHcaptcha ? '<div class="wpbb-captcha-note">hCaptcha enabled. Add site key/secret in plugin settings for production.</div>' : '';
        return "<div {$wrapper}><div class=\"card-body\"><h3 class=\"card-title\">{$title}</h3><p>{$text}</p><form class=\"wpbb-mailchimp-form\" method=\"post\" action=\"{$action}\" target=\"_blank\"><div class=\"row g-2\">{$nameField}<div class=\"col-12\"><div class=\"input-group\"><input type=\"email\" class=\"form-control\" name=\"{$fieldName}\" placeholder=\"Email\"><button class=\"btn btn-primary\" type=\"submit\" style=\"background:{$btnBg};border-color:{$btnBg};color:{$btnColor}\">{$buttonText}</button></div></div></div>{$hcaptcha}</form></div></div>";
    }


    public function render_bootstrap_div_block($attributes, $content, $block) {
        $tag = in_array(($attributes['tagName'] ?? 'div'), ['div','section','article','aside'], true) ? $attributes['tagName'] : 'div';
        $classes = trim('wpbb-bootstrap-div ' . trim((string)($attributes['utilityClasses'] ?? '')) . ' ' . trim((string)($attributes['className'] ?? '')));
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

    private function wpbb_compile_preview_css($selector, $scss) {
        $scss = trim((string)$scss);
        if ($scss === '') return '';
        return trim(preg_replace('/\s+/', ' ', $this->wpbb_compile_scoped_scss($selector, $scss)));
    }

    private function wpbb_collect_spacing_classes($attributes) {
        $classes = [];
        foreach (['spacingSm','spacingMd','spacingLg','spacingXl','spacingXxl','paddingSm','paddingMd','paddingLg','paddingXl','paddingXxl','marginSm','marginMd','marginLg','marginXl','marginXxl'] as $k) {
            if (empty($attributes[$k])) continue;
            $classes = array_merge($classes, $this->wpbb_class_tokens_from_value($attributes[$k]));
        }
        return array_values(array_unique($classes));
    }


    private function wpbb_capture_style_tag($css) {
        $css = trim((string) $css);
        if ($css === '') return '';
        if (!wpbb_get_option('aggregate_inline_block_css', 1)) {
            return '<style>' . $css . '</style>';
        }
        global $wpbb_inline_block_css_buffer;
        if (!isset($wpbb_inline_block_css_buffer) || !is_array($wpbb_inline_block_css_buffer)) {
            $wpbb_inline_block_css_buffer = [];
        }
        $wpbb_inline_block_css_buffer[] = $css;
        return '';
    }


    public function render_generic_block($attributes, $content, $block) {
        $name = '';
        if (is_object($block) && !empty($block->name)) {
            $name = (string) $block->name;
        } elseif (is_array($block) && !empty($block['blockName'])) {
            $name = (string) $block['blockName'];
        }
        $slug = preg_replace('~^wpbb/~', '', $name);
        $extra = !empty($attributes['className']) ? ' ' . sanitize_html_class($attributes['className']) : '';

        switch ($slug) {
            case 'google-map':
                $address = sanitize_text_field($attributes['address'] ?? '');
                $legacy_embed = trim((string) ($attributes['embedUrl'] ?? ''));
                $height = trim((string) ($attributes['height'] ?? '380px'));
                if ($height === '') $height = '380px';
                if (preg_match('/^\d+$/', $height)) $height .= 'px';
                $height_attr = preg_replace('/[^0-9.]/', '', $height);
                if ($height_attr === '') $height_attr = '380';
                $zoom = max(1, min(21, intval($attributes['zoom'] ?? 14)));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-google-map' . $extra]);

                $src = '';
                if ($address !== '') {
                    $src = 'https://www.google.com/maps?output=embed&q=' . rawurlencode($address) . '&z=' . $zoom;
                } elseif ($legacy_embed !== '') {
                    if (preg_match('~src=["\']([^"\']+)["\']~i', $legacy_embed, $m)) {
                        $src = $m[1];
                    } else {
                        $src = $legacy_embed;
                    }
                    $src = html_entity_decode($src, ENT_QUOTES, 'UTF-8');
                    $src = preg_replace('~^http://~i', 'https://', $src);
                    if (strpos($src, 'output=embed') === false) {
                        $src .= (strpos($src, '?') !== false ? '&' : '?') . 'output=embed';
                    }
                }

                if ($src === '') {
                    return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Add address', 'wp-bbuilder') . '</div></div>';
                }

                $overlay_color = trim((string) ($attributes['overlayColor'] ?? ''));
                $overlay_opacity = isset($attributes['overlayOpacity']) ? max(0, min(1, floatval($attributes['overlayOpacity']))) : 0;
                if ($overlay_color === '' && !empty($attributes['mapFilter']) && preg_match('/^(#|rgb|rgba|hsl|hsla)/i', trim((string) $attributes['mapFilter']))) {
                    $overlay_color = trim((string) $attributes['mapFilter']);
                    if ($overlay_opacity <= 0) $overlay_opacity = 0.2;
                }

                $html = '<div ' . $wrapper . '>';
                $html .= '<div class="wpbb-google-map__frame" style="position:relative;width:100%;min-height:' . esc_attr($height) . ';overflow:hidden;background:#f8fafc;">';
                $html .= '<iframe class="wpbb-google-map__iframe" src="' . esc_url($src) . '" title="' . esc_attr($address !== '' ? $address : __('Google map', 'wp-bbuilder')) . '" width="100%" height="' . esc_attr($height_attr) . '" style="border:0;width:100%;height:' . esc_attr($height) . ';min-height:' . esc_attr($height) . ';display:block;visibility:visible;opacity:1;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
                if ($overlay_color !== '' && $overlay_opacity > 0) {
                    $html .= '<span class="wpbb-google-map__overlay" aria-hidden="true" style="position:absolute;inset:0;pointer-events:none;background:' . esc_attr($overlay_color) . ';opacity:' . esc_attr((string) $overlay_opacity) . ';"></span>';
                }
                if ($address !== '') {
                    $html .= '<div class="wpbb-google-map__fallback" style="padding-top:8px;"><a href="' . esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address)) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Open map in Google Maps', 'wp-bbuilder') . '</a></div>';
                }
                $html .= '</div></div>';
                return $html;

            case 'file':
                $file_url = esc_url($attributes['fileUrl'] ?? '');
                $file_name = trim((string) ($attributes['fileName'] ?? ''));
                $button_text = trim((string) ($attributes['buttonText'] ?? 'Download file'));
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-file-block' . $extra]);
                if ($file_url === '') return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Add file URL', 'wp-bbuilder') . '</div></div>';
                if ($file_name === '') $file_name = basename((string) wp_parse_url($file_url, PHP_URL_PATH));
                $target = !empty($attributes['targetBlank']) ? ' target="_blank" rel="noopener"' : '';
                return '<div ' . $wrapper . '><div class="wpbb-file-block__name">' . esc_html($file_name) . '</div><a class="wpbb-file-block__link btn btn-primary" href="' . esc_url($file_url) . '"' . $target . '>' . esc_html($button_text !== '' ? $button_text : 'Download file') . '</a></div>';

            case 'inline-svg':
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-inline-svg' . $extra]);
                $svg_code = trim((string) ($attributes['svgCode'] ?? ''));
                if ($svg_code === '') return '<div ' . $wrapper . '><div class="wpbb-empty-note">' . esc_html__('Paste SVG code', 'wp-bbuilder') . '</div></div>';
                $svg_code = wp_kses($svg_code, [
                    'svg' => ['xmlns'=>true,'viewBox'=>true,'width'=>true,'height'=>true,'fill'=>true,'stroke'=>true,'class'=>true,'role'=>true,'aria-hidden'=>true,'focusable'=>true,'style'=>true],
                    'g' => ['fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'class'=>true,'style'=>true],
                    'path' => ['d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'class'=>true,'style'=>true],
                    'circle' => ['cx'=>true,'cy'=>true,'r'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'rect' => ['x'=>true,'y'=>true,'rx'=>true,'ry'=>true,'width'=>true,'height'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'polygon' => ['points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'polyline' => ['points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'line' => ['x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'ellipse' => ['cx'=>true,'cy'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'class'=>true,'style'=>true],
                    'defs' => [], 'clipPath' => ['id'=>true], 'mask' => ['id'=>true], 'title' => [], 'desc' => [],
                    'linearGradient' => ['id'=>true,'x1'=>true,'x2'=>true,'y1'=>true,'y2'=>true], 'radialGradient' => ['id'=>true,'cx'=>true,'cy'=>true,'r'=>true],
                    'stop' => ['offset'=>true,'stop-color'=>true,'stop-opacity'=>true]
                ]);
                return '<div ' . $wrapper . '>' . $svg_code . '</div>';

            default:
                $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-generic-block wpbb-' . sanitize_html_class($slug) . $extra]);
                return '<div ' . $wrapper . '>' . $content . '</div>';
        }
    }

    public function render_row_block($attributes, $content, $block) {
        $row_classes = ['row', 'wpbb-row'];
        foreach (['gutterX','gutterY','paddingClass','marginClass','backgroundClass','animationClass','displayClass','textUtilityClass','roundedClass','shadowClass','bootstrapClasses','utilityClasses','customClasses','visibilityClass','className'] as $k) {
            if (empty($attributes[$k])) continue;
            $row_classes = array_merge($row_classes, $this->wpbb_class_tokens_from_value($attributes[$k]));
        }
        $row_classes = array_merge($row_classes, $this->wpbb_collect_spacing_classes($attributes));
        if (!empty($attributes['align'])) $row_classes[] = 'justify-content-' . sanitize_html_class((string)$attributes['align']);
        $uid = !empty($attributes['uniqueId']) ? sanitize_html_class((string)$attributes['uniqueId']) : sanitize_html_class('wpbb-row-' . wp_unique_id());
        $row_style = $this->wpbb_build_spacing_inline($attributes);
        if (!empty($attributes['backgroundColor'])) $row_style .= 'background-color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
        if (!empty($attributes['backgroundImageUrl'])) $row_style .= 'background-image:url(' . esc_url_raw((string)$attributes['backgroundImageUrl']) . ');background-size:' . preg_replace('/[^a-z% -]/i', '', (string)($attributes['backgroundSize'] ?? 'cover')) . ';background-position:' . preg_replace('/[^a-z% -]/i', '', (string)($attributes['backgroundPosition'] ?? 'center center')) . ';background-repeat:no-repeat;background-attachment:' . preg_replace('/[^a-z-]/i', '', (string)($attributes['backgroundAttachment'] ?? 'scroll')) . ';';
        if (!empty($attributes['textColor'])) $row_style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
        if (!empty($attributes['customStyle'])) $row_style .= (string)$attributes['customStyle'];

        $responsiveSpacingCss = $this->wpbb_build_responsive_spacing_css($attributes, '#' . $uid);
        $spacingTag = $responsiveSpacingCss !== '' ? $this->wpbb_capture_style_tag($responsiveSpacingCss) : '';
        $scssTag = !empty($attributes['customScss']) ? $this->wpbb_capture_style_tag($this->wpbb_compile_scoped_scss('#' . $uid, (string)$attributes['customScss'])) : '';

        $row_wrapper = get_block_wrapper_attributes([
            'class' => implode(' ', array_values(array_unique(array_filter($row_classes)))),
            'style' => $row_style,
            'id' => $uid
        ]);

        $row_html = '<div ' . $row_wrapper . '>' . $content . '</div>';

        if (!empty($attributes['containerClass'])) {
            $container_tokens = array_values(array_unique(array_filter($this->wpbb_class_tokens_from_value($attributes['containerClass']))));
            $container_class = implode(' ', $container_tokens);
            if ($container_class !== '') {
                $container_style = '';
                if ($container_class === 'container-fluid') {
                    $container_style = ' style="max-width:none;width:100%;"';
                }
                $row_html = '<div class="' . esc_attr($container_class) . '"' . $container_style . '>' . $row_html . '</div>';
            }
        }

        if (!empty($attributes['overlayColor']) && !empty($attributes['overlayOpacity'])) {
            $opacity = floatval($attributes['overlayOpacity']);
            $row_html = '<div style="position:relative;overflow:hidden;">'
                . '<div class="wpbb-block-overlay" style="position:absolute;inset:0;pointer-events:none;background:' . esc_attr((string)$attributes['overlayColor']) . ';opacity:' . $opacity . ';"></div>'
                . '<div class="wpbb-block-content" style="position:relative;z-index:1">' . $row_html . '</div>'
                . '</div>';
        }

        return $spacingTag . $scssTag . $row_html;
    }

    public function render_column_block($attributes, $content, $block) {
        $classes = ['wpbb-column'];
        $bpMap = ['xs'=>'col','sm'=>'col-sm','md'=>'col-md','lg'=>'col-lg','xl'=>'col-xl','xxl'=>'col-xxl'];
        foreach ($bpMap as $bp => $prefix) {
            $val = isset($attributes[$bp]) ? intval($attributes[$bp]) : 0;
            if ($bp === 'xs' && $val <= 0) $val = 12;
            if ($val > 0) $classes[] = $prefix . '-' . $val;
        }
        foreach (['orderClass','verticalAlign','horizontalAlign','visibilityClass','animationClass','paddingClass','marginClass','backgroundClass','displayClass','textUtilityClass','roundedClass','shadowClass','boxShadowClass','bootstrapClasses','utilityClasses','customClasses','className'] as $k) {
            if (empty($attributes[$k])) continue;
            $classes = array_merge($classes, $this->wpbb_class_tokens_from_value($attributes[$k]));
        }
        $classes = array_merge($classes, $this->wpbb_collect_spacing_classes($attributes));
        $uid = !empty($attributes['uniqueId']) ? sanitize_html_class((string)$attributes['uniqueId']) : sanitize_html_class('wpbb-col-' . wp_unique_id());
        $style = $this->wpbb_build_spacing_inline($attributes);
        if (!empty($attributes['backgroundColor'])) $style .= 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
        if (!empty($attributes['textColor'])) $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
        if (!empty($attributes['borderRadius'])) $style .= 'border-radius:' . preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)$attributes['borderRadius']) . ';';
        if (!empty($attributes['customStyle'])) $style .= (string)$attributes['customStyle'];
        $responsiveSpacingCss = $this->wpbb_build_responsive_spacing_css($attributes, '#' . $uid);
        $spacingTag = $responsiveSpacingCss !== '' ? $this->wpbb_capture_style_tag($responsiveSpacingCss) : '';
        $scssTag = !empty($attributes['customScss']) ? $this->wpbb_capture_style_tag($this->wpbb_compile_scoped_scss('#' . $uid, (string)$attributes['customScss'])) : '';
        $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_values(array_unique(array_filter($classes)))), 'style' => $style, 'id' => $uid]);
        $inner = $content;
        if (!empty($attributes['containerClass'])) {
            $container_class = implode(' ', $this->wpbb_class_tokens_from_value($attributes['containerClass']));
            $inner = '<div class="' . esc_attr($container_class) . '">' . $content . '</div>';
        }
        $overlay = '';
        if (!empty($attributes['overlayColor']) && !empty($attributes['overlayOpacity'])) {
            $overlay = '<div class="wpbb-block-overlay" style="position:absolute;inset:0;pointer-events:none;background:' . esc_attr((string)$attributes['overlayColor']) . ';opacity:' . floatval($attributes['overlayOpacity']) . ';"></div>';
            $inner = '<div class="wpbb-block-content" style="position:relative;z-index:1">' . $inner . '</div>';
            $style .= 'position:relative;overflow:hidden;';
            $wrapper = get_block_wrapper_attributes(['class' => implode(' ', array_values(array_unique(array_filter($classes)))), 'style' => $style, 'id' => $uid]);
        }
        return $spacingTag . $scssTag . '<div ' . $wrapper . '>' . $overlay . $inner . '</div>';
    }

    public function render_social_follow_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? __('Follow Us', 'wp-bbuilder'));
        $titleTag = in_array(($attributes['titleTag'] ?? 'span'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'span') : 'span';
        $style = $attributes['socialStyle'] ?? 'icons';
        $sizeMap = ['sm' => '34px', 'md' => '42px', 'lg' => '50px'];
        $iconSize = $sizeMap[$attributes['iconSize'] ?? 'md'] ?? '42px';
        $shape = $attributes['iconShape'] ?? 'rounded';
        $shapeRadius = $shape === 'circle' ? '999px' : ($shape === 'square' ? '0' : '12px');
        $showLabels = !empty($attributes['showLabels']);
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-follow wpbb-soc-style-' . sanitize_html_class($style)]);
        $items = [
            'facebook' => esc_url($attributes['facebook'] ?? ''),
            'instagram' => esc_url($attributes['instagram'] ?? ''),
            'linkedin' => esc_url($attributes['linkedin'] ?? ''),
            'x' => esc_url($attributes['x'] ?? ''),
            'youtube' => esc_url($attributes['youtube'] ?? ''),
            'tiktok' => esc_url($attributes['tiktok'] ?? ''),
            'pinterest' => esc_url($attributes['pinterest'] ?? ''),
            'whatsapp' => esc_url($attributes['whatsapp'] ?? ''),
            'email' => !empty($attributes['email']) ? 'mailto:' . antispambot(sanitize_email((string)$attributes['email'])) : '',
        ];
        $labels = ['facebook'=>'Facebook','instagram'=>'Instagram','linkedin'=>'LinkedIn','x'=>'X','youtube'=>'YouTube','tiktok'=>'TikTok','pinterest'=>'Pinterest','whatsapp'=>'WhatsApp','email'=>'Email'];
        $links = '';
        foreach ($items as $key => $url) {
            if (!$url) continue;
            $bg = !empty($attributes['iconBgColor']) ? (string)$attributes['iconBgColor'] : '#0f172a';
            $fg = !empty($attributes['iconTextColor']) ? (string)$attributes['iconTextColor'] : '#ffffff';
            $icon = $this->wpbb_svg_icon($key);
            if ($style === 'buttons') {
                $links .= '<a href="' . esc_url($url) . '" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer"><span class="wpbb-social-icon wpbb-social-icon--inline" style="width:26px;height:26px;border-radius:' . esc_attr($shapeRadius) . ';background:' . esc_attr($bg) . ';color:' . esc_attr($fg) . ';">' . $icon . '</span>' . esc_html($labels[$key]) . '</a>';
            } else {
                $links .= '<a href="' . esc_url($url) . '" class="wpbb-social-icon" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($labels[$key]) . '" style="width:' . esc_attr($iconSize) . ';height:' . esc_attr($iconSize) . ';border-radius:' . esc_attr($shapeRadius) . ';background:' . esc_attr($bg) . ';color:' . esc_attr($fg) . ';">' . $icon . '</a>';
                if ($showLabels) $links .= '<span class="wpbb-social-label">' . esc_html($labels[$key]) . '</span>';
            }
        }
        if ($links === '') return '';
        return '<div ' . $wrapper . '>' . ($title ? '<' . $titleTag . ' class="wpbb-soc-title">' . $title . '</' . $titleTag . '>' : '') . '<div class="wpbb-soc-links">' . $links . '</div></div>';
    }

    public function render_social_share_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? __('Share', 'wp-bbuilder'));
        $titleTag = in_array(($attributes['titleTag'] ?? 'span'), ['h1','h2','h3','h4','h5','h6','div','p','span'], true) ? ($attributes['titleTag'] ?: 'span') : 'span';
        $style = $attributes['iconStyle'] ?? 'icons';
        $sizeMap = ['sm' => '34px', 'md' => '42px', 'lg' => '50px'];
        $iconSize = $sizeMap[$attributes['iconSize'] ?? 'md'] ?? '42px';
        $shape = $attributes['iconShape'] ?? 'rounded';
        $shapeRadius = $shape === 'circle' ? '999px' : ($shape === 'square' ? '0' : '12px');
        $shareUrl = rawurlencode(get_permalink());
        $shareTitle = rawurlencode(get_the_title());
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-soc-share wpbb-soc-style-' . sanitize_html_class($style)]);
        $items = [
            'facebook' => ['url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl, 'label' => 'Facebook'],
            'x' => ['url' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle, 'label' => 'X'],
            'linkedin' => ['url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl, 'label' => 'LinkedIn'],
            'whatsapp' => ['url' => 'https://wa.me/?text=' . $shareTitle . '%20' . $shareUrl, 'label' => 'WhatsApp'],
            'email' => ['url' => 'mailto:?subject=' . $shareTitle . '&body=' . $shareUrl, 'label' => 'Email'],
        ];
        $links = '';
        foreach ($items as $key => $data) {
            $bg = !empty($attributes['iconBgColor']) ? (string)$attributes['iconBgColor'] : '#0f172a';
            $fg = !empty($attributes['iconColor']) ? (string)$attributes['iconColor'] : '#ffffff';
            $icon = $this->wpbb_svg_icon($key);
            if ($style === 'buttons') {
                $links .= '<a href="' . esc_url($data['url']) . '" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer"><span class="wpbb-social-icon wpbb-social-icon--inline" style="width:26px;height:26px;border-radius:' . esc_attr($shapeRadius) . ';background:' . esc_attr($bg) . ';color:' . esc_attr($fg) . ';">' . $icon . '</span>' . esc_html($data['label']) . '</a>';
            } else {
                $links .= '<a href="' . esc_url($data['url']) . '" class="wpbb-share-link wpbb-social-icon" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($data['label']) . '" style="width:' . esc_attr($iconSize) . ';height:' . esc_attr($iconSize) . ';border-radius:' . esc_attr($shapeRadius) . ';background:' . esc_attr($bg) . ';color:' . esc_attr($fg) . ';">' . $icon . '</a>';
            }
        }
        return '<div ' . $wrapper . '>' . ($title ? '<' . $titleTag . ' class="wpbb-share-title">' . $title . '</' . $titleTag . '>' : '') . '<div class="wpbb-share-links">' . $links . '</div></div>';
    }



    public function render_feature_list_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Features');
        $items = wpbb_parse_fields_json($attributes['itemsJson'] ?? '');
        if (!$items) $items = [['title'=>'Fast setup','text'=>'Launch quickly with reusable UI.'],['title'=>'Clear messaging','text'=>'Highlight your strongest value points.']];
        $icon_color = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['iconColor'] ?? '#2563eb'));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-feature-list']);
        $html = '<div ' . $wrapper . '>';
        if ($title) $html .= '<h3>' . $title . '</h3>';
        $html .= '<div class="wpbb-feature-list__grid">';
        foreach ($items as $item) {
            $html .= '<div class="wpbb-feature-item"><span class="wpbb-feature-item__icon" style="color:' . esc_attr($icon_color) . '">✓</span><div><div class="wpbb-feature-item__title">' . esc_html($item['title'] ?? '') . '</div><div class="wpbb-feature-item__text">' . esc_html($item['text'] ?? '') . '</div></div></div>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function render_timeline_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Timeline');
        $layout = sanitize_html_class($attributes['layout'] ?? 'vertical');
        $items = wpbb_parse_fields_json($attributes['itemsJson'] ?? '');
        if (!$items) $items = [['date'=>'2024','title'=>'Discovery','text'=>'Research and planning.'],['date'=>'2025','title'=>'Launch','text'=>'Implementation and launch.']];
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-timeline wpbb-timeline--' . $layout]);
        $html = '<div ' . $wrapper . '>';
        if ($title) $html .= '<h3>' . $title . '</h3>';
        $html .= '<div class="wpbb-timeline__items">';
        foreach ($items as $item) {
            $html .= '<div class="wpbb-timeline__item"><div class="wpbb-timeline__dot"></div><div class="wpbb-timeline__content"><div class="wpbb-timeline__date">' . esc_html($item['date'] ?? '') . '</div><div class="wpbb-timeline__title">' . esc_html($item['title'] ?? '') . '</div><div class="wpbb-timeline__text">' . esc_html($item['text'] ?? '') . '</div></div></div>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function render_custom_embed_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Embed');
        $embed_url = esc_url($attributes['embedUrl'] ?? '');
        $embed_html = (string)($attributes['embedHtml'] ?? '');
        $height = preg_replace('/[^0-9.%a-zA-Z-]/', '', (string)($attributes['height'] ?? '420px'));
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-custom-embed']);
        $body = '';
        if ($embed_html !== '') {
            $body = '<div class="wpbb-custom-embed__html">' . wp_kses($embed_html, ['iframe' => ['src'=>true,'width'=>true,'height'=>true,'style'=>true,'frameborder'=>true,'allow'=>true,'allowfullscreen'=>true,'loading'=>true,'referrerpolicy'=>true], 'div'=>['class'=>true,'style'=>true]]) . '</div>';
        } elseif ($embed_url !== '') {
            $body = '<iframe class="wpbb-custom-embed__frame" src="' . $embed_url . '" style="min-height:' . esc_attr($height) . '" loading="lazy"></iframe>';
        } else {
            $body = '<div class="wpbb-custom-embed__placeholder">Add embed URL or HTML.</div>';
        }
        return '<div ' . $wrapper . '>' . ($title ? '<h3>' . $title . '</h3>' : '') . $body . '</div>';
    }

    public function render_ai_content_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'AI Content');
        $provider = esc_html($attributes['provider'] ?? 'simple-ai');
        $generated = wp_kses_post($attributes['generatedText'] ?? '');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-ai-content']);
        return '<div ' . $wrapper . '>' . ($title ? '<h3>' . $title . '</h3>' : '') . '<div class="wpbb-ai-content__meta">Mode: ' . $provider . '</div><div class="wpbb-ai-content__help">Use keywords or a short description in the editor, then click Generate text now.</div><div class="wpbb-ai-content__body">' . ($generated ?: 'No generated content yet.') . '</div></div>';
    }

    public function render_login_register_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Account Access');
        $show_register = !empty($attributes['showRegister']);
        $variant = sanitize_html_class($attributes['styleVariant'] ?? 'split');
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-auth wpbb-auth--' . $variant]);

        $login_form = wp_login_form([
            'echo' => false,
            'remember' => true,
            'label_username' => __('Email or Username', 'wp-bbuilder'),
            'label_password' => __('Password', 'wp-bbuilder'),
            'label_log_in'   => __('Log In', 'wp-bbuilder')
        ]);

        $register_html = '';
        if ($show_register) {
            if (get_option('users_can_register')) {
                $register_html = '<div class="wpbb-auth-card p-3"><h4>' . esc_html__('Register', 'wp-bbuilder') . '</h4><p>' . esc_html__('Create an account on the default WordPress registration page.', 'wp-bbuilder') . '</p><a class="btn btn-primary" href="' . esc_url(wp_registration_url()) . '">' . esc_html__('Register', 'wp-bbuilder') . '</a></div>';
            } else {
                $register_html = '<div class="wpbb-auth-card p-3"><h4>' . esc_html__('Register', 'wp-bbuilder') . '</h4><p>' . esc_html__('User registration is currently disabled.', 'wp-bbuilder') . '</p></div>';
            }
        }

        return '<div ' . $wrapper . '>' .
            ($title ? '<h3>' . $title . '</h3>' : '') .
            '<div class="row g-3">' .
                '<div class="' . esc_attr($show_register ? 'col-md-6' : 'col-12') . '">' .
                    '<div class="wpbb-auth-card p-3"><h4>' . esc_html__('Login', 'wp-bbuilder') . '</h4>' . $login_form . '</div>' .
                '</div>' .
                ($show_register ? '<div class="col-md-6">' . $register_html . '</div>' : '') .
            '</div>' .
        '</div>';
    }

    public function render_button_block($attributes, $content, $block) {
        $text = !empty($attributes['text']) ? wp_kses_post($attributes['text']) : 'Button';
        $url = !empty($attributes['url']) ? esc_url($attributes['url']) : '#';
        $variant = sanitize_html_class($attributes['variant'] ?? 'primary');
        $size = sanitize_html_class($attributes['size'] ?? '');
        $btn_class = trim((string)($attributes['btnClass'] ?? ''));
        if ($btn_class === '') {
            $btn_class = 'btn btn-' . $variant . ($size ? ' btn-' . $size : '') . (!empty($attributes['fullWidth']) ? ' w-100' : '');
        }
        $wrap_class = 'wpbb-button-wrap';
        if (!empty($attributes['fullWidth'])) $wrap_class .= ' w-100';
        $align = sanitize_html_class($attributes['align'] ?? '');
        if ($align) $wrap_class .= ' text-' . $align;

        $style = '';
        if (!empty($attributes['backgroundColor'])) {
            $bg = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']);
            $style .= 'background:' . $bg . ';border-color:' . $bg . ';';
        }
        if (!empty($attributes['textColor'])) {
            $style .= 'color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['textColor']) . ';';
        }
        if (!empty($attributes['borderRadius'])) {
            $style .= 'border-radius:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['borderRadius']) . ';';
        }

        $wrapper = get_block_wrapper_attributes(['class' => $wrap_class]);
        return '<div ' . $wrapper . '><a class="' . esc_attr($btn_class) . '" href="' . $url . '" style="' . esc_attr($style) . '">' . $text . '</a></div>';
    }

    public function render_accordion_block($attributes, $content, $block) {
        if (wpbb_get_option('load_bootstrap_js', 0) || wpbb_get_option('force_bootstrap_enqueue', 0)) {
            wp_enqueue_script('wpbb-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
        }
        $uid = !empty($attributes['anchor']) ? sanitize_html_class((string)$attributes['anchor']) : sanitize_html_class('wpbb-accordion-' . wp_unique_id());
        $flush = !empty($attributes['flush']) ? ' accordion-flush' : '';
        $shadow = !empty($attributes['boxShadowClass']) ? ' ' . sanitize_html_class((string)$attributes['boxShadowClass']) : '';
        $style = '';
        if (!empty($attributes['backgroundColor'])) $style .= 'background:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['backgroundColor']) . ';';
        if (!empty($attributes['borderColor'])) $style .= 'border-color:' . preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)$attributes['borderColor']) . ';';
        $wrapper = get_block_wrapper_attributes(['class' => 'accordion wpbb-accordion' . $flush . $shadow, 'id' => $uid, 'style' => $style]);
        return '<div ' . $wrapper . '>' . $content . '</div>';
    }

    public function render_accordion_item_block($attributes, $content, $block) {
        $title = esc_html($attributes['title'] ?? 'Accordion Item');
        $item_id = sanitize_html_class('wpbb-acc-item-' . wp_unique_id());
        $head_id = $item_id . '-head';
        $collapse_id = $item_id . '-collapse';
        return '<div class="accordion-item wpbb-accordion-item">'
            . '<h2 class="accordion-header" id="' . esc_attr($head_id) . '">'
            . '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#' . esc_attr($collapse_id) . '" aria-expanded="false" aria-controls="' . esc_attr($collapse_id) . '">'
            . $title
            . '</button></h2>'
            . '<div id="' . esc_attr($collapse_id) . '" class="accordion-collapse collapse">'
            . '<div class="accordion-body">' . $content . '</div></div></div>';
    }




    private function wpbb_normalize_post_type($value, $fallback = 'post') {
        $value = sanitize_key((string) $value);
        return $value !== '' ? $value : $fallback;
    }

    private function wpbb_resolve_existing_post_type($preferred, $fallbacks = []) {
        $candidates = array_merge([(string) $preferred], $fallbacks);
        foreach ($candidates as $candidate) {
            $candidate = sanitize_key((string) $candidate);
            if ($candidate !== '' && post_type_exists($candidate)) return $candidate;
        }
        return 'post';
    }

    private function wpbb_render_post_card($post_id, $item_class = '') {
        $item_class = trim($item_class ?: 'col-md-4');
        $thumb = get_the_post_thumbnail($post_id, 'medium', ['class' => 'card-img-top']);
        $permalink = get_permalink($post_id);
        $title = get_the_title($post_id);
        $excerpt = wp_trim_words(wp_strip_all_tags(get_the_excerpt($post_id) ?: get_post_field('post_content', $post_id)), 22);
        return '<article class="' . esc_attr($item_class) . ' wpbb-load-more-item"><div class="card h-100">' .
            ($thumb ? '<a href="' . esc_url($permalink) . '">' . $thumb . '</a>' : '') .
            '<div class="card-body"><h3 class="h5 card-title"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h3><p class="card-text">' . esc_html($excerpt) . '</p></div></div></article>';
    }

    public function ajax_load_more() {
        $post_type = $this->wpbb_resolve_existing_post_type($_POST['postType'] ?? 'post', ['post']);
        $page = max(1, intval($_POST['page'] ?? 1));
        $per_page = max(1, intval($_POST['perPage'] ?? 3));
        $category = sanitize_text_field($_POST['category'] ?? '');
        $query_args = ['post_type' => $post_type, 'post_status' => 'publish', 'paged' => $page, 'posts_per_page' => $per_page];
        if ($category !== '' && taxonomy_exists('category')) {
            $query_args['category_name'] = $category;
        }
        $query = new WP_Query($query_args);
        $item_class = sanitize_text_field($_POST['itemClass'] ?? 'col-md-4');
        $html = '';
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post(); $html .= $this->wpbb_render_post_card(get_the_ID(), $item_class); }
            wp_reset_postdata();
        }
        wp_send_json_success(['html' => $html, 'max' => intval($query->max_num_pages)]);
    }

    public function ajax_blog_filter() {
        $post_type = $this->wpbb_resolve_existing_post_type($_POST['postType'] ?? 'post', ['post']);
        $taxonomy = sanitize_key($_POST['taxonomy'] ?? 'category');
        $per_page = max(1, intval($_POST['perPage'] ?? 6));
        $search = sanitize_text_field($_POST['search'] ?? '');
        $category = sanitize_text_field($_POST['category'] ?? '');
        $year = intval($_POST['year'] ?? 0);
        $sort = sanitize_text_field($_POST['sort'] ?? 'date_desc');
        $args = ['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $per_page, 's' => $search];
        if ($category !== '' && taxonomy_exists($taxonomy)) {
            $args['tax_query'] = [[ 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $category ]];
        }
        if ($year > 0) {
            $args['date_query'] = [[ 'year' => $year ]];
        }
        switch ($sort) {
            case 'date_asc': $args['orderby'] = 'date'; $args['order'] = 'ASC'; break;
            case 'alpha_asc': $args['orderby'] = 'title'; $args['order'] = 'ASC'; break;
            case 'alpha_desc': $args['orderby'] = 'title'; $args['order'] = 'DESC'; break;
            default: $args['orderby'] = 'date'; $args['order'] = 'DESC';
        }
        $query = new WP_Query($args);
        $html = '<div class="row g-4">';
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post(); $html .= $this->wpbb_render_post_card(get_the_ID(), 'col-md-6 col-lg-4'); }
            wp_reset_postdata();
        } else {
            $html .= '<div class="col-12"><p>No posts found.</p></div>';
        }
        $html .= '</div>';
        wp_send_json_success(['html' => $html]);
    }

    public function render_load_more_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-content-filters');
        wp_enqueue_style('wpbb-shared');
        $post_type = $this->wpbb_resolve_existing_post_type($attributes['queryPostType'] ?? 'post', ['post']);
        $visible = max(1, intval($attributes['visibleItems'] ?? 6));
        $load = max(1, intval($attributes['loadItems'] ?? 3));
        $parent_class = trim((string)($attributes['parentClass'] ?? 'row'));
        $item_class = trim((string)($attributes['itemClass'] ?? 'col-md-4'));
        $button_class = trim((string)($attributes['buttonClass'] ?? 'btn btn-primary'));
        $button_text_raw = trim((string)($attributes['buttonText'] ?? 'Load more'));
        $button_text = esc_html($button_text_raw !== '' ? $button_text_raw : 'Load more');
        $button_color = trim((string)($attributes['buttonColor'] ?? ''));
        $category = sanitize_title($attributes['queryCategory'] ?? '');
        $style = $button_color !== '' ? 'background:' . esc_attr($button_color) . ';border-color:' . esc_attr($button_color) . ';color:#ffffff;' : 'background:#2563eb;border-color:#2563eb;color:#ffffff;';
        $query_args = ['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $visible, 'paged' => 1];
        if ($category !== '' && taxonomy_exists('category')) {
            $query_args['category_name'] = $category;
        }
        $query = new WP_Query($query_args);
        $html = '<div ' . get_block_wrapper_attributes(['class' => 'wpbb-load-more']) . '><div class="' . esc_attr($parent_class !== '' ? $parent_class : 'row') . '" data-wpbb-load-more-results>';
        if ($query->have_posts()) { while ($query->have_posts()) { $query->the_post(); $html .= $this->wpbb_render_post_card(get_the_ID(), $item_class); } wp_reset_postdata(); }
        $html .= '</div>';
        if (intval($query->found_posts) > $visible) {
            $html .= '<div class="text-center mt-4 wpbb-load-more__actions"><button type="button" class="' . esc_attr($button_class) . '" style="' . esc_attr($style) . '" data-wpbb-load-more-btn data-post-type="' . esc_attr($post_type) . '" data-category="' . esc_attr($category) . '" data-page="1" data-per-page="' . esc_attr($load) . '" data-item-class="' . esc_attr($item_class) . '" data-max="' . esc_attr($query->max_num_pages) . '">' . $button_text . '</button></div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function render_contact_links_block($attributes, $content, $block) {
        $email = sanitize_email($attributes['email'] ?? '');
        $phone = sanitize_text_field($attributes['phone'] ?? '');
        $icon_color = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['iconColor'] ?? ''));
        $link_color = preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', (string)($attributes['linkColor'] ?? ''));
        $layout = trim((string)($attributes['layoutClass'] ?? 'd-flex flex-column gap-2'));
        $style = $link_color ? 'color:' . $link_color . ';' : '';
        $icon_style = $icon_color ? 'style="color:' . esc_attr($icon_color) . '"' : '';
        $html = '<div ' . get_block_wrapper_attributes(['class' => 'wpbb-contact-links ' . $layout]) . '>';
        if ($phone !== '') {
            $html .= '<a class="wpbb-contact-links__item" href="tel:' . esc_attr(preg_replace('/[^0-9\+]/', '', $phone)) . '" style="' . esc_attr($style) . '"><span class="wpbb-contact-links__icon" ' . $icon_style . '>' . $this->wpbb_svg_icon($attributes['phoneIcon'] ?? 'whatsapp') . '</span><span>' . esc_html($phone) . '</span></a>';
        }
        if ($email !== '') {
            $html .= '<a class="wpbb-contact-links__item" href="mailto:' . esc_attr($email) . '" style="' . esc_attr($style) . '"><span class="wpbb-contact-links__icon" ' . $icon_style . '>' . $this->wpbb_svg_icon($attributes['emailIcon'] ?? 'email') . '</span><span>' . esc_html($email) . '</span></a>';
        }
        $html .= '</div>';
        return $html;
    }

    public function render_events_block($attributes, $content, $block) {
        $post_type = $this->wpbb_resolve_existing_post_type($attributes['postType'] ?? 'event', ['event','events','calendar']);
        $taxonomy = sanitize_key($attributes['taxonomy'] ?? 'event_category');
        $posts_to_show = max(1, intval($attributes['postsToShow'] ?? 6));
        $today = current_time('Ymd');
        $args = ['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $posts_to_show, 'orderby' => 'meta_value', 'meta_key' => 'event_date', 'order' => 'ASC'];
        $query = new WP_Query($args);
        $terms = taxonomy_exists($taxonomy) ? get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]) : [];
        $html = '<div ' . get_block_wrapper_attributes(['class' => 'wpbb-events']) . '>';
        $html .= '<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><h3 class="mb-0">' . esc_html($attributes['title'] ?? 'Events') . '</h3>';
        if (!is_wp_error($terms) && !empty($terms)) { $html .= '<div class="wpbb-events__filters">'; foreach ($terms as $term) { $html .= '<a class="btn btn-outline-secondary btn-sm me-2 mb-2" href="' . esc_url(add_query_arg('event_category', $term->slug)) . '">' . esc_html($term->name) . '</a>'; } $html .= '</div>'; }
        $html .= '</div>';
        if (!empty($attributes['showCalendar'])) {
            $html .= '<div class="wpbb-events__calendar card mb-4"><div class="card-body"><div class="wpbb-events__calendar-grid">';
            for ($d = 1; $d <= 31; $d++) { $html .= '<span class="wpbb-events__calendar-day' . (intval(wp_date('j')) === $d ? ' is-today' : '') . '">' . $d . '</span>'; }
            $html .= '</div></div></div>';
        }
        $html .= '<div class="row g-4">';
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post(); $event_date = get_post_meta(get_the_ID(), 'event_date', true); $display_date = $event_date ? date_i18n(get_option('date_format'), strtotime($event_date)) : get_the_date('', get_the_ID()); $html .= '<article class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><div class="text-muted small mb-2">' . esc_html($display_date) . '</div><h3 class="h5"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3><p>' . esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt() ?: get_the_content()), 20)) . '</p></div></div></article>'; }
            wp_reset_postdata();
        } else {
            $html .= '<div class="col-12"><p>No events found.</p></div>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function render_testimonials_block($attributes, $content, $block) {
        wp_enqueue_style('wpbb-swiper'); wp_enqueue_script('wpbb-swiper'); wp_enqueue_script('wpbb-swiper-init');
        $post_type = $this->wpbb_resolve_existing_post_type($attributes['postType'] ?? 'testimonial', ['testimonial','testimonials']);
        $posts_to_show = max(1, intval($attributes['postsToShow'] ?? 9));
        $query = new WP_Query(['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $posts_to_show]);
        $wrapper = get_block_wrapper_attributes(['class' => 'wpbb-testimonials', 'data-swiper' => '1', 'data-slides' => (string) intval($attributes['slidesDesktop'] ?? 3), 'data-slides-tablet' => (string) intval($attributes['slidesTablet'] ?? 2), 'data-slides-mobile' => (string) intval($attributes['slidesMobile'] ?? 1), 'data-space' => '24']);
        $html = '<div ' . $wrapper . '><h3 class="mb-4">' . esc_html($attributes['title'] ?? 'Testimonials') . '</h3><div class="swiper"><div class="swiper-wrapper">';
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post(); $role = get_post_meta(get_the_ID(), 'position', true) ?: get_post_meta(get_the_ID(), 'role', true); $html .= '<div class="swiper-slide"><div class="card h-100"><div class="card-body"><blockquote class="mb-3">“' . esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 40)) . '”</blockquote><div class="fw-semibold">' . esc_html(get_the_title()) . '</div>' . ($role ? '<div class="text-muted small">' . esc_html($role) . '</div>' : '') . '</div></div></div>'; }
            wp_reset_postdata();
        }
        $html .= '</div>' . (!empty($attributes['showPagination']) ? '<div class="swiper-pagination"></div>' : '') . (!empty($attributes['showNavigation']) ? '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>' : '') . '</div></div>';
        return $html;
    }

    public function render_blog_filter_block($attributes, $content, $block) {
        wp_enqueue_script('wpbb-content-filters');
        $post_type = $this->wpbb_resolve_existing_post_type($attributes['postType'] ?? 'post', ['post']);
        $taxonomy = sanitize_key($attributes['taxonomy'] ?? 'category');
        $posts_to_show = max(1, intval($attributes['postsToShow'] ?? 6));
        $terms = taxonomy_exists($taxonomy) ? get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]) : [];
        $years = get_posts(['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']);
        $year_values = [];
        foreach ($years as $id) { $year_values[] = get_the_date('Y', $id); }
        $year_values = array_values(array_unique(array_filter($year_values)));
        rsort($year_values);
        $query = new WP_Query(['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $posts_to_show]);
        $html = '<div ' . get_block_wrapper_attributes(['class' => 'wpbb-blog-filter']) . '><div class="d-flex flex-wrap gap-3 align-items-end mb-4">';
        $html .= '<div><label class="form-label">Search</label><input type="search" class="form-control" data-wpbb-blog-search placeholder="Search posts"></div>';
        $html .= '<div><label class="form-label">Category</label><select class="form-select" data-wpbb-blog-category><option value="">All</option>';
        if (!is_wp_error($terms)) foreach ($terms as $term) { $html .= '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>'; }
        $html .= '</select></div>';
        $html .= '<div><label class="form-label">Year</label><select class="form-select" data-wpbb-blog-year><option value="">All</option>';
        foreach ($year_values as $year) { $html .= '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>'; }
        $html .= '</select></div>';
        $html .= '<div><label class="form-label">Sort</label><select class="form-select" data-wpbb-blog-sort><option value="date_desc">Newest</option><option value="date_asc">Oldest</option><option value="alpha_asc">A-Z</option><option value="alpha_desc">Z-A</option></select></div>';
        $button_color = trim((string)($attributes['buttonColor'] ?? '#2563eb'));
        $button_style = $button_color !== '' ? ' style="background:' . esc_attr(preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', $button_color)) . ';border-color:' . esc_attr(preg_replace('/[^#(),.% 0-9a-zA-Z-]/', '', $button_color)) . ';"' : '';
        $html .= '<div><button type="button" class="btn btn-primary" data-wpbb-blog-submit' . $button_style . '>' . esc_html($attributes['buttonText'] ?? 'Filter') . '</button></div></div>';
        $html .= '<div data-wpbb-blog-results data-post-type="' . esc_attr($post_type) . '" data-taxonomy="' . esc_attr($taxonomy) . '" data-per-page="' . esc_attr($posts_to_show) . '"><div class="row g-4">';
        if ($query->have_posts()) { while ($query->have_posts()) { $query->the_post(); $html .= $this->wpbb_render_post_card(get_the_ID(), 'col-md-6 col-lg-4'); } wp_reset_postdata(); } else { $html .= '<div class="col-12"><p>No posts found.</p></div>'; }
        $html .= '</div></div></div>';
        return $html;
    }


    public function register_rest_routes() {
        register_rest_route('wpbb/v1', '/varda-dienas', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'rest_get_varda_dienas'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'description' => __('Date in YYYY-MM-DD or MM-DD format.', 'wp-bbuilder'),
                    'required' => false,
                    'type' => 'string',
                ],
            ],
        ]);

        register_rest_route('wpbb/v1', '/varda-dienas/today', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'rest_get_varda_dienas_today'],
            'permission_callback' => '__return_true',
        ]);
    }

    private function load_varda_dienas_data() {
        $file = WPBB_PLUGIN_DIR . 'assets/json/varda-dienas.json';
        if (!file_exists($file)) {
            return new WP_Error('wpbb_varda_dienas_missing', __('Vārda dienu data file not found.', 'wp-bbuilder'), ['status' => 500]);
        }

        $json = file_get_contents($file);
        $data = json_decode((string) $json, true);

        if (!is_array($data)) {
            return new WP_Error('wpbb_varda_dienas_invalid', __('Invalid vārda dienu data file.', 'wp-bbuilder'), ['status' => 500]);
        }

        return $data;
    }

    private function normalize_varda_dienas_key($raw_date = '') {
        $raw_date = trim((string) $raw_date);

        if ($raw_date === '') {
            $now = new DateTimeImmutable('now', wp_timezone());
            return $now->format('m-d');
        }

        if (preg_match('/^(\d{2})-(\d{2})$/', $raw_date, $m)) {
            return $m[1] . '-' . $m[2];
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $raw_date, $m)) {
            return $m[2] . '-' . $m[3];
        }

        return new WP_Error('wpbb_varda_dienas_bad_date', __('Invalid date format. Use YYYY-MM-DD or MM-DD.', 'wp-bbuilder'), ['status' => 400]);
    }

    public function rest_get_varda_dienas(WP_REST_Request $request) {
        $data = $this->load_varda_dienas_data();
        if (is_wp_error($data)) {
            return $data;
        }

        $requested = $request->get_param('date');
        $key = $this->normalize_varda_dienas_key($requested);
        if (is_wp_error($key)) {
            return $key;
        }

        $now = new DateTimeImmutable('now', wp_timezone());
        $today_key = $now->format('m-d');

        return rest_ensure_response([
            'success' => true,
            'date' => $requested ? (string) $requested : $now->format('Y-m-d'),
            'key' => $key,
            'today' => $key === $today_key,
            'names' => isset($data[$key]) && is_array($data[$key]) ? array_values($data[$key]) : [],
            'count' => isset($data[$key]) && is_array($data[$key]) ? count($data[$key]) : 0,
        ]);
    }

    public function rest_get_varda_dienas_today(WP_REST_Request $request) {
        $request->set_param('date', '');
        return $this->rest_get_varda_dienas($request);
    }

}
