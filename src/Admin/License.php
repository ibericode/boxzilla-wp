<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\iPlugin;
use Closure;

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
	 * @var array
	 */
	protected $default_data = array(
		'key' => '',
		'activations' => array(),
		'expires_at' => ''
	);

	/**
	 * @var bool Loaded?
	 */
	protected $loaded = false;

	/**
	 * @var bool Any changes?
	 */
	protected $dirty = false;

	/**
	 * @param string $option_key
	 */
	public function __construct( $option_key ) {
		$this->option_key = $option_key;
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

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * (Re)load the license from the database
	 *
	 * @return License
	 */
	public function load() {

		if( ! $this->loaded ) {
			$data = (array) get_option( $this->option_key, array() );

			if( ! empty( $data ) ) {
				$data = array_merge( $this->default_data, $data );
				$this->key = (string) $data['key'];
				$this->activations = (array) $data['activations'];
				$this->expires_at = (string) $data['expires_at'];
			}

			// always fill site
			$this->site = get_option( 'siteurl' );
			$this->loaded = true;
		}

		return $this;
	}

	/**
	 * Reload the license data from DB
	 *
	 * @return License
	 */
	public function reload() {
		$this->loaded = false;
		return $this->load();
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
			$this->dirty = false;
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
	 *
	 */
	public function activate() {
		$this->activations = array( -1 );
		$this->dirty = true;
	}

	/**
	 *
	 */
	public function deactivate() {
		$this->activations = array();
		$this->dirty = true;
	}

	/**
	 * @param iPlugin $plugin
	 * @return bool
	 */
	public function is_plugin_activated( iPlugin $plugin ) {
		return in_array( $plugin->id(), $this->activations );
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function activate_plugin( iPlugin $plugin ) {
		$this->activations[ $plugin->id() ] = $plugin->id();
		$this->dirty = true;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function deactivate_plugin( iPlugin $plugin ) {
		unset( $this->activations[ $plugin->id() ] );
		$this->dirty = true;
	}

	/**
	 * @return int
	 */
	public function is_activated() {
		return count( $this->activations ) > 0;
	}

}