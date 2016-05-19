<?php

namespace Boxzilla\Licensing;

use Boxzilla\Admin\Notices;
use Boxzilla\Collection;
use Exception;

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
	 * @var Notices
	 */
	protected $notices;

	/**
	 * @param Collection $extensions
	 * @param API $api
	 * @param License $license
	 */
	public function __construct( Collection $extensions, API $api, License $license, Notices $notices ) {
		$this->extensions = $extensions;
		$this->license = $license;
		$this->api = $api;
		$this->notices = $notices;
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
		// do nothing if no extensions
		if( empty( $this->extensions ) ) {
			return;
		}

		// register license key form
		add_action( 'boxzilla_after_settings', array( $this, 'show_license_form' ) );

		// listen for activation / deactivation requests
		$this->listen();
	}

	/**
	 * @return void
	 */
	protected function listen() {

		// do nothing if not authenticated
		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// nothing to do
		if( ! isset( $_POST['boxzilla_license_form'] ) ) {
			return;
		}

		$action = isset( $_POST['action'] ) ? $_POST['action'] : 'activate';
		$key_changed = false;

		// did key change or was "activate" button pressed?
		$new_license_key = sanitize_text_field( $_POST['boxzilla_license_key'] );
		if( $new_license_key !== $this->license->key ) {
			$this->license->key = $new_license_key;
			$key_changed = true;
		}

		// run actions
		if( $action === 'deactivate' ) {
			$this->deactivate_license();
		} elseif( $action === 'activate' || $key_changed ) {
			$this->activate_license();
		}
	}

	/**
	 * Deactivate the license
	 */
	protected function deactivate_license() {
		try {
			$activation = $this->api->deactivate_license();
			$this->notices->add( $activation->message, 'info' );
		} catch( API_Exception $e ) {
			$this->notices->add( $e->getMessage(), 'warning' );
		}

		$this->license->activated = false;
		$this->license->activation_key = '';
		$this->license->save();
	}

	/**
	 * Activate the license
	 */
	protected function activate_license() {
		try {
			$activation = $this->api->activate_license();
		} catch( API_Exception $e ) {
			$this->notices->add( $e->getMessage(), 'warning' );
			return;
		}

		$this->license->activation_key = $activation->key;
		$this->license->activated = true;
		$this->license->save();

		$this->notices->add( $activation->message, 'info' );
	}

	/**
	 * Shows the license form
	 */
	public function show_license_form() {
		$license = $this->license;
		require __DIR__ . '/views/license-form.php';
	}

}