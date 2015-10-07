<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\CommandInterface;

/**
 * Class that abstracts the usage of raspistill cli utility that is used to take photos with the
 * Raspberry Pi camera module.
 *
 * @see https://www.raspberrypi.org/documentation/raspbian/applications/camera.md
 *
 * @package Cvuorinen\Raspicam
 */
class Raspistill extends Raspicam
{
    const COMMAND = 'raspistill';

    /**
     * @var array
     */
    protected $booleanArguments = [];

    /**
     * @var array
     */
    protected $valueArguments = [];

    /**
     * Flips the image both vertically and horizontally
     *
     * @param bool $value
     *
     * @return $this
     */
    public function flip($value = true)
    {
        $this->booleanArguments['vflip'] = (bool) $value;
        $this->booleanArguments['hflip'] = (bool) $value;

        return $this;
    }

    /**
     * Flips the image vertically
     *
     * @param bool $value
     *
     * @return $this
     */
    public function verticalFlip($value = true)
    {
        $this->booleanArguments['vflip'] = (bool) $value;

        return $this;
    }

    /**
     * Flips the image horizontally
     *
     * @param bool $value
     *
     * @return $this
     */
    public function horizontalFlip($value = true)
    {
        $this->booleanArguments['hflip'] = (bool) $value;

        return $this;
    }

    /**
     * Set the sharpness of the image, 0 is the default (-100 to 100)
     *
     * @param int $value
     *
     * @return $this
     */
    public function sharpness($value)
    {
        $this->assertIntBetween($value, -100, 100);

        $this->valueArguments['sharpness'] = $value;

        return $this;
    }

    /**
     * Set the contrast of the image, 0 is the default (-100 to 100)
     *
     * @param int $value
     *
     * @return $this
     */
    public function contrast($value)
    {
        $this->assertIntBetween($value, -100, 100);

        $this->valueArguments['contrast'] = $value;

        return $this;
    }

    /**
     * Set the brightness of the image, 50 is the default. 0 is black, 100 is white.
     *
     * @param int $value
     *
     * @return $this
     */
    public function brightness($value)
    {
        $this->assertIntBetween($value, 0, 100);

        $this->valueArguments['brightness'] = $value;

        return $this;
    }

    /**
     * Set the colour saturation of the image. 0 is the default (-100 to 100)
     *
     * @param int $value
     *
     * @return $this
     */
    public function saturation($value)
    {
        $this->assertIntBetween($value, -100, 100);

        $this->valueArguments['saturation'] = $value;

        return $this;
    }

    /**
     * Sets the ISO to be used for captures. Range is 100 to 800.
     *
     * @param int $value
     *
     * @return $this
     */
    public function ISO($value)
    {
        $this->assertIntBetween($value, 100, 800);

        $this->valueArguments['ISO'] = $value;

        return $this;
    }

    /**
     * Set the exposure (EV) compensation of the image. Range is -10 to +10, default is 0.
     *
     * @param int $value
     *
     * @return $this
     */
    public function exposureCompensation($value)
    {
        $this->assertIntBetween($value, -10, 10);

        $this->valueArguments['ev'] = $value;

        return $this;
    }

    /**
     * Set exposure mode
     *
     * Possible options are:
     *  - 'auto' Use automatic exposure mode
     *  - 'night' Select setting for night shooting
     *  - 'nightpreview'
     *  - 'backlight' Select setting for back lit subject
     *  - 'spotlight'
     *  - 'sports' Select setting for sports (fast shutter etc)
     *  - 'snow' Select setting optimised for snowy scenery
     *  - 'beach' Select setting optimised for beach
     *  - 'verylong' Select setting for long exposures
     *  - 'fixedfps' Constrain fps to a fixed value
     *  - 'antishake' Antishake mode
     *  - 'fireworks' Select settings
     * Note that not all of these settings may be implemented, depending on camera type.
     *
     * @param string $mode
     *
     * @return $this
     */
    public function exposure($mode)
    {
        $exposureModes = [
            self::EXPOSURE_AUTO,
            self::EXPOSURE_NIGHT,
            self::EXPOSURE_NIGHTPREVIEW,
            self::EXPOSURE_BACKLIGHT,
            self::EXPOSURE_SPOTLIGHT,
            self::EXPOSURE_SPORTS,
            self::EXPOSURE_SNOW,
            self::EXPOSURE_BEACH,
            self::EXPOSURE_VERYLONG,
            self::EXPOSURE_FIXEDFPS,
            self::EXPOSURE_ANTISHAKE,
            self::EXPOSURE_FIREWORKS,
        ];

        $this->assertInArray($mode, $exposureModes);

        $this->valueArguments['exposure'] = $mode;

        return $this;
    }

    /**
     * Set jpeg quality (0 to 100)
     *
     * Quality 100 is almost completely uncompressed. 75 is a good all round value
     *
     * @param int $value
     *
     * @return $this
     */
    public function quality($value)
    {
        $this->assertIntBetween($value, 0, 100);

        $this->valueArguments['quality'] = $value;

        return $this;
    }

    /**
     * Add raw bayer data to jpeg metadata
     *
     * This option inserts the raw Bayer data from the camera in to the JPEG metadata
     *
     * @param bool $value
     *
     * @return $this
     */
    public function raw($value = true)
    {
        $this->booleanArguments['raw'] = (bool) $value;

        return $this;
    }

    /**
     * Time before takes picture, default is 5 seconds
     *
     * The camera will run for this length of time, then take the picture.
     * Unit can be one of: Raspicam::TIMEUNIT_SECOND, Raspicam::TIMEUNIT_MILLISECOND, Raspicam::TIMEUNIT_MICROSECOND
     *
     * @param int|float $value
     * @param string    $unit
     *
     * @return $this
     */
    public function timeout($value, $unit = self::TIMEUNIT_SECOND)
    {
        $this->assertPositiveNumber($value);

        $this->valueArguments['timeout'] = $this->convertTimeUnit(
            $value,
            $unit,
            self::TIMEUNIT_MILLISECOND
        );

        return $this;
    }

    /**
     * Set Automatic White Balance (AWB) mode
     *
     * Possible options are:
     *  - 'off' Turn off white balance calculation
     *  - 'auto' Automatic mode (default)
     *  - 'sun' Sunny mode
     *  - 'cloud' Cloudy mode
     *  - 'shade' Shaded mode
     *  - 'tungsten' Tungsten lighting mode
     *  - 'fluorescent' Fluorescent lighting mode
     *  - 'incandescent' Incandescent lighting mode
     *  - 'flash' Flash mode
     *  - 'horizon' Horizon mode
     * Note that not all of these settings may be implemented, depending on camera type.
     *
     * @param string $mode
     *
     * @return $this
     */
    public function whiteBalance($mode)
    {
        $awbModes = [
            self::WHITE_BALANCE_OFF,
            self::WHITE_BALANCE_AUTO,
            self::WHITE_BALANCE_SUN,
            self::WHITE_BALANCE_CLOUD,
            self::WHITE_BALANCE_SHADE,
            self::WHITE_BALANCE_TUNGSTEN,
            self::WHITE_BALANCE_FLUORESCENT,
            self::WHITE_BALANCE_INCANDESCENT,
            self::WHITE_BALANCE_FLASH,
            self::WHITE_BALANCE_HORIZON,
        ];

        $this->assertInArray($mode, $awbModes);

        $this->valueArguments['awb'] = $mode;

        return $this;
    }

    /**
     * Set an effect to be applied to the image
     *
     * Possible options are:
     * - 'none' NO effect (default)
     * - 'negative' Negate the image
     * - 'solarise' Solarise the image
     * - 'posterise' Posterise the image
     * - 'whiteboard' Whiteboard effect
     * - 'blackboard' Blackboard effect
     * - 'sketch' Sketch style effect
     * - 'denoise' Denoise the image
     * - 'emboss' Emboss the image
     * - 'oilpaint' Apply an oil paint style effect
     * - 'hatch' Hatch sketch style
     * - 'gpen'
     * - 'pastel' A pastel style effect
     * - 'watercolour' A watercolour style effect
     * - 'film' Film grain style effect
     * - 'blur' Blur the image
     * - 'saturation' Colour saturate the image
     * - 'colourswap' Not fully implemented
     * - 'washedout' Not fully implemented
     * - 'colourpoint' Not fully implemented
     * - 'colourbalance' Not fully implemented
     * - 'cartoon' Not fully implemented
     * Note that not all of these settings may be available in all circumstances.
     *
     * @param string $mode
     *
     * @return $this
     */
    public function effect($mode)
    {
        $effectModes = [
            self::EFFECT_NONE,
            self::EFFECT_NEGATIVE,
            self::EFFECT_SOLARISE,
            self::EFFECT_POSTERISE,
            self::EFFECT_WHITEBOARD,
            self::EFFECT_BLACKBOARD,
            self::EFFECT_SKETCH,
            self::EFFECT_DENOISE,
            self::EFFECT_EMBOSS,
            self::EFFECT_OILPAINT,
            self::EFFECT_HATCH,
            self::EFFECT_GPEN,
            self::EFFECT_PASTEL,
            self::EFFECT_WATERCOLOUR,
            self::EFFECT_FILM,
            self::EFFECT_BLUR,
            self::EFFECT_SATURATION,
            self::EFFECT_COLOURSWAP,
            self::EFFECT_WASHEDOUT,
            self::EFFECT_COLOURPOINT,
            self::EFFECT_COLOURBALANCE,
            self::EFFECT_CARTOON,
        ];

        $this->assertInArray($mode, $effectModes);

        $this->valueArguments['imxfx'] = $mode;

        return $this;
    }

    /**
     * Set metering mode
     *
     * Possible options are:
     * - 'average' Average the whole frame for metering
     * - 'spot' Spot metering
     * - 'backlit' Assume a backlit image
     * - 'matrix' Matrix metering
     *
     * @param string $mode
     *
     * @return $this
     */
    public function metering($mode)
    {
        $meteringModes = [
            self::METERING_AVERAGE,
            self::METERING_SPOT,
            self::METERING_BACKLIT,
            self::METERING_MATRIX,
        ];

        $this->assertInArray($mode, $meteringModes);

        $this->valueArguments['metering'] = $mode;

        return $this;
    }

    /**
     * Set image rotation
     *
     * Only 0, 90, 180 and 270 degree rotations are supported.
     *
     * @param int $degrees
     *
     * @return $this
     */
    public function rotate($degrees)
    {
        $supportedRotations = [
            0,
            90,
            180,
            270,
        ];

        $this->assertInArray($degrees, $supportedRotations);

        $this->valueArguments['rotation'] = $degrees;

        return $this;
    }

    /**
     * Set the shutter speed to the specified time.
     *
     * There is currently an upper limit of approximately 6000000us (6000ms, 6s) past which operation is undefined.
     * Unit can be one of: Raspicam::TIMEUNIT_SECOND, Raspicam::TIMEUNIT_MILLISECOND, Raspicam::TIMEUNIT_MICROSECOND
     *
     * @param int    $value
     * @param string $unit
     *
     * @return $this
     */
    public function shutterSpeed($value, $unit = self::TIMEUNIT_SECOND)
    {
        $this->assertPositiveNumber($value);

        $this->valueArguments['shutter'] = $this->convertTimeUnit(
            $value,
            $unit,
            self::TIMEUNIT_MICROSECOND
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutable()
    {
        return self::COMMAND;
    }

    /**
     * @param string $filename
     */
    public function takePicture($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->valueArguments['output'] = $filename;

        $this->execute(
            $this->buildCommand()
        );
    }

    /**
     * @return CommandInterface
     */
    private function buildCommand()
    {
        $command = $this->getCommandBuilder();

        foreach ($this->booleanArguments as $name => $value) {
            if ($value) {
                $command->addArgument($name);
            }
        }

        foreach ($this->valueArguments as $name => $value) {
            $command->addArgument($name, $value);
        }

        return $command;
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     */
    private function assertIntBetween($value, $min, $max)
    {
        if (!is_int($value) || $value < $min || $value > $max) {
            throw new \InvalidArgumentException(
                sprintf('Expected integer between %s and %s', $min, $max)
            );
        }
    }

    /**
     * @param mixed $value
     * @param array $validValues
     */
    private function assertInArray($value, array $validValues)
    {
        if (!in_array($value, $validValues, true)) {
            throw new \InvalidArgumentException(
                sprintf('Expected value to be one of [%s]', implode(', ', $validValues))
            );
        }
    }

    /**
     * @param mixed $value
     */
    private function assertPositiveNumber($value)
    {
        if ((!is_int($value) && !is_float($value)) || $value < 0) {
            throw new \InvalidArgumentException('Expected value to be positive number');
        }
    }

    /**
     * @param int|float $value
     * @param string    $inputUnit
     * @param string    $outputUnit
     *
     * @return int
     */
    private function convertTimeUnit($value, $inputUnit, $outputUnit)
    {
        switch ($inputUnit) {
            case self::TIMEUNIT_SECOND:
                $modifier = 1000000;
                break;
            case self::TIMEUNIT_MILLISECOND:
                $modifier = 1000;
                break;
            case self::TIMEUNIT_MICROSECOND:
                $modifier = 1;
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Invalid time unit \'%s\'', $inputUnit)
                );
        }

        switch ($outputUnit) {
            case self::TIMEUNIT_SECOND:
                $modifier /= 1000000;
                break;
            case self::TIMEUNIT_MILLISECOND:
                $modifier /= 1000;
                break;
            case self::TIMEUNIT_MICROSECOND:
                $modifier /= 1;
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Invalid time unit \'%s\'', $outputUnit)
                );
        }

        return (int) ceil($value * $modifier);
    }
}
