<?php
	/**
	 * ACF CTA card
	 */
	$dir = plugins_url('', dirname(__FILE__) );
	//var_dump($dir);
	$block_id = '';
	$class_name = 'card-block-acf';
	if ( ! empty( $block['className'] ) ) {	$class_name .= ' ' . $block['className']; }
	$front_render = include 'front-render.php';
?>
<?php if ( $is_preview ) { ?>
	<div <?=wp_kses_data(get_block_wrapper_attributes(array('id' => $block_id,'class' => esc_attr( $class_name ))))?> >
		<?php esc_html( $front_render ); ?>
	</div>
<?php } ?>
