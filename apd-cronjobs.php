<?php
/*
 * apd-cronjob.php
 * 
 * This file handles all that is related to initializing and stopping APD cronjobs
 */

/**
 * APD Amazon Cache Cronjob Constants
 *
 * These are options for the initial startup of the Amazon Cache cronjob
 */
$apd_ac_options = array(
	'interval_minutes'          => 1,                    //the initial interval to update the cache
	'inc_interval_rate_minutes' => 5,           //the amount of minutes to add to the interval, if Amazon API returns throttle error
	'dec_interval_rate_minutes' => 1,           //the amount of minutes to subtract  from the interval, to recover from a throtteling event
	'dec_interval_every_nth'    => 20,             //the number of intervals after which an attempt to decrease the interval is made
	'items_per_update'          => 10,                   //the number of Amazon items that are updated with each request
);

/**
 * set up options for the apdcronjob
 */
function apdcronjob_bootstrap(){

	global $apd_ac_options;
	$amazonCache = new ApdAmazonCache();
	$amazonCache->setOptions( $apd_ac_options );

	//insert first row in cache options table
	global $wpdb;
	$data              = $apd_ac_options;
	$data['last_edit'] = current_time( 'mysql' );
	$result = $wpdb->insert( $amazonCache->getTablenameOptions(), $data );

}

register_activation_hook( APD_BASE_FILE, 'apdcronjob_bootstrap' );

/**
 * creates custom cronjob intervals for every x minutes
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_minute( $schedules ) {
	for ( $i = 0; $i < 60; $i ++ ) {
		$schedules[ $i ] = array(
			'interval' => 60 * $i,
			'display'  => __( "Every $i Minute(s)" )
		);
	}

	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_minute' );

/**
 * schedule a cronjob if none is set on every pageload
 */
function apdcronjob_trigger() {

	//----------------------- for testing -----------------------
//	$interval = 1;
//	if ( ! wp_next_scheduled( 'apdcronjob' ) ) {
//		echo "IN";
//		wp_schedule_event( time(), $interval, 'apdcronjob' );
//	}

	//----------------------- deployment -----------------------
	$amazonCache = new ApdAmazonCache();
	$amazonCache->setCronjob( $amazonCache->getOption( 'interval_minutes' ) );

}

add_action( 'wp', 'apdcronjob_trigger' );

/**
 * unschedule cronjobs upon plugin deactivation
 */
function apdcronjob_deactivate() {

	$timestamp = wp_next_scheduled( 'apdcronjob' );
	wp_unschedule_event( $timestamp, 'apdcronjob' );

}

register_deactivation_hook( APD_BASE_FILE, 'apdcronjob_deactivate' );

/**
 * cronjob trigger function
 */
function update_amazon_items_cache() {

	//----------------------- for testing -----------------------

	global $wpdb;

	$time      = current_time( 'mysql' );
	$tablename = $wpdb->prefix . APD_AMAZON_ITEMS_TABLE;

	$sql = "INSERT " . $tablename . " SET ASIN = \"" . $time . "\"";
	$wpdb->query( $sql );


	//----------------------- deployment code -----------------------

//	$amazonCache = new ApdAmazonCache();
//	$amazonCache->updateCache();
}

add_action( 'apdcronjob', 'update_amazon_items_cache' );


echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
$amazonCache = new ApdAmazonCache();
$options = array( 'interval_minutes' => 42);

$amazonCache->setOptions($options);

//
//krumo( wp_get_schedules() );

/**
 * // * * ----------------------- [ =for debugging ] -----------------------
 */
//
//function tl_save_error() {
//	update_option( 'plugin_error', ob_get_contents() );
//}
//
//add_action( 'activated_plugin', 'tl_save_error' );
///* Then to display the error message: */
//echo get_option( 'plugin_error' );
///* Or you could do the following: */
//file_put_contents( 'C:\errors', ob_get_contents() ); // or any suspected variable