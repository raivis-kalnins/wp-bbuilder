<?php
	$id = 0;
	$dir = plugins_url('', dirname(__FILE__) );
	$blockClass = 'wp-block-acf-gallery-block'; 
	$acf_sec_gallery = get_fields()['acf_gallery'] ?? '';
?>
<div class="<?=$blockClass?>">
	<div class="wp-block-acf-gallery-block__content faux-link__element" style="position:relative; padding:50px; text-align:center;<?php if( $cta_sec_gallery ) : ?>background:url(<?=$cta_sec_gallery ?>) center / cover no-repeat;<?php endif; ?>">
		<div class="row section-wrapper">
			<div class="col col-12 wp-block-acf-gallery-block__content align-self-center">
                ...
			</div>
		</div>
	</div>
</div>