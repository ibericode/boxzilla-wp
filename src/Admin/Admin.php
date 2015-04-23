<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Plugin,
	ScrollTriggeredBoxes\Box;

class Admin {

	/**
	 * @var string
	 */
	private $plugin_file = '';

	/**
	 * @var Plugin $plugin
	 */
	private $plugin;

	/**
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {

		// store path to plugin file in property
		$this->plugin_file = plugin_basename( Plugin::FILE );

		// store reference to plugin file
		$this->plugin = $plugin;

		$this->register_services();

		// Load the plugin textdomain
		load_plugin_textdomain( 'scroll-triggered-boxes', null, dirname( plugin_basename( Plugin::FILE ) ) . '/languages' );

		// action hooks
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );

		$this->api_authenticator = new APIAuthenticator( $plugin['api_url'], $plugin['license'] );
		$this->license_manager = new LicenseManager( $plugin['plugins'], $plugin['notices'], $plugin['license'] );
		$this->update_manager = new UpdateManager( $plugin['plugins'], $plugin['notices'], $plugin['license'] );
	}

	protected function register_services() {
		$this->plugin['notices'] = function( $app ) {
			return new Notices();
		};

		$this->plugin['api_url'] = function( $app ) {
			return 'http://local.stb.com/api';
		};

		$this->plugin['license'] = function( $app ) {
			return new License( 'stb_license' );
		};

		$this->plugin['api_connector'] = function( $app ) {
			return new APIConnector( $app['api_url'], $app['notices'] );
		};
	}

	/**
	 * Initialises the admin section
	 */
	public function init() {

		global $pagenow;
		add_action( 'save_post', array( $this, 'save_box_options' ), 20 );
		add_action( 'trashed_post', array( $this, 'flush_rules') );
		add_action( 'untrashed_post', array( $this, 'flush_rules') );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );

		if( $this->editing_box() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_init' ) );
		} elseif( $pagenow === 'plugins.php' ) {
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_settings_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2 );
		}
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'stb_settings', 'stb_settings', array( $this, 'sanitize_settings' ) );
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
		require dirname( Plugin::FILE ) . '/views/settings.php';
	}

	/**
	 * Shows the extensions page
	 */
	public function show_extensions_page() {
		require dirname( Plugin::FILE ) . '/views/extensions.php';
	}

	/**
	 * Are we currently editing a box?
	 *
	 * @return bool
	 */
	private function editing_box() {
		global $pagenow, $post;

		if( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) {
			return false;
		}

		if( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'scroll-triggered-box' ) {
			return true;
		}

		if( $post && $post instanceof \WP_Post && $post->post_type === 'scroll-triggered-box' ) {
			return true;
		}

		if( isset( $_GET['post'] ) && ( $post = get_post( $_GET['post'] ) ) && $post->post_type === 'scroll-triggered-box' ) {
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
		$args['setup'] = 'function( editor ) { if(typeof(window.STB_Admin) === \'undefined\') { return; } editor.on("PreInit", window.STB_Admin.Designer.init ); }';
		return $args;
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {

		if( ! $this->editing_box()
			&& ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'stb-settings' ) ) {
			return false;
		}

		// only load on "edit box" pages
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_url = plugins_url( '/assets', Plugin::FILE );

		// load the following only when editing a box
		if( $this->editing_box() ) {
			wp_enqueue_style( 'wp-color-picker' );

			// load scripts
			wp_enqueue_script( 'scroll-triggered-boxes-admin', $assets_url .' /js/admin-script' . $pre_suffix . '.js', array( 'jquery', 'wp-color-picker' ), Plugin::VERSION, true );
		}

		// load stylesheets
		wp_enqueue_style( 'scroll-triggered-boxes-admin', $assets_url .' /css/admin-styles' . $pre_suffix . '.css', array(), Plugin::VERSION );


		// allow add-ons to easily load their own scripts or stylesheets
		do_action( 'stb_load_admin_assets' );
	}

	/**
	 * Register meta boxes
	 */
	public function add_meta_boxes() {
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
			'stb-available-add-ons',
			__( 'Available add-ons.', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_available_add_ons' ),
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
		include dirname( Plugin::FILE ) . '/views/metaboxes/box-appearance-controls.php';
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
		include dirname( Plugin::FILE ) . '/views/metaboxes/box-option-controls.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param         $metabox
	 */
	public function metabox_appreciation_options( \WP_Post $post, $metabox ) {
		include dirname( Plugin::FILE ) . '/views/metaboxes/show-appreciation.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param         $metabox
	 */
	public function metabox_available_add_ons( \WP_Post $post, $metabox ) {
		include dirname( Plugin::FILE ) . '/views/metaboxes/available-add-ons.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param         $metabox
	 */
	public function metabox_support( \WP_Post $post, $metabox ) {
		include dirname( Plugin::FILE ) . '/views/metaboxes/need-support.php';
	}


	/**
	* Saves box options and rules
	 *
	 * @param int $box_id
	 * @return bool
	*/
	public function save_box_options( $box_id ) {

		// Verify that the nonce is set and valid.
		if ( ! isset( $_POST['stb_options_nonce'] ) || ! wp_verify_nonce( $_POST['stb_options_nonce'], 'stb_options' ) ) {
			return false;
		}

		// is this a revision save?
    	if ( wp_is_post_revision( $box_id ) ) {
		    return false;
    	}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
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

		$this->flush_rules();
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
	 * @return array
	 */
	public function add_plugin_settings_link( $links, $file ) {
		if( $file !== $this->plugin_file ) {
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
	 * @param string $file
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $file ) {
		if( $file !== $this->plugin_file ) {
			return $links;
		}

		$links[] = '<a href="https://wordpress.org/plugins/scroll-triggered-boxes/faq/">FAQ</a>';
		return $links;
	}

	/**
	* Flush all box rules
	*
	* Loops through all published boxes and fills the rules option
	*/
	public function flush_rules() {

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

}
