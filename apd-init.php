<?php
// set options
update_option( '_asa_get_rating_alternative', 2 );

// register shortcode handlers
add_shortcode( 'apd-tpl', 'apd_tpl_handler' );
add_shortcode( 'apd-group', 'apd_group_handler' );
add_shortcode( 'apd-filter', 'apd_filter_handler' );
add_shortcode( 'apd-data', 'apd_tpl_handler' );

/**
 * Hook for adding admin menus
 */
function setupBackendMenu() {
	$iconurl = plugins_url( '/img/admin/database.svg', __FILE__ );
	add_menu_page( 'Affiliate Products DB Setup Menu', 'Affiliate Products', 'manage_options', APD_MENU_SLUG, 'setup_menu_page', $iconurl );
}

add_action( 'admin_menu', 'setupBackendMenu' );

/**
 * include frontend stylesheets
 */
function add_apd_stylesheets() {
	wp_enqueue_style( 'apdplugin', plugins_url( '/css/apdplugin.css', __FILE__ ), array(), APD_PLUGIN_VERSION );
	wp_enqueue_style( 'font-awesome', plugins_url( '/css/font-awesome.min.css', __FILE__ ), array(), APD_PLUGIN_VERSION );
}

add_action( 'wp_enqueue_scripts', 'add_apd_stylesheets' );

/**
 * include backend stylesheets for APD plugin
 */
function add_apd_admin_stylesheets() {
	if ( $_GET['page'] == APD_MENU_SLUG ) {
		wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ), array(), APD_PLUGIN_VERSION );
		wp_enqueue_style( 'setupmenu', plugins_url( '/css/setupmenu.css', __FILE__ ), array(), APD_PLUGIN_VERSION );
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

/**
 * load fonts
 */
function load_fonts() {
	wp_register_style( 'apdGoogleFonts', 'https://fonts.googleapis.com/css?family=Droid+Sans:400,700|Roboto:400,400i,700,700i' );
	wp_enqueue_style( 'apdGoogleFonts' );
}

add_action( 'wp_print_styles', 'load_fonts' );