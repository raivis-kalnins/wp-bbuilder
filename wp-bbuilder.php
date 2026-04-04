<?php
/**
 * Plugin Name: WP BBuilder
 * Description: Bootstrap-oriented Gutenberg blocks with row, column, cards, accordion, tabs, button, dynamic form, admin settings, and optional ACF Hero block.
 * Version: 3.3.0
 * Author: Raivis Kalnins
 * Text Domain: wp-bbuilder
 */

if (!defined('ABSPATH')) exit;

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
