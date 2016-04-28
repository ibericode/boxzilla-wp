<?php

defined( 'ABSPATH' ) or exit;

/**
 * @return Boxzilla\Plugin
 */
function boxzilla() {
    static $instance;

    if( is_null( $instance ) ) {

        $classname =  'Boxzilla\\Plugin';
        $id = 0;
        $file = BOXZILLA_FILE;
        $dir = dirname( $file );
        $name = 'Boxzilla';
        $version = BOXZILLA_VERSION;

        $instance = new $classname(
            $id,
            $name,
            $version,
            $file,
            $dir
        );
    }

    return $instance;
}

/**
 * @deprecated 2.3
 * @return Boxzilla\Plugin
 */
function scroll_triggered_boxes() {
    _deprecated_function( __FUNCTION__, '2.3', 'boxzilla' );
    return boxzilla();
}
