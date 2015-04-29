<?php

namespace ScrollTriggeredBoxes;

use ScrollTriggeredBoxes\DI\Container;

final class Plugin extends Container implements iPlugin {

	/**
	 * @const Current plugin version
	 */
	const VERSION = '2.0';

	/**
	 * @var Plugin The One True Plugin Instance
	 */
	public static $instance;

	/**
	 * @var string The current version of the plugin
	 */
	protected $version = '1.0';

	/**
	 * @var string
	 */
	protected $file = __FILE__;

	/**
	 * @var string
	 */
	protected $dir = __DIR__;

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $slug = '';

	/**
	 * @var int
	 */
	protected $id = 0;


	/**
	 * @return Plugin
	 */
	public static function instance() {
		return self::$instance;
	}


	/**
	 * Constructor
	 *
	 * @param $id
	 * @param $name
	 * @param $version
	 * @param $file
	 * @param $dir
	 */
	public function __construct( $id, $name, $version, $file, $dir ) {
		$this->id = $id;
		$this->name = $name;
		$this->version = $version;
		$this->file = $file;
		$this->dir = $dir;
		$this->slug = plugin_basename( $file );

		parent::__construct();

		// register services early since some add-ons need 'm
		$this->register_services();

		// load rest of classes on a later hook
		add_action( 'plugins_loaded', array( $this, 'load' ), 20 );

		// store instance
		self::$instance = $this;
	}

	/**
	 * Register services in the Service Container
	 */
	protected function register_services() {
		$provider = new PluginServiceProvider();
		$provider->register( $this );
	}

	/**
	 * Start loading classes on `plugins_loaded`, priority 20.
	 */
	public function load() {
		add_action( 'init', array( $this, 'register_post_type' ), 11 );

		if( ! is_admin() ) {
			$this['box_loader'];
		} elseif( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$this['admin'];
		}
	}

	/**
	 * Register the box post type
	 */
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
			'menu_icon' => $this->url( '/assets/img/menu-icon.png' )
		);

		register_post_type( 'scroll-triggered-box', $args );
	}

	/**
	 * @return int
	 */
	public function id() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function file() {
		return $this->file;
	}

	/**
	 * @return string
	 */
	public function dir() {
		return $this->dir;
	}

	/**
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function url( $path = '' ) {
		return plugins_url( $path, $this->file() );
	}
}

/**
 * @return Plugin
 */
function plugin() {
	return Plugin::instance();
}