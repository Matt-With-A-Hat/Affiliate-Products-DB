<?php

class ApdDatabaseService {

	/**
	 * the name of the table where all the plugins create tablenames are listed
	 *
	 * @var
	 */
	protected $tableListTable;

	/**
	 * all columns of the table-list table
	 *
	 * @var array
	 */
	protected $listFields = array(
		'tablename',
		'purpose'
	);

	/**
	 * unique columns of the table-list table
	 *
	 * @var array
	 */
	protected $uniqueListFields = array(
		'tablename'
	);

	/**
	 * ApdDatabaseService constructor.
	 */
	public function __construct() {
		$this->setTableListTable( APD_TABLE_LIST_TABLE );
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
	 * @param mixed $tableListTable
	 */
	public function setTableListTable( $tableListTable ) {
		$this->tableListTable = add_table_prefix( $tableListTable );
	}

	/**
	 * @param string $newTable
	 * @param string $purpose
	 */
	public function updateTableList( $newTable, $purpose ) {

		global $wpdb;

		$column1 = $this->listFields[0];
		$column2 = $this->listFields[1];

		$sql    = "SELECT * FROM $this->tableListTable WHERE $column1 = %s";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $newTable ) );

		if ( $result ) {
			$error = "Table $newTable already exists";
			print_error( $error, __METHOD__, __LINE__ );
		}

		$array = array(
			$column1 => $newTable,
			$column2 => $purpose
		);

		$return = $wpdb->insert( $this->tableListTable, $array );

		if ( $result === false ) {
			$error = "Error when trying to insert data into $this->tableListTable";
		}
	}

	/**
	 * get all the APD tables that store imported products of any kind
	 *
	 * @return array
	 */
	public function getProductTables() {

		global $wpdb;

		$sql    = "SELECT * FROM $this->tableListTable WHERE purpose = %s";
		$result = $wpdb->get_col( $wpdb->prepare( $sql, 'products' ), 1 );

		return $result;
	}

	/**
	 * get every asin of the specified table
	 *
	 * @param $tablename
	 *
	 * @return array|null|object
	 */
	public function getAsins( $tablename ) {

		global $wpdb;
		$tablename = add_table_prefix($tablename);
		$sql   = "SELECT Asin FROM $tablename";
		$asins = $wpdb->get_results( $wpdb->prepare( $sql, '' ), ARRAY_N );
		$asins = array_filter( array_values_recursive( $asins ) );

		return $asins;
	}

	/**
	 * get every asin from all product tables as array
	 *
	 * @param bool $assoc if supplied, function returns an associative array with the name
	 * of the table where the asin was found
	 *
	 * @return array
	 */
	public function getAllAsins( $assoc = false ) {

		global $wpdb;
		$tables = $this->getProductTables();

		$asins      = array();
		$asinsArray = array();
		foreach ( $tables as $table ) {
			$sql                  = "SELECT Asin FROM $table";
			$asins                = $wpdb->get_results( $wpdb->prepare( $sql, '' ), ARRAY_N );
			$asinsArray[ $table ] = array_filter( array_values_recursive( $asins ) );
		}

		if ( $assoc ) {
			$asins = array();
			foreach ( $asinsArray as $key => $array ) {
				foreach ( $array as $item ) {
					$asins[] = array(
						'table' => $key,
						'asin'  => $item
					);
				}
			}
		} else {
			$asins = array_filter( array_values_recursive( $asinsArray ) );
		}

		return $asins;
	}

	/**
	 * update the asin table
	 *
	 * @return bool
	 */
	function updateAsins() {
		global $wpdb;
		$asins       = $this->getAllAsins( true );
		$asinTable   = add_table_prefix( APD_ASIN_TABLE );
		$currentTime = current_time( 'mysql' );

		//add asins of products that don't exist in asins table yet
		$sql = "REPLACE INTO $asinTable (`asin`, `table`, `last_edit`) VALUES ";
		foreach ( $asins as $asin ) {
			$sql .= "('$asin[asin]', '$asin[table]', '$currentTime'), ";
		}
		$sql    = rtrim( $sql, " ," ) . ";";
		$result = $wpdb->query( $wpdb->prepare( $sql, '' ) );

		//remove asins of products that have been deleted
		$productsAsins = $this->getAllAsins();
		$sql           = "SELECT Asin FROM $asinTable";
		$tableAsins    = $wpdb->get_results( $wpdb->prepare( $sql, '' ) );
		$tableAsins    = array_filter( array_values_recursive( $tableAsins ) );

		$diffTable = array_diff( $tableAsins, $productsAsins );
		if ( ! empty( $diffTable ) ) {
			$sql = "DELETE FROM $asinTable WHERE `asin` IN (";
			foreach ( $diffTable as $item ) {
				$sql .= "%s, ";
			}
			$sql = rtrim( $sql, " ," ) . ");";
			$wpdb->query( $wpdb->prepare( $sql, $diffTable ) );
		}

		return (bool) $result;
	}
}