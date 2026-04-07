<?php
/**
 * Plugin Name: WP BBuilder
 * Description: Bootstrap-oriented Gutenberg blocks with row, column, cards, accordion, tabs, button, dynamic form, admin settings, and optional ACF Hero block.
 * Version: 3.3.0
 * Author: Raivis Kalnins
 * Text Domain: wp-bbuilder
 */

if (!defined('ABSPATH')) exit;

// Include helper functions first
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';

// Include the optimized Bootstrap loader
require_once plugin_dir_path(__FILE__) . 'includes/class-bootstrap.php';

// Include other existing files...
require_once plugin_dir_path(__FILE__) . 'includes/class-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-blocks.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';
// ... rest of your includes

define('WPBB_VERSION', '3.3.0');
define('WPBB_PLUGIN_FILE', __FILE__);
define('WPBB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPBB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPBB_TEXTDOMAIN', 'wp-bbuilder');

require_once WPBB_PLUGIN_DIR . 'includes/helpers.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-settings.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-admin.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-blocks.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-form-handler.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-acf.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-whatsapp.php';
require_once WPBB_PLUGIN_DIR . 'includes/class-cookie-consent.php';

final class WP_BBuilder {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        WPBB_Settings::instance();
        WPBB_Admin::instance();
        WPBB_Blocks::instance();
        WPBB_Form_Handler::instance();
        WPBB_ACF::instance();
        WPBB_WhatsApp::instance();
        WPBB_Cookie_Consent::instance();
    }

    public function load_textdomain() {
        load_plugin_textdomain('wp-bbuilder', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
}

WP_BBuilder::instance();

if (!function_exists('wpbb_render_compiled_css')) {
    function wpbb_render_compiled_css() {
        $css = (string) wpbb_get_option('compiled_css', '');
        if ($css !== '') {
            echo "<style id=\"wpbb-compiled-css\">" . $css . "</style>";
        }
    }
    add_action('wp_head', 'wpbb_render_compiled_css', 99);

    function wpbb_render_meta_header_code() {
        $code = (string) wpbb_get_option('meta_header_code', '');
        if ($code !== '') {
            echo $code;
        }
    }
    add_action('wp_head', 'wpbb_render_meta_header_code', 100);

    function wpbb_render_global_footer_code() {
        $code = (string) wpbb_get_option('global_footer_code', '');
        if ($code !== '') {
            echo $code;
        }
    }
    add_action('wp_footer', 'wpbb_render_global_footer_code', 100);
}

// Instead of direct enqueue
add_action('wp_enqueue_scripts', function() {
    // Inline critical CSS
    $critical_css = '/* paste the CSS from above here, minified */';
    wp_register_style('wpbb-critical', false);
    wp_enqueue_style('wpbb-critical');
    wp_add_inline_style('wpbb-critical', $critical_css);
    
    // Optional full Bootstrap
    if (get_option('wpbb_load_bootstrap_css', '1') === '1') {
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');
    }
    if (get_option('wpbb_load_bootstrap_js', '1') === '1') {
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], '5.3.2', true);
    }
});

add_action('admin_enqueue_scripts', 'wp_bbuilder_admin_fonts');
add_action('enqueue_block_editor_assets', 'wp_bbuilder_editor_fonts');

function wp_bbuilder_admin_fonts() {
    // Ielādē Font Awesome 6 (vai jūsu izmantoto versiju)
    wp_enqueue_style(
        'wp-bbuilder-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        [],
        '6.4.0'
    );
}

function wp_bbuilder_editor_fonts() {
    // Ielādē arī block editorā
    wp_enqueue_style(
        'wp-bbuilder-fontawesome-editor',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        [],
        '6.4.0'
    );
}
