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
			$item_html .= $this->parseTpl( $tpl_src, $asin );
		} elseif ( is_array( $asin ) ) {
			$item_html .= $this->parseMultiTpl( $tpl_src, $asin );
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
	public function parseTpl( $tpl, $asin = null ) {

		$html = '';

		if ( ! empty( $asin ) ) {

			$asin = trim( $asin );

			//--------------------------------------------------------------
			//=replace with Amazon information
			//--------------------------------------------------------------

			$amazonCacheItem = new ApdAmazonCacheItem( $asin );
			$amazonItemArray = $amazonCacheItem->getArrayN();

			//if Amazon cache doesn't return anything, get the data directly from Amazon API
			if ( $amazonItemArray === null ) {
				$amazonItem      = new ApdAmazonItem( $this->amazonWbs, $asin );
				$amazonItemArray = $amazonItem->getArray();
			}

			if ( is_array( $amazonItemArray ) ) {
				$placeholders = $this->getTplPlaceholders( ApdAmazonItem::getAmazonItemFields(), true );
				$html         = preg_replace( $placeholders, $amazonItemArray, $tpl );
			} else {
				$error = "Amazon array is empty";
				print_error( $error, __METHOD__, __LINE__ );
			}

			//--------------------------------------------------------------
			// =replace with database information
			//--------------------------------------------------------------

			$tpl = $html;

			$apdCustomItem = new ApdCustomItem( $asin );

			$customItemObject = $apdCustomItem->getArrayR();
			$dbPlaceholders = get_fields($customItemObject);

			if ( ! empty( $customItemObject ) ) {

				$placeholders = $this->getTplPlaceholders( $dbPlaceholders, true );
				$replace      = $customItemObject;
				$html         = preg_replace( $placeholders, $replace, $tpl );

			}
		}

		//--------------------------------------------------------------
		// =replace with custom information
		//--------------------------------------------------------------

		$tpl = $html;

		$placeholders = array(
			'AutomowerModels',
			'ManufacturerDescription',
			'ManufacturerLogo'
		);

		/**
		 * =Automower models
		 */
		$tablename           = add_table_prefix( 'products' );
		$columns             = array( 'Longname', 'Tags' );
		$automowerModels     = ( new ApdDatabase( $tablename ) )->getColumns( $columns );
		$automowerModelsHtml = "";
		foreach ( $automowerModels as $automowerModel ) {
			$automowerModelsHtml .= "<li data-tag='$automowerModel[Tags]'>$automowerModel[Longname]</li>";
		}

		/**
		 * =Manufacturer description
		 */
//		@todo get this from database and implement backend area where manufacturers can be added
		$manufacturerDescription = '';
		$manufacturerLogo = '';
		if ( ! empty( $asin ) ) {
			$customItemObject = ( new ApdCustomItem( $asin ) )->getObjectR();
			if ( ! empty( $manufacturer = $customItemObject->Manufacturer ) ) {
				switch ( $manufacturer ) {
					case "Gardena":
						$manufacturerDescription = "Wie die meisten Hersteller von Gartengeräten hat auch die Firma Gardena seit einiger Zeit mehrere Rasenroboter in ihrem Sortiment. Die Firma Gardena ist seit 2006 ein Tochterunternehmen der schwedischen Husqvarna-Gruppe und profitiert dadurch von deren langjähriger Erfahrung bezüglich autonomer Rasenroboter.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/gardena-logo.png";
						break;
					case "Husqvarna":
						$manufacturerDescription = "Die schwedische Firma Husqvarna (Husqvarna Group) ist ein Hersteller von Motorgeräten für die Forstwirtschaft sowie Garten und Landschaftspflege. Dazu zählen Motorsägen und Schneidgerade wie auch Diamantwerkzeuge für das Baugewerbe und die Steinindustrie. Außerdem gilt Husqvarna als Pionier auf dem Gebiet der autonomen Rasenroboter.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/husqvarna-logo.png";
						break;
					case "Bosch":
						$manufacturerDescription = "Die Robert Bosch GmbH ist ein im Jahr 1886 gegründetes multinationales deutsches Unternehmen. Bekannt als Automobilzulieferer, Hersteller von Elektrowerkzeugen und Haushaltsgeräten, produziert Bosch seit einigen Jahren erfolgreich die Indego Mähroboter Reihe.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/bosch-logo.png";
						break;
					case "Wolf":
						$manufacturerDescription = "Das Unternehmen Wolf wurde ursprünglich 1922 gegründet und wurde 2009 durch MTD Products übernommen. Der Mutterkonzern MTD Inc. gilt nach eigenen Angaben als weltweit größter Hersteller von Motorgartengeräten. Dieses Know-How kommt ihrer Robo-Scooter Reihe zugute, welche durch ihre durchweg guten Bewertungen glänzen.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/wolf-logo.png";
						break;
					case "Robomow":
						$manufacturerDescription = "Das israelische Unternehmen Friendly Robotics, welches 1995 in Israel gegründet wurde, hat sich auf die Herstellungen der Rasenmähroboter spezialisiert. Bekannt sind sie durch ihre Robomow Mähroboter Reihe geworden.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/robomow-logo.png";
						break;
					case "Worx":
						$manufacturerDescription = "WORX ist eine Rasen- und Gartengerätereihe, die von der chinesischen Firma Positec Tool Corporation produziert und vertrieben wird. Gegründet wurde die Firma in Suzhou, China, jedoch liegt der Hauptsitz in Charlotte, North Carolina, USA. WORX wird unter anderem für große Einzelhandelskette wie Canadian Tire, Costco und Walmart vertrieben.";
						$manufacturerLogo = get_template_directory_uri()."/img/logos/worx-logo.png";
						break;
				}
			}
		}

		$placeholders = $this->getTplPlaceholders( $placeholders, true );
		$replace      = array(
			$automowerModelsHtml,
			$manufacturerDescription,
			$manufacturerLogo
		);

//		krumo( $placeholders );
//		krumo( $replace );

		$html = preg_replace( $placeholders, $replace, $tpl );

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
	public function parseMultiTpl( $tpl, array $asins ) {

		$html       = '';
		$codeBlocks = $this->divideTemplateIntoBlocks( $tpl );

		foreach ( $codeBlocks as $key => $codeBlock ) {

//			krumo(key($codeBlock));
//			krumo($key);
//			krumo(current($codeBlock));
//			krumo($codeBlock[$key]);

			$blockType = key( $codeBlock ); //$key of $codeBlock array (as opposed to $key of foreach)
			$blockHtml = current( $codeBlock ); //$codeBlock[$key] of $codeBlock array (as opposed to $value of foreach, which is the array)

			if ( $blockType == 'loop' ) {
				$loopHtml = '';
				foreach ( $asins as $asin ) {
					$loopHtml .= $this->parseTpl( $blockHtml, $asin );
				}
				$blockHtml = $loopHtml;
			} else {
//				$blockHtml = $this->parseTpl( $blockHtml );     //produces DOM bug
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

	public function getApdItem( $asin ) {
//		$amazonItem = (new ApdAmazonCacheItem($asin))->getObject;
//		$apdItem = (new ApdCustomItem($asin))->getItem();
	}

}

