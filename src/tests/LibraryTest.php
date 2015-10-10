<?php

namespace Mock;

require_once 'vendor/autoload.php';

class LibraryTest extends \PHPUnit_Framework_TestCase
{
    protected $library;

    protected function setUp()
    {
        $this->library = new Libraries\Mock;
    }

    function testSetApp()
    {
        $app = new App(new View, 'Mock', __DIR__);

        $this->assertEquals($this->library->setApp($app), $this->library);
    }
}
