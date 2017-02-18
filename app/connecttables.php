<?php
include "app_class/dataClass.php";
$myDataClass = new dataClass;
//$myDataClass->createTableCirkveAktivni("cirkve_aktivni");
//$myDataClass->connectCirkveWithRUIAN("cirkve_aktivni", "cirkve_aktivni_spatial", "Aktivní");
$myDataClass->connectCirkveWithRUIAN("cirkve", "cirkve_aktivni_spatial", "Aktivni");
?>
