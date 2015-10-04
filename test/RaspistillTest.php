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

    public function testGetOutputReturnCommandOutput()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn('some-output');

        $this->raspistill->takePicture('foo.jpg');

        $this->assertEquals(
            'some-output',
            $this->raspistill->getOutput()
        );
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

    public function testFlipSetsBothVerticalAndHorizontalArguments()
    {
        $this->expectCommandContains(['--vflip', '--hflip']);

        $this->raspistill->flip();
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
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testSaturationSetsCorrectArgument($saturation)
    {
        $this->expectCommandContains("--saturation '" . $saturation . "'");

        $this->raspistill->saturation($saturation);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidSaturationThrowsException($saturation)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->saturation($saturation);
    }

    /**
     * @dataProvider validIntegerBetweenHundredAndEightHundredProvider
     */
    public function testISOSetsCorrectArgument($iso)
    {
        $this->expectCommandContains("--ISO '" . $iso . "'");

        $this->raspistill->ISO($iso);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenHundredAndEightHundredProvider
     */
    public function testInvalidISOThrowsException($iso)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->ISO($iso);
    }

    /**
     * @dataProvider validIntegerBetweenNegativeTenAndTenProvider
     */
    public function testExposureCompensationSetsCorrectArgument($ev)
    {
        $this->expectCommandContains("--ev '" . $ev . "'");

        $this->raspistill->exposureCompensation($ev);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeTenAndTenProvider
     */
    public function testInvalidExposureCompensationThrowsException($ev)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->exposureCompensation($ev);
    }

    /**
     * @dataProvider validExposureProvider
     */
    public function testExposureSetsCorrectArgument($exposure)
    {
        $this->expectCommandContains("--exposure '" . $exposure . "'");

        $this->raspistill->exposure($exposure);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidExposureThrowsException($exposure)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->exposure($exposure);
    }

    /**
     * @dataProvider validIntegerBetweenZeroAndHundredProvider
     */
    public function testQualitySetsCorrectArgument($quality)
    {
        $this->expectCommandContains("--quality '" . $quality . "'");

        $this->raspistill->quality($quality);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidIntegerBetweenZeroAndHundredProvider
     */
    public function testInvalidQualityThrowsException($quality)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->quality($quality);
    }

    public function testRawSetsCorrectArgument()
    {
        $this->expectCommandContains('--raw');

        $this->raspistill->raw();
        $this->raspistill->takePicture('foo.jpg');
    }

    public function testTimeoutSetsCorrectArgument()
    {
        $this->expectCommandContains("--timeout '2000'");

        $this->raspistill->timeout(2);
        $this->raspistill->takePicture('foo.jpg');
    }

    public function testInvalidTimeoutThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->timeout(-2);
    }

    /**
     * @dataProvider validWhiteBalanceProvider
     */
    public function testWhiteBalanceSetsCorrectArgument($whiteBalance)
    {
        $this->expectCommandContains("--awb '" . $whiteBalance . "'");

        $this->raspistill->whiteBalance($whiteBalance);
        $this->raspistill->takePicture('foo.jpg');
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidWhiteBalanceThrowsException($whiteBalance)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->raspistill->whiteBalance($whiteBalance);
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
     * @return array
     */
    public function validIntegerBetweenHundredAndEightHundredProvider()
    {
        return [
            [100],
            [101],
            [567],
            [800]
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegerBetweenHundredAndEightHundredProvider()
    {
        return [
            [801],
            [999],
            [99],
            [1],
            [0],
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
     * @return array
     */
    public function validIntegerBetweenNegativeTenAndTenProvider()
    {
        return [
            [1],
            [9],
            [10],
            [0],
            [-1],
            [-9],
            [-10]
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegerBetweenNegativeTenAndTenProvider()
    {
        return [
            [99],
            [11],
            [-99],
            [-11],
            [5.5],
            [-2.0],
            [false],
            [null],
            ['foo']
        ];
    }

    /**
     * @return array
     */
    public function validExposureProvider()
    {
        return [
            [Raspistill::EXPOSURE_AUTO],
            [Raspistill::EXPOSURE_NIGHT],
            [Raspistill::EXPOSURE_NIGHTPREVIEW],
            [Raspistill::EXPOSURE_BACKLIGHT],
            [Raspistill::EXPOSURE_SPOTLIGHT],
            [Raspistill::EXPOSURE_SPORTS],
            [Raspistill::EXPOSURE_SNOW],
            [Raspistill::EXPOSURE_BEACH],
            [Raspistill::EXPOSURE_VERYLONG],
            [Raspistill::EXPOSURE_FIXEDFPS],
            [Raspistill::EXPOSURE_ANTISHAKE],
            [Raspistill::EXPOSURE_FIREWORKS],
        ];
    }

    /**
     * @return array
     */
    public function validWhiteBalanceProvider()
    {
        return [
            [Raspistill::WHITE_BALANCE_OFF],
            [Raspistill::WHITE_BALANCE_AUTO],
            [Raspistill::WHITE_BALANCE_SUN],
            [Raspistill::WHITE_BALANCE_CLOUD],
            [Raspistill::WHITE_BALANCE_SHADE],
            [Raspistill::WHITE_BALANCE_TUNGSTEN],
            [Raspistill::WHITE_BALANCE_FLUORESCENT],
            [Raspistill::WHITE_BALANCE_INCANDESCENT],
            [Raspistill::WHITE_BALANCE_FLASH],
            [Raspistill::WHITE_BALANCE_HORIZON],
        ];
    }

    /**
     * @return array
     */
    public function invalidStringProvider()
    {
        return [
            ['foo'],
            ['%¤"*-@'],
            [1],
            [0],
            [-11],
            [5.5],
            [-2.0],
            [false],
            [null],
        ];
    }

    /**
     * @param string|array $strings
     */
    private function expectCommandContains($strings)
    {
        if (!is_array($strings)) {
            $strings = [$strings];
        }

        $this->commandRunner
            ->expects($this->once())
            ->method('run')
            ->with($this->callback(function (CommandInterface $command) use ($strings) {
                foreach ($strings as $string) {
                    $this->assertContains($string, $command->__toString());
                }

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
                $this->assertNotContains($string, $command->__toString());

                return true;
            }));
    }
}
