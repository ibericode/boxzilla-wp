<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\iPlugin;

class License {

	/**
	 * @var string The license key
	 */
	public $key = '';

	/**
	 * When this license is set to expire
	 *
	 * @var \DateTime
	 */
	public $expires_at;

	/**
	 * Whether this license is currently activated
	 *
	 * @var bool
	 */
	public $activated = false;

	/**
	 * The site this license is used on
	 *
	 * @var string
	 */
	public $site = '';

	/**
	 * @var string The name of the option that holds the License data
	 */
	protected $option_name = '';

	/**
	 * @var API
	 */
	protected $api;

	/**
	 * @param string $option_key
	 */
	public function __construct( $option_key ) {
		$this->option_key = $option_key;
		$this->load();
	}

	/**
	 * (Re)load the license from the database
	 *
	 * @return License
	 */
	public function load() {
		$data = (array) get_option( $this->option_key, array() );

		if( ! empty( $data ) ) {
			$this->key = (string) $data['key'];
			$this->activated = (bool) $data['activated'];
			$this->expires_at = (string) $data['expires_at'];
		}

		// always fill site
		$this->site = get_option( 'siteurl' );

		return $this;
	}

	/**
	 * Save the license in the database
	 *
	 * @return License
	 */
	public function save() {
		$data = $this->toArray();
		update_option( $this->option_key, $data );
		return $this;
	}

	/**
	 * Create an array from this License object
	 *
	 * @return array
	 */
	public function toArray() {

		$data = array(
			'key' => $this->key,
			'expires_at' => $this->expires_at,
			'activated' => $this->activated
		);

		return $data;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function activate( iPlugin $plugin ) {
		$this->activated = $this->api()->activate_license( $plugin );
		$this->save();
		return $this->activated;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function deactivate( iPlugin $plugin ) {
		$this->activated = ! $this->api()->deactivate_license( $plugin );
		$this->save();
		return ! $this->activated;
	}

	/**
	 * @return API
	 */
	protected function api() {

		if( is_null( $this->api ) ) {
			$this->api = new API( $this );
		}

		return $this->api;
	}
}