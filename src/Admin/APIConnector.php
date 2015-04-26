<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\iPlugin;

class APIConnector {

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * @var Notices
	 */
	protected $notices;

	/**
	 * @var string
	 */
	protected $api_url = '';

	/**
	 * @var int
	 */
	protected $error_code = 0;

	/**
	 * @var string
	 */
	protected $error_message = '';

	/**
	 * @var
	 */
	protected $last_response;

	/**
	 * @param string $api_url
	 * @param Notices $notices
	 */
	public function __construct( $api_url, Notices $notices ) {
		$this->api_url = $api_url;
		$this->notices = $notices;
	}

	/**
	 * Active the license for the current site
	 *
	 * @param License $license
	 *
	 * @return bool
	 */
	public function activate( License $license ) {
		$endpoint = '/login';
		$args = array(
			'method' => 'POST'
		);
		$result = $this->call( $endpoint, $args );
		return is_object( $result ) && $result->success;
	}

	/**
	 * Deactivate the license for this site
	 *
	 * @param License $license
	 *
	 * @return bool
	 */
	public function deactivate( License $license ) {
		$endpoint = '/logout';
		$result = $this->call( $endpoint );
		return is_object( $result ) && $result->success;
	}

	/**
	 * @param iPlugin $plugin
	 * @return object
	 */
	public function get_plugin_info( iPlugin $plugin ) {
		$endpoint = sprintf( '/plugins/%d', $plugin->id() );
		$result = $this->call( $endpoint );

		if( is_object( $result ) && $result->success ) {
			return $result->data;
		}

		return null;
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
			$this->notices->add( $response->get_error_message(), 'error' ); ;
			return false;
		}

		// retrieve response body
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		if( ! is_object( $data ) ) {
			$this->notices->add( "Scroll Triggered Boxes failed to check for updates because no valid JSON object was returned.", 'error' );
			return false;
		}

		if( isset( $data->message ) ) {
			$this->notices->add( $data->message, ( $data->success ) ? 'success' : 'info' );
		}

		// store response
		$this->last_response = $data;

		return $data;
	}

	public function get_last_response() {
		return $this->last_response;
	}

}