<?php


namespace ScrollTriggeredBoxes\Admin;

class Installer {

	public static function run() {
		return new self;
	}

	public function __construct() {
		$this->create_sample_box();
	}

	public function create_sample_box() {

		// only create sample box if no boxes were found
		$boxes = get_posts( array( 'post_type' => 'scroll-triggered-box' ) );
		if( $boxes ) {
			return false;
		}

		$box_id = wp_insert_post(
			array(
				'post_type' => 'scroll-triggered-box',
				'post_title' => "Sample Box",
				'post_content' => "<h4>Hello world.</h4><p>This is a sample box, with some sample content in it.</p>",
				'post_status' => 'draft',
			)
		);

		// set box settings
		$settings = array(
				'css' => array(
				'background_color' => '#edf9ff',
				'color' => '',
				'width' => '340',
				'border_color' => '#dd7575',
				'border_width' => '4',
				'border_style' => 'dashed',
				'position' => 'bottom-right',
				'manual' => ''
			)
		);
		update_post_meta( $box_id, 'stb_options', $settings );

		return true;
	}
}