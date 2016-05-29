<?php

/**
 * Plugin Name: Photo Submission Plugin for the Spokane Interstate Fair
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

$controller = new \SpokaneFair\Controller;

/* activate */
register_activation_hook( __FILE__, array( $controller, 'activate' ) );

/* enqueue js and css */
add_action( 'init', array( $controller, 'init' ) );

/* capture form post */
add_action ( 'init', array( $controller, 'form_capture' ) );

/* register shortcode */
add_shortcode ( 'spokane_fair_photo_contest', array( $controller, 'short_code' ) );

/* admin stuff */
if (is_admin() )
{
	/* Add main menu and sub-menus */
	add_action( 'admin_menu', array( $nitro_k9_controller, 'admin_menus') );

	/* register settings */
	add_action( 'admin_init', array( $nitro_k9_controller, 'register_settings' ) );

	/* admin scripts */
	add_action( 'admin_init', array( $nitro_k9_controller, 'admin_scripts' ) );
}