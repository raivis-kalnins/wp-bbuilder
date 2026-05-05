<?php
// Get ACF Images for sub menus
function wp_nav_menu_objects( $items, $args ) {

	$menu = wp_get_nav_menu_object($args->menu);
	$menuImage = '<script>document.addEventListener("DOMContentLoaded",function(){const nSub=document.getElementsByClassName("dropdown-menu"),img="<li class=megaMenuImage></li>",v1=nSub[0],v2=nSub[1],v3=nSub[2],v4=nSub[3],v5=nSub[4],v6=nSub[5],v7=nSub[6],v8=nSub[7],v9=nSub[8];if(v1){v1.innerHTML+=img};if(v2){v2.innerHTML+=img};if(v3){v3.innerHTML+=img};if(v4){v4.innerHTML+=img};if(v5){v5.innerHTML+=img};if(v6){v6.innerHTML+=img};if(v7){v7.innerHTML+=img};if(v8){v8.innerHTML+=img};if(v9){v9.innerHTML+=img};});</script>';
	echo $menuImage;

	// Menu images
	foreach( $items as $item ) :
		$menu_img = wp_get_attachment_image_url( $item->menu_img, 'full' );
		if( $menu_img ) :			
			$item->classes[0].= ' menu-item-img';
			$item->title.= "<style>#menu-item-$item->ID.nav-item-$item->ID.active ~ li.megaMenuImage {background-image:url('$menu_img');}</style>";
		endif;
		$mega_post_id = $item->mega_post_id;
		if( $mega_post_id ) :	
			$item->classes[0].= ' megamenu-'.$mega_post_id;
			$item->title.= '<div class="megamenu-modal megamenu-'.$mega_post_id.'" style="display:none">'.do_blocks(get_post_field('post_content',$mega_post_id)).'</div>';
		endif;
	endforeach;

	// Check Sticky Header Class
	$stickyheader = get_field('sticky_header', $menu);
	if ( $stickyheader == 'true' ):
		$html_stickyheader = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector("header");nav_menu.className +=" wp-nav-menu__sticky-header";});</script>';
		echo $html_stickyheader;
	endif;

	// Check Megamenu Class
	$megamenu = get_field('mega_menu', $menu);
	if ( $megamenu == 'true' ):
		$html_megamenu = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector(".header-nav");nav_menu.className +=" wp-nav-menu__megamenu";});</script>';
		echo $html_megamenu;
	endif;

	// Check last item Button
	$lastnavbtn = get_field('last_button', $menu);
	if ( $lastnavbtn == 'true' ):
		$html_lastnavbtn = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector(".header-nav");nav_menu.className +=" wp-nav-menu__lastnavbtn";});</script>';
		echo $html_lastnavbtn;
	endif;

	// Search - searchbox
	$search = get_field('search_bar', $menu);
	if ( $search == 'true' ):
		$html_search = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_search = document.querySelector("body");nav_search.className +=" wp-nav-menu__search_bar";});</script>';
		echo $html_search;
	endif;

	// MyAccount - need to be added
	$account = get_field('customer_account', $menu);
	if ( $account == 'true' ):
		$html_account = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector("body");nav_menu.className +=" wp-nav-menu__account";});</script>';
		echo $html_account;
	endif;

	// Cart - need to be added
	$cart = get_field('mini_cart', $menu);
	if ( $cart == 'true' ):
		$html_cart = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector("body");nav_menu.className +=" wp-nav-menu__cart";});</script>';
		echo $html_cart;
	endif;

	// Light/Dark Theme Body Class
	$light_dark = get_field('light_dark', $menu);
	if ( $light_dark == 'true' ):
		$html_light_dark = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector("body");nav_menu.className +=" wp-nav-menu__light-dark";});</script>';
		echo $html_light_dark;
	endif;

	// Languages Switch - need to be added
	$language = get_field('language_bar', $menu);
	if ( $language == 'true' ):
		$html_language = '<script>document.addEventListener("DOMContentLoaded", function() {const nav_menu = document.querySelector("body");nav_menu.className +=" wp-nav-menu__lang";});</script>';
		echo $html_language;
	endif;

	return $items;
}
add_filter('wp_nav_menu_objects', 'wp_nav_menu_objects', 10, 2);
