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

$i = 1;

$ICs = file('seznam_IC/ic_cirkve_A');

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

//	$myDataClass->readCirkevniDBARES();
/*
include "app_class/RUIANClass.php";

$myRUIANClass = new RUIANClass;
//$myRUIANClass->createRUIANTable();
$myRUIANClass->importRUIAN();
*/

//vytvoreni tabulky pro data z ARES
//$myDataClass->createARESTable();
/*	$myIC = 48489034;
	$parsedData = $myDataClass->parseDataARES(trim($myIC));
	if (!$parsedData) {
		echo "chyba parseData " . $myIC;
	}
	
	$resultInsert = $myDataClass->insertDataARES($parsedData);
	if (!$resultInsert) { // chyba
		echo "chyba insertData " . $myIC;
	}	
*/
?>