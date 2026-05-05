<?php
/**
 * Gutenberg blocks functions
 */

// Define plugin constants
define('GANB_VERSION', '1.0.0');
define('GANB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GANB_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main plugin class
 */
class Gutenberg_Add_New_Button {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('gutenberg-add-new-button', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Add the button only in admin
        if (is_admin()) {
            add_action('enqueue_block_editor_assets', array($this, 'add_gutenberg_new_page_button'));
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '5.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires WordPress 5.0 or higher.', 'gutenberg-add-new-button'));
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup if needed
    }
    
    /**
     * Add the new page button to Gutenberg editor
     */
    public function add_gutenberg_new_page_button() {
        // Get current screen
        $screen = get_current_screen();
        if (!$screen || !$screen->is_block_editor()) {
            return;
        }

        // Get and sanitize post type
        $post_type = sanitize_key($screen->post_type);
        
        // Check if post type exists and is valid
        if (!post_type_exists($post_type)) {
            return;
        }

        // Get post type object for proper labeling
        $post_type_obj = get_post_type_object($post_type);
        $type_label = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst($post_type);

        // Register script with all necessary dependencies
        wp_register_script(
            'gutenberg-add-new-button',
            '',
            array(
                'wp-blocks',
                'wp-element',
                'wp-editor',
                'wp-components',
                'wp-plugins'
            ),
            GANB_VERSION
        );

        // Localize script with escaped data
        wp_localize_script('gutenberg-add-new-button', 'ganbData', array(
            'postType' => $post_type,
            'newUrl' => esc_url(admin_url("post-new.php?post_type={$post_type}")),
            'buttonText' => sprintf(
                /* translators: %s: Post type label */
                esc_html__('New %s', 'gutenberg-add-new-button'),
                esc_html($type_label)
            ),
            'confirmMessage' => esc_html__('You have unsaved changes. Do you want to leave anyway?', 'gutenberg-add-new-button')
        ));

        wp_add_inline_script('gutenberg-add-new-button', '
            window.addEventListener("load", function() {
                const { createElement } = wp.element;
                const { Button } = wp.components;
                
                function NewPageButton() {
                    return createElement(
                        "div",
                        {
                            className: "ganb-button-wrapper",
                            style: {
                                display: "inline-block",
                                marginLeft: "8px"
                            }
                        },
                        createElement(
                            Button,
                            {
                                icon: "plus",
                                isPrimary: true,
                                onClick: () => {
                                    // Check if there are unsaved changes
                                    const isDirty = wp.data.select("core/editor").isEditedPostDirty();
                                    
                                    if (isDirty && !window.confirm(ganbData.confirmMessage)) {
                                        return;
                                    }
                                    
                                    window.location.href = ganbData.newUrl;
                                },
                                className: "ganb-button",
                                style: {
                                    height: "32px",
                                    position: "relative"
                                }
                            },
                            ganbData.buttonText
                        )
                    );
                }

                function insertButton() {
                    const toolbar = document.querySelector(".edit-post-header-toolbar");
                    if (!toolbar || document.querySelector(".ganb-button-container")) {
                        return;
                    }
                
                    const container = document.createElement("div");
                    container.className = "ganb-button-container";
                    toolbar.insertBefore(container, toolbar.children[1]);
                
                    const { createElement } = wp.element;
                    const { Button } = wp.components;
                
                    // "Add New WP" button
                    const newWPBtn = createElement(
                        Button,
                        {
                            icon: "plus",
                            isPrimary: true,
                            onClick: () => {
                                const isDirty = wp.data.select("core/editor").isEditedPostDirty();
                                if (isDirty && !window.confirm(ganbData.confirmMessage)) return;
                                window.location.href = ganbData.newUrl;
                            },
                            className: "ganb-button",
                            style: {
                                height: "32px",
                                marginRight: "4px",
                                marginBottom: "2px"
                            }
                        },
                        ganbData.buttonText
                    );
                
                    // WP Templates button
                    const templatesBtn = createElement(
                        Button,
                        {
                            icon: "layout",
                            isPrimary: true,
                            className: "templates-button components-button components-toolbar__control",
                            onClick: () => {
                                const inserterBtn = document.querySelector(\'.editor-document-tools__inserter-toggle\');
                                if (!inserterBtn) return;
                            
                                inserterBtn.click();
                            
                                const waitForElement = (selector, maxAttempts = 20, delay = 300) => {
                                    return new Promise((resolve) => {
                                        let attempts = 0;
                                        const interval = setInterval(() => {
                                            const el = document.querySelector(selector);
                                            if (el) {
                                                clearInterval(interval);
                                                resolve(el);
                                            } else if (++attempts >= maxAttempts) {
                                                clearInterval(interval);
                                                resolve(null);
                                            }
                                        }, delay);
                                    });
                                };
                            
                                (async () => {
                                    const tablist = await waitForElement(\'.block-editor-tabbed-sidebar__tablist\');
                                    if (tablist && tablist.children.length > 1) {
                                        tablist.children[1].click();
                                    }
                                    
                                    const WPTab = await waitForElement(\'.block-editor-inserter__category-tablist \');
                                    if (WPTab && WPTab.children.length > 4) {
                                        WPTab.children[2].click();
                                    }
                                })();
                            },

                            style: {
                                height: "32px",
                                marginRight: "4px",
                                marginBottom: "2px"
                            }
                        },
                        "WP Templates"
                    );
                
                    const buttonWrapper = createElement(
                        "div",
                        { className: "ganb-button-wrapper" },
                        newWPBtn,
                        templatesBtn
                    );
                
                    wp.element.render(buttonWrapper, container);
                }


                // Initial insertion
                insertButton();

                // Watch for toolbar changes with debouncing
                let debounceTimeout;
                const observer = new MutationObserver(function() {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => {
                        if (!document.querySelector(".ganb-button-container")) {
                            insertButton();
                        }
                    }, 100);
                });

                // Start observing with error handling
                try {
                    const observerTarget = document.querySelector(".interface-interface-skeleton__header") || document.body;
                    observer.observe(observerTarget, {
                        subtree: true,
                        childList: true
                    });
                } catch (error) {
                    console.error("GANB Error:", error);
                }

                // Cleanup on page unload
                window.addEventListener("unload", function() {
                    observer.disconnect();
                    clearTimeout(debounceTimeout);
                });
            });
        ');
        wp_enqueue_script('gutenberg-add-new-button');

        // Add custom CSS
        wp_add_inline_style('wp-edit-post', '
            .ganb-button-container {
                display: inline-block;
            }
            .ganb-button-wrapper {
                position: relative;
            }
            .ganb-button.components-button.is-primary {
                display: inline-flex !important;
                align-items: center !important;
                padding-left: 8px !important;
            }
            .ganb-button.components-button.is-primary svg {
                margin-top: 2px;
                margin-right: 4px;
            }
        ');
    }
}

// Initialize the plugin
new Gutenberg_Add_New_Button();
