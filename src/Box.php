<?php

namespace Boxzilla;

use WP_Post;

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
	 * @param WP_Post|int $post
	 */
	public function __construct( $post ) {

		// fetch post if it hasn't been fetched yet
		if( ! $post instanceof WP_Post ) {
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
		$box = $this;

		$options = get_post_meta( $this->ID, 'boxzilla_options', true );
		$options = is_array( $options ) ? $options : array();

		// merge options with default options
		$options = array_replace_recursive( $defaults, $options );

		// set value of auto_show
		$options['auto_show'] = ! empty( $options['trigger'] );

		// allow others to filter the final array of options
		/**
		 * Filter the options for a given box
		 *
		 * @param array $options
		 * @param Box $box
		 */
		$options = apply_filters( 'boxzilla_box_options', $options, $box );

		return $options;
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
		$box = $this;
		$classes = array(
			'boxzilla',
			'boxzilla-' . $this->ID,
			'boxzilla-' . $this->options['css']['position']
		);

		/**
		 * Filters the CSS classes which are added to this box
		 *
		 * @param array $classes
		 * @param Box $box
		 */
		$classes = (array) apply_filters( 'boxzilla_box_css_classes', $classes, $box );

		// convert array of css classes to string
		return implode( ' ', $classes );
	}

	/**
	 * Get the close / hide icon for this box
	 *
	 * @return string
	 */
	public function get_close_icon() {

		$box = $this;
		$html = '&times;';

		/**
		 * Filters the HTML for the close icon.
		 *
		 * @param string $html
		 * @param Box $box
		 */
		$close_icon = (string) apply_filters( 'boxzilla_box_close_icon', $html, $box );

		return $close_icon;
	}

	/**
	 * Get the content of this box
	 *
	 * @return mixed|void
	 */
	public function get_content() {
		$content = $this->content;
		$box = $this;

		/**
		 * Filters the HTML for the box content
		 *
		 * @param string $content
		 * @param Box $box
		 */
		$content = apply_filters( 'boxzilla_box_content', $content, $box );
		return $content;
	}

	/**
	 * Get the minimum allowed screen size for this box
	 *
	 * @return int
	 */
	public function get_minimum_screen_size() {

		if( $this->options['hide_on_screen_size'] > 0 ) {
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

		?><div class="boxzilla-container boxzilla-<?php echo esc_attr( $opts['css']['position'] ); ?>-container">
			<div class="<?php echo esc_attr( $this->get_css_classes() ); ?>"
				 id="boxzilla-<?php echo $this->ID; ?>"
				 style="display: none;">
				<div class="boxzilla-content">
					<?php

					/**
					 * Runs just before outputting the box content
					 *
					 * @ignore
					 */
					do_action( 'boxzilla_print_box_content_before', $this );

					echo $this->get_content();

					/**
					 * Runs right after outputting the box content
					 *
					 * @ignore
					 */
					do_action( 'boxzilla_print_box_content_after', $this );
					?>
				</div>
				<?php if( ! empty( $close_icon ) && ! $this->options['unclosable'] ) { ?>
					<span class="boxzilla-close-icon"><?php echo $this->get_close_icon(); ?></span>
				<?php } ?>
			</div>
		</div><?php

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
		printf( '.boxzilla-%d {', $this->ID );
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
			printf( '@media ( max-width: %dpx ) { #boxzilla-%d { display: none !important; } }', $minimum_screen_size, $this->ID );
			print PHP_EOL;
		}

		// close wrapper
		echo '}' . PHP_EOL . PHP_EOL;

		// print manual css
		if( '' !== $css['manual'] ) {
			echo strip_tags( $css['manual'] );
		}

		/**
		 * Runs right after outputting custom CSS styles for a box.
		 */
		do_action( 'boxzilla_box_print_css', $this );

		if( $open_style_element ) {
			echo '</style>' . PHP_EOL;
		}

		$this->css_printed = true;
	}
}