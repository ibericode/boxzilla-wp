<?php

namespace Boxzilla\Licensing;

use Boxzilla\Collection,
	Boxzilla\Plugin,
	Boxzilla\Admin\Notices;

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
	public function hook() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_updates' ) );
		add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 20, 3 );
		add_filter( 'http_request_args', array( $this, 'add_auth_headers' ), 10, 2 );
	}

	/**
	 * This adds the license key header to download package requests.
	 *
	 * @param array $args
	 * @param string $url
	 *
	 * @return mixed
	 */
	public function add_auth_headers( $args, $url ) {
		// only act on download request's
		if( strpos( $url, $this->api->url ) !== 0 || strpos( $url, 'download' ) === false ) {
			return $args;
		}

		// only add if activation key not empty
		if( empty( $this->license->activation_key ) ) {
			return $args;
		}	

		if( empty( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$args['headers']['Authorization'] = 'Bearer ' . urlencode( $this->license->activation_key );
		return $args;
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

		// was it a plugin of ours?
		if( $plugin ) {
			return $this->get_update_info( $plugin );
		}

		return $result;
	}

	/**
	 * @param object $updates
	 * @return object
	 */
	public function add_updates( $updates ) {

		// do nothing if no plugins registered
		if( count( $this->extensions ) === 0 ) {
			return $updates;
		}

		// failsafe WP bug
		if( empty( $updates )
		    || empty( $updates->response )
			|| ! is_array( $updates->response ) ) {
			return $updates;
		}

		// fetch available updates
		$available_updates = $this->fetch_updates();

		// merge with other updates
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

        // don't try if we failed a request recently.
        $failed_at = get_transient( 'boxzilla_request_failed' );
        if( ! empty( $failed_at ) && ( (strtotime('now') - 300) < $failed_at ) ) {
            return array();
        }

		// fetch remote info
		try {
			$remote_plugins = $this->api->get_plugins( $this->extensions );
		} catch( API_Exception $e ) {
            // set flag for 5 minutes
            set_transient( 'boxzilla_request_failed', strtotime('now'), 300 );
            return array();
		}
		
		// filter remote plugins, we only want the ones with an update available
		$this->available_updates = $this->filter_remote_plugins( $remote_plugins );

		return $this->available_updates;

	}

	/**
	 * @param $remote_plugins
	 * @return array
	 */
	protected function filter_remote_plugins( $remote_plugins ) {

		$available_updates = array();

		// find new versions
		foreach( $remote_plugins as $remote_plugin ) {

			// find corresponding local plugin
			/** @var Plugin $local_plugin */
			$local_plugin = $this->extensions->find(
				function( $local_plugin ) use( $remote_plugin ){
					/** @var Plugin $local_plugin */
					return $local_plugin->id() == $remote_plugin->sid;
				}
			);

			// plugin found and local plugin version not same as remote version?
			if( ! $local_plugin || version_compare( $local_plugin->version(), $remote_plugin->new_version, '>=' ) ) {
				continue;
			}

			// add some dynamic data
			$available_updates[ $local_plugin->slug() ] = $this->format_response( $local_plugin, $remote_plugin );
		}

		return $available_updates;
	}

	/**
	 * @param Plugin $plugin
	 *
	 * @return null
	 */
	public function get_update_info( Plugin $plugin ) {
		$available_updates = $this->fetch_updates();

		if( isset( $available_updates[ $plugin->slug() ] ) ) {
			return $available_updates[ $plugin->slug() ];
		}

		return null;
	}

	/**
	 * @param Plugin $plugin
	 * @param         $response
	 *
	 * @return mixed
	 */
	protected function format_response( Plugin $plugin, $response ) {
		$response->slug = dirname( $plugin->slug() );
		$response->plugin = $plugin->slug();

		// add some notices if license is inactive
		if( ! $this->license->activated ) {
			$response->upgrade_notice = sprintf( 'You will need to <a href="%s">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=boxzilla-box&page=boxzilla-settings' ) );
			$response->sections->changelog = '<p>' . sprintf( 'You will need to <a href="%s" target="_top">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=boxzilla-box&page=boxzilla-settings' ) ) . '</p>' . $response->sections->changelog;
			$response->package = null;
		} 

		// cast subkey objects to array as that is what WP expects
		$response->sections = get_object_vars( $response->sections );
		$response->banners = get_object_vars( $response->banners );

		return $response;
	}

}
