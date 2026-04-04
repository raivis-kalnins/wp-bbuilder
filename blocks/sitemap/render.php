<?php $blockClass = 'wp-block-' . str_replace('/', '-', $block->parsed_block['blockName']); ?>
<div class="<?= $blockClass ?> sitemap-block__content">
	<h2 id="sitemap-pages">Pages</h2>
	<ul>
	<?php 
		wp_list_pages(
			array( 
				'exclude' => '139',
				'title_li' => '',
			)
		);
	?>
	</ul>
	<h2 id="sitemap-product">Products</h2>
	<ul>
	<?php 
	$postsArgs = array(
		'post_type' => 'product',
		'posts_per_page'=>'-1',
		//'post__not_in' => array(), 
	);
	$postsLoop = new WP_Query( $postsArgs );
	while ( $postsLoop->have_posts() ) {
		$postsLoop->the_post();
	?>
		<li <?php post_class(); ?>><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php } wp_reset_query(); ?>
	</ul>
</div>