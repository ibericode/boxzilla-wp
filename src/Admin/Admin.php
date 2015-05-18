<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Licensing\LicenseServiceProvider,
	ScrollTriggeredBoxes\iPlugin,
	ScrollTriggeredBoxes\Box;
use WP_Post;

class Admin {

	/**
	 * @var iPlugin $plugin
	 */
	private $plugin;

	/**
	 * @param iPlugin $plugin
	 */
	public function __construct( iPlugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Initialise the all admin related stuff
	 */
	public function init() {
		$this->register_services();

		// Load the plugin textdomain
		load_plugin_textdomain( 'scroll-triggered-boxes', null, $this->plugin->dir() . '/languages' );

		// action hooks
		$this->add_hooks();
	}

	/**
	 * Registers services into the Service Container
	 */
	protected function register_services() {
		$provider = new AdminServiceProvider();
		$provider->register( $this->plugin );

		// register other services
		$provider = new LicenseServiceProvider();
		$provider->register( $this->plugin );

	}

	/**
	 * Add necessary hooks
	 */
	protected function add_hooks() {
		global $pagenow;

		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );

		add_action( 'save_post_scroll-triggered-box', array( $this, 'save_box_options' ), 20, 2 );
		add_action( 'trashed_post', array( $this, 'flush_rules') );
		add_action( 'untrashed_post', array( $this, 'flush_rules') );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_init' ) );

		if( $pagenow === 'plugins.php' ) {
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_settings_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2 );
		}

		// if a premium add-on is installed, instantiate dependencies
		if( count( $this->plugin['plugins'] ) > 0 ) {
			$this->plugin['license_manager']->add_hooks();
			$this->plugin['update_manager']->add_hooks();
			$this->plugin['api_authenticator']->add_hooks();
		}
	}

	/**
	 * Register stuffs
	 */
	public function register() {

		// register settings
		register_setting( 'stb_settings', 'stb_settings', array( $this, 'sanitize_settings' ) );

		// register scripts
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'scroll-triggered-boxes-admin', $this->plugin->url( '/assets/js/admin-script' . $pre_suffix . '.js' ), array( 'jquery', 'wp-color-picker' ), $this->plugin->version(), true );

		// load stylesheets
		wp_register_style( 'scroll-triggered-boxes-admin', $this->plugin->url( '/assets/css/admin-styles' . $pre_suffix . '.css' ), array(), $this->plugin->version() );
	}

	/**
	 * Renders the STB Menu items
	 */
	public function menu() {

		$menu_items = array(
			array( __( 'Settings', 'scroll-triggered-boxes' ), __( 'Settings', 'scroll-triggered-boxes' ), 'stb-settings', array( $this, 'show_settings_page' ) ),
			array( __( 'Extensions', 'scroll-triggered-boxes' ), '<span style="color: orange">'. __( 'Extensions', 'scroll-triggered-boxes' ) .'</span>', 'stb-extensions', array( $this, 'show_extensions_page' ) )
		);

		$menu_items = apply_filters( 'stb_admin_menu_items', $menu_items );

		foreach( $menu_items as $item ) {
			add_submenu_page( 'edit.php?post_type=scroll-triggered-box', $item[0] . '- Scroll Triggered Boxes', $item[1], 'manage_options', $item[2], $item[3] );
		}
	}

	/**
	 * Shows the settings page
	 */
	public function show_settings_page() {
		$opts = $this->plugin['options'];
		require __DIR__ . '/views/settings.php';
	}

	/**
	 * Shows the extensions page
	 */
	public function show_extensions_page() {
		$extensions = $this->fetch_extensions();
		require __DIR__ . '/views/extensions.php';
	}

	/**
	 * Are we currently editing a box?
	 *
	 * @return bool
	 */
	protected function on_edit_box_page() {
		global $pagenow;

		if( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) {
			return false;
		}

		if( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'scroll-triggered-box' ) {
			return true;
		}

		if( get_post_type() === 'scroll-triggered-box' ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $args
	 *
	 * @return mixed
	 */
	public function tinymce_init($args) {

		// only act on our post type
		if( get_post_type() !== 'scroll-triggered-box' ) {
			return $args;
		}

		$args['setup'] = 'function( editor ) { if(typeof(window.STB_Admin) === \'undefined\') { return; } editor.on("PreInit", window.STB_Admin.Designer.init ); }';
		return $args;
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {

		// load the following only when editing a box
		if( $this->on_edit_box_page() ) {
			wp_enqueue_style( 'wp-color-picker' );

			// load scripts
			wp_enqueue_script( 'scroll-triggered-boxes-admin' );
		}

		if( $this->on_edit_box_page() || ( isset( $_GET['page'] ) && $_GET['page'] === 'stb-settings' ) ) {

			// load stylesheets
			wp_enqueue_style( 'scroll-triggered-boxes-admin' );

			// allow add-ons to easily load their own scripts or stylesheets
			do_action( 'stb_load_admin_assets' );
		}

	}

	/**
	 * Register meta boxes
	 * @param string $post_type
	 * @return bool
	 */
	public function add_meta_boxes( $post_type ) {

		if( $post_type !== 'scroll-triggered-box' ) {
			return false;
		}

		add_meta_box(
			'stb-box-appearance-controls',
			__( 'Box Appearance', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_box_appearance_controls' ),
			'scroll-triggered-box',
			'normal',
			'core'
		);

		add_meta_box(
			'stb-box-options-controls',
			__( 'Box Options', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_box_option_controls' ),
			'scroll-triggered-box',
			'normal',
			'core'
		);

		add_meta_box(
			'stb-support',
			__( 'Need support?', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_support' ),
			'scroll-triggered-box',
			'side'
		);

		add_meta_box(
			'stb-show-appreciation',
			__( 'Show your appreciation!', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_appreciation_options' ),
			'scroll-triggered-box',
			'side'
		);

		return true;
	}

	/**
	 * @param \WP_Post $post
	 * @param $metabox
	 */
	public function metabox_box_appearance_controls( \WP_Post $post, $metabox ) {

		// get box options
		$box = new Box( $post );
		$opts = $box->get_options();

		// include view
		include __DIR__ . '/views/metaboxes/box-appearance-controls.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param $metabox
	 */
	public function metabox_box_option_controls( \WP_Post $post, $metabox ) {

		// get box options
		$box = new Box( $post );
		$opts = $box->get_options();

		// include view
		include __DIR__ . '/views/metaboxes/box-option-controls.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param         $metabox
	 */
	public function metabox_appreciation_options( \WP_Post $post, $metabox ) {
		include __DIR__ . '/views/metaboxes/show-appreciation.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param         $metabox
	 */
	public function metabox_support( \WP_Post $post, $metabox ) {
		include __DIR__ . '/views/metaboxes/need-support.php';
	}


	/**
	* Saves box options and rules
	 *
	 * @param int $box_id
	 * @return bool
	*/
	public function save_box_options( $box_id, $post ) {

		// Only act on our own post type
		if( $post->post_type !== 'scroll-triggered-box' ) {
			return false;
		}

		// is this a revision save?
    	if ( wp_is_post_revision( $box_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		    return false;
    	}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $box_id ) ) {
			return false;
		}

		// make sure options array is set
		if( ! isset( $_POST['stb'] ) || ! is_array( $_POST['stb'] ) ) {
			return false;
		}

		// get new options from $_POST
		$opts = $this->sanitize_box_options( $_POST['stb'] );

		// allow extensions to filter the saved options
		$opts = apply_filters( 'stb_saved_options', $opts, $box_id );

		// save box settings
		update_post_meta( $box_id, 'stb_options', $opts );

		$this->flush_rules( $box_id );
		return true;
	}

	/**
	 * @param array $opts
	 *
	 * @return array
	 */
	public function sanitize_settings( $opts ) {
		return $opts;
	}

	/**
	 * Sanitize the options for this box.
	 *
	 * @param array $opts
	 *
	 * @return array
	 */
	protected function sanitize_box_options( $opts ) {

		// sanitize rules
		$sanitized_rules = array();
		if( isset( $opts['rules'] ) && is_array( $opts['rules'] ) ) {
			foreach( $opts['rules'] as $key => $rule ) {

				// trim all whitespace in value field
				if ( $rule['condition'] !== 'manual' ) {
					$rule['value'] = implode( ',', array_map( 'trim', explode( ',', $rule['value'] ) ) );
				}

				// (re)set value to 0 when condition is everywhere
				if( $rule['condition'] === 'everywhere' ) {
					$rule['value'] = '';
				}

				$sanitized_rules[] = $rule;
			}
		}

		$opts['rules'] = $sanitized_rules;

		// sanitize settings
		if( '' !== $opts['css']['width'] ) {
			$opts['css']['width'] = absint( $opts['css']['width'] );
		}

		if( '' !== $opts['css']['border_width'] ) {
			$opts['css']['border_width'] = absint( $opts['css']['border_width'] );
		}

		$opts['cookie'] = absint( $opts['cookie'] );
		$opts['trigger'] = sanitize_text_field( $opts['trigger'] );
		$opts['trigger_percentage'] = absint( $opts['trigger_percentage'] );
		$opts['trigger_element'] = sanitize_text_field( $opts['trigger_element'] );

		// make sure colors start with `#`
		$color_keys = array( 'color', 'background_color', 'border_color' );
		foreach( $color_keys as $key => $value ) {
			$color = sanitize_text_field( $value );

			// make sure color starts with `#`
			if( '' !== $color && $color[0] !== '#' ) {
				$color = '#' . $color;
			}
			$opts['css'][$key] = $color;
		}

		return $opts;
	}

	/**
	 * Add the settings link to the Plugins overview
	 * @param array $links
	 * @param string $slug
	 * @return array
	 */
	public function add_plugin_settings_link( $links, $slug ) {
		if( $slug !== $this->plugin->slug() ) {
			return $links;
		}

		$settings_link = '<a href="' . admin_url( 'edit.php?post_type=scroll-triggered-box' ) . '">'. __( 'Boxes' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Adds meta links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array $links
	 * @param string $slug
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $slug ) {
		if( $slug !== $this->plugin->slug() ) {
			return $links;
		}

		$links[] = '<a href="https://scrolltriggeredboxes.com/kb#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page">Documentation</a>';
		return $links;
	}

	/**
	* Flush all box rules
	*
	* Loops through all published boxes and fills the rules option
	 *
	 * @param int $post_id
	*/
	public function flush_rules( $post_id ) {

		// only act on our own post type
		$post = get_post( $post_id );
		if( $post instanceof WP_Post && $post->post_type !== 'scroll-triggered-box' ) {
			return;
		}

		// get all published boxes
		$boxes = get_posts(
			array(
				'post_type' => 'scroll-triggered-box',
				'post_status' => 'publish',
				'numberposts' => -1
			)
		);

		// setup empty array of rules
		$rules = array();

		// fill rules array
		if( is_array( $boxes ) ) {

			foreach( $boxes as $box ) {
				// get box meta data
				$box_meta = get_post_meta( $box->ID, 'stb_options', true );

				// add box rules to all rules
				$rules[ $box->ID ] = $box_meta['rules'];

			}

		}

		update_option( 'stb_rules', $rules );
	}

	/**
	 * Fetches a list of available add-on plugins
	 *
	 * @return array
	 */
	protected function fetch_extensions() {

		$extensions = get_transient( 'stb_remote_extensions' );
		if( $extensions ) {
			return $extensions;
		}

		$request = wp_remote_get('https://scrolltriggeredboxes.com/api/v1/plugins');

		if( is_wp_error( $request ) ) {
			return array();
		}

		$response = wp_remote_retrieve_body( $request );
		$response = json_decode( $response );

		if( is_array( $response->data ) ) {
			set_transient( 'stb_remote_extensions', $response->data, HOUR_IN_SECONDS );
			return $response->data;
		}

		return array();
	}

}
