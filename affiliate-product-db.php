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
 * APD Functions
 */
include_once( dirname( __FILE__ ) . '/ApdFunctions.php' );

/**
 * APD Classes
 */
include_once( dirname( __FILE__ ) . '/ApdCore.php' );
require_once( dirname( __FILE__ ) . '/ApdDatabase.php' );

/**
 * Krumo
 */
require dirname( __FILE__ ) . '/krumo_0.2.1a/class.krumo.php';

/**
 * Debug
 */
require dirname( __FILE__ ) . '/debug.php';
