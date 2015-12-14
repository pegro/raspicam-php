<?php

namespace Cvuorinen\Raspicam\Test;

use AdamBrett\ShellWrapper\Command\CommandInterface;
use Cvuorinen\Raspicam\Raspivid;

class RaspividTest extends RaspicamTest
{
    /**
     * @var Raspivid
     */
    protected $camera;

    public function setUp()
    {
        $this->commandRunner = $this->getMock('AdamBrett\ShellWrapper\Runners\Exec');

        $this->camera = new Raspivid();

        $this->camera->setCommandRunner(
            $this->commandRunner
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute($filename = 'foo.h264')
    {
        $this->camera->recordVideo($filename, 5);
    }

    public function testRecordVideoThrowsExceptionOnEmptyFilename()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->recordVideo('', 5);
    }

    public function testRecordVideoExecutesCommand()
    {
        $this->expectCommandContains(Raspivid::COMMAND);

        $this->execute();
    }

    public function testRecordVideoSetsOutputAndTimeoutArguments()
    {
        $filename = 'foo.h264';
        $length = 5;

        $this->expectCommandContains(
            [
                "--output '" . $filename . "'",
                "--timeout '" . ($length * 1000) . "'"
            ]
        );

        $this->camera->recordVideo($filename, $length);
    }

    /**
     * @dataProvider validResolutionProvider
     */
    public function testWidthSetsCorrectArgument($width)
    {
        $this->expectCommandContains("--width '" . $width . "'");

        $this->camera->width($width);
        $this->execute();
    }

    /**
     * @dataProvider invalidResolutionProvider
     */
    public function testInvalidWidthThrowsException($width)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->width($width);
    }

    /**
     * @dataProvider validResolutionProvider
     */
    public function testHeightSetsCorrectArgument($height)
    {
        $this->expectCommandContains("--height '" . $height . "'");

        $this->camera->height($height);
        $this->execute();
    }

    /**
     * @dataProvider invalidResolutionProvider
     */
    public function testInvalidHeightThrowsException($height)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->height($height);
    }

    /**
     * @return array
     */
    public function validResolutionProvider()
    {
        return [
            [64],
            [600],
            [1080],
        ];
    }

    /**
     * @return array
     */
    public function invalidResolutionProvider()
    {
        return [
            [0],
            [1],
            [15],
            [2592],
            [5000],
            [25000],
            [-999],
            [100.5],
            [-12.0],
            [false],
            [null],
            ['foo'],
        ];
    }

    public function testConstructorArraySetsCorrectArguments()
    {
        $options = [
            'flip' => true,
            'width' => 600,
            'height' => 400,
        ];

        $expectedArguments = [
            '--vflip',
            '--hflip',
            '--width',
            '--height',
        ];

        $raspistill = new Raspivid($options);

        $raspistill->setCommandRunner(
            $this->commandRunner
        );

        $this->expectCommandContains($expectedArguments);

        $raspistill->recordVideo('foo.h264', 5);
    }

    public function testFluentInterfaceSetsCorrectArguments()
    {
        $expectedArguments = [
            '--vflip',
            '--hflip',
            '--width',
            '--height',
        ];

        $this->expectCommandContains($expectedArguments);

        $this->camera->flip()
            ->width(600)
            ->height(400)
            ->recordVideo('foo.h264', 5);
    }
}
