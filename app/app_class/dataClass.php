<?php

//error_reporting( E_ALL );
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

set_time_limit(0);

class ExDataClassGetDataReadFile extends Exception {}
class ExDataClassInsertDataDBConnection extends Exception {}
class ExDataClassInsertDataDBInsert extends Exception {}
class ExDataClassLoadXML extends Exception {}
class	ExDataClassInsertDataDBCreateTableWithActive extends Exception {}

class dataClass {
	/************************************************************
				DATA from KURZY.CZ
	*************************************************************/

	/*
	KURZY
	Reads data of given IC and returns webpage
	@param: numeric $myIC, IC dane cirkve
	@return: boolean 0-error | string - HTML doc
	*/

	function getDataKuryz($myIC) {
		$str1 = "http://rejstrik-firem.kurzy.cz/" . $myIC;
		try {
			$result = file_get_contents($str1);
			if (!$result) {
				throw new ExDataClassGetDataReadFile;
			}
			return $result;
		}
		catch (ExDataClassGetDataReadFile $e) {
			echo "Chyba: nepovedlo se nacist data ze zdroje: " . $str1 . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
	}


	/*
	KURZY
	Reads HTML document, picks important info and creates associative array
	@param: string $data,  HTML document for given IC
	@return: array, associative array | boolean, 0 - error
	*/

	function parseDataKurzy($data) {
		$res_arr = array();

		try {
	   	$domOb = new DOMDocument();
			$html = $domOb->loadHTML($data);
			$domOb->preserveWhiteSpace = false;
			$container = $domOb->getElementById('orsmallinfotab');
			$container = $container->getElementsByTagName('tr');
			foreach ($container as $item) {
				$arr = explode(":", $item->nodeValue);
				$arr_index = trim(strtolower(self::CZ2US($arr[0])));
				$arr_value = trim($arr[1], " \t\n\r\0\x0B");
				if ($arr_index == "schranka" || $arr_index == "mapa") {
					continue;
				}
				$res_arr[$arr_index] = $arr_value;
			}
			return $res_arr;
		}
		catch (Exception $e) {
			echo "Exception " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch (Error $e) {
			echo "Error " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
	}



	/*
	KURZY
	Inserting data to DBMS
	@param array $data - associative array with items
	@return boolean 1 - OK | 0 - error
	*/

	function insertDataKurzy($data){
		try{
			require_once "configClass.php";
			$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEKURZY);
			if (!$conn) {
				throw new ExDataClassInsertDataDBConnection;
			}

			mysqli_set_charset($conn, 'utf8');

			$sql = "INSERT INTO cirkev(nazev, ic, forma, adresa) VALUES ('" . $data["nazev"] . "', " . $data["ico"] . ", ' " . $data["forma"] . " ', '" . $data["adresa"] . "')";
			if (!mysqli_query($conn, $sql)) {
				throw new ExDataClassInsertDataDBInsert;
			}
			mysqli_close($conn);
			return 1;
		}
		catch(ExDataClassInsertDataDBConnection $e) {
			echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(ExDataClassInsertDataDBInsert $e) {
			mysqli_close($conn);
			echo "Chyba: nepovedlo se provest dotaz: " . $sql . "<br>" . mysqli_error($conn) . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Exception $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Error $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}

	}

	/*
	KURZY
	Reads and prints data
	*/
	function readCirkevniDBKurzy() {
		require_once "configClass.php";
		$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEKURZY);
		$sql = "SELECT * FROM cirkev;";
		$result = mysqli_query($conn, $sql);
   	echo "<table>\n";
	   while($row = mysqli_fetch_assoc($result)) {
	   	echo "<tr>";
      	echo "<th>id: " . $row["id"]. "</th><th>" . $row["nazev"]. "</th><th>" . $row["ic"]. "</th><th>" . $row["forma"]. "</th><th>" . $row["adresa"] . "</th>";
	   	echo "</tr>\n";

    	}
      echo "\n</table>";
		mysqli_close($conn);
	}

/****************************************************************
					DATA from ARES
*****************************************************************/

	function createARESTable() {
		/*column "Zrizovatel_text" does not exist, it will be updated
			PSC_RES - at RES is different PSC then in CNS
		*/
		$sql = "CREATE TABLE `cirkve` (
	  `ICO` varchar(8) COLLATE utf8_czech_ci NOT NULL,
	  `Stav_subjektu_RCNS` varchar(200) COLLATE utf8_czech_ci,
	  `Nazev_CPO` varchar(200) COLLATE utf8_czech_ci,
	  `Typ_CNS` varchar(200) COLLATE utf8_czech_ci,
	  `Zkr_statu` varchar(200) COLLATE utf8_czech_ci,
	  `Nazev_PF` varchar(200) COLLATE utf8_czech_ci,
	  `ID_adresy` decimal(10,0) DEFAULT NULL,
	  `Nazev_obce` varchar(200) COLLATE utf8_czech_ci,
		`Nazev_casti_obce` varchar(200) COLLATE utf8_czech_ci,
	  `Nazev_ulice` varchar(200) COLLATE utf8_czech_ci,
	  `Cislo_do_adresy` varchar(200) COLLATE utf8_czech_ci,
	  `Cislo_do_adresy_RES` varchar(200) COLLATE utf8_czech_ci,
	  `PSC` int(11) DEFAULT NULL,
	  `PSC_RES` int(11) DEFAULT NULL,
	  `Zrizovatel` varchar(200) COLLATE utf8_czech_ci,
	  `Zrizovatel_text` varchar(200) COLLATE utf8_czech_ci,
	  `Zvlastni_prava` varchar(1000) COLLATE utf8_czech_ci,
	  `web` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,		/* column web added and not tested */
	  `Datum_vzniku` varchar(50) COLLATE utf8_czech_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";

		try{
			require_once "configClass.php";
			$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEARES);
			if (!$conn) {
				throw new ExDataClassInsertDataDBConnection;
			}

			if (!mysqli_query($conn, $sql)) {
				throw new ExDataClassInsertDataDBInsert;
			}
			mysqli_close($conn);
			return 1;
		}
		catch(ExDataClassInsertDataDBConnection $e) {
			echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(ExDataClassInsertDataDBInsert $e) {
			mysqli_close($conn);
			echo "Chyba: nepovedlo se provest dotaz: " . $sql . "<br>" . mysqli_error($conn) . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Exception $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Error $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
	}

	/*
	ARES
	Reads XML document and creates associative array

	@param: numeric $myIC, IC dane cirkve
	@return: array; associative array | boolean 0-error
	*/

	function parseDataARES($myIC) {
		$res_arr = array();
		//data from Registru církví a náboženských společností
		//we are interested in this XML nodes
		$XMLnodes = array("ICO", "Stav_subjektu_RCNS", "Nazev_CPO", "Typ_CNS", "Zkr_statu", "Nazev_PF", "ID_adresy", "Nazev_obce", "Nazev_ulice", "Cislo_do_adresy", "PSC", "Zrizovatel", "Zvlastni_prava", "Datum_vzniku");

		$str = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_cns.cgi?ico=" . $myIC . "&ver=1.0.1";

		try {
	   	$domOb = new DOMDocument();
			$domOb->preserveWhiteSpace = false;
			if (!$domOb->load($str)) {
				throw new ExDataClassLoadXML;
			}
			//looping through array
			foreach ($XMLnodes as $nodeName) {
				 //there is not given element, continue to next
				 if ($domOb->getElementsByTagName($nodeName)->length == 0 ) {
					 $res_arr[$nodeName] = ''; //or NULL?
					 continue;
				 }
				 $value = $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
				 //adding node name and node value to associative array
				 $res_arr[$nodeName] = $value;
			}

			//data from statistického registru RES
			//we are interested in this node ... in previous register is not Nazev_casti_obce
			// - without this info I am unable connect cirkevni table with data from RUIAN.
			//In RES is different PSC
			$XMLnodes = array("Cislo_domovni", "Nazev_ulice", "Nazev_casti_obce", "PSC");
			//	  Cislo_do_adresy_RES

			$str = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_res.cgi?ico=" . $myIC . "&ver=1.0.0";

	   	$domOb = new DOMDocument();
			$domOb->preserveWhiteSpace = false;
			if (!$domOb->load($str)) {
				throw new ExDataClassLoadXML;
			}
			//looping through array
			foreach ($XMLnodes as $nodeName) {
				//if i have node Cislo_domovni
				if (($domOb->getElementsByTagName("Cislo_domovni")->length != 0) && ($nodeName == "Cislo_domovni")) {
					//and there is also node Cislo_orientacni
					if ($domOb->getElementsByTagName("Cislo_orientacni")->length != 0) {
						$res_arr["Cislo_do_adresy_RES"] = $domOb->getElementsByTagName("Cislo_domovni")->item(0)->nodeValue . '/' . $domOb->getElementsByTagName("Cislo_orientacni")->item(0)->nodeValue;
						continue;
					} else {
						$res_arr["Cislo_do_adresy_RES"] = $domOb->getElementsByTagName("Cislo_domovni")->item(0)->nodeValue;
						continue;
					}
				}
				if ($domOb->getElementsByTagName("$nodeName")->length == 0 && $nodeName == "Cislo_domovni") {
					$res_arr["Cislo_do_adresy_RES"] = $res_arr["Cislo_do_adresy"];
					continue;
				}

				//if in previous XML do not contain Nazev_ulice and if this info is in RES ...
				if (($res_arr["Nazev_ulice"] == "") && ($domOb->getElementsByTagName("Nazev_ulice")->length != 0) && ($nodeName == "Nazev_ulice")) {
					echo $nodeName . "<br>";
					echo $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
					$res_arr["Nazev_ulice"] = $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
					continue;
				}

				//in RES is not present node Nazev_casti_obce
				if (($domOb->getElementsByTagName("Nazev_casti_obce")->length == 0) && ($nodeName == "Nazev_casti_obce")) {
					$res_arr[$nodeName] = $res_arr["Nazev_obce"];
					continue;
				}
				if (($domOb->getElementsByTagName("Nazev_casti_obce")->length != 0) && ($nodeName == "Nazev_casti_obce")) {
					$res_arr[$nodeName] = $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
					continue;
				}


				if ($domOb->getElementsByTagName($nodeName)->length != 0) {
					$value = $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
				}

				if ($nodeName == 'PSC' && $domOb->getElementsByTagName($nodeName)->length == 0 /*$value == ''*/) { //if in RES is not set PSC, just copy PSC from previous source. PSC from RES is used for connection tables
					$value = $res_arr["PSC"];
				}
				if ($nodeName == "PSC") {
				 	$res_arr["PSC_RES"] = $value;
					continue;
				}
	//			echo $nodeName . "<br>";
	//			echo $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;

				 //adding node name and node value to associative array
//				 $res_arr[$nodeName] = $value;
			}
			return $res_arr;
		}
		catch (ExDataClassLoadXML $e) {
			echo "Exception - nepovedlo se nacist XMLko" . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			$file = '../err.txt';
			$current = file_get_contents($file);
			$current .= "Exception - nepovedlo se nacist XMLko \n" . $myIC . "\n";
			file_put_contents($file, $current);
			return 0;
		}
		catch (Exception $e) {
			echo "My Exception: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch (Error $e) {
			echo "My Error: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
	}



	/*
	ARES
	Inserting data to DB

	@param array $data - associative array
	@return boolean, 1 - OK | 0 - error
	*/

	function insertDataARES($data){
		try{
			require_once "configClass.php";
			$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEARES);
			if (!$conn) {
				throw new ExDataClassInsertDataDBConnection;
			}

			mysqli_set_charset($conn, 'utf8');

			$sql = "INSERT INTO cirkve(ICO, Stav_subjektu_RCNS, Nazev_CPO, Typ_CNS, Zkr_statu, Nazev_PF, ID_adresy, Nazev_obce, Nazev_casti_obce, Nazev_ulice, Cislo_do_adresy, Cislo_do_adresy_RES, PSC, PSC_RES, Zrizovatel, Zvlastni_prava, Datum_vzniku)
			VALUES ('" . $data["ICO"] . "', '" . $data["Stav_subjektu_RCNS"] . "', '" . $data["Nazev_CPO"] . "', '" . $data["Typ_CNS"] . "', '" . $data["Zkr_statu"] . "', '" . $data["Nazev_PF"] . "', "
			. $data["ID_adresy"] . ", '" . $data["Nazev_obce"] . "' , '" . $data["Nazev_casti_obce"] . "', '" . $data["Nazev_ulice"] . "', '" . $data["Cislo_do_adresy"] . "', '" . $data["Cislo_do_adresy_RES"] . "', "
			. $data["PSC"] . ", " . $data["PSC_RES"] . ", '" . $data["Zrizovatel"] . "', '" . $data["Zvlastni_prava"] . "', '" . $data["Datum_vzniku"] . "')";
			if (!mysqli_query($conn, $sql)) {
				throw new ExDataClassInsertDataDBInsert;
			}
			mysqli_close($conn);
			return 1;
		}
		catch(ExDataClassInsertDataDBConnection $e) {
			echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(ExDataClassInsertDataDBInsert $e) {
			mysqli_close($conn);
			$file = '../err.txt';
			$current = file_get_contents($file);
			$current .= "Exception - nepovedlo se vlozit IC od DB \n" . $data["ICO"] . "\n";
			$current .= $sql;
			file_put_contents($file, $current);

			echo "Chyba: nepovedlo se provest dotaz: " . $sql . "<br>" . mysqli_error($conn) . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Exception $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Error $e) {
			echo "Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}

	}

	/*
	ARES
	Reads and prints data from RDBMS
	*/

	function readCirkevniDBARES() {
		require_once "configClass.php";
		$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEARES);
		mysqli_set_charset($conn, 'utf8');
		$sql = "SELECT * FROM cirkve;";
		$result = mysqli_query($conn, $sql);
   	echo '<table border = "1">';
   	echo "<tr><th>IC</th><th>Nazev</th><th>Stav</th><th>Obec</th><th>Zrizovatel</th><th>Datum vzniku</th><th>Zvlastni prava</th><th>ID adresy</th><th>Ulice</th><th>č.p.</th></tr>";
	   while($row = mysqli_fetch_assoc($result)) {
	   	echo "<tr>";
      	echo "<th>" . $row["ICO"]. "</th>
      			<th>" . $row["Nazev_CPO"]. "</th>
      			<th>" . $row["Stav_subjektu_RCNS"]. "</th>
      			<th>" . $row["Nazev_obce"]. "</th>
      			<th>" . $row["Nazev_casti_obce"]. "</th>
      			<th>" . $row["Zrizovatel"]. "</th>
      			<th>" . $row["Datum_vzniku"] . "</th>
      			<th>" . $row["Zvlastni_prava"] . "</th>
      			<th>" . $row["ID_adresy"]. "</th>
      			<th>" . $row["Nazev_ulice"]. "</th>
      			<th>" . $row["Cislo_do_adresy"] . "</th>";
	   	echo "</tr>\n";
    	}
      echo "\n</table>";
		mysqli_close($conn);
	}



/*
Creates indexes on RUIAN table, new table as connection ARES a RUIAN tables,
updates result table - sets name of Zrivovatel and copy rights of Zrizovatel to
every subject

@param: string $tableNameCirkve, name of table imported from ARES or created by createTableCirkveAktivni()
@param: string $tableNameCirkveSpatial, name of new table containing coordinates
@param: string $Stav_subjektu_RCNS, state of subject - active, canceled, ...
@return boolean, 1 - OK | 0 - error
*/

	function connectCirkveWithRUIAN ($tableNameCirkve, $tableNameCirkveSpatial, $Stav_subjektu_RCNS) {
		require_once "configClass.php";
		$conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEARES);
		if (!$conn) {
			echo "Nepovedlo se pripojit k DB";
			return 0;
		}
		mysqli_set_charset($conn, 'utf8');
/*
		$sql = "CREATE INDEX index_obec_cast_obce_ulice_cp_psc ON RUIAN_data (Nazev_obce, Nazev_casti_obce, Nazev_ulice, Cislo_do_adresy, PSC)";
		if (!mysqli_query($conn, $sql)) {
			echo "nepovedlo se vytvorit klic index_obec_ulice_cp_psc v tabulce RUIAN_data";
			echo "<BR>" . $sql;
			mysqli_close($conn);
			exit;
		}

		$sql = "CREATE INDEX index_obec_cast_obce_ulice_cp ON RUIAN_data (Nazev_obce, Nazev_casti_obce, Nazev_ulice, Cislo_do_adresy)";
		if (!mysqli_query($conn, $sql)) {
			echo "nepovedlo se vytvorit klic index_obec_cast_obce_ulice_cp v tabulce RUIAN_data";
			echo "<BR>" . $sql;
			mysqli_close($conn);
			exit;
		}
*/
		$sql = 'CREATE TABLE ' . $tableNameCirkveSpatial . ' AS
		(SELECT ' . $tableNameCirkve . '.*, RUIAN_data.Souradnice_Y, RUIAN_data.Souradnice_X
		FROM ' . $tableNameCirkve . '
		LEFT JOIN RUIAN_data
		ON ' . $tableNameCirkve . '.Nazev_obce = RUIAN_data.Nazev_obce
		AND ' . $tableNameCirkve . '.Nazev_casti_obce = RUIAN_data.Nazev_casti_obce
		AND ' . $tableNameCirkve . '.Nazev_ulice = RUIAN_data.Nazev_ulice
		AND ' . $tableNameCirkve . '.Cislo_do_adresy_RES LIKE RUIAN_data.Cislo_do_adresy
		AND ' . $tableNameCirkve . '.PSC_RES = RUIAN_data.PSC
		WHERE ' . $tableNameCirkve . '.Stav_subjektu_RCNS LIKE"' . $Stav_subjektu_RCNS . '");';

		if (!mysqli_query($conn, $sql)) {
			echo "nepovedlo se spojeni cirkevni a RUIAN tabulky";
			echo "<BR>" . $sql . "<br>";
			mysqli_close($conn);
			return 0;
		}
		echo "ok - CREATE TABLE " . $tableNameCirkveSpatial . "<BR>";
		echo $sql . "<br>";

// update of zvlastni prava and name of founder. This info is logged only for founder
		$sql = "UPDATE " . $tableNameCirkveSpatial . " AS t1
		INNER JOIN ( SELECT ICO, Nazev_CPO, Zvlastni_prava FROM " . $tableNameCirkveSpatial . ") AS t2
		SET t1.Zrizovatel_text = t2.Nazev_CPO, t1.Zvlastni_prava = t2.Zvlastni_prava
		WHERE t2.ICO = t1.Zrizovatel;";

		if (!mysqli_query($conn, $sql)) {
			echo "nepovedl se update na cirkevni tabulce";
			echo "<BR>" . $sql . "<br>";
			mysqli_close($conn);
			return 0;
		}
		echo "ok - UPDATE TABLE " . $tableNameCirkveSpatial . "<BR>";
		echo $sql . "<br>";


		return 1;
	}

/******************************** END ARES ***********************************************/


	/* Conversion of CZ chars to US chars
	 *
	 * @param string $retezec
	 * @return string
	 */
	function CZ2US($retezec) {
	    static $convertTable = array (
	        'á' => 'a', 'Á' => 'A', 'ä' => 'a', 'Ä' => 'A', 'č' => 'c',
	        'Č' => 'C', 'ď' => 'd', 'Ď' => 'D', 'é' => 'e', 'É' => 'E',
	        'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'í' => 'i',
	        'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ľ' => 'l', 'Ľ' => 'L',
	        'ĺ' => 'l', 'Ĺ' => 'L', 'ň' => 'n', 'Ň' => 'N', 'ń' => 'n',
	        'Ń' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O',
	        'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R', 'š' => 's',
	        'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ť' => 't', 'Ť' => 'T',
	        'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u',
	        'Ü' => 'U', 'ý' => 'y', 'Ý' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y',
	        'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z',
	    );
	    return strtr($retezec, $convertTable);
	}
}

?>
