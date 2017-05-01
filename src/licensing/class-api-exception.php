<?php

namespace Boxzilla\Licensing;

use Exception;

class API_Exception extends Exception {

	/**
	 * @var string
	 */
	protected $apiCode;

	/**
	 * API_Exception constructor.
	 *
	 * @param string $message
	 * @param string $apiCode (optional)
	 */
	public function __construct( $message, $apiCode = '' ) {
		parent::__construct( $message );

		$this->apiCode = $apiCode;
	}

	/**
	 * @return string
	 */
	public function getApiCode() {
		return $this->apiCode;
	}

}