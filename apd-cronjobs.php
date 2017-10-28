<?php
/**
 * apd-cronjob.php
 *
 * This file handles all that is related to initializing and stopping APD cronjobs
 */

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
 * set up options for the apdcronjob
 */
function cron_amazon_cache_bootstrap() {

//	@todo make this a class ApdOptionsCache that is extended by ApdAmazonCacheDatabase
	//These are options for the initial startup of the Amazon Cache cronjob
	$apd_ac_options = array(
		'interval_minutes'              => 1,                    //the initial interval to update the cache
		'inc_interval_rate_minutes'     => 5,           //the amount of minutes to add to the interval, if Amazon API returns throttle error
		'dec_interval_rate_minutes'     => 1,           //the amount of minutes to subtract  from the interval, to recover from a throtteling event
		'successful_requests_threshold' => 20,             //the number of intervals after which an attempt to decrease the interval is made
		'items_per_update'              => 5,                   //the number of Amazon items that are updated with each request
	);

	$amazonCacheDatabase = new ApdAmazonCacheDatabase();
	$amazonCacheDatabase->setOptions( $apd_ac_options );

	//insert first row in cache options table
	global $wpdb;
	$data              = $apd_ac_options;
	$data['last_edit'] = current_time( 'mysql' );
	$result            = $wpdb->insert( $amazonCacheDatabase->getTablenameOptions(), $data );

}

register_activation_hook( APD_BASE_FILE, 'cron_amazon_cache_bootstrap' );

/**
 * schedule a cronjob if none is set on every pageload
 */
function apdcronjob_trigger() {

	//cache cronjob
	$name     = ApdAmazonCache::getCronjobName();
	$interval = ( new ApdAmazonCacheDatabase() )->getOption( 'interval_minutes' );
	$cronjob  = new ApdCronjob( $name, $interval );
	$cronjob->setCronjob();

	//asin table cronjob
	$name    = ApdAsinTable::getCronjobName();
	$cronjob = new ApdCronjob( $name, 5 );
	$cronjob->setCronjob();

	//database consistency cronjob
	$name    = APD_DB_CONSISTENCY_CRON;
	$cronjob = new ApdCronjob( $name, 15 );
	$cronjob->setCronjob();
}

add_action( 'wp', 'apdcronjob_trigger' );

/**
 * unschedule cronjobs upon plugin deactivation
 */
function apdcronjob_deactivate() {

	//cache cronjob
	$name      = ApdAmazonCache::getCronjobName();
	$timestamp = wp_next_scheduled( $name );
	wp_unschedule_event( $timestamp, $name );

	//asin table cronjob
	$name      = ApdAsinTable::getCronjobName();
	$timestamp = wp_next_scheduled( $name );
	wp_unschedule_event( $timestamp, $name );

	//database consistency cronjob
	$name      = APD_DB_CONSISTENCY_CRON;
	$timestamp = wp_next_scheduled( $name );
	wp_unschedule_event( $timestamp, $name );
}

register_deactivation_hook( APD_BASE_FILE, 'apdcronjob_deactivate' );

/**
 * --------------------------------------------------------------
 * =Cronjobs
 * --------------------------------------------------------------
 *
 */

/**
 * cronjob trigger cache update
 */
function update_amazon_items_cache() {
	$amazonCacheDatabase = new ApdAmazonCacheDatabase();
	$amazonCacheDatabase->updateCache();
}

add_action( ApdAmazonCache::getCronjobName(), 'update_amazon_items_cache' );

/**
 * cronjob trigger asins table update
 */
function update_asin_table() {
	$databaseService = new ApdDatabaseService();
	$databaseService->updateAsins();
}

add_action( ApdAsinTable::getCronjobName(), 'update_asin_table' );

/**
 * cronjob trigger database consistency check
 */
function check_database_consistency(){
	(new ApdDatabaseService())->checkDatabaseTables();
}

add_action( APD_DB_CONSISTENCY_CRON, 'check_database_consistency' );