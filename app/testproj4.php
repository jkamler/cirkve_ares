<?php
include("app_class/proj4php-master/vendor/autoload.php");

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

// Initialise Proj4
$proj4 = new Proj4php();

// Create two different projections.
$projSJTSK    = new Proj('EPSG:5514', $proj4);
$projMercator  = new Proj('EPSG:3857', $proj4);

// Create a point.
$pointSrc = new Point(-887965.59, -1023769.65, $projSJTSK);
echo "Source: " . $pointSrc->toShortString() . " in S-JTSK <br>";

// Transform the point between datums.
$pointDest = $proj4->transform($projMercator, $pointSrc);
var_dump($pointDest->x);
echo "Conversion: " . $pointDest->toShortString() . " in Mercator<br><br>";
?>
