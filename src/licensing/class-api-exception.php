<?php

namespace Boxzilla\Licensing;

use Exception;

class API_Exception extends Exception {


	/**
	 * @var string
	 */
	protected $api_code;

	/**
	 * API_Exception constructor.
	 *
	 * @param string $message
	 * @param string $api_code (optional)
	 */
	public function __construct( $message, $api_code = '' ) {
		parent::__construct( $message );

		$this->api_code = $api_code;
	}

	/**
	 * @return string
	 */
	public function getApiCode() {
		return $this->api_code;
	}
}
