<?php
/**
 * Tabs Block Render Template
 *
 * @package dp-blocks
 */

// Build nav classes
$nav_classes = ['nav'];

// Tab type
if ( ! empty( $attributes['type'] ) ) {
    $nav_classes[] = 'nav-' . sanitize_html_class( $attributes['type'] );
}

// Alignment
if ( ! empty( $attributes['alignment'] ) ) {
    $nav_classes[] = 'justify-content-' . sanitize_html_class( $attributes['alignment'] );
}

// Vertical
if ( ! empty( $attributes['vertical'] ) ) {
    $nav_classes[] = 'flex-column';
}

// Fill
if ( ! empty( $attributes['fill'] ) ) {
    $nav_classes[] = 'nav-fill';
}

// Justify
if ( ! empty( $attributes['justify'] ) ) {
    $nav_classes[] = 'nav-justified';
}

$nav_class = implode( ' ', $nav_classes );
$tabs_id     = ! empty( $attributes['tabsId'] ) ? $attributes['tabsId'] : 'tabs-' . uniqid();
$tab_type    = ! empty( $attributes['type'] ) ? $attributes['type'] : 'tabs';
$fade_effect = ! empty( $attributes['fadeEffect'] ) ? 'true' : 'false';

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
    <?php echo $content; ?>
</div>