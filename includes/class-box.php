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
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
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
	 * @return string
	 */
	public function get_close_icon() {
		$close_icon = apply_filters( 'stb_box_close_icon', '&times;', $this );
		return (string) $close_icon;
	}

	/**
	 * @return mixed|void
	 */
	public function get_content() {
		$content = apply_filters( 'stb_box_content', $this->content, $this );
		return $content;
	}

	/**
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
			$opts = $this->get_options();
			$css = $opts['css'];

			// run filters
			$minimum_screen_size = $this->get_minimum_screen_size();
			?>
			<style type="text/css">
				#stb-<?php echo $this->ID; ?> {
					background: <?php echo ( ! empty( $css['background_color'] ) ) ? esc_html( $css['background_color'] ) : ''; ?>;
					<?php if ( !empty( $css['color'] ) ) { ?>color: <?php echo esc_html( $css['color'] ); ?>;<?php } ?>
					<?php if ( !empty( $css['border_color'] ) && ! empty( $css['border_width'] ) ) { ?>border: <?php echo esc_html( $css['border_width'] ) . 'px' ?> solid <?php echo esc_html( $css['border_color'] ); ?>;<?php } ?>
					max-width: <?php echo ( !empty( $css['width'] ) ) ? absint( $css['width'] ) . 'px': 'auto'; ?>;
				}

				<?php if( $minimum_screen_size > 0 ) { ?>
				@media (max-width: <?php echo $minimum_screen_size; ?>px) {
					#stb-<?php echo $this->ID; ?> { display: none !important; }
				}
				<?php } ?>

				<?php do_action( 'stb_print_box_css', $this ); ?>
			</style>
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
}