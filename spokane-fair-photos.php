<?php

/**
 * Plugin Name: Spokane Interstate Fair Photo Submissions
 * Plugin URI: http://8feetacross.com/spokane-interstate-fair-photo-contest/
 * Description: A custom plugin for the Spokane Interstate Fair Photo Contest
 * Author: Spokane WordPress Development
 * Author URI: http://www.spokanewp.com
 * Version: 1.0.0
 * Text Domain: spokane-fair
 *
 * Copyright 2016 Spokane WordPress Development
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

require_once ( 'classes/SpokaneFair/Controller.php' );
require_once ( 'classes/SpokaneFair/Category.php' );
require_once ( 'classes/SpokaneFair/CategoryTable.php' );
require_once ( 'classes/SpokaneFair/Entry.php' );
require_once ( 'classes/SpokaneFair/Order.php' );
require_once ( 'classes/SpokaneFair/Photographer.php' );

$controller = new \SpokaneFair\Controller;

/* activate */
register_activation_hook( __FILE__, array( $controller, 'activate' ) );

/* enqueue js and css */
add_action( 'init', array( $controller, 'init' ) );

/* capture form post */
add_action ( 'init', array( $controller, 'form_capture' ) );

/* register shortcode */
add_shortcode ( 'spokane_fair_photos', array( $controller, 'short_code' ) );

/* admin stuff */
if (is_admin() )
{
	/* Add main menu and sub-menus */
	add_action( 'admin_menu', array( $controller, 'admin_menus') );

	/* register settings */
	add_action( 'admin_init', array( $controller, 'register_settings' ) );

	/* admin scripts */
	add_action( 'admin_init', array( $controller, 'admin_scripts' ) );

	/* add the settings page link */
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $controller, 'settings_link' ) );

	add_action( 'wp_ajax_spokane_fair_category_add', function() use ( $controller ) 
	{
		$controller->add_category();
	} );

	add_action( 'wp_ajax_spokane_fair_category_update', function() use ( $controller )
	{
		$controller->update_category();
	} );

	add_action( 'wp_ajax_spokane_fair_category_delete', function() use ( $controller )
	{
		$controller->delete_category();
	} );
}