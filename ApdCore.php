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
	public $amazonTrackingId;

	/**
	 * selected country code
	 */
	public $amazonCountryCode = 'DE';

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
	public $amazonUrl = array(
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
	 * Delimiter for iterating through every item in a template
	 *
	 * @var string
	 */
	protected $loopDelimiterStart = "{loop-start}";

	/**
	 * Delimiter for iterating through every item in a template
	 *
	 * @var string
	 */
	protected $loopDelimiterEnd = "{loop-end}";

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
	public function getElement( $asin, $tpl = false ) {
		$item_html = '';

		if ( $tpl == false ) {
			$tpl = 'sidebar_item';
		}

		$tpl_src = $this->getTpl( $tpl );

		if ( is_string( $asin ) ) {
			$item_html .= $this->parseTpl( $asin, $tpl_src );
		} elseif ( is_array( $asin ) ) {
			$item_html .= $this->parseMultiTpl( $asin, $tpl_src );
		}

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
	 * Replace placeholders in template with information for item
	 *
	 * @param $asin
	 * @param $tpl
	 *
	 * @return string
	 */
	public function parseTpl( $asin, $tpl ) {

		$asin = trim( $asin );

		$html = '';
		//--------------------------------------------------------------
		//=replace with Amazon information
		//--------------------------------------------------------------

		$amazonCacheItem = new ApdAmazonCacheItem( $asin );
		$amazonArray     = $amazonCacheItem->getArray();

		//if Amazon cache doesn't return anything, get the data directly from Amazon API
		if ( $amazonArray === null ) {
			$amazonItem  = new ApdAmazonItem( $this->amazonWbs, $asin );
			$amazonArray = $amazonItem->getArray();
		}


		if ( is_array( $amazonArray ) ) {
			$placeholders = $this->getTplPlaceholders( ApdAmazonItem::getAmazonItemFields(), true );
			$html         = preg_replace( $placeholders, $amazonArray, $tpl );
		} else {
			$error = "Amazon array is empty";
			print_error( $error, __METHOD__, __LINE__ );
		}

		//--------------------------------------------------------------
		// =replace with database information
		//--------------------------------------------------------------

		$tpl = $html;

		$apdItem  = new ApdItem( $asin );
		$tablename = $apdItem->getItemTable();
		$database = new ApdDatabase( $tablename );

		$dbPlaceholders = $database->getTableColumns( false );
		$tableInfo      = $database->getTableInfo();

		if ( $dbPlaceholders === false OR $tableInfo === false ) {
			return false;
		}

		if ( ! empty( array_duplicates( $dbPlaceholders ) ) ) {
			$dbPlaceholders = array_remove_duplicates( $dbPlaceholders );
		}

		$dbItem = $apdItem->getItem();

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
						$dbItem->$key = '<i class="check"></i>';
					} else if ( field_is_false( $item ) ) {
						$dbItem->$key = '<i class="times"></i>';
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
	 * Parse loops in template and replace placeholders with asins
	 *
	 * @param array $asins
	 * @param $tpl
	 *
	 * @return string
	 */
	public function parseMultiTpl( array $asins, $tpl ) {

		$html = '';
		$codeBlocks = $this->divideTemplateIntoBlocks( $tpl );

		foreach ( $codeBlocks as $key => $codeBlock ) {
			$blockType = key( $codeBlock );
			$blockHtml = current( $codeBlock );

			if ( $blockType == 'loop' ) {
				$loopHtml = '';
				foreach ( $asins as $asin ) {
					$loopHtml .= $this->parseTpl( $asin, $blockHtml );
				}
				$blockHtml = $loopHtml;
			}

			$html .= $blockHtml;
		}

		return $html;
	}

	/**
	 * @param $tpl
	 *
	 * @return array
	 */
	public function divideTemplateIntoBlocks( $tpl ) {

		$htmlArray = array();

		while ( $tpl != '' ) {
			//get html until first loop
			$html = strstr( $tpl, $this->loopDelimiterStart, true );

			if ( $html !== false ) {
				//store html until first loop
				$htmlArray[]['block'] = $html;
			} else {
				//if there is no next loop, store rest of html
				$htmlArray[]['block'] = $tpl;
			}
			//delete stored html from template
			$tpl = str_replace_first( $this->loopDelimiterStart, '', strstr( $tpl, $this->loopDelimiterStart ) );

			//get html until end of loop
			$html = strstr( $tpl, $this->loopDelimiterEnd, true );
			if ( $html ) {
				//store html until end of loop
				$htmlArray[]['loop'] = $html;
			}
			//delete stored html from template
			$tpl = str_replace_first( $this->loopDelimiterEnd, '', strstr( $tpl, $this->loopDelimiterEnd ) );
		}

		return $htmlArray;
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

	/**
	 * formats the price value from amazon webservice
	 *
	 * @param         string        price
	 *
	 * @return         mixed        price (float, int for JP)
	 */
	public function formatPrice( $price ) {
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
	public function handleItemUrl( $url ) {
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

	public function getApdItem($asin){
//		$amazonItem = (new ApdAmazonCacheItem($asin))->getObject;
//		$apdItem = (new ApdItem($asin))->getItem();
	}

}

