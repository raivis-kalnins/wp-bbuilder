<?php
	$id = 1;
	$heroID = $id + 1;
	$h = get_fields();
	$dir = plugins_url('', dirname(__FILE__) );
	$blockClass = 'wp-block-hero-section-block'; 
	$hero_sec_bg = $h['hero_sec_background'] ?? '';
	$hero_h_first = $h['h_first'] ?? '';
	$hero_sec_caption = $h['hero_sec_caption'] ?? '';
	$hero_sec_txt = $h['hero_sec_txt'] ?? '';
	$hero_sec_button_url = $h['hero_sec_button']['url'] ?? '';
	$hero_sec_button_title = $h['hero_sec_button']['title'] ?? '';
	$hero_sec_slider = $h['hero_sec_slider'] ?? '';
	$heroItems = $h['hero_items'] ?? '';
?>
<?php if( $hero_sec_slider == 'true' ) { ?>

	<div class="hero-slider">
		<div class="swiper-wrapper">
			<div class="<?=$blockClass?> hero-slide hero-slide_1 swiper-slide" style="position:relative; <?php if( $hero_sec_bg ) : ?>background:transparent url('<?=$hero_sec_bg ?>') center / cover no-repeat;<?php endif; ?>">
				<div class="<?=$blockClass?>__content faux-link__element container swiper-wrapper">			
					<div class="container section-wrapper">
						<div class="wp-block-hero-section-block__content">
							<?php if( $hero_sec_caption ) : ?><<?php if( $hero_h_first == 'true') { ?>h1<?php } else { ?>h2<?php } ?> class="wp-block-heading"><?=$hero_sec_caption?></<?php if( $hero_h_first == 'true' ) { ?>h1<?php } else { ?>h2<?php } ?>><?php endif; ?>
							<p><?php if( $hero_sec_txt ) : ?><?=$hero_sec_txt?><?php endif; ?></p>
							<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
							<?php if( $hero_sec_button_url ) : ?><a href="<?=$hero_sec_button_url?>" class="btn btn-primary above-faux"><?=$hero_sec_button_title ?></a><?php endif; ?>
						</div>
					</div>
					<?php if( $hero_sec_button_url ) : ?><a href="<?=$hero_sec_button_url?>" class="faux-link__overlay-link"></a><?php endif; ?>
				</div>
			</div>
			<?php foreach( $heroItems as $heroItem ): 
					$id++;
					$heroItem_sec_bg = $heroItem['hero_item_background'] ?? '';
					$heroItem_sec_caption = $heroItem['hero_item_caption'] ?? '';
					$heroItem_sec_txt = $heroItem['hero_item_txt'] ?? '';
					$heroItem_sec_button_url = $heroItem['hero_item_button']['url'] ?? '';
					$heroItem_sec_button_title = $heroItem['hero_item_button']['title'] ?? '';
			?>
				<div class="<?=$blockClass?> hero-slide hero-slide_<?=$heroID ?> swiper-slide" style="position:relative; <?php if( $heroItem_sec_bg ) : ?>background:transparent url('<?=$heroItem_sec_bg ?>') center / cover no-repeat;<?php endif; ?>">
					<div class="<?=$blockClass?>__content faux-link__element container swiper-wrapper">			
						<div class="container section-wrapper">
							<div class="wp-block-hero-section-block__content">
								<?php if( $heroItem_sec_caption ) : ?><h2 class="wp-block-heading h1"><?=$heroItem_sec_caption?></h2><?php endif; ?>
								<p><?php if( $heroItem_sec_txt ) : ?><?=$heroItem_sec_txt?><?php endif; ?></p>
								<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
								<?php if( $heroItem_sec_button_url ) : ?><a href="<?=$heroItem_sec_button_url?>" class="btn btn-primary above-faux"><?=$heroItem_sec_button_title ?></a><?php endif; ?>
							</div>
						</div>
						<?php if( $heroItem_sec_button_url ) : ?><a href="<?=$heroItem_sec_button_url?>" class="faux-link__overlay-link"></a><?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-button-prev"></div><div class="swiper-button-next"></div><div class="swiper-pagination"></div>
	</div>

<?php } else {  ?>

	<div class="<?=$blockClass?>" style="position:relative; <?php if( $hero_sec_bg ) : ?>background:transparent url('<?=$hero_sec_bg ?>') center / cover no-repeat;<?php endif; ?>">
		<div class="<?=$blockClass?>__content faux-link__element container">
			<div class="container section-wrapper">
				<div class="wp-block-hero-section-block__content">
					<?php if( $hero_sec_caption ) : ?><<?php if( $hero_h_first == 'true') { ?>h1<?php } else { ?>h2<?php } ?> class="wp-block-heading"><?=$hero_sec_caption?></<?php if( $hero_h_first == 'true' ) { ?>h1<?php } else { ?>h2<?php } ?>><?php endif; ?>
					<p><?php if( $hero_sec_txt ) : ?><?=$hero_sec_txt?><?php endif; ?></p>
					<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
					<?php if( $hero_sec_button_url ) : ?><a href="<?=$hero_sec_button_url?>" class="btn btn-primary above-faux"><?=$hero_sec_button_title ?></a><?php endif; ?>
				</div>
			</div>
			<?php if( $hero_sec_button_url ) : ?><a href="<?=$hero_sec_button_url?>" class="faux-link__overlay-link"></a><?php endif; ?>
		</div>
	</div>

<?php } ?>