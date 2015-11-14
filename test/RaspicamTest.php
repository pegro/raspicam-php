<?php

namespace Cvuorinen\Raspicam\Test;

use AdamBrett\ShellWrapper\Command\CommandInterface;
use AdamBrett\ShellWrapper\ExitCodes;
use Cvuorinen\Raspicam\Raspicam;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

abstract class RaspicamTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Raspicam
     */
    protected $camera;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $commandRunner;

    /**
     * Execute cli command with a specific method of the tested class
     *
     * @param string $filename
     */
    abstract protected function execute($filename = 'foo');

    public function testGetOutputReturnCommandOutput()
    {
        $this->commandRunner
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn('some-output');

        $this->execute();

        $this->assertEquals(
            'some-output',
            $this->camera->getOutput()
        );
    }

    public function testFailedCommandThrowsException()
    {
        $this->commandRunner
            ->method('getReturnValue')
            ->willReturn(ExitCodes::GENERAL_ERROR);

        $this->setExpectedException(
            'Cvuorinen\Raspicam\CommandFailedException'
        );

        $this->execute();
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

        $this->execute();
    }

    public function testVerticalFlipSetsCorrectArgument()
    {
        $this->expectCommandContains('--vflip');

        $this->camera->verticalFlip();
        $this->execute();
    }

    public function testVerticalFlipArgumentNotSetWhenUnset()
    {
        $this->camera->verticalFlip();
        $this->execute();

        $this->expectCommandNotContains('--vflip');

        $this->camera->verticalFlip(false);
        $this->execute();
    }

    public function testHorizontalFlipSetsCorrectArgument()
    {
        $this->expectCommandContains('--hflip');

        $this->camera->horizontalFlip();
        $this->execute();
    }

    public function testFlipSetsBothVerticalAndHorizontalArguments()
    {
        $this->expectCommandContains(['--vflip', '--hflip']);

        $this->camera->flip();
        $this->execute();
    }

    /**
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testSharpnessSetsCorrectArgument($sharpness)
    {
        $this->expectCommandContains("--sharpness '" . $sharpness . "'");

        $this->camera->sharpness($sharpness);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidSharpnessThrowsException($sharpness)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->sharpness($sharpness);
    }

    /**
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testContrastSetsCorrectArgument($contrast)
    {
        $this->expectCommandContains("--contrast '" . $contrast . "'");

        $this->camera->contrast($contrast);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidContrastThrowsException($contrast)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->contrast($contrast);
    }

    /**
     * @dataProvider validIntegerBetweenZeroAndHundredProvider
     */
    public function testBrightnessSetsCorrectArgument($brightness)
    {
        $this->expectCommandContains("--brightness '" . $brightness . "'");

        $this->camera->brightness($brightness);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenZeroAndHundredProvider
     */
    public function testInvalidBrightnessThrowsException($brightness)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->brightness($brightness);
    }

    /**
     * @dataProvider validIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testSaturationSetsCorrectArgument($saturation)
    {
        $this->expectCommandContains("--saturation '" . $saturation . "'");

        $this->camera->saturation($saturation);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeHundredAndHundredProvider
     */
    public function testInvalidSaturationThrowsException($saturation)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->saturation($saturation);
    }

    /**
     * @dataProvider validIntegerBetweenHundredAndEightHundredProvider
     */
    public function testISOSetsCorrectArgument($iso)
    {
        $this->expectCommandContains("--ISO '" . $iso . "'");

        $this->camera->ISO($iso);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenHundredAndEightHundredProvider
     */
    public function testInvalidISOThrowsException($iso)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->ISO($iso);
    }

    /**
     * @dataProvider validIntegerBetweenNegativeTenAndTenProvider
     */
    public function testExposureCompensationSetsCorrectArgument($ev)
    {
        $this->expectCommandContains("--ev '" . $ev . "'");

        $this->camera->exposureCompensation($ev);
        $this->execute();
    }

    /**
     * @dataProvider invalidIntegerBetweenNegativeTenAndTenProvider
     */
    public function testInvalidExposureCompensationThrowsException($ev)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->exposureCompensation($ev);
    }

    /**
     * @dataProvider validExposureProvider
     */
    public function testExposureSetsCorrectArgument($exposure)
    {
        $this->expectCommandContains("--exposure '" . $exposure . "'");

        $this->camera->exposure($exposure);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validExposureProvider()
    {
        return [
            [Raspicam::EXPOSURE_AUTO],
            [Raspicam::EXPOSURE_NIGHT],
            [Raspicam::EXPOSURE_NIGHTPREVIEW],
            [Raspicam::EXPOSURE_BACKLIGHT],
            [Raspicam::EXPOSURE_SPOTLIGHT],
            [Raspicam::EXPOSURE_SPORTS],
            [Raspicam::EXPOSURE_SNOW],
            [Raspicam::EXPOSURE_BEACH],
            [Raspicam::EXPOSURE_VERYLONG],
            [Raspicam::EXPOSURE_FIXEDFPS],
            [Raspicam::EXPOSURE_ANTISHAKE],
            [Raspicam::EXPOSURE_FIREWORKS],
        ];
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidExposureThrowsException($exposure)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->exposure($exposure);
    }

    /**
     * @dataProvider validWhiteBalanceProvider
     */
    public function testWhiteBalanceSetsCorrectArgument($whiteBalance)
    {
        $this->expectCommandContains("--awb '" . $whiteBalance . "'");

        $this->camera->whiteBalance($whiteBalance);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validWhiteBalanceProvider()
    {
        return [
            [Raspicam::WHITE_BALANCE_OFF],
            [Raspicam::WHITE_BALANCE_AUTO],
            [Raspicam::WHITE_BALANCE_SUN],
            [Raspicam::WHITE_BALANCE_CLOUD],
            [Raspicam::WHITE_BALANCE_SHADE],
            [Raspicam::WHITE_BALANCE_TUNGSTEN],
            [Raspicam::WHITE_BALANCE_FLUORESCENT],
            [Raspicam::WHITE_BALANCE_INCANDESCENT],
            [Raspicam::WHITE_BALANCE_FLASH],
            [Raspicam::WHITE_BALANCE_HORIZON],
        ];
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidWhiteBalanceThrowsException($whiteBalance)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->whiteBalance($whiteBalance);
    }

    /**
     * @dataProvider validEffectProvider
     */
    public function testEffectSetsCorrectArgument($effect)
    {
        $this->expectCommandContains("--imxfx '" . $effect . "'");

        $this->camera->effect($effect);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validEffectProvider()
    {
        return [
            [Raspicam::EFFECT_NONE],
            [Raspicam::EFFECT_NEGATIVE],
            [Raspicam::EFFECT_SOLARISE],
            [Raspicam::EFFECT_POSTERISE],
            [Raspicam::EFFECT_WHITEBOARD],
            [Raspicam::EFFECT_BLACKBOARD],
            [Raspicam::EFFECT_SKETCH],
            [Raspicam::EFFECT_DENOISE],
            [Raspicam::EFFECT_EMBOSS],
            [Raspicam::EFFECT_OILPAINT],
            [Raspicam::EFFECT_HATCH],
            [Raspicam::EFFECT_GPEN],
            [Raspicam::EFFECT_PASTEL],
            [Raspicam::EFFECT_WATERCOLOUR],
            [Raspicam::EFFECT_FILM],
            [Raspicam::EFFECT_BLUR],
            [Raspicam::EFFECT_SATURATION],
            [Raspicam::EFFECT_COLOURSWAP],
            [Raspicam::EFFECT_WASHEDOUT],
            [Raspicam::EFFECT_COLOURPOINT],
            [Raspicam::EFFECT_COLOURBALANCE],
            [Raspicam::EFFECT_CARTOON],
        ];
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidEffectThrowsException($effect)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->effect($effect);
    }

    /**
     * @dataProvider validMeteringProvider
     */
    public function testMeteringSetsCorrectArgument($metering)
    {
        $this->expectCommandContains("--metering '" . $metering . "'");

        $this->camera->metering($metering);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validMeteringProvider()
    {
        return [
            [Raspicam::METERING_AVERAGE],
            [Raspicam::METERING_SPOT],
            [Raspicam::METERING_BACKLIT],
            [Raspicam::METERING_MATRIX],
        ];
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidMeteringThrowsException($metering)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->metering($metering);
    }

    /**
     * @dataProvider validDrcProvider
     */
    public function testDynamicRangeCompressionSetsCorrectArgument($drc)
    {
        $this->expectCommandContains("--drc '" . $drc . "'");

        $this->camera->dynamicRangeCompression($drc);
        $this->execute();
    }

    /**
     * @dataProvider invalidStringProvider
     */
    public function testInvalidDynamicRangeCompressionThrowsException($drc)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->dynamicRangeCompression($drc);
    }

    /**
     * @dataProvider validRotateProvider
     */
    public function testRotateSetsCorrectArgument($rotate)
    {
        $this->expectCommandContains("--rotation '" . $rotate . "'");

        $this->camera->rotate($rotate);
        $this->execute();
    }

    /**
     * @return array
     */
    public function validRotateProvider()
    {
        return [
            [0],
            [90],
            [180],
            [270],
        ];
    }

    /**
     * @dataProvider invalidRotateProvider
     */
    public function testInvalidRotateThrowsException($rotate)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->rotate($rotate);
    }

    /**
     * @return array
     */
    public function invalidRotateProvider()
    {
        return [
            ['foo'],
            ['%¤"*-@'],
            ['90'],
            [1],
            [10],
            [-11],
            [5.5],
            [-2.0],
            [false],
            [null],
        ];
    }

    /**
     * @dataProvider shutterSpeedProvider
     */
    public function testShutterSpeedSetsCorrectArgument($value, $unit, $expected)
    {
        $this->expectCommandContains("--shutter '" . $expected . "'");

        $this->camera->shutterSpeed($value, $unit);
        $this->execute();
    }

    /**
     * @return array
     */
    public function shutterSpeedProvider()
    {
        return [
            ['value' => 2, 'unit' => Raspicam::TIMEUNIT_SECOND, 'expected' => 2000000],
            ['value' => 1.54, 'unit' => Raspicam::TIMEUNIT_SECOND, 'expected' => 1540000],
            ['value' => 4000, 'unit' => Raspicam::TIMEUNIT_MILLISECOND, 'expected' => 4000000],
            ['value' => 5000000, 'unit' => Raspicam::TIMEUNIT_MICROSECOND, 'expected' => 5000000],
        ];
    }

    /**
     * @dataProvider invalidPositiveNumberProvider
     */
    public function testInvalidShutterSpeedThrowsException($shutter)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->shutterSpeed($shutter);
    }

    public function testInvalidTimeUnitThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->shutterSpeed(1, 'foo');
    }

    /**
     * @dataProvider validSensorModeProvider
     */
    public function testSensorModeSetsCorrectArgument($mode)
    {
        $this->expectCommandContains("--mode '" . $mode . "'");

        $this->camera->sensorMode($mode);
        $this->execute();
    }

    /**
     * @dataProvider invalidSensorModeProvider
     */
    public function testInvalidSensorModeThrowsException($mode)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->camera->sensorMode($mode);
    }

    /**
     * @return array
     */
    public function validSensorModeProvider()
    {
        return [
            [0],
            [1],
            [5],
            [7],
        ];
    }

    /**
     * @return array
     */
    public function invalidSensorModeProvider()
    {
        return [
            [-1],
            [15],
            [1.5],
            [-12.0],
            [false],
            [null],
            ['foo'],
        ];
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
    public function invalidPositiveNumberProvider()
    {
        return [
            [0],
            [-99],
            [-11],
            [-2.0],
            [false],
            [null],
            ['foo']
        ];
    }

    /**
     * @return array
     */
    public function validDrcProvider()
    {
        return [
            [Raspicam::DRC_OFF],
            [Raspicam::DRC_LOW],
            [Raspicam::DRC_MEDIUM],
            [Raspicam::DRC_HIGH],
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
    protected function expectCommandContains($strings)
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
    protected function expectCommandNotContains($string)
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
