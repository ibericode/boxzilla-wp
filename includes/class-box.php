<?php

class STB_Box {

	/**
	 * @param \WP_Post $post
	 */
	public $post;

	/**
	 * @var int
	 */
	public $ID;

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var string
	 */
	public $title = '';

	/**
	 * @var string
	 */
	public $content = '';

	/**
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * @param \WP_Post|int $post
	 */
	public function __construct( $post ) {

		// fetch post if it hasn't been fetched yet
		if( is_int( $post ) ) {
			$post = get_post( $post );
		}

		// store reference to post object in property
		$this->post = $post;

		// store ID in property for quick access
		$this->ID = $post->ID;

		// store title in property
		$this->title = $post->post_title;

		// store content in property
		$this->content = $post->post_content;

		// is this box enabled?
		$this->enabled = ( $post->post_status === 'publish' );

		// load and store options in property
		$this->options = $this->load_options();
	}

	/**
	 * Get the options for this box.
	 **
	 * @return array Array of box options
	 */
	protected function load_options() {

		static $defaults = array(
			'css' => array(
				'background_color' => '',
				'color' => '',
				'width' => '',
				'border_color' => '',
				'border_width' => '',
				'border_style' => '',
				'position' => 'bottom-right',
				'manual' => ''
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

		$opts = get_post_meta( $this->ID, 'stb_options', true );

		// merge with array of defaults
		foreach( $defaults as $key => $value ) {
			if( ! isset( $opts[$key] ) ) {
				$opts[ $key ] = $defaults[ $key ];
			} else {
				if( is_array( $value ) ) {
					$opts[$key] = array_merge( $defaults[$key], $opts[$key]);
				}
			}
		}
		$opts = array_merge( $defaults, $opts );

		// allow others to filter the final array of options
		return apply_filters( 'stb_box_options', $opts, $this );
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Get the options for this box
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get the CSS classes which are added to the `class` attribute of the Box element
	 *
	 * @return string
	 */
	public function get_css_classes() {

		// default classes
		$classes = array(
			'scroll-triggered-box',
			'stb',
			'stb-' . $this->options['css']['position']
		);

		// allow other plugins to add more classes
		$classes = (array) apply_filters( 'stb_box_css_classes', $classes, $this );

		// convert array of css classes to string
		return implode( ' ', $classes );
	}

	/**
	 * Get the close / hide icon for this box
	 *
	 * @return string
	 */
	public function get_close_icon() {
		$close_icon = apply_filters( 'stb_box_close_icon', '&times;', $this );
		return (string) $close_icon;
	}

	/**
	 * Get the content of this box
	 *
	 * @return mixed|void
	 */
	public function get_content() {
		$content = apply_filters( 'stb_box_content', $this->content, $this );
		return $content;
	}

	/**
	 * Get the minimum allowed screen size for this box
	 *
	 * @return int
	 */
	public function get_minimum_screen_size() {

		/**
		 * @filter stb_auto_hide_small_screens
		 * @expects bool
		 * @param int $box_id
		 * @deprecated 4.0 Use the hide_on_screen_size option instead
		 *
		 * Use to set whether the box should auto-hide on devices with a width smaller than 480px
		 */
		$auto_hide_small_screens = apply_filters( 'stb_auto_hide_small_screens', true, $this );

		if( '' === $this->options['hide_on_screen_size'] && $auto_hide_small_screens ) {
			$minimum_screen_size = absint( $this->options['css']['width'] );
		} elseif( $this->options['hide_on_screen_size'] > 0 ) {
			$minimum_screen_size = absint( $this->options['hide_on_screen_size'] );
		} else {
			$minimum_screen_size = 0;
		}

		return $minimum_screen_size;
	}

	/**
	 * Output HTML of this box
	 */
	public function output_html() {
			$opts = $this->options;
			$this->output_css();
			?>
			<div class="stb-container stb-<?php echo esc_attr( $opts['css']['position'] ); ?>-container">
				<div class="<?php echo esc_attr( $this->get_css_classes() ); ?>"
				     id="stb-<?php echo $this->ID; ?>"
				     style="display: none;">
					<div class="stb-content">
						<?php
						do_action( 'stb_print_box_content_before', $this );
						echo $this->get_content();
						do_action( 'stb_print_box_content_after', $this );
						?>
					</div>
					<span class="stb-close"><?php echo $this->get_close_icon(); ?></span>
				</div></div>
			<?php
	}

	/**
	 * Output a <style> block, containing the custom styles for this box
	 */
	public function output_css() {
		$css = $this->options['css'];

		// run filters
		$minimum_screen_size = $this->get_minimum_screen_size();
		?>
		<style type="text/css">
			<?php
				// open selector wrapper
				printf( '#stb-%d {', $this->ID );

				// print any rules which may have been set
				if ( '' !== $css['background_color'] ) {
					printf( 'background: %s;', esc_html( $css['background_color'] ) );
				}
				if ( '' !== $css['color'] ) {
					printf( 'color: %s;', esc_html( $css['color'] ) );
				}
				if ( '' !== $css['border_color'] ) {
					printf( 'border-color: %s;', esc_html( $css['border_color'] ) );
				}

				if( '' !== $css['border_width'] ) {
					printf( 'border-width: %dpx;', absint( $css['border_width'] ) );
				}

				if( ! empty( $css['width'] ) ) {
					printf( 'max-width: %dpx;', absint( $css['width'] ) );
				}

				if( $minimum_screen_size > 0 ) {
					printf( '@media ( max-width: %dpx ) { #stb-%d { display: none !important; } }', $minimum_screen_size, $this->ID );
				}

				// close wrapper
				echo '}';
				do_action( 'stb_print_box_css', $this ); ?>
		</style>
		<?php
	}
}