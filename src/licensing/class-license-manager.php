<?php

namespace Boxzilla\Licensing;

use Boxzilla\Collection;

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
		// register license key form
		add_action( 'boxzilla_after_settings', array( $this, 'show_license_form' ) );

		// listen for activation / deactivation requests
		$this->listen();
	}

	/**
	 * @return bool
	 */
	protected function listen() {

		// do nothing if not authenticated
		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// nothing to do
		if( ! isset( $_POST['boxzilla_license_form'] ) ) {
			return false;
		}

		$action = isset( $_POST['action'] ) ? $_POST['action'] : 'activate';
		$key_changed = false;

		// the form was submitted, let's see..
		if( $action === 'deactivate' ) {
			$this->api->deactivate_license();
			$this->license->activated = false;
			$this->license->activation_key = '';
		}

		// did key change or was "activate" button pressed?
		$new_license_key = sanitize_text_field( $_POST['boxzilla_license_key'] );
		if( $new_license_key !== $this->license->key ) {
			$this->license->key = $new_license_key;
			$key_changed = true;
		}

		// try to activate license
		if( $action === 'activate' || $key_changed ) {
			$activation_key = $this->api->activate_license();
			if( $activation_key ) {
				$this->license->activation_key = $activation_key;
				$this->license->activated = true;
			}
		}

		// save changes
		$this->license->save();
		return false;
	}

	/**
	 * Shows the license form
	 */
	public function show_license_form() {
		$license = $this->license;
		require __DIR__ . '/views/license-form.php';
	}

}