<?php

namespace Cvuorinen\Raspicam;

use Symfony\Component\Process\Process;

/**
 * Abstracts some common functionality related to al of the camera commands
 *
 * @package Cvuorinen\Raspicam
 */
abstract class Raspicam
{
    const EXPOSURE_AUTO = 'auto';
    const EXPOSURE_NIGHT = 'night';
    const EXPOSURE_NIGHTPREVIEW = 'nightpreview';
    const EXPOSURE_BACKLIGHT = 'backlight';
    const EXPOSURE_SPOTLIGHT = 'spotlight';
    const EXPOSURE_SPORTS = 'sports';
    const EXPOSURE_SNOW = 'snow';
    const EXPOSURE_BEACH = 'beach';
    const EXPOSURE_VERYLONG = 'verylong';
    const EXPOSURE_FIXEDFPS = 'fixedfps';
    const EXPOSURE_ANTISHAKE = 'antishake';
    const EXPOSURE_FIREWORKS = 'fireworks';

    const WHITE_BALANCE_OFF = 'off';
    const WHITE_BALANCE_AUTO = 'auto';
    const WHITE_BALANCE_SUN = 'sun';
    const WHITE_BALANCE_CLOUD = 'cloud';
    const WHITE_BALANCE_SHADE = 'shade';
    const WHITE_BALANCE_TUNGSTEN = 'tungsten';
    const WHITE_BALANCE_FLUORESCENT = 'fluorescent';
    const WHITE_BALANCE_INCANDESCENT = 'incandescent';
    const WHITE_BALANCE_FLASH = 'flash';
    const WHITE_BALANCE_HORIZON = 'horizon';

    const EFFECT_NONE = 'none';
    const EFFECT_NEGATIVE = 'negative';
    const EFFECT_SOLARISE = 'solarise';
    const EFFECT_POSTERISE = 'posterise';
    const EFFECT_WHITEBOARD = 'whiteboard';
    const EFFECT_BLACKBOARD = 'blackboard';
    const EFFECT_SKETCH = 'sketch';
    const EFFECT_DENOISE = 'denoise';
    const EFFECT_EMBOSS = 'emboss';
    const EFFECT_OILPAINT = 'oilpaint';
    const EFFECT_HATCH = 'hatch';
    const EFFECT_GPEN = 'gpen';
    const EFFECT_PASTEL = 'pastel';
    const EFFECT_WATERCOLOUR = 'watercolour';
    const EFFECT_FILM = 'film';
    const EFFECT_BLUR = 'blur';
    const EFFECT_SATURATION = 'saturation';
    const EFFECT_COLOURSWAP = 'colourswap';
    const EFFECT_WASHEDOUT = 'washedout';
    const EFFECT_COLOURPOINT = 'colourpoint';
    const EFFECT_COLOURBALANCE = 'colourbalance';
    const EFFECT_CARTOON = 'cartoon';

    const METERING_AVERAGE = 'average';
    const METERING_SPOT = 'spot';
    const METERING_BACKLIT = 'backlit';
    const METERING_MATRIX = 'matrix';

    const DRC_OFF = 'off';
    const DRC_LOW = 'low';
    const DRC_MEDIUM = 'medium';
    const DRC_HIGH = 'high';

    const ENCODING_JPG = 'jpg';
    const ENCODING_BMP = 'bmp';
    const ENCODING_GIF = 'gif';
    const ENCODING_PNG = 'png';

    const TIMEUNIT_MINUTE = 'm';
    const TIMEUNIT_SECOND = 's';
    const TIMEUNIT_MILLISECOND = 'ms';
    const TIMEUNIT_MICROSECOND = 'us';

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var string
     */
    protected $lastReturnValue;

    /**
     * @var array
     */
    protected $booleanArguments = [];

    /**
     * @var array
     */
    protected $valueArguments = [];

    /**
     * Get name of the executable cli command
     *
     * @return string
     */
    abstract protected function getExecutable();

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $method = [$this, $key];

            if (is_callable($method)) {
                call_user_func($method, $value);
            }
        }
    }

    /**
     * Flips the image both vertically and horizontally
     *
     * @param bool $value Optional. TRUE enables flip, FALSE disables (default=TRUE)
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
     * @param bool $value Optional. TRUE enables flip, FALSE disables (default=TRUE)
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
     * @param bool $value Optional. TRUE enables flip, FALSE disables (default=TRUE)
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
     * @param int $value Sharpness
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
     * @param int $value Contrast
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
     * @param int $value Brightness
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
     * @param int $value Saturation
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
     * @param int $value ISO
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
     * @param int $value Exposure compensation
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
     *  - 'auto' `Raspicam::EXPOSURE_AUTO` Use automatic exposure mode
     *  - 'night' `Raspicam::EXPOSURE_NIGHT` Select setting for night shooting
     *  - 'nightpreview' `Raspicam::EXPOSURE_NIGHTPREVIEW`
     *  - 'backlight' `Raspicam::EXPOSURE_BACKLIGHT` Select setting for back lit subject
     *  - 'spotlight' `Raspicam::EXPOSURE_SPOTLIGHT`
     *  - 'sports' `Raspicam::EXPOSURE_SPORTS` Select setting for sports (fast shutter etc)
     *  - 'snow' `Raspicam::EXPOSURE_SNOW` Select setting optimised for snowy scenery
     *  - 'beach' `Raspicam::EXPOSURE_BEACH` Select setting optimised for beach
     *  - 'verylong' `Raspicam::EXPOSURE_VERYLONG` Select setting for long exposures
     *  - 'fixedfps' `Raspicam::EXPOSURE_FIXEDFPS` Constrain fps to a fixed value
     *  - 'antishake' `Raspicam::EXPOSURE_ANTISHAKE` Antishake mode
     *  - 'fireworks' `Raspicam::EXPOSURE_FIREWORKS` Select settings
     *
     * Note that not all of these settings may be implemented, depending on camera type.
     *
     * @param string $mode Exposure mode
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
     * Set Automatic White Balance (AWB) mode
     *
     * Possible options are:
     *  - 'off' `Raspicam::WHITE_BALANCE_OFF` Turn off white balance calculation
     *  - 'auto' `Raspicam::WHITE_BALANCE_AUTO` Automatic mode (default)
     *  - 'sun' `Raspicam::WHITE_BALANCE_SUN` Sunny mode
     *  - 'cloud' `Raspicam::WHITE_BALANCE_CLOUD` Cloudy mode
     *  - 'shade' `Raspicam::WHITE_BALANCE_SHADE` Shaded mode
     *  - 'tungsten' `Raspicam::WHITE_BALANCE_TUNGSTEN` Tungsten lighting mode
     *  - 'fluorescent' `Raspicam::WHITE_BALANCE_FLUORESCENT` Fluorescent lighting mode
     *  - 'incandescent' `Raspicam::WHITE_BALANCE_INCANDESCENT` Incandescent lighting mode
     *  - 'flash' `Raspicam::WHITE_BALANCE_FLASH` Flash mode
     *  - 'horizon' `Raspicam::WHITE_BALANCE_HORIZON` Horizon mode
     *
     * Note that not all of these settings may be implemented, depending on camera type.
     *
     * @param string $mode AWB mode
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
     * - 'none' `Raspicam::EFFECT_NONE` NO effect (default)
     * - 'negative' `Raspicam::EFFECT_NEGATIVE` Negate the image
     * - 'solarise' `Raspicam::EFFECT_SOLARISE` Solarise the image
     * - 'posterise' `Raspicam::EFFECT_POSTERISE` Posterise the image
     * - 'whiteboard' `Raspicam::EFFECT_WHITEBOARD` Whiteboard effect
     * - 'blackboard' `Raspicam::EFFECT_BLACKBOARD` Blackboard effect
     * - 'sketch' `Raspicam::EFFECT_SKETCH` Sketch style effect
     * - 'denoise' `Raspicam::EFFECT_DENOISE` Denoise the image
     * - 'emboss' `Raspicam::EFFECT_EMBOSS` Emboss the image
     * - 'oilpaint' `Raspicam::EFFECT_OILPAINT` Apply an oil paint style effect
     * - 'hatch' `Raspicam::EFFECT_HATCH` Hatch sketch style
     * - 'gpen' `Raspicam::EFFECT_GPEN`
     * - 'pastel' `Raspicam::EFFECT_PASTEL` A pastel style effect
     * - 'watercolour' `Raspicam::EFFECT_WATERCOLOUR` A watercolour style effect
     * - 'film' `Raspicam::EFFECT_FILM` Film grain style effect
     * - 'blur' `Raspicam::EFFECT_BLUR` Blur the image
     * - 'saturation' `Raspicam::EFFECT_SATURATION` Colour saturate the image
     * - 'colourswap' `Raspicam::EFFECT_COLOURSWAP` Not fully implemented
     * - 'washedout' `Raspicam::EFFECT_WASHEDOUT` Not fully implemented
     * - 'colourpoint' `Raspicam::EFFECT_COLOURPOINT` Not fully implemented
     * - 'colourbalance' `Raspicam::EFFECT_COLOURBALANCE` Not fully implemented
     * - 'cartoon' `Raspicam::EFFECT_CARTOON` Not fully implemented
     *
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
     * - 'average' `Raspicam::METERING_AVERAGE` Average the whole frame for metering
     * - 'spot' `Raspicam::METERING_SPOT` Spot metering
     * - 'backlit' `Raspicam::METERING_BACKLIT` Assume a backlit image
     * - 'matrix' `Raspicam::METERING_MATRIX` Matrix metering
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
     * Enable/Disable Dynamic Range compression
     *
     * DRC changes the images by increasing the range of dark areas of the image, and decreasing the brighter areas.
     * This can improve the image in low light areas.
     *
     * Possible options are:
     * - 'off' `Raspicam::DRC_OFF` (default)
     * - 'low' `Raspicam::DRC_LOW`
     * - 'medium' `Raspicam::DRC_MEDIUM`
     * - 'high' `Raspicam::DRC_HIGH`
     *
     * @param string $mode
     *
     * @return $this
     */
    public function dynamicRangeCompression($mode)
    {
        $drcModes = [
            self::DRC_OFF,
            self::DRC_LOW,
            self::DRC_MEDIUM,
            self::DRC_HIGH,
        ];

        $this->assertInArray($mode, $drcModes);

        $this->valueArguments['drc'] = $mode;

        return $this;
    }

    /**
     * Set image rotation
     *
     * Only 0, 90, 180 and 270 degree rotations are supported.
     *
     * @param int $degrees Degrees
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
     * Unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
     * `Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.
     *
     * @param int|float $value Shutter speed
     * @param string    $unit  Optional. Time unit for $value Default: `Raspicam::TIMEUNIT_SECOND`
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
     * Sets a specified sensor mode, disabling the automatic selection.
     *
     * Possible values are:
     *
     * | Mode | Size      | Aspect ratio | Frame rates | FOV     | Binning       |
     * | -----|-----------|--------------|-------------|---------|---------------|
     * | 0    |                  automatic selection                             |
     * | 1    | 1920x1080 | 16:9         | 1-30fps     | Partial | None          |
     * | 2    | 2592x1944 | 4:3          | 1-15fps     | Full    | None          |
     * | 3    | 2592x1944 | 4:3          | 0.1666-1fps | Full    | None          |
     * | 4    | 1296x972  | 4:3          | 1-42fps     | Full    | 2x2           |
     * | 5    | 1296x730  | 16:9         | 1-49fps     | Full    | 2x2           |
     * | 6    | 640x480   | 4:3          | 42.1-60fps  | Full    | 2x2 plus skip |
     * | 7    | 640x480   | 4:3          | 60.1-90fps  | Full    | 2x2 plus skip |
     *
     * @param int $mode
     *
     * @return $this
     */
    public function sensorMode($mode)
    {
        $this->assertIntBetween($mode, 0, 7);

        $this->valueArguments['mode'] = $mode;

        return $this;
    }

    /**
     * @throws CommandFailedException
     */
    public function start()
    {
        if($this->process && $this->process->isRunning()) {
            throw new CommandFailedException('Process already in progress');
        }

        $command = [$this->getExecutable()];

        foreach ($this->booleanArguments as $name => $value) {
            if ($value) {
                $command[] = '--' . $name;
            }
        }

        foreach ($this->valueArguments as $name => $value) {
            $command[] = '--' . $name;
            $command[] = $value;
        }

        $this->process = new Process($command);

        try {
            $this->process->start();

            return $this->process->isRunning();
        } catch (ProcessFailedException $exception) {
            throw new CommandFailedException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->process != null ? $this->process->isRunning() : false;
    }

    /**
     *
     */
    public function stop()
    {
        if($this->process && $this->process->isRunning()) {
            $this->process->stop();
        }

        // HACK: make sure raspistill is dead
        $retries = 5;
        while($retries) {
            sleep(1);
            $pid = exec('pidof raspistill');

            // it's dead... okay, move along
            if(empty($pid)) {
                return true;
            }
            $retries--;
        }

        // KILL IT!!
        exec('killall -9 raspistill');

        if(exec('pidof raspistill')) {
            throw new \Exception('could not kill raspistill. I guess it\'s hung and the watchdog has to force a reboot :/');
        }
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     */
    protected function assertIntBetween($value, $min, $max)
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
    protected function assertInArray($value, array $validValues)
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
    protected function assertPositiveNumber($value)
    {
        if ((!is_int($value) && !is_float($value)) || $value <= 0) {
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
    protected function convertTimeUnit($value, $inputUnit, $outputUnit)
    {
        switch ($inputUnit) {
            case self::TIMEUNIT_MINUTE:
                $modifier = 60000000;
                break;
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
                    sprintf('Invalid input time unit \'%s\'', $inputUnit)
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
                    sprintf('Invalid output time unit \'%s\'', $outputUnit)
                );
        }

        return (int) ceil($value * $modifier);
    }
}
