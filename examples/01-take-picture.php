<?php

require __DIR__ . '/../vendor/autoload.php';

use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

// Take picture with default values
$camera->takePicture('pic1.jpg');

// Configure camera with fluent interface
$camera->flip()
    ->brightness(65)
    ->contrast(45)
    ->raw(true)
    ->exposure(Raspistill::EXPOSURE_BACKLIGHT)
    ->effect(Raspistill::EFFECT_FILM)
    ->ISO(600)
    ->whiteBalance(Raspistill::WHITE_BALANCE_FLUORESCENT)
    ->quality(50)
    ->shutterSpeed(0.75)
    ->timeout(1);

$camera->takePicture('pic2.jpg');

// Configure camera with a constructor parameter
$camera2 = new Raspistill([
    'timeout' => 2.5,
    'rotate' => 90,
    'exposure' => Raspistill::EXPOSURE_NIGHT,
    'sharpness' => 85,
]);

$camera2->takePicture('pic3.jpg');
