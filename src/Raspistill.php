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
}
