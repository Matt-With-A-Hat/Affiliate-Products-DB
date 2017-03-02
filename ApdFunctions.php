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

	if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){

		return false;

	}

	return true;

}