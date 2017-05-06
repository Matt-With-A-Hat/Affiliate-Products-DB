<?php

class ApdAmazonCache {

	protected $amazonFields = array(
		'ASIN',
		'DetailPageURL',
		'SalesRank',
		'TotalReviews',
		'AverageRating',
		'SmallImageUrl',
		'SmallImageHeight',
		'SmallImageWidth',
		'MediumImageUrl',
		'MediumImageHeight',
		'MediumImageWidth',
		'LargeImageUrl',
		'LargeImageHeight',
		'LargeImageWidth',
		'Subjects',
		'Features',
		'LowestNewPrice',
		'LowestNewPriceCurrency',
		'LowestNewPriceFormattedPrice',
		'LowestUsedPrice',
		'LowestUsedPriceCurrenty',
		'LowestUsedPriceFormattedPrice',
		'SalePriceAmount',
		'SalePriceFormatted',
		'SalePriceCurrencyCode',
		'TotalNew',
		'TotalUsed',
		'TotalCollectible',
		'TotalRefurbished',
		'MerchantMerchantId',
		'MerchantMerchantName',
		'MerchantGlancePage',
		'MerchantCondition',
		'MerchantOfferListingId',
		'MerchantPrice',
		'MerchantCurrencyCode',
		'MerchantFormattedPrice',
		'MerchantAvailability',
		'MerchantIsEligibleForSuperSaverShipping',
		'CustomerReviews',
		'EditorialReviews',
		'Source',
		'Content',
		'SimilarProducts',
		'Accessories',
		'Track',
		'ListmaniaLists',
		'CurrencyCode',
		'Amount',
		'FormattedPrice',
		'ListPriceFormatted',
		'Brand',
		'EAN',
		'Feature',
		'Label',
		'Manufacturer',
		'ProductGroup',
		'ProductTypeName',
		'Publisher',
		'Studio',
		'Title',
		'CustomerReviewsIFrameUrl',
		'CustomerReviewsImgTag',
		'CustomerReviewsImgSrc',
		'CustomerReviewsTotalReviews',
		'CustomerReviewsIFrameUrl2'
	);

	protected $uniqueAmazonFields = array(
		'ASIN'
	);

	protected $optionFields = array(
		'interval_minutes',
		'inc_interval_rate_minutes',
		'dec_interval_rate_minutes',
		'dec_interval_every_nth',
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
		$database               = new ApdDatabase($tablenameOptions);
		$this->tablenameOptions = $database->getTablename();
	}

	/**
	 * @return array
	 */
	public function getAmazonFields() {
		return $this->amazonFields;
	}

	/**
	 * @return array
	 */
	public function getUniqueAmazonFields() {
		return $this->uniqueAmazonFields;
	}

	/**
	 * @return array
	 */
	public function getOptionFields() {
		return $this->optionFields;
	}

	/**
	 * set or update options in database
	 *
	 * @param array|null $options
	 *
	 * @return bool
	 */
	public function setOptions( array $options ) {

		$database = new ApdDatabase($this->tablenameOptions);
		$columns  = $database->getTableColumns();

		//@todo on new install cache options can only be empty
		if ( $columns == false ) {
			if ( APD_DEBUG ) {
				$error = "Cache options table is empty";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		$columnsFlipped = array_flip( $columns );
		if ( count( array_diff_key( $options, $columnsFlipped ) ) > 0 ) {
			if ( APD_DEBUG ) {
				$error = "Array contains fields that are not cache options";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		global $wpdb;
		$data              = $options;
		$data['last_edit'] = current_time( 'mysql' );
		$result            = $wpdb->update( $this->tablenameOptions, $data, array( 'ID' => 1 ) );

		//error handling if database update didn't work
		if ( $result == false ) {
			if ( APD_DEBUG ) {
				$error = "Options couldn't be updated";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		return true;
	}

	/**
	 * get all options
	 *
	 * @return array|null|object|void
	 */
	public function getOptions() {

		$database = new ApdDatabase($this->tablenameOptions);
		$result   = $database->getRow( 1, $this->getOptionFields() );

		return $result;
	}

	/**
	 * get a single option
	 *
	 * @param $option
	 *
	 * @return mixed
	 */
	public function getOption( $option ) {

		$database     = new ApdDatabase($this->tablenameOptions);
		$optionsArray = array( 0 => $option );
		$resultArray  = $database->getRow( 1, $optionsArray );
		$result       = reset( $resultArray );

		return $result;
	}

	/**
	 * Creates a new apdcronjob and kills the old one, if the supplied interval is different from
	 * the current one in options.
	 *
	 * If the supplied interval equals the current interval in options, a new apdcronjob will be
	 * created, if there is no apdcronjob yet.
	 *
	 * @param $interval
	 */
	public function setCronjob( $interval ) {

		$currentInterval = $this->getOption( 'interval_minutes' );

		if ( $currentInterval != $interval ) {
			$timestamp = wp_next_scheduled( 'apdcronjob' );
			wp_unschedule_event( $timestamp, 'apdcronjob' );
			wp_schedule_event( time(), $interval, 'apdcronjob' );

		} else if ( $currentInterval == $interval ) {
			if ( ! wp_next_scheduled( 'apdcronjob' ) ) {
				wp_schedule_event( time(), $interval, 'apdcronjob' );
			}

		} else if ( APD_DEBUG ) {
			$error = "Cronjob couldn't be created";
			print_error( $error, __METHOD__, __LINE__ );
		}
	}


	/**
	 * collect all Asins from product tables
	 */
	public function getAsinsFromTables() {

	}

	public function getNextItems() {

	}

	/**
	 * get a number of items from Amazon API starting from last checked item
	 *
	 * @param $numberOfRows
	 *
	 * @return array|bool|string
	 */
	public function getAmazonItems( $numberOfRows ) {

		global $wpdb;
		$apdCore         = new ApdCore();
		$amazonWbs       = $apdCore->amazonWbs;
		$tablename       = $wpdb->prefix . 'products';                                       //@todo Fill amazonCache with ASINs from other tables. Check for new ASINs on every update
		$lastCheckedItem = $this->getOption( 'last_checked_id' );
		$lastCheckedItem = ( $lastCheckedItem === null ? 0 : $lastCheckedItem );

		$sql = "SELECT Asin_unique FROM $tablename LIMIT $lastCheckedItem, $numberOfRows";   //@todo make this "ASIN". Remove underscores from DB

		$asins = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $asins ) ) {
			$error = "Query didn't return any results";
			print_error( $error, __METHOD__, __LINE__ );

			return false;
		}

		foreach ( $asins as $asin ) {
			$asinArray[] = $asin['Asin_unique'];
		}

		foreach ( $asinArray as $asin ) {
			$amazonItem    = new ApdAmazonItem( $amazonWbs );
			$amazonItems[] = $amazonItem->getAmazonItem( $asin );

			if ( "Amazon returns throttle error" === true ) {                                   //@todo catch if request throttle error occurs
				return "throttle";
			}
		}

		return $amazonItems;
	}

	public function updateCache() {

		//methods updateCacheItems oder getAmazonItems

		$numberPerUpdate = $this->getOption( 'items_per_update' );

		// request info for x items from Amazon API
		$amazonItems = $this->getAmazonItems( $numberPerUpdate );

		// if something went wrong with the request
		if ( $amazonItems == 'throttle' ) {
			// create a new cronjob with increased interval
			$currentInterval = $this->getOption( 'interval_minutes' );
			$incInterval     = $this->getOption( 'inc_interval_rate_minutes' );
			$this->setCronjob( $currentInterval + $incInterval );

			// set number of successful requests to 0
			$options = array( 'successful_requests' => 0 );
			$this->setOptions( $options );

			//@todo #lastedit

			// else
		} else {
			// get the last updated item and update the next x items

			// set new last updated item
			// increase number of successful attempts by 1
			// if x request attempts were successful, decrease the interval by x
		}


		krumo( $amazonItems );
	}
}


