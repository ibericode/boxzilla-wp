<?php

namespace Boxzilla;

use Iterator;
use Countable;
use ArrayAccess;

class Collection implements Iterator, Countable, ArrayAccess {

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

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset)
	{
		return isset( $this->elements[ $offset ] );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset)
	{
		return $this->elements[ $offset ];
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value)
	{
		$this->elements[ $offset ] = $value;
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset)
	{
		unset( $this->elements[ $offset] );
	}

	/**
	 * Return a random value out of the collection.
	 *
	 * @return mixed
	 */
	public function random() {
		$key = array_rand( $this->elements );
		return $this->elements[ $key ];
	}
}