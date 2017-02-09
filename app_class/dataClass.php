<?php

//error_reporting( E_ALL );
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

set_time_limit(0);

class IC {
	/*
	Verification of IC
	@param: string $ic, given IC
	@return: boolean, 0-false | 1-true	
	*/	
	
	function verifyIC($ic)
	{
	   $ic = preg_replace('#\s+#', '', $ic);
	   if (!preg_match('#^\d{8}$#', $ic)) {
      	return FALSE;
	   }
	  	// kontrolní součet
    	$a = 0;
    	for ($i = 0; $i < 7; $i++) {
        $a += $ic[$i] * (8 - $i);
	   }
    	$a = $a % 11;
    	if ($a === 0) {
	       $c = 1;
	    } elseif ($a === 1) {
	       $c = 0;
    	 } else {
        	 $c = 11 - $a;
    	 }
	 	 //vraci true nebo false
    	 return (int) $ic[7] === $c;
	 }
}


class ExDataClassGetDataReadFile extends Exception {}
class ExDataClassInsertDataDBConnection extends Exception {}
class ExDataClassInsertDataDBInsert extends Exception {}
class ExDataClassLoadXML extends Exception {}

class dataClass {
	
	const SERVERNAME = "localhost";
	const USERNAME = "root";
	const PASSWORD = "jara777";
	const DBNAMEKURZY = "cirkve";
	const DBNAMEARES = "cirkveARES";
//	const DBNAMEARES = "test";	
	
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
				$arr_index = trim(strtolower(dataClass::CZ2US($arr[0])));
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
	@return boolean 1 - OK, 0 - error
	*/	
	
	function insertDataKurzy($data){	
		try{
			$conn = mysqli_connect(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAMEKURZY);			
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
		$conn = mysqli_connect(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAMEKURZY);
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
		$sql = "CREATE TABLE `cirkve` (
	  `ICO` varchar(8) COLLATE utf8_czech_ci NOT NULL,
	  `Stav_subjektu_RCNS` text COLLATE utf8_czech_ci,
	  `Nazev_CPO` text COLLATE utf8_czech_ci,
	  `Typ_CNS` text COLLATE utf8_czech_ci,
	  `Zkr_statu` text COLLATE utf8_czech_ci,
	  `Nazev_PF` text COLLATE utf8_czech_ci,
	  `ID_adresy` decimal(10,0) DEFAULT NULL,
	  `Nazev_obce` text COLLATE utf8_czech_ci,
	  `Nazev_ulice` text COLLATE utf8_czech_ci,
	  `Cislo_do_adresy` text COLLATE utf8_czech_ci,
	  `PSC` int(11) DEFAULT NULL,
	  `Zrizovatel` text COLLATE utf8_czech_ci,
	  `Zvlastni_prava` text COLLATE utf8_czech_ci,
	  `Datum_vzniku` text COLLATE utf8_czech_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";
		
		try{
			$conn = mysqli_connect(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAMEARES);
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
				 $value = $domOb->getElementsByTagName($nodeName)->item(0)->nodeValue;
				 //adding node name and node value to associative array
				 $res_arr[$nodeName] = $value;			
			}
			return $res_arr;
		}
		catch (ExDataClassLoadXML $e) {
			echo "Exception - nepovedlo se nacist XMLko" . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
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
	@return boolean 1 - OK, 0 - error
	*/	
	
	function insertDataARES($data){	
		try{
			$conn = mysqli_connect(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAMEARES);
			if (!$conn) {
				throw new ExDataClassInsertDataDBConnection;
			}

			mysqli_set_charset($conn, 'utf8');
			
			$sql = "INSERT INTO cirkve(ICO, Stav_subjektu_RCNS, Nazev_CPO, Typ_CNS, Zkr_statu, Nazev_PF, ID_adresy, Nazev_obce, Nazev_ulice, Cislo_do_adresy, PSC, Zrizovatel, Zvlastni_prava, Datum_vzniku) 
			VALUES ('" . $data["ICO"] . "', '" . $data["Stav_subjektu_RCNS"] . "', '" . $data["Nazev_CPO"] . "', '" . $data["Typ_CNS"] . "', '" . $data["Zkr_statu"] . "', '" . $data["Nazev_PF"] . "', "  
			. $data["ID_adresy"] . ", '" . $data["Nazev_obce"] . "', '" . $data["Nazev_ulice"] . "', '" . $data["Cislo_do_adresy"] . "', " . $data["PSC"] . ", '" . $data["Zrizovatel"] . "', '" 
			. $data["Zvlastni_prava"] . "', '" . $data["Datum_vzniku"] . "')";
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
	Reads and prints data from RDBMS	
	*/
	
	function readCirkevniDBARES() {
		$conn = mysqli_connect(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAMEARES);
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
