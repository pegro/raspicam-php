<?php

namespace Cvuorinen\Raspicam\Test;

use Cvuorinen\Raspicam\Raspistill;
use PHPUnit_Framework_TestCase;

class RaspistillTest extends PHPUnit_Framework_TestCase
{
    private $raspistill;

    public function setUp()
    {
        $this->raspistill = new Raspistill();
    }

    public function testTakePicture()
    {
        $picture = $this->raspistill->takePicture();

        $this->assertEquals('pic', $picture);
    }
}
