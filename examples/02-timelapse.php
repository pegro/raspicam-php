<?php

require __DIR__ . '/../vendor/autoload.php';

use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

$camera->flip()
    ->exposure(Raspistill::EXPOSURE_BACKLIGHT)
    ->ISO(600)
    ->quality(50);

// take picture every ten seconds for two minutes
$camera->startTimelapse('image%04d.jpg', 10, 120);

// Timelapse: take picture every five minutes for 24 hours
$camera->startTimelapse('image%04d.jpg', 5, 1440, Raspistill::TIMEUNIT_MINUTE);

// Burst: take pictures as fast as possible (~ 30ms interval) for three seconds
$camera->startTimelapse('burst%04d.jpg', 0, 3);
