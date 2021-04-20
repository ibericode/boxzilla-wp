<?php

namespace Boxzilla\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

class WP_Test_Case extends TestCase {

    protected function setUp() : void
    {
        parent::setUp();
        Monkey::setUpWP();
    }

    protected function tearDown()  : void
    {
        Monkey::tearDownWP();
        parent::tearDown();
    }

}
