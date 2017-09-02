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
	function getItemByAsin( $asin ) {
		$amazonCacheItem = new ApdAmazonCacheItem( $asin );
		$apdCustomItem   = new ApdCustomItem( $asin );

		$amazonCacheItemArrayA = $amazonCacheItem->getAssocArray();
		$apdCustomItemArrayA   = $apdCustomItem->getArrayAssoc();

		krumo($amazonCacheItemArrayA);
		krumo($apdCustomItemArrayA);

		return array_merge( $amazonCacheItemArrayA, $apdCustomItemArrayA );
	}

	/**
	 * get an item with every value from the custom database and from amazon by its post ID
	 *
	 * @param $postId
	 */
	function getItemByPostId( $postId ) {
		
	}
}