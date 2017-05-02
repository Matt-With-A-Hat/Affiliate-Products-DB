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

		$this->setTablenameCache( APD_AMAZON_ITEMS_TABLE );
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
		$apdDatabase          = new ApdDatabase();
		$this->tablenameCache = $apdDatabase->addTablePrefix( $tablenameCache );
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
		$apdDatabase            = new ApdDatabase();
		$this->tablenameOptions = $apdDatabase->addTablePrefix( $tablenameOptions );
	}


	/**
	 * @return array
	 */
	public function getAmazonFields() {
		return $this->amazonFields;
	}

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

		$apdDatabase = new ApdDatabase();
		$columns     = $apdDatabase->getTableColumns( $this->tablenameOptions );

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
		$data = $options;
		$data['last_edit'] = current_time( 'mysql' );
		$result = $wpdb->update( $this->tablenameOptions, $data, array( 'ID' => 0 ) );

		//if no row was updated table is probably still empty
		if ( $result == 0 ) {
			$result = $wpdb->insert( $this->tablenameOptions, $data );
		}

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

	public function getOptions() {

		$apdDatabase = new ApdDatabase();
		$result      = $apdDatabase->getRow( $this->tablenameOptions, 0, $this->getOptionFields() );

	}

	public function getOption() {

	}

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
			echo "Cronjob couldn't be created";
		}
	}

	public function updateCache() {

		// request info from Amazon API

		// if something went wrong with the request
		// create a new cronjob with increased interval
		// set number of successful requests to 0

		// else if request contains amazon item
		// get the last updated item and update the next x items
		// set new last updated item
		// increase number of successful attempts by 1
		// if x request attempts were successful, decrease the interval by x

	}
}


