<?php

namespace Boxzilla\Tests;

use Brain\Monkey;
use PHPUnit_Framework_TestCase;

class WP_Test_Case extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
        parent::setUp();
        Monkey::setUpWP();
    }

    protected function tearDown()
    {
        Monkey::tearDownWP();
        parent::tearDown();
    }

}