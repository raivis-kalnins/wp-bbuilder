<?php
	/**
	 * ACF CTA section
	 */
	$dir = plugins_url('', dirname(__FILE__) );
	//var_dump($dir);
	$block_id = '';
	if ( ! empty( $block['anchor'] ) ) { $block_id = esc_attr( $block['anchor'] ); }
	$class_name = 'section-block-acf';
	if ( ! empty( $block['className'] ) ) {	$class_name .= ' ' . $block['className']; }
	$section_acf = get_fields('acf/ctasection');
	$front_render = include 'front-render.php';
?>
<?php if ( !$is_preview ) { ?>
	<div <?=wp_kses_data(get_block_wrapper_attributes(array('id' => $block_id,'class' => esc_attr( $class_name ))))?> >
		<?php esc_html( $front_render ); ?>
	<?php } else { ?>
		<?php esc_html( $front_render ); ?>
<?php } if ( !$is_preview ) { ?></div><?php } ?>