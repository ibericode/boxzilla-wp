<?php

namespace ScrollTriggeredBoxes;

use ScrollTriggeredBoxes\Admin\Admin;
use ScrollTriggeredBoxes\Admin\License;
use ScrollTriggeredBoxes\Admin\LicenseAPI;
use Pimple\Container;

final class Plugin extends Container {

	/**
	 * @const Current plugin version
	 */
	const VERSION = '2.0';

	/**
	 * @const Base plugin file
	 */
	const FILE = STB_PLUGIN_FILE;

	/**
	 * @const Base plugin directory
	 */
	const DIR = STB_PLUGIN_DIR;

	/**
	 * @var Plugin The One True Plugin Instance
	 */
	public static $instance;


	/**
	 * @return Plugin
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Initialise the plugin
	 *
	 * @return Plugin
	 */
	public static function bootstrap() {

		if( is_null( self::$instance ) ) {
			self::$instance = new Plugin();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		// register services early
		$this->register_services();

		// load rest of classes on a later hook
		add_action( 'plugins_loaded', array( $this, 'load' ), 20 );
	}

	/**
	 * Register services in the Service Container
	 */
	protected function register_services() {
		$this['options'] = function( $app ) {
			$defaults = array(
				'test_mode' => 0
			);

			$options = (array) get_option( 'stb_settings', $defaults );
			$options = array_merge( $defaults, $options );
			return $options;
		};

		$this['plugins'] = function( $app ) {
			$plugins = (array) apply_filters( 'stb_extensions', array() );
			return new Collection( $plugins );
		};
	}

	/**
	 * Start loading classes on `plugins_loaded`, priority 20.
	 */
	public function load() {
		add_action( 'init', array( $this, 'register_post_type' ), 11 );

		if( ! is_admin() ) {

			// FRONTEND
			$this['box_loader'] = new BoxLoader( $this );

		} elseif( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

			// BACKEND (NOT AJAX)
			$this['admin'] = new Admin( $this );
		}
	}

	public function register_post_type() {
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
			'menu_position' => '108.1337133',
			'menu_icon' => plugins_url( '/assets/img/menu-icon.png', self::FILE )
		);

		register_post_type( 'scroll-triggered-box', $args );
	}

}

/**
 * @return Plugin
 */
function plugin() {
	return Plugin::instance();
}