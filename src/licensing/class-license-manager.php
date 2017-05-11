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
	 * @param Notices $notices
	 */
	public function __construct( Collection $extensions, API $api, License $license, Notices $notices ) {
		$this->extensions = $extensions;
		$this->license = $license;
		$this->api = $api;
		$this->notices = $notices;
	}

	/**
	 * @param mixed $object
	 * @param string $property
	 * @param string $default
	 *
	 * @return string
	 */
	protected function get_object_property( $object, $property, $default = '' ) {
		return isset( $object->$property ) ? $object->$property : $default;
	}

	/**
	 * @return void
	 */
	public function hook() {

		// do nothing if no extensions are registered at this point
		if( count( $this->extensions ) === 0 ) {
			return;
		}

		// hooks
		add_action( 'boxzilla_after_settings', array( $this, 'show_license_form' ) );
		add_action( 'admin_notices', array( $this, 'show_notice' ), 1 );

		// listen for activation / deactivation requests
		$this->listen();
	}

	/**
	 * Maybe show notice to activate license.
	 */
	public function show_notice() {
		global $current_screen;

		if( $this->license->activated ) {
			return;
		}

		if( $this->get_object_property( $current_screen, 'post_type' ) !== 'boxzilla-box' ) {
			return;
		}

		$plugin = $this->extensions->random();
		$this->notices->add( sprintf( 'Please <a href="%s">activate your Boxzilla license</a> to use %s.', admin_url( 'edit.php?post_type=boxzilla-box&page=boxzilla-settings' ), '<strong>' . $plugin->name() . '</strong>' ), 'warning' );
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

		$this->license->save();
	}

	/**
	 * Deactivate the license
	 */
	protected function deactivate_license() {
		try {
			$this->api->deactivate_license();
			$this->notices->add( 'Your license was successfully deactivated!', 'info' );
		} catch( API_Exception $e ) {
			$this->notices->add( $e->getMessage(), 'warning' );
		}

		$this->license->activated = false;
		$this->license->activation_key = '';
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

		$this->license->activation_key = $activation->token;
		$this->license->activated = true;

		$this->notices->add( 'Your license was successfully activated!', 'info' );
	}

	/**
	 * Shows the license form
	 */
	public function show_license_form() {
		$license = $this->license;
		require __DIR__ . '/views/license-form.php';
	}

}