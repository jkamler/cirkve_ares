<?php
include "app_class/geoJSONClass.php";

$myJSON = new geoJSONClass;

$cond = htmlspecialchars($_GET["query"]);

//$query = 'Nazev_CPO LIKE ' . '"%' . $cond . '%"';
$query = 'Nazev_CPO LIKE ' . '"%' . $cond . '%" OR Nazev_obce LIKE ' . '"%' . $cond . '%"';
//echo $query;
$data = $myJSON->getData("cirkve_aktivni_spatial", $query);
//var_dump($data);

$result = $myJSON->writeGeoJSON($data);
echo $result;
//var_dump($result);

 ?>
