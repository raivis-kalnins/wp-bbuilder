<?php
if (!defined('ABSPATH')) {
    exit;
}

while (have_posts()) {
    the_post();

    $event_name = function_exists('get_field') ? get_field('event_name') : '';
    $event_location = function_exists('get_field') ? get_field('event_location') : '';
    $event_date = function_exists('get_field') ? get_field('event_date') : '';
    $event_time = function_exists('get_field') ? get_field('event_time') : '';
    $event_short_description = function_exists('get_field') ? get_field('event_short_description') : '';
    $event_details = function_exists('get_field') ? get_field('event_details') : '';
    $event_excerpt = get_the_excerpt();

    if (!$event_name) {
        $event_name = get_the_title();
    }
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class('single-event-template'); ?>>
    <?php wp_body_open(); ?>
    <?php echo do_blocks('<!-- wp:template-part {"slug":"header","tagName":"header"} /-->'); ?>

    <main class="wp-block-group alignfull" style="padding:48px 20px;background:#fff;">
        <div class="wp-block-group alignwide" style="max-width:1100px;margin:0 auto;">
            <div class="wp-block-columns is-not-stacked-on-mobile">
                <div class="wp-block-column" style="flex-basis:68%">
                    <p class="has-small-font-size" style="letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">
                        <?php echo esc_html__('Event', 'wp-theme'); ?>
                    </p>
                    <h1 style="margin-top:0;margin-bottom:12px;"><?php echo esc_html($event_name); ?></h1>
                    <?php if ($event_short_description || $event_excerpt) : ?>
                        <div class="wp-block-group" style="margin:0 0 20px;padding:18px 20px;border-left:4px solid #d21629;background:#f8fafc;border-radius:12px;">
                            <p style="margin:0;font-size:1.05rem;line-height:1.7;color:#334155;"><?php echo wp_kses_post($event_short_description ? nl2br(esc_html($event_short_description)) : esc_html($event_excerpt)); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (has_post_thumbnail()) : ?>
                        <div style="margin:0 0 24px;overflow:hidden;border-radius:18px;">
                            <?php the_post_thumbnail('large', ['style' => 'width:100%;height:auto;display:block;']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($event_details) : ?>
                        <div class="wp-block-group" style="margin:0 0 24px;padding:22px;border:1px solid #e2e8f0;border-radius:18px;background:#fff;">
                            <h3 style="margin-top:0;margin-bottom:12px;"><?php echo esc_html__('Event Details', 'wp-theme'); ?></h3>
                            <div class="wp-block-post-content">
                                <?php echo wp_kses_post($event_details); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="wp-block-post-content">
                        <?php the_content(); ?>
                    </div>
                </div>
                <div class="wp-block-column" style="flex-basis:32%">
                    <div class="wp-block-group" style="padding:24px;border:1px solid #e2e8f0;border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(15,23,42,.05);">
                        <h3 style="margin-top:0;margin-bottom:16px;"><?php echo esc_html__('Event Details', 'wp-theme'); ?></h3>
                        <?php if ($event_name) : ?>
                            <p><strong><?php echo esc_html__('Event Name:', 'wp-theme'); ?></strong><br><?php echo esc_html($event_name); ?></p>
                        <?php endif; ?>
                        <?php if ($event_date) : ?>
                            <p><strong><?php echo esc_html__('Date:', 'wp-theme'); ?></strong><br><?php echo esc_html(function_exists('wp_theme_format_event_date') ? wp_theme_format_event_date($event_date) : $event_date); ?></p>
                        <?php endif; ?>
                        <?php if ($event_time) : ?>
                            <p><strong><?php echo esc_html__('Time:', 'wp-theme'); ?></strong><br><?php echo esc_html($event_time); ?></p>
                        <?php endif; ?>
                        <?php if ($event_location) : ?>
                            <p><strong><?php echo esc_html__('Location:', 'wp-theme'); ?></strong><br><?php echo esc_html($event_location); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php echo do_blocks('<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->'); ?>
    <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}
