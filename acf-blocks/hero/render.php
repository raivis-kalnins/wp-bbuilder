<?php
if (!defined('ABSPATH')) exit;
require_once dirname(__DIR__) . '/_helpers.php';

$title = wpbb_acf_field('title', '');
$text = wpbb_acf_field('text', '');
$button_text = wpbb_acf_field('button_text', '');
$button_url = wpbb_acf_field('button_url', '');
$background_image = wpbb_acf_field('background_image', []);
$theme = wpbb_acf_field('theme', 'light');
$title_size = wpbb_acf_field('title_size', 'display-3');
$text_size = wpbb_acf_field('text_size', 'lead');
$title_color = wpbb_acf_field('title_color', '');
$text_color = wpbb_acf_field('text_color', '');

$style = '';
if (!empty($background_image['url'])) $style .= 'background-image:url(' . esc_url($background_image['url']) . ');';
?>
<section class="wpbb-hero wpbb-hero--<?php echo esc_attr($theme); ?> alignfull" style="<?php echo esc_attr($style); ?>">
  <div class="container-fluid py-5">
    <?php if ($title): ?><h1 class="wpbb-hero__title <?php echo esc_attr($title_size); ?>" style="<?php echo esc_attr($title_color ? 'color:' . $title_color . ';' : ''); ?>"><?php echo esc_html($title); ?></h1><?php endif; ?>
    <?php if ($text): ?><div class="wpbb-hero__text <?php echo esc_attr($text_size); ?>" style="<?php echo esc_attr($text_color ? 'color:' . $text_color . ';' : ''); ?>"><?php echo wp_kses_post(wpautop($text)); ?></div><?php endif; ?>
    <?php if ($button_text && $button_url): ?><p><a class="btn btn-primary" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a></p><?php endif; ?>
  </div>
</section>
