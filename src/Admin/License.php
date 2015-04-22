<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\iPlugin;

class License {

	/**
	 * @var string The license key
	 */
	protected $key = '';

	/**
	 * When this license is set to expire
	 *
	 * @var \DateTime
	 */
	protected $expires_at;

	/**
	 * Plugins this license is activated for
	 *
	 * @var array
	 */
	protected $activations = array();

	/**
	 * The site this license is used on
	 *
	 * @var string
	 */
	protected $site = '';

	/**
	 * @var string The name of the option that holds the License data
	 */
	protected $option_name = '';

	/**
	 * @var API
	 */
	protected $api;


	protected $default_data = array(
		'key' => '',
		'activations' => array(),
		'expires_at' => ''
	);

	/**
	 * @var bool Any changes?
	 */
	protected $dirty = false;

	/**
	 * @param string $option_key
	 */
	public function __construct( $option_key ) {
		$this->option_key = $option_key;
		$this->load();
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		$this->$name = $value;
		$this->dirty = true;
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function __get($name) {
		if( property_exists( $this, $name ) ) {
			return $this->$name;
		}

		return null;
	}

	public function __isset($name) {
		return isset($this->$name);
	}

	/**
	 * (Re)load the license from the database
	 *
	 * @return License
	 */
	public function load() {
		$data = (array) get_option( $this->option_key, array() );

		if( ! empty( $data ) ) {
			$data = array_merge( $this->default_data, $data );
			$this->key = (string) $data['key'];
			$this->activations = (array) $data['activations'];
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

		if( $this->dirty ) {
			$data = $this->toArray();
			update_option( $this->option_key, $data );
		}

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
			'activations' => $this->activations
		);

		return $data;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function activate( iPlugin $plugin ) {
		$success = $this->api()->activate_plugin( $plugin );
		if( $success ) {
			$this->activations[ $plugin->id() ] = $plugin->id();
			$this->dirty = true;
		}
		return $success;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function deactivate( iPlugin $plugin ) {
		$success = $this->api()->deactivate_plugin( $plugin );
		if( $success ) {
			unset( $this->activations[ $plugin->id() ] );
			$this->dirty = true;
		}

		return $success;
	}

	/**
	 *
	 */
	public function deactivate_all() {
		$success = $this->api()->deactivate_all();
		if( $success ) {
			$this->activations = array();
			$this->dirty = true;
		}
		return $success;
	}

	/**
	 * @param iPlugin $plugin
	 * @return bool
	 */
	public function is_plugin_activated( iPlugin $plugin ) {
		return in_array( $plugin->id(), $this->activations );
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