<?php

namespace Boxzilla\Admin;

class Notices {

	/**
	 * @var array
	 */
	protected $notices = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show' ) );
	}

	/**
	 * @param $message
	 * @param $type
	 *
	 * @return $this
	 */
	public function add( $message, $type = 'updated' ) {
		$this->notices[] = array(
			'message' => $message,
			'type' => $type,
		);

		return $this;
	}

	/**
	 * Output the registered notices
	 */
	public function show() {
		foreach( $this->notices as $notice ) {
			echo sprintf( '<div class="notice notice-%s"><p>%s</p></div>', $notice['type'], $notice['message'] );
		}
	}
}