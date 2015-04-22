<?php

namespace ScrollTriggeredBoxes;

class Collection implements \Iterator {

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
		return $this->elements[$this->position];
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->elements[$this->position]);
	}

}