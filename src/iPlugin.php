<?php

namespace ScrollTriggeredBoxes;

interface iPlugin {

	/**
	 * @return int
	 */
	public function id();

	/**
	 * @return string
	 */
	public function slug();

	/**
	 * @return string
	 */
	public function name();

	/**
	 * @return string
	 */
	public function version();

	/**
	 * @return string
	 */
	public function file();

	/**
	 * @return string
	 */
	public function dir();

	/**
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function url( $path = '' );
}