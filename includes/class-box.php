<?php

class STB_Box {

	/**
	 * @param $post
	 */
	public $post;

	/**
	 * @var int
	 */
	public $ID;

	/**
	 * @var array
	 */
	public $options;

	/**
	 * @var string
	 */
	public $content;

	/**
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * @param \WP_Post $post
	 */
	public function __construct( \WP_Post $post, $options ) {
		$this->ID = $post->ID;
		$this->content = $post->post_content;
		$this->options = $options;
		$this->enabled = ( $post->post_status === 'publish' );
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
					printf( 'color: %s', esc_html( $css['color'] ) );
				}
				if ( '' !== $css['border_color'] && ! '' !== $css['border_width'] ) {
					printf( 'border: %spx solid %s', esc_html( $css['border_width'] ), esc_html( $css['border_color'] ) );
				}
				if( ! empty( $css['width'] ) ) {
					printf( 'max-width: %dpx', absint( $css['width'] ) );
				}

				if( $minimum_screen_size > 0 ) {
					printf( '@media (max-width: %dpx { #stb-%d { display: none !important; } }', $minimum_screen_size, $this->ID );
				}

				// close wrapper
				echo '}';
				do_action( 'stb_print_box_css', $this ); ?>
		</style>
		<?php
	}
}