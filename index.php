<?php
	include "app_class/dataClass.php";
	$myDataClass = new dataClass;
/*
$ICs = file('ic_cirkve');

foreach ($ICs as $myIC) {
	sleep(3);
	$resultData = $myDataClass->getDataKurzy(trim($myIC));
	if (!$resultData) { // chyba
//		exit;
		echo "chyba getData";
	}
	$parsedData = $myDataClass->parseDataKurzy($resultData);
	if (!$parsedData) {
		echo "chyba parseData";
	}
	
	$resultInsert = $myDataClass->insertDataKurzy($parsedData);
	if (!$resultInsert) { // chyba
//		exit;
		echo "chyba insertData";
	}	
}
*/

//$result = $myDataClass->parseData($myDataClass->getData(73633119));

//$myDataClass->readCirkevniDBKurzy();

/*
$ICs = file('ic_cirkve_AA');

foreach ($ICs as $myIC) {
//	sleep(1);
	$parsedData = $myDataClass->parseDataARES(trim($myIC));
	if (!$parsedData) {
		echo "chyba parseData";
	}
	
	$resultInsert = $myDataClass->insertDataARES($parsedData);
	if (!$resultInsert) { // chyba
		echo "chyba insertData";
	}	
}
*/
	$myDataClass->readCirkevniDBARES();
?>
