<?php
if (!defined('ABSPATH')) exit;
final class WPBB_Admin {
    private static $instance = null;
    public static function instance() { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { add_action('admin_menu', [$this,'menu']); add_action('admin_enqueue_scripts', [$this,'assets']); }
    public function menu() {
        add_menu_page(__('BBuilder','wp-bbuilder'), __('BBuilder','wp-bbuilder'), 'manage_options', 'wpbb-settings', [$this,'render'], 'dashicons-screenoptions', 3);
        add_submenu_page('wpbb-settings', __('Settings','wp-bbuilder'), __('Settings','wp-bbuilder'), 'manage_options', 'wpbb-settings', [$this,'render']);
    }
    public function assets($hook) {
        if (strpos((string)$hook, 'wpbb-settings') === false) return;
        wp_enqueue_style('wpbb-admin', WPBB_PLUGIN_URL . 'assets/admin.css', [], WPBB_VERSION);
        $width = wpbb_get_option('admin_max_width', '1400px');
        wp_add_inline_style('wpbb-admin', '.wpbb-admin-wrap{max-width:' . esc_attr($width) . ';overflow-x:hidden}.wpbb-admin-wrap input[type=text],.wpbb-admin-wrap input[type=email],.wpbb-admin-wrap input[type=url],.wpbb-admin-wrap input[type=password],.wpbb-admin-wrap input[type=number],.wpbb-admin-wrap textarea,.wpbb-admin-wrap select{width:100%;max-width:100%;box-sizing:border-box}');
    }
    public function render() {
        $opts = wp_parse_args(get_option('wpbb_settings', []), wpbb_defaults());
        ?>
        <div class="wrap wpbb-admin-wrap">
            <h1><?php esc_html_e('WP BBuilder Settings', 'wp-bbuilder'); ?></h1>
            <div class="wpbb-admin-nav">
                <a href="#tools"><?php esc_html_e('BBuilder tools', 'wp-bbuilder'); ?></a>
                <a href="#blocks"><?php esc_html_e('Blocks', 'wp-bbuilder'); ?></a>
                <a href="#forms"><?php esc_html_e('Forms', 'wp-bbuilder'); ?></a>
                <a href="#acf"><?php esc_html_e('ACF', 'wp-bbuilder'); ?></a>
                <a href="#core"><?php esc_html_e('Core blocks', 'wp-bbuilder'); ?></a>
                <a href="#chat"><?php esc_html_e('WhatsApp chat', 'wp-bbuilder'); ?></a>
                <a href="#cookie"><?php esc_html_e('Cookie consent', 'wp-bbuilder'); ?></a>
            </div>
            <form method="post" action="options.php">
                <?php settings_fields('wpbb_settings_group'); ?>
                <div class="wpbb-admin-grid">
                    <div class="wpbb-card wpbb-card--priority" id="tools">
                        <h2><?php esc_html_e('BBuilder tools', 'wp-bbuilder'); ?></h2>
                        <div class="wpbb-settings-tools">
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Blocks', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('All BBuilder blocks together.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Forms', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Bootstrap, validation, captcha.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('ACF', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Hero, Boot Card, Gallery.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Bootstrap classes', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Reusable class list for blocks.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Core controls', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Enable or disable selected core blocks.', 'wp-bbuilder'); ?></div></div>
                        </div>
                    </div>
                    <div class="wpbb-card" id="blocks">
                        <h2><?php esc_html_e('BBuilder blocks', 'wp-bbuilder'); ?></h2>
                        <?php foreach (wpbb_get_blocks_list() as $slug): ?>
                            <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[enabled_blocks][<?php echo esc_attr($slug); ?>]" value="1" <?php checked(!empty($opts['enabled_blocks'][$slug])); ?>><span><?php echo esc_html($slug); ?></span></label>
                        <?php endforeach; ?>
                    </div>
                    <div class="wpbb-card" id="forms">
                        <h2><?php esc_html_e('Form defaults', 'wp-bbuilder'); ?></h2>
                        <p><label><?php esc_html_e('Recipient email', 'wp-bbuilder'); ?><br><input type="email" name="wpbb_settings[default_recipient_email]" value="<?php echo esc_attr($opts['default_recipient_email']); ?>"></label></p>
                        <p><label><?php esc_html_e('Success message', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[default_success_message]" value="<?php echo esc_attr($opts['default_success_message']); ?>"></label></p>
                        <p><label><?php esc_html_e('Error message', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[default_error_message]" value="<?php echo esc_attr($opts['default_error_message']); ?>"></label></p>
                        <p><label><?php esc_html_e('Validation text', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[default_validation_text]" value="<?php echo esc_attr($opts['default_validation_text']); ?>"></label></p>
                        <p><label><?php esc_html_e('Default button class', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[button_class]" value="<?php echo esc_attr($opts['button_class']); ?>"></label></p>
                        <p><label><?php esc_html_e('Default form class', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[form_class]" value="<?php echo esc_attr($opts['form_class']); ?>"></label></p>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[save_entries]" value="1" <?php checked(!empty($opts['save_entries'])); ?>> <?php esc_html_e('Save form entries internally', 'wp-bbuilder'); ?></label>
                        <h3><?php esc_html_e('Captcha options', 'wp-bbuilder'); ?></h3>
                        <p><label>hCaptcha site key<br><input type="text" name="wpbb_settings[hcaptcha_site_key]" value="<?php echo esc_attr($opts['hcaptcha_site_key']); ?>"></label></p>
                        <p><label>hCaptcha secret key<br><input type="text" name="wpbb_settings[hcaptcha_secret_key]" value="<?php echo esc_attr($opts['hcaptcha_secret_key']); ?>"></label></p>
                        <p><label>reCAPTCHA site key<br><input type="text" name="wpbb_settings[recaptcha_site_key]" value="<?php echo esc_attr($opts['recaptcha_site_key']); ?>"></label></p>
                        <p><label>reCAPTCHA secret key<br><input type="text" name="wpbb_settings[recaptcha_secret_key]" value="<?php echo esc_attr($opts['recaptcha_secret_key']); ?>"></label></p>
                        <h3><?php esc_html_e('Form colors', 'wp-bbuilder'); ?></h3>
                        <p><label>Label color<br><input type="color" name="wpbb_settings[default_label_color]" value="<?php echo esc_attr($opts['default_label_color']); ?>"></label></p>
                        <p><label>Input border color<br><input type="color" name="wpbb_settings[default_input_border_color]" value="<?php echo esc_attr($opts['default_input_border_color']); ?>"></label></p>
                        <p><label>Button background<br><input type="color" name="wpbb_settings[default_button_bg]" value="<?php echo esc_attr($opts['default_button_bg']); ?>"></label></p>
                        <p><label>Button text color<br><input type="color" name="wpbb_settings[default_button_text]" value="<?php echo esc_attr($opts['default_button_text']); ?>"></label></p>
                    </div>
                    <div class="wpbb-card" id="acf">
                        <h2><?php esc_html_e('ACF blocks', 'wp-bbuilder'); ?></h2>
                        <p class="description"><?php esc_html_e('Included rebuilt ACF blocks: Hero, Boot Card, Gallery.', 'wp-bbuilder'); ?></p>
                    </div>
                    <div class="wpbb-card" id="chat">
                        <h2><?php esc_html_e('WhatsApp chat', 'wp-bbuilder'); ?></h2>
                        <p><label><?php esc_html_e('Phone number', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[whatsapp_phone]" value="<?php echo esc_attr($opts['whatsapp_phone']); ?>"></label></p>
                        <p><label><?php esc_html_e('Default message', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[whatsapp_message]" value="<?php echo esc_attr($opts['whatsapp_message']); ?>"></label></p>
                        <p><label><?php esc_html_e('Position', 'wp-bbuilder'); ?><br>
                            <select name="wpbb_settings[whatsapp_position]">
                                <option value="bottom-right" <?php selected($opts['whatsapp_position'], 'bottom-right'); ?>>bottom-right</option>
                                <option value="bottom-left" <?php selected($opts['whatsapp_position'], 'bottom-left'); ?>>bottom-left</option>
                                <option value="top-right" <?php selected($opts['whatsapp_position'], 'top-right'); ?>>top-right</option>
                                <option value="top-left" <?php selected($opts['whatsapp_position'], 'top-left'); ?>>top-left</option>
                            </select>
                        </label></p>
                        <p><label><?php esc_html_e('Bubble background', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[whatsapp_bg]" value="<?php echo esc_attr($opts['whatsapp_bg']); ?>"></label></p>
                        <p><label><?php esc_html_e('Bubble text color', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[whatsapp_text]" value="<?php echo esc_attr($opts['whatsapp_text']); ?>"></label></p>
                        <p class="description"><?php esc_html_e('For transparent, leave color field as-is and use rgba/transparent in block custom style fields.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="datatable-options">
                        <h2><?php esc_html_e('DataTables defaults', 'wp-bbuilder'); ?></h2>
                        <p class="description"><?php esc_html_e('Bootstrap Table block supports searching, paging, ordering and responsive wrapper from block settings.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="bootstrap-classes">
                        <h2><?php esc_html_e('Bootstrap class helper', 'wp-bbuilder'); ?></h2>
                        <p class="description"><?php esc_html_e('Use these class ideas in Additional CSS class(es) or Bootstrap class fields: container, container-fluid, row, col-*, d-flex, justify-content-*, align-items-*, p-*, m-*, bg-*, text-*, rounded, shadow, w-100, ratio, table, table-striped, table-hover.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="cookie">
                        <h2><?php esc_html_e('Cookie consent + analytics', 'wp-bbuilder'); ?></h2>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[cookie_consent_enabled]" value="1" <?php checked(!empty($opts['cookie_consent_enabled'])); ?>><span><?php esc_html_e('Enable cookie consent banner', 'wp-bbuilder'); ?></span></label>
                        <p><label><?php esc_html_e('Banner text', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[cookie_consent_text]" value="<?php echo esc_attr($opts['cookie_consent_text']); ?>"></label></p>
                        <p><label><?php esc_html_e('Accept button text', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[cookie_accept_text]" value="<?php echo esc_attr($opts['cookie_accept_text']); ?>"></label></p>
                        <p><label><?php esc_html_e('Reject button text', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[cookie_reject_text]" value="<?php echo esc_attr($opts['cookie_reject_text']); ?>"></label></p>
                        <p><label><?php esc_html_e('Policy URL', 'wp-bbuilder'); ?><br><input type="url" name="wpbb_settings[cookie_policy_url]" value="<?php echo esc_attr($opts['cookie_policy_url']); ?>"></label></p>
                        <p><label><?php esc_html_e('Banner position', 'wp-bbuilder'); ?><br>
                            <select name="wpbb_settings[cookie_position]">
                                <option value="bottom" <?php selected($opts['cookie_position'], 'bottom'); ?>>bottom</option>
                                <option value="top" <?php selected($opts['cookie_position'], 'top'); ?>>top</option>
                            </select>
                        </label></p>
                        <p><label><?php esc_html_e('Banner background', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[cookie_bg]" value="<?php echo esc_attr($opts['cookie_bg']); ?>"></label></p>
                        <p><label><?php esc_html_e('Banner text color', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[cookie_text_color]" value="<?php echo esc_attr($opts['cookie_text_color']); ?>"></label></p>
                        <p><label><?php esc_html_e('Button background', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[cookie_button_bg]" value="<?php echo esc_attr($opts['cookie_button_bg']); ?>"></label></p>
                        <p><label><?php esc_html_e('Button text color', 'wp-bbuilder'); ?><br><input type="color" name="wpbb_settings[cookie_button_text]" value="<?php echo esc_attr($opts['cookie_button_text']); ?>"></label></p>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[google_analytics_enabled]" value="1" <?php checked(!empty($opts['google_analytics_enabled'])); ?>><span><?php esc_html_e('Enable Google Analytics head code', 'wp-bbuilder'); ?></span></label>
                        <p><label><?php esc_html_e('Google Analytics head code', 'wp-bbuilder'); ?><br><textarea name="wpbb_settings[google_analytics_head]" rows="6"><?php echo esc_textarea($opts['google_analytics_head']); ?></textarea></label></p>
                        <p class="description"><?php esc_html_e('Optional and off by default.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="core">
                        <h2><?php esc_html_e('Core blocks and assets', 'wp-bbuilder'); ?></h2>
                        <?php foreach ([
                            'disable_core_group'=>'core/group','disable_core_columns'=>'core/columns','disable_core_column'=>'core/column',
                            'disable_core_table'=>'core/table','disable_core_embed'=>'core/embed','disable_core_gallery'=>'core/gallery',
                            'disable_core_image'=>'core/image','disable_core_cover'=>'core/cover','disable_core_media_text'=>'core/media-text',
                            'disable_core_buttons'=>'core/buttons','disable_core_button'=>'core/button'
                        ] as $setting => $label): ?>
                            <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[<?php echo esc_attr($setting); ?>]" value="1" <?php checked(!empty($opts[$setting])); ?>> <?php echo esc_html__('Disable ', 'wp-bbuilder') . esc_html($label); ?></label>
                        <?php endforeach; ?>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_bootstrap_css]" value="1" <?php checked(!empty($opts['load_bootstrap_css'])); ?>> Load Bootstrap CSS</label>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_bootstrap_js]" value="1" <?php checked(!empty($opts['load_bootstrap_js'])); ?>> Load Bootstrap JS</label>
                        <p><label>Admin max width<br><input type="text" name="wpbb_settings[admin_max_width]" value="<?php echo esc_attr($opts['admin_max_width']); ?>"></label></p>
                    </div>
                </div>
                <?php submit_button(__('Save settings', 'wp-bbuilder')); ?>
            </form>
        </div>
        <?php
    }
}
