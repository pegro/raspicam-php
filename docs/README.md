# Raspicam PHP Documentation

## Table of Contents

* [Raspistill](#raspistill)
    * [__construct](#__construct)
    * [quality](#quality)
    * [raw](#raw)
    * [timeout](#timeout)
    * [encoding](#encoding)
    * [width](#width)
    * [height](#height)
    * [addExif](#addexif)
    * [setExif](#setexif)
    * [disableExif](#disableexif)
    * [linkLatest](#linklatest)
    * [takePicture](#takepicture)
    * [startTimelapse](#starttimelapse)
    * [flip](#flip)
    * [verticalFlip](#verticalflip)
    * [horizontalFlip](#horizontalflip)
    * [sharpness](#sharpness)
    * [contrast](#contrast)
    * [brightness](#brightness)
    * [saturation](#saturation)
    * [ISO](#iso)
    * [exposureCompensation](#exposurecompensation)
    * [exposure](#exposure)
    * [whiteBalance](#whitebalance)
    * [effect](#effect)
    * [metering](#metering)
    * [dynamicRangeCompression](#dynamicrangecompression)
    * [rotate](#rotate)
    * [shutterSpeed](#shutterspeed)
    * [sensorMode](#sensormode)
    * [getOutput](#getoutput)

## Raspistill

Class that abstracts the usage of raspistill cli utility that is used to take photos with the
Raspberry Pi camera module.



* Full name: Cvuorinen\Raspicam\Raspistill
* Parent class: Cvuorinen\Raspicam\Raspicam



**See Also:**

* https://www.raspberrypi.org/documentation/raspbian/applications/camera.md 



### __construct

Raspistill constructor.

```php
Raspistill::__construct( array $options )
```

**Example:**
```php
$camera = new Raspistill([
    'rotate' => 90,
    'width' => 640,
    'height' => 480,
    'exposure' => Raspistill::EXPOSURE_NIGHT,
]);
```



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $options | **array** | Associative array where key=method name & value=parameter passed to the method |




---


### quality

Set jpeg quality (0 to 100)

```php
Raspistill::quality( int $value ): Raspistill
```

Quality 100 is almost completely uncompressed. 75 is a good all round value



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Quality |




---


### raw

Add raw bayer data to jpeg metadata

```php
Raspistill::raw( bool $value ): Raspistill
```

This option inserts the raw Bayer data from the camera in to the JPEG metadata



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **bool** | Optional. TRUE enables raw, FALSE disables (default=TRUE) |




---


### timeout

Time before takes picture, default is 5 seconds

```php
Raspistill::timeout( int|float $value, string $unit ): Raspistill
```

The camera will run for this length of time, then take the picture.
Unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
`Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int&#124;float** | Timeout |
| $unit | **string** | Optional. Time unit for $value Default: <code>Raspicam::TIMEUNIT_SECOND</code> |




---


### encoding

Encoding to use for output file

```php
Raspistill::encoding( string $mode ): Raspistill
```

Note that unaccelerated image types (gif, png, bmp) will take much longer to save than JPG which is hardware
accelerated. Also note that the filename suffix is completely ignored when deciding the encoding of a file.

Possible options are:
- 'jpg' `Raspicam::ENCODING_JPG` (default)
- 'bmp' `Raspicam::ENCODING_BMP`
- 'gif' `Raspicam::ENCODING_GIF`
- 'png' `Raspicam::ENCODING_PNG`



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** | Encoding mode |




---


### width

Set image width in pixels

```php
Raspistill::width( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Width |




---


### height

Set image height in pixels

```php
Raspistill::height( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Height |




---


### addExif

Add EXIF tag to apply to pictures.

```php
Raspistill::addExif( string $tagName, mixed $value ): Raspistill
```

Allows the insertion of specific exif tags in to the JPEG image. You can have up to 32 exif tag entries.
This is useful for things like adding GPS metadata. See exif documentation for more details on the range of
tags available.

Note that a small subset of these tags will be set automatically by the camera system, but will be overridden
by any exif options set by this method.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $tagName | **string** | EXIF tag name |
| $value | **mixed** | Tag value |




---


### setExif

Add multiple EXIF tags at once

```php
Raspistill::setExif( array $tags ): Raspistill
```

**Example:**
```php
$camera->setExif([
    'IFD0.Artist' => 'Boris',
    'GPS.GPSAltitude' => '1235/10',
    'EXIF.MakerNote' => 'Testing',
]);
```



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $tags | **array** | Associative array where key=EXIF tag name & value=tag value |



**See Also:**

* \Cvuorinen\Raspicam\Raspistill::addExif 

---


### disableExif

Prevent any EXIF information being stored in the file.

```php
Raspistill::disableExif(  ): Raspistill
```

This reduces the file size slightly.






---


### linkLatest

Link latest picture to filename.

```php
Raspistill::linkLatest( string $filename ): Raspistill
```

Make a file system link under this name to the latest picture.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $filename | **string** |  |




---


### takePicture

Take a picture and save with the given filename

```php
Raspistill::takePicture( string $filename )
```

**Example:**
```php
// Take picture with default configurations
$camera->takePicture('pic1.jpg');
```



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $filename | **string** |  |




---


### startTimelapse

Take pictures with timelapse mode.

```php
Raspistill::startTimelapse( string $filename, int|float $interval, int|float $length, string $timeUnit )
```

Note you should specify %04d at the point in the filename where you want a frame count number to appear.
e.g. 'image%04d.jpg'. Note that the %04d indicates a 4 digit number with leading zero's added to pad to the
required number of digits. So, for example, %08d would result in an 8 digit number.

If a timelapse value of 0 is entered, the application will take pictures as fast as possible. Note there is an
minimum enforced pause of 30ms between captures to ensure that exposure calculations can be made.

Time unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
`Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.

**Example:**
```php
// take picture every ten seconds for two minutes
$camera->startTimelapse('image%04d.jpg', 10, 120);
```



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $filename | **string** |  |
| $interval | **int&#124;float** | Time between shots. |
| $length | **int&#124;float** | Time how long to keep taking pictures. |
| $timeUnit | **string** | Optional. Time unit for $interval and $length. Default: <code>Raspicam::TIMEUNIT_SECOND</code> |




---


### flip

Flips the image both vertically and horizontally

```php
Raspistill::flip( bool $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **bool** | Optional. TRUE enables flip, FALSE disables (default=TRUE) |




---


### verticalFlip

Flips the image vertically

```php
Raspistill::verticalFlip( bool $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **bool** | Optional. TRUE enables flip, FALSE disables (default=TRUE) |




---


### horizontalFlip

Flips the image horizontally

```php
Raspistill::horizontalFlip( bool $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **bool** | Optional. TRUE enables flip, FALSE disables (default=TRUE) |




---


### sharpness

Set the sharpness of the image, 0 is the default (-100 to 100)

```php
Raspistill::sharpness( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Sharpness |




---


### contrast

Set the contrast of the image, 0 is the default (-100 to 100)

```php
Raspistill::contrast( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Contrast |




---


### brightness

Set the brightness of the image, 50 is the default. 0 is black, 100 is white.

```php
Raspistill::brightness( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Brightness |




---


### saturation

Set the colour saturation of the image. 0 is the default (-100 to 100)

```php
Raspistill::saturation( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Saturation |




---


### ISO

Sets the ISO to be used for captures. Range is 100 to 800.

```php
Raspistill::ISO( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | ISO |




---


### exposureCompensation

Set the exposure (EV) compensation of the image. Range is -10 to +10, default is 0.

```php
Raspistill::exposureCompensation( int $value ): Raspistill
```





**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int** | Exposure compensation |




---


### exposure

Set exposure mode

```php
Raspistill::exposure( string $mode ): Raspistill
```

Possible options are:
 - 'auto' `Raspicam::EXPOSURE_AUTO` Use automatic exposure mode
 - 'night' `Raspicam::EXPOSURE_NIGHT` Select setting for night shooting
 - 'nightpreview' `Raspicam::EXPOSURE_NIGHTPREVIEW`
 - 'backlight' `Raspicam::EXPOSURE_BACKLIGHT` Select setting for back lit subject
 - 'spotlight' `Raspicam::EXPOSURE_SPOTLIGHT`
 - 'sports' `Raspicam::EXPOSURE_SPORTS` Select setting for sports (fast shutter etc)
 - 'snow' `Raspicam::EXPOSURE_SNOW` Select setting optimised for snowy scenery
 - 'beach' `Raspicam::EXPOSURE_BEACH` Select setting optimised for beach
 - 'verylong' `Raspicam::EXPOSURE_VERYLONG` Select setting for long exposures
 - 'fixedfps' `Raspicam::EXPOSURE_FIXEDFPS` Constrain fps to a fixed value
 - 'antishake' `Raspicam::EXPOSURE_ANTISHAKE` Antishake mode
 - 'fireworks' `Raspicam::EXPOSURE_FIREWORKS` Select settings

Note that not all of these settings may be implemented, depending on camera type.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** | Exposure mode |




---


### whiteBalance

Set Automatic White Balance (AWB) mode

```php
Raspistill::whiteBalance( string $mode ): Raspistill
```

Possible options are:
 - 'off' `Raspicam::WHITE_BALANCE_OFF` Turn off white balance calculation
 - 'auto' `Raspicam::WHITE_BALANCE_AUTO` Automatic mode (default)
 - 'sun' `Raspicam::WHITE_BALANCE_SUN` Sunny mode
 - 'cloud' `Raspicam::WHITE_BALANCE_CLOUD` Cloudy mode
 - 'shade' `Raspicam::WHITE_BALANCE_SHADE` Shaded mode
 - 'tungsten' `Raspicam::WHITE_BALANCE_TUNGSTEN` Tungsten lighting mode
 - 'fluorescent' `Raspicam::WHITE_BALANCE_FLUORESCENT` Fluorescent lighting mode
 - 'incandescent' `Raspicam::WHITE_BALANCE_INCANDESCENT` Incandescent lighting mode
 - 'flash' `Raspicam::WHITE_BALANCE_FLASH` Flash mode
 - 'horizon' `Raspicam::WHITE_BALANCE_HORIZON` Horizon mode

Note that not all of these settings may be implemented, depending on camera type.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** | AWB mode |




---


### effect

Set an effect to be applied to the image

```php
Raspistill::effect( string $mode ): Raspistill
```

Possible options are:
- 'none' `Raspicam::EFFECT_NONE` NO effect (default)
- 'negative' `Raspicam::EFFECT_NEGATIVE` Negate the image
- 'solarise' `Raspicam::EFFECT_SOLARISE` Solarise the image
- 'posterise' `Raspicam::EFFECT_POSTERISE` Posterise the image
- 'whiteboard' `Raspicam::EFFECT_WHITEBOARD` Whiteboard effect
- 'blackboard' `Raspicam::EFFECT_BLACKBOARD` Blackboard effect
- 'sketch' `Raspicam::EFFECT_SKETCH` Sketch style effect
- 'denoise' `Raspicam::EFFECT_DENOISE` Denoise the image
- 'emboss' `Raspicam::EFFECT_EMBOSS` Emboss the image
- 'oilpaint' `Raspicam::EFFECT_OILPAINT` Apply an oil paint style effect
- 'hatch' `Raspicam::EFFECT_HATCH` Hatch sketch style
- 'gpen' `Raspicam::EFFECT_GPEN`
- 'pastel' `Raspicam::EFFECT_PASTEL` A pastel style effect
- 'watercolour' `Raspicam::EFFECT_WATERCOLOUR` A watercolour style effect
- 'film' `Raspicam::EFFECT_FILM` Film grain style effect
- 'blur' `Raspicam::EFFECT_BLUR` Blur the image
- 'saturation' `Raspicam::EFFECT_SATURATION` Colour saturate the image
- 'colourswap' `Raspicam::EFFECT_COLOURSWAP` Not fully implemented
- 'washedout' `Raspicam::EFFECT_WASHEDOUT` Not fully implemented
- 'colourpoint' `Raspicam::EFFECT_COLOURPOINT` Not fully implemented
- 'colourbalance' `Raspicam::EFFECT_COLOURBALANCE` Not fully implemented
- 'cartoon' `Raspicam::EFFECT_CARTOON` Not fully implemented

Note that not all of these settings may be available in all circumstances.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** |  |




---


### metering

Set metering mode

```php
Raspistill::metering( string $mode ): Raspistill
```

Possible options are:
- 'average' `Raspicam::METERING_AVERAGE` Average the whole frame for metering
- 'spot' `Raspicam::METERING_SPOT` Spot metering
- 'backlit' `Raspicam::METERING_BACKLIT` Assume a backlit image
- 'matrix' `Raspicam::METERING_MATRIX` Matrix metering



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** |  |




---


### dynamicRangeCompression

Enable/Disable Dynamic Range compression

```php
Raspistill::dynamicRangeCompression( string $mode ): Raspistill
```

DRC changes the images by increasing the range of dark areas of the image, and decreasing the brighter areas.
This can improve the image in low light areas.

Possible options are:
- 'off' `Raspicam::DRC_OFF` (default)
- 'low' `Raspicam::DRC_LOW`
- 'medium' `Raspicam::DRC_MEDIUM`
- 'high' `Raspicam::DRC_HIGH`



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **string** |  |




---


### rotate

Set image rotation

```php
Raspistill::rotate( int $degrees ): Raspistill
```

Only 0, 90, 180 and 270 degree rotations are supported.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $degrees | **int** | Degrees |




---


### shutterSpeed

Set the shutter speed to the specified time.

```php
Raspistill::shutterSpeed( int|float $value, string $unit ): Raspistill
```

There is currently an upper limit of approximately 6000000us (6000ms, 6s) past which operation is undefined.
Unit can be one of: `Raspicam::TIMEUNIT_MINUTE`, `Raspicam::TIMEUNIT_SECOND`,
`Raspicam::TIMEUNIT_MILLISECOND`, `Raspicam::TIMEUNIT_MICROSECOND`.



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $value | **int&#124;float** | Shutter speed |
| $unit | **string** | Optional. Time unit for $value Default: <code>Raspicam::TIMEUNIT_SECOND</code> |




---


### sensorMode

Sets a specified sensor mode, disabling the automatic selection.

```php
Raspistill::sensorMode( int $mode ): Raspistill
```

Possible values are:

| Mode | Size      | Aspect ratio | Frame rates | FOV     | Binning       |
| -----|-----------|--------------|-------------|---------|---------------|
| 0    |                  automatic selection                             |
| 1    | 1920x1080 | 16:9         | 1-30fps     | Partial | None          |
| 2    | 2592x1944 | 4:3          | 1-15fps     | Full    | None          |
| 3    | 2592x1944 | 4:3          | 0.1666-1fps | Full    | None          |
| 4    | 1296x972  | 4:3          | 1-42fps     | Full    | 2x2           |
| 5    | 1296x730  | 16:9         | 1-49fps     | Full    | 2x2           |
| 6    | 640x480   | 4:3          | 42.1-60fps  | Full    | 2x2 plus skip |
| 7    | 640x480   | 4:3          | 60.1-90fps  | Full    | 2x2 plus skip |



**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| $mode | **int** |  |




---


### getOutput



```php
Raspistill::getOutput(  ): string
```






**Return Value:**

Output from the last executed CLI command



---


--------
> This document was automatically generated from source code comments on 2015-11-13 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-md](https://github.com/cvuorinen/phpdoc-md)
