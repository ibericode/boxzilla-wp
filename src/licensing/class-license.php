<?php

namespace Boxzilla\Licensing;

/**
 * Class License
 *
 *
 * @property string $key
 * @property string $site
 * @property string $activation_key
 * @property boolean $activated
 * @property string $expires_at
 *
 * @package Boxzilla\Licensing
 */
class License {

	/**
	 * @var string The name of the option that holds the License data
	 */
	protected $option_key = '';

	/**
	 * @var bool Loaded?
	 */
	protected $loaded = false;

	/**
	 * @var bool Any changes?
	 */
	protected $dirty = false;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @param string $option_key
	 */
	public function __construct( $option_key ) {
		$this->option_key = $option_key;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->load();
		$this->data[ $name ] = $value;
		$this->dirty = true;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get($name) {
		$this->load();
		return $this->data[ $name ];
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		$this->load();
		return isset( $this->data[ $name ] );
	}

	/**
	 * Load the license data from the database
	 */
	protected function load() {
		static $defaults = array(
			'key' => '',
			'activation_key' => '',
			'activated' => false,
			'expires_at' => ''
		);

		if( ! $this->loaded ) {
			$data = (array) get_option( $this->option_key, array() );
			$this->data = array_replace( $defaults, $data );
			$this->loaded = true;
		}
	}

	/**
	 * Reload the license data from DB
	 */
	public function reload() {
		$this->loaded = false;
		$this->load();
	}

	/**
	 * Save the license in the database
	 *
	 * @return License
	 */
	public function save() {
		if( $this->dirty ) {
			update_option( $this->option_key, $this->data );
			$this->dirty = false;
		}
	}

}