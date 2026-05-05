<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('wp_theme_style_defaults')) {
    function wp_theme_style_defaults() {
        return [
            'theme_brand_color'          => '#d21629',
            'theme_accent_color'         => '#4a4549',
            'theme_text_color'           => '#333333',
            'theme_heading_color'        => '#000000',
            'theme_background_color'     => '#ffffff',
            'theme_surface_color'        => '#F2F3F3',
            'theme_surface_alt_color'    => '#EDEDED',
            'theme_border_color'         => '#d9dde3',
            'theme_grey_dark_color'      => '#4a4549',
            'theme_grey_light_color'     => '#EDEDED',
            'theme_success_color'        => '#48C52C',
            'theme_warning_color'        => '#f59e0b',
            'theme_danger_color'         => '#FF0000',
            'theme_info_color'           => '#2563eb',
            'theme_link_color'           => '#d21629',
            'theme_link_hover_color'     => '#a61222',
            'theme_container_width'      => '1200px',
            'theme_content_width'        => '840px',
            'theme_wide_width'           => '1280px',
            'theme_gutter_width'         => '1.5rem',
            'theme_section_spacing'      => 'clamp(2rem, 4vw, 5rem)',
            'theme_radius'               => '18px',

            'media_glightbox'            => 'false',
            'select2_js'                 => 'false',
            'alpine_js'                  => 'false',
            'theme_login_logo_enabled'   => 0,
            'theme_login_logo'           => '',
            'theme_login_logo_width'     => '160',
            'theme_login_logo_height'    => '80',

            'theme_general_cta_text'     => '',
            'theme_general_cta_url'      => '',
            'theme_general_notice'       => '',

            'theme_font_provider'        => 'system',
            'theme_body_font'            => "'Albert Sans', sans-serif",
            'theme_heading_font'         => "'Albert Sans', sans-serif",
            'theme_ui_font'              => "'Albert Sans', sans-serif",
            'theme_google_body_family'   => 'Albert Sans',
            'theme_google_heading_family'=> 'Albert Sans',
            'theme_google_ui_family'     => 'Albert Sans',
            'theme_google_body_weights'  => '300;400;500;600;700',
            'theme_google_heading_weights'=> '400;500;600;700;800',
            'theme_google_ui_weights'    => '400;500;600;700',
            'theme_custom_font_import_url_1' => '',
            'theme_custom_font_import_url_2' => '',
            'theme_custom_font_import_url_3' => '',
            'theme_body_weight'          => '400',
            'theme_body_weight_medium'   => '500',
            'theme_heading_weight'       => '700',
            'theme_heading_weight_light' => '500',
            'theme_ui_weight'            => '500',
            'theme_ui_weight_bold'       => '700',
            'theme_font_variables'       => [],

            'theme_small_size'           => '14px',
            'theme_small_size_tablet'    => '13px',
            'theme_small_size_mobile'    => '12px',
            'theme_body_size'            => '16px',
            'theme_body_size_tablet'     => '15px',
            'theme_body_size_mobile'     => '14px',
            'theme_large_size'           => '20px',
            'theme_large_size_tablet'    => '18px',
            'theme_large_size_mobile'    => '16px',
            'theme_h1_size'              => '45px',
            'theme_h1_size_tablet'       => '38px',
            'theme_h1_size_mobile'       => '32px',
            'theme_h2_size'              => '30px',
            'theme_h2_size_tablet'       => '26px',
            'theme_h2_size_mobile'       => '22px',
            'theme_h3_size'              => '20px',
            'theme_h3_size_tablet'       => '18px',
            'theme_h3_size_mobile'       => '17px',
            'theme_h4_size'              => '16px',
            'theme_h4_size_tablet'       => '15px',
            'theme_h4_size_mobile'       => '15px',
            'theme_h5_size'              => '16px',
            'theme_h5_size_tablet'       => '15px',
            'theme_h5_size_mobile'       => '14px',
            'theme_h6_size'              => '12px',
            'theme_h6_size_tablet'       => '12px',
            'theme_h6_size_mobile'       => '11px',

            'theme_custom_colors'        => [],

            'theme_anim_enabled'         => 1,
            'theme_anim_default_class'   => 'animate__fadeInUp',
            'theme_anim_duration'        => '1s',
            'theme_anim_delay'           => '0s',
            'theme_anim_repeat'          => '1',
            'theme_anim_disable_mobile'  => 0,
            'theme_anim_reduce_motion'   => 1,
            'theme_anim_custom_class'    => '',
            'theme_anim_preview_text'    => 'Animation preview',

            'theme_motion_enable_lottie' => 0,
            'theme_motion_lottie_url'    => '',
            'theme_motion_lottie_width'  => '240px',
            'theme_motion_lottie_height' => '240px',
            'theme_motion_lottie_speed'  => '1',
            'theme_motion_lottie_loop'   => 1,
            'theme_motion_lottie_autoplay' => 1,
            'theme_motion_enable_svg_motion' => 0,
            'theme_motion_svg_class'     => 'is-animated-svg',

            'theme_enable_booking_cpt'    => 0,
            'theme_enable_event_cpt'      => 0,
            'theme_enable_products_cpt'   => 0,
            'theme_enable_case_study_cpt' => 0,
            'theme_enable_testimonial_cpt'=> 0,
            'theme_enable_megamenu_cpt'   => 0,
            'theme_smart_library_loading' => 1,
            'theme_disable_theme_js_home' => 0,
            'theme_disable_child_js_home' => 0,
            'theme_disable_block_css_home' => 0,
            'theme_disable_global_styles_home' => 0,
            'theme_disable_dashicons_front' => 1,
            'theme_disable_wp_embed_front' => 1,
            'theme_booking_auto_reply'    => 0,
            'theme_booking_auto_reply_subject' => 'We received your booking request',
            'theme_booking_auto_reply_message' => 'Thank you. We will reply soon.',
            'theme_booking_delete_canceled' => 0,
            'theme_booking_delete_old' => 0,
            'theme_booking_delete_old_days' => 30,
            'theme_dev_show_borders'      => 0,
            'theme_dev_show_spacing'      => 0,
            'theme_dev_show_typography'   => 0,
            'theme_dev_show_colors'       => 0,
            'theme_dev_pixel_perfect'     => 0,
            'theme_dev_mockup_image'      => '',
            'theme_dev_mockup_opacity'    => 35,
            'perf_cache_headers'          => 0,
            'perf_object_cache'           => 0,
            'perf_object_cache_driver'    => 'auto',
            'perf_object_cache_ttl'       => 600,
            'perf_html_minify'            => 0,
            'perf_auto_critical_css'      => 0,
            'perf_search_thumbnails'      => 1,
            'perf_search_enhanced'        => 1,

            'theme_media_convert_to_avif' => 1,
            'theme_media_max_width'      => '1920',
            'theme_media_quality'        => '82',
            'theme_media_size_sm'        => '480',
            'theme_media_size_md'        => '960',
            'theme_media_size_lg'        => '1600',
            'theme_media_size_xl'        => '1920',
        ];
    }
}

if (!function_exists('wp_theme_style_tokens')) {
    function wp_theme_style_tokens() {
        $defaults = wp_theme_style_defaults();
        $tokens = [];
        foreach ($defaults as $key => $default) {
            $value = function_exists('get_field') ? get_field($key, 'option') : null;
            $tokens[$key] = ($value !== null && $value !== false && $value !== '') ? $value : $default;
        }
        foreach (['theme_custom_colors', 'theme_font_variables'] as $array_key) {
            if (!is_array($tokens[$array_key])) {
                $tokens[$array_key] = [];
            }
        }
        return $tokens;
    }
}

if (!function_exists('wp_theme_register_style_options_page')) {
    function wp_theme_register_style_options_page() {
        if (!function_exists('acf_add_options_sub_page')) {
            return;
        }

        acf_add_options_sub_page([
            'page_title'  => __('Theme Settings', 'wp-theme'),
            'menu_title'  => __('Theme Settings', 'wp-theme'),
            'menu_slug'   => 'wp-theme-settings',
            'parent_slug' => 'options-general.php',
            'capability'  => 'edit_theme_options',
            'redirect'    => false,
        ]);
    }
}
add_action('acf/init', 'wp_theme_register_style_options_page', 5);

if (!function_exists('wp_theme_style_size_triplet_fields')) {
    function wp_theme_style_size_triplet_fields($slug_prefix, $label, $desktop, $tablet, $mobile) {
        $safe = sanitize_title($slug_prefix);
        return [
            [
                'key' => 'field_' . $safe . '_label',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => '<strong>' . esc_html($label) . '</strong>',
                'wrapper' => ['width' => '22', 'class' => 'wp-theme-size-row-label'],
                'esc_html' => 0,
            ],
            [
                'key' => 'field_' . $safe . '_desktop',
                'label' => '',
                'name' => $slug_prefix,
                'type' => 'text',
                'default_value' => $desktop,
                'placeholder' => 'Desktop',
                'wrapper' => ['width' => '26', 'class' => 'wp-theme-size-row wp-theme-size-desktop'],
                'prepend' => 'Desktop',
            ],
            [
                'key' => 'field_' . $safe . '_tablet',
                'label' => '',
                'name' => $slug_prefix . '_tablet',
                'type' => 'text',
                'default_value' => $tablet,
                'placeholder' => 'Tablet',
                'wrapper' => ['width' => '26', 'class' => 'wp-theme-size-row wp-theme-size-tablet'],
                'prepend' => 'Tablet',
            ],
            [
                'key' => 'field_' . $safe . '_mobile',
                'label' => '',
                'name' => $slug_prefix . '_mobile',
                'type' => 'text',
                'default_value' => $mobile,
                'placeholder' => 'Mobile',
                'wrapper' => ['width' => '26', 'class' => 'wp-theme-size-row wp-theme-size-mobile'],
                'prepend' => 'Mobile',
            ],
        ];
    }
}



if (!function_exists('wp_theme_admin_cpt_url')) {
    function wp_theme_admin_cpt_url($preferred, $fallbacks = []) {
        $slugs = array_merge((array) $preferred, (array) $fallbacks);
        foreach ($slugs as $slug) {
            if ($slug && post_type_exists($slug)) {
                return admin_url('edit.php?post_type=' . $slug);
            }
        }
        $first = reset($slugs);
        return admin_url('edit.php?post_type=' . $first);
    }
}

if (!function_exists('wp_theme_general_tools_markup')) {
    function wp_theme_general_tools_markup() {
        $items = [];

        if ((bool) wp_theme_acf_get('theme_enable_booking_cpt', 'option', 0)) {
            $items[] = ['label' => 'Bookings', 'url' => admin_url('edit.php?post_type=theme_booking')];
        }

        if ((bool) wp_theme_acf_get('theme_enable_event_cpt', 'option', 0)) {
            $items[] = ['label' => 'Events', 'url' => admin_url('edit.php?post_type=event')];
        }

        if ((bool) wp_theme_acf_get('theme_enable_products_cpt', 'option', 0)) {
            $items[] = ['label' => 'Products', 'url' => admin_url('edit.php?post_type=products')];
        }

        if ((bool) wp_theme_acf_get('theme_enable_case_study_cpt', 'option', 0)) {
            $items[] = ['label' => 'Case Studies', 'url' => admin_url('edit.php?post_type=case-study')];
        }

        if ((bool) wp_theme_acf_get('theme_enable_testimonial_cpt', 'option', 0)) {
            $items[] = ['label' => 'Testimonials', 'url' => admin_url('edit.php?post_type=testimonial')];
        }

        if ((bool) wp_theme_acf_get('theme_enable_megamenu_cpt', 'option', 0)) {
            $items[] = ['label' => 'Megamenu', 'url' => admin_url('edit.php?post_type=megamenu')];
        }

        $items[] = ['label' => 'Pages Order', 'url' => admin_url('edit.php?post_type=page')];
        $items[] = ['label' => 'Posts Order', 'url' => admin_url('edit.php')];
        $items[] = ['label' => 'Documentation', 'url' => admin_url('admin.php?page=wp-theme-docs')];

        $html = '<div class="wp-theme-general-tools">';
        foreach ($items as $item) {
            $html .= '<a class="button button-secondary" target="_blank" rel="noopener" href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
        }
        $html .= '</div>';
        $html .= '<p class="description">Compact on/off switches live above. Use these shortcuts after enabling the matching function.</p>';
        return $html;
    }
}


if (!function_exists('wp_theme_docs_register_page')) {
    function wp_theme_docs_register_page() {
        add_submenu_page(
            'wp-theme-settings',
            __('Theme Documentation', 'wp-theme'),
            __('Documentation', 'wp-theme'),
            'edit_theme_options',
            'wp-theme-docs',
            'wp_theme_docs_render_page'
        );
    }
}
add_action('admin_menu', 'wp_theme_docs_register_page', 30);

if (!function_exists('wp_theme_docs_markdown_to_html')) {
    function wp_theme_docs_markdown_to_html($markdown) {
        $lines = preg_split("/\r\n|\r|\n/", (string) $markdown);
        $html = '';
        $in_list = false;
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                continue;
            }
            if (preg_match('/^###\s+(.*)$/', $trim, $m)) {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $html .= '<h3>' . esc_html($m[1]) . '</h3>';
                continue;
            }
            if (preg_match('/^##\s+(.*)$/', $trim, $m)) {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $html .= '<h2>' . esc_html($m[1]) . '</h2>';
                continue;
            }
            if (preg_match('/^#\s+(.*)$/', $trim, $m)) {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $html .= '<h1>' . esc_html($m[1]) . '</h1>';
                continue;
            }
            if (preg_match('/^-\s+(.*)$/', $trim, $m)) {
                if (!$in_list) {
                    $html .= '<ul class="wp-theme-docs-list">';
                    $in_list = true;
                }
                $html .= '<li>' . esc_html($m[1]) . '</li>';
                continue;
            }
            if (preg_match('/^\d+\.\s+(.*)$/', $trim, $m)) {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $html .= '<p><strong>' . esc_html($m[1]) . '</strong></p>';
                continue;
            }
            if ($in_list) {
                $html .= '</ul>';
                $in_list = false;
            }
            $html .= '<p>' . esc_html($trim) . '</p>';
        }
        if ($in_list) {
            $html .= '</ul>';
        }
        return $html;
    }
}

if (!function_exists('wp_theme_docs_render_page')) {
    function wp_theme_docs_render_page() {
        if (!current_user_can('edit_theme_options')) {
            wp_die(esc_html__('You do not have permission to view this page.', 'wp-theme'));
        }
        $path = trailingslashit(get_template_directory()) . 'README.md';
        $markdown = file_exists($path) ? file_get_contents($path) : '# Documentation\n\nREADME.md not found.';
        echo '<div class="wrap wp-theme-docs">';
        echo '<h1>' . esc_html__('Theme Documentation', 'wp-theme') . '</h1>';
        echo '<p><a class="button button-secondary" href="' . esc_url(admin_url('admin.php?page=wp-theme-settings')) . '">' . esc_html__('Back to Theme Settings', 'wp-theme') . '</a></p>';
        echo '<div class="notice notice-info inline"><p>' . esc_html__('This screen renders the README.md bundled with the parent theme.', 'wp-theme') . '</p></div>';
        echo '<div class="wp-theme-docs-card" style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:24px;max-width:1100px;line-height:1.6;">';
        echo wp_kses_post(wp_theme_docs_markdown_to_html($markdown));
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('wp_theme_google_font_browse_markup')) {
    function wp_theme_google_font_browse_markup() {
        return '<div class="wp-theme-helper-links">'
            . '<a class="button button-secondary" href="https://fonts.google.com/" target="_blank" rel="noopener">Browse Google Fonts</a> '
            . '<a class="button button-secondary" href="https://fontsource.org/fonts" target="_blank" rel="noopener">Browse Fontsource</a> '
            . '<a class="button button-secondary" href="https://fonts.bunny.net/" target="_blank" rel="noopener">Browse Bunny Fonts</a>'
            . '</div>';
    }
}

if (!function_exists('wp_theme_animation_settings_markup')) {
    function wp_theme_animation_settings_markup() {
        $settings = function_exists('bbtheme_get_animation_settings')
            ? bbtheme_get_animation_settings()
            : [
                'default_class' => 'animate__fadeInUp',
                'default_duration' => '1s',
                'default_delay' => '0s',
                'default_repeat' => '1',
                'preview_text' => 'Animation preview',
            ];

        $grouped = function_exists('bbtheme_get_grouped_animation_registry')
            ? bbtheme_get_grouped_animation_registry()
            : [];

        ob_start();
        ?>
        <div class="bbtheme-animation-admin bbtheme-animation-admin--embedded">
            <h2 class="nav-tab-wrapper bbtheme-animation-tabs">
                <a href="#bbtheme-tab-general" class="nav-tab nav-tab-active"><?php esc_html_e('General', 'wp-theme'); ?></a>
                <a href="#bbtheme-tab-preview" class="nav-tab"><?php esc_html_e('Preview', 'wp-theme'); ?></a>
                <a href="#bbtheme-tab-library" class="nav-tab"><?php esc_html_e('Library', 'wp-theme'); ?></a>
                <a href="#bbtheme-tab-integration" class="nav-tab"><?php esc_html_e('Integration', 'wp-theme'); ?></a>
            </h2>

            <div id="bbtheme-tab-general" class="bbtheme-tab-panel is-active">
                <div class="bbtheme-grid bbtheme-grid--narrow">
                    <div class="bbtheme-card">
                        <h3><?php esc_html_e('Theme animation defaults', 'wp-theme'); ?></h3>
                        <p><?php esc_html_e('Use the fields above to set global animation defaults. The preview and library below read those same fields live.', 'wp-theme'); ?></p>
                    </div>
                    <div class="bbtheme-card">
                        <h3><?php esc_html_e('Optional motion formats', 'wp-theme'); ?></h3>
                        <p><?php esc_html_e('Lottie and SVG motion stay optional. Keep them off and the theme will not load their support files.', 'wp-theme'); ?></p>
                        <div class="wp-theme-helper-links">
                            <a class="button button-secondary" href="https://lottiefiles.com/free-animations" target="_blank" rel="noopener"><?php esc_html_e('Browse free Lottie', 'wp-theme'); ?></a>
                            <a class="button button-secondary" href="https://lottiefiles.com/tools/web-player" target="_blank" rel="noopener"><?php esc_html_e('Open Lottie player docs', 'wp-theme'); ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bbtheme-tab-preview" class="bbtheme-tab-panel">
                <div class="bbtheme-grid">
                    <div class="bbtheme-card">
                        <div class="bbtheme-preview-toolbar">
                            <strong><?php esc_html_e('Live preview', 'wp-theme'); ?></strong>
                            <button id="bbtheme-preview-trigger" type="button" class="button button-primary"><?php esc_html_e('Replay animation', 'wp-theme'); ?></button>
                        </div>
                        <div class="bbtheme-preview-stage">
                            <div id="bbtheme-preview-box" class="animate__animated <?php echo esc_attr($settings['default_class']); ?>">
                                <strong><?php echo esc_html($settings['preview_text'] ?? __('Animation preview', 'wp-theme')); ?></strong>
                                <span><?php echo esc_html($settings['default_class']); ?></span>
                            </div>
                        </div>
                        <div class="bbtheme-code-snippet">
                            <code id="bbtheme-animation-code-snippet">&lt;div class="animate__animated <?php echo esc_html($settings['default_class'] ?? 'animate__fadeInUp'); ?>"&gt;...&lt;/div&gt;</code>
                        </div>
                    </div>

                    <div class="bbtheme-card">
                        <h3><?php esc_html_e('Preview animation class', 'wp-theme'); ?></h3>
                        <select id="bbtheme-preview-select">
                            <?php foreach ($grouped as $group_name => $items) : ?>
                                <optgroup label="<?php echo esc_attr($group_name); ?>">
                                    <?php foreach ($items as $item) : ?>
                                        <option value="<?php echo esc_attr($item['class']); ?>" <?php selected(($settings['default_class'] ?? ''), $item['class']); ?>>
                                            <?php echo esc_html($item['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('Use this selector to preview a different animation without changing the saved default animation field.', 'wp-theme'); ?></p>
                    </div>
                </div>
            </div>

            <div id="bbtheme-tab-library" class="bbtheme-tab-panel">
                <div class="bbtheme-card">
                    <div class="bbtheme-catalog-toolbar">
                        <strong><?php esc_html_e('Available animation classes', 'wp-theme'); ?></strong>
                        <input type="search" id="bbtheme-animation-search" class="regular-text" placeholder="<?php esc_attr_e('Search animation name or group…', 'wp-theme'); ?>">
                    </div>
                    <table class="widefat striped bbtheme-animation-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Group', 'wp-theme'); ?></th>
                                <th><?php esc_html_e('Animation', 'wp-theme'); ?></th>
                                <th><?php esc_html_e('Class', 'wp-theme'); ?></th>
                                <th><?php esc_html_e('Description', 'wp-theme'); ?></th>
                                <th><?php esc_html_e('Actions', 'wp-theme'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($grouped as $group_name => $items) : ?>
                            <?php foreach ($items as $item) : ?>
                                <tr data-search="<?php echo esc_attr(strtolower($group_name . ' ' . ($item['label'] ?? '') . ' ' . ($item['class'] ?? '') . ' ' . ($item['description'] ?? ''))); ?>">
                                    <td><?php echo esc_html($group_name); ?></td>
                                    <td><strong><?php echo esc_html($item['label'] ?? $item['class']); ?></strong></td>
                                    <td><code><?php echo esc_html($item['class'] ?? ''); ?></code></td>
                                    <td><?php echo esc_html($item['description'] ?? ''); ?></td>
                                    <td>
                                        <button type="button" class="button button-secondary bbtheme-preview-row" data-animation="<?php echo esc_attr($item['class'] ?? ''); ?>"><?php esc_html_e('Preview', 'wp-theme'); ?></button>
                                        <button type="button" class="button button-link bbtheme-copy-class" data-class="<?php echo esc_attr($item['class'] ?? ''); ?>"><?php esc_html_e('Copy class', 'wp-theme'); ?></button>
                                        <button type="button" class="button button-link bbtheme-use-animation" data-class="<?php echo esc_attr($item['class'] ?? ''); ?>"><?php esc_html_e('Use as default', 'wp-theme'); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="bbtheme-tab-integration" class="bbtheme-tab-panel">
                <div class="bbtheme-grid bbtheme-grid--narrow">
                    <div class="bbtheme-card">
                        <h3><?php esc_html_e('Animate.css helper', 'wp-theme'); ?></h3>
<pre>&lt;div &lt;?php echo bbtheme_get_animation_attributes([
    'animation' =&gt; 'animate__fadeInUp',
    'duration'  =&gt; '1.2s',
    'delay'     =&gt; '150ms',
    'repeat'    =&gt; '1',
]); ?&gt;&gt;
    Content here
&lt;/div&gt;</pre>
                    </div>
                    <div class="bbtheme-card">
                        <h3><?php esc_html_e('Lottie helper', 'wp-theme'); ?></h3>
<pre>&lt;?php
echo bbtheme_render_lottie([
    'src' =&gt; 'https://example.com/animation.lottie',
    'width' =&gt; '240px',
    'height' =&gt; '240px',
    'loop' =&gt; true,
    'autoplay' =&gt; true,
]);
?&gt;</pre>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('wp_theme_register_style_fields')) {
    function wp_theme_register_style_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $animation_choices = function_exists('bbtheme_get_animation_choices')
            ? bbtheme_get_animation_choices()
            : ['animate__fadeInUp' => 'animate__fadeInUp'];

        $fields = [];

        $fields[] = ['key' => 'tab_theme_colors', 'label' => 'Colors', 'type' => 'tab'];
        $fields[] = ['key' => 'msg_theme_colors_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Palette</strong><br><span>Theme colors sync to CSS variables automatically. Add more custom colors below if needed.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_brand_color','label'=>'Brand','name'=>'theme_brand_color','type'=>'color_picker','default_value'=>'#d21629','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_accent_color','label'=>'Accent','name'=>'theme_accent_color','type'=>'color_picker','default_value'=>'#4a4549','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_text_color','label'=>'Text','name'=>'theme_text_color','type'=>'color_picker','default_value'=>'#333333','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_heading_color','label'=>'Heading','name'=>'theme_heading_color','type'=>'color_picker','default_value'=>'#000000','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_background_color','label'=>'Background','name'=>'theme_background_color','type'=>'color_picker','default_value'=>'#ffffff','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_surface_color','label'=>'Surface','name'=>'theme_surface_color','type'=>'color_picker','default_value'=>'#F2F3F3','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_surface_alt_color','label'=>'Surface Alt','name'=>'theme_surface_alt_color','type'=>'color_picker','default_value'=>'#EDEDED','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_border_color','label'=>'Border','name'=>'theme_border_color','type'=>'color_picker','default_value'=>'#d9dde3','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_grey_dark_color','label'=>'Grey Dark','name'=>'theme_grey_dark_color','type'=>'color_picker','default_value'=>'#4a4549','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_grey_light_color','label'=>'Grey Light','name'=>'theme_grey_light_color','type'=>'color_picker','default_value'=>'#EDEDED','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_success_color','label'=>'Success','name'=>'theme_success_color','type'=>'color_picker','default_value'=>'#48C52C','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_warning_color','label'=>'Warning','name'=>'theme_warning_color','type'=>'color_picker','default_value'=>'#f59e0b','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_danger_color','label'=>'Danger','name'=>'theme_danger_color','type'=>'color_picker','default_value'=>'#FF0000','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_info_color','label'=>'Info','name'=>'theme_info_color','type'=>'color_picker','default_value'=>'#2563eb','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_link_color','label'=>'Link','name'=>'theme_link_color','type'=>'color_picker','default_value'=>'#d21629','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            ['key'=>'field_theme_link_hover_color','label'=>'Link Hover','name'=>'theme_link_hover_color','type'=>'color_picker','default_value'=>'#a61222','wrapper'=>['width'=>'25','class'=>'wp-theme-color-item']],
            [
                'key' => 'field_theme_custom_colors',
                'label' => 'Custom Theme Colors',
                'name' => 'theme_custom_colors',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add color',
                'sub_fields' => [
                    ['key' => 'field_theme_custom_color_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text', 'wrapper' => ['width' => '30']],
                    ['key' => 'field_theme_custom_color_slug', 'label' => 'Slug', 'name' => 'slug', 'type' => 'text', 'instructions' => 'Used for CSS variable names like --wp-custom-your-slug', 'wrapper' => ['width' => '30']],
                    ['key' => 'field_theme_custom_color_value', 'label' => 'Color', 'name' => 'value', 'type' => 'color_picker', 'wrapper' => ['width' => '20']],
                    ['key' => 'field_theme_custom_color_usage', 'label' => 'Usage note', 'name' => 'usage', 'type' => 'text', 'wrapper' => ['width' => '20']],
                ],
            ],
        ]);

        
        
        $fields[] = ['key'=>'tab_theme_general','label'=>'General','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_general_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>WP Options General</strong><br><span>Compact conditional controls for CPTs, login tools, and theme features.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro wp-theme-general-intro']];
        $fields[] = ['key' => 'msg_theme_general_tools', 'label' => '', 'name' => '', 'type' => 'message', 'message' => wp_theme_general_tools_markup(), 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-general-tools-wrap']];
        $fields[] = ['key'=>'field_media_glightbox','label'=>'Enable Lightbox','name'=>'media_glightbox','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_select2_js','label'=>'Enable Select2','name'=>'select2_js','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_alpine_js','label'=>'Enable Alpine JS','name'=>'alpine_js','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_booking_cpt','label'=>'Booking','name'=>'theme_enable_booking_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_event_cpt','label'=>'Event','name'=>'theme_enable_event_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_products_cpt','label'=>'Products','name'=>'theme_enable_products_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_case_study_cpt','label'=>'Case Study','name'=>'theme_enable_case_study_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_testimonial_cpt','label'=>'Testimonial','name'=>'theme_enable_testimonial_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_megamenu_cpt','label'=>'Megamenu','name'=>'theme_enable_megamenu_cpt','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_enable_drag_drop_ordering','label'=>'Drag & Drop Ordering','name'=>'theme_enable_drag_drop_ordering','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_msg_theme_cpt_hint','label'=>'','name'=>'','type'=>'message','message'=>'<span>Enable only the CPTs you want to use. Megamenu appears under Appearance when enabled. Enable Event to use event details and the single event template. Turn on Drag & Drop Ordering to reorder pages, posts, and public CPTs directly from the admin list table.</span>','esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']];
        $fields[] = ['key'=>'field_theme_login_logo_enabled','label'=>'Replace Admin Login Logo','name'=>'theme_login_logo_enabled','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_login_logo','label'=>'Login Logo Image','name'=>'theme_login_logo','type'=>'image','return_format'=>'array','preview_size'=>'medium','library'=>'all','wrapper'=>['width'=>'32','class'=>'wp-theme-general-field']];
        $fields[] = ['key'=>'field_theme_login_logo_width','label'=>'Logo Width','name'=>'theme_login_logo_width','type'=>'text','default_value'=>'160','wrapper'=>['width'=>'16','class'=>'wp-theme-general-field']];
        $fields[] = ['key'=>'field_theme_login_logo_height','label'=>'Logo Height','name'=>'theme_login_logo_height','type'=>'text','default_value'=>'80','wrapper'=>['width'=>'16','class'=>'wp-theme-general-field']];

        $fields[] = ['key'=>'field_msg_theme_asset_hint','label'=>'','name'=>'','type'=>'message','message'=>'<strong>SEO asset controls</strong><br><span>Keep everything off by default. Turn on only the frontend asset rules you really need for homepage and library optimisation.</span>','esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']];
        $fields[] = ['key'=>'field_theme_smart_library_loading','label'=>'Smart library loading','name'=>'theme_smart_library_loading','type'=>'true_false','ui'=>1,'default_value'=>1,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_theme_js_home','label'=>'Disable parent JS on homepage','name'=>'theme_disable_theme_js_home','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_child_js_home','label'=>'Disable child JS on homepage','name'=>'theme_disable_child_js_home','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_block_css_home','label'=>'Disable block CSS on homepage','name'=>'theme_disable_block_css_home','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_global_styles_home','label'=>'Disable global styles on homepage','name'=>'theme_disable_global_styles_home','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_dashicons_front','label'=>'Disable dashicons on frontend','name'=>'theme_disable_dashicons_front','type'=>'true_false','ui'=>1,'default_value'=>1,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_disable_wp_embed_front','label'=>'Disable wp-embed','name'=>'theme_disable_wp_embed_front','type'=>'true_false','ui'=>1,'default_value'=>1,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_msg_theme_asset_hint_2','label'=>'','name'=>'','type'=>'message','message'=>'<span>Smart library loading keeps Alpine and Lightbox off pages that do not appear to use them. Homepage switches only affect the front page.</span>','esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']];


        $fields[] = ['key'=>'tab_theme_acf_hero','label'=>'Custom Heros','type'=>'tab'];
$fields[] = ['key' => 'msg_theme_hero_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Custom Heros ( Archive &amp; uneditable pages )</strong><br><span>ID shortcode: [custom_hero_shop] = shop</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
$fields[] = [
    'key' => 'field_69047f82c3029',
    'label' => 'Custom Heros ( Archive & uneditable pages )',
    'name' => 'custom_heros',
    'type' => 'repeater',
    'instructions' => 'ID shortcode: [custom_hero_shop] = shop',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
    'layout' => 'table',
    'pagination' => 0,
    'min' => 0,
    'max' => 0,
    'collapsed' => '',
    'button_label' => 'Add Row',
    'rows_per_page' => 20,
    'sub_fields' => [
        [
            'key' => 'field_6904813159747',
            'label' => 'Hero ID',
            'name' => 'hero_id',
            'type' => 'text',
            'wrapper' => ['width' => '10', 'class' => '', 'id' => ''],
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ],
        [
            'key' => 'field_69047f06c3026',
            'label' => 'Hero Section Background',
            'name' => 'hero_sec_background',
            'type' => 'image',
            'wrapper' => ['width' => '20', 'class' => '', 'id' => ''],
            'return_format' => 'url',
            'library' => 'all',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => '',
            'allow_in_bindings' => 0,
            'preview_size' => 'medium',
        ],
        [
            'key' => 'field_69047f35c3027',
            'label' => 'Hero Section Caption',
            'name' => 'hero_sec_caption',
            'type' => 'text',
            'required' => 0,
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_690484c92a38f',
                        'operator' => '!=',
                        'value' => '1',
                    ],
                ],
            ],
            'wrapper' => ['width' => '20', 'class' => '', 'id' => ''],
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ],
        [
            'key' => 'field_6904804cdac82',
            'label' => 'Hero Section Text',
            'name' => 'hero_sec_txt',
            'type' => 'textarea',
            'wrapper' => ['width' => '25', 'class' => '', 'id' => ''],
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 1,
            'rows' => '',
            'placeholder' => '',
            'new_lines' => 'br',
        ],
        [
            'key' => 'field_69047fc9c302a',
            'label' => 'Hero Button',
            'name' => 'hero_sec_button',
            'type' => 'link',
            'wrapper' => ['width' => '11', 'class' => '', 'id' => ''],
            'return_format' => 'array',
            'allow_in_bindings' => 0,
        ],
    ],
];

$fields[] = ['key'=>'tab_theme_layout','label'=>'Layout','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_layout_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Spacing & Containers</strong>', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_container_width','label'=>'Container Width','name'=>'theme_container_width','type'=>'text','default_value'=>'1200px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_content_width','label'=>'Content Width','name'=>'theme_content_width','type'=>'text','default_value'=>'840px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_wide_width','label'=>'Wide Width','name'=>'theme_wide_width','type'=>'text','default_value'=>'1280px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_gutter_width','label'=>'Gutter','name'=>'theme_gutter_width','type'=>'text','default_value'=>'1.5rem','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_section_spacing','label'=>'Section Spacing','name'=>'theme_section_spacing','type'=>'text','default_value'=>'clamp(2rem, 4vw, 5rem)','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_radius','label'=>'Radius','name'=>'theme_radius','type'=>'text','default_value'=>'18px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
        ]);

        $fields[] = ['key'=>'tab_theme_typography','label'=>'Typography','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_typography_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Typography</strong><br><span>Font providers, import URLs, repeatable font variables, weight variables, and all responsive font sizes live here.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_font_provider','label'=>'Font Provider','name'=>'theme_font_provider','type'=>'select','choices'=>['system'=>'System / local only','google'=>'Google Fonts','custom'=>'Custom import URL(s)'],'default_value'=>'system','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_font_browse','label'=>'','name'=>'','type'=>'message','message'=>wp_theme_google_font_browse_markup(),'esc_html'=>0,'wrapper'=>['width'=>'67','class'=>'wp-theme-settings-intro wp-theme-font-browse']],
            ['key'=>'field_theme_body_font','label'=>'Body Font Stack','name'=>'theme_body_font','type'=>'text','default_value'=>"'Albert Sans', sans-serif",'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_heading_font','label'=>'Heading Font Stack','name'=>'theme_heading_font','type'=>'text','default_value'=>"'Albert Sans', sans-serif",'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_ui_font','label'=>'UI Font Stack','name'=>'theme_ui_font','type'=>'text','default_value'=>"'Albert Sans', sans-serif",'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_body_family','label'=>'Google Body Family','name'=>'theme_google_body_family','type'=>'text','default_value'=>'Albert Sans','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_heading_family','label'=>'Google Heading Family','name'=>'theme_google_heading_family','type'=>'text','default_value'=>'Albert Sans','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_ui_family','label'=>'Google UI Family','name'=>'theme_google_ui_family','type'=>'text','default_value'=>'Albert Sans','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_body_weights','label'=>'Body Weights','name'=>'theme_google_body_weights','type'=>'text','default_value'=>'300;400;500;600;700','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_heading_weights','label'=>'Heading Weights','name'=>'theme_google_heading_weights','type'=>'text','default_value'=>'400;500;600;700;800','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_google_ui_weights','label'=>'UI Weights','name'=>'theme_google_ui_weights','type'=>'text','default_value'=>'400;500;600;700','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_custom_font_import_url_1','label'=>'Custom Font CSS URL 1','name'=>'theme_custom_font_import_url_1','type'=>'url','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_custom_font_import_url_2','label'=>'Custom Font CSS URL 2','name'=>'theme_custom_font_import_url_2','type'=>'url','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_custom_font_import_url_3','label'=>'Custom Font CSS URL 3','name'=>'theme_custom_font_import_url_3','type'=>'url','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_body_weight','label'=>'Body Weight','name'=>'theme_body_weight','type'=>'text','default_value'=>'400','wrapper'=>['width'=>'16']],
            ['key'=>'field_theme_body_weight_medium','label'=>'Body Medium','name'=>'theme_body_weight_medium','type'=>'text','default_value'=>'500','wrapper'=>['width'=>'16']],
            ['key'=>'field_theme_heading_weight','label'=>'Heading Weight','name'=>'theme_heading_weight','type'=>'text','default_value'=>'700','wrapper'=>['width'=>'17']],
            ['key'=>'field_theme_heading_weight_light','label'=>'Heading Medium','name'=>'theme_heading_weight_light','type'=>'text','default_value'=>'500','wrapper'=>['width'=>'17']],
            ['key'=>'field_theme_ui_weight','label'=>'UI Weight','name'=>'theme_ui_weight','type'=>'text','default_value'=>'500','wrapper'=>['width'=>'17']],
            ['key'=>'field_theme_ui_weight_bold','label'=>'UI Bold','name'=>'theme_ui_weight_bold','type'=>'text','default_value'=>'700','wrapper'=>['width'=>'17']],
            [
                'key' => 'field_theme_font_variables',
                'label' => 'Additional Font Variables',
                'name' => 'theme_font_variables',
                'type' => 'repeater',
                'layout' => 'row',
                'button_label' => 'Add font variable',
                'sub_fields' => [
                    ['key' => 'field_theme_font_var_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text', 'wrapper' => ['width' => '18']],
                    ['key' => 'field_theme_font_var_slug', 'label' => 'Variable Slug', 'name' => 'slug', 'type' => 'text', 'instructions' => 'Creates CSS variable --wp-font-{slug}', 'wrapper' => ['width' => '16']],
                    ['key' => 'field_theme_font_var_stack', 'label' => 'Font Stack', 'name' => 'font_stack', 'type' => 'text', 'wrapper' => ['width' => '26']],
                    ['key' => 'field_theme_font_var_weights', 'label' => 'Weights', 'name' => 'weights', 'type' => 'text', 'wrapper' => ['width' => '14']],
                    ['key' => 'field_theme_font_var_import_url', 'label' => 'Import URL', 'name' => 'import_url', 'type' => 'url', 'wrapper' => ['width' => '26']],
                ],
            ],
            ['key' => 'msg_theme_typography_sizes', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<div class="wp-theme-size-header"><span>Style</span><span>Desktop</span><span>Tablet</span><span>Mobile</span></div>', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-size-header-wrap']],
        ]);
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_small_size', 'Small Text', '14px', '13px', '12px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_body_size', 'Body Text', '16px', '15px', '14px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_large_size', 'Large Text', '20px', '18px', '16px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h1_size', 'H1', '45px', '38px', '32px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h2_size', 'H2', '30px', '26px', '22px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h3_size', 'H3', '20px', '18px', '17px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h4_size', 'H4', '16px', '15px', '15px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h5_size', 'H5', '16px', '15px', '14px'));
        $fields = array_merge($fields, wp_theme_style_size_triplet_fields('theme_h6_size', 'H6', '12px', '12px', '11px'));

        $fields[] = ['key'=>'tab_theme_animations','label'=>'Animations','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_animations_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Animation controls</strong><br><span>Full animation UI with preview, library search, integration examples, plus optional Lottie and SVG motion support.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_anim_enabled','label'=>'Enable Animations','name'=>'theme_anim_enabled','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_disable_mobile','label'=>'Disable on Mobile','name'=>'theme_anim_disable_mobile','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_reduce_motion','label'=>'Respect Reduced Motion','name'=>'theme_anim_reduce_motion','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_repeat','label'=>'Repeat Count','name'=>'theme_anim_repeat','type'=>'select','choices'=>['1'=>'1','2'=>'2','3'=>'3','infinite'=>'infinite'],'default_value'=>'1','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_default_class','label'=>'Default Animation','name'=>'theme_anim_default_class','type'=>'select','choices'=>$animation_choices,'ui'=>1,'allow_null'=>1,'default_value'=>'animate__fadeInUp','wrapper'=>['width'=>'50']],
            ['key'=>'field_theme_anim_custom_class','label'=>'Extra Class','name'=>'theme_anim_custom_class','type'=>'text','default_value'=>'','wrapper'=>['width'=>'50']],
            ['key'=>'field_theme_anim_duration','label'=>'Duration','name'=>'theme_anim_duration','type'=>'text','default_value'=>'1s','placeholder'=>'1s or 800ms','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_delay','label'=>'Delay','name'=>'theme_anim_delay','type'=>'text','default_value'=>'0s','placeholder'=>'0s or 150ms','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_anim_preview_text','label'=>'Preview Text','name'=>'theme_anim_preview_text','type'=>'text','default_value'=>'Animation preview','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_motion_enable_lottie','label'=>'Enable Lottie Support','name'=>'theme_motion_enable_lottie','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_motion_lottie_url','label'=>'Default Lottie URL','name'=>'theme_motion_lottie_url','type'=>'url','wrapper'=>['width'=>'40']],
            ['key'=>'field_theme_motion_lottie_speed','label'=>'Lottie Speed','name'=>'theme_motion_lottie_speed','type'=>'text','default_value'=>'1','wrapper'=>['width'=>'10']],
            ['key'=>'field_theme_motion_lottie_loop','label'=>'Loop','name'=>'theme_motion_lottie_loop','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'10']],
            ['key'=>'field_theme_motion_lottie_autoplay','label'=>'Autoplay','name'=>'theme_motion_lottie_autoplay','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'10']],
            ['key'=>'field_theme_motion_lottie_width','label'=>'Lottie Width','name'=>'theme_motion_lottie_width','type'=>'text','default_value'=>'240px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_motion_lottie_height','label'=>'Lottie Height','name'=>'theme_motion_lottie_height','type'=>'text','default_value'=>'240px','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_motion_enable_svg_motion','label'=>'Enable SVG Motion Helpers','name'=>'theme_motion_enable_svg_motion','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_motion_svg_class','label'=>'SVG Motion Class','name'=>'theme_motion_svg_class','type'=>'text','default_value'=>'is-animated-svg','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key' => 'msg_theme_animation_full_ui', 'label' => '', 'name' => '', 'type' => 'message', 'message' => wp_theme_animation_settings_markup(), 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-animation-ui-wrap']],
        ]);

        
        $fields[] = ['key'=>'tab_theme_demo_import','label'=>'Demo Import','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_demo_import_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Homepage demo import</strong><br><span>Runs a first demo homepage in the theme and uses WP BBuilder plugin blocks if the plugin is available.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields[] = ['key' => 'msg_theme_demo_import_ui', 'label' => '', 'name' => '', 'type' => 'message', 'message' => wp_theme_demo_import_markup(), 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-demo-import-wrap']];

        $fields[] = ['key'=>'tab_theme_media','label'=>'Media','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_media_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Import optimization</strong><br><span>Free Images Import uses these defaults when bringing images into the Media Library.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_media_convert_to_avif','label'=>'AVIF','name'=>'theme_media_convert_to_avif','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'14','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_max_width','label'=>'Max Width','name'=>'theme_media_max_width','type'=>'text','default_value'=>'1920','wrapper'=>['width'=>'14','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_quality','label'=>'Quality','name'=>'theme_media_quality','type'=>'text','default_value'=>'82','wrapper'=>['width'=>'14','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_size_xl','label'=>'XL','name'=>'theme_media_size_xl','type'=>'text','default_value'=>'1920','wrapper'=>['width'=>'14','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_size_lg','label'=>'LG','name'=>'theme_media_size_lg','type'=>'text','default_value'=>'1600','wrapper'=>['width'=>'14','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_size_md','label'=>'MD','name'=>'theme_media_size_md','type'=>'text','default_value'=>'960','wrapper'=>['width'=>'15','class'=>'wp-theme-media-compact']],
            ['key'=>'field_theme_media_size_sm','label'=>'SM','name'=>'theme_media_size_sm','type'=>'text','default_value'=>'480','wrapper'=>['width'=>'15','class'=>'wp-theme-media-compact']],
        ]);

        $fields[] = ['key'=>'tab_theme_performance','label'=>'Performance Extras','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_performance_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Cache, critical CSS & search</strong><br><span>Optional performance tools and cache controls.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_wp_theme_cache_headers','label'=>'Cache Headers','name'=>'perf_cache_headers','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_object_cache','label'=>'Object Cache','name'=>'perf_object_cache','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_object_cache_driver','label'=>'Object Cache Driver','name'=>'perf_object_cache_driver','type'=>'select','choices'=>['auto'=>'Auto','redis'=>'Redis','memcached'=>'Memcached','transient'=>'Transient','disabled'=>'Disabled'],'default_value'=>'auto','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_object_cache_ttl','label'=>'Object Cache TTL','name'=>'perf_object_cache_ttl','type'=>'number','default_value'=>600,'min'=>60,'step'=>60,'append'=>'sec','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_html_minify','label'=>'HTML Minify (safe)','name'=>'perf_html_minify','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_critical_css','label'=>'Auto Critical CSS','name'=>'perf_auto_critical_css','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_search_thumbs','label'=>'Search Thumbnails','name'=>'perf_search_thumbnails','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_search_enhanced','label'=>'Enhanced Ajax Search','name'=>'perf_search_enhanced','type'=>'true_false','ui'=>1,'default_value'=>1,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_wp_theme_perf_actions','label'=>'Tools','name'=>'perf_tools_markup','type'=>'message','message'=>wp_theme_performance_actions_markup(),'esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']],
        ]);

        $fields[] = ['key'=>'tab_theme_developer_tools','label'=>'Developer Tools','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_dev_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Developer tools</strong><br><span>Optional frontend QA helpers for borders, spacing, typography, colors, and pixel-perfect overlay. Keep everything off unless you are actively checking a page.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields[] = ['key'=>'field_theme_dev_show_borders','label'=>'Show Borders','name'=>'theme_dev_show_borders','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_dev_show_spacing','label'=>'Show Margins & Paddings','name'=>'theme_dev_show_spacing','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_dev_show_typography','label'=>'Typography Inspector','name'=>'theme_dev_show_typography','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_dev_show_colors','label'=>'Color Codes','name'=>'theme_dev_show_colors','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_dev_pixel_perfect','label'=>'Pixel Perfect Overlay','name'=>'theme_dev_pixel_perfect','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']];
        $fields[] = ['key'=>'field_theme_dev_mockup_image','label'=>'Mockup Image','name'=>'theme_dev_mockup_image','type'=>'image','return_format'=>'array','preview_size'=>'medium','library'=>'all','wrapper'=>['width'=>'32','class'=>'wp-theme-general-field']];
        $fields[] = ['key'=>'field_theme_dev_mockup_opacity','label'=>'Mockup Opacity','name'=>'theme_dev_mockup_opacity','type'=>'range','default_value'=>35,'min'=>5,'max'=>100,'step'=>5,'append'=>'%','wrapper'=>['width'=>'16','class'=>'wp-theme-general-field']];
        $fields[] = ['key'=>'field_theme_dev_note','label'=>'','name'=>'','type'=>'message','message'=>'<span>Developer tools load only for logged-in users who can edit theme options. Use Alt + click on the frontend to inspect typography, spacing, and color values.</span>','esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']];

        $fields[] = ['key'=>'tab_theme_booking_dashboard','label'=>'Booking Dashboard','type'=>'tab'];
        $fields[] = ['key' => 'msg_theme_booking_intro', 'label' => '', 'name' => '', 'type' => 'message', 'message' => '<strong>Bookings</strong><br><span>Calendar, client table, and reply tools.</span>', 'new_lines' => 'br', 'esc_html' => 0, 'wrapper' => ['class' => 'wp-theme-settings-intro']];
        $fields = array_merge($fields, [
            ['key'=>'field_theme_booking_auto_reply','label'=>'Auto Reply Client','name'=>'theme_booking_auto_reply','type'=>'true_false','ui'=>1,'default_value'=>0,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_booking_delete_canceled','label'=>'Delete Canceled','name'=>'theme_booking_delete_canceled','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_booking_delete_old','label'=>'Delete Old Bookings','name'=>'theme_booking_delete_old','type'=>'true_false','ui'=>1,'default_value'=>0,'ui_on_text'=>'On','ui_off_text'=>'Off','wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_booking_delete_old_days','label'=>'Old After (days)','name'=>'theme_booking_delete_old_days','type'=>'number','default_value'=>30,'min'=>1,'step'=>1,'wrapper'=>['width'=>'16','class'=>'wp-theme-general-toggle']],
            ['key'=>'field_theme_booking_auto_reply_subject','label'=>'Auto Reply Subject','name'=>'theme_booking_auto_reply_subject','type'=>'text','default_value'=>'We received your booking request','wrapper'=>['width'=>'32']],
            ['key'=>'field_theme_booking_auto_reply_message','label'=>'Auto Reply Message','name'=>'theme_booking_auto_reply_message','type'=>'textarea','rows'=>3,'default_value'=>'Thank you. We will reply soon.','wrapper'=>['width'=>'32']],
            ['key'=>'field_theme_booking_cleanup_tools','label'=>'','name'=>'','type'=>'message','message'=>function_exists('wp_theme_booking_cleanup_tools_markup') ? wp_theme_booking_cleanup_tools_markup() : '', 'esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']],
            ['key'=>'field_theme_booking_dashboard_markup','label'=>'','name'=>'','type'=>'message','message'=>function_exists('wp_theme_booking_dashboard_markup') ? wp_theme_booking_dashboard_markup() : '', 'esc_html'=>0,'wrapper'=>['width'=>'100','class'=>'wp-theme-general-note']],
        ]);

        acf_add_local_field_group([
            'key'    => 'group_wp_theme_style_options',
            'title'  => __('Theme Settings', 'wp-theme'),
            'fields' => $fields,
            'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'wp-theme-settings']]],
            'style'    => 'seamless',
            'active'   => true,
        ]);
    }
}
add_action('acf/init', 'wp_theme_register_style_fields', 20);

if (!function_exists('wp_theme_collect_google_font_families')) {
    function wp_theme_collect_google_font_families($tokens) {
        $items = [];
        $families = [
            [$tokens['theme_google_body_family'] ?? '', $tokens['theme_google_body_weights'] ?? '400;500;700'],
            [$tokens['theme_google_heading_family'] ?? '', $tokens['theme_google_heading_weights'] ?? '400;500;700'],
            [$tokens['theme_google_ui_family'] ?? '', $tokens['theme_google_ui_weights'] ?? '400;500;700'],
        ];
        foreach ($families as $entry) {
            [$family, $weights] = $entry;
            $family = trim((string) $family);
            if ($family === '') {
                continue;
            }
            $weight_list = preg_replace('/[^0-9;]+/', '', (string) $weights);
            $weight_list = trim($weight_list, ';');
            $weight_list = $weight_list !== '' ? $weight_list : '400;500;700';
            $items[] = 'family=' . rawurlencode(str_replace(' ', '+', $family) . ':wght@' . $weight_list);
        }
        return array_values(array_unique($items));
    }
}

if (!function_exists('wp_theme_enqueue_font_imports')) {
    function wp_theme_enqueue_font_imports() {
        $tokens = wp_theme_style_tokens();

        if (($tokens['theme_font_provider'] ?? 'system') === 'google') {
            $families = wp_theme_collect_google_font_families($tokens);
            if (!empty($families)) {
                $url = 'https://fonts.googleapis.com/css2?' . implode('&', $families) . '&display=swap';
                wp_enqueue_style('wp-theme-google-fonts', $url, [], null);
            }
        }

        $custom_urls = [
            $tokens['theme_custom_font_import_url_1'] ?? '',
            $tokens['theme_custom_font_import_url_2'] ?? '',
            $tokens['theme_custom_font_import_url_3'] ?? '',
        ];

        if (($tokens['theme_font_provider'] ?? 'system') === 'custom') {
            foreach ($custom_urls as $index => $url) {
                $url = esc_url_raw((string) $url);
                if ($url !== '') {
                    wp_enqueue_style('wp-theme-custom-font-' . $index, $url, [], null);
                }
            }
        }

        if (!empty($tokens['theme_font_variables']) && is_array($tokens['theme_font_variables'])) {
            foreach ($tokens['theme_font_variables'] as $index => $row) {
                $url = esc_url_raw((string) ($row['import_url'] ?? ''));
                if ($url !== '') {
                    wp_enqueue_style('wp-theme-repeat-font-' . $index, $url, [], null);
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'wp_theme_enqueue_font_imports', 12);
add_action('admin_enqueue_scripts', 'wp_theme_enqueue_font_imports', 12);

if (!function_exists('wp_theme_style_css_from_tokens')) {
    function wp_theme_style_css_from_tokens($tokens) {
        $map = [
            '--wp-brand-color'         => $tokens['theme_brand_color'],
            '--wp-accent-color'        => $tokens['theme_accent_color'],
            '--wp-text-color'          => $tokens['theme_text_color'],
            '--wp-heading-color'       => $tokens['theme_heading_color'],
            '--wp-background-color'    => $tokens['theme_background_color'],
            '--wp-light-bg-color'      => $tokens['theme_surface_color'],
            '--wp-surface-alt-color'   => $tokens['theme_surface_alt_color'],
            '--wp-border-color'        => $tokens['theme_border_color'],
            '--wp-grey-dark-color'     => $tokens['theme_grey_dark_color'],
            '--wp-grey-light-color'    => $tokens['theme_grey_light_color'],
            '--wp-green-color'         => $tokens['theme_success_color'],
            '--wp-warning-color'       => $tokens['theme_warning_color'],
            '--wp-red-color'           => $tokens['theme_danger_color'],
            '--wp-info-color'          => $tokens['theme_info_color'],
            '--wp-link-color'          => $tokens['theme_link_color'],
            '--wp-link-hover-color'    => $tokens['theme_link_hover_color'],
            '--wp-container-width'     => $tokens['theme_container_width'],
            '--wp-content-width'       => $tokens['theme_content_width'],
            '--wp-wide-width'          => $tokens['theme_wide_width'],
            '--wp-gutter-width'        => $tokens['theme_gutter_width'],
            '--wp-section-spacing'     => $tokens['theme_section_spacing'],
            '--wp-theme-radius'        => $tokens['theme_radius'],
            '--wp-body-font'           => $tokens['theme_body_font'],
            '--wp-headings-font'       => $tokens['theme_heading_font'],
            '--wp-ui-font'             => $tokens['theme_ui_font'],
            '--wp-body-weight'         => $tokens['theme_body_weight'],
            '--wp-body-weight-medium'  => $tokens['theme_body_weight_medium'],
            '--wp-heading-weight'      => $tokens['theme_heading_weight'],
            '--wp-heading-weight-light'=> $tokens['theme_heading_weight_light'],
            '--wp-ui-weight'           => $tokens['theme_ui_weight'],
            '--wp-ui-weight-bold'      => $tokens['theme_ui_weight_bold'],
            '--wp-font-size-small'     => $tokens['theme_small_size'],
            '--wp-body-size'           => $tokens['theme_body_size'],
            '--wp-font-size-medium'    => $tokens['theme_large_size'],
            '--wp-h1-font-size'        => $tokens['theme_h1_size'],
            '--wp-h2-font-size'        => $tokens['theme_h2_size'],
            '--wp-h3-font-size'        => $tokens['theme_h3_size'],
            '--wp-h4-font-size'        => $tokens['theme_h4_size'],
            '--wp-h5-font-size'        => $tokens['theme_h5_size'],
            '--wp-h6-font-size'        => $tokens['theme_h6_size'],
        ];

        if (!empty($tokens['theme_custom_colors']) && is_array($tokens['theme_custom_colors'])) {
            foreach ($tokens['theme_custom_colors'] as $row) {
                $slug = sanitize_title($row['slug'] ?? '');
                $value = trim((string) ($row['value'] ?? ''));
                if ($slug !== '' && $value !== '') {
                    $map['--wp-custom-' . $slug] = $value;
                }
            }
        }

        if (!empty($tokens['theme_font_variables']) && is_array($tokens['theme_font_variables'])) {
            foreach ($tokens['theme_font_variables'] as $row) {
                $slug = sanitize_title($row['slug'] ?? '');
                $stack = trim((string) ($row['font_stack'] ?? ''));
                $weights = trim((string) ($row['weights'] ?? ''));
                if ($slug !== '' && $stack !== '') {
                    $map['--wp-font-' . $slug] = $stack;
                }
                if ($slug !== '' && $weights !== '') {
                    $map['--wp-font-' . $slug . '-weights'] = $weights;
                }
            }
        }

        $css = ':root{';
        foreach ($map as $name => $value) {
            $css .= sprintf('%s:%s;', esc_html($name), trim((string) $value));
        }
        $css .= '}';

        $css .= '@media (max-width: 1024px){:root{';
        $css .= sprintf('--wp-font-size-small:%s;--wp-body-size:%s;--wp-font-size-medium:%s;--wp-h1-font-size:%s;--wp-h2-font-size:%s;--wp-h3-font-size:%s;--wp-h4-font-size:%s;--wp-h5-font-size:%s;--wp-h6-font-size:%s;',
            trim((string) $tokens['theme_small_size_tablet']),
            trim((string) $tokens['theme_body_size_tablet']),
            trim((string) $tokens['theme_large_size_tablet']),
            trim((string) $tokens['theme_h1_size_tablet']),
            trim((string) $tokens['theme_h2_size_tablet']),
            trim((string) $tokens['theme_h3_size_tablet']),
            trim((string) $tokens['theme_h4_size_tablet']),
            trim((string) $tokens['theme_h5_size_tablet']),
            trim((string) $tokens['theme_h6_size_tablet'])
        );
        $css .= '}}';

        $css .= '@media (max-width: 767px){:root{';
        $css .= sprintf('--wp-font-size-small:%s;--wp-body-size:%s;--wp-font-size-medium:%s;--wp-h1-font-size:%s;--wp-h2-font-size:%s;--wp-h3-font-size:%s;--wp-h4-font-size:%s;--wp-h5-font-size:%s;--wp-h6-font-size:%s;',
            trim((string) $tokens['theme_small_size_mobile']),
            trim((string) $tokens['theme_body_size_mobile']),
            trim((string) $tokens['theme_large_size_mobile']),
            trim((string) $tokens['theme_h1_size_mobile']),
            trim((string) $tokens['theme_h2_size_mobile']),
            trim((string) $tokens['theme_h3_size_mobile']),
            trim((string) $tokens['theme_h4_size_mobile']),
            trim((string) $tokens['theme_h5_size_mobile']),
            trim((string) $tokens['theme_h6_size_mobile'])
        );
        $css .= '}}';

        return $css;
    }
}

if (!function_exists('wp_theme_output_style_tokens')) {
    function wp_theme_output_style_tokens() {
        echo '<style id="wp-theme-style-tokens">' . wp_theme_style_css_from_tokens(wp_theme_style_tokens()) . '</style>';
    }
}
add_action('wp_head', 'wp_theme_output_style_tokens', 8);
add_action('admin_head', 'wp_theme_output_style_tokens', 8);

if (!function_exists('wp_theme_enqueue_generated_style_tokens')) {
    function wp_theme_enqueue_generated_style_tokens() {
        $css = wp_theme_style_css_from_tokens(wp_theme_style_tokens());
        if (wp_style_is('wp-theme-style', 'enqueued')) {
            wp_add_inline_style('wp-theme-style', $css);
        }
        if (wp_style_is('wp-theme-dist', 'enqueued')) {
            wp_add_inline_style('wp-theme-dist', $css);
        }
    }
}
add_action('wp_enqueue_scripts', 'wp_theme_enqueue_generated_style_tokens', 99);
add_action('admin_enqueue_scripts', 'wp_theme_enqueue_generated_style_tokens', 99);

if (!function_exists('wp_theme_write_generated_style_files')) {
    function wp_theme_write_generated_style_files($post_id) {
        if ($post_id !== 'options') {
            return;
        }
        $tokens = wp_theme_style_tokens();
        $scss = "/* Auto-generated from ACF Theme Settings. */\n";
        $scss .= '$acf-body-font: ' . $tokens['theme_body_font'] . " !default;\n";
        $scss .= '$acf-headings-font: ' . $tokens['theme_heading_font'] . " !default;\n";
        $scss .= '$acf-ui-font: ' . $tokens['theme_ui_font'] . " !default;\n";
        $css = "/* Auto-generated from ACF Theme Settings. */\n" . wp_theme_style_css_from_tokens($tokens);

        $scss_file = trailingslashit(get_template_directory()) . 'src/scss/_acf-variables.generated.scss';
        $css_file  = trailingslashit(get_template_directory()) . 'assets/css/acf-theme-vars.css';

        if (wp_is_writable(dirname($scss_file))) {
            file_put_contents($scss_file, $scss);
        }
        if (wp_is_writable(dirname($css_file))) {
            file_put_contents($css_file, $css);
        }
    }
}
add_action('acf/save_post', 'wp_theme_write_generated_style_files', 20);

if (!function_exists('wp_theme_sync_animation_settings_from_acf')) {
    function wp_theme_sync_animation_settings_from_acf($post_id) {
        if ($post_id !== 'options' || !function_exists('bbtheme_get_animation_choices')) {
            return;
        }

        $choices = bbtheme_get_animation_choices();
        $default_class = get_field('theme_anim_default_class', 'option');
        if (!is_string($default_class) || !isset($choices[$default_class])) {
            $default_class = 'animate__fadeInUp';
        }

        $repeat = (string) get_field('theme_anim_repeat', 'option');
        if (!in_array($repeat, ['1', '2', '3', 'infinite'], true)) {
            $repeat = '1';
        }

        update_option('bbtheme_animation_settings', [
            'enabled' => get_field('theme_anim_enabled', 'option') ? '1' : '',
            'default_class' => $default_class,
            'default_duration' => (string) get_field('theme_anim_duration', 'option'),
            'default_delay' => (string) get_field('theme_anim_delay', 'option'),
            'default_repeat' => $repeat,
            'disable_on_mobile' => get_field('theme_anim_disable_mobile', 'option') ? '1' : '',
            'respect_reduced_motion' => get_field('theme_anim_reduce_motion', 'option') ? '1' : '',
            'custom_class' => (string) get_field('theme_anim_custom_class', 'option'),
            'preview_text' => (string) get_field('theme_anim_preview_text', 'option'),
        ]);
    }
}
add_action('acf/save_post', 'wp_theme_sync_animation_settings_from_acf', 30);

if (!function_exists('wp_theme_render_media_free_images_panel')) {
    function wp_theme_render_media_free_images_panel() {
        if (!current_user_can('upload_files')) {
            return;
        }
        $tokens = wp_theme_style_tokens();
        ?>
        <div class="wrap wp-theme-media-tools-wrap">
            <div class="wp-theme-media-tools">
                <button type="button" class="button button-primary button-hero" id="wp-theme-toggle-free-images"><?php esc_html_e('Free Images Import', 'wp-theme'); ?></button>
                <p class="description"><?php esc_html_e('Search public free image sources and import directly into the Media Library. Quick links open other libraries in a new tab.', 'wp-theme'); ?></p>
                <p class="description"><?php echo esc_html(sprintf(__('Optimization defaults: max width %spx, AVIF %s, quality %s.', 'wp-theme'), $tokens['theme_media_max_width'], !empty($tokens['theme_media_convert_to_avif']) ? __('on when supported', 'wp-theme') : __('off', 'wp-theme'), $tokens['theme_media_quality'])); ?></p>
            </div>

            <div id="wp-theme-free-images-panel" class="wp-theme-free-images-panel" hidden>
                <div class="wp-theme-media-toolbar">
                    <input type="search" id="wp-theme-media-query" class="regular-text" placeholder="<?php esc_attr_e('Search free images…', 'wp-theme'); ?>">
                    <button type="button" class="button button-primary" id="wp-theme-media-search"><?php esc_html_e('Search selected providers', 'wp-theme'); ?></button>
                </div>
                <div class="wp-theme-provider-grid">
                    <?php foreach ([
                        'openverse' => 'Openverse',
                        'wikimedia' => 'Wikimedia Commons',
                    ] as $provider_key => $provider_label) : ?>
                        <label class="wp-theme-provider-pill"><input type="checkbox" class="wp-theme-media-provider" value="<?php echo esc_attr($provider_key); ?>" checked> <?php echo esc_html($provider_label); ?></label>
                    <?php endforeach; ?>
                </div>
                <div class="wp-theme-helper-links wp-theme-media-quick-links">
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://unsplash.com/s/photos/" id="wp-theme-quick-unsplash"><?php esc_html_e('Unsplash', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://www.pexels.com/search/" id="wp-theme-quick-pexels"><?php esc_html_e('Pexels', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://pixabay.com/images/search/" id="wp-theme-quick-pixabay"><?php esc_html_e('Pixabay', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://giphy.com/search/" id="wp-theme-quick-giphy"><?php esc_html_e('Giphy', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://burst.shopify.com/photos/search?utf8=%E2%9C%93&q=" id="wp-theme-quick-burst"><?php esc_html_e('Burst', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://stocksnap.io/search/" id="wp-theme-quick-stocksnap"><?php esc_html_e('StockSnap.io', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://kaboompics.com/gallery?search=" id="wp-theme-quick-kaboom"><?php esc_html_e('Kaboompics', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://gratisography.com/?s=" id="wp-theme-quick-gratisography"><?php esc_html_e('Gratisography', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://picjumbo.com/?s=" id="wp-theme-quick-picjumbo"><?php esc_html_e('Picjumbo', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://www.lifeofpix.com/?s=" id="wp-theme-quick-lifeofpix"><?php esc_html_e('Life of Pix', 'wp-theme'); ?></a>
                    <a class="button button-secondary" target="_blank" rel="noopener" href="https://www.freepik.com/search?format=search&query=" id="wp-theme-quick-freepik"><?php esc_html_e('Freepik', 'wp-theme'); ?></a>
                </div>
                <div class="wp-theme-media-optimize-tools">
                    <h3><?php esc_html_e('Optimize existing upload', 'wp-theme'); ?></h3>
                    <div class="wp-theme-media-toolbar">
                        <input type="number" min="1" id="wp-theme-optimize-attachment-id" class="small-text" placeholder="<?php esc_attr_e('Attachment ID', 'wp-theme'); ?>">
                        <button type="button" class="button button-secondary" id="wp-theme-optimize-attachment"><?php esc_html_e('Optimize attachment', 'wp-theme'); ?></button>
                    </div>
                    <p class="description"><?php esc_html_e('Use this for any image already uploaded from your computer. It will resize and try AVIF conversion using the same defaults.', 'wp-theme'); ?></p>
                </div>
                <p id="wp-theme-media-status" class="wp-theme-media-status"></p>
                <div id="wp-theme-media-results" class="wp-theme-media-results"></div>
            </div>
        </div>
        <?php
    }
}
add_action('all_admin_notices', function () {
    global $pagenow;
    if ($pagenow === 'upload.php') {
        wp_theme_render_media_free_images_panel();
    }
});

if (!function_exists('wp_theme_free_image_search')) {
    function wp_theme_free_image_search() {
        check_ajax_referer('wp_theme_settings_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('You do not have permission to search images.', 'wp-theme')], 403);
        }

        $query = sanitize_text_field(wp_unslash($_POST['query'] ?? ''));
        $providers = array_map('sanitize_key', (array) ($_POST['providers'] ?? ['openverse', 'wikimedia']));
        $page  = max(1, absint($_POST['page'] ?? 1));

        if ($query === '') {
            wp_send_json_error(['message' => __('Enter a search term first.', 'wp-theme')], 400);
        }

        $results = [];
        foreach ($providers as $provider) {
            if ($provider === 'openverse') {
                $url = add_query_arg([
                    'q' => $query,
                    'page' => $page,
                    'page_size' => 12,
                    'license_type' => 'commercial',
                    'extension' => 'jpg,jpeg,png',
                ], 'https://api.openverse.org/v1/images/');
                $response = wp_remote_get($url, ['timeout' => 20, 'headers' => ['User-Agent' => 'WP-BBTheme Media Browser']]);
                if (!is_wp_error($response)) {
                    $body = json_decode(wp_remote_retrieve_body($response), true);
                    foreach (($body['results'] ?? []) as $item) {
                        $results[] = [
                            'title' => sanitize_text_field($item['title'] ?? __('Untitled image', 'wp-theme')),
                            'creator' => sanitize_text_field($item['creator'] ?? ''),
                            'license' => trim(sanitize_text_field(($item['license'] ?? '') . ' ' . ($item['license_version'] ?? ''))),
                            'thumbnail' => esc_url_raw($item['thumbnail'] ?? ''),
                            'url' => esc_url_raw($item['url'] ?? ''),
                            'foreign_landing_url' => esc_url_raw($item['foreign_landing_url'] ?? ''),
                            'provider' => 'Openverse',
                        ];
                    }
                }
            }

            if ($provider === 'wikimedia') {
                $url = add_query_arg([
                    'action' => 'query',
                    'generator' => 'search',
                    'gsrsearch' => $query,
                    'gsrnamespace' => '6',
                    'gsrlimit' => '12',
                    'prop' => 'imageinfo|info',
                    'inprop' => 'url',
                    'iiprop' => 'url|extmetadata',
                    'iiurlwidth' => '480',
                    'format' => 'json',
                    'origin' => '*',
                ], 'https://commons.wikimedia.org/w/api.php');
                $response = wp_remote_get($url, ['timeout' => 20, 'headers' => ['User-Agent' => 'WP-BBTheme Media Browser']]);
                if (!is_wp_error($response)) {
                    $body = json_decode(wp_remote_retrieve_body($response), true);
                    foreach (($body['query']['pages'] ?? []) as $page_item) {
                        $image = $page_item['imageinfo'][0] ?? [];
                        $meta = $image['extmetadata'] ?? [];
                        $results[] = [
                            'title' => sanitize_text_field($page_item['title'] ?? __('Untitled image', 'wp-theme')),
                            'creator' => wp_strip_all_tags($meta['Artist']['value'] ?? ''),
                            'license' => wp_strip_all_tags($meta['LicenseShortName']['value'] ?? 'Wikimedia Commons'),
                            'thumbnail' => esc_url_raw($image['thumburl'] ?? $image['url'] ?? ''),
                            'url' => esc_url_raw($image['url'] ?? ''),
                            'foreign_landing_url' => esc_url_raw($page_item['fullurl'] ?? $image['descriptionurl'] ?? ''),
                            'provider' => 'Wikimedia Commons',
                        ];
                    }
                }
            }
        }

        wp_send_json_success(['results' => $results]);
    }
}
add_action('wp_ajax_wp_theme_free_image_search', 'wp_theme_free_image_search');

if (!function_exists('wp_theme_optimize_attachment_image')) {
    function wp_theme_optimize_attachment_image($attachment_id) {
        $tokens = wp_theme_style_tokens();
        $max_width = max(320, absint($tokens['theme_media_max_width'] ?? 1920));
        $quality = max(40, min(100, absint($tokens['theme_media_quality'] ?? 82)));
        $convert_to_avif = !empty($tokens['theme_media_convert_to_avif']);

        $file = get_attached_file($attachment_id);
        if (!$file || !file_exists($file)) {
            return ['converted' => false, 'message' => __('Imported image saved without optimization.', 'wp-theme')];
        }

        $editor = wp_get_image_editor($file);
        if (is_wp_error($editor)) {
            return ['converted' => false, 'message' => __('Image editor unavailable. Imported original file only.', 'wp-theme')];
        }

        $size = $editor->get_size();
        if (!empty($size['width']) && $size['width'] > $max_width) {
            $editor->resize($max_width, null, false);
        }

        if (method_exists($editor, 'set_quality')) {
            $editor->set_quality($quality);
        }

        $message = __('Image optimized after import.', 'wp-theme');
        $saved = null;

        if ($convert_to_avif) {
            $avif_file = preg_replace('/\.[^.]+$/', '.avif', $file);
            $saved = $editor->save($avif_file, 'image/avif');
            if (!is_wp_error($saved) && !empty($saved['path'])) {
                update_attached_file($attachment_id, $saved['path']);
                wp_update_post(['ID' => $attachment_id, 'post_mime_type' => 'image/avif']);
                $message = __('Image imported and converted to AVIF.', 'wp-theme');
            } else {
                $saved = null;
                $message = __('AVIF conversion not supported here, but image was resized/optimized.', 'wp-theme');
            }
        }

        if (!$saved) {
            $saved = $editor->save($file);
        }

        if (!is_wp_error($saved)) {
            $metadata = wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id));
            if (!is_wp_error($metadata) && !empty($metadata)) {
                wp_update_attachment_metadata($attachment_id, $metadata);
            }
        }

        return ['converted' => !empty($saved['path']) && str_ends_with((string) $saved['path'], '.avif'), 'message' => $message];
    }
}

if (!function_exists('wp_theme_free_image_import')) {
    function wp_theme_free_image_import() {
        check_ajax_referer('wp_theme_settings_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('You do not have permission to import images.', 'wp-theme')], 403);
        }

        $image_url = esc_url_raw(wp_unslash($_POST['image_url'] ?? ''));
        $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
        $creator = sanitize_text_field(wp_unslash($_POST['creator'] ?? ''));
        $license = sanitize_text_field(wp_unslash($_POST['license'] ?? ''));
        $source_url = esc_url_raw(wp_unslash($_POST['source_url'] ?? ''));
        $provider = sanitize_text_field(wp_unslash($_POST['provider'] ?? ''));

        if ($image_url === '') {
            wp_send_json_error(['message' => __('Image URL is missing.', 'wp-theme')], 400);
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_sideload_image($image_url, 0, $title, 'id');
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => $attachment_id->get_error_message()], 500);
        }

        wp_update_post([
            'ID' => $attachment_id,
            'post_title' => $title !== '' ? $title : __('Imported image', 'wp-theme'),
            'post_excerpt' => $creator !== '' ? sprintf(__('Photo by %s', 'wp-theme'), $creator) : '',
        ]);

        if ($title !== '') {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', $title);
        }
        if ($license !== '') {
            update_post_meta($attachment_id, '_bbtheme_image_license', $license);
        }
        if ($source_url !== '') {
            update_post_meta($attachment_id, '_bbtheme_image_source_url', $source_url);
        }
        if ($provider !== '') {
            update_post_meta($attachment_id, '_bbtheme_image_provider', $provider);
        }

        $opt_result = wp_theme_optimize_attachment_image($attachment_id);

        wp_send_json_success([
            'attachment_id' => $attachment_id,
            'edit_url' => get_edit_post_link($attachment_id, 'raw'),
            'message' => $opt_result['message'] ?? __('Image imported into Media Library.', 'wp-theme'),
        ]);
    }
}
add_action('wp_ajax_wp_theme_free_image_import', 'wp_theme_free_image_import');

if (!function_exists('wp_theme_enqueue_admin_assets')) {
    function wp_theme_enqueue_admin_assets($hook) {
        $is_theme_settings = strpos((string) $hook, 'wp-theme-settings') !== false;
        $is_media_library = $hook === 'upload.php';
        if (!$is_theme_settings && !$is_media_library) {
            return;
        }

        foreach ([
            get_template_directory() . '/assets/css/admin-theme-settings.css',
            get_template_directory() . '/assets/css/admin-animations.css',
        ] as $file) {
            if (file_exists($file)) {
                $relative = str_replace(wp_normalize_path(get_template_directory()), '', wp_normalize_path($file));
                wp_enqueue_style('wp-theme-admin-' . md5($file), get_template_directory_uri() . $relative, [], filemtime($file));
            }
        }

        $js_file = get_template_directory() . '/assets/js/admin-theme-settings.js';
        if (file_exists($js_file)) {
            wp_enqueue_style('bbtheme-animate-admin', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', [], '4.1.1');
            wp_enqueue_script('wp-theme-settings-admin', get_template_directory_uri() . '/assets/js/admin-theme-settings.js', [], filemtime($js_file), true);
            wp_localize_script('wp-theme-settings-admin', 'BBThemeAdminSettings', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_theme_settings_nonce'),
                'animationRegistry' => function_exists('bbtheme_get_animation_registry') ? bbtheme_get_animation_registry() : [],
                'strings' => [
                    'searching' => __('Searching images…', 'wp-theme'),
                    'importing' => __('Importing image…', 'wp-theme'),
                    'noResults' => __('No images found for that search.', 'wp-theme'),
                    'searchError' => __('Could not search images right now.', 'wp-theme'),
                    'importError' => __('Could not import image right now.', 'wp-theme'),
                    'imported' => __('Imported to Media Library.', 'wp-theme'),
                    'copyLabel' => __('Copy class', 'wp-theme'),
                    'copiedLabel' => __('Copied', 'wp-theme'),
                    'defaultPreviewText' => __('Animation preview', 'wp-theme'),
                    'demoImportText' => __('Importing demo homepage…', 'wp-theme'),
                    'demoImportedText' => __('Demo homepage imported and set as front page.', 'wp-theme'),
                ],
            ]);
        }
    }
}
add_action('admin_enqueue_scripts', 'wp_theme_enqueue_admin_assets', 20);


if (!function_exists('wp_theme_register_dynamic_media_sizes')) {
    function wp_theme_register_dynamic_media_sizes() {
        $tokens = wp_theme_style_tokens();
        add_image_size('wp-theme-sm', max(200, absint($tokens['theme_media_size_sm'] ?? 480)), 0, false);
        add_image_size('wp-theme-md', max(320, absint($tokens['theme_media_size_md'] ?? 960)), 0, false);
        add_image_size('wp-theme-lg', max(480, absint($tokens['theme_media_size_lg'] ?? 1600)), 0, false);
        add_image_size('wp-theme-xl', max(640, absint($tokens['theme_media_size_xl'] ?? 1920)), 0, false);
    }
}
add_action('after_setup_theme', 'wp_theme_register_dynamic_media_sizes', 30);

if (!function_exists('wp_theme_image_sizes_choose')) {
    function wp_theme_image_sizes_choose($sizes) {
        return array_merge($sizes, [
            'wp-theme-sm' => __('Theme Small', 'wp-theme'),
            'wp-theme-md' => __('Theme Medium', 'wp-theme'),
            'wp-theme-lg' => __('Theme Large', 'wp-theme'),
            'wp-theme-xl' => __('Theme XL', 'wp-theme'),
        ]);
    }
}
add_filter('image_size_names_choose', 'wp_theme_image_sizes_choose');

if (!function_exists('wp_theme_optimize_any_uploaded_image')) {
    function wp_theme_optimize_any_uploaded_image($attachment_id) {
        if (!wp_attachment_is_image($attachment_id) || get_post_meta($attachment_id, '_wp_theme_optimized', true)) {
            return;
        }
        $result = wp_theme_optimize_attachment_image($attachment_id);
        update_post_meta($attachment_id, '_wp_theme_optimized', 1);
        if (!empty($result['message'])) {
            update_post_meta($attachment_id, '_wp_theme_optimization_message', sanitize_text_field($result['message']));
        }
    }
}
add_action('add_attachment', 'wp_theme_optimize_any_uploaded_image', 20);


if (!function_exists('wp_theme_optimize_existing_attachment_ajax')) {
    function wp_theme_optimize_existing_attachment_ajax() {
        check_ajax_referer('wp_theme_settings_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => __('You do not have permission to optimize images.', 'wp-theme')], 403);
        }

        $attachment_id = absint($_POST['attachment_id'] ?? 0);
        if ($attachment_id < 1 || !wp_attachment_is_image($attachment_id)) {
            wp_send_json_error(['message' => __('Please enter a valid image attachment ID.', 'wp-theme')], 400);
        }

        $result = wp_theme_optimize_attachment_image($attachment_id);
        update_post_meta($attachment_id, '_wp_theme_optimized', 1);
        if (!empty($result['message'])) {
            update_post_meta($attachment_id, '_wp_theme_optimization_message', sanitize_text_field($result['message']));
        }

        wp_send_json_success([
            'attachment_id' => $attachment_id,
            'message' => $result['message'] ?? __('Attachment optimized.', 'wp-theme'),
            'edit_url' => get_edit_post_link($attachment_id, 'raw'),
        ]);
    }
}
add_action('wp_ajax_wp_theme_optimize_existing_attachment', 'wp_theme_optimize_existing_attachment_ajax');

if (!function_exists('wp_theme_remove_legacy_options_menus')) {
    function wp_theme_remove_legacy_options_menus() {
        foreach ([
            'acf-options',
            'acf-options-general-settings',
            'theme-general-settings',
            'wp-options',
            'theme-options',
            'site-options',
            'general-settings',
        ] as $slug) {
            remove_menu_page($slug);
        }
    }
}
add_action('admin_menu', 'wp_theme_remove_legacy_options_menus', 999);


if (!function_exists('wp_theme_demo_import_markup')) {
    function wp_theme_demo_import_markup() {
        ob_start();
        ?>
        <div class="wp-theme-demo-import-ui">
            <div class="wp-theme-demo-import-card">
                <h3><?php esc_html_e('Import & run first demo', 'wp-theme'); ?></h3>
                <p><?php esc_html_e('Creates or updates a Demo Homepage page, sets it as the front page, and uses WP BBuilder plugin blocks where available.', 'wp-theme'); ?></p>
                <div class="wp-theme-demo-import-grid">
                    <div><strong><?php esc_html_e('Included sections', 'wp-theme'); ?></strong><div><?php esc_html_e('Header, Hero, Logos, Features, How It Works, Products/Services, Testimonials, Pricing, FAQ, Blog, CTA, Footer.', 'wp-theme'); ?></div></div>
                    <div><strong><?php esc_html_e('Block usage', 'wp-theme'); ?></strong><div><?php esc_html_e('Uses WP BBuilder rows, columns, buttons, accordion, CTA section, and blog filter blocks.', 'wp-theme'); ?></div></div>
                </div>
                <p>
                    <button type="button" class="button button-primary button-hero" id="wp-theme-import-demo-homepage"><?php esc_html_e('Import & Run First Demo', 'wp-theme'); ?></button>
                </p>
                <p class="description" id="wp-theme-demo-import-status"><?php esc_html_e('You can run this multiple times. The page slug stays demo-homepage.', 'wp-theme'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}


if (!function_exists('wp_theme_demo_homepage_content')) {
    function wp_theme_demo_homepage_content() {
        return <<<'HTML'
<!-- wp:wpbb/row {"gutterX":"gx-5","gutterY":"gy-4","customClasses":"container py-5 align-items-center wp-theme-demo-homepage"} -->
<!-- wp:wpbb/column {"xs":12,"md":7} -->
<!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">Standard Business / SaaS</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"wp-theme-demo-heading"} --><h1 class="wp-block-heading wp-theme-demo-heading">Build a cleaner SaaS website with WP BBuilder-powered sections.</h1><!-- /wp:heading -->
<!-- wp:paragraph {"className":"wp-theme-demo-copy"} --><p class="wp-theme-demo-copy">A polished starter demo with hero, trust logos, features, pricing, FAQ, blog, and CTA — ready to customize with Bootstrap-friendly blocks.</p><!-- /wp:paragraph -->
<!-- wp:wpbb/button {"text":"Start Free Trial","url":"#pricing","btnClass":"btn btn-primary px-4 py-2 me-2"} /-->
<!-- wp:wpbb/button {"text":"See Pricing","url":"#pricing","btnClass":"btn btn-outline-dark px-4 py-2"} /-->
<!-- wp:html --><div class="row g-3 mt-4"><div class="col-md-4"><div class="wp-theme-demo-stat"><strong>12k+</strong><br/>active users</div></div><div class="col-md-4"><div class="wp-theme-demo-stat"><strong>42%</strong><br/>faster onboarding</div></div><div class="col-md-4"><div class="wp-theme-demo-stat"><strong>99.9%</strong><br/>uptime target</div></div></div><!-- /wp:html -->
<!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":5} -->
<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="https://placehold.co/960x680/e2e8f0/0f172a?text=SaaS+Dashboard" alt="Dashboard preview"/></figure><!-- /wp:image -->
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container pb-5 wp-theme-demo-homepage text-center"} -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+1" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+2" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+3" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+4" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+5" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":6,"md":2} --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} --><figure class="wp-block-image size-medium"><img src="https://placehold.co/180x60/ffffff/64748b?text=Logo+6" alt=""/></figure><!-- /wp:image --><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage"} -->
<!-- wp:wpbb/column {"xs":12} --><!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">Features</p><!-- /wp:paragraph --><!-- wp:heading {"level":2,"className":"wp-theme-demo-heading"} --><h2 class="wp-block-heading wp-theme-demo-heading">Everything you need to launch a modern business site.</h2><!-- /wp:heading --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":4} --><!-- wp:html --><div class="wp-theme-demo-card"><div class="wp-theme-demo-icon">01</div><h4>Reusable blocks</h4><p>Mix Gutenberg content with structured builder blocks for faster editing.</p></div><!-- /wp:html --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":4} --><!-- wp:html --><div class="wp-theme-demo-card"><div class="wp-theme-demo-icon">02</div><h4>Bootstrap layout</h4><p>Rows, columns, utilities, and spacing stay consistent across the site.</p></div><!-- /wp:html --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":4} --><!-- wp:html --><div class="wp-theme-demo-card"><div class="wp-theme-demo-icon">03</div><h4>Conversion flow</h4><p>Pricing, FAQ, testimonials, blog, and CTA sections work together.</p></div><!-- /wp:html --><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage"} -->
<!-- wp:wpbb/column {"xs":12,"md":4} --><!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">How It Works</p><!-- /wp:paragraph --><!-- wp:heading {"level":2,"className":"wp-theme-demo-heading"} --><h2 class="wp-block-heading wp-theme-demo-heading">Go from idea to launch in three steps.</h2><!-- /wp:heading --><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":8} --><!-- wp:wpbb/timeline {"title":"Timeline","layout":"vertical","itemsJson":"[{"date":"Step 1","title":"Setup","text":"Install blocks, choose styles, and import the demo."},{"date":"Step 2","title":"Customize","text":"Replace content, logos, pricing, and brand visuals."},{"date":"Step 3","title":"Publish","text":"Set the page live and refine each section over time."}]"} /--><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage","anchor":"pricing"} -->
<!-- wp:wpbb/column {"xs":12} --><!-- wp:wpbb/pricecards {"title":"Pricing","currency":"£","styleVariant":"default","cardsJson":"[{"title":"Starter","price":"29","period":"/mo","text":"Core pages, forms, and analytics.","button":"Choose plan","featured":false},{"title":"Growth","price":"99","period":"/mo","text":"Automation, CRM, and advanced reporting.","button":"Choose plan","featured":true},{"title":"Scale","price":"Custom","period":"","text":"Bespoke onboarding, support, and optimization.","button":"Contact sales","featured":false}]","showFeatured":true} /--><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage"} -->
<!-- wp:wpbb/column {"xs":12} --><!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">FAQ</p><!-- /wp:paragraph --><!-- wp:heading {"level":2,"className":"wp-theme-demo-heading"} --><h2 class="wp-block-heading wp-theme-demo-heading">Frequently asked questions</h2><!-- /wp:heading --><!-- wp:wpbb/accordion {"flush":false} --><!-- wp:wpbb/accordion-item {"title":"Can I use this as my first live homepage?"} --><p>Yes. Import it, replace the placeholder copy, and set the page as your front page.</p><!-- /wp:wpbb/accordion-item --><!-- wp:wpbb/accordion-item {"title":"Does this use WP BBuilder blocks?"} --><p>Yes. The demo uses WP BBuilder rows, columns, buttons, accordion, CTA section, blog filter, and dynamic form blocks where available.</p><!-- /wp:wpbb/accordion-item --><!-- wp:wpbb/accordion-item {"title":"Can I swap sections out later?"} --><p>Absolutely. Each section is modular and can be edited or replaced independently.</p><!-- /wp:wpbb/accordion-item --><!-- /wp:wpbb/accordion --><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage"} -->
<!-- wp:wpbb/column {"xs":12} --><!-- wp:wpbb/blog-filter {"title":"Latest Posts","postsToShow":3,"buttonText":"Filter"} /--><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5 wp-theme-demo-homepage","anchor":"cta"} -->
<!-- wp:wpbb/column {"xs":12} --><!-- wp:wpbb/cta-section {"title":"Ready to launch your new homepage?","text":"Import the layout, replace the content, and start publishing with a cleaner workflow.","buttonText":"Request Demo","buttonUrl":"#"} /--><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
HTML;
    }
}

if (!function_exists('wp_theme_demo_blog_post_content')) {
    function wp_theme_demo_blog_post_content($index = 1) {
        return '<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus commodo, libero sit amet feugiat posuere, mauris arcu sodales turpis, sed bibendum enim nisl vel justo.</p><!-- /wp:paragraph -->'
            . '<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="https://placehold.co/1200x700/e2e8f0/0f172a?text=Demo+Post+' . intval($index) . '" alt="Demo post"/></figure><!-- /wp:image -->'
            . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Section heading</h3><!-- /wp:heading -->'
            . '<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at arcu non risus laoreet iaculis. Integer ut augue nisl. Integer interdum sem quis arcu porta, et feugiat dui finibus.</p><!-- /wp:paragraph -->'
            . '<!-- wp:list --><ul><li>First benefit point</li><li>Second benefit point</li><li>Third benefit point</li></ul><!-- /wp:list -->';
    }
}

if (!function_exists('wp_theme_demo_about_page_content')) {
    function wp_theme_demo_about_page_content() {
        return <<<'HTML'
<!-- wp:wpbb/row {"gutterX":"gx-5","gutterY":"gy-4","customClasses":"container py-5"} -->
<!-- wp:wpbb/column {"xs":12,"md":6} -->
<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="https://placehold.co/900x700/e2e8f0/0f172a?text=About+Us" alt="About us"/></figure><!-- /wp:image -->
<!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":6} -->
<!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">About</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"wp-theme-demo-heading"} --><h1 class="wp-block-heading wp-theme-demo-heading">A simple story, told with clean blocks.</h1><!-- /wp:heading -->
<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec magna justo. Sed posuere sem vel leo feugiat, ac mattis nunc maximus.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus in orci pharetra tempor sed at nunc.</p><!-- /wp:paragraph -->
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->

<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"container py-5"} -->
<!-- wp:wpbb/column {"xs":12} -->
<!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">Timeline</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":2,"className":"wp-theme-demo-heading"} --><h2 class="wp-block-heading wp-theme-demo-heading">Milestones</h2><!-- /wp:heading -->
<!-- wp:html --><div class="row g-4"><div class="col-md-4"><div class="wp-theme-demo-card"><strong>2019</strong><p>Company founded and first service offer launched.</p></div></div><div class="col-md-4"><div class="wp-theme-demo-card"><strong>2022</strong><p>Expanded into productized services and platform workflows.</p></div></div><div class="col-md-4"><div class="wp-theme-demo-card"><strong>2026</strong><p>New modern marketing site and reusable content system released.</p></div></div></div><!-- /wp:html -->
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
HTML;
    }
}

if (!function_exists('wp_theme_demo_contact_page_content')) {
    function wp_theme_demo_contact_page_content() {
        return <<<'HTML'
<!-- wp:wpbb/row {"gutterX":"gx-5","gutterY":"gy-4","customClasses":"container py-5 align-items-start"} -->
<!-- wp:wpbb/column {"xs":12,"md":5} -->
<!-- wp:paragraph {"className":"wp-theme-demo-kicker"} --><p class="wp-theme-demo-kicker">Contact</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"wp-theme-demo-heading"} --><h1 class="wp-block-heading wp-theme-demo-heading">Let’s talk about your project.</h1><!-- /wp:heading -->
<!-- wp:html --><div class="wp-theme-demo-card"><p><strong>Email</strong><br/>hello@example.com</p><p><strong>Phone</strong><br/>+44 0000 000000</p><p><strong>Address</strong><br/>123 Business Street, London</p></div><!-- /wp:html -->
<!-- wp:wpbb/dynamic-form {"showTitle":true,"formTitle":"Send us a message"} /-->
<!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"md":7} -->
<!-- wp:embed {"url":"https://www.google.com/maps?q=London&output=embed","type":"rich","providerNameSlug":"google"} -->
<figure class="wp-block-embed is-type-rich is-provider-google wp-block-embed-google"><div class="wp-block-embed__wrapper">https://www.google.com/maps?q=London&output=embed</div></figure>
<!-- /wp:embed -->
<!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
HTML;
    }
}

if (!function_exists('wp_theme_seed_demo_blog_posts')) {
    function wp_theme_seed_demo_blog_posts() {
        for ($i = 1; $i <= 5; $i++) {
            $slug = 'demo-blog-post-' . $i;
            $existing = get_page_by_path($slug, OBJECT, 'post');
            $args = [
                'post_title'   => 'Demo Blog Post ' . $i,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'post',
                'post_content' => wp_theme_demo_blog_post_content($i),
            ];
            if ($existing instanceof WP_Post) {
                $args['ID'] = $existing->ID;
                wp_update_post($args);
            } else {
                wp_insert_post($args);
            }
        }
    }
}

if (!function_exists('wp_theme_seed_demo_pages')) {
    function wp_theme_seed_demo_pages() {
        $pages = [
            'about' => ['title' => 'About', 'content' => wp_theme_demo_about_page_content()],
            'contact' => ['title' => 'Contact', 'content' => wp_theme_demo_contact_page_content()],
        ];

        foreach ($pages as $slug => $data) {
            $existing = get_page_by_path($slug);
            $args = [
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $data['content'],
            ];
            if ($existing instanceof WP_Post) {
                $args['ID'] = $existing->ID;
                wp_update_post($args);
            } else {
                wp_insert_post($args);
            }
        }
    }
}

if (!function_exists('wp_theme_import_demo_homepage')) {
    function wp_theme_import_demo_homepage() {
        $page = get_page_by_path('demo-homepage');
        $args = [
            'post_title'   => 'Demo Homepage',
            'post_name'    => 'demo-homepage',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => wp_theme_demo_homepage_content(),
        ];

        if ($page instanceof WP_Post) {
            $args['ID'] = $page->ID;
            $page_id = wp_update_post($args, true);
        } else {
            $page_id = wp_insert_post($args, true);
        }

        if (is_wp_error($page_id)) {
            return $page_id;
        }

        wp_theme_seed_demo_blog_posts();
        wp_theme_seed_demo_pages();

        update_post_meta($page_id, '_wp_theme_demo_homepage', 1);
        update_option('show_on_front', 'page');
        update_option('page_on_front', (int) $page_id);

        return $page_id;
    }
}

if (!function_exists('wp_theme_ajax_import_demo_homepage')) {
    function wp_theme_ajax_import_demo_homepage() {
        check_ajax_referer('wp_theme_settings_nonce', 'nonce');

        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-theme')], 403);
        }

        $page_id = wp_theme_import_demo_homepage();
        if (is_wp_error($page_id)) {
            wp_send_json_error(['message' => $page_id->get_error_message()], 500);
        }

        wp_send_json_success([
            'pageId' => $page_id,
            'editUrl' => get_edit_post_link($page_id, 'raw'),
            'viewUrl' => get_permalink($page_id),
            'message' => __('Demo homepage, 5 demo blog posts, About page, and Contact page imported.', 'wp-theme'),
        ]);
    }
}
add_action('wp_ajax_wp_theme_import_demo_homepage', 'wp_theme_ajax_import_demo_homepage');

