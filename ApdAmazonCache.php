<?php

class ApdAmazonCache {

	/**
	 * @todo not used anymore.
	 *
	 * @var array
	 */
//	protected $amazonCacheColumns = array(
//		'Asin',
//		'DetailPageURL',
//		'SalesRank',
//		'TotalReviews',
//		'AverageRating',
//		'SmallImageUrl',
//		'SmallImageHeight',
//		'SmallImageWidth',
//		'MediumImageUrl',
//		'MediumImageHeight',
//		'MediumImageWidth',
//		'LargeImageUrl',
//		'LargeImageHeight',
//		'LargeImageWidth',
//		'Subjects',
//		'Features',
//		'LowestNewPrice',
//		'LowestNewPriceCurrency',
//		'LowestNewPriceFormattedPrice',
//		'LowestUsedPrice',
//		'LowestUsedPriceCurrenty',
//		'LowestUsedPriceFormattedPrice',
//		'SalePriceAmount',
//		'SalePriceFormatted',
//		'SalePriceCurrencyCode',
//		'TotalNew',
//		'TotalUsed',
//		'TotalCollectible',
//		'TotalRefurbished',
//		'MerchantMerchantId',
//		'MerchantMerchantName',
//		'MerchantGlancePage',
//		'MerchantCondition',
//		'MerchantOfferListingId',
//		'MerchantPrice',
//		'MerchantCurrencyCode',
//		'MerchantFormattedPrice',
//		'MerchantAvailability',
//		'MerchantIsEligibleForSuperSaverShipping',
//		'CustomerReviews',
//		'EditorialReviews',
//		'Source',
//		'Content',
//		'SimilarProducts',
//		'Accessories',
//		'Track',
//		'ListmaniaLists',
//		'CurrencyCode',
//		'Amount',
//		'FormattedPrice',
//		'ListPriceFormatted',
//		'Brand',
//		'EAN',
//		'Feature',
//		'Label',
//		'Manufacturer',
//		'ProductGroup',
//		'ProductTypeName',
//		'Publisher',
//		'Studio',
//		'Title',
//		'CustomerReviewsIFrameUrl',
//		'CustomerReviewsImgTag',
//		'CustomerReviewsImgSrc',
//		'CustomerReviewsTotalReviews',
//		'CustomerReviewsIFrameUrl2'
//	);

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

		$database = new ApdDatabase( $this->tablenameOptions );
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
		$result            = $wpdb->update( $this->tablenameOptions, $data, array( 'id' => 1 ) );

		//error handling if database update didn't work
		if ( $result === false ) {
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

		$database = new ApdDatabase( $this->tablenameOptions );
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

		$database     = new ApdDatabase( $this->tablenameOptions );
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
	 * Update the Amazon cache
	 */
	public function updateCache() {

		//methods updateCacheItems oder getAmazonItems
		$this->updateCacheAsins();
		$itemsPerUpdate              = $this->getOption( 'items_per_update' );
		$currentInterval             = $this->getOption( 'interval_minutes' );
		$incInterval                 = $this->getOption( 'inc_interval_rate_minutes' );
		$decInterval                 = $this->getOption( 'dec_interval_rate_minutes' );
		$startId                     = $this->getOption( 'last_checked_id' );
		$startId                     = ( $startId === null ) ? 0 : $startId;                     //@todo make column in table have 0 for default. Make ApdDatabase::modifyColumns allow default values
		$successfulRequests          = $this->getOption( 'successful_requests' );
		$successfulRequestsThreshold = $this->getOption( 'successful_requests_threshold' );


		// request info for x items from Amazon API
		$amazonItems = $this->getAmazonItems( $itemsPerUpdate, $startId );

		// if something went wrong with the request
		if ( $amazonItems == 'throttle' ) {
			//set new interval
			$options = array( 'interval_minutes' => $currentInterval + $incInterval );
			$this->setOptions( $options );

			// create a new cronjob with increased interval
			$this->setCronjob( $currentInterval + $incInterval );

			// set number of successful requests to 0
			$options = array( 'successful_requests' => 0 );
			$this->setOptions( $options );

			//@todo #lastedit

			// else
		} else {

			// update items in cache with returned amazon items
			$lastUpdatedId = $this->updateCacheProducts( $amazonItems );

			// set new last updated item
			if ( $lastUpdatedId !== null ) {
				$options = array( 'last_checked_id' => $lastUpdatedId );
				$this->setOptions( $options );
			}
			// increase number of successful attempts by 1
			$options = array( 'successful_requests' => $successfulRequests + 1 );
			$this->setOptions( $options );

			// if x request attempts were successful, decrease the interval by x and create new cronjob
			if ( $successfulRequests >= $successfulRequestsThreshold ) {
				//interval can't ne smaller than 1
				$newInterval = ( $currentInterval < 2 ) ? $currentInterval = 1 : $currentInterval - $decInterval;
				$options = array( 'interval_minutes' => $newInterval );
				$this->setOptions( $options );
				$this->setCronjob( $newInterval );
			}
		}

	}

	/**
	 * get a number of items from Amazon API starting from last checked item
	 *
	 * @param $numberOfRows
	 *
	 * @return array|bool|string
	 */
	public function getAmazonItems( $numberOfRows, $startId ) {

		global $wpdb;
		$apdCore   = new ApdCore();
		$amazonWbs = $apdCore->amazonWbs;

		$sql = "SELECT Asin FROM $this->tablenameCache WHERE id >= $startId LIMIT $numberOfRows";

		$asins = $wpdb->get_results( $sql, ARRAY_A );

		if ( count( $asins ) == 0 ) {
			$options = array( 'last_checked_id' => 0 );
			$this->setOptions( $options );
		}

		$asins = array_filter( array_values_recursive( $asins ) );

		$amazonItems = array();
		foreach ( $asins as $asin ) {
			$amazonItem    = new ApdAmazonItem( $amazonWbs, $asin );
			$amazonItems[] = $amazonItem->getArrayAssoc();

			if ( "Amazon returns throttle error" === true ) {                                   //@todo catch if request throttle error occurs
				return "throttle";
			}
		}

		return $amazonItems;
	}

	/**
	 * Get asins from every products table.
	 * Fill in missing asins and delete unknown asins from cache table.
	 */
	public function updateCacheAsins() {

		global $wpdb;
		$databaseService = new ApdDatabaseService();
		$productAsins    = $databaseService->getAllProductAsins();


		$sql        = "SELECT Asin FROM $this->tablenameCache";
		$cacheAsins = $wpdb->get_results( $wpdb->prepare( $sql, '' ) );
		$cacheAsins = array_filter( array_values_recursive( $cacheAsins ) );

		//asins from product tables that don't exist in cache yet
		$diffProducts = array_diff( $productAsins, $cacheAsins );
		if ( ! empty( $diffProducts ) ) {
			$sql = "REPLACE INTO $this->tablenameCache (Asin) VALUES ";
			foreach ( $diffProducts as $diffProduct ) {
				$sql .= "(%s), ";
			}
			$sql = rtrim( $sql, " ," ) . ";";
			krumo($wpdb->prepare( $sql, $diffProducts ));
			$wpdb->query( $wpdb->prepare( $sql, $diffProducts ) );
		}


		//asins from cache table that don't exist in procuts anymore
		$diffCache = array_diff( $cacheAsins, $productAsins );
		if ( ! empty( $diffCache ) ) {
			$sql = "DELETE FROM $this->tablenameCache WHERE `Asin` IN (";
			foreach ( $diffCache as $item ) {
				$sql .= "%s, ";
			}
			$sql = rtrim( $sql, " ," ) . ");";
			$wpdb->query( $wpdb->prepare( $sql, $diffCache ) );
		}

	}

	/**
	 * Update products in cache table with provided array of Amazon objects
	 *
	 * @param array $amazonItems
	 *
	 * @return int
	 */
	public function updateCacheProducts( array $amazonItems ) {

		global $wpdb;

		$success = 0;

		$sql = "SET @update_id := 0;";
		$wpdb->query( $sql );

		$count = count( $amazonItems );
		$i     = 0;
		foreach ( $amazonItems as $amazonItem ) {
			$sql = "UPDATE $this->tablenameCache SET ";

			foreach ( $amazonItem as $key => $value ) {
				$sql .= "`$key` = '%s', ";
			}
			$sql = rtrim( $sql, " ," );

			//set the id for the last updated item and retrieve it later on
			if ( $i == $count - 1 ) {
				$sql .= ", id = (SELECT @update_id := id)";
			}

			$sql .= " WHERE `Asin` = '$amazonItem[ASIN]';";

			$result = $wpdb->query( $wpdb->prepare( $sql, $amazonItem ) );

			if ( ! $result ) {
				$error = "Amazon item $amazonItem[ASIN] couldn't be updated";
				print_error( $error, __METHOD__, __LINE__ );
			}

			$i ++;
		}

		//get last updated item
		$sql        = "SELECT @update_id;";
		$lastItemId = $wpdb->get_var( $sql );

		return $lastItemId;
	}
}


