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
	private $array;

	/**
	 * The item from the database stored as associative array
	 *
	 * @var
	 */
	private $assocArray;

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
	public function getArray() {
		return $this->array;
	}

	/**
	 * @return mixed
	 */
	public function getAssocArray() {
		return $this->assocArray;
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

			$this->array = $result;
		} else {
			$this->array = null;
		}
	}

	/**
	 * set the associative array variable
	 */
	private function setAssocArray() {
		$fieldsArray = self::getAmazonItemFields();
		if($this->array === null){
			$valuesArray = $this->setArray();
		}else{
			$valuesArray = $this->getArray();
		}

		if ( $fieldsArray === null ) {
			$error = "Supplied fields array is empty";
			print_error( $error, __METHOD__, __LINE__ );
		} else if ( $valuesArray === null ) {
			$error = "Supplied values array is empty";
			print_error( $error, __METHOD__, __LINE__ );
		}

		$this->assocArray = array_combine( $fieldsArray, $valuesArray );
	}

	/**
	 * set the object variable
	 */
	public function setObject() {
		if($this->assocArray === null){
			$assocArray = $this->setAssocArray();
		}else{
			$assocArray = $this->getAssocArray();
		}
		$this->object = (object) $assocArray;
	}

}