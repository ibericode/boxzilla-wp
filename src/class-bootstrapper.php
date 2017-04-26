<?php

namespace Boxzilla;

use InvalidArgumentException;

/**
 * Class Bootstrapper
 * @package Boxzilla
 *
 * @method void admin( callable $callback )
 * @method void cron( callable $callback )
 * @method void front( callable $callback )
 * @method void ajax( callable $callback )
 * @method void cli( callable $callback )
 */
class Bootstrapper {

    /**
     * @var array
     */
    private $bootstrappers = array(
        'admin' => array(),
        'ajax' => array(),
        'cli' => array(),
        'cron' => array(),
        'front' => array(),
        'global' => array(),
    );

    /**
     * @param string $section
     * @param callable $callable
     */
    public function register( $section, $callable ) {

        if( ! isset( $this->bootstrappers[ $section ] ) ) {
            throw new InvalidArgumentException( "Section $section is invalid." );
        }

        if( ! is_callable( $callable ) ) {
            throw new InvalidArgumentException( 'Callable argument is not callable.' );
        }

        $this->bootstrappers[ $section ][] = $callable;
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call( $name, $arguments ) {
        if( isset( $this->bootstrappers[ $name ] ) ) {
            $this->register( $name, $arguments[0] );
        }
    }

    /**
     * Run registered bootstrappers
     *
     * @param string $section
     */
    public function run( $section = '' ) {

        if( ! $section ) {
            $section = $this->section();
        }

        // call all global callbacks
        foreach( $this->bootstrappers['global'] as $callback ) {
            $callback();
        }

        // call section specific callbacks
        foreach( $this->bootstrappers[ $section ] as $callback ) {
            $callback();
        }
    }

    /**
     * Get currently active section.
     *
     * @return string
     */
    public function section() {
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
                return 'front';
            }
        }
    }

}