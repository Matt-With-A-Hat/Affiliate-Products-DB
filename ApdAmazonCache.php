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

	/**
	 * Amazon item fields respectively available template placeholders
	 */
	public static $amazonItemFields = array(
		'ASIN',
		'SmallImageUrl',
		'SmallImageWidth',
		'SmallImageHeight',
		'MediumImageUrl',
		'MediumImageWidth',
		'MediumImageHeight',
		'LargeImageUrl',
		'LargeImageWidth',
		'LargeImageHeight',
		'Label',
		'Manufacturer',
		'Publisher',
		'Studio',
		'Title',
		'AmazonUrl',
		'TotalOffers',
		'LowestOfferPrice',
		'LowestOfferCurrency',
		'LowestOfferFormattedPrice',
		'LowestNewPrice',
		'LowestNewOfferFormattedPrice',
		'LowestUsedPrice',
		'LowestUsedOfferFormattedPrice',
		'AmazonPrice',
		'AmazonPriceFormatted',
		'ListPriceFormatted',
		'AmazonCurrency',
		'AmazonAvailability',
		'AmazonLogoSmallUrl',
		'AmazonLogoLargeUrl',
		'DetailPageURL',
		'Platform',
		'ISBN',
		'EAN',
		'NumberOfPages',
		'ReleaseDate',
		'Binding',
		'Author',
		'Creator',
		'Edition',
		'AverageRating',
		'TotalReviews',
		'RatingStars',
		'RatingStarsSrc',
		'Director',
		'Actors',
		'RunningTime',
		'Format',
		'CustomRating',
		'ProductDescription',
		'AmazonDescription',
		'Artist',
		'Comment',
		'PercentageSaved',
		'Prime',
		'PrimePic',
		'ProductReviewsURL',
		'IFrameUrl',
		'TrackingId',
		'AmazonShopURL',
		'SalePriceAmount',
		'SalePriceCurrencyCode',
		'SalePriceFormatted',
		'Class',
		'OffersMainPriceAmount',
		'OffersMainPriceCurrencyCode',
		'OffersMainPriceFormattedPrice',
		'LastCacheUpdate'
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
	public static function getAmazonItemFields() {
		return self::$amazonItemFields;
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