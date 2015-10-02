<?php

namespace Cvuorinen\Raspicam\Test;

use AdamBrett\ShellWrapper\Command\CommandInterface;
use AdamBrett\ShellWrapper\ExitCodes;
use AdamBrett\ShellWrapper\Runners\Exec;
use Cvuorinen\Raspicam\CommandFailedException;
use Cvuorinen\Raspicam\Raspistill;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class RaspistillTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Raspistill
     */
    private $raspistill;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $commandRunner;


    public function setUp()
    {
        $this->commandRunner = $this->getMock('AdamBrett\ShellWrapper\Runners\Exec');

        $this->raspistill = new Raspistill();

        $this->raspistill->setCommandRunner(
            $this->commandRunner
        );
    }

    public function testTakePictureThrowsExceptionOnEmptyFilename()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->takePicture('');
    }

    public function testTakePictureExecutesCommand()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run');

        $this->raspistill->takePicture('foo.jpg');
    }

    public function testTakePictureSetsOutputArgument()
    {
        $filename = 'foo.jpg';

        $this->expectCommandContains("--output '" . $filename . "'");

        $this->raspistill->takePicture($filename);
    }

    public function testFailedCommandThrowsException()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::GENERAL_ERROR);

        $this->setExpectedException(
            'Cvuorinen\Raspicam\CommandFailedException'
        );

        $this->raspistill->takePicture('foo.jpg');
    }

    public function testFailedCommandExceptionMessage()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::COMMAND_NOT_FOUND);

        $this->setExpectedException(
            'Cvuorinen\Raspicam\CommandFailedException',
            'Command not found'
        );

        $this->raspistill->takePicture('foo.jpg');
    }

    public function testVerticalFlipSetsCorrectArgument()
    {
        $this->expectCommandContains('--vflip');

        $this->raspistill->verticalFlip();
        $this->raspistill->takePicture('foo.jpg');
    }

    public function testVerticalFlipArgumentNotSetWhenUnset()
    {
        $this->raspistill->verticalFlip();
        $this->raspistill->takePicture('foo.jpg');

        $this->expectCommandNotContains('--vflip');

        $this->raspistill->verticalFlip(false);
        $this->raspistill->takePicture('foo.jpg');
    }

    public function testHorizontalFlipSetsCorrectArgument()
    {
        $this->expectCommandContains('--hflip');

        $this->raspistill->horizontalFlip();
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testSharpnessSetsCorrectArgument($sharpness)
    {
        $this->expectCommandContains("--sharpness '" . $sharpness . "'");

        $this->raspistill->sharpness($sharpness);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidSharpnessThrowsException($sharpness)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->sharpness($sharpness);
    }

    /**
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testContrastSetsCorrectArgument($contrast)
    {
        $this->expectCommandContains("--contrast '" . $contrast . "'");

        $this->raspistill->contrast($contrast);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidContrastThrowsException($contrast)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->contrast($contrast);
    }

    /**
     * @dataProvider validIntegerBetweenZeroAndHundredProvider
     */
    public function testBrightnessSetsCorrectArgument($brightness)
    {
        $this->expectCommandContains("--brightness '" . $brightness . "'");

        $this->raspistill->brightness($brightness);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenZeroAndHundredProvider
     */
    public function testInvalidBrightnessThrowsException($brightness)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->brightness($brightness);
    }

    /**
     * @return array
     */
    public function validIntegerBetweenNegativeHundredAndHundredProvider()
    {
        return [
            [1],
            [99],
            [100],
            [0],
            [-1],
            [-99],
            [-100]
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegerBetweenNegativeHundredAndHundredProvider()
    {
        return [
            [999],
            [101],
            [-999],
            [-101],
            [5.5],
            [-12.0],
            [false],
            [null],
            ['foo']
        ];
    }

    /**
     * @return array
     */
    public function validIntegerBetweenZeroAndHundredProvider()
    {
        return [
            [1],
            [99],
            [100],
            [0],
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegerBetweenZeroAndHundredProvider()
    {
        return [
            [999],
            [101],
            [-1],
            [-101],
            [5.5],
            [-12.0],
            [false],
            [null],
            ['foo']
        ];
    }

    /**
     * @param string $string
     */
    private function expectCommandContains($string)
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run')
            ->with($this->callback(function (CommandInterface $command) use ($string) {
                $this->assertContains($string, (string) $command);

                return true;
            }));
    }

    /**
     * @param string $string
     */
    private function expectCommandNotContains($string)
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run')
            ->with($this->callback(function (CommandInterface $command) use ($string) {
                $this->assertNotContains($string, (string) $command);

                return true;
            }));
    }
}
