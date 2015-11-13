# Raspicam PHP

[![Build Status](https://travis-ci.org/cvuorinen/raspicam-php.svg?branch=master)](https://travis-ci.org/cvuorinen/raspicam-php)

Raspicam PHP is a library to control the [Raspberry Pi Camera Module](https://www.raspberrypi.org/documentation/raspbian/applications/camera.md) with PHP. It is a wrapper around the command line tool [raspistill](https://www.raspberrypi.org/documentation/usage/camera/raspicam/raspistill.md).

# Requirements

You need a Raspberry Pi running Raspbian and the Camera Module.
On the Raspberry Pi you also need to have PHP and composer installed.

First, install and enable the Camera on the Raspberry Pi: [Instructions](https://www.raspberrypi.org/documentation/configuration/camera.md)

If you don't have PHP installed on the Raspberry Pi yet, you can install it by running:

```bash
sudo apt-get install php5
```

Then install [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx):

```bash
curl -sS https://getcomposer.org/installer | php
```

# Install

First check requirements above.

Install with composer:

```bash
composer require cvuorinen/raspicam-php
```

Add to your php file (adjust path accordingly if file not in project root):

```php
require 'vendor/autoload.php';
```

# Usage

## Take picture

```php
use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

$camera->takePicture('pic.jpg');
```

### Fluent interface

```php
use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();
$camera->timeout(1)
    ->rotate(90)
    ->exposure(Raspistill::EXPOSURE_NIGHT)
    ->quality(85);

$camera->takePicture('pic.jpg');
```

### Constructor options array

```php
use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill([
    'timeout' => 1,
    'rotate' => 90,
    'exposure' => Raspistill::EXPOSURE_NIGHT,
    'quality' => 85,
]);

$camera->takePicture('pic.jpg');
```

## Timelapse

```php
use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

// take picture every ten seconds for two minutes
$camera->startTimelapse('image%04d.jpg', 10, 120);
```

More complex examples can be found in the [examples](examples) directory.

# Documentation

Documentation can be found in the the [docs](docs) directory.

# License

Released under the MIT License (MIT).
See [LICENSE](LICENSE) for more information.

