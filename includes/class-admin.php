<?php
if( ! defined( 'STB::VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class STB_Admin {

	/**
	 * @var string
	 */
	private $plugin_file = '';

	public function __construct() {

		$this->plugin_file = plugin_basename( STB::FILE );

		// action hooks
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'save_post', array( $this, 'save_meta_options' ), 20 );
		add_action( 'trashed_post', array( $this, 'flush_rules') );
		add_action( 'untrashed_post', array( $this, 'flush_rules') );
	}

	/**
	 * Initialises the admin section
	 */
	public function init() {

		// Load the plugin textdomain
		load_plugin_textdomain( 'scroll-triggered-boxes', false, STB::$dir . '/languages/' );

		global $pagenow;

		if( $this->editing_box() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_init' ) );
		} elseif( $pagenow === 'plugins.php' ) {
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_settings_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2 );
		}

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

		if( $post && $post instanceof WP_Post && $post->post_type === 'scroll-triggered-box' ) {
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
		$args['setup'] = 'function( editor ) { if(typeof STB === \'undefined\') { return; } editor.on("PreInit", STB.onTinyMceInit ); }';
		return $args;
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {

		// only load on "edit box" pages
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// load stylesheets
		wp_enqueue_style( 'scroll-triggered-boxes', STB::$url . 'assets/css/admin-styles' . $pre_suffix . '.css', array( 'wp-color-picker' ), STB::VERSION );

		// load scripts
		wp_enqueue_script( 'scroll-triggered-boxes', STB::$url . 'assets/js/admin-script' . $pre_suffix . '.js', array( 'jquery', 'wp-color-picker' ), STB::VERSION, true );
	}

	/**
	 * Register meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'stb-options',
			__( 'Box Options', 'scroll-triggered-boxes' ),
			array( $this, 'show_meta_options' ),
			'scroll-triggered-box',
			'normal',
			'core'
		);

		add_meta_box(
			'stb-dvk-info-support',
			__( 'Need support?', 'scroll-triggered-boxes' ),
			array( $this, 'show_dvk_info_support' ),
			'scroll-triggered-box',
			'side'
		);

		add_meta_box(
			'stb-dvk-info-donate',
			__( 'Donate $10, $20 or $50', 'scroll-triggered-boxes' ),
			array( $this, 'show_dvk_info_donate' ),
			'scroll-triggered-box',
			'side'
		);

		add_meta_box(
			'stb-dvk-info-links',
			__( 'About the developer', 'scroll-triggered-boxes' ),
			array( $this, 'show_dvk_info_links' ),
			'scroll-triggered-box',
			'side'
		);
	}

	public function show_meta_options( $post, $metabox ) {
		$opts = STB::get_box_options($post->ID);
		include STB::$dir . '/includes/views/metabox-options.php';
	}

	public function show_dvk_info_donate( $post, $metabox ) {
		include STB::$dir . '/includes/views/metabox-dvk-donate.php';
	}

	public function show_dvk_info_support( $post, $metabox ) {
		include STB::$dir . '/includes/views/metabox-dvk-support.php';
	}

	public function show_dvk_info_links( $post, $metabox ) {
		include STB::$dir . '/includes/views/metabox-dvk-links.php';
	}


	/**
	* Saves box options and rules
	*/
	public function save_meta_options( $post_id ) {

		// Verify that the nonce is set and valid.
		if ( ! isset( $_POST['stb_options_nonce'] ) || ! wp_verify_nonce( $_POST['stb_options_nonce'], 'stb_options' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

    	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
       	 	return $post_id;
    	}

		// is this a revision save?
    	if ( wp_is_post_revision( $post_id ) ) {
        	return $post_id; 
    	}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$opts = $_POST['stb'];
		unset( $_POST['stb'] );

		// sanitize rules
		if( is_array( $opts['rules'] ) ) {
			foreach( $opts['rules'] as $key => $rule ) {

				// set value to 0 when condition is everywhere
				if( $rule['condition'] === 'everywhere' ) {
					$opts['rules'][$key]['value'] = '';
					break;
				}

			}
		}

		// sanitize settings
		$opts['css']['width'] = absint( sanitize_text_field( $opts['css']['width'] ) );
		$opts['css']['border_width'] = absint( sanitize_text_field( $opts['css']['border_width'] ) );
		$opts['cookie'] = absint( sanitize_text_field( $opts['cookie'] ) );
		$opts['trigger_percentage'] = absint( sanitize_text_field( $opts['trigger_percentage'] ) );
		$opts['trigger_element'] = sanitize_text_field( $opts['trigger_element'] );

		// save box settings
		update_post_meta( $post_id, 'stb_options', $opts );

		$this->flush_rules();
	}

	/**
	 * Add the settings link to the Plugins overview
	 * @param array $links
	 * @return array
	 */
	public function add_plugin_settings_link( $links, $file )
	{
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
