<?php

namespace Cvuorinen\Raspicam;

/**
 * Class that abstracts the usage of raspistill cli utility that is used to take photos with the
 * Raspberry Pi camera module.
 *
 * @link https://www.raspberrypi.org/documentation/raspbian/applications/camera.md
 *
 * @package Cvuorinen\Raspicam
 */
class Raspistill extends Raspicam
{
    const COMMAND = 'raspistill';

    const EXIF_NONE = 'none';

    /**
     * Raspistill constructor.
     *
     * **Example:**
     * ```php
     * $camera = new Raspistill([
     *     'rotate' => 90,
     *     'width' => 640,
     *     'height' => 480,
     *     'exposure' => Raspistill::EXPOSURE_NIGHT,
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
     * Set jpeg quality (0 to 100)
     *
     * Quality 100 is almost completely uncompressed. 75 is a good all round value
     *
     * @param int $value Quality
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
     * @param bool $value Optional. TRUE enables raw, FALSE disables (default=TRUE)
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
     * Unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
     * `Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.
     *
     * @param int|float $value Timeout
     * @param string    $unit  Optional. Time unit for $value Default: `Raspicam::TIMEUNIT_SECOND`
     *
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
     * - 'jpg' `Raspicam::ENCODING_JPG` (default)
     * - 'bmp' `Raspicam::ENCODING_BMP`
     * - 'gif' `Raspicam::ENCODING_GIF`
     * - 'png' `Raspicam::ENCODING_PNG`
     *
     * @param string $mode Encoding mode
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
     * @param int $value Width
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
     * @param int $value Height
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
     * Set starting frame number in output pattern(%d)
     *
     * @param int $value starting frame number
     *
     * @return $this
     */
    public function framestart($value)
    {
        // Documentation doesn't mention min/max values, so these are based on my own testing
        $this->assertIntBetween($value, 0, 99999);

        $this->valueArguments['framestart'] = $value;

        return $this;
    }

    /**
     * Add EXIF tag to apply to pictures.
     *
     * Allows the insertion of specific exif tags in to the JPEG image. You can have up to 32 exif tag entries.
     * This is useful for things like adding GPS metadata. See exif documentation for more details on the range of
     * tags available.
     *
     * Note that a small subset of these tags will be set automatically by the camera system, but will be overridden
     * by any exif options set by this method.
     *
     * @param string $tagName EXIF tag name
     * @param mixed  $value   Tag value
     *
     * @return $this
     */
    public function addExif($tagName, $value)
    {
        if (empty($tagName)) {
            throw new \InvalidArgumentException('Tag name required');
        }

        if ('' === (string) $value) {
            throw new \InvalidArgumentException('Non-empty value required');
        }

        if (!isset($this->valueArguments['exif']) || !is_array($this->valueArguments['exif'])) {
            $this->valueArguments['exif'] = [];
        }

        if (count($this->valueArguments['exif']) == 32) {
            throw new \OverflowException('Maximum of 32 EXIF tag entries allowed');
        }

        $this->valueArguments['exif'][] = $tagName . '=' . (string) $value;

        return $this;
    }

    /**
     * Add multiple EXIF tags at once
     *
     * **Example:**
     * ```php
     * $camera->setExif([
     *     'IFD0.Artist' => 'Boris',
     *     'GPS.GPSAltitude' => '1235/10',
     *     'EXIF.MakerNote' => 'Testing',
     * ]);
     * ```
     *
     * @see Raspistill::addExif
     *
     * @param array $tags Associative array where key=EXIF tag name & value=tag value
     *
     * @return $this
     */
    public function setExif(array $tags)
    {
        $this->valueArguments['exif'] = [];

        foreach ($tags as $tagName => $value) {
            $this->addExif($tagName, $value);
        }

        return $this;
    }

    /**
     * Prevent any EXIF information being stored in the file.
     *
     * This reduces the file size slightly.
     *
     * @return $this
     */
    public function disableExif()
    {
        $this->valueArguments['exif'] = self::EXIF_NONE;

        return $this;
    }

    /**
     * Link latest picture to filename.
     *
     * Make a file system link under this name to the latest picture.
     *
     * @param string $filename
     *
     * @return $this
     */
    public function linkLatest($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->valueArguments['latest'] = $filename;

        return $this;
    }

    /**
     * Take a picture and save with the given filename
     *
     * **Example:**
     * ```php
     * // Take picture with default configurations
     * $camera->takePicture('pic1.jpg');
     * ```
     *
     * @param string $filename
     */
    public function takePicture($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        # TODO check filepath and create missing dirs?

        $this->valueArguments['output'] = $filename;

        $this->run();
    }

    /**
     * Take pictures with timelapse mode.
     *
     * Note you should specify %04d at the point in the filename where you want a frame count number to appear.
     * e.g. 'image%04d.jpg'. Note that the %04d indicates a 4 digit number with leading zero's added to pad to the
     * required number of digits. So, for example, %08d would result in an 8 digit number.
     *
     * If a timelapse value of 0 is entered, the application will take pictures as fast as possible. Note there is an
     * minimum enforced pause of 30ms between captures to ensure that exposure calculations can be made.
     *
     * Time unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
     * `Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.
     *
     * **Example:**
     * ```php
     * // take picture every ten seconds for two minutes
     * $camera->startTimelapse('image%04d.jpg', 10, 120);
     * ```
     *
     * @param string    $filename
     * @param int|float $interval Time between shots.
     * @param int|float $length   Time how long to keep taking pictures.
     * @param string    $timeUnit Optional. Time unit for $interval and $length. Default: `Raspicam::TIMEUNIT_SECOND`
     */
    public function startTimelapse($filename, $interval, $length, $timeUnit = self::TIMEUNIT_SECOND)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->valueArguments['output'] = $filename;

        $this->timeout($length, $timeUnit);
        $this->timelapse($interval, $timeUnit);

        $this->start();
    }

    /**
     * @param int|float $value
     * @param string    $unit
     *
     * @return $this
     */
    private function timelapse($value, $unit = self::TIMEUNIT_SECOND)
    {
        $this->assertPositiveNumber($value);

        $this->valueArguments['timelapse'] = $this->convertTimeUnit(
            $value,
            $unit,
            self::TIMEUNIT_MILLISECOND
        );

        return $this;
    }
}
