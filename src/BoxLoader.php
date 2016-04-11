<?php

namespace ScrollTriggeredBoxes;

class BoxLoader {

	/**
	 * @var iPlugin
	 */
	private $plugin;

	/**
	 * @var array
	 */
	private $matched_box_ids = array();

	/**
	 * Constructor
	 *
	 * @param iPlugin $plugin
	 */
	public function __construct( iPlugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Initializes the plugin, runs on `wp` hook.
	 */
	public function init() {

		$this->matched_box_ids = $this->filter_boxes();

		// Only add other hooks if necessary
		if( count( $this->matched_box_ids ) > 0 ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
			add_action( 'wp_head', array( $this, 'print_boxes_css' ), 90 );
			add_action( 'wp_footer', array( $this, 'print_boxes_html' ) );

			add_filter( 'stb_box_content', 'wptexturize') ;
			add_filter( 'stb_box_content', 'convert_smilies' );
			add_filter( 'stb_box_content', 'convert_chars' );
			add_filter( 'stb_box_content', 'wpautop' );
			add_filter( 'stb_box_content', 'shortcode_unautop' );
			add_filter( 'stb_box_content', 'do_shortcode', 11 );
		}
	}

	/**
	 * Get global rules for all boxes
	 *
	 * @return array
	 */
	protected function get_filter_rules() {
		$rules = get_option( 'stb_rules', array() );

		if( ! is_array( $rules ) ) {
			return array();
		}

		return $rules;
	}


	/**
	 * Match a string against an array of patterns, glob-style.
	 *
	 * @param string $string
	 * @param array $patterns
	 *
	 * @return boolean
	 */
	protected function match_patterns( $string, $patterns ) {
		$string = strtolower( $string );

		foreach( $patterns as $pattern ) {

			$pattern = strtolower( $pattern );

			if( function_exists( 'fnmatch' ) ) {
				$match = fnmatch( $pattern, $string );
			} else {
				$match = ( $pattern === $string );
			}

			if( $match ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if this rule passes (conditional matches expected value)
	 *
	 * @param $condition
	 * @param $value
	 *
	 * @return bool|mixed
	 */
	protected function match_rule( $condition, $value ) {

		$matched = false;
		$value = trim( $value );

		// cast value to array & trim whitespace or excess comma's
		if ( $condition !== 'manual' ) {
			$value = array_map( 'trim', explode( ',', rtrim( trim( $value ), ',' ) ) );
		}

		switch ( $condition ) {
			case 'everywhere';
				$matched = true;
				break;

			case 'is_url':
				$matched = $this->match_patterns( $_SERVER['REQUEST_URI'], $value );
				break;

			case 'is_referer':
				if( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
					$referer = $_SERVER['HTTP_REFERER'];
					$matched = $this->match_patterns( $referer, $value );
				}
				break;

			case 'is_post_type':
				$post_type = (string) get_post_type();
				$matched = in_array( $post_type, (array) $value );
				break;

			case 'is_single':
			case 'is_post':
				$matched = is_single( $value );
				break;

			case 'is_post_in_category':
				$matched = is_singular( 'post' ) && has_category( $value );
				break;


			case 'is_page':
				$matched = is_page( $value );
				break;

			/**
			 * @deprecated 2.1
			 */
			case 'is_not_page':
				$matched = ! is_page( $value );
				break;

			case 'manual':
				// eval for now...
				$value = stripslashes( trim( $value ) );

				if( ! empty( $value ) ) {
					$matched = eval( "return (" . $value . ");" );
				}

				break;

		}

		return $matched;
	}

	/**
	 * Checks which boxes should be loaded for this request.
	 *
	 * @return array
	 */
	private function filter_boxes() {

		$matched_box_ids = array();
		$rules = $this->get_filter_rules();

		foreach( $rules as $box_id => $box_rules ) {

			$matched = false;
			$comparision = isset( $box_rules['comparision'] ) ? $box_rules['comparision'] : 'any';

			// loop through all rules for all boxes
			foreach ( $box_rules as $rule ) {

				// skip faulty values (and comparision rule)
				if( empty( $rule['condition'] ) ) {
					continue;
				}

				$matched = $this->match_rule( $rule['condition'], $rule['value'] );

				// break out of loop if we've already matched
				if( $comparision === 'any' && $matched ) {
					break;
				}

				if( $comparision === 'all' && ! $matched ) {
					break;
				}
			}

			/**
			 * @filter stb_show_box_{$box_id]
			 * @expects bool
			 *
			 * Use to run some custom logic whether to show this specific box or not.
			 * Return true if box should be shown.
			 */
			$matched = apply_filters( 'stb_show_box_' . $box_id, $matched );

			/**
			 * @filter stb_show_box
			 * @expects bool
			 * @param int $box_id
			 *
			 * Use to run some custom logic whether to show a box or not.
			 * Return true if box should be shown.
			 */
			$matched = apply_filters( 'stb_show_box', $matched, $box_id );

			// if matched, box should be loaded on this page
			if ( $matched ) {
				$matched_box_ids[] = $box_id;
			}

		}

		return (array) apply_filters( 'stb_matched_boxes_ids', $matched_box_ids );
	}

	/**
	* Load plugin styles
	*/
	public function load_assets() {
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// stylesheets
		wp_register_style( 'scroll-triggered-boxes', $this->plugin->url( '/assets/css/styles' . $pre_suffix . '.css' ), array(), $this->plugin->version() );

		// scripts
		wp_register_script( 'scroll-triggered-boxes',$this->plugin->url( '/assets/js/script' . $pre_suffix . '.js' ), array( 'jquery' ), $this->plugin->version(), true );

		// Finally, enqueue style.
		wp_enqueue_style( 'scroll-triggered-boxes' );
		wp_enqueue_script( 'scroll-triggered-boxes' );

		$this->pass_box_options();

		do_action( 'stb_load_assets', $this );
	}

	/**
	 * Get an array of Box objects. These are the boxes that will be loaded for the current request.
	 *
	 * @return array An array of `Box` objects.
	 */
	public function get_matched_boxes() {
		static $boxes;

		if( is_null( $boxes ) ) {

			if( count( $this->matched_box_ids ) === 0 ) {
				$boxes = array();
				return $boxes;
			}

			// query Box posts
			$boxes = get_posts(
				array(
					'post_type' => 'scroll-triggered-box',
					'post_status' => 'publish',
					'post__in'    => $this->matched_box_ids,
					'numberposts' => -1
				)
			);

			// create `Box` instances out of \WP_Post instances
			foreach ( $boxes as $key => $box ) {
				$boxes[ $key ] = new Box( $box );
			}
		}

		return $boxes;
	}

	/**
	 * Create array of Box options and pass it to JavaScript script.
	 */
	public function pass_box_options() {

		// create STB_Global_Options object
		$plugin_options = $this->plugin['options'];
		$global_options = array(
			'testMode' => (bool) $plugin_options['test_mode']
		);
		wp_localize_script( 'scroll-triggered-boxes', 'STB_Global_Options', $global_options );


		// create STB_Box_Options object
		$boxes_options = array();
		foreach( $this->get_matched_boxes() as $box ) {

			/* @var $box Box */

			// create array with box options
			$options = array(
				'id' => $box->ID,
				'title' => $box->title,
				'trigger' => $box->options['trigger'],
				'triggerPercentage' => absint( $box->options['trigger_percentage'] ),
				'triggerElementSelector' => $box->options['trigger_element'],
				'animation' => $box->options['animation'],
				'cookieTime' => absint( $box->options['cookie'] ),
				'autoHide' => (bool) $box->options['auto_hide'],
				'autoShow' => (bool) $box->options['auto_show'],
				'position' => $box->options['css']['position'],
				'minimumScreenWidth' => $box->get_minimum_screen_size(),
				'unclosable' => $box->options['unclosable'],
			);

			$boxes_options[ $box->ID ] = $options;
		}

		wp_localize_script( 'scroll-triggered-boxes', 'STB_Box_Options', $boxes_options );
	}

	/**
	* Outputs the boxes in the footer
	*/
	public function print_boxes_html() {
		?><!-- Scroll Triggered Boxes v<?php echo $this->plugin->version(); ?> - https://wordpress.org/plugins/scroll-triggered-boxes/--><?php

		// print HTML for each of the boxes
		foreach ( $this->get_matched_boxes() as $box ) {
			/* @var $box Box */
			$box->print_html();
		}

			// print overlay element, we only need this once (it's re-used for all boxes)
			echo '<div id="stb-overlay"></div>';
		?><!-- / Scroll Triggered Box --><?php
	}

	/**
	 * Print CSS of all boxes in <head>
	 */
	public function print_boxes_css() {
		echo '<style type="text/css">' . PHP_EOL;

		// print HTML for each of the boxes
		foreach ( $this->get_matched_boxes() as $box ) {
			/* @var $box Box */
			$box->print_css();
		}

		echo '</style>' . PHP_EOL . PHP_EOL;
	}


}


