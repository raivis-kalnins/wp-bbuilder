<?php
/**
 * Gutenberg blocks functions
 */

/*
* Show Info text for Pages admin area
*/
function prefix_mb_callback() {
	if( get_post_type() == 'page' ) :
		echo 	
		'<label for="custom_info">
			<p style="color:red;font-size:18px">Reminder!!!</p> 
			<ul>
				<li>- To change Header & Footer menus: Mobile  Main menu <b>Apperance =></b> <a href="/wp-admin/nav-menus.php" target="_blank">Menus</a></li>
				<li>- To change Contact details or social media: <b>Options =></b> <a href="/wp-admin/admin.php?page=wp-options" target="_blank">Contact Info</a></li>
			</ul>
		</label>';
	endif;
}
function prefix_add_meta_boxes() {
	if( get_post_type() == 'page' ) :
		add_meta_box( 'unique_custom_info', __( 'Header / Footer','text-domain' ),'prefix_mb_callback', ['page', 'post'] );
	endif;
}
add_action('add_meta_boxes', 'prefix_add_meta_boxes' );

/*--------------------------------------------------------------
# Disable Specific Plugin Update
--------------------------------------------------------------*/
function disable_plugin_updates( $value ) {
	// Replace 'plugin-folder/plugin-file.php' with the actual path to the plugin's main file
	//$plugin_to_disable = 'advanced-custom-fields-pro/acf.php';

	if ( isset( $value ) && is_object( $value ) ) {
		if ( isset( $value->response[$plugin_to_disable] ) ) {
			unset( $value->response[$plugin_to_disable] );
		}
	}
	return $value;
}
//add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );
