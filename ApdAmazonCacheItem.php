<?php

class ApdAmazonCacheItem extends ApdAmazonCache {

	/**
	 * The items ASIN
	 *
	 * @var
	 */
	private $asin;

	/**
	 * The item from the database stored as array
	 *
	 * @var
	 */
	private $arrayN;

	/**
	 * The item from the database stored as associative array
	 *
	 * @var
	 */
	private $arrayA;

	/**
	 * The item from the database stored as object
	 *
	 * @var
	 */
	private $object;

	public function __construct( $asin ) {

		parent::__construct();

		$this->setAsin( $asin );
		$this->setArray();
		$this->setAssocArray();
		$this->setObject();
	}

	/**
	 * @return mixed
	 */
	public function getAsin() {
		return $this->asin;
	}

	/**
	 * @return mixed
	 */
	public function getArrayN() {
		return $this->arrayN;
	}

	/**
	 * @return mixed
	 */
	public function getArrayA() {
		return $this->arrayA;
	}

	/**
	 * @return mixed
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * @param mixed $asin
	 */
	private function setAsin( $asin ) {
		$this->asin = $asin;
	}

	/**
	 * set the array variable
	 */
	private function setArray() {
		global $wpdb;
		$tablename = $this->getTablenameCache();

		$sql = "SELECT * FROM $tablename WHERE Asin = %s";

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $this->asin ), ARRAY_N );

		if ( is_array( $result ) ) {
			array_shift( $result );

			$this->arrayN = $result;
		} else {
			$this->arrayN = null;
		}
	}

	/**
	 * set the associative array variable
	 */
	private function setAssocArray() {
		$fieldsArray = self::getAmazonItemFields();
		if( $this->arrayN === null){
			$this->setArray();
		}else{
			$valuesArray = $this->getArrayN();
		}

		if ( $fieldsArray === null ) {
			$error = "Supplied fields array is empty";
			print_error( $error, __METHOD__, __LINE__ );
			return null;
		} else if ( $valuesArray === null ) {
			$error = "Supplied values array is empty";
			print_error( $error, __METHOD__, __LINE__ );
			return null;
		}

		$this->arrayA = array_combine( $fieldsArray, $valuesArray );
		if($this->arrayA === false){
			$error = "Number of fields nodes not match. Checking database tables...";
			print_error( $error, __METHOD__, __LINE__ );
			(new ApdDatabaseService())->checkDatabaseTables();
		}
	}

	/**
	 * set the object variable
	 */
	public function setObject() {
		if( $this->arrayA === null){
			$assocArray = $this->setAssocArray();
		}else{
			$assocArray = $this->getArrayA();
		}
		$this->object = (object) $assocArray;
	}

}