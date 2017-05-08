<?php

class ApdCore {

	/**
	 * user's Amazon Access Key ID
	 */
	protected $amazonApiKey;

	/**
	 * user's Amazon Access Key ID
	 * @var string
	 */
	protected $amazonApiSecretKey = '';

	/**
	 * user's Amazon Tracking ID
	 */
	protected $amazonTrackingId;

	/**
	 * selected country code
	 */
	protected $amazonCountryCode = 'DE';

	/**
	 * supported amazon country IDs
	 */
	protected $amazonValidCountryCodes = array(
		'BR',
		'CA',
		'DE',
		'FR',
		'IN',
		'JP',
		'MX',
		'UK',
		'US',
		'IT',
		'ES',
		'CN'
	);

	/**
	 * the international amazon product page urls
	 */
	protected $amazonUrl = array(
		'BR' => 'http://www.amazon.com.br/exec/obidos/ASIN/%s/%s',
		'CA' => 'http://www.amazon.ca/exec/obidos/ASIN/%s/%s',
		'DE' => 'http://www.amazon.de/exec/obidos/ASIN/%s/%s',
		'FR' => 'http://www.amazon.fr/exec/obidos/ASIN/%s/%s',
		'JP' => 'http://www.amazon.jp/exec/obidos/ASIN/%s/%s',
		'MX' => 'http://www.amazon.com.mx/exec/obidos/ASIN/%s/%s',
		'UK' => 'http://www.amazon.co.uk/exec/obidos/ASIN/%s/%s',
		'US' => 'http://www.amazon.com/exec/obidos/ASIN/%s/%s',
		'IN' => 'http://www.amazon.in/exec/obidos/ASIN/%s/%s',
		'IT' => 'http://www.amazon.it/exec/obidos/ASIN/%s/%s',
		'ES' => 'http://www.amazon.es/exec/obidos/ASIN/%s/%s',
		'CN' => 'http://www.amazon.cn/exec/obidos/ASIN/%s/%s',
	);

	/**
	 * @var string
	 */
	protected $amazonShopUrl;

	/**
	 * @var
	 */
	protected $amazonApiConnectionType = 'http';

	/**
	 * the amazon webservice object
	 */
	public $amazonWbs;

	/**
	 * APD Options
	 */
	protected $asaUseShortAmazonLinks = true;

	/**
	 * template placeholder prefix
	 */
	protected $tplPrefix = '{$';

	/**
	 * template placeholder postfix
	 */
	protected $tplPostfix = '}';

	/**
	 * ApdCore constructor.
	 */
	public function __construct() {

		//use the defined connection
		//@todo make connection data be filled out via a form
		$this->amazonApiKey            = AMAZON_API_KEY;
		$this->amazonApiSecretKey      = AMAZON_API_SECRET_KEY;
		$this->amazonTrackingId        = AMAZON_TRACKING_ID;
		$this->amazonCountryCode       = AMAZON_COUNTRY_CODE;
		$this->amazonApiConnectionType = AMAZON_API_CONNECTION_TYPE;

		$this->apdOptions();
		$this->amazonWbs = $this->connect();

	}

	/**
	 * set APD options
	 */
	protected function apdOptions() {

		update_option( 'apd_use_short_amazon_links', $this->asaUseShortAmazonLinks );

	}

	/**
	 * @return bool
	 */
	public function isDebug() {
		return get_option( '_apd_debug' );
	}

	/**
	 * trys to connect to the amazon webservice
	 */
	protected function connect() {
		require_once APD_LIB_DIR . 'Apd/Service/Amazon.php';

		try {
			$amazon = Apd_Service_Amazon::factory(
				$this->amazonApiKey,
				$this->amazonApiSecretKey,
				$this->amazonTrackingId,
				$this->amazonCountryCode,
				$this->amazonApiConnectionType
			);

			return $amazon;

		} catch ( Exception $e ) {

			//@todo add debugging

			return null;
		}
	}

	/**
	 * Gets an element specified by the supplied asin, template and tablename.
	 *
	 * @param $asin
	 * @param bool $tpl
	 *
	 * @return string
	 */
	public function getElement( $asin, $tpl = false, $tablename ) {
		$item_html = '';

		if ( $tpl == false ) {
			$tpl = 'sidebar_item';
		}

		$tpl_src = $this->getTpl( $tpl );

		$item_html .= $this->parseTpl( trim( $asin ), $tpl_src, $tablename );

//		$item_html = $tpl_src;

		return $item_html;
	}

	/**
	 * Retrieves template file to use
	 *
	 * @param $tpl_file
	 * @param bool $default
	 *
	 * @return bool|string
	 */
	public function getTpl( $tpl_file, $default = false ) {
		if ( ! empty( $tpl_file ) ) {

			foreach ( $this->getTplLocations() as $loc ) {
				if ( ! is_dir( $loc ) ) {
					continue;
				}
				foreach ( $this->getTplExtensions() as $ext ) {
					$tplPath = $loc . $tpl_file . '.' . $ext;
					if ( file_exists( $tplPath ) ) {
						$tpl = file_get_contents( $tplPath );
					}
				}
				if ( isset( $tpl ) ) {
					break;
				}
			}
		}

		if ( ! isset( $tpl ) ) {
			$tpl = $default;
		}

		return $tpl;
	}

	/**
	 * @return mixed|void
	 */
	public function getTplExtensions() {
		$tplExtensions = array( 'htm', 'html' );

		return apply_filters( 'adp_tpl_extensions', $tplExtensions );
	}

	/**
	 * @return mixed|void
	 */
	public function getTplLocations() {
		$tplLocations = array(
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR,
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'built-in' . DIRECTORY_SEPARATOR
		);

		return apply_filters( 'adp_tpl_locations', $tplLocations );
	}

	/**
	 * @param $asin
	 * @param $tpl_src
	 *
	 * @return string
	 */
	public function parseTpl( $asin, $tpl, $tablename ) {

		$html = '';
		//--------------------------------------------------------------
		//=replace with Amazon information
		//--------------------------------------------------------------

		$apdAmazonItem = new ApdAmazonItem( $this->amazonWbs, $asin );

		$amazonPlaceholders = ApdAmazonItem::$amazonItemFields;

		if ( ! empty( array_duplicates( $amazonPlaceholders ) ) ) {
			$amazonPlaceholders = array_remove_duplicates( $amazonPlaceholders );
		}

		$amazonItem = $apdAmazonItem->getAmazonObject();

		if ( $amazonItem instanceof AsaZend_Service_Amazon_Item ) {

			$placeholders = $this->getTplPlaceholders( ApdAmazonItem::$amazonItemFields, true );

			$trackingId = '';

			if ( ! empty( $this->amazonTrackingId ) ) {
				// set the user's tracking id
				$trackingId = $this->amazonTrackingId;
			}

			// get the customer rating object
			$customerReviews = $this->getCustomerReviews( $amazonItem );

			if ( isset( $amazonItem->Offers->LowestUsedPrice ) && isset( $amazonItem->Offers->LowestNewPrice ) ) {

				$lowestOfferPrice          = ( $amazonItem->Offers->LowestUsedPrice < $amazonItem->Offers->LowestNewPrice ) ?
					$amazonItem->Offers->LowestUsedPrice : $amazonItem->Offers->LowestNewPrice;
				$lowestOfferCurrency       = ( $amazonItem->Offers->LowestUsedPrice < $amazonItem->Offers->LowestNewPrice ) ?
					$amazonItem->Offers->LowestUsedPriceCurrency : $amazonItem->Offers->LowestNewPriceCurrency;
				$lowestOfferFormattedPrice = ( $amazonItem->Offers->LowestUsedPrice < $amazonItem->Offers->LowestNewPrice ) ?
					$amazonItem->Offers->LowestUsedPriceFormattedPrice : $amazonItem->Offers->LowestNewPriceFormattedPrice;

			} else if ( isset( $amazonItem->Offers->LowestNewPrice ) ) {

				$lowestOfferPrice          = $amazonItem->Offers->LowestNewPrice;
				$lowestOfferCurrency       = $amazonItem->Offers->LowestNewPriceCurrency;
				$lowestOfferFormattedPrice = $amazonItem->Offers->LowestNewPriceFormattedPrice;

			} else if ( isset( $amazonItem->Offers->LowestUsedPrice ) ) {

				$lowestOfferPrice          = $amazonItem->Offers->LowestUsedPrice;
				$lowestOfferCurrency       = $amazonItem->Offers->LowestUsedPriceCurrency;
				$lowestOfferFormattedPrice = $amazonItem->Offers->LowestUsedPriceFormattedPrice;
			}

			$lowestOfferPrice              = $this->formatPrice( $lowestOfferPrice );
			$lowestNewPrice                = isset( $amazonItem->Offers->LowestNewPrice ) ? $this->formatPrice( $amazonItem->Offers->LowestNewPrice ) : '';
			$lowestNewOfferFormattedPrice  = isset( $amazonItem->Offers->LowestNewPriceFormattedPrice ) ? $amazonItem->Offers->LowestNewPriceFormattedPrice : '';
			$lowestUsedPrice               = isset( $amazonItem->Offers->LowestUsedPrice ) ? $this->formatPrice( $amazonItem->Offers->LowestUsedPrice ) : '';
			$lowestUsedOfferFormattedPrice = isset( $amazonItem->Offers->LowestUsedPriceFormattedPrice ) ? $amazonItem->Offers->LowestUsedPriceFormattedPrice : '';

			$amazonPrice          = $this->getAmazonPrice( $amazonItem );
			$amazonPriceFormatted = $this->getAmazonPrice( $amazonItem, true );

			if ( isset( $amazonItem->Offers->Offers[0]->Price ) && ! empty( $amazonItem->Offers->Offers[0]->Price ) ) {

				if ( isset( $amazonItem->Offers->SalePriceAmount ) ) {
					// set main price to sale price
					$offerMainPriceAmount       = $this->formatPrice( (string) $amazonItem->Offers->SalePriceAmount );
					$offerMainPriceCurrencyCode = $amazonItem->Offers->SalePriceCurrencyCode;
					$offerMainPriceFormatted    = $amazonItem->Offers->SalePriceFormatted;
				} else {
					$offerMainPriceAmount       = $this->formatPrice( (string) $amazonItem->Offers->Offers[0]->Price );
					$offerMainPriceCurrencyCode = (string) $amazonItem->Offers->Offers[0]->CurrencyCode;
					$offerMainPriceFormatted    = (string) $amazonItem->Offers->Offers[0]->FormattedPrice;
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

			$listPriceFormatted = $amazonItem->ListPriceFormatted;

			//Amazon logo URLs
			if ( empty( $this->amazonCountryCode ) ) {

				$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_US_small.gif', __FILE__ );
				$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_US.gif', __FILE__ );

			} else if ( $this->amazonCountryCode == 'DE' ) {

				$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small.png', __FILE__ );
				$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE.png', __FILE__ );

				if ( $amazonItem->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) {

					$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small_prime.png', __FILE__ );
					$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE_prime.png', __FILE__ );

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


			$totalOffers = $amazonItem->Offers->TotalNew + $amazonItem->Offers->TotalUsed +
			               $amazonItem->Offers->TotalCollectible + $amazonItem->Offers->TotalRefurbished;

			$platform = $amazonItem->Platform;
			if ( is_array( $platform ) ) {
				$platform = implode( ', ', $platform );
			}

			$percentageSaved = $amazonItem->PercentageSaved;

			$no_img_url = apd_plugins_url( 'img/no_image.png', __FILE__ );

			$replace = array(
				$amazonItem->ASIN,
				( $amazonItem->SmallImage != null ) ? $amazonItem->SmallImage->Url->getUri() : $no_img_url,
				( $amazonItem->SmallImage != null ) ? $amazonItem->SmallImage->Width : 60,
				( $amazonItem->SmallImage != null ) ? $amazonItem->SmallImage->Height : 60,
				( $amazonItem->MediumImage != null ) ? $amazonItem->MediumImage->Url->getUri() : $no_img_url,
				( $amazonItem->MediumImage != null ) ? $amazonItem->MediumImage->Width : 60,
				( $amazonItem->MediumImage != null ) ? $amazonItem->MediumImage->Height : 60,
				( $amazonItem->LargeImage != null ) ? $amazonItem->LargeImage->Url->getUri() : $no_img_url,
				( $amazonItem->LargeImage != null ) ? $amazonItem->LargeImage->Width : 60,
				( $amazonItem->LargeImage != null ) ? $amazonItem->LargeImage->Height : 60,
				$amazonItem->Label,
				$amazonItem->Manufacturer,
				$amazonItem->Publisher,
				$amazonItem->Studio,
				$amazonItem->Title,
				$this->getItemUrl( $amazonItem ),
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
				isset( $amazonItem->Offers->Offers[0]->CurrencyCode ) ? $amazonItem->Offers->Offers[0]->CurrencyCode : '',
				isset( $amazonItem->Offers->Offers[0]->Availability ) ? $amazonItem->Offers->Offers[0]->Availability : '',
				$amazonItem->AmazonLogoSmallUrl,
				$amazonItem->AmazonLogoLargeUrl,
				$this->handleItemUrl( $amazonItem->DetailPageURL ),
				$platform,
				$amazonItem->ISBN,
				$amazonItem->EAN,
				$amazonItem->NumberOfPages,
				$this->getLocalizedDate( $amazonItem->ReleaseDate ),
				$amazonItem->Binding,
				is_array( $amazonItem->Author ) ? implode( ', ', $amazonItem->Author ) : $amazonItem->Author,
				is_array( $amazonItem->Creator ) ? implode( ', ', $amazonItem->Creator ) : $amazonItem->Creator,
				$amazonItem->Edition,
				$customerReviews->averageRating,
				( $customerReviews->totalReviews != null ) ? $customerReviews->totalReviews : 0,
//				( $customerReviews->imgTag != null ) ? $customerReviews->imgTag : '<img src="' . apd_plugins_url( 'img/stars-0.gif', __FILE__ ) . '" class="asa_rating_stars" />',
				$ratingStarsHtml,
				( $customerReviews->imgSrc != null ) ? $customerReviews->imgSrc : apd_plugins_url( 'img/stars-0.gif', __FILE__ ),
				is_array( $amazonItem->Director ) ? implode( ', ', $amazonItem->Director ) : $amazonItem->Director,
				is_array( $amazonItem->Actor ) ? implode( ', ', $amazonItem->Actor ) : $amazonItem->Actor,
				$amazonItem->RunningTime,
				is_array( $amazonItem->Format ) ? implode( ', ', $amazonItem->Format ) : $amazonItem->Format,
				! empty( $parse_params['custom_rating'] ) ? '<img src="' . apd_plugins_url( 'img/stars-' . $parse_params['custom_rating'] . '.gif', __FILE__ ) . '" class="asa_rating_stars" />' : '',
				isset( $amazonItem->EditorialReviews[0] ) ? $amazonItem->EditorialReviews[0]->Content : '',
				! empty( $amazonItem->EditorialReviews[1] ) ? $amazonItem->EditorialReviews[1]->Content : '',
				is_array( $amazonItem->Artist ) ? implode( ', ', $amazonItem->Artist ) : $amazonItem->Artist,
				! empty( $parse_params['comment'] ) ? $parse_params['comment'] : '',
				! empty( $percentageSaved ) ? $percentageSaved : 0,
				! empty( $amazonItem->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) ? 'AmazonPrime' : '',
				! empty( $amazonItem->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) ? '<img src="' . apd_plugins_url( 'img/amazon_prime.png', __FILE__ ) . '" class="asa_prime_pic" />' : '',
				$this->getAmazonShopUrl() . 'product-reviews/' . $amazonItem->ASIN . '/&tag=' . $this->getTrackingId(),
				$customerReviews->iframeUrl,
				$this->getTrackingId(),
				$this->getAmazonShopUrl(),
				isset( $amazonItem->Offers->SalePriceAmount ) ? $this->formatPrice( $amazonItem->Offers->SalePriceAmount ) : '',
				isset( $amazonItem->Offers->SalePriceCurrencyCode ) ? $amazonItem->Offers->SalePriceCurrencyCode : '',
				isset( $amazonItem->Offers->SalePriceFormatted ) ? $amazonItem->Offers->SalePriceFormatted : '',
				! empty( $parse_params['class'] ) ? $parse_params['class'] : '',
				$offerMainPriceAmount,
				$offerMainPriceCurrencyCode,
				$offerMainPriceFormatted,
			);

			$html = preg_replace( $placeholders, $replace, $tpl );

		}

		//--------------------------------------------------------------
		// =replace with database information
		//--------------------------------------------------------------

		$tpl = $html;

		$database = new ApdDatabase( $tablename );
		$apdItem  = new ApdItem( $tablename );

		$dbPlaceholders = $database->getTableColumns( false );
		$tableInfo      = $database->getTableInfo();

		if ( $dbPlaceholders === false OR $tableInfo === false ) {
			return false;
		}

		if ( ! empty( array_duplicates( $dbPlaceholders ) ) ) {
			$dbPlaceholders = array_remove_duplicates( $dbPlaceholders );
		}

		$dbItem = $apdItem->getItem( $asin );

		if ( ! empty( $dbItem ) ) {
			//reformat advantage list
			$advantagesArray = explode( "*", $dbItem->Advantages );
			$advantagesHtml  = '';

			foreach ( $advantagesArray as $advantage ) {

				$advantagesHtml .= "<li>" . $advantage . "</li>";

			}
			$dbItem->Advantages = $advantagesHtml;


			//reformat disadvantage list
			$disadvantagesArray = explode( "*", $dbItem->Disadvantages );
			$disadvantagesHtml  = '';

			foreach ( $disadvantagesArray as $disadvantage ) {

				$disadvantagesHtml .= "<li>" . $disadvantage . "</li>";

			}
			$dbItem->Disadvantages = $disadvantagesHtml;

			//convert bool values to checkbox
			$i = 0;
			foreach ( $dbItem as $key => $item ) {

				$fieldType = $tableInfo[ $i ++ ]['Type'];

				if ( type_is_boolean( $fieldType ) ) {

					if ( field_is_true( $item ) ) {
						$dbItem->$key = '<i class="check-square"></i>';
					} else if ( field_is_false( $item ) ) {
						$dbItem->$key = '<i class="minus-square"></i>';
					}

				}

			}

			//convert decimal percent values to percent numbers
			foreach ( $dbItem as $key => $item ) {

				if ( preg_match( "/percent/i", $key ) ) {

					$dbItem->$key = $item * 100;

				}

			}

			$placeholders = $this->getTplPlaceholders( $dbPlaceholders, true );

			$replace = (array) $dbItem;

			$html = preg_replace( $placeholders, $replace, $tpl );

		}

		return $html;

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

		krumo( $result );

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

	/**
	 * formats the price value from amazon webservice
	 *
	 * @param         string        price
	 *
	 * @return         mixed        price (float, int for JP)
	 */
	protected function formatPrice( $price ) {
		if ( $price === null || empty( $price ) ) {
			return $price;
		}

		if ( $this->amazonCountryCode != 'JP' ) {
			$price = (float) substr_replace( $price, '.', ( strlen( $price ) - 2 ), - 2 );
		} else {
			$price = intval( $price );
		}

		$dec_point     = '.';
		$thousands_sep = ',';

		if ( $this->amazonCountryCode == 'DE' ||
		     $this->amazonCountryCode == 'FR'
		) {
			// taken the amazon websites as example
			$dec_point     = ',';
			$thousands_sep = '.';
		}

		if ( $this->amazonCountryCode != 'JP' ) {
			$price = number_format( $price, 2, $dec_point, $thousands_sep );
		} else {
			$price = number_format( $price, 0, $dec_point, $thousands_sep );
		}

		return $price;
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function getItemUrl( $item ) {
		if ( get_option( 'apd_use_short_amazon_links' ) ) {
			$url = sprintf( $this->amazonUrl[ $this->amazonCountryCode ],
				$item->ASIN, $this->amazonTrackingId );
		} else {
			$url = $item->DetailPageURL;
		}

		return $this->handleItemUrl( $url );
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	protected function handleItemUrl( $url ) {
		$url = urldecode( $url );

		$url = strtr( $url, array(
			'%' => '%25'
		) );

		return $url;
	}

	/**
	 * @param $date
	 *
	 * @return bool|string
	 */
	public function getLocalizedDate( $date ) {
		if ( ! empty( $date ) ) {
			$dt = new DateTime( $date );

			$format = get_option( 'date_format' );

			$date = date( $format, $dt->format( 'U' ) );
		}

		return $date;
	}

	/**
	 * @return string
	 */
	public function getCountryCode() {
		return $this->amazonCountryCode;
	}

	/**
	 * @return mixed
	 */
	public function getAmazonShopUrl() {
		if ( $this->amazonShopUrl == null ) {
			$url                 = $this->amazonUrl[ $this->getCountryCode() ];
			$this->amazonShopUrl = current( explode( 'exec', $url ) );
		}

		return $this->amazonShopUrl;
	}

	/**
	 * @return mixed
	 */
	public function getTrackingId() {
		return $this->amazonTrackingId;
	}

	/**
	 * @param $item
	 * @param bool $formatted
	 *
	 * @return mixed|null
	 */
	public function getAmazonPrice( $item, $formatted = false ) {
		$result = null;

		if ( isset( $item->Offers->SalePriceAmount ) && $item->Offers->SalePriceAmount != null ) {
			if ( $formatted === false ) {
				$result = $this->formatPrice( $item->Offers->SalePriceAmount );
			} else {
				$result = $item->Offers->SalePriceFormatted;
			}
		} elseif ( isset( $item->Offers->Offers[0]->Price ) && $item->Offers->Offers[0]->Price != null ) {
			if ( $formatted === false ) {
				$result = $this->formatPrice( $item->Offers->Offers[0]->Price );
			} else {
				$result = $item->Offers->Offers[0]->FormattedPrice;
			}
		} elseif ( isset( $item->Offers->LowestNewPrice ) && ! empty( $item->Offers->LowestNewPrice ) ) {
			if ( $formatted === false ) {
				$result = $this->formatPrice( $item->Offers->LowestNewPrice );
			} else {
				$result = $item->Offers->LowestNewPriceFormattedPrice;
			}
		} elseif ( isset( $item->Offers->LowestUsedPrice ) && ! empty( $item->Offers->LowestUsedPrice ) ) {
			if ( $formatted === false ) {
				$result = $this->formatPrice( $item->Offers->LowestUsedPrice );
			} else {
				$result = $item->Offers->LowestUsedPriceFormattedPrice;
			}
		}

		return $result;
	}

	/**
	 * Retrieve the customer reviews object
	 *
	 * @param $item
	 * @param bool $uncached
	 *
	 * @return AsaCustomerReviews|null
	 */
	public function getCustomerReviews( $item, $uncached = false ) {
		require_once( dirname( __FILE__ ) . '/ApdCustomerReviews.php' );

		$iframeUrl = ( $item->CustomerReviewsIFrameURL != null ) ? $item->CustomerReviewsIFrameURL : '';

		if ( $uncached ) {
			$cache = null;
		} else {
			$cache = $this->cache;
		}

		$reviews = new ApdCustomerReviews( $item->ASIN, $iframeUrl, $cache );
		if ( get_option( '_asa_get_rating_alternative' ) ) {
			$reviews->setFindMethod( ApdCustomerReviews::FIND_METHOD_DOM );
		}
		$reviews->load();

		return $reviews;
	}

}

