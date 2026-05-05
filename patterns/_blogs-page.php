<?php
/**
 * Title: Blogs Page
 * Slug: blogs-page
 * Categories: wp-patterns-main-core
 */
?>
<!-- wp:spacer {"height":"90px"} --><div style="height:90px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer -->
<!-- wp:shortcode -->[custom_hero_blog]<!-- /wp:shortcode -->
<!-- wp:group {"tagName":"main","className":"wp-theme-section","layout":{"type":"constrained"}} -->
<main class="wp-block-group wp-theme-section" id="wp-theme-main">
	<!-- wp:shortcode -->[wp_theme_breadcrumbs]<!-- /wp:shortcode -->
	<!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column {"width":"70%"} --><div class="wp-block-column" style="flex-basis:70%"><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">Blog</h1><!-- /wp:heading --><!-- wp:post-content /--><!-- wp:shortcode -->[cat_listed_cpt]<!-- /wp:shortcode --><!-- wp:shortcode -->[posts_cpt]<!-- /wp:shortcode --></div><!-- /wp:column --><!-- wp:column {"width":"30%"} --><div class="wp-block-column" style="flex-basis:30%"><!-- wp:pattern {"slug":"blog-sidebar"} /--></div><!-- /wp:column --></div><!-- /wp:columns -->
</main>
<!-- /wp:group -->
