<?php

class ApdDatabaseService {

	protected $tablename;

	protected $listFields = array(
		'tablename',
		'purpose'
	);

	protected $uniqueListFields = array(
		'tablename'
	);

	/**
	 * ApdDatabaseService constructor.
	 */
	public function __construct() {
		$this->setTablename( APD_TABLE_LIST_TABLE );
	}

	/**
	 * @return array
	 */
	public function getListFields() {
		return $this->listFields;
	}

	/**
	 * @return array
	 */
	public function getUniqueListFields() {
		return $this->uniqueListFields;
	}

	/**
	 * @param mixed $tablename
	 */
	public function setTablename( $tablename ) {
		$this->tablename = add_table_prefix( $tablename );
	}

	/**
	 * @param string $newTable
	 * @param string $purpose
	 */
	public function updateTableList( $newTable, $purpose ) {

		global $wpdb;

		$column1 = $this->listFields[0];
		$column2 = $this->listFields[1];

		$sql    = "SELECT * FROM $this->tablename WHERE $column1 = %s";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $newTable ) );

		if ( $result ) {
			$error = "Table $newTable already exists";
			print_error( $error, __METHOD__, __LINE__ );
		}

		$array = array(
			$column1 => $newTable,
			$column2 => $purpose
		);

		$return = $wpdb->insert( $this->tablename, $array );

		if ( $result === false ) {
			$error = "Error when trying to insert data into $this->tablename";
		}
	}

	/**
	 * get all the APD tables that store imported products of any kind
	 *
	 * @return array
	 */
	public function getProductTables() {

		global $wpdb;

		$sql    = "SELECT * FROM $this->tablename WHERE purpose = %s";
		$result = $wpdb->get_col( $wpdb->prepare( $sql, 'products' ), 1 );

		return $result;
	}

	/**
	 * get every asin from all product tables
	 *
	 * @return array
	 */
	public function getAllProductAsins() {

		global $wpdb;
		$tables = $this->getProductTables();

		$asins = array();
		foreach ( $tables as $table ) {
			$sql = "SELECT Asin FROM $table";
			$asins[] = $wpdb->get_results($wpdb->prepare($sql, ''), ARRAY_N);
		}

		$asinsRefined = array_filter(array_values_recursive($asins));

		return $asinsRefined;
	}
}