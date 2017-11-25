<?php
/**
 * Created by PhpStorm.
 * User: Matthias
 * Date: 01.12.2016
 * Time: 10:49
 */

function d_visibility() {

	$ip = $_SERVER['REMOTE_ADDR'];

	$whitelist = array(
		'89.0.223.149',
		'127.0.0.1',
		'::1'
	);

	if ( in_array( $ip, $whitelist ) ) {

		return true;

	} else {

		return false;

	}

}

function d_var_dump( $mixed = null ) {
	echo '<pre class="debug-pre">';
	var_dump( $mixed );
	echo '</pre>';

	return null;
}

function d_print_r( $mixed = null ) {
	echo '<pre class="debug-pre">';
	print_r( $mixed );
	echo '</pre>';

	return null;
}

function d_print_no_contents( $arr ) {
	echo '<pre class="debug-pre">';
	foreach ( $arr as $k => $v ) {
		echo $k . "=> ";
		if ( is_array( $v ) ) {
			echo "\n";
			print_no_contents( $v );
		} else {
			echo "[data]";
		}
		echo "\n";
	}
	echo "</pre>";
}