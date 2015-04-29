<?php

namespace ScrollTriggeredBoxes\Licensing;

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
	 * Is license activated?
	 *
	 * @var bool
	 */
	protected $activated = false;

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
		'activated' => false,
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
				$this->activated = (bool) $data['activated'];
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
			'activated' => $this->activated
		);

		return $data;
	}

	/**
	 *
	 */
	public function activate() {
		$this->activated = true;
		$this->dirty = true;
	}

	/**
	 *
	 */
	public function deactivate() {
		$this->activated = false;
		$this->dirty = true;
	}

}