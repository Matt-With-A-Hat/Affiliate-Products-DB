<?php

Class ApdApi {

	function __construct() {
	}

	/**
	 * get an item with every value from the custom database and from amazon by its asin
	 *
	 * @param $asin
	 *
	 * @return array
	 */
	public function getItemByAsin( $asin ) {
		$amazonCacheItem = new ApdAmazonCacheItem( $asin );
		$apdCustomItem   = new ApdCustomItem( $asin );

		$amazonCacheItemArrayA = $amazonCacheItem->getArrayA();
		$apdCustomItemArrayA   = $apdCustomItem->getArrayR();

		return array_merge( $amazonCacheItemArrayA, $apdCustomItemArrayA );
	}

	/**
	 * get an item with every value from the custom database and from amazon by its post ID
	 *
	 * @param $postId
	 *
	 * @return null|string
	 */
	public function getItemByPostId( $postId ) {
		global $wpdb;
		$asintable = add_table_prefix( APD_ASIN_TABLE );
		$sql       = "SELECT `asin` FROM $asintable WHERE post_id = $postId";
		$asin      = $wpdb->get_var( $wpdb->prepare( $sql, '' ) );

		return $this->getItemByAsin( $asin );
	}

	/**
	 * get the bestseller of the provided table
	 *
	 * @param $tablename
	 *
	 * @return array
	 */
	public function getBestseller( $tablename ) {
		global $wpdb;
		$tablename = add_table_prefix( $tablename );
		$sql       = "SELECT `asin` FROM $tablename WHERE `PromoClass`= 'bestseller'";
		$asin      = $wpdb->get_var( $wpdb->prepare( $sql, '' ) );

		return $this->getItemByAsin( $asin );
	}
}