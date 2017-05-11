<?php

namespace Boxzilla\Licensing;

use Boxzilla\Plugin;
use Exception;
use WP_Error;

class API {

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * The API url
	 *
	 * @var string
	 */
	public $url = '';

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
	 * @param string    $url
	 * @param License 	$license
	 */
	public function __construct( $url, License $license ) {
		$this->url = $url;
		$this->license = $license;
	}

	/**
	 * Gets license status
	 *
	 * @return object
	 */
	public function get_license() {
		$endpoint = '/license';
		$response = $this->request( 'GET', $endpoint );
		return $response;
	}


	/**
	 * Logs the current site in to the remote API
	 *
	 * @return object
	 */
	public function activate_license() {
		$endpoint = '/license/activations';
		$args = array(
			'site_url' => get_option( 'siteurl' )
		);
		$response = $this->request( 'POST', $endpoint, $args );
		return $response;
	}

	/**
	 * Logs the current site out of the remote API
	 *
	 * @return object
	 */
	public function deactivate_license() {
		$endpoint = sprintf( '/license/activations/%s', $this->license->activation_key );
		$response = $this->request( 'DELETE', $endpoint );
		return $response;
	}

	/**
	 * @param Plugin $plugin
	 * @return object
	 */
	public function get_plugin( Plugin $plugin ) {
		$endpoint = sprintf( '/plugins/%s?format=wp', $plugin->id() );
		$response = $this->request( 'GET', $endpoint );
		return $response;
	}

	/**
	 * @param Plugin[] $plugins 	(optional)
	 * @return object
	 */
	public function get_plugins( $plugins = null ) {

		$args = array(
			'format' => 'wp',
		);

		$endpoint = add_query_arg( $args, '/plugins' );
		$response = $this->request( 'GET', $endpoint );
		return $response;
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param array $data
	 *
	 * @return object|array
	 */
	public function request( $method, $endpoint, $data = array() ) {

		$url = $this->url . $endpoint;
		$args = array(
			'method' => $method,
			'headers' => array(
			    'Content-Type' => 'application/json',
                'Accepts' => 'application/json',
            ),
		);

		// add license key to headers if set
		if( ! empty( $this->license->key ) ) {
			$args['headers']['Authorization'] = 'Bearer ' . urlencode( $this->license->key );
		}

		if( ! empty( $data ) ) {
            if( in_array( $method, array( 'GET', 'DELETE' ) ) ) {
                $url = add_query_arg( $data, $url );
            } else {
                $args['body'] = json_encode( $data );
            }
        }

		$response = wp_remote_request( $url, $args );
		return $this->parse_response( $response );
	}

	/**
	 * @param mixed $response
	 *
	 * @return object|null
	 *
	 * @throws API_Exception
	 */
	public function parse_response( $response ) {
		// test for wp errors (request failures)
        if( $response instanceof WP_Error) {
			throw new API_Exception( $response->get_error_message() );
		}

		// retrieve response body
		$body = wp_remote_retrieve_body( $response );
		if( empty( $body) ) {
		    return null;
        }

		$json = json_decode( $body, false );
		if( is_null( $json ) ) {
			throw new API_Exception( __( "The Boxzilla server returned an invalid response.", 'boxzilla' ) );
		}

		// did request return an error response?
        if( wp_remote_retrieve_response_code( $response ) >= 400 ) {
            throw new API_Exception( $json->message, $json->code );
        }

		// return actual response data
		return $json;
	}

}
