<?php


namespace Boxzilla\Admin;

class Installer {

	/**
	 * Run the installer
	 */
	public static function run() {
		$installer = new self;
		$installer->install();
	}

	/**
	 * The main install method
	 */
	public function install() {

		// don't install sample boxes on multisite
		if( is_multisite() ) {
			return;
		}

		$this->transfer_from_stb();
		$this->create_sample_box();
	}

	/**
	 *
	 */
	public function transfer_from_stb() {
		global $wpdb;

		// transfer post types
		$query = $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = %s WHERE  post_type = %s", 'boxzilla-box', 'scroll-triggered-box' );
		$wpdb->query( $query );

		// transfer post meta
		$query = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s", 'boxzilla_options', 'stb_options' );
		$wpdb->query( $query );

		// transfer rules
		$query = $wpdb->prepare( "UPDATE {$wpdb->options} SET option_name = %s WHERE option_name = %s", 'boxzilla_rules', 'stb_rules' );
		$wpdb->query( $query );
	}

	/**
	 * @return bool
	 */
	protected function create_sample_box() {

		// only create sample box if no boxes were found
		$boxes = get_posts(
			array(
				'post_type' => 'boxzilla-box',
				'post_status' => array( 'publish', 'draft' )
			)
		);

		if( ! empty( $boxes ) ) {
			return false;
		}

		$box_id = wp_insert_post(
			array(
				'post_type' => 'boxzilla-box',
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

		update_post_meta( $box_id, 'boxzilla_options', $settings );

		return true;
	}
}