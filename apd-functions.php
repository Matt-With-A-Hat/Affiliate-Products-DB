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
	global $apdCore;

	return $apdCore->getElement( $asin, $tpl );
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
function apd_shortcode_handler( $atts, $asin = null ) {

	if ( $atts[2] === 'disabled' ) {
		return false;
	}

	if ( count( $atts ) < 1 ) {
		if ( APD_DEBUG ) {
			echo "Missing attribute in shortcode: $asin<br>";
		}

		return false;
	} else {
		$tpl = $atts[0];
	}

	//catch asin arrays
	$allowedDelimiters = "/[ ,;]/";
	if ( preg_match( $allowedDelimiters, $asin ) ) {
		$asin = preg_split( $allowedDelimiters, $asin );
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

	$apdTablePrefix = APD_TABLE_PREFIX;

	$tablenameArray = explode( "_", $tablename );

	$tablename1 = strtolower( $tablenameArray[0] );
	$tablename2 = strtolower( $tablenameArray[1] );
	$tablename3 = strtolower( $tablenameArray[2] );

	$desiredPrefix = strtolower( $wpdb->prefix . $apdTablePrefix );

	if ( $tablename1 . "_" . $tablename2 . "_" == $desiredPrefix ) {
		//tablename has both prefixes
		return $tablename;

	} else if ( $tablename1 . "_" . $apdTablePrefix == $desiredPrefix ) {
		//tablename was WordPress prefix
		return $tablename = $wpdb->prefix . $apdTablePrefix . $tablename2;

	} else {
		//tablename has no prefix
		return $tablename = $wpdb->prefix . $apdTablePrefix . $tablename;
	}

}

function remove_table_prefix( $tablename ) {
	global $wpdb;
	$apdTablePrefix = APD_TABLE_PREFIX;
	$wpPrefix       = $wpdb->prefix;

	$tablename = str_replace( $apdTablePrefix, '', $tablename );
	$tablename = str_replace( $wpPrefix, '', $tablename );

	return $tablename;
}

function print_error( $error, $function, $line ) {

	if ( APD_DEBUG ) {
		echo "Error in " . $function . " line " . $line . ": " . $error . "<br>\n";
	}

}

/**
 * flatten an arbitrarily deep multidimensional array or object into a list of its scalar values
 * (may be inefficient for large structures)
 * (will infinite recurse on self-referential structures)
 * (could be extended to handle objects)
 *
 * @param array|object $mixed
 *
 * @return array $list
 */
function array_values_recursive( $mixed ) {
	$list = array();

	if ( is_array( $mixed ) ) {
		foreach ( array_keys( $mixed ) as $key ) {
			$value = $mixed[ $key ];
			if ( is_scalar( $value ) OR $value === null ) {
				$list[] = $value;
			} elseif ( is_array( $value ) OR is_object( $value ) ) {
				$list = array_merge( $list,
					array_values_recursive( $value )
				);
			} elseif ( $value !== null ) {
				$error = "Unknown datatype.";
				print_error( $error, __METHOD__, __LINE__ );
			}
		}

	} else if ( is_object( $mixed ) ) {
		foreach ( get_object_vars( $mixed ) as $key => $value ) {
			if ( is_scalar( $value ) OR $value === null ) {
				$list[] = $value;
			} elseif ( is_array( $value ) OR is_object( $value ) ) {
				$list = array_merge( $list,
					array_values_recursive( $value )
				);
			} elseif ( $value !== null ) {
				$error = "Unknown datatype.";
				print_error( $error, __METHOD__, __LINE__ );
			}
		}

	} else {
		$error = "Provided type is not supported.";
		print_error( $error, __METHOD__, __LINE__ );
	}

	return $list;
}

/**
 * Get a string between two different other strings
 *
 * @param $string
 * @param $start
 * @param $end
 *
 * @return string
 */
function get_string_between( $string, $start, $end ) {
	$string = ' ' . $string;
	$ini    = strpos( $string, $start );
	if ( $ini == 0 ) {
		return '';
	}
	$ini += strlen( $start );
	$len = strpos( $string, $end, $ini ) - $ini;

	return substr( $string, $ini, $len );
}

/**
 * @param $haystack
 * @param $needle
 * @param $replace
 *
 * @return bool|mixed
 */
function str_replace_first( $needle, $replace, $haystack ) {
	$pos = strpos( $haystack, $needle );
	if ( $pos !== false ) {
		return $newstring = substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
	} else {
		return false;
	}
}