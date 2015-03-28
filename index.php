<?php
/*
Plugin Name: Scroll Triggered Boxes
Version: 2.0
Plugin URI: https://dannyvankooten.com/
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Highly conversing, not so annoying!
Author: Danny van Kooten
Author URI: https://dannyvankooten.com/
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

final class STB {

	/**
	 * @const string
	 */
	const VERSION = '2.0';

	/**
	 * @const string
	 */
	const FILE = __FILE__;

	/**
	 * @var STB
	 */
	public static $instance;

	/**
	 * @var string
	 */
	public $url = '';

	/**
	 * @var STB_Public
	 */
	protected $public;

	/**
	 * @var STB_Admin
	 */
	protected $admin;

	/**
	 * @return STB
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Initialise the plugin
	 *
	 * @return STB
	 */
	public static function init() {

		if( is_null( self::$instance ) ) {
			self::$instance = new STB();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {

		// store the URL to the plugin directory
		$this->url = plugins_url( '/' , __FILE__ );

		add_action( 'init', array( $this, 'register_box_post_type' ), 11 );

		if( ! is_admin() ) {

			// FRONTEND
			require_once dirname( self::FILE ) . '/includes/class-public.php';
			$this->public = new STB_Public( $this );

		} elseif( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			// BACKEND (NOT AJAX)
			require_once dirname( self::FILE ) . '/includes/class-admin.php';
			$this->admin = new STB_Admin( $this );

		}
	}

	/**
	 * Initializes the plugin
	 */
	public function register_box_post_type() {

		// Register custom post type
		$args = array(
			'public' => false,
			'labels'  =>  array(
				'name'               => __( 'Scroll Triggered Boxes', 'scroll-triggered-boxes' ),
				'singular_name'      => __( 'Scroll Triggered Box', 'scroll-triggered-boxes' ),
				'add_new'            => __( 'Add New', 'scroll-triggered-boxes' ),
				'add_new_item'       => __( 'Add New Box', 'scroll-triggered-boxes' ),
				'edit_item'          => __( 'Edit Box', 'scroll-triggered-boxes' ),
				'new_item'           => __( 'New Box', 'scroll-triggered-boxes' ),
				'all_items'          => __( 'All Boxes', 'scroll-triggered-boxes' ),
				'view_item'          => __( 'View Box', 'scroll-triggered-boxes' ),
				'search_items'       => __( 'Search Boxes', 'scroll-triggered-boxes' ),
				'not_found'          => __( 'No Boxes found', 'scroll-triggered-boxes' ),
				'not_found_in_trash' => __( 'No Boxes found in Trash', 'scroll-triggered-boxes' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Scroll Triggered Boxes', 'scroll-triggered-boxes' )
			),
			'show_ui' => true,
			'menu_position' => 108,
			'menu_icon' => $this->url . '/assets/img/menu-icon.png'
		);

		register_post_type( 'scroll-triggered-box', $args );
	}

	/**
	 * Get the box options for box with given ID.
	 *
	 * @param int $id
	 *
	 * @return array Array of box options
	 */
	public function get_box_options( $id ) {

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
			'auto_hide' => 0,
			'hide_on_screen_size' => ''
		);

		$opts = get_post_meta( $id, 'stb_options', true );

		// merge with array of defaults
		$opts = array_merge( $defaults, $opts );

		// allow others to filter the final array of options
		return apply_filters( 'stb_box_options', $opts, $id );
	}

	/**
	 * @return STB_Public
	 */
	public function get_public() {
		return $this->public;
	}

	/**
	 * @return STB_Admin
	 */
	public function get_admin() {
		return $this->admin;
	}

}

// store the one true instance in a global var
$GLOBALS['ScrollTriggeredBoxes'] = STB::init();