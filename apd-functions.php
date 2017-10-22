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
 * @param bool $atts
 *
 * @return string
 */
function apd_get_item( $asin, $atts = false ) {
	global $apdCore;

	return $apdCore->getElement( $asin, $atts );
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
 *
 * @return string
 * @internal param string $code
 *
 */
function apd_tpl_handler( $atts, $asin = null ) {

	if ( $atts[2] === 'disabled' ) {
		return false;
	}

	//catch asin arrays
	$allowedDelimiters = "/[ ,;]/";
	if ( preg_match( $allowedDelimiters, $asin ) ) {
		$asin = preg_split( $allowedDelimiters, $asin );
	}

	return apd_get_item( $asin, $atts );
}

/**
 * groups a number of shortcodes and puts it in a box with tabs
 *
 * @param $atts
 * @param $shortcodes
 *
 * @return mixed|string
 */
function apd_group_handler( $atts, $shortcodes ) {
	preg_match_all( '/\[apd-tpl(.)*\]/', $shortcodes, $matches );
	$html       = '';
	$htmlBefore = '<div class="products-box"><div class="tabs"><ul class="nav nav-tabs">';
	$htmlAfter  = '</div></div>';

	$i = 0;
	foreach ( $matches[0] as $match ) {
		$box = do_shortcode( $match, true );
		$html .= str_replace( '<br />', '', $box ); //remove unnecessary <br> tags at beginning and end of box that come from WP
		$atts    = get_shortcode_atts( $match );
		$titleId = $atts[1];
		$title   = str_replace( '-', ' ', $titleId );
		if ( $i == 0 ) {
			$tablink = "tablink active";
		} else {
			$tablink = "tablink";
		}
		$htmlBefore .= "<li class='" . $tablink . "' data-toggle='$titleId'><a>$title</a></li>";
		$i ++;
	}
	$htmlBefore .= '</ul>';

	$html = preg_replace( '/active tab-content/', 'tab-content', $html );
	$html = preg_replace( '/tab-content/', 'active tab-content', $html, 1 );

	$html = $htmlBefore . $html . $htmlAfter;

	return $html;
}

/**
 * get the attributes of a shortcode with content
 *
 * @param $shortcode
 *
 * @return array
 */
function get_shortcode_atts( $shortcode ) {
	preg_match( '/\[apd-tpl([^\]])*/', $shortcode, $match );
	$atts = explode( ' ', $match[0] );
	array_shift( $atts );

	return $atts;
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
 * removes duplicates from array (case insensitive)
 *
 * @param array $array
 *
 * @return array
 */
function array_remove_duplicates( array $array ) {
	$uniqueArrayUpper = array_unique( array_map( "strtoupper", $array ) );

	$uniqueArray = array_intersect_key( $array, $uniqueArrayUpper );

	return $uniqueArray;
}

/**
 * removes everything after $character in each element of a string array
 *
 * @param array $array
 * @param $character
 *
 * @return array
 */
function array_remove_tail( array $array, $character ) {
	foreach ( $array as $key => $item ) {
		$pos = strrpos( $item, $character );
		if ( $pos !== false ) {
			$array[ $key ] = substr( $item, 0, $pos );
		}
	}

	return $array;
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

/**
 * get names of all of an objects accessible fields as an array
 *
 * @param $item (only objects and arrays with one level)
 *
 * @return array
 */
function get_fields( $item ) {
	if ( is_object( $item ) ) {
		$fields = get_object_vars( $item );
		$result = array();
		foreach ( $fields as $key => $field ) {
			$result[] = $key;
		}
	} else if ( is_array( $item ) ) {
		foreach ( $item as $key => $field ) {
			$result[] = $key;
		}
	} else {
		return false;
	}

	return $result;
}