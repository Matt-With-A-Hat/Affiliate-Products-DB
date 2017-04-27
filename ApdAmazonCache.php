<?php

class ApdAmazonCache {

	protected $amazonFields = array(
		'ASIN',
		'DetailPageURL',
		'SalesRank ',
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

	protected $cacheInterval = 10;

	protected $amazonTableName = "amazon_items";

	/**
	 * @return array
	 */
	public function getAmazonFields() {
		return $this->amazonFields;
	}

}


