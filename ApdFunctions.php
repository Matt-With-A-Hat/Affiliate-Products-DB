<?php

/**
 * checks if wordpress installation is on local machine
 * @return bool
 */
function isLocalInstallation() {

	$whitelist = array(
		'127.0.0.1',
		'::1'
	);

	if ( ! in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {

		return false;

	}

	return true;

}


/**
 * Refines a path so it can be used on local installations
 *
 * @param $path
 *
 * @return mixed
 */
function path_for_local( $path ) {

	if ( isLocalInstallation() ) {
		$path = str_replace( "\\", "/", $path );
	}

	return $path;
}

/**
 * return the rendered product template
 *
 * @param string $asin
 * @param bool $tpl
 *
 * @return string
 */
function apd_get_item( $asin, $tpl = false ) {
	global $apd;

	return $apd->getItem( $asin, $tpl );
}

/**
 * checks if field is boolean
 *
 * @param string $type
 *
 * @return bool
 */
function type_is_boolean( $type ) {
	if ( in_array( strtolower( $type ), array_map( "strtolower", BOOLEAN_TYPES ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * checks if field is true
 *
 * @param string $field
 *
 * @return bool
 */
function field_is_true( $field ) {
	if ( in_array( strtolower( $field ), array_map( "strtolower", TRUE_TYPES ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * checks if field is false
 *
 * @param string $field
 *
 * @return bool
 */
function field_is_false( $field ) {
	if ( in_array( strtolower( $field ), array_map( "strtolower", FALSE_TYPES ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * checks if field is null
 *
 * @param string $field
 *
 * @return bool
 */
function field_is_null( $field ) {
	if ( in_array( strtolower( $field ), array_map( "strtolower", NULL_TYPES ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * shortcode handler for [apd] tags
 *
 * @param array $atts
 * @param string $asin
 * @param string $code
 *
 * @return string
 */
function apd_shortcode_handler( $atts, $asin = null, $code = "" ) {

	global $apd;

	$tpl = false;
	if ( ! empty( $atts[0] ) ) {
		$tpl = $atts[0];
	}

	return apd_get_item( $asin, $tpl );
}

/**
 * checks if an array has duplicates
 *
 * @param array $array
 *
 * @return array
 */
function array_duplicates( array $array ) {
	$duplicates = array();
	natcasesort( $array );
	reset( $array );

	$old_key   = null;
	$old_value = null;
	foreach ( $array as $key => $value ) {
		if ( $value === null ) {
			continue;
		}
		if ( strcasecmp( $old_value, $value ) === 0 ) {
			$duplicates[ $old_key ] = $old_value;
			$duplicates[ $key ]     = $value;
		}
		$old_value = $value;
		$old_key   = $key;
	}

	return $duplicates;
}

/**
 * removes duplicates from array (case insensitive)
 *
 * @param array $array
 */
function array_remove_duplicates( array $array ) {
	$uniqueArrayUpper = array_unique( array_map( "strtoupper", $array ) );

	$uniqueArray = array_intersect_key( $array, $uniqueArrayUpper );

	return $uniqueArray;
}

/**
 * @param string $path
 * @param string $plugin
 *
 * @return string
 */
function apd_plugins_url( $path = '', $plugin = '' ) {
	if ( getenv( 'APD_APPLICATION_ENV' ) == 'development' ) {
		return get_bloginfo( 'wpurl' ) . '/wp-content/plugins/affiliate-product-db/' . $path;
	}

	return plugins_url( $path, $plugin );
}

/**
 * add WP table prefix if it's missing
 *
 * @param $tablename
 *
 * @return string
 */
function add_table_prefix( $tablename ) {

	global $wpdb;

	$tablenameArray = explode( "_", $tablename );

	if ( $tablenameArray[0] . "_" == $wpdb->prefix ) {

		return $tablename;

	} else {

		return $wpdb->prefix . $tablename;

	}

}