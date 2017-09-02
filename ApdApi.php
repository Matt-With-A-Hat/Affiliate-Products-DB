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

		$amazonCacheItemArrayA = $amazonCacheItem->getAssocArray();
		$apdCustomItemArrayA   = $apdCustomItem->getArrayAssoc();

//		krumo($amazonCacheItemArrayA);
//		krumo($apdCustomItemArrayA);

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
		$tableList = add_table_prefix(APD_ASIN_TABLE);
		$sql = "SELECT `asin` FROM $tableList WHERE post_id = $postId";
		krumo($sql);
		$asin = $wpdb->get_var( $wpdb->prepare( $sql, '' ) );

		return $this->getItemByAsin($asin);
	}
}