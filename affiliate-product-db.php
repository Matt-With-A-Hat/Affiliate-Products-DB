<?php
/**
 * WordPress plugin "TablePress" main file, responsible for initiating the plugin
 *
 * @package TablePress
 * @author Tobias Bäthge
 * @version 1.7
 */

/*
Plugin Name: Affiliate Product DB
Plugin URI: https://#
Description: Manage details on all your presented affiliate products of your affiliate site.
Version: 0.1.3
Author: Matthias Müller
Author URI: https://#
Author email: matthias.mueller88@web.de
Text Domain: affiliate-product-db
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
//D:\xampp\htdocs\mein_rasenroboter\wp-content\plugins\affiliate-product-db/
define( 'APD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

//options-general.php?page=affiliate-product-db.php
define( 'APD_PLUGIN_URL', 'options-general.php?page=affiliate-product-db.php' );

//affiliate-product-db/affiliate-product-db.php
define( 'APD_BASENAME', plugin_basename( __FILE__ ) );

//D:\xampp\htdocs\mein_rasenroboter\wp-content\plugins\affiliate-product-db\affiliate-product-db.php
define( 'APD_BASE_FILE', __FILE__ );

//D:\xampp\htdocs\mein_rasenroboter\wp-content\plugins\affiliate-product-db/lib/
define( 'APD_LIB_DIR', dirname( __FILE__ ) . '/lib/' );

//affiliate-products-db
define( 'APD_MENU_SLUG', 'affiliate-products-db' );

define( 'APD_DEBUG', true );

//WARNING: THIS WILL DROP TABLES FROM DB IF THEY ALREADY EXIST UPON CREATION
define( 'APD_DEBUG_DEV', true );

/**
 * Table Names
 */
//define( 'APD_ITEMS_TABLE', 'amazon_items' );
define( 'APD_AMAZON_CACHE_TABLE', 'amazon_cache' );
define( 'APD_CACHE_OPTIONS_TABLE', 'cache_options' );
define( 'APD_TABLE_LIST_TABLE', 'table_list' );
define( 'APD_TABLE_PREFIX', 'apd_' );

/**
 * User Constants
 */
define( 'AMAZON_API_KEY', 'AKIAIN6P6NRW4F3AFUCQ' );
define( 'AMAZON_API_SECRET_KEY', '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' );
define( 'AMAZON_TRACKING_ID', 'wwwmeinrasenr-21' );
define( 'AMAZON_COUNTRY_CODE', 'DE' );
define( 'AMAZON_API_CONNECTION_TYPE', 'http' );

//csv import field types
const BOOLEAN_TYPES = array( 'BOOLEAN', 'BOOL', 'TINYINT(1)' );
const TRUE_TYPES    = array( 'JA', 'YES', 'TRUE', '1', 1 );
const FALSE_TYPES   = array( 'NEIN', 'NO', 'FALSE', '0', 0 );
const NULL_TYPES    = array( '', 'NULL', null );

/**
 * Krumo
 */
require dirname( __FILE__ ) . '/krumo_0.2.1a/class.krumo.php';

/**
 * Debug
 */
require dirname( __FILE__ ) . '/debug.php';

/**
 * APD Functions
 */
require( dirname( __FILE__ ) . '/apd-functions.php' );

/**
 * functions for initializing plugin
 */
require dirname( __FILE__ ) . '/apd-init.php';


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
require_once( dirname( __FILE__ ) . '/ApdDatabaseService.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonItem.php' );
require_once( dirname( __FILE__ ) . '/ApdAmazonCache.php' );
require_once( dirname( __FILE__ ) . '/ApdItem.php' );

/**
 * cronjob functions
 */
require dirname( __FILE__ ) . '/apd-cronjobs.php';

$apd = new ApdCore();
