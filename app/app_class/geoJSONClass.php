<?php

class ExGeoJSONClassGetDataDBConnection extends Exception {}
class ExGeoJSONClassGetDataDBInsert extends Exception {}

class geoJSONClass {

  function writeGeoJSON($data) {
    try {
      # Build GeoJSON feature collection array
      $geojson = array(
         'type'      => 'FeatureCollection',
         'features'  => array()
      );
      # Loop through rows to build feature arrays
      while($row = mysqli_fetch_assoc($data)) {
          $feature = array(
  //            'id' => $row['partnership_id'],
              'type' => 'Feature',
              'geometry' => array(
                  'type' => 'Point',
                  # Pass Longitude and Latitude Columns here
                  'coordinates' => array($row['Souradnice_Y'] * -1, $row['Souradnice_X'] * -1)
              ),
              # Pass other attribute columns here
              'properties' => array(
                  'Nazev_CPO' => $row['Nazev_CPO'],
                  'Nazev_obce' => $row['Nazev_obce'],
                  'Nazev_ulice' => $row['Nazev_ulice'],
                  'Zrizovatel_text' => $row['Zrizovatel_text'],
                  'Cislo_do_adresy' => $row['Cislo_do_adresy'],
                  'PSC' => $row['PSC'],
                  'Zrizovatel_text' => $row['Zrizovatel_text'],
                  'Zvlastni_prava' => $row['Zvlastni_prava'],
                  'Datum_vzniku' => $row['Datum_vzniku']
                  )
              );
          # Add feature arrays to feature collection array
          array_push($geojson['features'], $feature);
      }
  //    header('Content-type: application/json');
//      var_dump($geojson);
      return json_encode($geojson, JSON_NUMERIC_CHECK);
    }
    catch(Exception $e) {
			echo "Exception - Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}
		catch(Error $e) {
			echo "Error - Chyba: " . $e->getMessage() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
		}

  }

/*
Selects data from table containing attributes and x,y

@param: string $tableName - name of table
@param: string $cond - query
@return: object-ok | 0-false
*/

  function getData($tableName, $cond) {
    try {
      require_once "configClass.php";
      $conn = mysqli_connect(configClass::SERVERNAME, configClass::USERNAME, configClass::PASSWORD, configClass::DBNAMEARES);
      if (!$conn) {
				throw new ExGeoJSONClassGetDataDBConnection;
			}
			mysqli_set_charset($conn, 'utf8');
  		$sql = "SELECT * FROM $tableName WHERE " . $cond . ";";
      $result = mysqli_query($conn, $sql);
      if (!$result) {
				throw new ExGeoJSONClassGetDataDBInsert;
			}
      return $result;
    }
    catch (ExGeoJSONClassGetDataDBConnection $e) {
      echo "Chyba: nepovedlo se pripojit k DB: " . mysqli_connect_error() . ". File: " . $e->getFile() . ", line: " . $e->getLine();
			return 0;
    }
    catch(ExGeoJSONClassGetDataDBInsert $e) {
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
    mysqli_close($conn);
  }

}
?>
