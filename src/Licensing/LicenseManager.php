<?php

namespace ScrollTriggeredBoxes\Licensing;

use ScrollTriggeredBoxes\Collection;

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
	 * @var API
	 */
	protected $api;

	/**
	 * @param Collection $extensions
	 * @param API $api
	 * @param License $license
	 */
	public function __construct( Collection $extensions, API $api, License $license ) {
		$this->extensions = $extensions;
		$this->license = $license;
		$this->api = $api;
	}

	/**
	 * Initialise the awesome
	 */
	public function add_hooks() {
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

		// load license
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

		$key_changed = false;

		// the form was submitted, let's see..
		if( $_POST['action'] === 'deactivate' ) {
			$this->license->deactivate();
			$this->api->logout();
		}

		// did key change or was "activate" button pressed?
		$new_license_key = sanitize_text_field( $_POST['stb_license_key'] );
		if( $new_license_key !== $this->license->key ) {
			$this->license->key = $new_license_key;
			$key_changed = true;
		}

		if( ! empty( $new_license_key )
		    && ! $this->license->activated
		    && ( $_POST['action'] === 'activate' || $key_changed ) ) {
			// let's try to activate it
			if( $this->api->login() ) {
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
		require __DIR__ . '/views/license-form.php';
	}

}