<?php

require __DIR__ . '/../vendor/autoload.php';

use Cvuorinen\Raspicam\Raspistill;

$camera = new Raspistill();

$filename = date('Y-m-d_H:i:s') . '.jpg';

try {
    $camera->takePicture($filename);

    echo 'Saved picture to ' . $filename . "\n";

} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
