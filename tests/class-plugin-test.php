<?php

namespace Boxzilla\Tests;

use Boxzilla\Plugin;
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

    /**
     * @covers \Boxzilla\Plugin::id
     */
    public function test_id() {
        self::assertEquals( $this->instance->id(), 'id' );
    }

    /**
     * @covers \Boxzilla\Plugin::slug
     */
    public function test_slug() {
        self::assertEquals( $this->instance->slug(), 'file' );
    }

    /**
     * @covers \Boxzilla\Plugin::name
     */
    public function test_name() {
        self::assertEquals( $this->instance->name(), 'name' );
    }

    /**
     * @covers \Boxzilla\Plugin::version
     */
    public function test_version() {
        self::assertEquals( $this->instance->version(), 'version' );
    }

    /**
     * @covers \Boxzilla\Plugin::file
     */
    public function test_file() {
        self::assertEquals( $this->instance->file(), 'file' );
    }

    /**
     * @covers \Boxzilla\Plugin::dir
     */
    public function test_dir() {
        self::assertEquals( $this->instance->dir(), 'dir' );
    }

    /**
     * @covers \Boxzilla\Plugin::url
     */
    public function test_url() {
        Functions::when('plugins_url')->returnArg();
        self::assertEquals( $this->instance->url(), '' );

        Functions::when('plugins_url')->returnArg();
        self::assertEquals( $this->instance->url('/here'), '/here' );
    }

}