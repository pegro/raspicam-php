<?php

namespace Cvuorinen\Raspicam\Test;

use AdamBrett\ShellWrapper\Command\Builder;
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
    private $commandBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $commandRunner;


    public function setUp()
    {
        $this->commandBuilder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->commandRunner = $this->getMock(Exec::class);

        $this->raspistill = new Raspistill();

        $this->raspistill->setCommandBuilder(
            $this->commandBuilder
        );

        $this->raspistill->setCommandRunner(
            $this->commandRunner
        );
    }

    public function testTakePictureThrowsExceptionOnEmptyFilename()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::SUCCESS);

        $this->setExpectedException(\InvalidArgumentException::class);

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

        $this->commandBuilder
            ->expects($this->once())
            ->method('addArgument')
            ->with($this->equalTo('output'), $this->equalTo($filename));

        $picture = $this->raspistill->takePicture($filename);

        $this->assertTrue($picture);
    }

    public function testFailedCommandThrowsException()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::GENERAL_ERROR);

        $this->setExpectedException(
            CommandFailedException::class
        );

        $this->raspistill->takePicture('foo.jpg');
    }

    public function testFailedCommandExceptionMessage()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::COMMAND_NOT_FOUND);

        $this->setExpectedException(
            CommandFailedException::class,
            'Command not found'
        );

        $this->raspistill->takePicture('foo.jpg');
    }
}
