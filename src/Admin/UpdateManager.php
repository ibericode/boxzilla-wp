<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\Collection;
use ScrollTriggeredBoxes\iPlugin;

class UpdateManager {

	protected $extensions;

	protected $notices;

	protected $api;

	protected $responses = array();

	/**
	 * @param Collection $extensions
	 * @param Notices    $notices
	 */
	public function __construct( Collection $extensions, Notices $notices ) {

		$this->extensions = $extensions;
		$this->notices = $notices;

		if( count( $this->extensions ) > 0 ) {
			add_filter( 'site_transient_update_plugins', array( $this, 'add_plugin_info' ) );
		}


	}

	/**
	 * @param object $updates
	 * @return object
	 */
	public function add_plugin_info( $updates ) {

		foreach( $this->extensions as $plugin ) {

			if( ! isset( $this->responses[ $plugin->slug() ] ) ) {
				$this->responses[ $plugin->slug() ] =$this->api()->get_plugin_info( $plugin );
			}

			if( $this->has_new_version( $plugin ) ) {
				$updates->response[ $plugin->slug()] = $this->responses[ $plugin->slug() ];
			}
		}

		return $updates;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function has_new_version( iPlugin $plugin ) {
		$data = $this->responses[ $plugin->slug() ];
		return isset( $data->new_version ) && version_compare( $plugin->version(), $data->new_version, '<' );
	}

	/**
	 * @return LicenseAPI
	 */
	public function api() {

		if( is_null( $this->api ) ) {
			$this->api = new LicenseAPI( $this->notices );
		}

		return $this->api;
	}

}