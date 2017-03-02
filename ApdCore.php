<?php

class AffiliateProductDb {

	/**
	 * this plugins home directory
	 */
	protected $plugin_dir = '/wp-content/plugins/affiliate-product-db';

	protected $plugin_url = 'options-general.php?page=/affiliate-product-db.php';

	/**
	 * constructor
	 */
	public function __construct() {

		// register shortcode handler for [apd] tags
		add_shortcode( 'apd-tpl', 'apd_shortcode_handler' );

		/**
		 * Hook for adding admin menus
		 */
		function setupBackendMenu() {
//			add_menu_page( 'Affiliate Product DB Setup Menu', 'Affiliate Products', 'manage_options', 'apdsetupmenu', 'setupMenuPage', '', 75 );
			add_options_page( 'Affiliate Product DB Setup Menu', 'Affiliate Products', 'manage_options', 'affiliate-product-db', 'setupMenuPage' );

		}

		add_action( 'admin_menu', 'setupBackendMenu' );

		/**
		 * include stylesheets for plugin
		 */
		function add_apd_stylesheets() {
			wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
			wp_enqueue_style( 'apdplugin', plugins_url( '/css/apdplugin.css', __FILE__ ) );
		}

		add_action( 'admin_enqueue_scripts', 'add_apd_stylesheets' );

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

			require_once( dirname( __FILE__ ) . '/ApdDatabase.php' );

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

	}


	/**
	 * return
	 *
	 * @param $shortname
	 * @param bool $tpl
	 *
	 * @return string
	 */
	public function getItem( $shortname, $tpl = false ) {
		$item_html = '';

		if ( $tpl == false ) {
			$tpl = 'sidebar_item';
		}

		$tpl_src = $this->getTpl( $tpl );

		$item_html .= $this->parseTpl( trim( $shortname ), $tpl_src );

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

	public function parseTpl( $shortname, $tpl_src ) {
		return false;
	}

}

global $wpdb;
$apd = new AffiliateProductDb( $wpdb );

/**
 * return the rendered product template
 *
 * @param string $shortname
 * @param bool $tpl
 *
 * @return string
 */
function apd_get_item( $shortname, $tpl = false ) {
	global $apd;

	return $apd->getItem( $shortname, $tpl );
}

/**
 * shortcode handler for [apd] tags
 *
 * @param array $atts
 * @param string $content
 * @param string $code
 *
 * @return string
 */
function apd_shortcode_handler( $atts, $content = null, $code = "" ) {

	echo "atts:";
	krumo( $atts );
	echo "<br>";
	echo "content:";
	krumo( $content );
	echo "<br>";

	$tpl = false;
	if ( ! empty( $atts[0] ) ) {
		$tpl = $atts[0];
	}

	return apd_get_item( $content, $tpl );
}

