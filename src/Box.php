<?php

namespace ScrollTriggeredBoxes;

class Box {

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
	 * @var bool
	 */
	public $css_printed = false;

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
				0 => array('condition' => '', 'value' => '')
			),
			'rules_comparision' => 'any',
			'cookie' => 0,
			'trigger' => 'percentage',
			'trigger_percentage' => 65,
			'trigger_element' => '',
			'animation' => 'fade',
			'auto_hide' => 0,
			'hide_on_screen_size' => '',
			'unclosable' => false,
		);

		$opts = get_post_meta( $this->ID, 'stb_options', true );

		// merge CSS options
		if( ! isset( $opts['css'] ) ) {
			$opts['css'] = $defaults['css'];
		} else {
			$opts['css'] = array_merge( $defaults['css'], $opts['css'] );
		}

		// merge rest
		$opts = array_merge( $defaults, $opts );

		// set value of auto_show
		$opts['auto_show'] = ! empty( $opts['trigger'] );

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
			'stb-' . $this->ID,
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
	public function print_html() {
		$opts = $this->options;
		$close_icon = $this->get_close_icon();
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
					<?php if( ! empty( $close_icon ) && ! $this->options['unclosable'] ) { ?>
						<span class="stb-close"><?php echo $this->get_close_icon(); ?></span>
					<?php } ?>
				</div>
			</div>
			<?php

		// make sure box specifix CSS is printed
		$this->print_css( true );
	}

	/**
	 * Output a <style> block, containing the custom styles for this box
	 * @param bool $open_style_element
	 */
	public function print_css( $open_style_element = false ) {

		// only print this once
		if( $this->css_printed ) {
			return;
		}

		$css = $this->options['css'];

		// run filters
		$minimum_screen_size = $this->get_minimum_screen_size();

		if( $open_style_element ) {
			echo '<style type="text/css">' . PHP_EOL;
		}

		printf( "/* Custom Styles for Box %d */", $this->ID );
		print PHP_EOL;

		// open selector wrapper
		printf( '.stb-%d {', $this->ID );
		print PHP_EOL;

		// print any rules which may have been set
		if ( '' !== $css['background_color'] ) {
			printf( 'background: %s !important;', strip_tags( $css['background_color'] ) );
			print PHP_EOL;
		}
		if ( '' !== $css['color'] ) {
			printf( 'color: %s !important;', strip_tags( $css['color'] ) );
			print PHP_EOL;
		}
		if ( '' !== $css['border_color'] ) {
			printf( 'border-color: %s !important;', strip_tags( $css['border_color'] ) );
			print PHP_EOL;
		}

		if( '' !== $css['border_width'] ) {
			printf( 'border-width: %dpx !important;', absint( $css['border_width'] ) );
			print PHP_EOL;
		}

		if( '' !== $css['border_style'] ) {
			printf( 'border-style: %s !important;', strip_tags( $css['border_style'] ) );
			print PHP_EOL;
		}

		if( ! empty( $css['width'] ) ) {
			printf( 'max-width: %dpx;', absint( $css['width'] ) );
			print PHP_EOL;
		}

		if( $minimum_screen_size > 0 ) {
			printf( '@media ( max-width: %dpx ) { #stb-%d { display: none !important; } }', $minimum_screen_size, $this->ID );
			print PHP_EOL;
		}

		// close wrapper
		echo '}' . PHP_EOL . PHP_EOL;

		// print manual css
		if( '' !== $css['manual'] ) {
			echo strip_tags( $css['manual'] );
		}

		do_action( 'stb_box_print_css', $this );

		if( $open_style_element ) {
			echo '</style>' . PHP_EOL;
		}

		$this->css_printed = true;
	}
}