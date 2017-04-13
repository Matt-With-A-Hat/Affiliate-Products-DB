<?php

class ApdCore {

	/**
	 * this plugins home directory
	 */
	protected $plugin_dir = '/wp-content/plugins/affiliate-product-db';

	protected $plugin_url = 'options-general.php?page=/affiliate-product-db.php';

	/**
	 * template placeholder prefix
	 */
	protected $tpl_prefix = '{$';

	/**
	 * template placeholder postfix
	 */
	protected $tpl_postfix = '}';

	/**
	 * table in database with product details
	 */
	//@todo get this dynamically from db
	public $tablename;

	/**
	 * available template placeholders
	 */
	protected $tpl_placeholder = array(
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

	protected $amazon_tpl_placeholder;

	/**
	 * user's Amazon Access Key ID
	 */
	protected $amazon_api_key;

	/**
	 * user's Amazon Access Key ID
	 * @var string
	 */
	protected $amazon_api_secret_key = '';

	/**
	 * user's Amazon Tracking ID
	 */
	protected $amazon_tracking_id;

	/**
	 * selected country code
	 */
	protected $amazon_country_code = 'DE';

	/**
	 * supported amazon country IDs
	 */
	protected $_amazon_valid_country_codes = array(
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
	protected $amazon_url = array(
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
	protected $amazon_shop_url;

	/**
	 * @var
	 */
	protected $amazon_api_connection_type = 'http';

	/**
	 * the amazon webservice object
	 */
	protected $amazon;

	/**
	 * the cache object
	 */
	protected $cache;

	/**
	 * APD Options
	 */
	protected $asa_use_short_amazon_links = true;

	/**
	 * constructor
	 */
	public function __construct() {

		global $wbpd;

		require_once APD_LIB_DIR . 'AsaZend/Uri/Http.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/Accessories.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/EditorialReview.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/Image.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/Item.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/ListmaniaList.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/Offer.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/OfferSet.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/Query.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/ResultSet.php';
		require_once APD_LIB_DIR . 'AsaZend/Service/Amazon/SimilarProduct.php';

		//use the defined connection
		//@todo make connection data be filled out via a form
		$this->amazon_api_key             = AMAZON_API_KEY;
		$this->amazon_api_secret_key      = AMAZON_API_SECRET_KEY;
		$this->amazon_tracking_id         = AMAZON_TRACKING_ID;
		$this->amazon_country_code        = AMAZON_COUNTRY_CODE;
		$this->amazon_api_connection_type = AMAZON_API_CONNECTION_TYPE;

		// register shortcode handlers
		add_shortcode( 'apd-tpl', 'apd_shortcode_handler' );
		add_shortcode( 'apd-data', 'apd_shortcode_handler' );

		/**
		 * Hook for adding admin menus
		 */
		function setupBackendMenu() {
			add_options_page( 'Affiliate Product DB Setup Menu', 'Affiliate Products', 'manage_options', 'affiliate-product-db', 'setupMenuPage' );
		}

		add_action( 'admin_menu', 'setupBackendMenu' );

		/**
		 * include frontend stylesheets
		 */
		function add_apd_stylesheets() {
			wp_enqueue_style( 'apdplugin', plugins_url( '/css/apdplugin.css', __FILE__ ) );
			wp_enqueue_style( 'font-awesome', plugins_url( '/css/font-awesome.min.css', __FILE__ ) );
		}

		add_action( 'wp_enqueue_scripts', 'add_apd_stylesheets' );

		/**
		 * include backend stylesheets
		 */
		function add_apd_admin_stylesheets() {
			wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
			wp_enqueue_style( 'setupmenu', plugins_url( '/css/setupmenu.css', __FILE__ ) );
		}

		add_action( 'admin_enqueue_scripts', 'add_apd_admin_stylesheets' );

		/**
		 * include stylesheets for plugin
		 */
		function add_apd_scripts() {
//			wp_enqueue_script( 'apd-jquery', plugins_url( '/js/jquery-3.1.1.min.js', __FILE__ ), array(), '3.1.1', true );
//			wp_enqueue_script( 'bootstrap', plugins_url( '/js/bootstrap.min.js', __FILE__ ) );
			wp_enqueue_script( 'apd-functions', plugins_url( '/js/functions.js', __FILE__ ) );
		}

		add_action( 'admin_enqueue_scripts', 'add_apd_scripts' );

		/**
		 * Create the setup menupage
		 */
		function setupMenuPage() {

			handleUploadForm();

			include( 'apd-setupmenu-tpl.php' );

		}

		/**
		 * Handles the form content
		 */
		function handleUploadForm() {

			// First check if the file appears in the _FILES array
			if ( isset( $_FILES['csv-file'] ) AND $_POST['table-name'] ) {

				$csv       = $_FILES['csv-file'];
				$tablename = $_POST['table-name'];

				//check if file is csv an abort if not
				$csv_name = explode( ".", $csv['name'] );
				$csv_name = $csv_name[1];

				if ( $csv_name != 'csv' ) {

					echo "Error uploading file: File is not a CSV file";

					return false;
				}

				$apdDB = new ApdDatabase( $tablename );

				$result = $apdDB->addCsvToDatabase( $tablename, $csv );

				if ( $result ) {
					update_option( 'PRODUCTS_TABLE', $tablename );
				}

				return $result;

			} else {

				echo "Please fill out missing fields";

				return false;

			}

		}

		$this->setTablename(get_option('PRODUCTS_TABLE'));
		$this->apdOptions();
		$this->amazon = $this->connect();

	}

	/**
	 * @return mixed
	 */
	public function getTablename() {
		return $this->tablename;
	}

	/**
	 * @param mixed $tablename
	 */
	public function setTablename( $tablename ) {

		global $wpdb;

		$this->tablename = add_table_prefix( $tablename );;
	}

	protected function apdOptions() {

		update_option( 'apd_use_short_amazon_links', $this->asa_use_short_amazon_links );

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
				$this->amazon_api_key,
				$this->amazon_api_secret_key,
				$this->amazon_tracking_id,
				$this->amazon_country_code,
				$this->amazon_api_connection_type
			);

			return $amazon;

		} catch ( Exception $e ) {

			//@todo add debugging

			return null;
		}
	}

	/**
	 * get item information from amazon webservice
	 *
	 * @param       string      ASIN
	 *
	 * @return      object      AsaZend_Service_Amazon_Item object
	 */
	public function getAmazonItem( $asin ) {
		$result = $this->amazon->itemLookup( $asin, array(
			'ResponseGroup' => 'ItemAttributes,Images,Offers,OfferListings,Reviews,EditorialReview,Tracks'
		) );

		return $result;
	}

	/**
	 * get item information from database
	 *
	 * @param       string      ASIN
	 *
	 * @return      object      AsaZend_Service_Amazon_Item object
	 */
	public function getDbItem( $asin ) {

		$apdDB = new ApdDatabase();

		$item = $apdDB->getItem( $asin );

		return $item;
	}

	/**
	 * return
	 *
	 * @param $asin
	 * @param bool $tpl
	 *
	 * @return string
	 */
	public function getItem( $asin, $tpl = false ) {
		$item_html = '';

		if ( $tpl == false ) {
			$tpl = 'sidebar_item';
		}

		$tpl_src = $this->getTpl( $tpl );

		$item_html .= $this->parseTpl( trim( $asin ), $tpl_src );

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
	public function parseTpl( $asin, $tpl ) {

		$html = '';

		//--------------------------------------------------------------
		//=replace with Amazon information
		//--------------------------------------------------------------

		$amazonPlaceholders = $this->tpl_placeholder;

		if ( ! empty( array_duplicates( $amazonPlaceholders ) ) ) {
			$amazonPlaceholders = array_remove_duplicates( $amazonPlaceholders );
		}

		$amazonItem = $this->getAmazonItem( $asin );

		if ( $amazonItem instanceof AsaZend_Service_Amazon_Item ) {

			$search = $this->getTplPlaceholders( $amazonPlaceholders, true );

			$trackingId = '';

			if ( ! empty( $this->amazon_tracking_id ) ) {
				// set the user's tracking id
				$trackingId = $this->amazon_tracking_id;
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
			if ( empty( $this->amazon_country_code ) ) {

				$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_US_small.gif', __FILE__ );
				$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_US.gif', __FILE__ );

			} else if ( $this->amazon_country_code == 'DE' ) {

				$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small.png', __FILE__ );
				$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE.png', __FILE__ );

				if ( $amazonItem->Offers->Offers[0]->IsEligibleForSuperSaverShipping ) {

					$amazonItem->AmazonLogoSmallUrl = apd_plugins_url( 'img/amazon_DE_small_prime.png', __FILE__ );
					$amazonItem->AmazonLogoLargeUrl = apd_plugins_url( 'img/amazon_DE_prime.png', __FILE__ );

				}

			}


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
				( $customerReviews->imgTag != null ) ? $customerReviews->imgTag : '<img src="' . apd_plugins_url( 'img/stars-0.gif', __FILE__ ) . '" class="asa_rating_stars" />',
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

			$html = preg_replace( $search, $replace, $tpl );

		}


		//--------------------------------------------------------------
		// =replace with database information
		//--------------------------------------------------------------

		$tpl = $html;

		$apdDb = new ApdDatabase();

		$dbPlaceholders = $apdDb->getTableColumns( $this->tablename, false );
		$tableInfo      = $apdDb->getTableInfo( $this->tablename );

		if ( ! empty( array_duplicates( $dbPlaceholders ) ) ) {
			echo "IN";
			$dbPlaceholders = array_remove_duplicates( $dbPlaceholders );
		}

		$dbItem = $this->getDbItem( $asin );

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

		if ( ! empty( $dbItem ) ) {

			$search = $this->getTplPlaceholders( $dbPlaceholders, true );

			$replace = (array) $dbItem;

			$html = preg_replace( $search, $replace, $tpl );

		}

		return $html;

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

		if ( $this->amazon_country_code != 'JP' ) {
			$price = (float) substr_replace( $price, '.', ( strlen( $price ) - 2 ), - 2 );
		} else {
			$price = intval( $price );
		}

		$dec_point     = '.';
		$thousands_sep = ',';

		if ( $this->amazon_country_code == 'DE' ||
		     $this->amazon_country_code == 'FR'
		) {
			// taken the amazon websites as example
			$dec_point     = ',';
			$thousands_sep = '.';
		}

		if ( $this->amazon_country_code != 'JP' ) {
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
			$url = sprintf( $this->amazon_url[ $this->amazon_country_code ],
				$item->ASIN, $this->amazon_tracking_id );
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
		return $this->amazon_country_code;
	}

	/**
	 * @return mixed
	 */
	public function getAmazonShopUrl() {
		if ( $this->amazon_shop_url == null ) {
			$url                   = $this->amazon_url[ $this->getCountryCode() ];
			$this->amazon_shop_url = current( explode( 'exec', $url ) );
		}

		return $this->amazon_shop_url;
	}

	/**
	 * @return mixed
	 */
	public function getTrackingId() {
		return $this->amazon_tracking_id;
	}

	/**
	 * generates right placeholder format and returns them as array
	 * optionally prepared for use as regex
	 *
	 * @param bool true for regex prepared
	 *
	 * @return array
	 */
	protected function getTplPlaceholders( $placeholders, $regex = false ) {
		$result = array();
		foreach ( $placeholders as $ph ) {
			$result[] = $this->tpl_prefix . $ph . $this->tpl_postfix;
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
			$reviews->setFindMethod( AsaCustomerReviews::FIND_METHOD_DOM );
		}
		$reviews->load();

		return $reviews;
	}

}

$apd = new ApdCore();
