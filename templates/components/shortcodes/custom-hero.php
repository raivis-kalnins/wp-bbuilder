<?php  //ini_set('display_errors', 1);
/**
 * Add short code Custom Hero [custom_hero_shop]
 */
$id = 1;
$heroID = $id + 1;
$HeroItems = get_fields('option')['custom_heros'] ?? '';
$blockClass = 'wp-block-hero-section-block';
$hero_sec_button_btn = '';
if ( !empty($HeroItems) ) :
	foreach( $HeroItems as $h ) : 
		//var_dump($h);
		$hero_sec_id = $h['hero_id'] ?? '';
		$hero_sec_bg = $h['hero_sec_background'] ?? '';
		$hero_h_first = $h['h_first'] ?? '';
		$hero_sec_caption = $h['hero_sec_caption'] ?? '';
		$hero_sec_txt = $h['hero_sec_txt'] ?? '';
		$hero_sec_button_url = $h['hero_sec_button']['url'] ?? '';
		$hero_sec_button_title = $h['hero_sec_button']['title'] ?? '';
		if( $hero_sec_caption ) {
			$hero_sec_caption = '<h1 class="wp-block-heading" style="color:white">'.$hero_sec_caption.'</h1>';
		} else {
			$hero_sec_caption = '<!-- wp:post-title {"level":1,"className":"h1","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white"} /-->';
		};
		if( $hero_sec_txt ) : $hero_sec_txt = '<p>'.$hero_sec_txt.'</p>'; endif;
		if( $hero_sec_button_url ) : $hero_sec_button_btn = '<a href="'.$hero_sec_button_url.'" class="btn btn-primary">'.$hero_sec_button_title.'</a>'; endif;
		$loop =  $loop ?? '';
		$loop =	'<div class="'.$blockClass.'__content container">
					<div class="section-wrapper">
						<div class="wp-block-hero-section-block__content">'.$hero_sec_caption.$hero_sec_txt.$hero_sec_button_btn.'<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div></div>
					</div>
				</div>';
		$eachHero = '<div class="'.$blockClass.'" style="position:relative; background:transparent url('.$hero_sec_bg.') center / cover no-repeat;">'.$loop.'</div>';
		add_shortcode( 'custom_hero_'.$hero_sec_id, function() use ($eachHero) {
			return $eachHero;
		});
	endforeach;
endif;
