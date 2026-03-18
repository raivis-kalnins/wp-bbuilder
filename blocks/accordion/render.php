<?php
/**
 * Accordion Block Render Template
 *
 * @package dp-blocks
 */

// Build accordion classes
$accordion_classes = ['accordion'];

if ( ! empty( $attributes['flush'] ) ) {
    $accordion_classes[] = 'accordion-flush';
}

if ( ! empty( $attributes['itemSpacing'] ) ) {
    $accordion_classes[] = $attributes['itemSpacing'];
}

$accordion_class = implode( ' ', $accordion_classes );
$accordion_id    = ! empty( $attributes['accordionId'] ) ? $attributes['accordionId'] : 'accordion-' . uniqid();
$always_open     = ! empty( $attributes['alwaysOpen'] ) ? 'true' : 'false';

// Data attributes for Bootstrap
$data_attrs = sprintf(
    'id="%s" data-bs-always-open="%s"',
    esc_attr( $accordion_id ),
    esc_attr( $always_open )
);

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $accordion_class ] ) ); ?> <?php echo $data_attrs; ?>>
    <?php echo $content; ?>
</div>