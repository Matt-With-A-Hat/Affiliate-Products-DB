<?php

class ApdAmazonItem {

	/**
	 * available template placeholders
	 */
	public $tplPlaceholder = array(
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
	 * template placeholder prefix
	 */
	protected $tplPrefix = '{$';

	/**
	 * template placeholder postfix
	 */
	protected $tplPostfix = '}';

	/**
	 * ApdAmazonItem constructor.
	 *
	 * @param $amazonWbs
	 */
	public function __construct( $amazonWbs ) {

		$this->amazonWbs = $amazonWbs;

	}

	/**
	 * get item information from amazon webservice
	 *
	 * @param       string      ASIN
	 *
	 * @return      object      AsaZend_Service_Amazon_Item object
	 */
	public function getAmazonItem( $asin ) {
		$result = $this->amazonWbs->itemLookup( $asin, array(
			'ResponseGroup' => 'ItemAttributes,Images,Offers,OfferListings,Reviews,EditorialReview,Tracks'
		) );

		return $result;
	}

	/**
	 * generates right placeholder format and returns them as array
	 * optionally prepared for use as regex
	 *
	 * @param bool true for regex prepared
	 *
	 * @return array
	 */
	public function getTplPlaceholders( $placeholders, $regex = false ) {
		$result = array();
		foreach ( $placeholders as $ph ) {
			$result[] = $this->tplPrefix . $ph . $this->tplPostfix;
		}
		if ( $regex == true ) {
			return array_map( array( $this, 'TplPlaceholderToRegex' ), $result );
		}

		return $result;
	}

	/**
	 * excapes placeholder for regex usage
	 *
	 * @param string placehoder
	 *
	 * @return string escaped placeholder
	 */
	public function TplPlaceholderToRegex( $ph ) {
		$search = array(
			'{',
			'}',
			'$',
			'-',
			'>'
		);

		$replace = array(
			'\{',
			'\}',
			'\$',
			'\-',
			'\>'
		);

		$ph = str_replace( $search, $replace, $ph );

		return '/' . $ph . '/';
	}

}