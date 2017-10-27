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

	public function getPostCategories() {
		$args = array(
			'hide_empty' => 0,
			'orderby'    => 'name'
		);

		return $categories = get_categories( $args );
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
		$tablename = add_table_prefix( $tablename );
		$sql       = "SELECT Asin FROM $tablename";
		$asins     = $wpdb->get_results( $wpdb->prepare( $sql, '' ), ARRAY_N );
		$asins     = array_filter( array_values_recursive( $asins ) );

		return $asins;
	}

	/**
	 * get every asin from all product tables as array
	 *
	 * @param bool $extend if supplied, function returns an array with the name
	 * of the table where the asin was found and the associated post ID
	 *
	 * @return array
	 */
	public function getAllAsins( $extend = false ) {

		global $wpdb;
		$tables = $this->getProductTables();

		$asinsArray = array();
		foreach ( $tables as $table ) {
			$sql   = "SELECT Asin, PostId FROM $table";
			$asins = $wpdb->get_results( $wpdb->prepare( $sql, '' ), ARRAY_N );
//			$asinsArray[ $table ]  = array_filter( array_values_recursive( $asins ) );
			$asinsArray[ $table ] = array_filter( $asins );
		}

		$extendedAsinsArray = array();
		$simpleAsinsArray   = array();
		foreach ( $asinsArray as $key => $asins ) {
			foreach ( $asins as $asin ) {
				$simpleAsinsArray[]   = $asin[0];
				$extendedAsinsArray[] = array(
					'table'   => $key,
					'asin'    => $asin[0],
					'post_id' => $asin[1]
				);
			}
		}

		if ( $extend ) {
			return $extendedAsinsArray;
		} else {
			return $simpleAsinsArray;
		}
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
	 * Check if database core tables exist. If not create them. These tables are necessary for the plugin to work.
	 *
	 * This function is meant to catch database inconsistencies, e.g. if a database table was manually deleted by
	 * accident, or if an update requires a new table that older versions don't have.
	 */
	public function checkDatabaseTables() {
		$tableListTable = new ApdDatabase( APD_TABLE_LIST_TABLE );
		//create table list
		if ( ! $tableListTable->checkTableExistence() ) {
			$tablename = APD_TABLE_LIST_TABLE;
			$database  = new ApdDatabase( $tablename );
			$database->createTableFromArray( $this->getListFields(), 'core' );
			$database->modifyColumns( $this->getUniqueListFields(), 'unique' );
		}

		/**
		 * Create asin table if it doesn't exist
		 */
		$asinTable = new ApdDatabase( APD_ASIN_TABLE );

		if ( ! $asinTable->checkTableExistence() ) {
			$asinTable->createTableFromArray( ApdCustomItem::getItemFields(), 'core' );
			$asinTable->modifyColumns( ApdCustomItem::getUniqueItemFields(), 'unique' );
		}

		/**
		 * Create amazon cache items table if it doesn't exist
		 * If it does exist, but it's structure doesn't match the classes fields, update it
		 */
		$amazonCacheTable    = new ApdDatabase( APD_AMAZON_CACHE_TABLE );
		$amazonCacheDatabase = new ApdAmazonCacheDatabase();
		$amazonCacheColumns  = $amazonCacheDatabase->getAmazonCacheColumns();

		if ( ! $amazonCacheTable->checkTableExistence() ) {
			$amazonCacheTable->createTableFromArray( $amazonCacheDatabase->getAmazonCacheColumns(), 'cache' );
			$amazonCacheTable->modifyColumns( $amazonCacheColumns, 'unique' );
		} else if ( ! $amazonCacheTable->checkTableIntegrity( $amazonCacheColumns ) ) {
			$columnDiff = $amazonCacheTable->getTableDiff( $amazonCacheColumns );
			$amazonCacheTable->addColumns( $columnDiff, 'text' );
		}

		/**
		 * Create cache options table if it doesn't exist
		 * If it does exist, but it's structure doesn't match the classes fields, update it
		 */
		$cacheOptionsTable   = new ApdDatabase( APD_CACHE_OPTIONS_TABLE );
		$cacheOptionsColumns = $amazonCacheDatabase->getOptionFields();
		//create amazon cache options table
		if ( ! $cacheOptionsTable->checkTableExistence() ) {
			$cacheOptionsTable->createTableFromArray( $cacheOptionsColumns, 'cache' );
		} else if ( ! $cacheOptionsTable->checkTableIntegrity( $cacheOptionsColumns ) ) {
			$columnDiff = $cacheOptionsTable->getTableDiff( $cacheOptionsColumns );
			$cacheOptionsTable->addColumns( $columnDiff, 'text' );
		}
	}

	/**
	 * update the asin table
	 *
	 * @return bool
	 */
	public function updateAsins() {
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