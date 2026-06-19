<?php

if (! defined('ABSPATH')) {
    exit;
}

use Boxzilla\Boxzilla;

/**
 * @return Boxzilla
 */
function boxzilla()
{
    static $instance;

    if ($instance === null) {
        $instance = new Boxzilla();
    }

    return $instance;
}
