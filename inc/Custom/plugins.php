<?php
/**
 * Disable specific plugin updates only: because can destroy template
 */
function my_filter_plugin_updates( $value ) {
	
	if( isset( $value->response['wp-all-import-pro/wp-all-import-pro.php'] ) ) {
		unset( $value->response['wp-all-import-pro/wp-all-import-pro.php'] );
	}
	
	if( isset( $value->response['wpai-woocommerce-add-on/wpai-woocommerce-add-on.php'] ) ) {
		unset( $value->response['wpai-woocommerce-add-on/wpai-woocommerce-add-on.php'] );
	}

    if( isset( $value->response['wp-all-export-pro/wp-all-export-pro.php'] ) ) {
		unset( $value->response['wp-all-export-pro/wp-all-export-pro.php'] );
	}
	
	if( isset( $value->response['wpae-woocommerce-add-on/wpae-woocommerce-add-on.php'] ) ) {
		unset( $value->response['wpae-woocommerce-add-on/wpae-woocommerce-add-on.php'] );
	}

    // if( isset( $value->response['woo-discount-rules/woo-discount-rules.php'] ) ) {
	// 	unset( $value->response['woo-discount-rules/woo-discount-rules.php'] );
	// }
	
	// if( isset( $value->response['woo-discount-rules-pro/woo-discount-rules-pro.php'] ) ) {
	// 	unset( $value->response['woo-discount-rules-pro/woo-discount-rules-pro.php'] );
	// }

	return $value;
}
add_filter( 'site_transient_update_plugins', 'my_filter_plugin_updates' );
