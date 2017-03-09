<?php

class ApdDatabase {

	/**
	 * wpdb object
	 */
	protected $db;

	public function __construct() {

		global $wpdb;
		$this->db = $wpdb;

	}

	/**
	 * return all fields of a table as an array
	 *
	 * @param $tablename
	 *
	 * @return array
	 */
	public function getTableColumns( $tablename ) {

		$wpdb      = $this->db;
		$tablename = $this->addTablePrefix( $tablename );

		foreach ( $wpdb->get_col( "DESC " . $tablename, 0 ) as $columnname ) {

			$columns[] = $columnname;

		}

		return $columns;

	}

	/**
	 * path to csv file or $_FILE array of csv file
	 *
	 * @param $csv
	 *
	 * @return array of fields
	 */
	public function getCsvFields( $csv ) {

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$file = new SplFileObject( $csv );

		$fileDelimiter = $this->getFileDelimiter( $csv );

		$fields = $file->fgetcsv( $fileDelimiter );

		return $fields;

	}

	/**
	 * @param $file
	 *
	 * @return mixed
	 */
	public function getFileDelimiter( $csv ) {

		$file = new SplFileObject( $csv );

		$control   = $file->getCsvControl();
		$delimiter = $control[0];

		return $delimiter;

	}

	/**
	 * create a table with the supplied tablename and an array of fields
	 *
	 * @param $tablename
	 * @param $fields
	 *
	 * @return bool
	 */
	public function createTableFromFields( $tablename, $fields ) {

		if ( ! is_array( $fields ) ) {

			echo '$fields is not an array';

			return false;

		}

		//@todo make fields unique that have "_unique"
		$wpdb      = $this->db;
		$tablename = $this->addTablePrefix( $tablename );

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

		foreach ( $fields as $field ) {

			$sql .= ", $field TEXT";

			if ( preg_match( "/_unique/", $field ) ) {

				$sql .= " NOT NULL UNIQUE";

			}

		}

		$sql .= ')';

		echo $sql;
		exit();
		$result = $wpdb->query( $sql );

		return $result;

	}

	/**
	 * adds WP table prefix if it's missing
	 *
	 * @param $tablename
	 *
	 * @return mixed
	 */
	public function addTablePrefix( $tablename ) {

		$wpdb = $this->db;

		$tablenameArray = explode( "_", $tablename );

		if ( $tablenameArray[0] . "_" == $wpdb->prefix ) {

			return $tablename;

		} else {

			return $wpdb->prefix . $tablename;

		}

	}

	/**
	 * check if supplied table exists in wordpress
	 *
	 * @param $tablename
	 *
	 * @return bool
	 */
	public function tableExists( $tablename ) {

		$wpdb      = $this->db;
		$tablename = $this->addTablePrefix( $tablename );

		$sql            = "SHOW TABLES LIKE '%'";
		$existingTables = $wpdb->get_results( $sql, ARRAY_N );

		foreach ( $existingTables as $existingTable ) {

			if ( ! strcasecmp( $existingTable[0], $tablename ) ) {

				return true;

			}

		}

		return false;

	}

	/**
	 * Inserts csv content into the specified table
	 * removes redundant values that are marked as unique via adding "_unique" in a field name
	 *
	 * @param $csv
	 * path to csv file or $_FILE array of csv file
	 *
	 * @return bool
	 */
	public function insertCsv( $csv, $tablename ) {

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$csvPath     = $csv;
		$wpdb        = $this->db;
		$tablename   = $this->addTablePrefix( $tablename );
		$csv         = new SplFileObject( $csv );
		$tableFields = $this->getTableColumns( $tablename );

		$csv->setFlags( SplFileObject::READ_CSV );
		$csvArray = array();
		foreach ( $csv as $row ) {
			$csvArray[] = $row;
		}

		$csvFields = array_shift( $csvArray );

		//if installation is local, csv path may have backslashes, which have to be replaced with regular slashes
		if ( isLocalInstallation() ) {
			$csvPathNoBackslash = str_replace( "\\", "/", $csvPath );
			$csvPath            = $csvPathNoBackslash;
		}

		$sql    = "REPLACE INTO " . $tablename . " (";
		$values = '';

		foreach ( $tableFields as $key => $tableField ) {

			if ( $key == 0 ) {
				continue;
			}
			$sql .= "`" . $tableField . "`, ";

		}

		$sql = rtrim( $sql, " ," );
		$sql .= ") ";
		$sql .= "VALUES";

		foreach ( $csvArray as $csvRow ) {

			$values .= "(";

			foreach ( $csvRow as $csvField ) {

				$values .= "\"" . $csvField . "\",";

			}
			$values = rtrim( $values, " ," );
			$values .= "),";

		}

		$values = rtrim( $values, " ," );

		$sql .= $values;

		//@todo make it work with prepare

		$result = $wpdb->query( $sql );

		return $result;

	}

	/**
	 * remove redundant values in "_unique" fields from the supplied table
	 *
	 * @param $tablename
	 *
	 * @return bool
	 */
	public function removeRedundantValues( $tablename ) {

		$wpdb      = $this->db;
		$tablename = $this->addTablePrefix( $tablename );

		$uniqueFieldExists = false;

		$fields = $this->getTableColumns( $tablename );

		foreach ( $fields as $field ) {

			if ( preg_match( "/_unique/", $field ) ) {

				$uniqueFieldExists = true;
				$uniqueField       = $field;
				break;

			}

		}

		if ( $uniqueFieldExists ) {

			$sql    = "ALTER IGNORE TABLE " . $tablename . " ADD UNIQUE (" . $uniqueField . ")";
			$result = $wpdb->query( $sql );

			$sql    = "DELETE FROM TABLE " . $tablename . " WHERE " . $uniqueField . " = '' OR " . $uniqueField . " IS NULL";
			$result = $wpdb->query( $sql );

			return $result;

		}

		return false;
	}

	/**
	 * handles csv file addition to database
	 * calls function for table creation if necessary
	 * calls function for inserting csv content to table
	 *
	 * @param $csv
	 * path to csv file or $_FILE array of csv file
	 *
	 * @return bool
	 */
	public function addCsvToDatabase( $tablename, $csv ) {

		//@TODO check $tablename for injection!
		$result = true;

		$tablename = $this->addTablePrefix( $tablename );

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		//@TODO also check if fields already exist in table

		//create table if it doesn't exist yet
		$resultCreate = true;
		if ( $this->tableExists( $tablename ) === false ) {

			$fields       = $this->getCsvFields( $csv );
			$resultCreate = $this->createTableFromFields( $tablename, $fields );

		}

		$resultInsert = $this->insertCsv( $csv, $tablename );
		$resultUpdate = $this->removeRedundantValues( $tablename );

		//refine result
		$result = $resultCreate + $resultInsert + $resultUpdate;

		if ( $result ) {

			$result = true;

		} else {

			echo "Something went wrong.";

			return false;

		}

		return $result;
	}

}