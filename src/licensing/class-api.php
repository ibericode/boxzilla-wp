<?php

namespace Boxzilla\Licensing;

use Boxzilla\Admin\Notices;
use Boxzilla\Plugin;
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
	 * @param Notices   $notices
	 */
	public function __construct( $url, License $license, Notices $notices ) {
		$this->url = $url;
		$this->notices = $notices;
		$this->license = $license;
	}

	/**
	 * Logs the current site in to the remote API
	 *
	 * @return string|boolean
	 */
	public function activate_license() {

		// bail early if key empty
		if( empty( $this->license->key ) ) {
			return false;
		}

		$endpoint = '/license/activations';
		$data = array(
			'site_url' => $this->license->site
		);
		$result = $this->request( 'POST', $endpoint, $data );

		if( $result ) {
			$this->notices->add( $result->message, 'info' );
			return $result->key;
		}

		return false;
	}

	/**
	 * Logs the current site out of the remote API
	 *
	 * @return bool
	 */
	public function deactivate_license() {
		$endpoint = '/license/activations';
		$data = array(
			'site_url' => $this->license->site
		);
		$result = $this->request( 'DELETE', $endpoint, $data );

		if( $result ) {
			$this->notices->add( $result->message, 'info' );
			return true;
		}

		return false;
	}

	/**
	 * @param Plugin $plugin
	 * @return object
	 */
	public function get_plugin( Plugin $plugin ) {
		$endpoint = sprintf( '/plugins/%s?format=wp', $plugin->id() );
		return $this->request( 'GET', $endpoint );
	}

	/**
	 * @param Plugin[] $plugins
	 * @return object
	 */
	public function get_plugins( $plugins ) {
		// create array of plugin ID's
		$plugin_slugs = $plugins->map(function( $p ) { return $p->id(); });
		$endpoint = add_query_arg( array( 'sids' => implode(',', $plugin_slugs ), 'format' => 'wp' ), '/plugins' );
		return $this->request( 'GET', $endpoint );
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param array $data
	 * @return object
	 */
	public function request( $method, $endpoint, $data = array() ) {

		$url = $this->url . $endpoint;
		$args = array(
			'method' => $method,
			'headers' => array(
				'Authorization' => 'Bearer ' . urlencode( $this->license->key )
			),
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
			$this->notices->add( __( "The Boxzilla server returned an invalid response.", 'boxzilla' ), 'error' );
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