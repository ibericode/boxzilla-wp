<?php

namespace ScrollTriggeredBoxes;

class PluginCollection extends Collection {

	/**
	 * Find a given plugin by ID
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function find( $id ) {

		foreach( $this->elements as $element ) {
			if( $element->id() == $id ) {
				return $element;
			}
		}

		return false;
	}

}