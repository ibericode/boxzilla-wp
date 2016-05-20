<?php

namespace Boxzilla\Tests;

use Boxzilla\Bootstrapper;
use Brain\Monkey\Functions;

class Bootstrapper_Test extends WP_Test_Case {

    /**
     * @covers Bootstrapper::register
     */
    public function test_register() {
        $instance = new Bootstrapper();

        // invalid section
        self::setExpectedException( 'InvalidArgumentException' );
        $instance->register( 'foo', function() { });

        // valid
        $instance->register( 'admin', function() { });
    }

    /**
     * @covers Bootstrapper::run
     */
    public function test_run() {
        $instance = new Bootstrapper();
        $callable_front = new Test_Callable();
        $instance->register( 'front', array( $callable_front, 'call' ) );

        // front should not be run on admin
        $instance->run('admin');
        self::assertFalse( $callable_front->called );

        // now it should be run
        $instance->run( 'front' );
        self::assertTrue( $callable_front->called );

        // test with multiple registered callables
        $instance = new Bootstrapper();
        $callable_front = new Test_Callable();
        $callable_global = new Test_Callable();
        $callable_admin = new Test_Callable();
        $instance->register( 'front', array( $callable_front, 'call' ) );
        $instance->register( 'global', array( $callable_global, 'call' ) );
        $instance->register( 'admin', array( $callable_admin, 'call' ) );
        $instance->run( 'front' );
        self::assertTrue( $callable_front->called );
        self::assertTrue( $callable_global->called );
        self::assertFalse( $callable_admin->called );
    }

    /**
     * @covers Bootstrapper::section
     */
    public function test_section() {
        $instance = new Bootstrapper();

        Functions::expect('is_admin')->once()->andReturn(false);;
        self::assertEquals( $instance->section(), 'front' );

        Functions::expect('is_admin')->once()->andReturn(false);
        define('DOING_CRON', true);
        self::assertEquals( $instance->section(), 'cron' );

        Functions::expect('is_admin')->once()->andReturn(true);
        self::assertEquals( $instance->section(), 'admin' );

        Functions::expect('is_admin')->once()->andReturn(true);
        define('DOING_AJAX', true);
        self::assertEquals( $instance->section(), 'ajax' );
    }

    /**
     * @covers Bootstrapper::__call
     */
    public function test_magic_section_register() {
        $instance = new Bootstrapper();
        $callable = new Test_Callable();
        $instance->global(array( $callable, 'call' ) );
        $instance->run();
        self::assertTrue( $callable->called );
    }
}

class Test_Callable {
    public $called = false;

    public function call() {
        $this->called = true;
    }
}