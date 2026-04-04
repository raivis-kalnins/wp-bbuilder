<?php
/**
 * Plugin Name: WP BBlocks - Bootstrap Block & Dynamic Form Builder
 * Description: Bootstrap 5 Row/Column Gutenberg blocks, Gutenberg block with ACF, drag-drop form fields, hCaptcha/reCaptcha
 * Version: 2.0.0
 * Author: Raivis Kalnins
 * Text Domain: wp-bblocks
 */

if (!defined('ABSPATH')) exit;

defined( 'ABSPATH' ) || exit;

define('WPBOOTBLOCKS_ROOT_PATH', plugin_dir_path(__FILE__));
define('WPBOOTBLOCKS_ROOT_URL', plugin_dir_url(__FILE__));

/**
 * Register and enqueue Gutenberg Blocks
 */
function register_wp_blocks() {
    $blocksPath = WPBOOTBLOCKS_ROOT_PATH . 'build/blocks/';
    $iterator = new DirectoryIterator($blocksPath);
    foreach ($iterator as $file) {
        if ($file->isDot()) continue;
        if ($file->isDir()) {
            register_block_type($file->getPathname());
        }
    }
}
add_action('init', 'register_wp_blocks');

/**
 * Register and enqueue Script & Style WP Front Side
 */
function wpdocs_enqueue_custom_public_style() {
	$name       = 'public';
	$filepath   = 'build/public/' . $name;
	$asset_path = WPBOOTBLOCKS_ROOT_PATH . $filepath . '.asset.php';
	$asset_file = file_exists( $asset_path ) ? include $asset_path : array( 'dependencies' => array(), 'version' => '1.0', );
	$script_url = WPBOOTBLOCKS_ROOT_URL . 'build/style/public.js';
	$style_url = WPBOOTBLOCKS_ROOT_URL . 'build/style/public.scss.css';
	$v = $asset_file['version'];
	wp_enqueue_style( 'public-wpbootblocks-style', $style_url, array(), $v, 'all' );
	//wp_enqueue_script( 'js_bootstrap', '//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.min.js', ['jquery'], '', 'all' );
	wp_enqueue_script( 'public-wpbootblocks-style', $script_url, array(), $v, 'all' );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_enqueue_custom_public_style');

/**
 * Register and enqueue Script & Style WP Admin Side
 */
function wpdocs_enqueue_custom_admin_style() {
	$name       = 'admin';
	$filepath   = 'build/' . $name;
	$asset_path = WPBOOTBLOCKS_ROOT_PATH . $filepath . '.asset.php';
	$asset_file = file_exists( $asset_path ) ? include $asset_path : array( 'dependencies' => array(), 'version' => '1.0', );
	$script_url = WPBOOTBLOCKS_ROOT_URL . 'build/style/admin.js';
	$style_url = WPBOOTBLOCKS_ROOT_URL . 'build/style/admin.scss.css';
	$v = $asset_file['version'];
	wp_enqueue_script( 'admin-wpbootblocks-style', $script_url, array(), $v, 'all' );
	wp_enqueue_style( 'admin-wpbootblocks-style', $style_url, array(), $v, 'all' );
}
add_action('admin_enqueue_scripts','wpdocs_enqueue_custom_admin_style');

/**
 * Disabled Gutenberg blocks
 */
 function wp_blacklist_blocks() {
	// get all the registered blocks
	$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
	// then disable some of them
	//unset( $blocks[ 'mailchimp-for-wp/form' ] );
	unset( $blocks[ 'core/buttons' ] );
	unset( $blocks[ 'core/columns' ] );
	//unset( $blocks[ 'core/group' ] );
	//unset( $blocks[ 'core/stack' ] );
	//unset( $blocks[ 'core/embed' ] );
	//unset( $blocks[ 'core/verse' ] );
	// return the new list of allowed blocks
	return array_keys( $blocks );
}
add_filter( 'allowed_block_types_all', 'wp_blacklist_blocks' );

add_theme_support( 'editor-styles' );
add_theme_support( 'dark-editor-style' );
add_theme_support( 'align-wide' );
add_theme_support( 'wp-block-styles' );
add_theme_support( 'block-templates' );
add_theme_support( 'block-template-parts' );
add_theme_support( 'custom-units', 'rem', 'vw', 'vh', '%' );

// add_action('init', function () {
//     // Register block categories early
//     add_filter( 'block_categories_all', function( $categories, $post ) {
//         $custom_categories = [
//             ['slug' => 'WP-content', 'title' => '1. WP Stylised Content Blocks'],
//             ['slug' => 'WP-layout', 'title' => '2. WP Stylised Layout Blocks'],

//         ];
//         // Put your categories at the top
//         return array_merge( $custom_categories, $categories );
//     }, 1, 2 );
// }, 0);

// add_action('enqueue_block_editor_assets', function () {
//     wp_enqueue_script(
//         'move-core-blocks',
//         plugins_url('block-categories.js', __FILE__), // adjust path if needed
//         ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
//         null,
//         true
//     );
// });

// function add_acf_fields() {

// 	acf_add_local_field_group(

// 		array(
// 			'key' => 'group_6895fd1ab2561',
// 			'title' => 'Hero',
// 			'fields' => array(
// 				array(
// 					'key' => 'field_6895fd1b57f2e',
// 					'label' => 'Hero item',
// 					'name' => 'hero_item',
// 					'aria-label' => '',
// 					'type' => 'repeater',
// 					'instructions' => 'Add Hero Items',
// 					'required' => 0,
// 					'conditional_logic' => 0,
// 					'wrapper' => array(
// 						'width' => '',
// 						'class' => '',
// 						'id' => '',
// 					),
// 					'layout' => 'table',
// 					'pagination' => 0,
// 					'min' => 0,
// 					'max' => 0,
// 					'collapsed' => '',
// 					'button_label' => 'Add Row',
// 					'rows_per_page' => 20,
// 					'sub_fields' => array(
// 						array(
// 							'key' => 'field_6895fd5557f2f',
// 							'label' => 'Image',
// 							'name' => 'image',
// 							'aria-label' => '',
// 							'type' => 'image',
// 							'instructions' => '',
// 							'required' => 0,
// 							'conditional_logic' => 0,
// 							'wrapper' => array(
// 								'width' => '',
// 								'class' => '',
// 								'id' => '',
// 							),
// 							'return_format' => 'url',
// 							'library' => 'all',
// 							'min_width' => '',
// 							'min_height' => '',
// 							'min_size' => '',
// 							'max_width' => '',
// 							'max_height' => '',
// 							'max_size' => '',
// 							'mime_types' => '',
// 							'allow_in_bindings' => 0,
// 							'preview_size' => 'medium',
// 							'parent_repeater' => 'field_6895fd1b57f2e',
// 						),
// 						array(
// 							'key' => 'field_6895fd6757f30',
// 							'label' => 'Caption',
// 							'name' => 'caption',
// 							'aria-label' => '',
// 							'type' => 'text',
// 							'instructions' => '',
// 							'required' => 0,
// 							'conditional_logic' => 0,
// 							'wrapper' => array(
// 								'width' => '',
// 								'class' => '',
// 								'id' => '',
// 							),
// 							'default_value' => '',
// 							'maxlength' => '',
// 							'allow_in_bindings' => 0,
// 							'placeholder' => '',
// 							'prepend' => '',
// 							'append' => '',
// 							'parent_repeater' => 'field_6895fd1b57f2e',
// 						),
// 						array(
// 							'key' => 'field_6895fd9457f32',
// 							'label' => 'Gallery',
// 							'name' => 'gallery',
// 							'aria-label' => '',
// 							'type' => 'gallery',
// 							'instructions' => '',
// 							'required' => 0,
// 							'conditional_logic' => 0,
// 							'wrapper' => array(
// 								'width' => '',
// 								'class' => '',
// 								'id' => '',
// 							),
// 							'return_format' => 'url',
// 							'library' => 'all',
// 							'min' => '',
// 							'max' => '',
// 							'min_width' => '',
// 							'min_height' => '',
// 							'min_size' => '',
// 							'max_width' => '',
// 							'max_height' => '',
// 							'max_size' => '',
// 							'mime_types' => '',
// 							'insert' => 'append',
// 							'preview_size' => 'medium',
// 							'parent_repeater' => 'field_6895fd1b57f2e',
// 						),
// 						array(
// 							'key' => 'field_6895fd8157f31',
// 							'label' => 'Description',
// 							'name' => 'description',
// 							'aria-label' => '',
// 							'type' => 'wysiwyg',
// 							'instructions' => '',
// 							'required' => 0,
// 							'conditional_logic' => 0,
// 							'wrapper' => array(
// 								'width' => '',
// 								'class' => '',
// 								'id' => '',
// 							),
// 							'default_value' => '',
// 							'allow_in_bindings' => 0,
// 							'tabs' => 'all',
// 							'toolbar' => 'full',
// 							'media_upload' => 1,
// 							'delay' => 0,
// 							'parent_repeater' => 'field_6895fd1b57f2e',
// 						),
// 					),
// 				),
// 			),
// 			'location' => array(
// 				array(
// 					array(
// 						'param'    => 'block',
// 						'operator' => '==',
// 						'value'    => 'acf/hero',
// 					),
// 				),
// 			),
// 		)
// 	);

// 	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
// 		return;
// 	}
// }
// add_action( 'acf/include_fields', 'add_acf_fields' );

/**
 * ACF Blocks
 */
function x_acf_block_register() {
	register_block_type(  __DIR__ . '/acf-blocks/acf-gallery' );
	register_block_type(  __DIR__ . '/acf-blocks/cta-section' );
	register_block_type(  __DIR__ . '/acf-blocks/menu-option' );
	register_block_type(  __DIR__ . '/acf-blocks/cta-card' );
	register_block_type(  __DIR__ . '/acf-blocks/hero' );
}
add_action( 'init', 'x_acf_block_register', 5 );


class WP_BBlocks {
	public function __construct() {
		add_action('init', [$this, 'register_blocks']);
		add_action('init', [$this, 'register_acf_fields']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
		
		// AJAX handlers
		add_action('wp_ajax_bblocks_submit_form', [$this, 'handle_form_submission']);
		add_action('wp_ajax_nopriv_bblocks_submit_form', [$this, 'handle_form_submission']);
	}

	public function register_blocks() {
		register_block_type(__DIR__ . '/blocks/dynamic-form');
	}

	public function register_acf_fields() {
		if (!function_exists('acf_add_local_field_group')) return;

		acf_add_local_field_group([
			'key' => 'group_bblocks_form',
			'title' => 'Form Configuration',
			'fields' => [
				[
					'key' => 'field_form_fields',
					'label' => 'Form Fields',
					'name' => 'form_fields',
					'type' => 'repeater',
					'layout' => 'block',
					'button_label' => 'Add Field',
					'sub_fields' => [
						[
							'key' => 'field_type',
							'label' => 'Field Type',
							'name' => 'field_type',
							'type' => 'select',
							'choices' => [
								'text' => 'Text',
								'email' => 'Email',
								'textarea' => 'Textarea',
								'select' => 'Select',
								'checkbox' => 'Checkbox',
								'radio' => 'Radio',
								'file' => 'File Upload',
								'date' => 'Date Picker',
								'captcha' => 'Captcha (hCaptcha/reCaptcha)'
							],
							'default_value' => 'text'
						],
						[
							'key' => 'field_label',
							'label' => 'Label',
							'name' => 'field_label',
							'type' => 'text',
							'required' => 1
						],
						[
							'key' => 'field_name',
							'label' => 'Field Name (ID)',
							'name' => 'field_name',
							'type' => 'text',
							'required' => 1
						],
						[
							'key' => 'field_required',
							'label' => 'Required',
							'name' => 'field_required',
							'type' => 'true_false',
							'default_value' => 0
						],
						[
							'key' => 'field_options',
							'label' => 'Options (for Select/Radio/Checkbox)',
							'name' => 'field_options',
							'type' => 'textarea',
							'instructions' => 'One option per line (value:label)',
							'conditional_logic' => [
								[
									[
										'field' => 'field_type',
										'operator' => '==',
										'value' => 'select'
									]
								],
								[
									[
										'field' => 'field_type',
										'operator' => '==',
										'value' => 'radio'
									]
								],
								[
									[
										'field' => 'field_type',
										'operator' => '==',
										'value' => 'checkbox'
									]
								]
							]
						],
						[
							'key' => 'captcha_type',
							'label' => 'Captcha Provider',
							'name' => 'captcha_type',
							'type' => 'select',
							'choices' => [
								'hcaptcha' => 'hCaptcha',
								'recaptcha_v2' => 'reCaptcha v2',
								'recaptcha_v3' => 'reCaptcha v3'
							],
							'conditional_logic' => [
								[
									[
										'field' => 'field_type',
										'operator' => '==',
										'value' => 'captcha'
									]
								]
							]
						]
					]
				],
				[
					'key' => 'field_captcha_site_key',
					'label' => 'Captcha Site Key',
					'name' => 'captcha_site_key',
					'type' => 'text',
					'instructions' => 'Enter hCaptcha or reCaptcha site key'
				],
				[
					'key' => 'field_captcha_secret_key',
					'label' => 'Captcha Secret Key',
					'name' => 'captcha_secret_key',
					'type' => 'text',
					'instructions' => 'Enter hCaptcha or reCaptcha secret key'
				],
				[
					'key' => 'field_submit_button_text',
					'label' => 'Submit Button Text',
					'name' => 'submit_button_text',
					'type' => 'text',
					'default_value' => 'Submit'
				],
				[
					'key' => 'field_success_message',
					'label' => 'Success Message',
					'name' => 'success_message',
					'type' => 'textarea',
					'default_value' => 'Thank you for your submission!'
				]
			],
			'location' => [
				[
					[
						'param' => 'block',
						'operator' => '==',
						'value' => 'bblocks/dynamic-form'
					]
				]
			]
		]);
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'bblocks-form-style',
			plugins_url('blocks/dynamic-form/style.css', __FILE__),
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'bblocks-form-frontend',
			plugins_url('blocks/dynamic-form/form-frontend.js', __FILE__),
			['jquery'],
			'1.0.0',
			true
		);

		wp_localize_script('bblocks-form-frontend', 'bblocks_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('bblocks_form_nonce')
		]);
	}

	public function enqueue_admin_assets() {
		$screen = get_current_screen();
		if ($screen && $screen->is_block_editor) {
			wp_enqueue_script(
				'bblocks-form-builder',
				plugins_url('blocks/dynamic-form/form-builder.js', __FILE__),
				['wp-blocks', 'wp-element', 'wp-components', 'wp-data', 'wp-hooks', 'wp-block-editor'],
				'1.0.0',
				true
			);
		}
	}

	public function handle_form_submission() {
		check_ajax_referer('bblocks_form_nonce', 'nonce');

		$form_data = $_POST['form_data'] ?? [];
		$captcha_response = $_POST['captcha_response'] ?? '';
		$captcha_type = $_POST['captcha_type'] ?? '';
		$secret_key = $_POST['secret_key'] ?? '';

		// Verify captcha
		if ($captcha_response && $secret_key) {
			$verified = $this->verify_captcha($captcha_response, $secret_key, $captcha_type);
			if (!$verified) {
				wp_send_json_error(['message' => 'Captcha verification failed']);
				return;
			}
		}

		// Process form data
		$entry_id = $this->save_form_entry($form_data);
		
		// Send email notification
		$this->send_notification($form_data);

		wp_send_json_success([
			'message' => 'Form submitted successfully',
			'entry_id' => $entry_id
		]);
	}

	private function verify_captcha($response, $secret_key, $type) {
		if ($type === 'hcaptcha') {
			$verify_url = 'https://hcaptcha.com/siteverify';
		} else {
			$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
		}

		$result = wp_remote_post($verify_url, [
			'body' => [
				'secret' => $secret_key,
				'response' => $response
			]
		]);

		if (is_wp_error($result)) return false;

		$body = json_decode(wp_remote_retrieve_body($result), true);
		return $body['success'] ?? false;
	}

	private function save_form_entry($form_data) {
		$entry = [
			'post_title' => 'Form Entry ' . current_time('mysql'),
			'post_type' => 'bblocks_entry',
			'post_status' => 'publish',
			'meta_input' => $form_data
		];
		
		return wp_insert_post($entry);
	}

	private function send_notification($form_data) {
		$to = get_option('admin_email');
		$subject = 'New Form Submission';
		$message = "Form Data:\n\n" . print_r($form_data, true);
		wp_mail($to, $subject, $message);
	}
}

new WP_BBlocks();

/**
 * Bootstrap Blocks
 */

if (!defined('ABSPATH')) exit;

define('WPBB_VERSION', '2.0.0');
define('WPBB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPBB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPBB_BLOCKS_DIR', WPBB_PLUGIN_DIR . 'blocks/');

// Register blocks
add_action('init', 'wpbb_register_blocks');

function wpbb_register_blocks() {
	$blocks = ['row', 'column'];
	
	foreach ($blocks as $block) {
		$block_json = WPBB_BLOCKS_DIR . $block . '/block.json';
		if (file_exists($block_json)) {
			register_block_type($block_json, [
				'render_callback' => "wpbb_render_{$block}"
			]);
		}
	}
}

// Render callbacks
function wpbb_render_row($attributes, $content, $block) {
	$classes = ['row'];
	
	if (!empty($attributes['noGutters']) && $attributes['noGutters']) {
		$classes[] = 'g-0';
	} else {
		if (!empty($attributes['horizontalGutters'])) $classes[] = $attributes['horizontalGutters'];
		if (!empty($attributes['verticalGutters'])) $classes[] = $attributes['verticalGutters'];
	}
	
	if (!empty($attributes['alignment'])) $classes[] = 'justify-content-' . $attributes['alignment'];
	if (!empty($attributes['verticalAlignment'])) $classes[] = 'align-items-' . $attributes['verticalAlignment'];
	if (!empty($attributes['isCssGrid']) && $attributes['isCssGrid']) {
		$classes = ['grid'];
		if (!empty($attributes['gridColumns'])) $classes[] = 'grid-cols-' . $attributes['gridColumns'];
	}
	
	$wrapper = get_block_wrapper_attributes(['class' => implode(' ', $classes)]);
	
	return "<div {$wrapper}>{$content}</div>";
}

function wpbb_render_column($attributes, $content, $block) {
	$classes = [];
	$is_css_grid = $block->context['wpbb/rowIsCssGrid'] ?? false;
	
	if ($is_css_grid) {
		// CSS Grid classes
		if (!empty($attributes['xs'])) $classes[] = "g-col-{$attributes['xs']}";
		if (!empty($attributes['sm'])) $classes[] = "g-col-sm-{$attributes['sm']}";
		if (!empty($attributes['md'])) $classes[] = "g-col-md-{$attributes['md']}";
		if (!empty($attributes['lg'])) $classes[] = "g-col-lg-{$attributes['lg']}";
		if (!empty($attributes['xl'])) $classes[] = "g-col-xl-{$attributes['xl']}";
		if (!empty($attributes['xxl'])) $classes[] = "g-col-xxl-{$attributes['xxl']}";
	} else {
		// Flexbox classes
		$breakpoints = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
		foreach ($breakpoints as $bp) {
			if (!empty($attributes['equalWidth'][$bp])) {
				$suffix = $bp === 'xs' ? '' : "-{$bp}";
				$classes[] = "col{$suffix}";
			} elseif (!empty($attributes[$bp])) {
				$suffix = $bp === 'xs' ? '' : "-{$bp}";
				$classes[] = "col{$suffix}-{$attributes[$bp]}";
			}
		}
		
		// Offset
		if (!empty($attributes['offset'])) {
			foreach ($attributes['offset'] as $bp => $val) {
				if ($val > 0) {
					$suffix = $bp === 'xs' ? '' : "-{$bp}";
					$classes[] = "offset{$suffix}-{$val}";
				}
			}
		}
		
		// Order
		if (!empty($attributes['order'])) {
			foreach ($attributes['order'] as $bp => $val) {
				if ($val !== '') {
					$suffix = $bp === 'xs' ? '' : "-{$bp}";
					$classes[] = "order{$suffix}-{$val}";
				}
			}
		}
	}
	
	if (empty($classes)) $classes[] = 'col-12';
	
	$wrapper = get_block_wrapper_attributes(['class' => implode(' ', $classes)]);
	
	return "<div {$wrapper}>{$content}</div>";
}

// Enqueue assets
add_action('enqueue_block_editor_assets', 'wpbb_editor_assets');
add_action('wp_enqueue_scripts', 'wpbb_frontend_assets');
add_filter('block_categories_all', 'wpbb_categories', 10, 2);

function wpbb_editor_assets() {
	$asset = include WPBB_PLUGIN_DIR . 'build/index.asset.php';
	
	wp_enqueue_script(
		'wp-bblocks-editor',
		WPBB_PLUGIN_URL . 'build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
	
	wp_enqueue_style(
		'wp-bblocks-editor',
		WPBB_PLUGIN_URL . 'build/editor.css',
		[],
		WPBB_VERSION
	);
}

function wpbb_frontend_assets() {

	if (!wp_script_is('bootstrap', 'registered')) {
		wp_enqueue_script('bootstrap-js','https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], '5.3.2', true);
	}

	if (!wp_style_is('bootstrap', 'registered')) {
		wp_register_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');
	}
	wp_enqueue_style('bootstrap');

	wp_enqueue_style(
		'wp-bblocks',
		WPBB_PLUGIN_URL . 'build/style.css',
		['bootstrap'],
		WPBB_VERSION
	);
}

function wpbb_categories($categories, $post) {
	return array_merge($categories, [[
		'slug' => 'wpbb-layout',
		'title' => __('BBlocks Layout', 'wp-bblocks'),
		'icon' => 'layout',
	]]);
}