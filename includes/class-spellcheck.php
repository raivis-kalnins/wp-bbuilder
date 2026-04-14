<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Spellcheck {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
        add_filter('tiny_mce_before_init', [$this, 'filter_tinymce_settings']);
    }

    public static function get_supported_languages() {
        return [
            'en' => 'English',
            'lv' => 'Latvian',
            'et' => 'Estonian',
            'lt' => 'Lithuanian',
            'pl' => 'Polish',
            'de' => 'German',
            'fr' => 'French',
            'es' => 'Spanish',
            'it' => 'Italian',
            'sv' => 'Swedish',
            'fi' => 'Finnish',
            'no' => 'Norwegian',
            'da' => 'Danish',
            'is' => 'Icelandic',
            'ru' => 'Russian',
        ];
    }

    private function is_enabled() {
        return (bool) wpbb_get_option('admin_spellcheck_enabled', 0);
    }

    private function get_language() {
        $lang = (string) wpbb_get_option('admin_spellcheck_language', 'en');
        $supported = self::get_supported_languages();
        return isset($supported[$lang]) ? $lang : 'en';
    }

    private function should_load_for_screen() {
        if (!is_admin() || !$this->is_enabled()) {
            return false;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen) {
            return true;
        }

        $allowed_bases = ['post', 'site-editor', 'widgets', 'appearance_page_gutenberg-edit-site'];
        if (in_array($screen->base, $allowed_bases, true)) {
            return true;
        }

        return !empty($screen->is_block_editor);
    }

    public function enqueue_admin_assets() {
        if (!$this->should_load_for_screen()) {
            return;
        }

        wp_enqueue_script(
            'wpbb-admin-spellcheck',
            WPBB_PLUGIN_URL . 'assets/admin-spellcheck.js',
            [],
            WPBB_VERSION,
            true
        );

        wp_localize_script('wpbb-admin-spellcheck', 'wpbbSpellcheck', [
            'enabled' => true,
            'lang' => $this->get_language(),
            'languages' => self::get_supported_languages(),
            'selectors' => [
                'input[type="text"]',
                'input[type="search"]',
                'input[type="url"]',
                'input:not([type])',
                'textarea',
                '[contenteditable="true"]',
                '.block-editor-rich-text__editable',
                '.editor-post-title__input',
                '.acf-input input[type="text"]',
                '.acf-input input:not([type])',
                '.acf-input textarea',
                '.acf-field[data-type="wysiwyg"] iframe',
                '.mce-content-body',
            ],
            'notice' => sprintf(
                __('Spellcheck is active in admin only (%s). Frontend stays untouched for SEO and performance.', 'wp-bbuilder'),
                self::get_supported_languages()[$this->get_language()] ?? 'English'
            ),
        ]);
    }

    public function enqueue_block_editor_assets() {
        $this->enqueue_admin_assets();
    }

    public function filter_tinymce_settings($init) {
        if (!$this->is_enabled()) {
            return $init;
        }

        $init['browser_spellcheck'] = true;
        $init['gecko_spellcheck'] = true;
        $init['content_langs'] = [
            ['title' => self::get_supported_languages()[$this->get_language()] ?? 'English', 'code' => $this->get_language()],
        ];
        $init['language'] = $init['language'] ?? 'en';
        return $init;
    }
}
