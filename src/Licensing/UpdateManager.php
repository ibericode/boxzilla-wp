<?php

namespace ScrollTriggeredBoxes\Licensing;

use ScrollTriggeredBoxes\Collection,
	ScrollTriggeredBoxes\iPlugin,
	ScrollTriggeredBoxes\Plugin,
	ScrollTriggeredBoxes\Admin\Notices;

class UpdateManager {

	/**
	 * @var Collection
	 */
	protected $extensions;

	/**
	 * @var API
	 */
	protected $api;

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * @var
	 */
	protected $available_updates;

	/**
	 * @param Collection $extensions
	 * @param API        $api
	 * @param License    $license
	 */
	public function __construct( Collection $extensions, API $api, License $license ) {
		$this->extensions = $extensions;
		$this->license = $license;
		$this->api = $api;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_updates' ) );
		add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 20, 3 );
	}

	/**
	 * @param        $result
	 * @param string $action
	 * @param null   $args
	 *
	 * @return object
	 */
	public function get_plugin_info( $result, $action = '', $args = null ) {

		// do nothing for unrelated requests
		if( $action !== 'plugin_information' || ! isset( $args->slug ) ) {
			return $result;
		}

		// only act on our plugins
		$plugin = $this->extensions->find( function( $p ) use($args) {
			return dirname( $p->slug() ) == $args->slug;
		});

		if( $plugin ) {
			$update_info = $this->get_update_info( $plugin );
			return $update_info;
		}

		return $result;
	}

	/**
	 * @param object $updates
	 * @return object
	 */
	public function add_updates( $updates ) {

		if( empty( $updates )
		    || ! isset( $updates->response )
			|| ! is_array( $updates->response ) ) {
			return $updates;
		}

		$available_updates = $this->fetch_updates();

		// any other updates?
		$updates->response = array_merge( $updates->response, $available_updates );


		return $updates;
	}

	/**
	 * Fetch array of available updates from remote server
	 *
	 * @return array
	 */
	protected function fetch_updates() {

		if( is_array( $this->available_updates ) ) {
			return $this->available_updates;
		}

		// fetch remote info
		$remote_plugins = $this->api->get_plugins( $this->extensions );

		// start with an empty array
		$this->available_updates = array();

		// did we get a valid response?
		if( ! is_array( $remote_plugins  ) ) {
			return $this->available_updates;
		}

		// find new versions
		foreach( $remote_plugins as $remote_plugin ) {

			// find corresponding local plugin
			$plugin = $this->extensions->find(
				function( $p ) use( $remote_plugin ){
					return $p->id() == $remote_plugin->id;
				}
			);

			// check versions
			if( ! $plugin || version_compare( $plugin->version(), $remote_plugin->version, '>=' ) ) {
				continue;
			}

			// add some dynamic data
			$this->available_updates[ $plugin->slug() ] = $this->format_response( $plugin, $remote_plugin );
		}

		return $this->available_updates;

	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return null
	 */
	public function get_update_info( iPlugin $plugin ) {
		$available_updates = $this->fetch_updates();

		if( isset( $available_updates[ $plugin->slug() ] ) ) {
			return $available_updates[ $plugin->slug() ];
		}

		return null;
	}

	/**
	 * @param iPlugin $plugin
	 * @param         $response
	 *
	 * @return mixed
	 */
	protected function format_response( iPlugin $plugin, $response ) {
		$response->new_version = $response->version;
		$response->slug = dirname( $plugin->slug() );
		$response->plugin = $plugin->slug();

		$response->sections = get_object_vars( $response->sections );
		$response->banners = get_object_vars( $response->banners );

		// load license
		$this->license->load();

		// add some notices if license is inactive
		if( ! $this->license->activated ) {
			$response->upgrade_notice = sprintf( 'You will need to <a href="%s">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=scroll-triggered-box&page=stb-settings' ) );
			$response->sections->changelog = '<p>' . sprintf( 'You will need to <a href="%s" target="_top">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=scroll-triggered-box&page=stb-settings' ) ) . '</p>' . $data->sections->changelog;
			$response->package = null;
		}

		return $response;
	}

}