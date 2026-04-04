<?php
/**
 * Card Block Render Template
 *
 * @package dp-blocks
 */

// Build card classes
$card_classes = ['card'];

// Border color
if ( ! empty( $attributes['borderColor'] ) ) {
    $card_classes[] = 'border-' . sanitize_html_class( $attributes['borderColor'] );
}

// Background color
if ( ! empty( $attributes['backgroundColor'] ) ) {
    $card_classes[] = 'bg-' . sanitize_html_class( $attributes['backgroundColor'] );
}

// Text color
if ( ! empty( $attributes['textColor'] ) ) {
    $card_classes[] = 'text-' . sanitize_html_class( $attributes['textColor'] );
}

// Text alignment
if ( ! empty( $attributes['textAlignment'] ) ) {
    $card_classes[] = 'text-' . sanitize_html_class( $attributes['textAlignment'] );
}

$card_class = implode( ' ', $card_classes );

// Image attributes
$show_image       = ! empty( $attributes['showImage'] );
$image_url        = ! empty( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$image_alt        = ! empty( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
$image_position   = ! empty( $attributes['imagePosition'] ) ? $attributes['imagePosition'] : 'top';
$is_overlay       = $image_position === 'overlay';

// Header/Footer
$show_header = ! empty( $attributes['showHeader'] );
$show_footer = ! empty( $attributes['showFooter'] );
$header_text = ! empty( $attributes['headerText'] ) ? $attributes['headerText'] : '';
$footer_text = ! empty( $attributes['footerText'] ) ? $attributes['footerText'] : '';

?>
<div class="<?php echo esc_attr( $col_class ); ?>">
    <div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $card_class ] ) ); ?>>
        <?php if ( $show_image && $image_url && $image_position === 'top' ) : ?>
            <img src="<?php echo esc_url( $image_url ); ?>" class="card-img-top" alt="<?php echo esc_attr( $image_alt ); ?>">
        <?php endif; ?>

        <?php if ( $show_header ) : ?>
            <div class="card-header">
                <?php echo wp_kses_post( $header_text ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $show_image && $image_url && $is_overlay ) : ?>
            <img src="<?php echo esc_url( $image_url ); ?>" class="card-img" alt="<?php echo esc_attr( $image_alt ); ?>">
            <div class="card-img-overlay">
                <?php echo $content; ?>
            </div>
        <?php else : ?>
            <div class="card-body">
                <?php echo $content; ?>
            </div>
        <?php endif; ?>

        <?php if ( $show_footer ) : ?>
            <div class="card-footer">
                <?php echo wp_kses_post( $footer_text ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $show_image && $image_url && $image_position === 'bottom' ) : ?>
            <img src="<?php echo esc_url( $image_url ); ?>" class="card-img-bottom" alt="<?php echo esc_attr( $image_alt ); ?>">
        <?php endif; ?>
    </div>
</div>