<?php

namespace ScrollTriggeredBoxes\Licensing;

class Authenticator {

	/**
	 * @var string
	 */
	protected $api_url;

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * Add the necessary Auth headers to all requests to our API
	 *
	 * @param         $api_url
	 * @param License $license
	 */
	public function __construct( $api_url, License $license ) {
		$this->license = $license;
		$this->api_url = $api_url;
	}

	/**
	 * Initialise the awesome
	 */
	public function add_hooks() {
		add_filter( 'http_request_args', array( $this, 'add_auth_headers' ), 10, 2 );
	}

	/**
	 * @param $args
	 * @param $url
	 *
	 * @return mixed
	 */
	public function add_auth_headers( $args, $url ) {

		if( strpos( $url, $this->api_url ) !== 0 ) {
			return $args;
		}

		$this->license->load();

		if( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$args['headers']['Authorization'] = 'Basic ' . base64_encode( urlencode( $this->license->site ) . ':' . urlencode( $this->license->key ) );
		return $args;
	}
}