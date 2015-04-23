<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Plugin,
	ScrollTriggeredBoxes\Collection,
	Pimple\Container;

class LicenseManager {

	/**
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * @var Notices
	 */
	protected $notices;

	/**
	 * @var LicenseAPI
	 */
	protected $api;

	/**
	 * @param Collection $extensions
	 */
	public function __construct( Collection $extensions, Notices $notices ) {
		$this->extensions = $extensions;
		$this->notices = $notices;

		// register license activation form
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * @return bool
	 */
	public function init() {

		// do nothing if not authenticated
		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// do nothing if no registered extensions
		if( count( $this->extensions ) === 0 ) {
			return false;
		}

		// load license
		$this->license = new License( 'stb_license' );
		$this->license->load();

		// register license key form
		add_action( 'stb_after_settings', array( $this, 'show_license_form' ) );

		// listen for activation / deactivation requests
		$this->listen();

		// register update checks

		return true;
	}

	/**
	 * @return bool
	 */
	protected function listen() {

		// nothing to do
		if( ! isset( $_POST['stb_license_form'] ) ) {
			return false;
		}

		// the form was submitted, let's see..
		if( $_POST['action'] === 'deactivate' ) {
			if( $this->api()->deactivate( $this->license ) ) {
				$this->license->deactivate();
			}
		}

		// did key change or was "activate" button pressed?
		$new_license_key = sanitize_text_field( $_POST['license_key'] );
		if( ! empty( $new_license_key ) && ( $_POST['action'] === 'activate' || $new_license_key !== $this->license->key ) ) {
			$this->license->key = $new_license_key;
			if( $this->api()->activate( $this->license ) ) {
				$this->license->activate();
			}
		}

		$this->license->save();
		return false;
	}

	/**
	 * Shows the license form
	 */
	public function show_license_form() {
		require Plugin::DIR . '/views/parts/license-form.php';
	}

	/**
	 * @return APIConnector
	 */
	protected function api() {
		$plugin = Plugin::instance();
		return $plugin['api_connector'];
	}

}