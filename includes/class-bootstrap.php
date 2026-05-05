<?php
if (!defined('ABSPATH')) exit;

final class WPBBuilder_Bootstrap {
    private static $components = [];
    private static $registered = false;
    private static $detected = null;
    private static $debug_report = null;

    public static function needs($components) {
        foreach ((array) $components as $component) {
            $component = sanitize_key($component);
            if ($component !== '') {
                self::$components[$component] = true;
            }
        }
    }

    public static function get_version() {
        return '5.3.8';
    }

    public static function get_css_component_choices() {
        return [
            'buttons' => __('Buttons', 'wp-bbuilder'),
            'forms' => __('Forms', 'wp-bbuilder'),
            'nav' => __('Nav', 'wp-bbuilder'),
            'navbar' => __('Navbar', 'wp-bbuilder'),
            'transitions' => __('Transitions', 'wp-bbuilder'),
            'accordion' => __('Accordion', 'wp-bbuilder'),
            'dropdown' => __('Dropdown', 'wp-bbuilder'),
            'card' => __('Card', 'wp-bbuilder'),
            'button-group' => __('Button group', 'wp-bbuilder'),
            'modal' => __('Modal', 'wp-bbuilder'),
            'offcanvas' => __('Offcanvas', 'wp-bbuilder'),
            'breadcrumb' => __('Breadcrumb', 'wp-bbuilder'),
            'pagination' => __('Pagination', 'wp-bbuilder'),
            'badge' => __('Badge', 'wp-bbuilder'),
            'alert' => __('Alert', 'wp-bbuilder'),
            'progress' => __('Progress', 'wp-bbuilder'),
            'list-group' => __('List group', 'wp-bbuilder'),
            'close' => __('Close button', 'wp-bbuilder'),
            'toasts' => __('Toasts', 'wp-bbuilder'),
            'tooltip' => __('Tooltip', 'wp-bbuilder'),
            'popover' => __('Popover', 'wp-bbuilder'),
            'carousel' => __('Carousel', 'wp-bbuilder'),
            'spinners' => __('Spinners', 'wp-bbuilder'),
            'placeholders' => __('Placeholders', 'wp-bbuilder'),
            'images' => __('Images', 'wp-bbuilder'),
            'tables' => __('Tables', 'wp-bbuilder'),
            'helpers' => __('Helpers', 'wp-bbuilder'),
        ];
    }

    public static function get_js_component_choices() {
        return [
            'collapse' => __('Collapse (accordion / navbar toggle)', 'wp-bbuilder'),
            'dropdown' => __('Dropdown', 'wp-bbuilder'),
            'tab' => __('Tab', 'wp-bbuilder'),
            'modal' => __('Modal', 'wp-bbuilder'),
            'offcanvas' => __('Offcanvas', 'wp-bbuilder'),
            'tooltip' => __('Tooltip', 'wp-bbuilder'),
            'popover' => __('Popover', 'wp-bbuilder'),
            'toast' => __('Toast', 'wp-bbuilder'),
            'carousel' => __('Carousel', 'wp-bbuilder'),
            'alert' => __('Alert', 'wp-bbuilder'),
            'button' => __('Button', 'wp-bbuilder'),
            'scrollspy' => __('ScrollSpy', 'wp-bbuilder'),
        ];
    }

    private static function css_handle($slug) {
        return 'wpbb-bootstrap-css-' . sanitize_key($slug);
    }

    private static function js_handle($slug) {
        return 'wpbb-bootstrap-js-' . sanitize_key($slug);
    }

    private static function get_css_dependencies($slug) {
        $deps = ['wpbb-bootstrap-core'];
        if ($slug === 'accordion' || $slug === 'modal' || $slug === 'offcanvas') {
            $deps[] = self::css_handle('transitions');
        }
        if ($slug === 'navbar') {
            $deps[] = self::css_handle('nav');
        }
        if ($slug === 'popover') {
            $deps[] = self::css_handle('tooltip');
        }
        if ($slug === 'toasts' || $slug === 'alert') {
            $deps[] = self::css_handle('close');
        }
        return array_values(array_unique($deps));
    }

    private static function asset_url($path) {
        return WPBB_PLUGIN_URL . ltrim($path, '/');
    }

    private static function merge_unique_lists(...$lists) {
        $merged = [];
        foreach ($lists as $list) {
            foreach ((array) $list as $item) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $merged[$item] = true;
                }
            }
        }
        $items = array_keys($merged);
        natcasesort($items);
        return array_values($items);
    }

    private static function get_block_asset_map() {
        $map = [
            'accordion' => ['label' => __('Accordion', 'wp-bbuilder'), 'css' => ['transitions', 'accordion'], 'js' => ['collapse']],
            'accordion-item' => ['label' => __('Accordion item', 'wp-bbuilder'), 'css' => ['transitions', 'accordion'], 'js' => ['collapse']],
            'alert' => ['label' => __('Alert', 'wp-bbuilder'), 'css' => ['close', 'alert'], 'js' => ['alert']],
            'badge' => ['label' => __('Badge', 'wp-bbuilder'), 'css' => ['badge']],
            'breadcrumb' => ['label' => __('Breadcrumb', 'wp-bbuilder'), 'css' => ['breadcrumb']],
            'button' => ['label' => __('Button', 'wp-bbuilder'), 'css' => ['buttons']],
            'card' => ['label' => __('Card', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'cards' => ['label' => __('Cards', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'catalogue' => ['label' => __('Catalogue', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'contact-links' => ['label' => __('Contact links', 'wp-bbuilder'), 'css' => ['buttons']],
            'cta-card' => ['label' => __('CTA card', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'cta-section' => ['label' => __('CTA section', 'wp-bbuilder'), 'css' => ['buttons']],
            'custom-embed' => ['label' => __('Custom embed', 'wp-bbuilder'), 'css' => ['buttons']],
            'dynamic-form' => ['label' => __('Dynamic form', 'wp-bbuilder'), 'css' => ['forms', 'buttons']],
            'events' => ['label' => __('Events', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'file' => ['label' => __('File', 'wp-bbuilder'), 'css' => ['buttons']],
            'list-group' => ['label' => __('List group', 'wp-bbuilder'), 'css' => ['list-group']],
            'load-more' => ['label' => __('Load more', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'login-register' => ['label' => __('Login / register', 'wp-bbuilder'), 'css' => ['forms', 'buttons']],
            'mailchimp' => ['label' => __('Mailchimp', 'wp-bbuilder'), 'css' => ['card', 'forms', 'buttons']],
            'menu-option' => ['label' => __('Menu option', 'wp-bbuilder'), 'css' => ['badge']],
            'navbar' => ['label' => __('Navbar', 'wp-bbuilder'), 'css' => ['buttons', 'nav', 'navbar'], 'js' => ['collapse']],
            'pricecards' => ['label' => __('Price cards', 'wp-bbuilder'), 'css' => ['card', 'buttons']],
            'progress' => ['label' => __('Progress', 'wp-bbuilder'), 'css' => ['progress']],
            'soc-follow-block' => ['label' => __('Social follow', 'wp-bbuilder'), 'css' => ['buttons']],
            'soc-share' => ['label' => __('Social share', 'wp-bbuilder'), 'css' => ['buttons']],
            'spinner' => ['label' => __('Spinner', 'wp-bbuilder'), 'css' => ['spinners']],
            'table' => ['label' => __('Table', 'wp-bbuilder'), 'css' => ['tables']],
            'tabs' => ['label' => __('Tabs', 'wp-bbuilder'), 'css' => ['nav'], 'js' => ['tab']],
            'tab-item' => ['label' => __('Tab item', 'wp-bbuilder'), 'css' => ['nav'], 'js' => ['tab']],
            'testimonials' => ['label' => __('Testimonials', 'wp-bbuilder'), 'css' => ['card']],
            'weather' => ['label' => __('Weather', 'wp-bbuilder'), 'css' => ['card']],
            'booking-calendar' => ['label' => __('Booking calendar', 'wp-bbuilder'), 'css' => ['card', 'forms', 'buttons']],
            'blog-filter' => ['label' => __('Blog filter', 'wp-bbuilder'), 'css' => ['card', 'forms', 'buttons']],
        ];

        $map = apply_filters('wpbb_bootstrap_block_asset_map', $map);
        return is_array($map) ? $map : [];
    }

    private static function normalize_block_lookup_keys($block_name) {
        $block_name = (string) $block_name;
        $keys = [];
        if ($block_name === '') {
            return $keys;
        }
        $keys[] = $block_name;
        if (strpos($block_name, 'wpbb/') === 0) {
            $keys[] = substr($block_name, 5);
        }
        return array_values(array_unique(array_filter($keys)));
    }

    private static function get_block_asset_entry($block_name) {
        $map = self::get_block_asset_map();
        foreach (self::normalize_block_lookup_keys($block_name) as $key) {
            if (!empty($map[$key]) && is_array($map[$key])) {
                $entry = $map[$key];
                $entry['css'] = self::sanitize_components($entry['css'] ?? [], self::get_css_component_choices());
                $entry['js'] = self::sanitize_components($entry['js'] ?? [], self::get_js_component_choices());
                $entry['label'] = trim((string) ($entry['label'] ?? $key));
                $entry['key'] = $key;
                return $entry;
            }
        }
        return null;
    }

    private static function format_block_labels($block_keys) {
        $map = self::get_block_asset_map();
        $out = [];
        foreach ((array) $block_keys as $key) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }
            if (!empty($map[$key]['label'])) {
                $out[] = (string) $map[$key]['label'];
            } else {
                $label_key = strpos($key, 'wpbb/') === 0 ? substr($key, 5) : $key;
                $out[] = ucwords(str_replace(['-', '_', '/'], ' ', $label_key));
            }
        }
        natcasesort($out);
        return array_values(array_unique($out));
    }

    public static function get_block_mapping_report() {
        $detected = self::detect_components();
        return [
            'engine' => 'per-block-map',
            'mapped_blocks' => $detected['blocks'],
            'mapped_css' => $detected['css'],
            'mapped_js' => $detected['js'],
        ];
    }

    public static function record_rendered_block_html($html, $block_name = '') {
        return $html;
    }

    public static function persist_render_detection_cache() {
        return;
    }

    public static function clear_post_detection_cache($post_id) {
        return;
    }

    public static function register_assets() {
        if (self::$registered) return;

        $version = self::get_version();
        $base = 'assets/bootstrap/5.3.8/';

        wp_register_style('wpbb-critical', false, [], WPBB_VERSION);
        wp_register_style('wpbb-bootstrap-full', self::asset_url($base . 'css/bootstrap-full.min.css'), [], $version);
        wp_register_style('wpbb-bootstrap-core', self::asset_url($base . 'css/bootstrap-core.min.css'), [], $version);
        wp_register_style('wpbb-bootstrap-grid', self::asset_url($base . 'css/legacy/bootstrap-grid.min.css'), [], $version);
        wp_register_style('wpbb-bootstrap-utilities', self::asset_url($base . 'css/legacy/bootstrap-utilities.min.css'), [], $version);
        wp_register_style('wpbb-bootstrap-reboot', self::asset_url($base . 'css/legacy/bootstrap-reboot.min.css'), [], $version);

        foreach (array_keys(self::get_css_component_choices()) as $slug) {
            wp_register_style(
                self::css_handle($slug),
                self::asset_url($base . 'css/components/' . $slug . '.min.css'),
                self::get_css_dependencies($slug),
                $version
            );
        }

        wp_register_script('wpbb-bootstrap-bundle', self::asset_url($base . 'js/bootstrap-full.min.js'), [], $version, true);
        foreach (array_keys(self::get_js_component_choices()) as $slug) {
            wp_register_script(
                self::js_handle($slug),
                self::asset_url($base . 'js/components/' . $slug . '.min.js'),
                [],
                $version,
                true
            );
        }

        self::$registered = true;
    }

    public static function enqueue_critical() {
        self::register_assets();
        wp_enqueue_style('wpbb-critical');
        $critical_path = WPBB_PLUGIN_DIR . 'assets/css/bb-critical.css';
        if (file_exists($critical_path)) {
            $critical = trim((string) file_get_contents($critical_path));
            if ($critical !== '') {
                wp_add_inline_style('wpbb-critical', $critical);
            }
        }
    }

    private static function sanitize_components($components, $allowed) {
        $allowed = array_keys($allowed);
        $out = [];
        foreach ((array) $components as $component) {
            $component = sanitize_key($component);
            if (in_array($component, $allowed, true)) {
                $out[] = $component;
            }
        }
        return array_values(array_unique($out));
    }

    private static function get_selected_css_components() {
        return self::sanitize_components(
            wpbb_get_option('bootstrap_css_components', []),
            self::get_css_component_choices()
        );
    }

    private static function get_selected_js_components() {
        return self::sanitize_components(
            wpbb_get_option('bootstrap_js_components', []),
            self::get_js_component_choices()
        );
    }

    private static function collect_detected_components_from_blocks($blocks, &$css, &$js, &$block_keys) {
        foreach ((array) $blocks as $block) {
            $name = isset($block['blockName']) ? (string) $block['blockName'] : '';
            $entry = self::get_block_asset_entry($name);
            if ($entry) {
                $block_keys[$entry['key']] = true;
                foreach ($entry['css'] as $component) {
                    $css[$component] = true;
                }
                foreach ($entry['js'] as $component) {
                    $js[$component] = true;
                }
            }
            if (!empty($block['innerBlocks'])) {
                self::collect_detected_components_from_blocks($block['innerBlocks'], $css, $js, $block_keys);
            }
        }
    }

    private static function detect_components() {
        $detected = ['css' => [], 'js' => [], 'blocks' => []];
        if (!is_singular()) {
            self::$detected = $detected;
            return $detected;
        }

        if (self::$detected === null) {
            global $post;
            $css = [];
            $js = [];
            $block_keys = [];

            if ($post && !empty($post->post_content)) {
                self::collect_detected_components_from_blocks(parse_blocks((string) $post->post_content), $css, $js, $block_keys);
            }

            self::$detected = [
                'css' => self::merge_unique_lists(array_keys($css)),
                'js' => self::merge_unique_lists(array_keys($js)),
                'blocks' => self::format_block_labels(array_keys($block_keys)),
            ];
        }

        return self::$detected;
    }

    private static function ensure_debug_report() {
        if (self::$debug_report === null) {
            self::$debug_report = [
                'css_mode' => wpbb_get_option('bootstrap_css_mode', 'auto'),
                'js_mode' => wpbb_get_option('bootstrap_js_mode', 'auto'),
                'load_bootstrap_css' => !empty(wpbb_get_option('load_bootstrap_css', 1)),
                'load_bootstrap_js' => !empty(wpbb_get_option('load_bootstrap_js', 0)),
                'force_bootstrap_enqueue' => !empty(wpbb_get_option('force_bootstrap_enqueue', 0)),
                'detected' => self::detect_components(),
                'mapping' => self::get_block_mapping_report(),
                'selected_css' => self::get_selected_css_components(),
                'selected_js' => self::get_selected_js_components(),
                'runtime_js' => array_keys(self::$components),
                'final_css' => [],
                'final_js' => [],
            ];
        }
        return self::$debug_report;
    }

    private static function set_debug($key, $value) {
        self::ensure_debug_report();
        self::$debug_report[$key] = $value;
    }

    private static function enqueue_css_components($components) {
        wp_enqueue_style('wpbb-bootstrap-core');
        foreach ($components as $component) {
            wp_enqueue_style(self::css_handle($component));
        }
        return array_values(array_unique(array_merge(['core'], $components)));
    }

    public static function enqueue_css() {
        self::register_assets();
        self::enqueue_critical();

        self::set_debug('css_mode', wpbb_get_option('bootstrap_css_mode', 'auto'));
        self::set_debug('load_bootstrap_css', !empty(wpbb_get_option('load_bootstrap_css', 1)));
        self::set_debug('selected_css', self::get_selected_css_components());
        self::set_debug('detected', self::detect_components());
        self::set_debug('mapping', self::get_block_mapping_report());

        if (!wpbb_get_option('load_bootstrap_css', 1)) {
            self::set_debug('final_css', []);
            return;
        }

        $mode = wpbb_get_option('bootstrap_css_mode', 'auto');
        $detected = self::detect_components();
        $selected = self::get_selected_css_components();

        if ($mode === 'full') {
            wp_enqueue_style('wpbb-bootstrap-full');
            self::set_debug('final_css', ['full']);
            return;
        }

        if ($mode === 'grid') {
            wp_enqueue_style('wpbb-bootstrap-reboot');
            wp_enqueue_style('wpbb-bootstrap-grid');
            self::set_debug('final_css', ['reboot', 'grid']);
            return;
        }

        if ($mode === 'utilities') {
            wp_enqueue_style('wpbb-bootstrap-reboot');
            wp_enqueue_style('wpbb-bootstrap-utilities');
            self::set_debug('final_css', ['reboot', 'utilities']);
            return;
        }

        $components = ($mode === 'custom') ? $selected : array_merge($detected['css'], $selected);
        $components = self::sanitize_components($components, self::get_css_component_choices());
        self::set_debug('final_css', self::enqueue_css_components($components));
    }

    public static function enqueue_js_if_needed() {
        self::register_assets();

        $mode = wpbb_get_option('bootstrap_js_mode', 'auto');
        $selected = self::get_selected_js_components();
        $detected = self::detect_components();
        $runtime = array_keys(self::$components);
        $has_runtime_needs = !empty($runtime);
        $has_auto_needs = !empty($detected['js']);
        $has_global_flag = !empty(wpbb_get_option('load_bootstrap_js', 0));

        self::set_debug('js_mode', $mode);
        self::set_debug('selected_js', $selected);
        self::set_debug('runtime_js', $runtime);
        self::set_debug('detected', $detected);
        self::set_debug('mapping', self::get_block_mapping_report());
        self::set_debug('load_bootstrap_js', $has_global_flag);

        if ($mode === 'full') {
            if ($has_global_flag || $has_runtime_needs || $has_auto_needs || !empty($selected)) {
                wp_enqueue_script('wpbb-bootstrap-bundle');
                self::set_debug('final_js', ['full']);
            } else {
                self::set_debug('final_js', []);
            }
            return;
        }

        if ($mode === 'custom') {
            $components = $selected;
            if (!$has_global_flag && empty($components)) {
                self::set_debug('final_js', []);
                return;
            }
        } else {
            $components = array_merge($selected, $runtime, $detected['js']);
            if (!$has_global_flag && !$has_runtime_needs && !$has_auto_needs) {
                self::set_debug('final_js', []);
                return;
            }
        }

        $components = self::sanitize_components($components, self::get_js_component_choices());
        foreach ($components as $component) {
            wp_enqueue_script(self::js_handle($component));
        }
        self::set_debug('final_js', $components);
    }

    public static function get_debug_report() {
        $report = self::ensure_debug_report();
        $report['detected'] = self::detect_components();
        $report['mapping'] = self::get_block_mapping_report();
        $report['runtime_js'] = array_keys(self::$components);
        $report['selected_css'] = self::get_selected_css_components();
        $report['selected_js'] = self::get_selected_js_components();
        $report['css_mode'] = wpbb_get_option('bootstrap_css_mode', 'auto');
        $report['js_mode'] = wpbb_get_option('bootstrap_js_mode', 'auto');
        $report['load_bootstrap_css'] = !empty(wpbb_get_option('load_bootstrap_css', 1));
        $report['load_bootstrap_js'] = !empty(wpbb_get_option('load_bootstrap_js', 0));
        $report['force_bootstrap_enqueue'] = !empty(wpbb_get_option('force_bootstrap_enqueue', 0));
        self::$debug_report = $report;
        return $report;
    }
}
