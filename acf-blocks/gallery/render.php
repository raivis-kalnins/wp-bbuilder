<?php
if (!defined('ABSPATH')) exit;
require_once dirname(__DIR__) . '/_helpers.php';

$images = wpbb_acf_field('images', []);
$columns = wpbb_acf_field('columns', '3');
$gap = wpbb_acf_field('gap_class', 'g-3');
if (empty($images) || !is_array($images)) return;
?>
<div class="row row-cols-2 row-cols-md-<?php echo esc_attr($columns); ?> <?php echo esc_attr($gap); ?>">
  <?php foreach ($images as $image): ?>
    <div class="col"><img class="img-fluid rounded" src="<?php echo esc_url($image['url']); ?>" alt=""></div>
  <?php endforeach; ?>
</div>
