<?php
// set options
update_option( '_asa_get_rating_alternative', 2 );

// register shortcode handlers
add_shortcode( 'apd-tpl', 'apd_shortcode_handler' );
add_shortcode( 'apd-data', 'apd_shortcode_handler' );

/**
 * Hook for adding admin menus
 */
function setupBackendMenu() {
	add_options_page( 'Affiliate Products DB Setup Menu', 'Affiliate Products', 'manage_options', APD_MENU_SLUG, 'setupMenuPage' );
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
	if ( $_GET['page'] == APD_MENU_SLUG ) {
		wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'setupmenu', plugins_url( '/css/setupmenu.css', __FILE__ ) );
	}
}

add_action( 'admin_enqueue_scripts', 'add_apd_admin_stylesheets' );

/**
 * include backend scripts
 */
function add_apd_admin_scripts() {
	wp_enqueue_script( 'apd-functions', plugins_url( '/js/functions.js', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'add_apd_admin_scripts' );

/**
 * Create the setup menupage
 */
function setupMenuPage() {

	handle_upload_form();

	include( 'apd-setupmenu-tpl.php' );

}

/**
 * Handles the form content
 */
function handle_upload_form() {

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

/**
 * Add settings link on plugin page
 *
 * @param $links
 *
 * @return mixed
 */
function apd_settings_link( array $links ) {
	$url           = get_admin_url() . "options-general.php?page=" . APD_MENU_SLUG;
	$settings_link = '<a href="' . $url . '">' . __( 'Settings', 'textdomain' ) . '</a>';
	$links[]       = $settings_link;

	return $links;
}

add_filter( 'plugin_action_links_' . APD_BASENAME, 'apd_settings_link' );


/**
 * installations routines triggered on plugin activation
 */
function apd_options_install() {
	global $wpdb;

	//create amazon items table
	$apdDatabase    = new ApdDatabase();
	$apdAmazonCache = new ApdAmazonCache();
	$tablename      = APD_AMAZON_ITEMS_TABLE;

	$apdDatabase->createTableFromArray( $tablename, $apdAmazonCache->getAmazonFields() );

	//create amazon cache options table
	$tablename = APD_CACHE_OPTIONS_TABLE;

	$apdDatabase->createTableFromArray( $tablename, $apdAmazonCache->getOptionFields() );

}

register_activation_hook( APD_BASE_FILE, 'apd_options_install' );

//add_action('wp', 'apd_options_install');