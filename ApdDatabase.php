<?php

class ApdDatabase {

	/**
	 * wpdb object
	 */
	protected $db;

	/**
	 * the prefix for APD tables in the database
	 *
	 * @var string
	 */
	protected $apdTablePrefix = "apd_";

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
	 * return all fields of a table with this info:
	 * field, type, null, key, default, extra (auto increment etc.)
	 *
	 * @return array|bool
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

	/**
	 * get all the values from a column or multiple columns
	 *
	 * @param null $columns
	 *
	 * @return array|null|object
	 */
	public function getColumns( $columns = null ) {
		global $wpdb;
		$tablename = $this->tablename;
		if ( ! ( is_array( $columns ) ) ) {
			$columns = array( $columns );
		}

		$fields = '';
		if ( empty( $columns ) ) {
			$fields = "*";
		} else {
			foreach ( $columns as $field ) {
				$fields .= $field . ",";
			}
			$fields = rtrim( $fields, " ," );
		}

		$sql = "SELECT $fields FROM $tablename";

		return $wpdb->get_results( $wpdb->prepare( $sql, "" ), ARRAY_A );
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
//	public function getUniqueColumn() {
//
//		$tablename = $this->tablename;
//		$columns   = $this->getTableColumns( true );
//
//		foreach ( $columns as $column ) {
//			if ( preg_match( "/_unique/", $column ) ) {
//
//				return $column;
//
//			}
//
//		}
//
//		return false;
//
//	}

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

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id ), $type );

		if ( $fields !== null AND is_array( $result ) ) {
			$fields = array_flip( $fields );
			$result = array_intersect_key( $result, $fields );
		}

		return $result;
	}

	/**
	 * sets the tablename and adds WP table prefix if it's missing
	 *
	 * @param mixed $tablename
	 */
	public function setTablename( $tablename ) {

		$wpdb = $this->db;

		$this->tablename = add_table_prefix( $tablename );
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
	 * create a table with the supplied tablename and an array of fields from as csv
	 * _unique in field name will create a unique column
	 * _boolean, _varchar, _text in field name will set a column to the respective type
	 *
	 * @param array $fields
	 * @param null $purpose
	 *
	 * @return bool
	 *
	 * @todo refine code. Make it work with createTableFromArray and setUniqueColumns
	 */
	public function createTable( $purpose = null, array $fields = null ) {

		if ( ! is_array( $fields ) ) {
			if ( APD_DEBUG ) {
				$error = '$fields is not an array<br>';
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;

		} else if ( $fields === null ) {
			global $wpdb;
			$sql    = "CREATE TABLE IF NOT EXISTS $this->tablename;";
			$result = $wpdb->query( $sql );

			if ( $result ) {
				$databaseService = new ApdDatabaseService();
				$databaseService->updateTableList( $this->tablename, $purpose );

				return true;
			} else {
				return false;
			}
		} else {
			$newFields  = array();
			$fieldtypes = array();

			foreach ( $fields as $field ) {

				if ( preg_match( "/_unique/", $field ) ) {
					$fieldtypes['unique'][] = $newFields[] = str_replace( "_unique", "", $field );

				} else if ( preg_match( "/_bool/", $field ) ) {
					$fieldtypes['bool'][] = $newFields[] = str_replace( "_bool", "", $field );

				} else if ( preg_match( "/_text/", $field ) ) {
					$fieldtypes['text'][] = $newFields[] = str_replace( "_text", "", $field );

				} else if ( preg_match( "/_varchar/", $field ) ) {
					$fieldtypes['varchar'][] = $newFields[] = str_replace( "_unique", "", $field );
				} else {
					$newFields[] = $field;
				}
			}

			$this->createTableFromArray( $newFields, $purpose );
			foreach ( $fieldtypes as $type => $columns ) {
				$this->modifyColumns( $columns, $type );
			}

			return true;
		}
	}

	/**
	 * @param array $array
	 * @param null $purpose
	 *
	 * @return bool|false|int
	 */
	public function createTableFromArray( array $array, $purpose = null ) {
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
			if ( APD_REPLACE_TABLES ) {
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

		if ( $result ) {
			$databaseService = new ApdDatabaseService();
			$databaseService->updateTableList( $tablename, $purpose );
		}

		//@todo use this instead of wpdb->query and test whether it works
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//		dbDelta( $sql );

		return $result;
	}

	/**
	 * Alter every supplied column from the array with $modify
	 * You can define a column as unique or change its datatype to bool, text or varchar.
	 *
	 * @todo allow default values
	 *
	 * @param array $uniqueColumns
	 * @param $modify
	 *
	 * @return bool|false|int
	 */
	public function modifyColumns( array $uniqueColumns, $modify ) {

		$tablename = $this->tablename;
		$allowed   = array(
			'unique',
			'bool',
			'text',
			'varchar'
		);

		if ( ! in_array( $modify, $allowed ) ) {
			$error = "Supplied modify argument not allowed";
			print_error( $error, __METHOD__, __LINE__ );
		}

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

		if ( $modify == "unique" ) {
			//change columns data type to varchar, so unique can be applied to columns
			$sql = "ALTER TABLE $tablename ";
			foreach ( $uniqueColumns as $uniqueColumn ) {
				$sql .= "MODIFY COLUMN $uniqueColumn VARCHAR(255), ";
			}
			$sql = rtrim( $sql, " ," );

			$sql .= ";";
			$sql1 = $sql;

			//alter column so it is unique
			$sql = "ALTER TABLE $tablename ADD UNIQUE (";
			foreach ( $uniqueColumns as $uniqueColumn ) {
				$sql .= "$uniqueColumn, ";
			}
			$sql = rtrim( $sql, " ," );
			$sql .= ");";
			$sql2 = $sql;

			$result = $wpdb->query( $wpdb->prepare( $sql1, $uniqueColumns ) );
			$result .= $wpdb->query( $wpdb->prepare( $sql2, $uniqueColumns ) );

		} else {
			$modify = ( $modify == 'varchar' ) ? 'varchar(255)' : $modify;

			$sql = "ALTER TABLE $tablename ";
			foreach ( $uniqueColumns as $uniqueColumn ) {
				$sql .= "MODIFY COLUMN $uniqueColumn $modify, ";
			}
			$sql = rtrim( $sql, " ," );

			$result = $wpdb->query( $wpdb->prepare( $sql, $uniqueColumns ) );
		}

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
	public function csvInsert( $csv ) {
		$tablename = $this->tablename;

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$wpdb        = $this->db;
		$csv         = new SplFileObject( $csv );
		$tableFields = $this->getTableColumns();
		$tableInfo   = $this->getTableInfo();

		$csv->setFlags( SplFileObject::READ_CSV );
		$csvArray = array();
		foreach ( $csv as $row ) {
			$csvArray[] = $row;
		}

		array_shift( $csvArray );

		$sql    = "INSERT INTO " . $tablename . " (";
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

				//$key + 1 because first column needs to be skipped, which is "id" in excel
				$fieldtype = $tableInfo[ $key + 1 ]['Type'];
				$values .= $this->refineValue( $csvField, $fieldtype ) . ",";
			}
			$values = rtrim( $values, " ," );
			$values .= "),";
		}

		$values = rtrim( $values, " ," );
		$sql .= $values;

		return $result = $wpdb->query( $sql );
	}

	/**
	 * Update specified table with content from the provided csv
	 *
	 * @param $csv
	 *
	 * @return false|int
	 */
	function csvUpdate( $csv ) {
		$tablename = $this->tablename;
		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}
		$csvFields = array_remove_tail( $this->getCsvFields( $csv ), "_" );
		$asinIndex = array_search( 'Asin', $csvFields );

		$wpdb        = $this->db;
		$csv         = new SplFileObject( $csv );
		$tableFields = $this->getTableColumns();
		$tableInfo   = $this->getTableInfo();

		$csv->setFlags( SplFileObject::READ_CSV );
		$csvArray = array();
		foreach ( $csv as $row ) {
			$csvArray[] = $row;
		}

		array_shift( $csvArray );

		$result = true;
		foreach ( $csvArray as $keyrow => $csvRow ) {
			$sql = "UPDATE " . $tablename . " SET ";
			foreach ( $csvRow as $key => $csvField ) {
				$fieldtype  = $tableInfo[ $key + 1 ]['Type'];
				$columnname = $tableFields[ $key + 1 ];
				$value      = $this->refineValue( $csvField, $fieldtype );
				$sql .= "$columnname = $value, ";
			}
			$sql = rtrim( $sql, " ," );
			$sql .= " WHERE Asin = " . $this->refineValue( $csvRow[ $asinIndex ], 'text' );
			$sql .= ";";
			$result .= $wpdb->query( $sql );
		}

		return $result;
	}

	/**
	 * refine a value for input to database. For instance, make "1" become "true"
	 *
	 * @param $value
	 * @param $fieldtype
	 *
	 * @return string
	 */
	function refineValue( $value, $fieldtype ) {
		if ( type_is_boolean( $fieldtype ) ) {
			if ( field_is_true( $value ) ) {
				$value = 'TRUE';
			} else if ( field_is_false( $value ) ) {
				$value = 'FALSE';
			} else {
				$value = 'NULL';
			}
			$value = esc_sql( $value );
		} else {
			$value = esc_sql( $value );
		}

		return "\"" . $value . "\"";
	}

	/**
	 * remove redundant values in "_unique" fields from the supplied table
	 * @return bool
	 * @internal param $tablename
	 *
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

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		//@TODO also check if columns already exist in table

		//create table if it doesn't exist yet
		$new       = false;
		$csvFields = $this->getCsvFields( $csv );

		if ( ! $this->tableExists() ) {
			//create new table if it doesn't exist yet
			$fields = $csvFields;
			$this->createTable( 'products', $fields );
			$new    = true;
			$result = 1;

		} else if ( APD_REPLACE_TABLES === true ) {
			//delete existing table when uploading a new csv
			$this->dropTable();
			$fields = $csvFields;
			$this->createTable( 'products', $fields );
			$new    = true;
			$result = 2;

		} else {
			//add any columns from csv, that are missing in existing table
			$tableFields = $this->getTableColumns();
			array_shift( $tableFields );
			$csvFieldsNoTail = array_remove_tail( $csvFields, "_" );
			$newFields       = array_diff( $csvFieldsNoTail, $tableFields );

			//add any new fields from the csv to the existing equivalent database table
			if ( ! empty( $newFields ) ) {
				$newFields = array_intersect_key( $csvFields, $newFields ); //get the data type information from original csvFields array
				foreach ( $newFields as $newField ) {
					$pos = strrpos( $newField, "_" );
					if ( $pos !== false ) {
						$columnname = substr( $newField, 0, $pos );
						$datatype   = substr( $newField, $pos + 1 );
					} else {
						$columnname = $newField;
						$datatype   = null;
					}
					$this->addColumn( $columnname, $datatype );
				}
			}
			$result = 3;
		}

		$this->csvInsert( $csv );
		$this->csvUpdate( $csv );
		$this->removeRedundantValues();
		( new ApdDatabaseService() )->updateAsins();

		if ( $new ) {
			$this->addColumn( 'PostId', 'int' );
			$this->addColumn( 'Permalink', 'text' );
		}

		return $result;
	}

	/**
	 * Adds a column to the table
	 *
	 * @param $columnname
	 * @param string $datatype
	 *
	 * @return bool
	 */
	public function addColumn( $columnname, $datatype = "text" ) {
		global $wpdb;

		if ( empty( $datatype ) OR $datatype === '' ) {
			$datatype = "text";
		}

		$sql    = "ALTER TABLE $this->tablename ADD `$columnname` $datatype";
		$result = $wpdb->query( $wpdb->prepare( $sql, '' ) );
		if ( $result ) {
			return true;
		} else {
			$error = "Couldn't create column $columnname in $this->tablename";
			print_error( $error, __METHOD__, __LINE__ );

			return false;
		}
	}
}