<?php

class ApdAmazonItem extends ApdAmazonCache {

	/**
	 * the amazon webservice object
	 */
	protected $amazonWbs;

	/**
	 * The returned Amazon object
	 *
	 * @var
	 */
	protected $object;

	/**
	 * The refined array version of the Amazon object
	 *
	 * @var
	 */
	protected $array;

	/**
	 * The refined associative array version of the Amazon object
	 *
	 * @var
	 */
	protected $arrayAssoc;

	/**
	 * @var
	 */
	protected $cache;

	/**
	 * ApdAmazonItem constructor.
	 *
	 * @param $amazonWbs
	 */
	public function __construct( $amazonWbs, $asin ) {

		$this->amazonWbs = $amazonWbs;

		$this->object = $this->amazonWbs->itemLookup( $asin, array(
			'ResponseGroup' => 'ItemAttributes,Images,Offers,OfferListings,Reviews,EditorialReview,Tracks'
		) );

		$this->array = $this->refineAmazonItem();

		//If amazonWbs has returned an error, Asin will be null in object.
		//In that case the object still needs an asin, so it can properly be addressed.
		if ( $this->array[0] === null ) {
			$this->array[0] = $asin;
		}
		$this->arrayAssoc = $this->matchValuesWithFields();
	}

	/**
	 * get item information from amazon webservice
	 *
	 * @param       string      ASIN
	 *
	 * @return      object      AsaZend_Service_Amazon_Item object
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * @return mixed
	 */
	public function getArray() {
		return $this->array;
	}

	/**
	 * @return mixed
	 */
	public function getArrayAssoc() {
		return $this->arrayAssoc;
	}

	/**
	 * Make Amazon item a simple array that matches placeholder array
	 */
	private function refineAmazonItem() {

		$apdCore         = new ApdCore();
		$amazonObject    = $this->object;
		$amazonCacheItem = ( new ApdAmazonCacheItem( $amazonObject->ASIN ) )->getObject();

		if ( $amazonObject ) {

			$manualUpdate = '0';
			$errorMessage = 'Everything OK';
			if ( ! $amazonObject instanceof AsaZend_Service_Amazon_Item ) {
				//super ugly hack to access protected member
				$array   = (array) $amazonObject;
				$i       = 0;
				$message = false;
				foreach ( $array as $key => $item ) {
					$i ++;
					if ( $i == 3 ) {
						$hack    = $item;
						$message = $hack[0]['Message'];
					}
				}
				if ( $message ) {
					$error = $message;
				} else {
					$error = "Item is not an Amazon item";
				}
				print_error( $error, __METHOD__, __LINE__ );
				$manualUpdate = '1';
				$errorMessage = $error;
			}

			// get the customer rating object
			$customerReviews = $this->getCustomerReviews( $amazonObject );

			if ( isset( $amazonObject->Offers->LowestUsedPrice ) && isset( $amazonObject->Offers->LowestNewPrice ) ) {

				$lowestOfferPrice          = ( $amazonObject->Offers->LowestUsedPrice < $amazonObject->Offers->LowestNewPrice ) ?
					$amazonObject->Offers->LowestUsedPrice : $amazonObject->Offers->LowestNewPrice;
				$lowestOfferCurrency       = ( $amazonObject->Offers->LowestUsedPrice < $amazonObject->Offers->LowestNewPrice ) ?
					$amazonObject->Offers->LowestUsedPriceCurrency : $amazonObject->Offers->LowestNewPriceCurrency;
				$lowestOfferFormattedPrice = ( $amazonObject->Offers->LowestUsedPrice < $amazonObject->Offers->LowestNewPrice ) ?
					$amazonObject->Offers->LowestUsedPriceFormattedPrice : $amazonObject->Offers->LowestNewPriceFormattedPrice;

			} else if ( isset( $amazonObject->Offers->LowestNewPrice ) ) {

				$lowestOfferPrice          = $amazonObject->Offers->LowestNewPrice;
				$lowestOfferCurrency       = $amazonObject->Offers->LowestNewPriceCurrency;
				$lowestOfferFormattedPrice = $amazonObject->Offers->LowestNewPriceFormattedPrice;

			} else if ( isset( $amazonObject->Offers->LowestUsedPrice ) ) {

				$lowestOfferPrice          = $amazonObject->Offers->LowestUsedPrice;
				$lowestOfferCurrency       = $amazonObject->Offers->LowestUsedPriceCurrency;
				$lowestOfferFormattedPrice = $amazonObject->Offers->LowestUsedPriceFormattedPrice;
			}

			$lowestOfferPrice              = $apdCore->formatPrice( $lowestOfferPrice );
			$lowestNewPrice                = isset( $amazonObject->Offers->LowestNewPrice ) ? $apdCore->formatPrice( $amazonObject->Offers->LowestNewPrice ) : '';
			$lowestNewOfferFormattedPrice  = isset( $amazonObject->Offers->LowestNewPriceFormattedPrice ) ? $amazonObject->Offers->LowestNewPriceFormattedPrice : '';
			$lowestUsedPrice               = isset( $amazonObject->Offers->LowestUsedPrice ) ? $apdCore->formatPrice( $amazonObject->Offers->LowestUsedPrice ) : '';
			$lowestUsedOfferFormattedPrice = isset( $amazonObject->Offers->LowestUsedPriceFormattedPrice ) ? $amazonObject->Offers->LowestUsedPriceFormattedPrice : '';

			$amazonPrice          = $apdCore->getAmazonPrice( $amazonObject );
			$amazonPriceFormatted = $apdCore->getAmazonPrice( $amazonObject, true );

			if ( isset( $amazonObject->Offers->Offers[0]->Price ) && ! empty( $amazonObject->Offers->Offers[0]->Price ) ) {

				if ( isset( $amazonObject->Offers->SalePriceAmount ) ) {
					// set main price to sale price
					$offerMainPriceAmount       = $apdCore->formatPrice( (string) $amazonObject->Offers->SalePriceAmount );
					$offerMainPriceCurrencyCode = $amazonObject->Offers->SalePriceCurrencyCode;
					$offerMainPriceFormatted    = $amazonObject->Offers->SalePriceFormatted;
				} else {
					$offerMainPriceAmount       = $apdCore->formatPrice( (string) $amazonObject->Offers->Offers[0]->Price );
					$offerMainPriceCurrencyCode = (string) $amazonObject->Offers->Offers[0]->CurrencyCode;
					$offerMainPriceFormatted    = (string) $amazonObject->Offers->Offers[0]->FormattedPrice;
				}

			} else {
				// empty main price
				$emptyMainPriceText         = APD_EMPTY_PRICE_TEXT;
				$offerMainPriceCurrencyCode = '';
				if ( ! empty( $emptyMainPriceText ) ) {
					$offerMainPriceFormatted = $emptyMainPriceText;
					$offerMainPriceAmount    = $emptyMainPriceText;
				} else {
					$offerMainPriceFormatted = '--';
					$offerMainPriceAmount    = '--';
				}
			}

			$listPriceFormatted = $amazonObject->ListPriceFormatted;


			/* =Amazon logo URLs */
			if ( empty( $apdCore->amazonCountryCode ) ) {

				$amazonObject->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_US_small.gif', __FILE__ );
				$amazonObject->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_US.gif', __FILE__ );

			} else if ( $apdCore->amazonCountryCode == 'DE' ) {

				$amazonObject->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small.png', __FILE__ );
				$amazonObject->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE.png', __FILE__ );

				if ( $amazonObject->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) {

					$amazonObject->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small_prime.png', __FILE__ );
					$amazonObject->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE_prime.png', __FILE__ );

				}

			}

			/* =catch faulty Amazon rating */
			//this is necessary because Amazon doesn't always return ratings correctly (returns 0 stars)!
			//if Amazon returns 0 stars and cache stars are not 0, there must be something wrong.
			$newRating   = $customerReviews->averageRating;
			$cacheRating = $amazonCacheItem->AverageRating;

			if ( $cacheRating !== null ) {
				if ( $newRating == 0 AND $cacheRating != 0 ) {
					$customerReviews->averageRating = $cacheRating;
				}
			}

			/* =catch empty $AmazonAvailability */
			if ( isset( $amazonObject->Offers->Offers[0]->Availability ) ) {
				$AmazonAvailability = $amazonObject->Offers->Offers[0]->Availability;
			} else {
				$AmazonAvailability = APD_EMPTY_AVAILABILITY_TEXT;
			};

			/* =make Amazon Stars prettier */
			$ratingStarsHtml = '<span class="amazon-stars">';
			$numberStars     = ( $customerReviews->averageRating ) ? $customerReviews->averageRating : 0;
			$fullStar        = '<i class="fa fa-star"></i>';
			$halfStar        = '<i class="fa fa-star-half-o"></i>';
			$emptyStar       = '<i class="fa fa-star-o"></i>';

			$nFullStars  = floor( $numberStars );
			$nHalfStars  = $numberStars - $nFullStars;
			$nEmptyStars = floor( 5 - $numberStars );

			for ( $i = 0; $i < $nFullStars; $i ++ ) {
				$ratingStarsHtml .= $fullStar;
			}
			if ( $nHalfStars != 0 ) {
				$ratingStarsHtml .= $halfStar;
			}
			for ( $i = 0; $i < $nEmptyStars; $i ++ ) {
				$ratingStarsHtml .= $emptyStar;
			}
			$ratingStarsHtml .= "</span>";

			/* =catch empty AmazonPrice values */
			if ( empty( $amazonPriceFormatted ) ) {
				$amazonPriceFormatted = APD_EMPTY_PRICE_TEXT;
			}

			/* =try to create price performance ratio rating */
			$customItem       = new ApdCustomItem( $amazonObject->ASIN );
			$customItemArrayA = $customItem->getArrayA();
			if ( $customItemArrayA['OverallRatingPercent'] !== null ) {
				$rating  = $customItemArrayA['OverallRatingPercent'];
				$price   = (float) str_replace( ',', '.', str_replace( '.', '', $amazonPrice ) );
				$factor1 = APD_AUTOMOWERS_PPR_FACTOR_1;
				$factor2 = APD_AUTOMOWERS_PPR_FACTOR_2;
				if ( $price == 0 OR $price == null ) {
					$pricePerformanceRating = '.k A.';
				} else {
					$pricePerformanceRating      = ($factor1 * ($rating / $price) + $factor2) * 100;
					$pricePerformanceRating      = round( $pricePerformanceRating, 1 );
					$pricePerformanceRatingGrade = convert_percent_to_grade( $pricePerformanceRating/100 );
					$pricePerformanceRatingText  = convert_percent_to_grade( $pricePerformanceRating/100, true );
				}

				$longname = $customItemArrayA['Longname'];

				//log
				ApdCore::logContent( 'Price-performance-rating calculation of Amazon item: ' . $longname, 1 );
				ApdCore::logContent( '$rating: ' . $rating );
				ApdCore::logContent( '$price: ' . $price );
				ApdCore::logContent( '$factor1: ' . $factor1 );
				ApdCore::logContent( '$factor2: ' . $factor2 );
				ApdCore::logContent( '$pricePerformanceRating: ' . $pricePerformanceRating );
			} else {
				$pricePerformanceRating = 'k. A.';
				$pricePerformanceRatingGrade = 'k. A.';
				$pricePerformanceRatingText = 'k. A.';
				$longname               = $customItemArrayA['Longname'];

				ApdCore::logContent( 'Price-performance-rating calculation of Amazon item: ' . $longname, 1 );
				ApdCore::logContent( 'This item doesn\'t have a price-performance-rating' );
			}

			/* Other */
			$totalOffers = $amazonObject->Offers->TotalNew + $amazonObject->Offers->TotalUsed +
			               $amazonObject->Offers->TotalCollectible + $amazonObject->Offers->TotalRefurbished;

			$platform = $amazonObject->Platform;
			if ( is_array( $platform ) ) {
				$platform = implode( ', ', $platform );
			}

			$percentageSaved = $amazonObject->PercentageSaved;

			$no_img_url = apd_plugins_url( 'img/no-image.png', __FILE__ );

			$amazonItemArray = array(
				$amazonObject->ASIN,
				( $amazonObject->SmallImage != null ) ? $amazonObject->SmallImage->Url->getUri() : $no_img_url,
				( $amazonObject->SmallImage != null ) ? $amazonObject->SmallImage->Width : 60,
				( $amazonObject->SmallImage != null ) ? $amazonObject->SmallImage->Height : 60,
				( $amazonObject->MediumImage != null ) ? $amazonObject->MediumImage->Url->getUri() : $no_img_url,
				( $amazonObject->MediumImage != null ) ? $amazonObject->MediumImage->Width : 60,
				( $amazonObject->MediumImage != null ) ? $amazonObject->MediumImage->Height : 60,
				( $amazonObject->LargeImage != null ) ? $amazonObject->LargeImage->Url->getUri() : $no_img_url,
				( $amazonObject->LargeImage != null ) ? $amazonObject->LargeImage->Width : 60,
				( $amazonObject->LargeImage != null ) ? $amazonObject->LargeImage->Height : 60,
				$amazonObject->Label,
				$amazonObject->Manufacturer,
				$amazonObject->Publisher,
				$amazonObject->Studio,
				$amazonObject->Title,
				$apdCore->getItemUrl( $amazonObject ),
				empty( $totalOffers ) ? '0' : $totalOffers,
				empty( $lowestOfferPrice ) ? '---' : $lowestOfferPrice,
				isset( $lowestOfferCurrency ) ? $lowestOfferCurrency : '',
				isset( $lowestOfferFormattedPrice ) ? str_replace( '$', '\$', $lowestOfferFormattedPrice ) : '',
				empty( $lowestNewPrice ) ? '---' : $lowestNewPrice,
				str_replace( '$', '\$', $lowestNewOfferFormattedPrice ),
				empty( $lowestUsedPrice ) ? '---' : $lowestUsedPrice,
				str_replace( '$', '\$', $lowestUsedOfferFormattedPrice ),
				empty( $amazonPrice ) ? '---' : str_replace( '$', '\$', $amazonPrice ),
				empty( $amazonPriceFormatted ) ? '---' : str_replace( '$', '\$', $amazonPriceFormatted ),
				empty( $listPriceFormatted ) ? '---' : str_replace( '$', '\$', $listPriceFormatted ),
				isset( $amazonObject->Offers->Offers[0]->CurrencyCode ) ? $amazonObject->Offers->Offers[0]->CurrencyCode : '',
				$AmazonAvailability,
				$amazonObject->AmazonLogoSmallUrl,
				$amazonObject->AmazonLogoLargeUrl,
				$apdCore->handleItemUrl( $amazonObject->DetailPageURL ),
				$platform,
				$amazonObject->ISBN,
				$amazonObject->EAN,
				$amazonObject->NumberOfPages,
				$apdCore->getLocalizedDate( $amazonObject->ReleaseDate ),
				$amazonObject->Binding,
				is_array( $amazonObject->Author ) ? implode( ', ', $amazonObject->Author ) : $amazonObject->Author,
				is_array( $amazonObject->Creator ) ? implode( ', ', $amazonObject->Creator ) : $amazonObject->Creator,
				$amazonObject->Edition,
				$customerReviews->averageRating,
				( $customerReviews->totalReviews != null ) ? $customerReviews->totalReviews : 0,
//				( $customerReviews->imgTag != null ) ? $customerReviews->imgTag : '<img src="' . apd_plugins_url( 'img/stars-0.gif', __FILE__ ) . '" class="asa_rating_stars" />',
				$ratingStarsHtml,
				( $customerReviews->imgSrc != null ) ? $customerReviews->imgSrc : apd_plugins_url( 'img/stars-0.gif', __FILE__ ),
				is_array( $amazonObject->Director ) ? implode( ', ', $amazonObject->Director ) : $amazonObject->Director,
				is_array( $amazonObject->Actor ) ? implode( ', ', $amazonObject->Actor ) : $amazonObject->Actor,
				$amazonObject->RunningTime,
				is_array( $amazonObject->Format ) ? implode( ', ', $amazonObject->Format ) : $amazonObject->Format,
				! empty( $parse_params['custom_rating'] ) ? '<img src="' . apd_plugins_url( 'img/stars-' . $parse_params['custom_rating'] . '.gif', __FILE__ ) . '" class="asa_rating_stars" />' : '',
				isset( $amazonObject->EditorialReviews[0] ) ? $amazonObject->EditorialReviews[0]->Content : '',
				! empty( $amazonObject->EditorialReviews[1] ) ? $amazonObject->EditorialReviews[1]->Content : '',
				is_array( $amazonObject->Artist ) ? implode( ', ', $amazonObject->Artist ) : $amazonObject->Artist,
				! empty( $parse_params['comment'] ) ? $parse_params['comment'] : '',
				! empty( $percentageSaved ) ? $percentageSaved : 0,
				! empty( $amazonObject->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) ? 'AmazonPrime' : '',
				! empty( $amazonObject->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) ? '<img src="' . apd_plugins_url( 'img/amazon_prime.png', __FILE__ ) . '" class="asa_prime_pic" />' : '',
				$apdCore->getAmazonShopUrl() . 'product-reviews/' . $amazonObject->ASIN . '/&tag=' . $apdCore->getTrackingId(),
				$customerReviews->iframeUrl,
				$apdCore->getTrackingId(),
				$apdCore->getAmazonShopUrl(),
				isset( $amazonObject->Offers->SalePriceAmount ) ? $apdCore->formatPrice( $amazonObject->Offers->SalePriceAmount ) : '',
				isset( $amazonObject->Offers->SalePriceCurrencyCode ) ? $amazonObject->Offers->SalePriceCurrencyCode : '',
				isset( $amazonObject->Offers->SalePriceFormatted ) ? $amazonObject->Offers->SalePriceFormatted : '',
				! empty( $parse_params['class'] ) ? $parse_params['class'] : '',
				$offerMainPriceAmount,
				$offerMainPriceCurrencyCode,
				$offerMainPriceFormatted,
				current_time( 'mysql' ),
				$manualUpdate,
				$errorMessage,
				$pricePerformanceRating,
				$pricePerformanceRatingGrade,
				$pricePerformanceRatingText
			);

			return $amazonItemArray;

		} else {
			$error = "Item does not exist";
			print_error( $error, __METHOD__, __LINE__ );

			return $error;
		}
	}

	/**
	 * Match the returned items values from Amazon with the defined AmazonItemFields.
	 * @return array
	 */
	public function matchValuesWithFields() {
		$fieldsArray = self::getAmazonItemFields();
		$valuesArray = $this->array;

		return array_combine( $fieldsArray, $valuesArray );
	}

	/**
	 * Retrieve the customer reviews object
	 *
	 * @param bool $uncached
	 *
	 * @return AsaCustomerReviews|null
	 */
	public function getCustomerReviews( $uncached = false ) {

		$iframeUrl = ( $this->object->CustomerReviewsIFrameURL != null ) ? $this->object->CustomerReviewsIFrameURL : '';

		if ( $uncached ) {
			$cache = null;
		} else {
			$cache = $this->cache;
		}

		$reviews = new ApdCustomerReviews( $this->object->ASIN, $iframeUrl, $cache );
		if ( get_option( '_asa_get_rating_alternative' ) ) {
			$reviews->setFindMethod( ApdCustomerReviews::FIND_METHOD_DOM );
		}
		$reviews->load();

		return $reviews;
	}

}