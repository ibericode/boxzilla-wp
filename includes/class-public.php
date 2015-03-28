<?php
if( ! defined( 'STB::VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class STB_Public {

	/**
	 * @var STB
	 */
	private $plugin;

	/**
	 * @var array
	 */
	private $matched_box_ids = array();

	/**
	 * Constructor
	 */
	public function __construct( STB $plugin ) {
		$this->plugin = $plugin;
		add_action( 'wp', array( $this, 'init' ) );
	}

	/**
	 * Initializes the plugin, runs on `wp` hook.
	 */
	public function init() {

		$this->matched_box_ids = $this->filter_boxes();

		// Only add other hooks if necessary
		if( count( $this->matched_box_ids ) > 0 ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
			add_action( 'wp_footer', array( $this, 'output_boxes' ), 1 );

			add_filter( 'stb_box_content', 'wptexturize') ;
			add_filter( 'stb_box_content', 'convert_smilies' );
			add_filter( 'stb_box_content', 'convert_chars' );
			add_filter( 'stb_box_content', 'wpautop' );
			add_filter( 'stb_box_content', 'shortcode_unautop' );
			add_filter( 'stb_box_content', 'do_shortcode', 11 );
		}
	}

	/**
	 * Checks which boxes should be loaded for this request.
	 *
	 * @return array
	 */
	private function filter_boxes() {

		$matched_box_ids = array();
		$rules = get_option( 'stb_rules', false );

		if ( ! is_array( $rules ) ) {
			return array();
		}

		foreach ( $rules as $box_id => $box_rules ) {

			$matched = false;

			foreach ( $box_rules as $rule ) {

				$condition = $rule['condition'];
				$value = trim( $rule['value'] );

				if ( $condition !== 'manual' && $condition !== 'everywhere' ) {
					$value = array_filter( array_map( 'trim', explode( ',', $value ) ) );
				}

				switch ( $condition ) {
					case 'everywhere';
						$matched = true;
						break;

					case 'is_post_type':
						$matched = in_array( get_post_type(), $value );
						break;

					case 'is_single':
						$matched = is_single( $value );
						break;

					case 'is_page':
						$matched = is_page( $value );
						break;

					case 'is_not_page':
						$matched = !is_page( $value );
						break;

					case 'manual':
						// eval for now...
						$value = stripslashes(trim($value));
						$matched = eval( "return (" . $value . ");" );
						break;

				}

				// no need to run through the other rules
				// if criteria has already been met by this rule
				if( $matched ) {
					break;
				}
			}

			/**
			 * @filter stb_show_box
			 * @expects bool
			 * @param int $box_id
			 *
			 * Use to run some custom logic whether to show a box or not.
			 * Return true if box should be shown.
			 */
			$matched = apply_filters('stb_show_box', $matched, $box_id);

			// if matched, box should be loaded on this page
			if ( $matched ) {
				$matched_box_ids[] = $box_id;
			}

		}

		return $matched_box_ids;
	}

	/**
	* Load plugin styles
	*/
	public function load_styles() {
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// stylesheets
		wp_register_style( 'scroll-triggered-boxes', $this->plugin->url . 'assets/css/styles' . $pre_suffix . '.css', array(), STB::VERSION );

		// scripts
		wp_register_script( 'scroll-triggered-boxes', $this->plugin->url . 'assets/js/script' . $pre_suffix . '.js' , array( 'jquery' ), STB::VERSION, true );

		// Finally, enqueue style.
		wp_enqueue_style( 'scroll-triggered-boxes' );
		wp_enqueue_script( 'scroll-triggered-boxes' );

		$this->pass_box_options();
	}

	/**
	 * Get an array of STB_Box objects. These are the boxes that will be loaded for the current request.
	 *
	 * @return array An array of `STB_Box` objects.
	 */
	public function get_matched_boxes() {
		static $boxes;

		if( is_null( $boxes ) ) {

			if( count( $this->matched_box_ids ) === 0 ) {
				$boxes = array();
				return $boxes;
			}

			// include Box class
			require_once dirname( STB::FILE ) . '/includes/class-box.php';

			// query Box posts
			$boxes = get_posts(
				array(
					'post_type' => 'scroll-triggered-box',
					'post_status' => 'publish',
					'post__in'    => $this->matched_box_ids
				)
			);

			// create `STB_Box` instances out of \WP_Post instances
			foreach ( $boxes as $key => $box ) {
				$boxes[ $key ] = new STB_Box( $box, $this->plugin->get_box_options( $box->ID ) );
			}
		}

		return $boxes;
	}

	/**
	 * Create array of Box options and pass it to JavaScript script.
	 */
	public function pass_box_options() {

		$boxes_options = array();

		foreach( $this->get_matched_boxes() as $box ) {

			/* @var $box STB_Box */

			// create array with box options
			$options = array(
				'id' => $box->ID,
				'trigger' => $box->options['trigger'],
				'triggerPercentage' => absint( $box->options['trigger_percentage'] ),
				'triggerElementSelector' => $box->options['trigger_element'],
				'animation' => $box->options['animation'],
				'cookeTime' => absint( $box->options['cookie'] ),
				'testMode' => (bool) $box->options['test_mode'],
				'autoHide' => (bool) $box->options['auto_hide'],
				'position' => $box->options['css']['position'],
				'minimumScreenWidth' => $box->get_minimum_screen_size()
			);

			$boxes_options[ $box->ID ] = $options;
		}

		wp_localize_script( 'scroll-triggered-boxes', 'STB_Options', $boxes_options );
	}

	/**
	* Outputs the boxes in the footer
	*/
	public function output_boxes() {
		?><!-- Scroll Triggered Boxes v<?php echo STB::VERSION; ?> - https://wordpress.org/plugins/scroll-triggered-boxes/--><?php

		// print HTML for each of the boxes
		foreach ( $this->get_matched_boxes() as $box ) {
			/* @var $box STB_Box */
			$box->output_html();
		}

			// print overlay element, we only need this once (it's re-used for all boxes)
			echo '<div id="stb-overlay"></div>';
		?><!-- / Scroll Triggered Box --><?php
	}


}