<?php

class ApdDatabase {

	/**
	 * wpdb object
	 */
	protected $db;

	/**
	 * the returned item from database
	 *
	 * @var
	 */
	public $dbItem;

	/**
	 * the returned item from amazon
	 *
	 * @var
	 */
	public $amazonItem;

	/**
	 * @var
	 */
	protected $tablename;

	public function __construct() {

		global $wpdb;
		$this->db = $wpdb;

	}

	/**
	 * return all fields of a table with this info:
	 * field, type, null, key, default, extra (auto increment etc.)
	 *
	 *
	 * @param $tablename
	 *
	 * @return array
	 */
	public function getTableInfo( $tablename ) {

		$wpdb      = $this->db;
		$tablename = add_table_prefix( $tablename );

		$sql = "SHOW fields FROM $tablename";

		$columns = $wpdb->get_results( $wpdb->prepare( $sql, $tablename ), ARRAY_A );

		return $columns;

	}

	/**
	 * return all fields of a table as an array
	 * if suffix is false, strip everything after first underscore of column name
	 *
	 * @param $tablename
	 *
	 * @return array
	 */
	public function getTableColumns( $tablename, $suffix = true ) {

		$wpdb      = $this->db;
		$tablename = add_table_prefix( $tablename );

		$sql = "SELECT * FROM $tablename";


//		$sql = "SELECT * FROM $tablename";
//		if($wpdb->query($sql)){
//			echo "Connection works";
//		}else{
//			krumo($wpdb->query($sql));
//			exit( krumo( $wpdb->last_query ) );
//
//		}

		foreach ( $wpdb->get_col( "DESC " . $tablename, 0 ) as $columnname ) {

			if ( $suffix ) {
				$columns[] = $columnname;
			} else {
				$columns[] = explode( "_", $columnname )[0];
			}

		}

		if ( empty( $columns ) ) {

			echo "Query didn't return any columns";

			return false;
		} else {
			return $columns;
		}

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
	 * Finds the first unique column specified with "unique" in a table
	 *
	 * @param $tablename
	 *
	 * @return bool|mixed
	 * @internal param $table
	 *
	 */
	public function getUniqueColumn( $tablename ) {

		$tablename = add_table_prefix( $tablename );
		$columns   = $this->getTableColumns( $tablename, true );

		foreach ( $columns as $column ) {
			if ( preg_match( "/_unique/", $column ) ) {

				return $column;

			}

		}

		return false;

	}

	/**
	 * gets an item from the database
	 *
	 * @param $asin
	 *
	 * @return array|bool|null|object|void
	 */
	public function getItem( $asin ) {

		//@todo #lastedit
		global $wpdb;
		global $apd;
		
		$uniqeField   = $this->getUniqueColumn( $apd->tablename );
		$sql          = "SELECT * FROM $apd->tablename WHERE $uniqeField = %s";
		$this->dbItem = $wpdb->get_row( $wpdb->prepare( $sql, $asin ), OBJECT );

		if ( empty( $this->dbItem ) ) {

			echo "This item doesn't exist";

			return false;

		}


		return $this->dbItem;

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

		foreach ( $fields as $key => $field ) {

			$field          = esc_sql( $field );
			$fields[ $key ] = str_replace( ' ', '', $field );

		}

		//@todo make fields unique that have "_unique"
		$wpdb      = $this->db;
		$tablename = add_table_prefix( $tablename );

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

		foreach ( $fields as $field ) {

			if ( preg_match( "/_unique/", $field ) ) {

				$sql .= ", $field VARCHAR(255) NOT NULL UNIQUE";

			} else if ( ( preg_match( "/_bool/", $field ) ) ) {

				$sql .= ", $field BOOLEAN DEFAULT NULL";

			} else {

				$sql .= ", $field TEXT";

			}

		}

		$sql .= ')';

		$result = $wpdb->query( $sql );

		return $result;

	}

	/**
	 * drop a table from database
	 *
	 * @param $tablename
	 *
	 * @return false|int
	 */
	public function dropTable( $tablename ) {

		$wpdb      = $this->db;
		$tablename = add_table_prefix( $tablename );

		$sql = "DROP TABLE IF EXISTS $tablename";

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
	public function add_table_prefix( $tablename ) {

		$wpdb = $this->db;

		$tablenameArray = explode( "_", $tablename );

		if ( strtolower( $tablenameArray[0] . "_" ) == strtolower( $wpdb->prefix ) ) {

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
		$tablename = add_table_prefix( $tablename );

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
	 * removes redundant values that are marked as unique via adding "_unique" in a csv-field name
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
		$tablename   = add_table_prefix( $tablename );
		$csv         = new SplFileObject( $csv );
		$tableFields = $this->getTableColumns( $tablename );
		$tableInfo   = $this->getTableInfo( $tablename );

		$csv->setFlags( SplFileObject::READ_CSV );
		$csvArray = array();
		foreach ( $csv as $row ) {
			$csvArray[] = $row;
		}

		$csvFields = array_shift( $csvArray );

		$sql    = "REPLACE INTO " . $tablename . " (";
		$values = '';

		foreach ( $tableFields as $key => $tableField ) {

			//skip csv index?
			if ( $key == 0 ) {
				continue;
			}
			$sql .= "`" . $tableField . "`, ";

		}

		$sql = rtrim( $sql, " ," );
		$sql .= ") ";
		$sql .= "VALUES";

		foreach ( $csvArray as $keyrow => $csvRow ) {

			$values .= "(";

			foreach ( $csvRow as $key => $csvField ) {

				//$key + 1 because array skips first column which is "id" in excel
				$fieldType = $tableInfo[ $key + 1 ]['Type'];

				if ( type_is_boolean( $fieldType ) ) {

					if ( field_is_true( $csvField ) ) {
						$csvField = 'TRUE';
					} else if ( field_is_false( $csvField ) ) {
						$csvField = 'FALSE';
					} else {
						$csvField = 'NULL';
					}

					$values .= esc_sql( $csvField ) . ",";

				} else {

					$values .= "\"" . esc_sql( $csvField ) . "\",";

				}

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
		$tablename = add_table_prefix( $tablename );

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

		$tablename = add_table_prefix( $tablename );

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		//@TODO also check if fields already exist in table

		//create table if it doesn't exist yet
		$result = true;
		if ( $this->tableExists( $tablename ) === false ) {

			$fields = $this->getCsvFields( $csv );
			$result .= $this->createTableFromFields( $tablename, $fields );

		} else if ( DEBUG === true ) {

			//in debug always delete existing table when uploading a new csv
			$result .= $this->dropTable( $tablename );
			$fields = $this->getCsvFields( $csv );
			$result .= $this->createTableFromFields( $tablename, $fields );

		}

		$result .= $this->insertCsv( $csv, $tablename );
		$result .= $this->removeRedundantValues( $tablename );

		if ( $result === false ) {

			echo "Something went wrong.";

			return false;

		}

		return $result;
	}

}