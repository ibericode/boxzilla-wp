<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Plugin,
	ScrollTriggeredBoxes\PluginCollection;

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
	 * @param PluginCollection $extensions
	 */
	public function __construct( PluginCollection $extensions ) {
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
		if( $_POST['license_key'] !== $this->license->key ) {

			// key changed, let's deactivate old key (if it was activated)
			$this->license->deactivate_all();

			// now, let's save new key in database
			$this->license->key = sanitize_text_field( $_POST['license_key'] );
		}

		// if key isn't empty and activations have changed
		$new_activations = ( isset( $_POST['license_activations'] ) ) ? $_POST['license_activations'] : array();

		if( ! empty( $this->license->key )
			&& $new_activations !== $this->license->activations ) {

			// start by deactivating all
			$this->license->deactivate_all();

			foreach( $new_activations as $plugin_id ) {

				// get plugin
				$plugin = $this->extensions->find( $plugin_id );
				if( $plugin ) {
					$this->license->activate( $plugin );
				}
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

}