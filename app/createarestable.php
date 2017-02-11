<?php
include "app_class/dataClass.php";
$myDataClass = new dataClass;
if ($myDataClass->createARESTable()) {
  echo "Tabulka pro ARES byla vytvorena <BR>";
} else {
  echo "Chyba - Tabulka pro ARES nebyla vytvorena <BR>";
}

?>
