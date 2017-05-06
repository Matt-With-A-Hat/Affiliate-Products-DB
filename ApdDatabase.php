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

	public function __construct( $tablename ) {

		global $wpdb;
		$this->db = $wpdb;
		$this->setTablename( $tablename );

	}

	/**
	 * @return mixed
	 */
	public function getTablename() {
		return $this->tablename;
	}

	/**
	 * sets the tablename and adds WP table prefix if it's missing
	 *
	 * @param mixed $tablename
	 */
	public function setTablename( $tablename ) {
		$wpdb = $this->db;

		$tablenameArray = explode( "_", $tablename );

		if ( strtolower( $tablenameArray[0] . "_" ) == strtolower( $wpdb->prefix ) ) {
			$this->tablename = $tablename;
		} else {
			$this->tablename = $wpdb->prefix . $tablename;
		}
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
	public function getTableInfo() {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

		$sql = "SHOW fields FROM $tablename";

		$columns = $wpdb->get_results( $wpdb->prepare( $sql, $tablename ), ARRAY_A );

		if ( empty( $columns ) ) {

			if ( APD_DEBUG ) {
				echo "Query didn't return any columns";
			}

			return false;
		} else {
			return $columns;
		}

	}

	/**
	 * return all columns of a table as an array
	 * if suffix is false, strip everything after first underscore of column name
	 *
	 * @param $tablename
	 *
	 * @return array
	 */
	public function getTableColumns( $suffix = true ) {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

		$sql = "SELECT * FROM $tablename";

		foreach ( $wpdb->get_col( "DESC " . $tablename, 0 ) as $columnname ) {

			if ( $suffix ) {
				$columns[] = $columnname;
			} else {
				$columns[] = explode( "_", $columnname )[0];
			}

		}

		if ( empty( $columns ) ) {

			if ( DEBUG ) {
				$error = "Query didn't return any columns";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return array( 0 => "Empty query result" );

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

	public function getColumns( $id, $array ) {
		global $wpdb;
		$tablename = $this->tablename;

		$sql = "SELECT * FROM $tablename WHERE $id = %s";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $id ), ARRAY_A );

		krumo( $result );
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
	public function getUniqueColumn() {

		$tablename = $this->tablename;
		$columns   = $this->getTableColumns( true );

		foreach ( $columns as $column ) {
			if ( preg_match( "/_unique/", $column ) ) {

				return $column;

			}

		}

		return false;

	}

	/**
	 * @param $tablename
	 * @param $id
	 * @param array|null $fields if provided, function will only return given fields. Must be set like $key => $field.
	 * @param string $type
	 *
	 * @return array|null|object|void
	 */
	public function getRow( $id, array $fields = null, $type = OBJECT ) {

		global $wpdb;
		$tablename = $this->tablename;

		if ( $fields !== null ) {
			$type = ARRAY_A;
		}

		$sql = "SELECT * FROM $tablename WHERE id = %s";

//		krumo($wpdb->prepare($sql,$id));

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id ), $type );

		if ( $fields !== null AND is_array( $result ) ) {
			$fields = array_flip( $fields );
			$result = array_intersect_key( $result, $fields );
		}

		return $result;
	}

	/**
	 * gets a row from the database
	 *
	 * @param $id
	 *
	 * @return array|bool|null|object|void
	 */
	//old get row which didn't make sense here
	/*public function getRow( $tablename, $id, $type = OBJECT ) {

		global $wpdb;

		$uniqeField   = $this->getUniqueColumn( $tablename );
		$sql          = "SELECT * FROM $tablename WHERE $uniqeField = %s";
		$this->dbItem = $wpdb->get_row( $wpdb->prepare( $sql, $id ), $type );

		if ( empty( $this->dbItem ) ) {

			if ( APD_DEBUG ) {
				echo "Entry does not exist: $id<br>";
			}

			return false;

		}

		return $this->dbItem;
	}*/

	/**
	 * create a table with the supplied tablename and an array of fields
	 * _unique in field name will create a unique column
	 * _bool in field name will create a boolean column
	 *
	 * @param $tablename
	 * @param $fields
	 *
	 * @return bool
	 */
	public function createTableFromCsvFields( $fields ) {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

		if ( ! is_array( $fields ) ) {
			if ( APD_DEBUG ) {
				$error = '$fields is not an array<br>';
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		if ( $this->tableExists() ) {
			if ( APD_DEBUG_DEV ) {
				$this->dropTable();

				return true;
			}
			if ( APD_DEBUG ) {
				$error = "Can not create table $tablename, which already exists.";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		foreach ( $fields as $key => $field ) {
			$field          = esc_sql( $field );
			$fields[ $key ] = str_replace( ' ', '', $field );
		}

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

		foreach ( $fields as $field ) {

			$field = "`" . esc_sql( $field ) . "`";

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

		//@todo use this instead of wpdb->query and test whether it works
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//		dbDelta( $sql );

		return $result;

	}

	/**
	 * @param $tablename
	 * @param $array
	 *
	 * @return bool|false|int
	 */
	public function createTableFromArray( $array ) {
		$wpdb      = $this->db;
		$tablename = $this->tablename;

		if ( ! is_array( $array ) ) {
			if ( APD_DEBUG ) {
				$error = '$fields is not an array<br>';
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		if ( $this->tableExists() ) {
			if ( APD_DEBUG_DEV ) {
				$this->dropTable();

			} else if ( APD_DEBUG ) {
				$error = "Can not create table $tablename, which already exists.";
				print_error( $error, __METHOD__, __LINE__ );

				return false;

			} else {
				return false;

			}

		}

		foreach ( $array as $key => $field ) {
			$field          = esc_sql( $field );
			$fields[ $key ] = str_replace( ' ', '', $field );
		}

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

		foreach ( $array as $field ) {
			$field = "`" . esc_sql( $field ) . "`";
			$sql .= ", $field TEXT";
		}

		$sql .= ')';

		$result = $wpdb->query( $sql );

		//@todo use this instead of wpdb->query and test whether it works
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//		dbDelta( $sql );

		return $result;
	}

	public function setUniqueColumns( array $uniqueColumns ) {

		$tablename = $this->tablename;

		$tableColumns = $this->getTableColumns();

		if ( empty( $tableColumns ) ) {
			if ( APD_DEBUG ) {
				$error = "Tablecolumns $tablename couldn't be loaded";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		if ( $errorColumns = array_diff( $uniqueColumns, $tableColumns ) ) {
			if ( APD_DEBUG ) {
				$errorColumn = reset( $errorColumns );
				$error       = "$errorColumn is not a column of $tablename";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		global $wpdb;

		//change columns data type to varchar, so unique can be applied to columns
		$sql = "ALTER TABLE $tablename ";
		foreach ( $uniqueColumns as $uniqueColumn ) {
			$sql .= "MODIFY COLUMN $uniqueColumn VARCHAR(255), ";
		}
		$sql = rtrim( $sql, " ," );

		$sql .= ";";
		$sql1 = $wpdb->prepare( $sql, $uniqueColumns );

		//alter column so it is unique
		$sql = "ALTER TABLE $tablename ADD UNIQUE (";
		foreach ( $uniqueColumns as $uniqueColumn ) {
			$sql .= "$uniqueColumn, ";
		}
		$sql = rtrim( $sql, " ," );
		$sql .= ");";
		$sql2 = $wpdb->prepare( $sql, $uniqueColumns );

		$result = $wpdb->query( $wpdb->prepare( $sql1, $uniqueColumns ) );
		$result = $wpdb->query( $wpdb->prepare( $sql2, $uniqueColumns ) );

		return $result;
	}

	/**
	 * drop a table from database
	 *
	 * @param $tablename
	 *
	 * @return false|int
	 */
	public function dropTable() {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

		$sql = "DROP TABLE IF EXISTS $tablename";

		$result = $wpdb->query( $sql );

		return $result;
	}

	/**
	 * check if supplied table exists in wordpress
	 *
	 * @param $tablename
	 *
	 * @return bool
	 */
	public function tableExists() {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

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
	public function insertCsv( $csv ) {

		$tablename = $this->tablename;

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$csvPath     = $csv;
		$wpdb        = $this->db;
		$tablename   = add_table_prefix( $tablename );
		$csv         = new SplFileObject( $csv );
		$tableFields = $this->getTableColumns();
		$tableInfo   = $this->getTableInfo();

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
	public function removeRedundantValues() {

		$wpdb      = $this->db;
		$tablename = $this->tablename;

		$uniqueFieldExists = false;

		$fields = $this->getTableColumns();

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
	public function addCsvToDatabase( $csv ) {

		//@TODO check $tablename for injection!

		$tablename = $this->tablename;

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		//@TODO also check if fields already exist in table

		//create table if it doesn't exist yet
		$result = true;
		if ( $this->tableExists() === false ) {

			$fields = $this->getCsvFields( $csv );
			$result .= $this->createTableFromCsvFields( $fields );

		} else if ( APD_DEBUG_DEV === true ) {

			//in debug always delete existing table when uploading a new csv
			$result .= $this->dropTable();
			$fields = $this->getCsvFields( $csv );
			$result .= $this->createTableFromCsvFields( $fields );

		}

		$result .= $this->insertCsv( $csv );
		$result .= $this->removeRedundantValues();

		if ( $result === false ) {

			echo "Something went wrong.";

			return false;

		}

		return $result;
	}

}