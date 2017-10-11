<?php
// set options
update_option( '_asa_get_rating_alternative', 2 );

// register shortcode handlers
add_shortcode( 'apd-tpl', 'apd_tpl_handler' );
add_shortcode( 'apd-group', 'apd_group_handler' );
add_shortcode( 'apd-data', 'apd_tpl_handler' );

/**
 * Hook for adding admin menus
 */
function setupBackendMenu() {
	add_options_page( 'Affiliate Products DB Setup Menu', 'Affiliate Products', 'manage_options', APD_MENU_SLUG, 'setup_menu_page' );
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
 * include frontend scripts
 */
function add_apd_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'apd-functions', plugins_url( '/js/frontend.js', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'add_apd_scripts' );

/**
 * include backend scripts
 */
function add_apd_admin_scripts() {
	wp_enqueue_script( 'apd-functions', plugins_url( '/js/admin.js', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'add_apd_admin_scripts' );

/**
 * Create the setup menupage
 */
function setup_menu_page() {

	$answer = handle_upload_form();

	include( 'apd-setupmenu-tpl.php' );

}

/**
 * Handles the form content
 */
function handle_upload_form() {

	$answer = array( null );

	if ( $_POST['upload'] ) {
		// First check if the file appears in the _FILES array
		if ( isset( $_FILES['csv-file'] ) AND $_POST['table-name'] ) {

			$csv       = $_FILES['csv-file'];
			$tablename = $_POST['table-name'];

			//check if file is csv an abort if not
			$csv_name = explode( ".", $csv['name'] );
			$csv_name = $csv_name[1];

			if ( $csv_name != 'csv' ) {
				$answer['text']    = "Error uploading file: File is not a CSV file";
				$answer['success'] = 'alert-danger';

				return $answer;
			}

			$apdDB  = new ApdDatabase( $tablename );
			$result = $apdDB->addCsvToDatabase( $csv );

			if ( $result === 1 ) {
				$answer['text']    = "CSV upload successful. Table <strong><em>\"$tablename\"</em></strong> was <strong>created</strong>.";
				$answer['success'] = 'alert-success';
			} else if ( $result === 2 ) {
				$answer['text']    = "Existing table <strong><em>\"$tablename\"</em></strong> was <strong>replaced</strong> with new one (debug mode).";
				$answer['success'] = 'alert-success';
			} else if ( $result === 3 ) {
				$answer['text']    = "Existing table <strong><em>\"$tablename\"</em></strong> has been <strong>updated</strong> with content from CSV file.";
				$answer['success'] = 'alert-success';
			} else {
				$answer['text']    = "<strong>Something went wrong</strong>, while trying to upload the CSV";
				$answer['success'] = 'alert-danger';

				return $answer;
			}

			return $answer;

		} else {

			$answer['text']    = "Please fill out missing fields!";
			$answer['success'] = 'alert-danger';

			return $answer;
		}
	} else if ( $_POST['generate-posts'] ) {
		if ( isset( $_POST['product-tables-selection'] ) AND isset( $_POST['title-column'] ) ) {
			$tablename   = $_POST['product-tables-selection'];
			$titleColumn = $_POST['title-column'];
			$categories  = $_POST['categories'];
			$content     = $_POST['content'];

			if ( empty( $categories ) ) {
				$categories = null;
			} else {

			}

			if ( empty( $content ) ) {
				$content = '';
			}

			$postGenerator = new ApdPostGenerator( $tablename, $titleColumn, $categories, $content );
			$count         = $postGenerator->generatePosts();

			$answer['text']    = "$count posts generated successfully";
			$answer['success'] = 'alert-success';

			return $answer;
		} else {
			$answer['text']    = "Please fill in required fields";
			$answer['success'] = 'alert-danger';

			return $answer;
		}
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
	$databaseService = new ApdDatabaseService();
	$databaseService->checkDatabaseTables();
}

register_activation_hook( APD_BASE_FILE, 'apd_options_install' );

function load_fonts() {
	wp_register_style( 'apdGoogleFonts', 'https://fonts.googleapis.com/css?family=Droid+Sans:400,700|Roboto:400,400i,700,700i' );
	wp_enqueue_style( 'apdGoogleFonts' );
}

add_action( 'wp_print_styles', 'load_fonts' );

//add_action('wp', 'apd_options_install');