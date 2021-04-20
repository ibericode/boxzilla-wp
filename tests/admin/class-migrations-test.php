<?php

namespace Boxzilla\Tests\Admin;

use Boxzilla\Admin\Migrations;
use PHPUnit_Framework_TestCase;

/**
 * Class MigrationsTest
 *
 * @ignore
 */
class MigrationsTest extends \PHPUnit\Framework\TestCase {

    private $dir = '/tmp/boxzilla-tests/migrations';

    /**
     * Create the sample migrations directory
     */
    public function setUp() : void {
        if( ! file_exists( $this->dir ) ) {
            mkdir( $this->dir, 0700, true );
        }
    }

    /**
     * @covers \Boxzilla\Admin\Migrations::find_migrations
     */
    public function test_find_migrations() {
        $instance = new Migrations( '1.0', '1.1', $this->dir );
        self::assertEquals( $instance->find_migrations(), array() );

        // create correct migration file
        $migration_file =  $this->dir . '/1.1-do-something.php';
        file_put_contents( $migration_file, '' );
        self::assertEquals( $instance->find_migrations(), array( $migration_file ) );

        // create incorrect migrations file
        $older_migration_file =  $this->dir . '/1.0-do-something.php';
        file_put_contents( $older_migration_file, '' );
        self::assertEquals( $instance->find_migrations(), array( $migration_file ) );
    }

    /**
     * Remove files after each test.
     */
    public function tearDown() : void {
        array_map( 'unlink', glob( $this->dir . '/*.php' ) );
        if( file_exists( $this->dir ) ) {
            rmdir( $this->dir );
        }
    }


}
