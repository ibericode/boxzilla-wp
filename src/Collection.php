<?php

namespace ScrollTriggeredBoxes;

class Collection implements \Iterator, \Countable {

	protected $elements = array();
	private $position = 0;

	public function __construct( array $elements ) {
		$this->elements = $elements;
		$this->position = 0;
	}

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return $this->elements[ $this->position ];
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset( $this->elements[ $this->position ] );
	}

	/**
	 * @param $callback
	 *
	 * @return array
	 */
	function map($callback) {
		$result = array();

		foreach( $this->elements as $element ) {
			$result[] = $callback( $element );
		}

		return $result;
	}

	/**
	 * @param $callback
	 *
	 * @return null
	 */
	function find($callback) {

		foreach( $this->elements as $element ) {
			if( $callback( $element ) ) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count() {
		return count( $this->elements );
	}
}