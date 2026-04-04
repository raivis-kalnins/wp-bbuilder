<?php
	$id = 0;
	$dir = plugins_url('', dirname(__FILE__) );
	$blockClass = 'wp-block-cta-card-block'; 
	$cta_card_icon = get_fields()['cta_card_icon'] ?? '';
	$cta_card_logo_tpl = get_fields()['cta_card_logo_tpl'] ?? '';
	$cta_card_ico_tpl = get_fields()['cta_card_ico_tpl'] ?? '';
	$cta_card_centred_tpl = get_fields()['cta_card_centred_tpl'] ?? '';
	$cta_card_caption = get_fields()['cta_card_caption'] ?? '';
	$cta_card_content = get_fields()['cta_card_content'] ?? '';
	$cta_card_button_url = get_fields()['cta_card_button']['url'] ?? '';
	$cta_card_button_title = get_fields()['cta_card_button']['title'] ?? '';
    $cta_card_price = get_fields()['cta_card_price'] ?? '';
    $schema_product_card = get_fields()['schema_product_card'] ?? '';
?>
<div class="<?=$blockClass?><?php if( $cta_card_logo_tpl == 'true') { ?> wp-block-cta-card-block__logo<?php } ?><?php if( $cta_card_ico_tpl == 'true') { ?> wp-block-cta-card-block__ico<?php } ?><?php if( $cta_card_centred_tpl == 'true') { ?> wp-block-cta-card-block__centred<?php } ?>" <?php if( $schema_product_card == 'true' ) { ?>itemscope itemtype="https://schema.org/Product"<?php } ?>>
	<div class="wp-block-cta-card-block__content faux-link__element" style="position:relative;padding:25px;">
		<div class="row card-wrapper">
			<div class="col col-12 wp-block-cta-card-block__content align-self-center" <?php if( $schema_product_card == 'true') { ?> itemprop="offers" itemscope itemtype="https://schema.org/Offer"<?php } ?>>
                <?php if( $cta_card_icon ) : ?><div class="img-wrap"><img <?php if( $schema_product_card == 'true') { ?> itemprop="image"<?php } ?> src="<?=$cta_card_icon ?>" alt="img-<?=$cta_card_caption?>" /></div><?php endif; ?>
                <div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
				<?php if( $cta_card_caption ) : ?><div class="h3" <?php if( $schema_product_card == 'true') { ?> itemprop="name"<?php } ?>><?=$cta_card_caption?></div><?php endif; ?>
				<?php if( $cta_card_content ) : ?><p class="desc" <?php if( $schema_product_card == 'true') { ?> itemprop="description"<?php } ?>><?=$cta_card_content?></p><?php endif; ?>
                <?php if( ( $schema_product_card == 'true' ) && ( $cta_card_price ) ) { ?><div class="price" ><em <?php if( $schema_product_card == 'true') { ?> itemprop="priceCurrency" content="GBP"<?php } ?>>£</em> <span <?php if( $schema_product_card == 'true') { ?> itemprop="price"<?php } ?> content="<?=$cta_card_price?>"><?=$cta_card_price?></span></div><?php } ?>
				<?php if( $cta_card_button_url ) : ?><a itemprop="url" href="<?=$cta_card_button_url?>" class="btn btn-primary above-faux"><?=$cta_card_button_title ?></a><?php endif; ?>
			</div>
		</div>
		<?php if( $cta_card_button_url ) : ?><a href="<?=$cta_card_button_url?>" class="faux-link__overlay-link"></a><?php endif; ?>
	</div>
</div>