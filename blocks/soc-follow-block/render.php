<?php $blockClass = 'wp-block-' . str_replace('/', '-', $block->parsed_block['blockName']); ?>
<div class="<?= $blockClass ?> soc-follow-block__content">
	<?php
		$fields = get_fields('option');
		$fb = $fields['soc_fb'] ?? '';
		$x = $fields['soc_x'] ?? '';
		$yt = $fields['soc_yt'] ?? '';
		$ln = $fields['soc_ln'] ?? '';
		$in = $fields['soc_in'] ?? '';
		$pr = $fields['soc_pr'] ?? '';

		$content = '
			<!-- wp:social-links {"iconColor":"foreground","iconColorValue":"#000000","openInNewTab":true,"size":"has-small-icon-size","align":"center","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"}} -->
			<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only">
				<!-- wp:social-link {"url":"'.$fb.'","service":"facebook"} /-->
				<!-- wp:social-link {"url":"'.$x.'","service":"x"} /-->
				<!-- wp:social-link {"url":"'.$yt.'","service":"youtube"} /-->
				<!-- wp:social-link {"url":"'.$ln.'","service":"linkedin"} /-->
				<!-- wp:social-link {"url":"'.$in.'","service":"instagram"} /-->
				<!-- wp:social-link {"url":"'.$pr.'","service":"pinterest"} /-->
			</ul>
			<!-- /wp:social-links -->';
		echo do_blocks($content);
	?>
</div>