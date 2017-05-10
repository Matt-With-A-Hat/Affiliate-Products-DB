<?php

class ApdAmazonCacheItem extends ApdAmazonCache {

	public function __construct( $asin ) {

		parent::__construct();

		$this->setAsin( $asin );
	}

	private $asin;

	/**
	 * @return mixed
	 */
	public function getAsin() {
		return $this->asin;
	}

	/**
	 * @param mixed $asin
	 */
	public function setAsin( $asin ) {
		$this->asin = $asin;
	}

	public function getArray() {
		global $wpdb;
		$tablename = $this->getTablenameCache();

		$sql = "SELECT * FROM $tablename WHERE Asin = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $this->asin ), ARRAY_N );

		if ( is_array( $result ) ) {
			array_shift( $result );

			return $result;
		} else {
			return null;
		}
	}
}