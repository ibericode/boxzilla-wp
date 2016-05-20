<?php

use Boxzilla\Boxzilla;

/**
 * @return Boxzilla
 */
function boxzilla() {
    static $instance;

    if( is_null( $instance ) ) {
       $instance = new Boxzilla();
    }

    return $instance;
}

