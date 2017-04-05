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
 * @param string $shortname
 * @param bool $tpl
 *
 * @return string
 */
function apd_get_item( $shortname, $tpl = false ) {
	global $apd;

	return $apd->getItem( $shortname, $tpl );
}

/**
 * checks if field is boolean
 *
 * @param string $field
 *
 * @return bool
 */
function field_is_boolean( $field ) {
	if ( in_array( $field, array_map( "strtolower", BOOLEAN_TYPES ) ) ) {
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
	if ( in_array( $field, array_map( "strtolower", TRUE_TYPES ) ) ) {
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
	if ( in_array( $field, array_map( "strtolower", FALSE_TYPES ) ) ) {
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
	if ( in_array( $field, array_map( "strtolower", NULL_TYPES ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * shortcode handler for [apd] tags
 *
 * @param array $atts
 * @param string $content
 * @param string $code
 *
 * @return string
 */
function apd_shortcode_handler( $atts, $content = null, $code = "" ) {

	global $apd;

	$item = $apd->getItemLookup( 'B00GSMNIM6' );

	$db = new ApdDatabase();

	echo $db->getUniqueColumn( 'products' );

	krumo( $item );

//	echo "atts:";
//	krumo( $atts );
//	echo "<br>";
//	echo "content:";
//	krumo( $content );
//	echo "<br>";

	$tpl = false;
	if ( ! empty( $atts[0] ) ) {
		$tpl = $atts[0];
	}

	return apd_get_item( $content, $tpl );
}