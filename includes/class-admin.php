<?php
if (!defined('ABSPATH')) exit;
final class WPBB_Admin {
    private static $instance = null;
    public static function instance() { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { add_action('admin_menu', [$this,'menu']); add_action('admin_enqueue_scripts', [$this,'assets']); add_action('wp_ajax_wpbb_compile_scss', [$this,'ajax_compile_scss']); }
    public function menu() {
        add_options_page(__('BBuilder','wp-bbuilder'), __('BBuilder','wp-bbuilder'), 'manage_options', 'wpbb-settings', [$this,'render']);
        add_submenu_page('wpbb-settings', __('Settings','wp-bbuilder'), __('Settings','wp-bbuilder'), 'manage_options', 'wpbb-settings', [$this,'render']);
    }
    public function assets($hook) {
        if (strpos((string)$hook, 'wpbb-settings') === false) return;
        wp_enqueue_style('wpbb-admin', WPBB_PLUGIN_URL . 'assets/admin.css', [], WPBB_VERSION);
        $width = wpbb_get_option('admin_max_width', '1400px');
        wp_add_inline_style('wpbb-admin', '.wpbb-admin-wrap{max-width:' . esc_attr($width) . ';overflow-x:hidden}.wpbb-admin-wrap input[type=text],.wpbb-admin-wrap input[type=email],.wpbb-admin-wrap input[type=url],.wpbb-admin-wrap input[type=password],.wpbb-admin-wrap input[type=number],.wpbb-admin-wrap textarea,.wpbb-admin-wrap select{width:100%;max-width:100%;box-sizing:border-box}');
        $scss_settings = wp_enqueue_code_editor(['type' => 'text/x-scss']);
        $html_settings = wp_enqueue_code_editor(['type' => 'text/html']);
        $css_settings = wp_enqueue_code_editor(['type' => 'text/css']);
        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');
        wp_enqueue_script('wpbb-admin-builder', WPBB_PLUGIN_URL . 'assets/admin-builder.js', ['jquery', 'code-editor'], WPBB_VERSION, true);
        wp_enqueue_script('wpbb-admin-sortable', WPBB_PLUGIN_URL . 'assets/admin-sortable.js', [], WPBB_VERSION, true);
        wp_localize_script('wpbb-admin-builder', 'wpbbBuilder', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpbb_builder_nonce'),
            'scss' => $scss_settings,
            'html' => $html_settings,
            'css' => $css_settings,
            'compiledText' => __('SCSS compiled successfully.', 'wp-bbuilder'),
            'errorText' => __('Build failed.', 'wp-bbuilder'),
            'adminCompiledCss' => (string) wpbb_get_option('admin_compiled_css', ''),
        ]);
    }
    public function render() {
        $opts = wp_parse_args(get_option('wpbb_settings', []), wpbb_defaults());
        ?>
        <div class="wrap wpbb-admin-wrap">
            <h1><?php esc_html_e('Builder Settings', 'wp-bbuilder'); ?></h1>
            <div class="wpbb-admin-nav">
                <a href="#tools"><?php esc_html_e('BBuilder tools', 'wp-bbuilder'); ?></a>
                <a href="#blocks"><?php esc_html_e('Blocks', 'wp-bbuilder'); ?></a>
                <a href="#forms"><?php esc_html_e('Forms', 'wp-bbuilder'); ?></a>
                <a href="#acf"><?php esc_html_e('ACF', 'wp-bbuilder'); ?></a>
                <a href="#core"><?php esc_html_e('Core blocks', 'wp-bbuilder'); ?></a>
                <a href="#chat"><?php esc_html_e('WhatsApp chat', 'wp-bbuilder'); ?></a>
                <a href="#cookie"><?php esc_html_e('Cookie consent', 'wp-bbuilder'); ?></a>
                <a href="#spellcheck"><?php esc_html_e('Spellcheck', 'wp-bbuilder'); ?></a>
            </div>
            <form method="post" action="options.php">
                <?php settings_fields('wpbb_settings_group'); ?>
                <div class="wpbb-admin-grid">
                    <div class="wpbb-card wpbb-card--priority wpbb-card--tabs-first" id="tools">
                        <h2><?php esc_html_e('BBuilder tools', 'wp-bbuilder'); ?></h2>
                        <div class="wpbb-settings-tools">
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Blocks', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('All BBuilder blocks together.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Forms', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Bootstrap, validation, captcha.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('ACF', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Hero, Boot Card, Gallery.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Bootstrap classes', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Reusable class list for blocks.', 'wp-bbuilder'); ?></div></div>
                            <div class="wpbb-settings-tool"><strong><?php esc_html_e('Core controls', 'wp-bbuilder'); ?></strong><div><?php esc_html_e('Enable or disable selected core blocks.', 'wp-bbuilder'); ?></div></div>
                        </div>
                    </div>
                    <div class="wpbb-card" id="admin-scss">
                        <h2><?php esc_html_e('Admin style SCSS compiler', 'wp-bbuilder'); ?></h2>
                        <p><?php esc_html_e('Compile SCSS only for WordPress admin screens.', 'wp-bbuilder'); ?></p>
                        <p><label><?php esc_html_e('Admin SCSS', 'wp-bbuilder'); ?><br><textarea class="large-text code wpbb-code-editor wpbb-code-editor--scss" rows="12" data-wpbb-admin-scss-input><?php echo esc_textarea($opts['admin_scss'] ?? ''); ?></textarea></label></p>
                        <p>
                            <button type="button" class="button button-secondary" data-wpbb-admin-scss-build><?php esc_html_e('Build admin SCSS', 'wp-bbuilder'); ?></button>
                            <span class="wpbb-build-status" data-wpbb-admin-scss-status></span>
                        </p>
                        <p><label><?php esc_html_e('Compiled admin CSS preview', 'wp-bbuilder'); ?><br><textarea class="large-text code wpbb-code-editor wpbb-code-editor--css-output" rows="10" readonly data-wpbb-admin-css-preview><?php echo esc_textarea($opts['admin_compiled_css'] ?? ''); ?></textarea></label></p>
                        <input type="hidden" name="wpbb_settings[admin_scss]" value="<?php echo esc_attr($opts['admin_scss'] ?? ''); ?>" data-wpbb-admin-scss-hidden>
                        <input type="hidden" name="wpbb_settings[admin_compiled_css]" value="<?php echo esc_attr($opts['admin_compiled_css'] ?? ''); ?>" data-wpbb-admin-css-hidden>
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
                        <h3><?php esc_html_e('SMTP delivery', 'wp-bbuilder'); ?></h3>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[smtp_enabled]" value="1" <?php checked(!empty($opts['smtp_enabled'])); ?>><span><?php esc_html_e('Send form emails via SMTP', 'wp-bbuilder'); ?></span></label>
                        <p class="description"><?php esc_html_e('Helps form submissions avoid spam folders by using authenticated SMTP instead of the default PHP mail transport.', 'wp-bbuilder'); ?></p>
                        <p><label><?php esc_html_e('SMTP host', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[smtp_host]" value="<?php echo esc_attr($opts['smtp_host'] ?? ''); ?>"></label></p>
                        <div class="wpbb-subsettings">
                            <p><label><?php esc_html_e('SMTP port', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[smtp_port]" value="<?php echo esc_attr($opts['smtp_port'] ?? '587'); ?>"></label></p>
                            <p><label><?php esc_html_e('Encryption', 'wp-bbuilder'); ?><br>
                                <select name="wpbb_settings[smtp_encryption]">
                                    <option value="tls" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'tls'); ?>>TLS</option>
                                    <option value="ssl" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'ssl'); ?>>SSL</option>
                                    <option value="none" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'none'); ?>><?php esc_html_e('None', 'wp-bbuilder'); ?></option>
                                </select>
                            </label></p>
                        </div>
                        <p><label><?php esc_html_e('SMTP username', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[smtp_username]" value="<?php echo esc_attr($opts['smtp_username'] ?? ''); ?>" autocomplete="off"></label></p>
                        <p><label><?php esc_html_e('SMTP password', 'wp-bbuilder'); ?><br><input type="password" name="wpbb_settings[smtp_password]" value="<?php echo esc_attr($opts['smtp_password'] ?? ''); ?>" autocomplete="new-password"></label></p>
                        <div class="wpbb-subsettings">
                            <p><label><?php esc_html_e('From email', 'wp-bbuilder'); ?><br><input type="email" name="wpbb_settings[smtp_from_email]" value="<?php echo esc_attr($opts['smtp_from_email'] ?? ''); ?>"></label></p>
                            <p><label><?php esc_html_e('From name', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[smtp_from_name]" value="<?php echo esc_attr($opts['smtp_from_name'] ?? ''); ?>"></label></p>
                        </div>
                        <h3><?php esc_html_e('Spam protection', 'wp-bbuilder'); ?></h3>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[form_honeypot_enabled]" value="1" <?php checked(!empty($opts['form_honeypot_enabled'])); ?>><span><?php esc_html_e('Enable hidden honeypot + time trap', 'wp-bbuilder'); ?></span></label>
                        <p><label><?php esc_html_e('Minimum submit time (seconds)', 'wp-bbuilder'); ?><br><input type="number" min="0" step="1" name="wpbb_settings[form_min_submit_time]" value="<?php echo esc_attr($opts['form_min_submit_time'] ?? '3'); ?>"></label></p>
                        <p><label><?php esc_html_e('Spam blocked message', 'wp-bbuilder'); ?><br><input type="text" name="wpbb_settings[form_spam_message]" value="<?php echo esc_attr($opts['form_spam_message'] ?? __('Your submission was blocked as spam. Please try again.', 'wp-bbuilder')); ?>"></label></p>
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

                    <div class="wpbb-card" id="polylang-support">
                        <h2><?php esc_html_e('Polylang support', 'wp-bbuilder'); ?></h2>
                        <p class="description"><?php esc_html_e('Blocks use standard WordPress strings and content fields so Polylang can be used for translated pages. Dynamic form labels and ACF block content can be translated per page/language.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="datatable-options">
                        <h2><?php esc_html_e('DataTables defaults', 'wp-bbuilder'); ?></h2>
                        <p class="description"><?php esc_html_e('Bootstrap Table block supports searching, paging, ordering and responsive wrapper from block settings.', 'wp-bbuilder'); ?></p>
                    </div>

                    <div class="wpbb-card" id="spellcheck">
                        <h2><?php esc_html_e('Admin spellcheck', 'wp-bbuilder'); ?></h2>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[admin_spellcheck_enabled]" value="1" <?php checked(!empty($opts['admin_spellcheck_enabled'])); ?>><span><?php esc_html_e('Enable admin-side spellcheck assistance', 'wp-bbuilder'); ?></span></label>
                        <p class="description"><?php esc_html_e('Loads only in wp-admin for Gutenberg, ACF text fields, and visual editor content. Nothing is added to the frontend, keeping SEO and performance clean.', 'wp-bbuilder'); ?></p>
                        <p><label><?php esc_html_e('Default admin spellcheck language', 'wp-bbuilder'); ?><br>
                            <select name="wpbb_settings[admin_spellcheck_language]">
                                <?php foreach (WPBB_Spellcheck::get_supported_languages() as $code => $label): ?>
                                    <option value="<?php echo esc_attr($code); ?>" <?php selected(($opts['admin_spellcheck_language'] ?? 'en'), $code); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label></p>
                        <p class="description"><?php esc_html_e('Supported languages: English, Latvian, Estonian, Lithuanian, Polish, German, French, Spanish, Italian, Swedish, Finnish, Norwegian, Danish, Icelandic, Russian.', 'wp-bbuilder'); ?></p>
                        <p class="description"><?php esc_html_e('Uses browser and editor spellcheck support where available, with automatic lang and spellcheck attributes applied to admin-side fields.', 'wp-bbuilder'); ?></p>
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
                            'disable_core_buttons'=>'core/buttons','disable_core_button'=>'core/button','disable_core_query'=>'core/query'
                        ] as $setting => $label): ?>
                            <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[<?php echo esc_attr($setting); ?>]" value="1" <?php checked(!empty($opts[$setting])); ?>> <?php echo esc_html__('Disable ', 'wp-bbuilder') . esc_html($label); ?></label>
                        <?php endforeach; ?>
                        <h3><?php esc_html_e('Bootstrap optimization', 'wp-bbuilder'); ?></h3>
                        <p class="description"><?php esc_html_e('Untick CSS you do not need to reduce frontend weight and improve SEO/PageSpeed scores.', 'wp-bbuilder'); ?></p>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_bootstrap_css]" value="1" <?php checked(!empty($opts['load_bootstrap_css'])); ?>> <?php esc_html_e('Load Bootstrap CSS on frontend', 'wp-bbuilder'); ?></label>
                        <div class="wpbb-subsettings">
                            <p><label><?php esc_html_e('Bootstrap CSS mode', 'wp-bbuilder'); ?><br>
                                <select name="wpbb_settings[bootstrap_css_mode]">
                                    <option value="full" <?php selected(($opts['bootstrap_css_mode'] ?? 'grid'), 'full'); ?>><?php esc_html_e('Full library', 'wp-bbuilder'); ?></option>
                                    <option value="grid" <?php selected(($opts['bootstrap_css_mode'] ?? 'grid'), 'grid'); ?>><?php esc_html_e('Grid only', 'wp-bbuilder'); ?></option>
                                    <option value="utilities" <?php selected(($opts['bootstrap_css_mode'] ?? 'grid'), 'utilities'); ?>><?php esc_html_e('Utilities only', 'wp-bbuilder'); ?></option>
                                    <option value="custom" <?php selected(($opts['bootstrap_css_mode'] ?? 'grid'), 'custom'); ?>><?php esc_html_e('Custom parts', 'wp-bbuilder'); ?></option>
                                </select>
                            </label></p>
                            <div class="wpbb-setting-group">
                                <strong><?php esc_html_e('Custom Bootstrap CSS parts', 'wp-bbuilder'); ?></strong>
                                <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[bootstrap_css_components][]" value="reboot" <?php checked(in_array('reboot', (array)($opts['bootstrap_css_components'] ?? []), true)); ?>> <?php esc_html_e('Reboot / base reset', 'wp-bbuilder'); ?></label>
                                <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[bootstrap_css_components][]" value="grid" <?php checked(in_array('grid', (array)($opts['bootstrap_css_components'] ?? []), true)); ?>> <?php esc_html_e('Grid / containers / rows / columns', 'wp-bbuilder'); ?></label>
                                <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[bootstrap_css_components][]" value="utilities" <?php checked(in_array('utilities', (array)($opts['bootstrap_css_components'] ?? []), true)); ?>> <?php esc_html_e('Utilities helpers', 'wp-bbuilder'); ?></label>
                                <p class="description"><?php esc_html_e('Best for performance: keep only Grid + Utilities unless you need full Bootstrap component styling.', 'wp-bbuilder'); ?></p>
                            </div>
                        </div>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_shared_css]" value="1" <?php checked(!empty($opts['load_shared_css'])); ?>> <?php esc_html_e('Load BBuilder shared block CSS', 'wp-bbuilder'); ?></label>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_bootstrap_js]" value="1" <?php checked(!empty($opts['load_bootstrap_js'])); ?>> <?php esc_html_e('Load Bootstrap JS', 'wp-bbuilder'); ?></label>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[load_bootstrap_editor_css]" value="1" <?php checked(!empty($opts['load_bootstrap_editor_css'])); ?>> <?php esc_html_e('Load Bootstrap CSS inside block editor', 'wp-bbuilder'); ?></label>
                        <label class="wpbb-check"><input type="checkbox" name="wpbb_settings[force_bootstrap_enqueue]" value="1" <?php checked(!empty($opts['force_bootstrap_enqueue'])); ?>> <?php esc_html_e('Force Bootstrap on all frontend pages', 'wp-bbuilder'); ?></label>
                        <p class="description"><?php esc_html_e('Leave this off for best SEO/performance. Bootstrap will only load where BBuilder blocks need it.', 'wp-bbuilder'); ?></p>
                        <p><label>Admin max width<br><input type="text" name="wpbb_settings[admin_max_width]" value="<?php echo esc_attr($opts['admin_max_width']); ?>"></label></p>
                        <p><strong><?php esc_html_e('Container Width', 'wp-bbuilder'); ?></strong><br><span class="description"><?php esc_html_e('Container width is controlled by the active theme.', 'wp-bbuilder'); ?></span><br><a class="button button-secondary" href="<?php echo esc_url(wpbb_get_theme_settings_url()); ?>"><?php esc_html_e('Open Theme Settings', 'wp-bbuilder'); ?></a></p>
                    </div>

<div class="wpbb-card" id="scss-builder">
    <h2><?php esc_html_e('SCSS compiler', 'wp-bbuilder'); ?></h2>
    <p><?php esc_html_e('General SCSS compiler with AJAX build and minified CSS output.', 'wp-bbuilder'); ?></p>
    <p><label><?php esc_html_e('General SCSS', 'wp-bbuilder'); ?><br><textarea class="large-text code wpbb-code-editor wpbb-code-editor--scss" rows="16" name="wpbb_settings[custom_scss]"><?php echo esc_textarea($opts['custom_scss'] ?? ''); ?></textarea></label></p>
    <p><button type="button" class="button button-primary wpbb-build-scss"><?php esc_html_e('Build SCSS', 'wp-bbuilder'); ?></button> <span class="wpbb-build-status"></span></p>
    <p><label><?php esc_html_e('Compiled CSS', 'wp-bbuilder'); ?><br><textarea class="large-text code wpbb-code-editor wpbb-code-editor--css-output" rows="10" name="wpbb_settings[compiled_css]" readonly><?php echo esc_textarea($opts['compiled_css'] ?? ''); ?></textarea></label></p>
</div>


<div class="wpbb-card" id="redirects">
    <h2><?php esc_html_e('404 page redirects', 'wp-bbuilder'); ?></h2>
    <p class="description"><?php esc_html_e('Add optional redirects for pages or URLs that no longer exist.', 'wp-bbuilder'); ?></p>
    <div class="wpbb-repeatable" data-wpbb-redirects-builder>
        <div class="wpbb-repeatable__rows" data-wpbb-redirects-rows></div>
        <p class="wpbb-redirects-toolbar">
            <button type="button" class="button button-secondary" data-wpbb-add-redirect><?php esc_html_e('Add redirect rule', 'wp-bbuilder'); ?></button>
            <button type="submit" class="button button-primary"><?php esc_html_e('Save redirect rules', 'wp-bbuilder'); ?></button>
        </p>
        <textarea class="large-text code" rows="6" name="wpbb_settings[page_redirect_rules]" data-wpbb-redirects-input><?php echo esc_textarea($opts['page_redirect_rules'] ?? '[]'); ?></textarea>
        <p class="description"><?php esc_html_e('Use relative paths like /old-page or full URLs like http://wpbase.localhost/test-1/. Matching now works with both full URL and path, and no .htaccess update is needed.', 'wp-bbuilder'); ?></p>
    </div>
</div>

<div class="wpbb-card" id="runtime-code">
    <h2><?php esc_html_e('Meta header code', 'wp-bbuilder'); ?></h2>
    <p><textarea class="large-text code wpbb-code-editor wpbb-code-editor--html" rows="10" name="wpbb_settings[meta_header_code]"><?php echo esc_textarea($opts['meta_header_code'] ?? ''); ?></textarea></p>
    <h2><?php esc_html_e('Global footer code', 'wp-bbuilder'); ?></h2>
    <p><textarea class="large-text code wpbb-code-editor wpbb-code-editor--html" rows="10" name="wpbb_settings[global_footer_code]"><?php echo esc_textarea($opts['global_footer_code'] ?? ''); ?></textarea></p>
</div>

                </div>
                <?php submit_button(__('Save settings', 'wp-bbuilder')); ?>
            </form>
        </div>
        <?php
    }
    public function ajax_compile_scss() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Forbidden'], 403);
        }
        check_ajax_referer('wpbb_builder_nonce', 'nonce');
        $scss = isset($_POST['scss']) ? wp_unslash((string) $_POST['scss']) : '';
        try {
            $compiled = $this->simple_compile_scss($scss);
        } catch (Throwable $e) {
            wp_send_json_error(['message' => 'SCSS build failed: ' . $e->getMessage()], 500);
        }
        $opts = wp_parse_args(get_option('wpbb_settings', []), wpbb_defaults());
        $opts['custom_scss'] = $scss;
        $opts['compiled_css'] = $compiled;
        update_option('wpbb_settings', $opts);
        wp_send_json_success(['css' => $compiled]);
    }

    private function simple_compile_scss($scss) {
        $scss = trim((string) $scss);
        if ($scss === '') {
            return '';
        }

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

        $scss = preg_replace('/\s+/', ' ', $scss);

        $flatten = function ($source, $parent = '') use (&$flatten) {
            $css = '';
            $len = strlen($source);
            $i = 0;

            while ($i < $len) {
                while ($i < $len && ctype_space($source[$i])) $i++;
                if ($i >= $len) break;

                $selStart = $i;
                while ($i < $len && $source[$i] !== '{' && $source[$i] !== '}') $i++;
                if ($i >= $len || $source[$i] === '}') break;

                $selector = trim(substr($source, $selStart, $i - $selStart));
                $i++;

                $depth = 1;
                $bodyStart = $i;
                while ($i < $len && $depth > 0) {
                    if ($source[$i] === '{') $depth++;
                    if ($source[$i] === '}') $depth--;
                    $i++;
                }
                $body = trim(substr($source, $bodyStart, max(0, $i - $bodyStart - 1)));
                if ($selector === '') continue;

                $fullSelector = $parent
                    ? (strpos($selector, '&') !== false ? str_replace('&', $parent, $selector) : trim($parent . ' ' . $selector))
                    : $selector;

                $plain = preg_replace('/[^{}]+\{(?:[^{}]|\{[^{}]*\})*\}/', '', $body);
                $plain = trim((string) $plain);
                if ($plain !== '') {
                    $plain = preg_replace('/\s*;\s*/', ';', $plain);
                    $plain = preg_replace('/\s*:\s*/', ':', $plain);
                    $css .= $fullSelector . '{' . trim($plain, '; ') . '}';
                }

                if (strpos($body, '{') !== false) {
                    $css .= $flatten($body, $fullSelector);
                }
            }
            return $css;
        };

        $result = $flatten($scss, '');
        if ($result === '') {
            $result = trim($scss);
        }

        $result = preg_replace('/\s+/', ' ', $result);
        $result = str_replace([' {', '{ ', '; ', ': ', ', ', ' }'], ['{', '{', ';', ':', ',', '}'], $result);

        return trim($result);
    }
}
