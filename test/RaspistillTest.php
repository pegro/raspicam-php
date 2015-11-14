<?php

namespace Cvuorinen\Raspicam\Test;

use Cvuorinen\Raspicam\Raspicam;
use Cvuorinen\Raspicam\Raspistill;

class RaspistillTest extends RaspicamTest
{
    /**
     * @var Raspistill
     */
    protected $camera;

    public function setUp()
    {
        $this->commandRunner = $this->getMock('AdamBrett\ShellWrapper\Runners\Exec');

        $this->camera = new Raspistill();

        $this->camera->setCommandRunner(
            $this->commandRunner
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute($filename = 'foo')
    {
        $this->camera->takePicture($filename);
    }

    public function testTakePictureThrowsExceptionOnEmptyFilename()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->takePicture('');
    }

    public function testTakePictureExecutesCommand()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run');

        $this->camera->takePicture('foo.jpg');
    }

    public function testTakePictureSetsOutputArgument()
    {
        $filename = 'foo.jpg';

        $this->expectCommandContains("--output '" . $filename . "'");

        $this->camera->takePicture($filename);
    }

    /**
     * @dataProvider validIntegerBetweenZeroAndHundredProvider
     */
    public function testQualitySetsCorrectArgument($quality)
    {
        $this->expectCommandContains("--quality '" . $quality . "'");

        $this->camera->quality($quality);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenZeroAndHundredProvider
     */
    public function testInvalidQualityThrowsException($quality)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->quality($quality);
    }

    public function testRawSetsCorrectArgument()
    {
        $this->expectCommandContains('--raw');

        $this->camera->raw();
        $this->execute();
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testTimeoutSetsCorrectArgument($value, $unit, $expected)
    {
        $this->expectCommandContains("--timeout '" . $expected . "'");

        $this->camera->timeout($value, $unit);
        $this->execute();
    }

    public function testInvalidTimeoutThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->timeout(-2);
    }

    /**
     * @return array
     */
    public function timeoutProvider()
    {
        return [
            ['value' => 2, 'unit' => Raspicam::TIMEUNIT_SECOND, 'expected' => 2000],
            ['value' => 1.54, 'unit' => Raspicam::TIMEUNIT_SECOND, 'expected' => 1540],
            ['value' => 4000, 'unit' => Raspicam::TIMEUNIT_MILLISECOND, 'expected' => 4000],
            ['value' => 5000000, 'unit' => Raspicam::TIMEUNIT_MICROSECOND, 'expected' => 5000],
        ];
    }

    /**
     * @dataProvider validEncodingProvider
     */
    public function testEncodingSetsCorrectArgument($encoding)
    {
        $this->expectCommandContains("--encoding '" . $encoding . "'");

        $this->camera->encoding($encoding);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validEncodingProvider()
    {
        return [
            [Raspistill::ENCODING_JPG],
            [Raspistill::ENCODING_BMP],
            [Raspistill::ENCODING_GIF],
            [Raspistill::ENCODING_PNG],
        ];
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidEncodingThrowsException($encoding)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->encoding($encoding);
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
            [16],
            [600],
            [1600],
            [1920],
            [2592],
            [5000],
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
            [25000],
            [-999],
            [100.5],
            [-12.0],
            [false],
            [null],
            ['foo'],
        ];
    }

    /**
     * @dataProvider validExifProvider
     */
    public function testAddExifSetsCorrectArgument($tagName, $value)
    {
        $this->expectCommandContains("--exif '" . $tagName . "=" . $value . "'");

        $this->camera->addExif($tagName, $value);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validExifProvider()
    {
        return [
            ['EXIF.UserComment', 'testing'],
            ['EXIF.ExposureTime', 500],
            ['IDF0.Artist', 'Boris'],
            ['GPS.GPSAltitude', '1235/10'],
            ['GPS.GPSAltitude', '0'],
            ['GPS.GPSAltitude', 0],
            ['GPS.GPSAltitude', -1],
        ];
    }

    /**
     * @dataProvider invalidExifProvider
     */
    public function testInvalidAddExifThrowsException($tagName, $value)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->addExif($tagName, $value);
    }

    /**
     * @return array
     */
    public function invalidExifProvider()
    {
        return [
            ['EXIF.UserComment', ''],
            ['EXIF.UserComment', null],
            ['EXIF.UserComment', false],
            ['', 'foo'],
        ];
    }

    public function testAddExifThrowsExceptionWhenTooManyTags()
    {
        $this->setExpectedException('OverflowException');

        for ($i = 0; $i < 33; $i++) {
            $this->camera->addExif('EXIF.MakerNote' . $i, 'Testing');
        }
    }

    public function testAddExifSetsMultipleArguments()
    {
        $tags = [
            'IFD0.Artist' => 'Boris',
            'GPS.GPSAltitude' => '1235/10',
            'EXIF.MakerNote' => 'Testing',
        ];

        $expectedArgs = [];

        foreach ($tags as $tagName => $value) {
            $expectedArgs[] = "--exif '" . $tagName . "=" . $value . "'";

            $this->camera->addExif($tagName, $value);
        }

        $this->expectCommandContains($expectedArgs);

        $this->execute();
    }

    public function testSetExifSetsMultipleArguments()
    {
        $tags = [
            'IFD0.Artist' => 'Boris',
            'GPS.GPSAltitude' => '1235/10',
            'EXIF.MakerNote' => 'Testing',
        ];

        $expectedArgs = [];

        foreach ($tags as $tagName => $value) {
            $expectedArgs[] = "--exif '" . $tagName . "=" . $value . "'";
        }

        $this->camera->setExif($tags);

        $this->expectCommandContains($expectedArgs);

        $this->execute();
    }

    public function testDisableExifSetsCorrectArgument()
    {
        $this->expectCommandContains("--exif 'none'");

        $this->camera->disableExif();
        $this->execute();
    }

    public function testConstructorArraySetsCorrectArguments()
    {
        $options = [
            'flip' => true,
            'contrast' => 50,
            'ISO' => 500,
            'exposure' => Raspistill::EXPOSURE_NIGHT,
            'shutterSpeed' => 1.5
        ];

        $expectedArguments = [
            '--vflip',
            '--hflip',
            '--contrast',
            '--ISO',
            '--exposure',
            '--shutter',
        ];

        $raspistill = new Raspistill($options);

        $raspistill->setCommandRunner(
            $this->commandRunner
        );

        $this->expectCommandContains($expectedArguments);

        $raspistill->takePicture('foo.jpg');
    }

    public function testFluentInterfaceSetsCorrectArguments()
    {
        $expectedArguments = [
            '--vflip',
            '--hflip',
            '--contrast',
            '--ISO',
            '--exposure',
            '--shutter',
        ];

        $this->expectCommandContains($expectedArguments);

        $this->camera->flip()
            ->contrast(50)
            ->ISO(500)
            ->exposure(Raspistill::EXPOSURE_NIGHT)
            ->shutterSpeed(1.5)
            ->takePicture('foo.jpg');
    }

    public function testStartTimelapseExecutesCommand()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('run');

        $this->camera->startTimelapse('foo%04d.jpg', 5, 60);
    }

    public function testStartTimelapseSetsCorrectArguments()
    {
        $filename = 'foo%04d.jpg';
        $interval = 5;
        $length = 60;

        $expectedArguments = [
            "--output '" . $filename . "'",
            "--timelapse '" . ($interval * 1000) . "'",
            "--timeout '" . ($length * 1000) . "'",
        ];

        $this->expectCommandContains($expectedArguments);

        $this->camera->startTimelapse($filename, $interval, $length);
    }

    public function testStartTimelapseConvertsTimeUnits()
    {
        $filename = 'foo%04d.jpg';
        $interval = 1;
        $length = 60;
        $unit = Raspicam::TIMEUNIT_MINUTE;

        $expectedArguments = [
            "--output '" . $filename . "'",
            "--timelapse '" . ($interval * 1000 * 60) . "'",
            "--timeout '" . ($length * 1000 * 60) . "'",
        ];

        $this->expectCommandContains($expectedArguments);

        $this->camera->startTimelapse($filename, $interval, $length, $unit);
    }

    public function testStartTimelapseThrowsExceptionOnEmptyFilename()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->startTimelapse('', 5, 30);
    }

    public function testLinkLatestSetsCorrectArgument()
    {
        $filename = 'latest.jpg';
        $this->expectCommandContains("--latest '" . $filename . "'");

        $this->camera->linkLatest($filename);
        $this->execute();
    }

    public function testLinkLatestThrowsExceptionOnEmptyFilename()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->linkLatest('');
    }
}
