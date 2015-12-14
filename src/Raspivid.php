<?php

namespace Cvuorinen\Raspicam;

/**
 * Class that abstracts the usage of raspivid cli utility that is used to record videos with the
 * Raspberry Pi camera module.
 *
 * @link https://www.raspberrypi.org/documentation/raspbian/applications/camera.md
 *
 * @package Cvuorinen\Raspicam
 */
class Raspivid extends Raspicam
{
    const COMMAND = 'raspivid';

    /**
     * Raspivid constructor.
     *
     * **Example:**
     * ```php
     * $camera = new Raspivid([
     *     'width' => 640,
     *     'height' => 480,
     *     'framerate' => 24,
     * ]);
     * ```
     *
     * @param array $options Associative array where key=method name & value=parameter passed to the method
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutable()
    {
        return self::COMMAND;
    }

    /**
     * Set video width in pixels
     *
     * @param int $value Width of resulting video. This should be between 64 and 1920.
     *
     * @return $this
     */
    public function width($value)
    {
        $this->assertIntBetween($value, 64, 1920);

        $this->valueArguments['width'] = $value;

        return $this;
    }

    /**
     * Set video height in pixels
     *
     * @param int $value Height of resulting video. This should be between 64 and 1080.
     *
     * @return $this
     */
    public function height($value)
    {
        $this->assertIntBetween($value, 64, 1080);

        $this->valueArguments['height'] = $value;

        return $this;
    }

    /**
     * Record a video clip for the specified amount of time and save with the given filename
     *
     * Time unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
     * `Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.
     *
     * **Example:**
     * ```php
     * // Record a 5s video clip with default settings (1080p30)
     * $camera->recordVideo('video.h264', 5);
     * ```
     *
     * @param string    $filename
     * @param int|float $length   Time how long to keep recording.
     * @param string    $timeUnit Optional. Time unit for $length. Default: `Raspicam::TIMEUNIT_SECOND`
     */
    public function recordVideo($filename, $length, $timeUnit = self::TIMEUNIT_SECOND)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->valueArguments['output'] = $filename;

        $this->timeout($length, $timeUnit);

        $this->execute(
            $this->buildCommand()
        );
    }

    /**
     * @param int|float $value
     * @param string    $unit
     *
     * @return $this
     */
    private function timeout($value, $unit = self::TIMEUNIT_SECOND)
    {
        $this->assertPositiveNumber($value);

        $this->valueArguments['timeout'] = $this->convertTimeUnit(
            $value,
            $unit,
            self::TIMEUNIT_MILLISECOND
        );

        return $this;
    }
}
