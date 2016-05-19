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

/**
 * @return string
 */
function boxzilla_get_section() {
    if( is_admin() ) {
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return 'ajax';
        } else {
            return 'admin';
        }
    } else {
        if( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return 'cron';
        } else if( defined( 'WP_CLI' ) && WP_CLI ) {
            return 'cli';
        } else {
            return 'public';
        }
    }
}