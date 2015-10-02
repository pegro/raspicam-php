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
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $verticalFlip;

    /**
     * @var bool
     */
    protected $horizontalFlip;

    /**
     * @var int
     */
    protected $sharpness;

    /**
     * @var int
     */
    protected $contrast;

    /**
     * @var int
     */
    protected $brightness;

    /**
     * Flips the image vertically
     *
     * @param bool $value
     *
     * @return $this
     */
    public function verticalFlip($value = true)
    {
        $this->verticalFlip = (bool) $value;

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
        $this->horizontalFlip = (bool) $value;

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

        $this->sharpness = $value;

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

        $this->contrast = $value;

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

        $this->brightness = $value;

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

        $this->filename = $filename;

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

        if ($this->filename) {
            $command->addArgument('output', $this->filename);
        }

        if ($this->verticalFlip) {
            $command->addArgument('vflip');
        }

        if ($this->horizontalFlip) {
            $command->addArgument('hflip');
        }

        if (null !== $this->sharpness) {
            $command->addArgument('sharpness', $this->sharpness);
        }

        if (null !== $this->contrast) {
            $command->addArgument('contrast', $this->contrast);
        }

        if (null !== $this->brightness) {
            $command->addArgument('brightness', $this->brightness);
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
