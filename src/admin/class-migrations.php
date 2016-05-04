<?php

namespace Boxzilla\Admin;

use Exception;

/**
 *
 */
class Migrations {

    /**
     * @var float
     */
    protected $version_from = 0;

    /**
     * @var float
     */
    protected $version_to = 0;

    /**
     * @var string
     */
    protected $migrations_dir = '';

    /**
     * @param float $from
     * @param float $to
     * @param string $migrations_dir
     */
    public function __construct( $from, $to, $migrations_dir ) {
        $this->version_from = $from;
        $this->version_to = $to;
        $this->migrations_dir = $migrations_dir;
    }

    /**
     * Run the various upgrade routines, all the way up to the latest version
     */
    public function run() {
        $migrations = $this->find_migrations();
        // run in sub-function for scope
        array_map( array( $this, 'run_migration' ), $migrations );
    }

    /**
     * @return array
     */
    public function find_migrations() {
        $files = glob( rtrim( $this->migrations_dir, '/' ) . '/*.php' );
        $migrations =  array();

        foreach( $files as $file ) {
            $migration = basename( $file );
            $parts = explode( '-', $migration );
            $version = $parts[0];

            if( version_compare( $this->version_from, $version, '<' ) ) {
                $migrations[] = $file;
            }
        }

        return $migrations;
    }

    /**
     * Include a migration file and runs it.
     *
     * @param string $file
     *
     * @throws Exception
     */
    protected function run_migration( $file ) {

        if( ! file_exists( $file ) ) {
            throw new Exception( "Migration file $file does not exist.");
        }

        include $file;
    }
}