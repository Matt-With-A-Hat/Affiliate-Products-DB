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

	return $apd->getItemTemplate( $shortname, $tpl );
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

	$itemAmazon = $apd->getItemLookup($content);
	$item = $apd->getItem($content);

	krumo($itemAmazon);
	krumo($item);

	$tpl = false;
	if ( ! empty( $atts[0] ) ) {
		$tpl = $atts[0];
	}

	return apd_get_item( $content, $tpl );
}