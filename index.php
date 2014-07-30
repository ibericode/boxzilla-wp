<?php
/*
Plugin Name: Scroll Triggered Boxes
Version: 1.2.2
Plugin URI: http://dannyvankooten.com/
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Highly conversing, not so annoying!
Author: Danny van Kooten
Author URI: http://dannyvankooten.com/
Text Domain: scroll-triggered-boxes
Domain Path: /languages/
License: GPL v3

Scroll Triggered Boxes Plugin
Copyright (C) 2013-2014, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class STB
{
	const VERSION = '1.2.2';
	const FILE = __FILE__;

	public static $dir = '';
	public static $url = '';

	public static function bootstrap() {

		self::$dir = dirname( __FILE__ );
		self::$url = plugins_url( '/' , __FILE__ );

		add_action( 'init', array( __CLASS__, 'register_post_type' ) );

		if( ! is_admin() ) {

			// FRONTEND
			require_once self::$dir . '/includes/class-public.php';
			new STB_Public();

		} elseif( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			// BACKEND (NOT AJAX)
			require_once self::$dir . '/includes/class-admin.php';
			new STB_Admin();

		}
	}

	/**
	 * Get the box options for box with given ID.
	 *
	 * @param int $id
	 *
	 * @return array Array of box options
	 */
	public static function get_box_options( $id ) {

		static $defaults = array(
			'css' => array(
				'background_color' => '',
				'color' => '',
				'width' => '',
				'border_color' => '',
				'border_width' => '',
				'position' => 'bottom-right'
			),
			'rules' => array(
				array('condition' => '', 'value' => '')
			),
			'cookie' => 0,
			'trigger' => 'percentage',
			'trigger_percentage' => 65,
			'trigger_element' => '',
			'animation' => 'fade',
			'test_mode' => 0,
			'auto_hide' => 0
		);

		$opts = get_post_meta( $id, 'stb_options', true );

		return wp_parse_args( $opts, $defaults );

	}

	/**
	 * Registers the Scroll Triggered Boxes post type
	 */
	public static function register_post_type()
	{
		$args = array(
			'public' => false,
			'labels'  =>  array(
				'name'               => 'Scroll Triggered Boxes',
				'singular_name'      => 'Scroll Triggered Box',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Box',
				'edit_item'          => 'Edit Box',
				'new_item'           => 'New Box',
				'all_items'          => 'All Boxes',
				'view_item'          => 'View Box',
				'search_items'       => 'Search Boxes',
				'not_found'          => 'No Boxes found',
				'not_found_in_trash' => 'No Boxes found in Trash',
				'parent_item_colon'  => '',
				'menu_name'          => 'Scroll Triggered Boxes'
			),
			'show_ui' => true,
			'menu_position' => 108,
			'menu_icon' => STB::$url . '/assets/img/menu-icon.png'
		);

		register_post_type( 'scroll-triggered-box', $args );
	}
}

add_action( 'plugins_loaded', array( 'STB', 'bootstrap' ) );