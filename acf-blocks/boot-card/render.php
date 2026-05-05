<?php
if (!defined('ABSPATH')) exit;
require_once dirname(__DIR__) . '/_helpers.php';

$title = wpbb_acf_field('title', '');
$text = wpbb_acf_field('text', '');
$image = wpbb_acf_field('image', []);
$button_text = wpbb_acf_field('button_text', '');
$button_url = wpbb_acf_field('button_url', '');
$card_classes = wpbb_acf_field('card_classes', 'card h-100');
?>
<div class="<?php echo esc_attr($card_classes); ?>">
  <?php if (!empty($image['url'])): ?><img class="card-img-top" src="<?php echo esc_url($image['url']); ?>" alt=""><?php endif; ?>
  <div class="card-body">
    <?php if ($title): ?><h3 class="card-title"><?php echo esc_html($title); ?></h3><?php endif; ?>
    <?php if ($text): ?><div class="card-text"><?php echo wp_kses_post(wpautop($text)); ?></div><?php endif; ?>
    <?php if ($button_text && $button_url): ?><p><a class="btn btn-primary" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a></p><?php endif; ?>
  </div>
</div>
