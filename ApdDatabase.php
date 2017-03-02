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
	 * @param $csv
	 * path to csv file or $_FILE array of csv file
	 *
	 * @return array of fields
	 */
	public function getFieldsFromCSV( $csv ) {

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$csv = new SplFileObject( $csv );

		$fileDelimiter = $this->getFileDelimiter( $csv );

		$fields = $csv->fgetcsv( $fileDelimiter );

		return $fields;

	}

	/**
	 * @param $file
	 *
	 * @return mixed
	 */
	public function getFileDelimiter( $file ) {

		$control   = $file->getCsvControl( $file );
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

		$wpdb = $this->db;

		$tablename = $wpdb->prefix . $tablename;

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

		foreach ( $fields as $field ) {

			$sql .= ", $field TEXT";

		}

		$sql .= ')';

		$result = $wpdb->query( $sql );

		return $result;

	}

	/**
	 * Inserts csv content into the specified table
	 * @param $csv
	 * path to csv file or $_FILE array of csv file
	 *
	 * @return bool
	 */
	public function insertCsvContent( $csv, $tablename ) {

		$wpdb = $this->db;

		$tablename = $wpdb->prefix . $tablename;

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$fields = $this->getFieldsFromCSV( $csv );

		if(isLocalInstallation()) {
			$csvPathNoBackslash = str_replace("\\", "/", $csv);
		}

		$sql = "LOAD DATA LOCAL INFILE '" . $csvPathNoBackslash . "' INTO TABLE " . $tablename . "
			FIELDS TERMINATED BY ',' 
			ENCLOSED BY '\"' 
			LINES TERMINATED BY '\r\n'
			IGNORE 1 LINES 
			(";

		foreach ( $fields as $field ) {

			$sql .= $field . ", ";

		}

		$sql = trim($sql);
		$sql = rtrim($sql,",");
		$sql .= ")";

		echo $sql;

		$result = $wpdb->query( $sql );

		echo "<br>";
		echo "<br>";
		echo $result;

		return $result;

	}

	/**
	 * @param $csv
	 * path to csv file or $_FILE array of csv file
	 *
	 * @return bool
	 */
	public function addCsvToDatabase( $tablename, $csv ) {

		//@TODO check $tablename for injection!

		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}

		$wpdb = $this->db;

		//@TODO also check if fields have been added to the table
		//check if table already exists
		$sql            = "SHOW TABLES LIKE '%'";
		$existingTables = $wpdb->get_results( $sql, ARRAY_N );

		$exists = false;

		foreach ( $existingTables as $existingTable ) {

			if ( ! strcasecmp( $existingTable[0], $wpdb->prefix . $tablename ) ) {

				$exists = true;
				break;

			}

		}

		//create table if it doesn't exist yet
		if ( $exists === false ) {

			$fields = $this->getFieldsFromCSV( $csv );
			$result = $this->createTableFromFields( $tablename, $fields );

		}

		$result = $this->insertCsvContent( $csv, $tablename );

		return $result;
	}

}