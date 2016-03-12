<?php

namespace ScrollTriggeredBoxes;

use ScrollTriggeredBoxes\DI\Container;

class PluginBase extends Container implements iPlugin {

	/**
	 * @var string The current version of the plugin
	 */
	protected $version = '1.0';

	/**
	 * @var string
	 */
	protected $file = '';

	/**
	 * @var string
	 */
	protected $dir = '';

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $slug = '';

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Constructor
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $version
	 * @param string $file
	 * @param string $dir
	 */
	public function __construct( $id, $name, $version, $file, $dir ) {
		$this->id = $id;
		$this->name = $name;
		$this->version = $version;
		$this->file = $file;
		$this->dir = $dir;
		$this->slug = plugin_basename( $file );

		parent::__construct();

		// register services early since some add-ons need 'm
		$this->register_services();

		// load rest of classes on a later hook
		$this->load();
	}

	/**
	 * Register services in the Service Container
	 */
	protected function register_services() {}

	/**
	 * Start loading classes on `plugins_loaded`, priority 20.
	 */
	public function load() {}

	/**
	 * @return int
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function file() {
		return $this->file;
	}

	/**
	 * @return string
	 */
	public function dir() {
		return $this->dir;
	}

	/**
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function url( $path = '' ) {
		return plugins_url( $path, $this->file() );
	}
}