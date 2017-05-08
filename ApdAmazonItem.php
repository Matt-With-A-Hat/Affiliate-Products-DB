<?php

class ApdAmazonItem {

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
		'OffersMainPriceFormattedPrice'
	);

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
	 * Make Amazon item a simple array that matches placeholder array
	 */
	public function refineAmazonItem() {

		$apdCore      = new ApdCore();
		$amazonObject = $this->object;

		if ( $amazonObject instanceof AsaZend_Service_Amazon_Item ) {

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
				$emptyMainPriceText         = get_option( '_asa_replace_empty_main_price' );
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


			//Amazon logo URLs
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

			//make Amazon Stars prettier with font awesome
			$ratingStarsHtml = '<span class="amazon-stars">';
			$numberStars     = $customerReviews->averageRating;
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


			$totalOffers = $amazonObject->Offers->TotalNew + $amazonObject->Offers->TotalUsed +
			               $amazonObject->Offers->TotalCollectible + $amazonObject->Offers->TotalRefurbished;

			$platform = $amazonObject->Platform;
			if ( is_array( $platform ) ) {
				$platform = implode( ', ', $platform );
			}

			$percentageSaved = $amazonObject->PercentageSaved;

			$no_img_url = apd_plugins_url( 'img/no_image.png', __FILE__ );

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
				isset( $amazonObject->Offers->Offers[0]->Availability ) ? $amazonObject->Offers->Offers[0]->Availability : '',
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
			);

			return $amazonItemArray;

		} else {
			$error = "Item is not an Amazon item";
			print_error( $error, __METHOD__, __LINE__ );

			return $error;
		}
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