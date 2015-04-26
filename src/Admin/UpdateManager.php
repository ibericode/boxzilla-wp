<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Collection;
use ScrollTriggeredBoxes\iPlugin,
	ScrollTriggeredBoxes\Plugin;;

class UpdateManager {

	protected $extensions;

	protected $notices;

	protected $api;

	protected $responses = array();

	protected $license;

	/**
	 * @param Collection $extensions
	 * @param Notices    $notices
	 */
	public function __construct( Collection $extensions, Notices $notices, License $license ) {

		$this->extensions = $extensions;
		$this->notices = $notices;
		$this->license = $license;

		if( count( $this->extensions ) > 0 ) {
			$this->license->load();
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_updates' ) );
			add_filter('plugins_api', array($this, 'add_plugin_info'), 20, 3 );
		}

	}

	/**
	 * @param        $result
	 * @param string $action
	 * @param null   $args
	 *
	 * @return object
	 */
	public function add_plugin_info( $result, $action = '', $args = null ) {

		// do nothing for unrelated requests
		if( $action !== 'plugin_information' || ! isset( $args->slug ) ) {
			return $result;
		}

		// only act on our plugins
		$plugin = $this->extensions->find( function( $p ) use($args) {
				return dirname( $p->slug() ) == $args->slug;
		});

		if( $plugin ) {

			// get plugin info from remote API
			$info = $this->get_plugin_info( $plugin );

			if( $info ) {
				return $info;
			}
		}

		return $result;
	}

	/**
	 * @param object $updates
	 * @return object
	 */
	public function add_updates( $updates ) {

		foreach( $this->extensions as $plugin ) {

			if( $this->has_new_version( $plugin ) ) {
				$updates->response[ $plugin->slug() ] = $this->get_plugin_info( $plugin );
			}
		}

		return $updates;
	}

	/**
	 * @param iPlugin $plugin
	 * @return object
	 */
	protected function get_plugin_info( iPlugin $plugin ) {

		if( ! isset( $this->responses[ $plugin->slug() ] ) ) {
			$data = $this->api()->get_plugin_info( $plugin );

			if( ! $data ) {
				$this->responses[ $plugin->slug() ] = null;
				return null;
			}

			// add some notices if license is inactive
			if( ! $this->license->is_activated() ) {
				$data->upgrade_notice = sprintf( 'You will need to <a href="%s">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=scroll-triggered-box&page=stb-settings' ) );
				$data->sections->changelog = '<p>' . sprintf( 'You will need to <a href="%s" target="_top">activate your license</a> to install this plugin update.', admin_url( 'edit.php?post_type=scroll-triggered-box&page=stb-settings' ) ) . '</p>' . $data->sections->changelog;
				$data->package = null;
			}

			$data->sections = get_object_vars( $data->sections );
			$data->banners = get_object_vars( $data->banners );

			$this->responses[ $plugin->slug() ] = $data;
		}

		return $this->responses[ $plugin->slug() ];
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	protected function has_new_version( iPlugin $plugin ) {
		$data = $this->get_plugin_info( $plugin );
		return isset( $data->new_version ) && version_compare( $plugin->version(), $data->new_version, '<' );
	}

	/**
	 * @return APIConnector
	 */
	protected function api() {
		$plugin = Plugin::instance();
		return $plugin['api_connector'];
	}

}