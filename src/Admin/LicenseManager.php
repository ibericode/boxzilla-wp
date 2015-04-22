<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Plugin;

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
	 * @param array $extensions
	 */
	public function __construct( array $extensions ) {
		$this->extensions = $extensions;

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

		// register license key form
		add_action( 'stb_after_settings_rows', array( $this, 'show_license_form' ) );

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
		if( $_POST['license_key'] !== $this->license->key ) {

			// key changed, let's deactivate old key (if it was activated)
			if( $this->license->activated ) {
				foreach( $this->extensions as $plugin ) {
					$this->license->deactivate( $plugin );
				}
			}

			// now, let's save new key in database
			$this->license->key = sanitize_text_field( $_POST['license_key'] );
			$this->license->save();

			// if new key isn't empty, activate it
			if( ! empty( $this->license->key ) ) {
				foreach( $this->extensions as $plugin ) {
					$this->license->activate( $plugin );
				}
			}


		}

		return false;
	}

	/**
	 * Shows the license form
	 */
	public function show_license_form() {
		require Plugin::DIR . '/views/parts/license-form.php';
	}

}