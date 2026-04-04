<?php 
	$blockClass = 'wp-block-' . str_replace('/', '-', $block->parsed_block['blockName']); 
	$fields = get_fields('option');		
	$google_url = $fields['google_url'] ?? '';
	$embed_map = $fields['embed_map'] ?? '';
?>
<div class="<?= $blockClass ?> google-map-block__content"><?=$embed_map?></div>