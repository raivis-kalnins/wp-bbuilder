<?php
/**
 * Cards Grid Block Render Template
 *
 * @package dp-blocks
 */

// Build row classes
$row_classes = ['row'];

// Gutter
if ( ! empty( $attributes['gutter'] ) ) {
    $row_classes[] = sanitize_html_class( $attributes['gutter'] );
}

// Card group/deck
if ( ! empty( $attributes['cardGroup'] ) ) {
    $row_classes[] = 'card-group';
}

if ( ! empty( $attributes['cardDeck'] ) ) {
    $row_classes[] = 'card-deck';
}

// Equal height
$equal_height = ! empty( $attributes['equalHeight'] ) ? 'true' : 'false';

$row_class = implode( ' ', $row_classes );

// Column classes for responsive breakpoints
$col_classes = [];
if ( ! empty( $attributes['columnsMobile'] ) ) {
    $col_classes[] = 'col-' . floor( 12 / $attributes['columnsMobile'] );
}
if ( ! empty( $attributes['columnsTablet'] ) ) {
    $col_classes[] = 'col-sm-' . floor( 12 / $attributes['columnsTablet'] );
}
if ( ! empty( $attributes['columns'] ) ) {
    $col_classes[] = 'col-md-' . floor( 12 / $attributes['columns'] );
}

$col_class = implode( ' ', $col_classes );

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
    <div class="<?php echo esc_attr( $row_class ); ?>" data-equal-height="<?php echo esc_attr( $equal_height ); ?>">
        <?php echo $content; ?>
    </div>
</div>