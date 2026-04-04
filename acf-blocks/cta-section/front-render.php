<?php
	$id = 0;
	$dir = plugins_url('', dirname(__FILE__) );
	$blockClass = 'wp-block-cta-section-block'; 
	$cta_sec_bg = get_fields()['cta_sec_background'] ?? '';
	$cta_sec_caption = get_fields()['cta_sec_caption'] ?? '';
	$cta_sec_content = get_fields()['cta_sec_content'] ?? '';
	$cta_sec_button_url = get_fields()['cta_sec_button']['url'] ?? '';
	$cta_sec_button_title = get_fields()['cta_sec_button']['title'] ?? '';
?>
<div class="<?=$blockClass?>">
	<div class="wp-block-cta-section-block__content faux-link__element" style="position:relative; padding:50px; text-align:center;<?php if( $cta_sec_bg ) : ?>background:url(<?=$cta_sec_bg ?>) center / cover no-repeat;<?php endif; ?>">
		<div class="row section-wrapper">
			<div class="col col-12 wp-block-cta-section-block__content align-self-center">
				<?php if( $cta_sec_caption ) : ?><h2><?=$cta_sec_caption?></h2><?php endif; ?>
				<?php if( $cta_sec_content ) : ?><?=$cta_sec_content?><?php endif; ?>
				<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
				<?php if( $cta_sec_button_url ) : ?><a href="<?=$cta_sec_button_url?>" class="btn btn-primary above-faux"><?=$cta_sec_button_title ?></a><?php endif; ?>
			</div>
		</div>
		<?php if( $cta_sec_button_url ) : ?><a href="<?=$cta_sec_button_url?>" class="faux-link__overlay-link"></a><?php endif; ?>
	</div>
</div>