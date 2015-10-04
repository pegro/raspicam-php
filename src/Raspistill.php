<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\CommandInterface;

/**
 * Class that abstracts the usage of raspistill cli utility that is used to take photos with the
 * Raspberry Pi camera module.
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
     * Time in seconds before takes picture, default is 5
     *
     * The camera will run for this length of time, then take the picture.
     *
     * @param int $value
     *
     * @return $this
     */
    public function timeout($value)
    {
        if (!is_int($value) || $value < 0) {
            throw new \InvalidArgumentException('Expected positive integer');
        }

        $this->valueArguments['timeout'] = $value * 1000; // convert to milliseconds

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
}
