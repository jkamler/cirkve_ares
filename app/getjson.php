<?php
include "app_class/geoJSONClass.php";

$myJSON = new geoJSONClass;
/*
$data = '
{
  "type": "FeatureCollection",
  "features": [
    { "type": "Feature", "properties": { "title": "Dolni dira na morave" }, "geometry": { "type": "Point", "coordinates": [ -510501.05, -1164540.03 ] } },
    { "type": "Feature", "properties": { "title": "Horni dira v cechach" }, "geometry": { "type": "Point", "coordinates": [ -750000.00, -1100000.00 ] } }
  ]
}
';
*/
$data = $myJSON->getData("cirkve_aktivni_spatial", "1");
//var_dump($data);

$result = $myJSON->writeGeoJSON($data);
echo $result;
//var_dump($result);

 ?>