<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\iPlugin;

class API {

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * @var string
	 */
	protected $api_url = 'http://local.stb.com/api';

	/**
	 * @param License $license
	 */
	public function __construct( License $license ) {
		$this->license = $license;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function activate_license( iPlugin $plugin ) {

		$args = array(
			'method' => 'POST',
			'body' => array(
				'url' => $this->license->site
			)
		);

		$endpoint = sprintf( '/licenses/%s/activations/%d' , $this->license->key, $plugin->id() );
		$result = $this->call( $endpoint, $args );
		return $result && $result->success;
	}

	/**
	 * @param iPlugin $plugin
	 *
	 * @return bool
	 */
	public function deactivate_license( iPlugin $plugin) {

		$endpoint = sprintf( '/licenses/%s/activations/%d' , $this->license->key, $plugin->id() );
		$args = array(
			'method' => 'POST',
			'body' => array(
				'url' => $this->license->site,
				'_method' => 'DELETE'
			)
		);

		$result = $this->call( $endpoint, $args );
		return $result && $result->success;
	}

	/**
	 * @param iPlugin $plugin
	 */
	public function get_plugin_info( iPlugin $plugin ) {
		// todo
	}

	/**
	 * @param string $endpoint
	 * @param array $args
	 * @return object
	 */
	public function call( $endpoint,$args = array() ) {

		$response = wp_remote_request( $this->api_url . $endpoint, $args );

		// test for wp errors
		if( is_wp_error( $response ) ) {
			return false;
		}

		// retrieve response body
		$body = wp_remote_retrieve_body( $response );
		$response = json_decode( $body );

		if( ! is_object( $response ) ) {
			return false;
		}

		return $response;
	}

}