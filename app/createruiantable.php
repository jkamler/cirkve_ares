<?php
include "app_class/RUIANClass.php";
$myRUIANClass = new RUIANClass;
if ($myRUIANClass->createRUIANTable("ruian_test")){
  echo "Tabulka pro RUIAN byla vytvorena <BR>";
} else {
  echo "Chyba - Tabulka pro RUIAN nebyla vytvorena <BR>";
}
?>
