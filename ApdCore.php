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
	public $datatable = 'hxvct_products';

	/**
	 * available template placeholders
	 */
	protected $tpl_placeholder = array(
		'asin',
		'small_image_url',
		'small_image_width',
		'small_image_height',
		'medium_image_url',
		'medium_image_width',
		'medium_image_height',
		'large_image_url',
		'large_image_width',
		'large_image_height',
		'label',
		'manufacturer',
		'publisher',
		'studio',
		'title',
		'amazon_url',
		'total_offers',
		'lowest_offer_price',
		'lowest_offer_currency',
		'lowest_offer_formatted_price',
		'lowest_new_price',
		'lowest_new_offer_formatted_price',
		'lowest_used_price',
		'lowest_used_offer_formatted_price',
		'amazon_price',
		'amazon_price_formatted',
		'list_price_formatted',
		'amazon_currency',
		'amazon_availability',
		'amazon_logo_small_url',
		'amazon_logo_large_url',
		'detail_page_url',
		'platform',
		'ibsn',
		'ean',
		'number_of_pages',
		'release_date',
		'binding',
		'author',
		'creator',
		'edition',
		'average_rating',
		'total_reviews',
		'rating_stars',
		'rating_stars_src',
		'director',
		'actors',
		'running_time',
		'format',
		'custom_rating',
		'product_description',
		'amazon_description',
		'artist',
		'comment',
		'percentage_saved',
		'prime',
		'prime_pic',
		'product_reviews_url',
		'tracking_id',
		'amazon_shop_url',
		'sale_price_amount',
		'sale_price_currency_code',
		'sale_price_formatted',
		'class',
		'offers_main_price_amount',
		'offers_main_price_currency_code',
		'offers_main_price_formatted_price'
	);

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
	 * @var
	 */
	protected $amazon_api_connection_type = 'http';

	/**
	 * the amazon webservice object
	 */
	protected $amazon;

	/**
	 * constructor
	 */
	public function __construct() {

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
		add_shortcode( 'apd-template', 'apd_shortcode_handler' );
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

				$apdDB = new ApdDatabase();

				$result = $apdDB->addCsvToDatabase( $tablename, $csv );

				return $result;

			} else {

				echo "Please fill out missing fields";

				return false;

			}

		}

		$this->amazon = $this->connect();

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
	public function getItemLookup( $asin ) {
		$result = $this->amazon->itemLookup( $asin, array(
			'ResponseGroup' => 'ItemAttributes,Images,Offers,OfferListings,Reviews,EditorialReview,Tracks'
		) );

		return $result;
	}

	/**
	 * Gets a row out of the default database by the set unique database field
	 *
	 * @param $item
	 *
	 * @return array|null|object|void
	 */
	public function getItem($item){

		//@todo #lastedit
		global $wpdb;
		$apdDB = new ApdDatabase();

		$uniqeField = $apdDB->getUniqueColumn( $this->datatable );

		$sql = "SELECT * FROM $this->datatable WHERE $uniqeField = %s";

		$item = $wpdb->get_row( $wpdb->prepare( $sql, $item ), OBJECT );

		return $item;

	}

	/**
	 * return
	 *
	 * @param $id
	 * @param bool $tpl
	 *
	 * @return string
	 */
	public function getItemTemplate( $id, $tpl = false ) {
		$item_html = '';

		if ( $tpl == false ) {
			$tpl = 'sidebar_item';
		}

		$tpl_src = $this->getTpl( $tpl );

		$item_html .= $this->parseTpl( trim( $id ), $tpl_src );

		$item_html = $tpl_src;

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
	 * @param $shortname
	 * @param $tpl_src
	 *
	 * @return string
	 */
	public function parseTpl( $shortname, $tpl_src ) {

		$apdDB = new ApdDatabase();
		$item  = $apdDB->getItem( $shortname );

		$html = '';

		return $html;

	}

}

global $wpdb;
$apd = new ApdCore( $wpdb );
