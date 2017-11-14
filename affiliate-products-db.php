<?php
/**
 * WordPress plugin "TablePress" main file, responsible for initiating the plugin
 *
 * @package TablePress
 * @author Tobias Bäthge
 * @version 1.7
 */

/*
Plugin Name: Affiliate Products DB
Plugin URI: https://#
Description: Manage details on all your presented affiliate products of your affiliate site.
Version: 0.1.5
Author: Matthias Müller
Author URI: https://#
Author email: matthias.mueller88@web.de
Text Domain: affiliate-products-db
Domain Path: /languages
License: GPL 2
Donate URI: https://#
*/

/*	Copyright 2017 Matthias Müller

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
// Start up TablePress on WordPress's "init" action hook.
//add_action( 'init', array( 'TablePress', 'run' ) );


/**
 * APD Constants & Options
 */
define( 'APD_PLUGIN_VERSION', '0.1.9' );
define( 'APD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APD_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );
define( 'APD_PLUGIN_URL', 'options-general.php?page=affiliate-product-db.php' );
define( 'APD_BASENAME', plugin_basename( __FILE__ ) );
define( 'APD_BASE_FILE', __FILE__ );
define( 'APD_LIB_DIR', dirname( __FILE__ ) . '/lib/' );
define( 'APD_MENU_SLUG', 'affiliate-products-db' );

/**
 * Table Names
 */
//define( 'APD_ITEMS_TABLE', 'amazon_items' );
define( 'APD_AMAZON_CACHE_TABLE', 'amazon_cache' );
define( 'APD_CACHE_OPTIONS_TABLE', 'cache_options' );
define( 'APD_TABLE_LIST_TABLE', 'table_list' );
define( 'APD_ASIN_TABLE', 'asin_table' );
define( 'APD_TABLE_PREFIX', 'apd_' );

/**
 * Cron Jobs
 */
define( 'APD_DB_CONSISTENCY_CRON', 'apd_database_consistency' );
define( 'APD_LOG_FILE', APD_PLUGIN_DIR . '/apdlog.log' );

/**
 * User Constants
 */
define( 'AMAZON_API_KEY', 'AKIAIN6P6NRW4F3AFUCQ' );
define( 'AMAZON_API_SECRET_KEY', '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' );
define( 'AMAZON_TRACKING_ID', 'wwwmeinrasenr-21' );
define( 'AMAZON_COUNTRY_CODE', 'DE' );
define( 'AMAZON_API_CONNECTION_TYPE', 'http' );

/**
 * =APD Data
 * @todo this is supposed to go into a database
 */
define( 'APD_AUTOMOWERS_PPR_FACTOR_1', 4900 / 17 );
define( 'APD_AUTOMOWERS_PPR_FACTOR_2', 10 / 17 );

/**
 * User Settings
 */
define( 'APD_EMPTY_PRICE_TEXT', '<span class="text-red not-available">Derzeit nicht verfügbar</span>' );
define( 'APD_EMPTY_AVAILABILITY_TEXT', 'Händlerabhängig' );

//csv import field types
const BOOLEAN_TYPES = array( 'BOOLEAN', 'BOOL', 'TINYINT(1)' );
const TRUE_TYPES    = array( 'JA', 'YES', 'TRUE', '1', 1 );
const FALSE_TYPES   = array( 'NEIN', 'NO', 'FALSE', '0', 0 );
const NULL_TYPES    = array( '', 'NULL', null );

/**
 * APD Functions
 */
require( dirname( __FILE__ ) . '/apd-functions.php' );

/**
 * =Debug Functions & Settings
 */
$domain_name = $_SERVER['HTTP_HOST'];
if ( isLocalInstallation() OR $domain_name == 'wp-apd.refugeek.net') {
	define( 'APD_DEBUG', true );
	define( 'APD_REPLACE_TABLES', false ); //WARNING: THIS WILL DROP TABLES FROM DB IF THEY ALREADY EXIST UPON CREATION
} else {
	define( 'APD_DEBUG', false );
	define( 'APD_REPLACE_TABLES', false ); //WARNING: THIS WILL DROP TABLES FROM DB IF THEY ALREADY EXIST UPON CREATION
}
if ( APD_DEBUG ) {
	require dirname( __FILE__ ) . '/debug.php';
	require_once dirname( __FILE__ ) . '/vendor/mmucklo/krumo/class.krumo.php';
}

/**
 * functions for initializing plugin
 */
require dirname( __FILE__ ) . '/apd-init.php';

/**
 * functions for backend menu
 */
require dirname( __FILE__ ) . '/apd-setupmenu.php';

/**
 * early loading of WordPress functions
 */
require_once( ABSPATH . "wp-includes/pluggable.php" );      //required for PostGenerator to work

/**
 * AsaZend library
 */
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

/**
 * APD classes
 */
require_once( dirname( __FILE__ ) . '/ApdCore.php' );
require_once( dirname( __FILE__ ) . '/ApdDatabase.php' );
require_once( dirname( __FILE__ ) . '/ApdCsv.php' );
require_once( dirname( __FILE__ ) . '/ApdDatabaseService.php' );
require_once( dirname( __FILE__ ) . '/ApdCronjob.php' );
require_once( dirname( __FILE__ ) . '/ApdCustomerReviews.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonCache.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonCacheDatabase.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonCacheItem.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonItem.php' );
require_once( dirname( __FILE__ ) . '/ApdCustomItem.php' );
require_once( dirname( __FILE__ ) . '/ApdApi.php' );
require_once( dirname( __FILE__ ) . '/ApdAsinTable.php' );
require_once( dirname( __FILE__ ) . '/ApdPostGenerator.php' );

/**
 * =Widgets
 */
require_once( dirname( __FILE__ ) . '/inc/BestsellerWidget.php' );
require_once( dirname( __FILE__ ) . '/inc/FilterWidget.php' );


/**
 * cronjob functions
 */
require dirname( __FILE__ ) . '/apd-cronjobs.php';

$apdCore = new ApdCore();

/**
 * =For testing
 */
//echo "<br><br><br><br>";

//krumo('test');
//$Api  = new ApdApi();
//$item = $Api->getItemByAsin( 'B00S4Z8BIQ' );
//krumo( $item );
//$item = $Api->getItemByPostId( '1543' );
//krumo( $item );
//$asin1       = 'B00S4Z8BIQ';
//$asin2       = 'B006MWDNVI';
//$apdCore    = new ApdCore();
//$amazonWbs  = $apdCore->amazonWbs;
//$amazonItem = new ApdAmazonItem( $amazonWbs, $asin1 );
//krumo($amazonItem);

//$apdAmazonCacheDatabase = new ApdAmazonCacheDatabase();
//$apdAmazonCacheDatabase->updateCache();

//$databaseService = new ApdDatabaseService();
//$databaseService->checkDatabaseTables();
//$databaseService->updateAsins();
