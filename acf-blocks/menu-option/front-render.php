<?php
	$id = 0;
	$dir = plugins_url('', dirname(__FILE__) );
	$blockClass = 'wp-block-menu-option-block'; 
	$menu_option = get_fields()['menu_option'] ?? '';

	// Menu Walkers
	$wp_header_top_menu = array(
		'theme_location' => 'wp-header-top-menu',
		'container' => 'nav',
		'container_class' => 'header-top-menu-block__content--list',						
		'container_id' => 'wp-header-top-menu',
		'menu_class' => 'wp-header-top-menu-container',
		'fallback_cb' => '__return_false',
		'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
		'depth' => 1, // 1 = no dropdowns, 2+ dropdowns
		'before' => '<input class="item-sub" type="checkbox" name="nav" style="opacity:0">',
		'after' => '',
		'link_before' => '',
		'link_after' => '',
		'walker' => new bootstrap_5_wp_nav_menu_walker()
	) ?? '';
	$wp_header_menu = array(
		'theme_location' 	=> 'wp-header-menu',
		'depth'             => 4,
		'container'         => false,
		'menu_class'        => '',
		'before' 			=> '<input class="item-sub" type="checkbox" name="menu-item" style="opacity:0"><em></em>',
		'fallback_cb'       => '__return_false',
		'items_wrap' 		=> '<ul id="wp-header-menu" class="menu-item__sub-wrap navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
		'walker'            => new bootstrap_5_wp_nav_menu_walker(),
	) ?? '';

	$wp_footer_menu = array(
		'theme_location' => 'wp-footer-menu',
		'container' => 'nav',
		'container_class' => 'foo-menu-block__content--list',						
		'container_id' => 'wp-footer-menu',
		'menu_class' => 'wp-footer-menu-container',
		'fallback_cb' => '__return_false',
		'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
		'depth' => 2, // 2 level listing
		'before' => '<input class="item-sub" type="checkbox" name="nav" style="opacity:0">',
		'after' => '',
		'link_before' => '',
		'link_after' => '',
		'walker' => new bootstrap_5_wp_nav_menu_walker()
	) ?? '';
?>
<?php if( $menu_option == 'WP Header Top Menu' ) { ?>
	<div class="header-top-menu-block">
		<?php wp_nav_menu( $wp_header_top_menu ); ?>
	</div>
<?php } elseif ( $menu_option == 'WP Header Menu' ) { ?>
	<nav class="navbar navbar-expand-md navbar-light bg-light header-nav header-menu-block" role="navigation">
		<?php /* <span class="navbar-toggler-btn" data-target="#wp-header-menu" style="display:none"><em></em><em></em><em></em><input type="checkbox" class="checkbox" id="toggleBtn"  /></span> // HTML moved to Header pattern */ ?>
		<div class="container">
			<div class="collapse navbar-collapse wp-header-menu-container" id="wp-header-menu">
				<?php wp_nav_menu( $wp_header_menu ); ?>
				<div class="navbar_customer-account"><?=do_blocks('<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"alt","iconClass":"wc-block-customer-account__account-icon","backgroundColor":"#333333","textColor":"white","style":{"elements":{"link":{"color":{"text":"var:preset|color|black"}}}}} /-->')?></div>
				<div class="navbar_mini-cart"><?=do_blocks('<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag","addToCartBehaviour":"open_drawer","hasHiddenPrice":false,"priceColor":{"name":"White","slug":"white","color":"#ffffff","class":"has-white-product-count-color"},"iconColor":{"name":"White","slug":"white","color":"#ffffff","class":"has-white-product-count-color"},"productCountColor":{"color":"#f28c2e"}} /-->')?></div>
				<div class="navbar_wishlist"><!-- wp:shortcode -->[yith_wcwl_wishlist_url]<!-- /wp:shortcode --></div>
				<div class="navbar_language-bar"><ul id="lang-sw"><?php if ( function_exists('pll_the_languages') ) { pll_the_languages( array( 'show_flags' => 1,'show_names' => 0 ) ); } ?></ul></div>
				<div class="navbar_search-bar"><?=do_blocks('<!-- wp:search {"label":"","widthUnit":"%","buttonText":"Search","buttonPosition":"button-only","buttonUseIcon":true,"isSearchFieldHidden":true,"align":"left","fontSize":"small"} /-->')?></div>
				<div class="navbar_light-dark"><i class="fas fa-moon"></i><i class="fas fa-sun"></i><div class="ball"></div></div>
			</div>
		</div>
	</nav>
	<div class="header-nav-overlay" style="display:none"></div>
<?php } elseif( $menu_option == 'WP Footer Menu' ) { ?>
	<div class="foo-menu-block">
		<?php wp_nav_menu( $wp_footer_menu ); ?>
	</div>
<?php } else { ?>
	<div>* Please choose navigation menu!</div>
<?php } ?>
