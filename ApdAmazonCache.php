<?php

class ApdAmazonCache {

	protected $uniqueAmazonCacheFields = array(
		'ASIN'
	);

	protected $optionFields = array(
		'interval_minutes',
		'inc_interval_rate_minutes',
		'dec_interval_rate_minutes',
		'successful_requests_threshold',
		'successful_requests',
		'items_per_update',
		'last_checked_id',
		'max_id',
		'last_edit'
	);

	protected $tablenameCache;

	protected $tablenameOptions;

	public function __construct() {
		$this->setTablenameCache( APD_AMAZON_CACHE_TABLE );
		$this->setTablenameOptions( APD_CACHE_OPTIONS_TABLE );
	}

	/**
	 * @return mixed
	 */
	public function getTablenameCache() {
		return $this->tablenameCache;
	}

	/**
	 * @param mixed $tablenameCache
	 */
	public function setTablenameCache( $tablenameCache ) {
		$database             = new ApdDatabase( $tablenameCache );
		$this->tablenameCache = $database->getTablename();
	}

	/**
	 * @return mixed
	 */
	public function getTablenameOptions() {
		return $this->tablenameOptions;
	}

	/**
	 * @param mixed $tablenameOptions
	 */
	public function setTablenameOptions( $tablenameOptions ) {
		$database               = new ApdDatabase( $tablenameOptions );
		$this->tablenameOptions = $database->getTablename();
	}

	/**
	 * Get the (database-)columns of the cache. Columns mirror the Amazon items fields.
	 *
	 * @return array
	 */
	public function getAmazonCacheColumns() {
//		return $this->amazonCacheFields;
		return ApdAmazonItem::getAmazonItemFields();
	}

	/**
	 * @return array
	 */
	public function getUniqueAmazonCacheColumns() {
		return $this->uniqueAmazonCacheFields;
	}
}