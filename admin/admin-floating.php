<?php
/**
 * Floating Admin Settings Panel
 * Replaces traditional settings page with slide-out panel
 */

if (!defined('ABSPATH')) exit;

class WPBBuilder_Floating_Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu'], 1);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_wpbb_quick_save', [$this, 'ajax_save']);
        add_action('admin_footer', [$this, 'render_panel']);
    }
    
    public function register_menu() {
        // Top-level compact menu
        add_menu_page(
            'BBuilder',
            'BBuilder',
            'manage_options',
            'wp-bbuilder',
            [$this, 'render_main_page'],
            'dashicons-editor-kitchen-sink',
            3 // High priority
        );
        
        // Hidden submenu pages for settings sections
        add_submenu_page('wp-bbuilder', 'Settings', 'Settings', 'manage_options', 'wp-bbuilder');
        add_submenu_page('wp-bbuilder', 'Blocks', 'Blocks', 'manage_options', 'wp-bbuilder-blocks', [$this, 'render_blocks_page']);
    }
    
    public function enqueue_assets($hook) {
        if (strpos($hook, 'wp-bbuilder') === false) return;
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_add_inline_style('wp-admin', '
            /* Floating Panel CSS */
            #wpbb-floating-panel {
                position: fixed;
                top: 32px;
                right: -400px;
                width: 380px;
                height: calc(100vh - 32px);
                background: #f0f0f1;
                border-left: 1px solid #c3c4c7;
                box-shadow: -2px 0 12px rgba(0,0,0,0.15);
                z-index: 9999;
                transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            #wpbb-floating-panel.wpbb-open { right: 0; }
            
            .wpbb-panel-header {
                background: #2271b1;
                color: #fff;
                padding: 0 16px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 14px;
                font-weight: 600;
            }
            
            .wpbb-toggle-btn {
                position: fixed;
                top: 35px;
                right: 0;
                background: #2271b1;
                color: #fff;
                border: none;
                padding: 10px 14px;
                border-radius: 4px 0 0 4px;
                cursor: pointer;
                z-index: 10000;
                box-shadow: -2px 2px 8px rgba(0,0,0,0.2);
                transition: right 0.3s;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                font-weight: 500;
            }
            .wpbb-toggle-btn.wpbb-open { right: 380px; }
            .wpbb-toggle-btn:hover { background: #135e96; }
            
            .wpbb-panel-content {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
            }
            
            .wpbb-section {
                background: #fff;
                border-radius: 6px;
                margin-bottom: 16px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            
            .wpbb-section-title {
                padding: 12px 16px;
                margin: 0;
                font-size: 13px;
                font-weight: 600;
                color: #1d2327;
                background: #f6f7f7;
                border-bottom: 1px solid #dcdcde;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                user-select: none;
            }
            .wpbb-section-title:hover { background: #f0f0f1; }
            .wpbb-section-title::after {
                content: "▼";
                font-size: 10px;
                transition: transform 0.2s;
            }
            .wpbb-section.wpbb-collapsed .wpbb-section-title::after { transform: rotate(-90deg); }
            
            .wpbb-section-content {
                padding: 16px;
                display: none;
            }
            .wpbb-section:not(.wpbb-collapsed) .wpbb-section-content { display: block; }
            
            .wpbb-field {
                margin-bottom: 16px;
            }
            .wpbb-field:last-child { margin-bottom: 0; }
            
            .wpbb-field label {
                display: block;
                margin-bottom: 6px;
                font-size: 12px;
                font-weight: 500;
                color: #3c434a;
            }
            
            .wpbb-field input[type="text"],
            .wpbb-field input[type="email"],
            .wpbb-field input[type="url"],
            .wpbb-field input[type="password"],
            .wpbb-field select,
            .wpbb-field textarea {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #8c8f94;
                border-radius: 4px;
                font-size: 13px;
                line-height: 1.5;
                box-sizing: border-box;
            }
            .wpbb-field input:focus,
            .wpbb-field select:focus {
                border-color: #2271b1;
                box-shadow: 0 0 0 1.5px #2271b1;
                outline: none;
            }
            
            .wpbb-field-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }
            
            .wpbb-color-field {
                display: flex;
                gap: 8px;
            }
            .wpbb-color-field input { flex: 1; }
            .wpbb-color-preview {
                width: 36px;
                height: 36px;
                border-radius: 4px;
                border: 1px solid #8c8f94;
                cursor: pointer;
                flex-shrink: 0;
            }
            
            .wpbb-switch {
                position: relative;
                display: inline-block;
                width: 44px;
                height: 24px;
            }
            .wpbb-switch input { opacity: 0; width: 0; height: 0; }
            .wpbb-slider {
                position: absolute;
                cursor: pointer;
                top: 0; left: 0; right: 0; bottom: 0;
                background-color: #ccc;
                transition: .3s;
                border-radius: 24px;
            }
            .wpbb-slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s;
                border-radius: 50%;
            }
            input:checked + .wpbb-slider { background-color: #2271b1; }
            input:checked + .wpbb-slider:before { transform: translateX(20px); }
            
            .wpbb-quick-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 16px;
            }
            .wpbb-quick-btn {
                background: #fff;
                border: 1px solid #c3c4c7;
                padding: 12px;
                border-radius: 6px;
                text-align: center;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 12px;
                color: #3c434a;
            }
            .wpbb-quick-btn:hover {
                border-color: #2271b1;
                color: #2271b1;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .wpbb-quick-btn .dashicons {
                display: block;
                font-size: 24px;
                margin-bottom: 6px;
                color: #2271b1;
            }
            
            .wpbb-save-bar {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                padding: 12px 16px;
                border-top: 1px solid #dcdcde;
                display: flex;
                gap: 10px;
                align-items: center;
            }
            
            .wpbb-btn-primary {
                background: #2271b1;
                color: #fff;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 500;
                flex: 1;
            }
            .wpbb-btn-primary:hover { background: #135e96; }
            .wpbb-btn-secondary {
                background: #f6f7f7;
                color: #3c434a;
                border: 1px solid #c3c4c7;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13px;
            }
            
            .wpbb-status-toast {
                position: fixed;
                bottom: 80px;
                right: 400px;
                background: #00a32a;
                color: #fff;
                padding: 12px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.3s;
                z-index: 10001;
                font-size: 13px;
                font-weight: 500;
            }
            .wpbb-status-toast.wpbb-show { opacity: 1; transform: translateY(0); }
            .wpbb-status-toast.wpbb-error { background: #d63638; }
            
            @media screen and (max-width: 782px) {
                #wpbb-floating-panel { top: 46px; height: calc(100vh - 46px); width: 320px; }
                #wpbb-floating-panel.wpbb-open { right: 0; }
                .wpbb-toggle-btn { top: 50px; }
                .wpbb-toggle-btn.wpbb-open { right: 320px; }
            }
        ');
        
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Toggle panel
                $(".wpbb-toggle-btn, .wpbb-panel-close").on("click", function() {
                    $("#wpbb-floating-panel").toggleClass("wpbb-open");
                    $(".wpbb-toggle-btn").toggleClass("wpbb-open");
                    $("body").toggleClass("wpbb-panel-open");
                });
                
                // Accordion sections
                $(".wpbb-section-title").on("click", function() {
                    $(this).closest(".wpbb-section").toggleClass("wpbb-collapsed");
                });
                
                // Keep first section open
                $(".wpbb-section").first().removeClass("wpbb-collapsed");
                
                // Color picker integration
                $(".wpbb-color-picker").wpColorPicker({
                    change: function(e, ui) {
                        $(this).closest(".wpbb-color-field").find(".wpbb-color-preview")
                            .css("background-color", ui.color.toString());
                    }
                });
                
                // Quick toggle switches
                $(".wpbb-switch-input").on("change", function() {
                    var setting = $(this).data("setting");
                    var value = $(this).is(":checked") ? "1" : "0";
                    quickSave(setting, value);
                });
                
                // Form auto-save
                var saveTimeout;
                $(".wpbb-auto-save").on("input", function() {
                    clearTimeout(saveTimeout);
                    var $input = $(this);
                    saveTimeout = setTimeout(function() {
                        var setting = $input.attr("name");
                        var value = $input.val();
                        quickSave(setting, value);
                    }, 800);
                });
                
                function quickSave(key, value) {
                    $.post(ajaxurl, {
                        action: "wpbb_quick_save",
                        key: key,
                        value: value,
                        nonce: "' . wp_create_nonce('wpbb_admin_nonce') . '"
                    }, function(response) {
                        showToast(response.success ? "Saved automatically" : "Error saving", !response.success);
                    });
                }
                
                function showToast(msg, isError) {
                    var $toast = $(".wpbb-status-toast");
                    $toast.text(msg).toggleClass("wpbb-error", isError).addClass("wpbb-show");
                    setTimeout(function() { $toast.removeClass("wpbb-show"); }, 2000);
                }
                
                // Save all button
                $("#wpbb-save-all").on("click", function() {
                    var $btn = $(this).text("Saving...");
                    var data = $("#wpbb-settings-form").serialize();
                    
                    $.post(ajaxurl, data + "&action=wpbb_quick_save&nonce=' . wp_create_nonce('wpbb_admin_nonce') . '", function(response) {
                        $btn.text("Save All Changes");
                        showToast(response.success ? "All changes saved" : "Error saving", !response.success);
                    });
                });
            });
        ');
    }
    
    public function ajax_save() {
        check_ajax_referer('wpbb_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
        
        $key = sanitize_text_field($_POST['key'] ?? '');
        $value = sanitize_textarea_field($_POST['value'] ?? '');
        
        if ($key) {
            update_option('wpbb_' . $key, $value);
        }
        
        wp_send_json_success();
    }
    
    public function render_main_page() {
        echo '<div class="wrap"><h1>WP BBuilder</h1><p>Use the floating panel on the right to configure settings.</p></div>';
    }
    
    public function render_blocks_page() {
        echo '<div class="wrap"><h1>Block Settings</h1><p>Manage individual block settings from the floating panel.</p></div>';
    }
    
    public function render_panel() {
        if (!$this->is_bbuilder_page()) return;
        
        $opts = [
            'load_bootstrap_css' => get_option('wpbb_load_bootstrap_css', '1'),
            'load_bootstrap_js' => get_option('wpbb_load_bootstrap_js', '1'),
            'primary_color' => get_option('wpbb_primary_color', '#0d6efd'),
            'whatsapp_phone' => get_option('wpbb_whatsapp_phone', ''),
            'recaptcha_site' => get_option('wpbb_recaptcha_site_key', ''),
            'enable_cookie' => get_option('wpbb_enable_cookie_banner', '0'),
        ];
        ?>
        
        <button class="wpbb-toggle-btn" type="button">
            <span class="dashicons dashicons-admin-generic"></span>
            <span>BBuilder</span>
        </button>
        
        <div id="wpbb-floating-panel">
            <div class="wpbb-panel-header">
                <span>⚡ BBuilder Settings</span>
                <button class="wpbb-panel-close" style="background:none;border:none;color:#fff;cursor:pointer;padding:4px;">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            
            <div class="wpbb-panel-content">
                <form id="wpbb-settings-form">
                    
                    <!-- Quick Actions -->
                    <div class="wpbb-quick-actions">
                        <div class="wpbb-quick-btn" onclick="window.location='<?php echo admin_url('admin.php?page=wp-bbuilder-blocks'); ?>'">
                            <span class="dashicons dashicons-block-default"></span>
                            Manage Blocks
                        </div>
                        <div class="wpbb-quick-btn" onclick="window.open('<?php echo home_url(); ?>','_blank')">
                            <span class="dashicons dashicons-welcome-view-site"></span>
                            View Site
                        </div>
                    </div>
                    
                    <!-- General Settings -->
                    <div class="wpbb-section">
                        <h3 class="wpbb-section-title">General</h3>
                        <div class="wpbb-section-content">
                            <div class="wpbb-field">
                                <label style="display:flex;justify-content:space-between;align-items:center;">
                                    Load Bootstrap CSS
                                    <label class="wpbb-switch">
                                        <input type="checkbox" class="wpbb-switch-input" data-setting="load_bootstrap_css" <?php checked($opts['load_bootstrap_css'], '1'); ?>>
                                        <span class="wpbb-slider"></span>
                                    </label>
                                </label>
                            </div>
                            <div class="wpbb-field">
                                <label style="display:flex;justify-content:space-between;align-items:center;">
                                    Load Bootstrap JS
                                    <label class="wpbb-switch">
                                        <input type="checkbox" class="wpbb-switch-input" data-setting="load_bootstrap_js" <?php checked($opts['load_bootstrap_js'], '1'); ?>>
                                        <span class="wpbb-slider"></span>
                                    </label>
                                </label>
                            </div>
                            <div class="wpbb-field">
                                <label>Primary Brand Color</label>
                                <div class="wpbb-color-field">
                                    <input type="text" name="primary_color" class="wpbb-color-picker wpbb-auto-save" value="<?php echo esc_attr($opts['primary_color']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Integrations -->
                    <div class="wpbb-section wpbb-collapsed">
                        <h3 class="wpbb-section-title">Integrations</h3>
                        <div class="wpbb-section-content">
                            <div class="wpbb-field">
                                <label>WhatsApp Phone Number</label>
                                <input type="text" name="whatsapp_phone" class="wpbb-auto-save" value="<?php echo esc_attr($opts['whatsapp_phone']); ?>" placeholder="+1234567890">
                            </div>
                            <div class="wpbb-field-row">
                                <div class="wpbb-field">
                                    <label>ReCaptcha Site Key</label>
                                    <input type="text" name="recaptcha_site_key" class="wpbb-auto-save" value="<?php echo esc_attr($opts['recaptcha_site']); ?>">
                                </div>
                                <div class="wpbb-field">
                                    <label>Secret Key</label>
                                    <input type="password" name="recaptcha_secret_key" class="wpbb-auto-save" value="<?php echo esc_attr(get_option('wpbb_recaptcha_secret_key', '')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <div class="wpbb-section wpbb-collapsed">
                        <h3 class="wpbb-section-title">Features</h3>
                        <div class="wpbb-section-content">
                            <div class="wpbb-field">
                                <label style="display:flex;justify-content:space-between;align-items:center;">
                                    Cookie Consent Banner
                                    <label class="wpbb-switch">
                                        <input type="checkbox" class="wpbb-switch-input" data-setting="enable_cookie_banner" <?php checked($opts['enable_cookie'], '1'); ?>>
                                        <span class="wpbb-slider"></span>
                                    </label>
                                </label>
                            </div>
                            <div class="wpbb-field">
                                <label style="display:flex;justify-content:space-between;align-items:center;">
                                    Disable Core Blocks
                                    <label class="wpbb-switch">
                                        <input type="checkbox" class="wpbb-switch-input" data-setting="disable_core_blocks" <?php checked(get_option('wpbb_disable_core_blocks'), '1'); ?>>
                                        <span class="wpbb-slider"></span>
                                    </label>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            
            <div class="wpbb-save-bar">
                <button type="button" id="wpbb-save-all" class="wpbb-btn-primary">Save All Changes</button>
                <button type="button" class="wpbb-btn-secondary wpbb-panel-close">Close</button>
            </div>
        </div>
        
        <div class="wpbb-status-toast">Settings saved</div>
        
        <?php
    }
    
    private function is_bbuilder_page() {
        global $pagenow;
        if ($pagenow !== 'admin.php') return false;
        return isset($_GET['page']) && strpos($_GET['page'], 'wp-bbuilder') !== false;
    }
}

// Initialize
new WPBBuilder_Floating_Admin();