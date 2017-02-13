<?php
include "app_class/dataClass.php";
$myDataClass = new dataClass;

$i = 1;

$ICs = file('../seznam_IC/ic_cirkve_B');

foreach ($ICs as $myIC) {
//	sleep(1);
	$parsedData = $myDataClass->parseDataARES(trim($myIC));
	if (!$parsedData) {

		echo "<br>chyba parseData " . $myIC;
	}

	$resultInsert = $myDataClass->insertDataARES($parsedData);
	if (!$resultInsert) { // chyba
		echo "<br>chyba insertData " . $myIC;
	}
	echo "<br>I: " . $i . " ";
	$i++;
}

?>
