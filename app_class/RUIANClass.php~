<?php

class ExRUIANClassCreateRUIANTableDBQuery extends Exception {}
class ExRUIANClassCreateRUIANTableDBConnection extends Exception {}
class ExRUIANClassImportRUIANDBConnection extends Exception {}
class ExRUIANClassImportRUIANDBQuery extends Exception {}

class RUIANClass {
	
	/*
	Reads first file in RUIAN_data folder and creates from table head (first row) SQL query for creating table
	
	@output: boolean, 1 - success | 0 - fail 	
	*/

	function createRUIANTable () {
		try {
			$RUIANdir = getcwd() . '/RUIAN_data/';
			$RUIANFiles = array_slice(scandir($RUIANdir), 2);
			$fileContent = file($RUIANdir . $RUIANFiles[0]);
			$firstLine = iconv('windows-1250', 'UTF-8', $fileContent[0]);
			require_once "dataClass.php";
			$firstLine = str_replace(' ', '_', dataClass::CZ2US($firstLine));
			$tableRows = str_replace(';', ' text, ', $firstLine);
			$tableRows = str_replace('PSC text', 'PSC int(5)', $tableRows);
			$tableRows = str_replace('Souradnice_Y text', 'Souradnice_Y decimal(9,2)', $tableRows);
			$tableRows = str_replace('Souradnice_X text', 'Souradnice_X decimal(9,2)', $tableRows);
			$tableRows = str_replace('Plati_Od', 'Plati_Od text', $tableRows);
			$sql = "CREATE TABLE RUIAN_data (". $tableRows .")";
			$conn = mysqli_connect(dataClass::SERVERNAME, dataClass::USERNAME, dataClass::PASSWORD, dataClass::DBNAMEARES);
			if (!$conn) {
				throw new ExRUIANClassCreateRUIANTableDBConnection;
			}
			if (!mysqli_query($conn, $sql)) {
				throw new ExRUIANClassCreateRUIANTableDBQuery;
			} 
			mysqli_close($conn);
			return 1;
		}
		catch(ExRUIANClassCreateRUIANTableDBConnection $e) {
			echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch (ExRUIANClassCreateRUIANTableDBQuery $e) {
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
	
		
	
	function importRUIAN() {
		$sql = "INSERT INTO RUIAN_data VALUES (";
		try {
			require_once "dataClass.php";
			$RUIANdir = getcwd() . '/RUIAN_data/';
			$RUIANFiles = array_slice(scandir($RUIANdir), 2);
			$conn = mysqli_connect(dataClass::SERVERNAME, dataClass::USERNAME, dataClass::PASSWORD, dataClass::DBNAMEARES);
			if (!$conn) {
				throw new ExRUIANClassImportRUIANDBConnection;
			}
			foreach ($RUIANFiles as $fileName) {
				$fileContent = file($RUIANdir . $fileName);
				//cut first line - first line is headre of table and do not contain data
				$fileContent = array_slice($fileContent, 1);
				foreach ($fileContent as $fileLine) {
					$fileLine = iconv('windows-1250', 'UTF-8', $fileLine);				
					echo $fileLine . "<br>";
					$arr_sql = explode(';', $fileLine);
//					var_dump($arr_sql);
					for($i = 0; $i < count($arr_sql) - 1; $i++)  {
						if ($i > 12 && $i < 15 ) {
							$sql .= $arr_sql[$i] . ", ";
							continue;
						}
						$sql .= "'" . $arr_sql[$i] . "', ";
					} 
					echo "<br>SQL " . $sql;
					if (!mysqli_query($conn, $sql)) {
						throw new ExRUIANClassImportRUIANDBQuery;
					} 
				}		
				exit;
			}
			mysqli_close($conn);
			return 1;
		}
		catch(ExRUIANClassImportRUIANDBConnection $e) {
			echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch (ExRUIANClassImportRUIANDBQuery $e) {
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
}

?> 
