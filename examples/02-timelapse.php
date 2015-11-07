<?php

require __DIR__ . '/../vendor/autoload.php';

use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

$camera->flip()
    ->exposure(Raspistill::EXPOSURE_BACKLIGHT)
    ->ISO(600)
    ->quality(50);

// Note you should specify %04d at the point in the filename where you want a frame count number to appear.
// e.g. 'image%04d.jpg' will save files with names image0001.jpg, image0002.jpg, image003.jpg etc.

// take picture every ten seconds for two minutes
$camera->startTimelapse('image%04d.jpg', 10, 120);

// Timelapse: take picture every five minutes for 24 hours
$camera->startTimelapse('timelapse%04d.jpg', 5, 1440, Raspistill::TIMEUNIT_MINUTE);

// Burst: take pictures as fast as possible (~ 30ms interval) for three seconds
$camera->startTimelapse('burst%04d.jpg', 0, 3);
