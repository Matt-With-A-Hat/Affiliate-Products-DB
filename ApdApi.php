<?php

Class ApdApi {

	function __construct() {
	}

	/**
	 * get an item with every value from the custom database and from amazon by its asin
	 *
	 * @param $asin
	 * @param $refined
	 *
	 * @return array
	 */
	public function getItemByAsin( $asin, $refined = true ) {
		$amazonCacheItem = new ApdAmazonCacheItem( $asin );
		$apdCustomItem   = new ApdCustomItem( $asin );

		if ( $refined ) {
			$amazonCacheItemArrayA = $amazonCacheItem->getArrayA();
			$apdCustomItemArrayA   = $apdCustomItem->getArrayR();
		} else {
			$amazonCacheItemArrayA = $amazonCacheItem->getArrayA();
			$apdCustomItemArrayA   = $apdCustomItem->getArrayA();
		}

		if ( is_array( $amazonCacheItem ) AND is_array( $apdCustomItemArrayA ) ) {
			return array_merge( $amazonCacheItemArrayA, $apdCustomItemArrayA );
		} else {
			return null;
		}
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
		$sql       = "SELECT `asin` FROM $asintable WHERE post_id = $postId AND `Disabled` = '0'";
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
		$sql       = "SELECT `asin` FROM $tablename WHERE `PromoClass` = 'bestseller' AND `Disabled` = '0'";
		$asin      = $wpdb->get_var( $wpdb->prepare( $sql, '' ) );

		return $this->getItemByAsin( $asin );
	}
}