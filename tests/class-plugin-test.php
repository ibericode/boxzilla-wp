<?php

namespace Boxzilla\Tests;

use Boxzilla\Plugin;
use Brain\Monkey;
use Brain\Monkey\Functions;

class PluginTest extends WP_Test_Case {

    /**
     * @var Plugin
     */
    private $instance;

    public function setUp() {
        parent::setUp();

        Functions::when('plugin_basename')->returnArg();
        $this->instance = new Plugin( 'id', 'name', 'version', 'file', 'dir' );
    }

    public function test_id() {
        self::assertEquals( $this->instance->id(), 'id' );
    }

    public function test_name() {
        self::assertEquals( $this->instance->name(), 'name' );
    }

    public function test_version() {
        self::assertEquals( $this->instance->version(), 'version' );
    }

    public function test_file() {
        self::assertEquals( $this->instance->file(), 'file' );
    }

    public function test_dir() {
        self::assertEquals( $this->instance->dir(), 'dir' );
    }

    public function test_url() {
        Functions::when('plugins_url')->returnArg();
        self::assertEquals( $this->instance->url(), '' );

        Functions::when('plugins_url')->returnArg();
        self::assertEquals( $this->instance->url('/here'), '/here' );
    }

}