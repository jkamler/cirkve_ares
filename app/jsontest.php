<?php

require_once "app_class/geoJSONClass.php";

$myJSON = new geoJSONClass;
$res = $myJSON->getData("1 limit 5");
if (!$res){
    echo "posral se myJSON->getData";
}
//var_dump($myJSON->writeGeoJSON($res));


?>
