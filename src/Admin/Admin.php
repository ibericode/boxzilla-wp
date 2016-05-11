<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Licensing\LicenseServiceProvider,
	ScrollTriggeredBoxes\iPlugin,
	ScrollTriggeredBoxes\Box;
use WP_Post;
use WP_Screen;

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

		add_action( 'admin_init', array( $this, 'lazy_add_hooks' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'nudge_to_boxzilla' ) );

		add_action( 'save_post_scroll-triggered-box', array( $this, 'save_box_options' ), 20, 2 );
		add_action( 'trashed_post', array( $this, 'flush_rules' ) );
		add_action( 'untrashed_post', array( $this, 'flush_rules' ) );

		// if a premium add-on is installed, instantiate dependencies
		if ( count( $this->plugin['plugins'] ) > 0 ) {
			$this->plugin['license_manager']->add_hooks();
			$this->plugin['update_manager']->add_hooks();
			$this->plugin['api_authenticator']->add_hooks();
		}
	}

	public function lazy_add_hooks() {
		global $pagenow;

		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_init' ) );
		add_filter( 'manage_edit-scroll-triggered-box_columns', array( $this, 'post_type_column_titles' ) );
		add_action( 'manage_scroll-triggered-box_posts_custom_column', array(
			$this,
			'post_type_column_content'
		), 10, 2 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );


		if ( $pagenow === 'plugins.php' ) {
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_settings_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
		}
	}

	/**
	 * @param $post_id
	 */
	public function post_type_column_box_id_content( $post_id ) {
		echo $post_id;
	}

	/**
	 * @param $column
	 * @param $post_id
	 */
	public function post_type_column_content( $column, $post_id ) {
		if ( method_exists( $this, 'post_type_column_' . $column . '_content' ) ) {
			call_user_func( array( $this, 'post_type_column_' . $column . '_content' ), $post_id );
		}
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function post_type_column_titles( $columns ) {
		$columns = self::array_insert( $columns, array(
			'box_id' => __( 'Box ID', 'scroll-triggered-box' )
		), 1 );

		$columns['title'] = __( 'Box Title', 'scroll-triggered-box' );

		return $columns;
	}

	/**
	 * Register stuffs
	 */
	public function register() {

		// register settings
		register_setting( 'stb_settings', 'stb_settings', array( $this, 'sanitize_settings' ) );

		// register scripts
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'scroll-triggered-boxes-admin', $this->plugin->url( '/assets/js/admin-script' . $pre_suffix . '.js' ), array(
			'jquery',
			'wp-util',
			'wp-color-picker',
			'suggest'
		), $this->plugin->version(), true );

		// load stylesheets
		wp_register_style( 'scroll-triggered-boxes-admin', $this->plugin->url( '/assets/css/admin-styles' . $pre_suffix . '.css' ), array(), $this->plugin->version() );
	}

	/**
	 * Renders the STB Menu items
	 */
	public function menu() {

		$menu_items = array(
			array(
				__( 'Settings', 'scroll-triggered-boxes' ),
				__( 'Settings', 'scroll-triggered-boxes' ),
				'stb-settings',
				array( $this, 'show_settings_page' )
			),
			array(
				__( 'Extensions', 'scroll-triggered-boxes' ),
				'<span style="color: orange">' . __( 'Extensions', 'scroll-triggered-boxes' ) . '</span>',
				'stb-extensions',
				array( $this, 'show_extensions_page' )
			)
		);

		$menu_items = apply_filters( 'stb_admin_menu_items', $menu_items );

		foreach ( $menu_items as $item ) {
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

		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) {
			return false;
		}

		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'scroll-triggered-box' ) {
			return true;
		}

		if ( get_post_type() === 'scroll-triggered-box' ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $args
	 *
	 * @return mixed
	 */
	public function tinymce_init( $args ) {

		// only act on our post type
		if ( get_post_type() !== 'scroll-triggered-box' ) {
			return $args;
		}

		$args['setup'] = 'function( editor ) { if(typeof(window.STB_Admin) === \'undefined\') { return; } editor.on("PreInit", window.STB_Admin.Designer.init ); }';

		return $args;
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {

		$screen = get_current_screen();

		if ( ! $screen instanceof WP_Screen ) {
			return false;
		}

		if ( $screen->base === 'edit' && $screen->post_type === 'scroll-triggered-box' ) {
			// load stylesheets
			wp_enqueue_style( 'scroll-triggered-boxes-admin' );
		}

		if ( $screen->base === 'post' && $screen->post_type === 'scroll-triggered-box' ) {
			// color picker
			wp_enqueue_style( 'wp-color-picker' );

			// load scripts
			wp_enqueue_script( 'scroll-triggered-boxes-admin' );

			wp_localize_script( 'scroll-triggered-boxes-admin' ,'stb_i18n', array(
					'enterCommaSeparatedValues' => __( 'Enter a comma-separated list of values.', 'scroll-triggered-boxes' ),
					'enterCommaSeparatedPosts' => __( "Enter a comma-separated list of post slugs or post ID's..", 'scroll-triggered-boxes' ),
					'enterCommaSeparatedPages' => __( "Enter a comma-separated list of page slugs or page ID's..", 'scroll-triggered-boxes' ),
					'enterCommaSeparatedPostTypes' => __( "Enter a comma-separated list of post types..", 'scroll-triggered-boxes' ),
					'enterCommaSeparatedRelativeUrls' => __( "Enter a comma-separated list of relative URL's, eg /contact/", 'scroll-triggered-boxes' ),
				)
			);

			// load stylesheets
			wp_enqueue_style( 'scroll-triggered-boxes-admin' );

			// allow add-ons to easily load their own scripts or stylesheets
			do_action( 'stb_load_admin_assets' );
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'stb-settings' ) {
			// load stylesheets
			wp_enqueue_style( 'scroll-triggered-boxes-admin' );
		}

	}

	/**
	 * Register meta boxes
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function add_meta_boxes( $post_type ) {

		if ( $post_type !== 'scroll-triggered-box' ) {
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
			__( 'Looking for help?', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_support' ),
			'scroll-triggered-box',
			'side'
		);

		add_meta_box(
			'stb-email-optin',
			__( 'Subscribe to our newsletter', 'scroll-triggered-boxes' ),
			array( $this, 'metabox_email_optin' ),
			'scroll-triggered-box',
			'side'
		);

		return true;
	}

	/**
	 * @param \WP_Post $post
	 * @param          $metabox
	 */
	public function metabox_box_appearance_controls( \WP_Post $post, $metabox ) {

		// get box options
		$box  = new Box( $post );
		$opts = $box->get_options();

		// include view
		include __DIR__ . '/views/metaboxes/box-appearance-controls.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param          $metabox
	 */
	public function metabox_box_option_controls( \WP_Post $post, $metabox ) {

		// get box options
		$box  = new Box( $post );
		$opts = $box->get_options();
		$global_opts = $this->plugin['options'];

		if ( empty( $opts['rules'] ) ) {
			$opts['rules'][] = array( 'condition' => '', 'value' => '' );
		}

		// include view
		include __DIR__ . '/views/metaboxes/box-option-controls.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param          $metabox
	 */
	public function metabox_email_optin( \WP_Post $post, $metabox ) {
		include __DIR__ . '/views/metaboxes/email-optin.php';
	}

	/**
	 * @param \WP_Post $post
	 * @param          $metabox
	 */
	public function metabox_support( \WP_Post $post, $metabox ) {
		include __DIR__ . '/views/metaboxes/need-help.php';
	}


	/**
	 * Saves box options and rules
	 *
	 * @param int $box_id
	 *
	 * @return bool
	 */
	public function save_box_options( $box_id, $post ) {

		// Only act on our own post type
		if ( $post->post_type !== 'scroll-triggered-box' ) {
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
		if ( ! isset( $_POST['stb'] ) || ! is_array( $_POST['stb'] ) ) {
			return false;
		}

		// get new options from $_POST
		$opts = $this->sanitize_box_options( $_POST['stb'] );

		// allow extensions to filter the saved options
		$opts = apply_filters( 'stb_saved_options', $opts, $box_id );

		// save individual box settings
		update_post_meta( $box_id, 'stb_options', $opts );

		// update global settings if given
		if( ! empty( $_POST['stb_global_settings'] ) ) {
			$global_settings = get_option( 'stb_settings', array() );
			if( ! is_array( $global_settings ) ) { $global_settings = array(); }
			$global_settings = array_merge( $global_settings, $_POST['stb_global_settings'] );
			update_option( 'stb_settings', $global_settings );
		}

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
	 * @param string $url_string
	 *
	 * @return string
	 */
	public function sanitize_url( $url_string ) {

		// if empty, just return a slash
		if( empty( $url_string ) ) {
			return '/';
		}

		// if string looks like an absolute URL, extract just the path
		if( preg_match( '/^((https|http)?\:\/\/)?(\w+\.)?\w+\.\w+\.*/i', $url_string ) ) {

			// make sure URL has scheme prepended, to make parse_url() understand..
			$url_string = 'https://' . str_replace( array( 'http://', 'https://' ), '', $url_string );

			// get just the path
			$url_string = parse_url( $url_string, PHP_URL_PATH );
		}

		// leading slash it
		return '/' . ltrim( $url_string, '/' );
	}

	/**
	 * @param array $rule
	 * @return array The sanitized rule array
	 */
	public function sanitize_box_rule( $rule) {
		$rule['value'] = trim( $rule['value'] );

		// don't touch empty values or manual rules
		if ( $rule['condition'] !== 'manual' ) {

			// convert to array
			$rule['value'] = explode( ',', trim( $rule['value'], ',' ) );

			// trim all whitespace in value field
			$rule['value'] = array_map( 'trim', $rule['value'] );

			// Make sure "is_url" values have a leading slash
			if ( $rule['condition'] === 'is_url' ) {
				$rule['value'] = array_map( array( $this, 'sanitize_url' ), $rule['value'] );
			}

			// (re)set value to 0 when condition is everywhere
			if ( $rule['condition'] === 'everywhere' ) {
				$rule['value'] = '';
			}

			// convert back to string before saving
			if ( is_array( $rule['value'] ) ) {
				$rule['value'] = join( ',', $rule['value'] );
			}
		}

		return $rule;
	}

	/**
	 * @param array $css
	 * @return array
	 */
	public function sanitize_box_css( $css ) {

		// sanitize settings
		if ( '' !== $css['width'] ) {
			$css['width'] = absint( $css['width'] );
		}

		if ( '' !== $css['border_width'] ) {
			$css['border_width'] = absint( $css['border_width'] );
		}

		// make sure colors start with `#`
		$color_keys = array( 'color', 'background_color', 'border_color' );
		foreach ( $color_keys as $key ) {
			$value = $css[ $key ];
			$color = sanitize_text_field( $value );

			// make sure color starts with `#`
			if ( '' !== $color && $color[0] !== '#' ) {
				$color = '#' . $color;
			}
			$css[ $key ] = $color;
		}

		return $css;
	}

	/**
	 * Sanitize the options for this box.
	 *
	 * @param array $opts
	 *
	 * @return array
	 */
	protected function sanitize_box_options( $opts ) {

		static $defaults = array(
			'rules' => array(),
			'css' => array()
		);
		$opts = array_merge( $defaults, $opts );

		$opts['rules'] = array_map( array( $this, 'sanitize_box_rule' ), $opts['rules'] );
		$opts['css'] = $this->sanitize_box_css( $opts['css'] );
		$opts['cookie']             = absint( $opts['cookie'] );
		$opts['trigger']            = sanitize_text_field( $opts['trigger'] );
		$opts['trigger_percentage'] = absint( $opts['trigger_percentage'] );
		$opts['trigger_element']    = sanitize_text_field( $opts['trigger_element'] );

		return $opts;
	}

	/**
	 * Add the settings link to the Plugins overview
	 *
	 * @param array  $links
	 * @param string $slug
	 *
	 * @return array
	 */
	public function add_plugin_settings_link( $links, $slug ) {
		if ( $slug !== $this->plugin->slug() ) {
			return $links;
		}

		$settings_link = '<a href="' . admin_url( 'edit.php?post_type=scroll-triggered-box' ) . '">' . __( 'Boxes' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds meta links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array  $links
	 * @param string $slug
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $slug ) {
		if ( $slug !== $this->plugin->slug() ) {
			return $links;
		}

		$links[] = '<a href="https://scrolltriggeredboxes.com/kb#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page">Documentation</a>';
		$links[] = '<a href="https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page">Add-ons</a>';

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
		if ( $post instanceof WP_Post && $post->post_type !== 'scroll-triggered-box' ) {
			return;
		}

		// get all published boxes
		$boxes = get_posts(
			array(
				'post_type'   => 'scroll-triggered-box',
				'post_status' => 'publish',
				'numberposts' => - 1
			)
		);

		// setup empty array of rules
		$rules = array();

		// fill rules array
		if ( is_array( $boxes ) ) {

			foreach ( $boxes as $box ) {
				// get box meta data
				$box_meta = get_post_meta( $box->ID, 'stb_options', true );

				// add box rules to all rules
				$rules[ $box->ID ]                = $box_meta['rules'];
				$rules[ $box->ID ]['comparision'] = $box_meta['rules_comparision'];

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
		if ( $extensions ) {
			return $extensions;
		}

		$request = wp_remote_get( 'https://scrolltriggeredboxes.com/api/v1/plugins' );

		if ( is_wp_error( $request ) ) {
			return array();
		}

		$response = wp_remote_retrieve_body( $request );
		$response = json_decode( $response );

		if ( is_array( $response->data ) ) {
			set_transient( 'stb_remote_extensions', $response->data, HOUR_IN_SECONDS );

			return $response->data;
		}

		return array();
	}

	/**
	 * @param $arr
	 * @param $insert
	 * @param $position
	 *
	 * @return array
	 */
	public static function array_insert( $arr, $insert, $position ) {
		$i   = 0;
		$ret = array();
		foreach ( $arr as $key => $value ) {
			if ( $i == $position ) {
				foreach ( $insert as $ikey => $ivalue ) {
					$ret[ $ikey ] = $ivalue;
				}
			}
			$ret[ $key ] = $value;
			$i ++;
		}

		return $ret;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function admin_footer_text( $text ) {
		$screen = get_current_screen();

		if ( ! $screen instanceof WP_Screen ) {
			return $text;
		}

		$on_edit_page = $screen->parent_base === 'edit' && $screen->post_type === 'scroll-triggered-box';
		if ( $on_edit_page ) {
			return sprintf( 'If you enjoy using <strong>Scroll Triggered Boxes</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/scroll-triggered-boxes?rate=5#postform' );
		}

		return $text;
	}

	/**
	 *
	 */
	public function nudge_to_boxzilla() {

		global $pagenow;

		if( get_post_type() != 'scroll-triggered-box' && ! in_array( $pagenow, array( 'plugins.php', 'update-core.php' ) ) ) {
			return;
		}

		if( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		include __DIR__ . '/views/boxzilla-nudge.php';
	}

}
