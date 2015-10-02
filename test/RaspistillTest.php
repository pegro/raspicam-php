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
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::SUCCESS);

        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->takePicture('');
    }

    public function testTakePictureExecutesCommand()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run');

        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::SUCCESS);

        $picture = $this->raspistill->takePicture('foo.jpg');

        $this->assertTrue($picture);
    }

    public function testTakePictureSetsOutputArgument()
    {
        $filename = 'foo.jpg';

        $this->expectCommandContains("--output '" . $filename . "'");

        $picture = $this->raspistill->takePicture($filename);

        $this->assertTrue($picture);
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
