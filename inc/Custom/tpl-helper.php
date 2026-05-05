<?php
/**
 * Theme Helper Class
 */

class TemplateHelper {
	/**
	 * Loads header main menu function
	 */
	public static function main_menu_section(array $args = []) {
		get_template_part('templates/components/menus/main-menu', null, $args);
	}
	/**
	 * Loads header top submenu function
	 */
	// public static function top_submenu_section(array $args = []) {
	// 	get_template_part('templates/components/menus/topsubmenu', null, $args);
	// }
}
