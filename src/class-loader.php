<?php

namespace Boxzilla;

class BoxLoader {


	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * @var array
	 */
	private $box_ids_to_load = array();

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor
	 *
	 * @param Plugin $plugin
	 * @param array $options
	 */
	public function __construct( Plugin $plugin, array $options ) {
		$this->plugin  = $plugin;
		$this->options = $options;
	}

	/**
	 * Initializes the plugin, runs on `wp` hook.
	 */
	public function init() {
		$this->box_ids_to_load = $this->filter_boxes();

		// Only add other hooks if necessary
		if ( count( $this->box_ids_to_load ) > 0 ) {
			add_action( 'wp_footer', array( $this, 'print_boxes_content' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 90 );
		}
	}

	/**
	 * Get global rules for all boxes
	 *
	 * @return array
	 */
	protected function get_filter_rules() {
		$rules = get_option( 'boxzilla_rules', array() );

		if ( ! is_array( $rules ) ) {
			return array();
		}

		return $rules;
	}


	/**
	 * Match a string against an array of patterns, glob-style.
	 *
	 * @param string $string
	 * @param array $patterns
	 * @param boolean $contains
	 * @return boolean
	 */
	protected function match_patterns( $string, array $patterns, $contains = false ) {
		$string = strtolower( $string );

		foreach ( $patterns as $pattern ) {
			$pattern = rtrim( $pattern, '/' );
			$pattern = strtolower( $pattern );

			if ( $contains ) {
				// contains means we should do a simple occurrence check
				// does not support wildcards
				$match = strpos( $string, $pattern ) !== false;
			} elseif ( function_exists( 'fnmatch' ) ) {
				$match = fnmatch( $pattern, $string );
			} else {
				$match = ( $pattern === $string );
			}

			if ( $match ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	protected function get_request_url() {
		return rtrim( $_SERVER['REQUEST_URI'], '/' );
	}

	/**
	 * Check if this rule passes (conditional matches expected value)
	 *
	 * @param string $condition
	 * @param string $value
	 * @param boolean $qualifier
	 *
	 * @return bool
	 */
	protected function match_rule( $condition, $value, $qualifier = true ) {
		$matched = false;

		// cast value to array & trim whitespace or excess comma's
		$values = array_map( 'trim', explode( ',', rtrim( trim( $value ), ',' ) ) );

		switch ( $condition ) {
			case 'everywhere':
				$matched = true;
				break;

			case 'is_url':
				$url     = $this->get_request_url();
				$matched = $this->match_patterns( $url, $values, $qualifier === 'contains' || $qualifier === 'not_contains' );
				break;

			case 'is_referer':
				if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
					$referer = $_SERVER['HTTP_REFERER'];
					$matched = $this->match_patterns( $referer, $values, $qualifier === 'contains' || $qualifier === 'not_contains' );
				}
				break;

			case 'is_post_type':
				$post_type = (string) get_post_type();
				$matched   = in_array( $post_type, $values, true );
				break;

			case 'is_single':
			case 'is_post':
				// convert to empty string if array with just empty string in it
				$value   = ( $values === array( '' ) ) ? '' : $values;
				$matched = is_single( $value );
				break;

			case 'is_post_in_category':
				$matched = is_singular() && has_category( $values );
				break;

			case 'is_page':
				$matched = is_page( $values );
				break;

			case 'is_post_with_tag':
				$matched = is_singular() && has_tag( $values );
				break;

			case 'is_user_logged_in':
				$matched = is_user_logged_in();
				break;
		}

		/**
		 * Filters whether a given box rule matches the condition and expected value.
		 *
		 * The dynamic portion of the hook, `$condition`, refers to the condition being matched.
		 *
		 * @param boolean $matched
		 * @param array $values
		 */
		$matched = apply_filters( 'boxzilla_box_rule_matches_' . $condition, $matched, $values );

		// if qualifier is set to false, we need to reverse this value here.
		if ( ! $qualifier || $qualifier === 'not_contains' ) {
			$matched = ! $matched;
		}

		return $matched;
	}

	/**
	 * Checks which boxes should be loaded for this request.
	 *
	 * @return array
	 */
	private function filter_boxes() {
		$box_ids_to_load = array();
		$rules           = $this->get_filter_rules();

		foreach ( $rules as $box_id => $box_rules ) {
			$matched     = false;
			$comparision = isset( $box_rules['comparision'] ) ? $box_rules['comparision'] : 'any';

			// loop through all rules for all boxes
			foreach ( $box_rules as $rule ) {

				// skip faulty values (and comparision rule)
				if ( empty( $rule['condition'] ) ) {
					continue;
				}

				$qualifier = isset( $rule['qualifier'] ) ? $rule['qualifier'] : true;
				$matched   = $this->match_rule( $rule['condition'], $rule['value'], $qualifier );

				// break out of loop if we've already matched
				if ( $comparision === 'any' && $matched ) {
					break;
				}

				// no need to continue if this rule didn't match
				if ( $comparision === 'all' && ! $matched ) {
					break;
				}
			}

			// value of $matched at this point determines whether box should be loaded
			$load_box = $matched;

			/**
			 * Filters whether a box should be loaded into the page HTML.
			 *
			 * The dynamic portion of the hook, `$box_id`, refers to the ID of the box. Return true if you want to output the box.
			 *
			 * @param boolean $load_box
			 */
			$load_box = apply_filters( 'boxzilla_load_box_' . $box_id, $load_box );

			/**
			 * Filters whether a box should be loaded into the page HTML.
			 *
			 * @param boolean $load_box
			 * @param int $box_id
			 */
			$load_box = apply_filters( 'boxzilla_load_box', $load_box, $box_id );

			// if matched, box should be loaded on this page
			if ( $load_box ) {
				$box_ids_to_load[] = $box_id;
			}
		}

		/**
		 * Filters which boxes should be loaded on this page, expects an array of post ID's.
		 *
		 * @param array $box_ids_to_load
		 */
		return apply_filters( 'boxzilla_load_boxes', $box_ids_to_load );
	}

	/**
	* Load plugin styles
	*/
	public function load_assets() {
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'boxzilla', $this->plugin->url( '/assets/js/script' . $pre_suffix . '.js' ), array(), $this->plugin->version(), true );

		// create boxzilla_Global_Options object
		$plugin_options = $this->options;
		$boxes          = $this->get_matched_boxes();

		$data = array(
			'testMode' => (bool) $plugin_options['test_mode'],
			'boxes'    => (array) array_map(
				function ( Box $box ) {
					return $box->get_client_options();
				},
				$boxes
			),
		);

		wp_localize_script( 'boxzilla', 'boxzilla_options', $data );

		do_action( 'boxzilla_load_assets', $this );
	}

	public function print_boxes_content() {
		$boxes = $this->get_matched_boxes();
		if ( empty( $boxes ) ) {
			return;
		}

		echo '<div style="display: none;">';
		foreach ( $boxes as $box ) {
			echo sprintf( '<div id="boxzilla-box-%d-content">', $box->ID ) . $box->get_content() . '</div>';
		}
		echo '</div>';
	}

	/**
	 * Get an array of Box objects. These are the boxes that will be loaded for the current request.
	 *
	 * @return Box[]
	 */
	public function get_matched_boxes() {
		static $boxes;

		if ( is_null( $boxes ) ) {
			if ( count( $this->box_ids_to_load ) === 0 ) {
				$boxes = array();
				return $boxes;
			}

			// query Box posts
			$q     = new \WP_Query();
			$posts = $q->query(
				array(
					'post_type'           => 'boxzilla-box',
					'post_status'         => 'publish',
					'post__in'            => $this->box_ids_to_load,
					'posts_per_page'      => count( $this->box_ids_to_load ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);

			// create `Box` instances out of \WP_Post instances
			$boxes = array();
			foreach ( $posts as $key => $post ) {
				$box = new Box( $post );

				// skip boxes without any content
				if ( $box->get_content() === '' ) {
					continue;
				}

				$boxes[ $key ] = $box;
			}
		}

		return $boxes;
	}
}
