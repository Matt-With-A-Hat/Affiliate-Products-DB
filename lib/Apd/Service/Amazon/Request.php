<?php

/**
 * AmazonSimpleAdmin - Wordpress Plugin
 *
 * @author Timo Reith
 * @copyright Copyright (c) 2007-2011 Timo Reith (http://www.wp-amazon-plugin.com)
 *
 *
 */
abstract class Apd_Service_Amazon_Request {

	private function __construct() {
	}

	/**
	 * Fatory for Apd_Service_Amazon_Request_Abstract
	 *
	 * @param Apd_Service_Amazon $apd
	 *
	 * @return Apd_Service_Amazon_Request_Abstract
	 */
	public static function factory( Apd_Service_Amazon $apd ) {
		$request = null;

		try {
			require_once APD_LIB_DIR . 'Apd/Service/Amazon/Request/Rest.php';
			$request = new Apd_Service_Amazon_Request_Rest( $apd );
		} catch ( Exception $e1 ) {

			try {
				if ( function_exists( 'curl_init' ) ) {
					// if curl exists
					require_once APD_LIB_DIR . 'Apd/Service/Amazon/Request/Curl.php';
					$request = new Apd_Service_Amazon_Request_Curl( $apd );
				} else {
					// else socket
					require_once APD_LIB_DIR . 'Apd/Service/Amazon/Request/Socket.php';
					$request = new Apd_Service_Amazon_Request_Socket( $apd );
				}
			} catch ( Exception $e2 ) {
				throw $e1;
			}
		}

		return $request;
	}
}