<?php
	/**
	 * Hero section
	 */
	$dir = plugins_url('', dirname(__FILE__) );
	//var_dump($dir);
	$block_id = '';
	if ( ! empty( $block['anchor'] ) ) { $block_id = esc_attr( $block['anchor'] ); }
	$class_name = 'hero-section-block-acf';
	if ( ! empty( $block['className'] ) ) {	$class_name .= ' ' . $block['className']; }
	$section_acf = get_fields('acf/herosection');
	$front_render = include 'front-render.php';
	$inner_blocks_template = '';
?>
<InnerBlocks class="hero-block-acf__innerblocks" template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>"	templateLock="all" />
<?php if ( ! $is_preview ) { ?>
	<?php esc_html( $front_render ); ?>
<?php } else { ?>
	<hr />
	<p class="has-text-align-center"><button class="components-button is-primary is-compact">Edit Hero</button> <?php echo esc_html( $http_response_header ); ?></p>
	<hr />
</div>
<?php } ?>