<?php

namespace Cvuorinen\Raspicam;

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
     * {@inheritdoc}
     */
    protected function getExecutable()
    {
        return self::COMMAND;
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
     * Encoding to use for output file
     *
     * Note that unaccelerated image types (gif, png, bmp) will take much longer to save than JPG which is hardware
     * accelerated. Also note that the filename suffix is completely ignored when deciding the encoding of a file.
     *
     * Possible options are:
     * - 'jpg' (default)
     * - 'bmp'
     * - 'gif'
     * - 'png'
     *
     * @param string $mode
     *
     * @return $this
     */
    public function encoding($mode)
    {
        $encodings = [
            self::ENCODING_JPG,
            self::ENCODING_BMP,
            self::ENCODING_GIF,
            self::ENCODING_PNG,
        ];

        $this->assertInArray($mode, $encodings);

        $this->valueArguments['encoding'] = $mode;

        return $this;
    }

    /**
     * Set image width in pixels
     *
     * @param int $value
     *
     * @return $this
     */
    public function width($value)
    {
        // Documentation doesn't mention min/max values, so these are based on my own testing
        $this->assertIntBetween($value, 16, 9000);

        $this->valueArguments['width'] = $value;

        return $this;
    }

    /**
     * Set image height in pixels
     *
     * @param int $value
     *
     * @return $this
     */
    public function height($value)
    {
        // Documentation doesn't mention min/max values, so these are based on my own testing
        $this->assertIntBetween($value, 16, 20000);

        $this->valueArguments['height'] = $value;

        return $this;
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
}
