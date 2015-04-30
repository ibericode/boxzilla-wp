<?php

namespace ScrollTriggeredBoxes\Licensing;

use ScrollTriggeredBoxes\Admin\Notices;
use ScrollTriggeredBoxes\Collection;
use ScrollTriggeredBoxes\iPlugin;

class API {

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
	 * @param string        $api_url
	 * @param Notices       $notices
	 */
	public function __construct( $api_url, Notices $notices ) {
		$this->api_url = $api_url;
		$this->notices = $notices;
	}

	/**
	 * Logs the current site in to the remote API
	 *
	 * @return bool
	 */
	public function login() {
		$endpoint = '/login';
		$args = array(
			'method' => 'POST'
		);
		return $this->call( $endpoint, $args );
	}

	/**
	 * Logs the current site out of the remote API
	 *
	 * @return bool
	 */
	public function logout() {
		$endpoint = '/logout';
		return $this->call( $endpoint );
	}

	/**
	 * @param iPlugin $plugin
	 * @return object
	 */
	public function get_plugin( iPlugin $plugin ) {
		$endpoint = sprintf( '/plugins/%d', $plugin->id() );
		return $this->call( $endpoint );
	}

	/**
	 * @param Collection $plugins
	 * @return object
	 */
	public function get_plugins( Collection $plugins ) {
		// create array of plugin ID's
		$plugin_ids = $plugins->map(
			function( $p ) { return $p->id(); }
		);

		$endpoint = add_query_arg( array( 'ids' => implode(',', $plugin_ids ) ), '/plugins' );
		return $this->call( $endpoint );
	}

	/**
	 * @param string $endpoint
	 * @param array $args
	 * @return object
	 */
	public function call( $endpoint,$args = array() ) {

		$request = wp_remote_request( $this->api_url . $endpoint, $args );

		// test for wp errors
		if( is_wp_error( $request ) ) {
			$this->notices->add( $request->get_error_message(), 'error' ); ;
			return false;
		}

		// retrieve response body
		$body = wp_remote_retrieve_body( $request );
		$response = json_decode( $body );
		if( ! is_object( $response ) ) {
			$this->notices->add( __( "The Scroll Triggered Boxes server returned an invalid response.", 'scroll-triggered-boxes' ), 'error' );
			return false;
		}

		// did remote API return a message?
		if( isset( $response->message ) ) {
			$this->notices->add( $response->message, ( $response->success ) ? 'success' : 'info' );
		}

		// store response
		$this->last_response = $response;

		// return response data
		if( $response && $response->success ) {

			if( isset( $response->data ) ) {
				return $response->data;
			}

			return true;
		}

		return false;
	}

	/**
	 * @return object|null
	 */
	public function get_last_response() {
		return $this->last_response;
	}

}