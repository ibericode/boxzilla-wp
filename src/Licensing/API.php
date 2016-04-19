<?php

namespace ScrollTriggeredBoxes\Licensing;

use ScrollTriggeredBoxes\Admin\Notices;
use ScrollTriggeredBoxes\Collection;
use ScrollTriggeredBoxes\iPlugin;
use WP_Error;

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
	 * @param License $license
	 * @return bool
	 */
	public function create_license_activation( License $license ) {
		$endpoint = '/license/activations';
		$data = array(
			'site_url' => $license->site
		);
		$result = $this->request( 'POST', $endpoint, $data );
		if( $result ) {
			$this->notices->add( $result->message, 'info' );
			return true;
		}

		return false;
	}

	/**
	 * Logs the current site out of the remote API
	 *
	 * @param License $license
	 * @return bool
	 */
	public function delete_license_activation( License $license ) {
		$endpoint = '/license/activations';
		$data = array(
			'site_url' => $license->site
		);
		$result = $this->request( 'DELETE', $endpoint, $data );

		if( $result ) {
			$this->notices->add( $result->message, 'info' );
			return true;
		}

		return false;
	}

	/**
	 * @param iPlugin $plugin
	 * @return object
	 */
	public function get_plugin( iPlugin $plugin ) {
		$endpoint = sprintf( '/plugins/%d?format=wp', $plugin->id() );
		return $this->request( 'GET', $endpoint );
	}

	/**
	 * @param Collection $plugins
	 * @return object
	 */
	public function get_plugins( Collection $plugins ) {
		// create array of plugin ID's
		$plugin_ids = $plugins->map(function( $p ) { return $p->id(); });
		$endpoint = add_query_arg( array( 'ids' => implode(',', $plugin_ids ), 'format' => 'wp' ), '/plugins' );
		return $this->request( 'GET', $endpoint );
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param array $data
	 * @return object
	 */
	public function request( $method, $endpoint, $data = array() ) {

		$url = $this->api_url . $endpoint;
		$args = array(
			'method' => $method
		);

		if( in_array( $method, array( 'GET', 'DELETE' ) ) ) {
			$url = add_query_arg( $data, $url );
		} else {
			$args['body'] = $data;
		}

		$request = wp_remote_request( $url, $args );

		// test for wp errors
		if( $request instanceof WP_Error) {
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

		// store response
		$this->last_response = $response;

		// did request return an error response?
		if( isset( $response->error ) ) {
			$this->notices->add( $response->error->message, 'error' );
			return null;
		}

		// return actual response data
		return $response->data;
	}

	/**
	 * @return object|null
	 */
	public function get_last_response() {
		return $this->last_response;
	}

}