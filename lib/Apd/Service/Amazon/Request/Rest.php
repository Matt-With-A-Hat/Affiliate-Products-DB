<?php
/**
 * AmazonSimpleAdmin - Wordpress Plugin
 * 
 * @author Timo Reith
 * @copyright Copyright (c) 2007-2011 Timo Reith (http://www.wp-amazon-plugin.com)
 * 
 * 
 */

require_once APD_LIB_DIR . 'Apd/Service/Amazon/Request/Abstract.php';

class Apd_Service_Amazon_Request_Rest extends Apd_Service_Amazon_Request_Abstract
{
	/**
     * @var AsaZend_Rest_Client
	 */
    protected $_rest_client;
    
    
    
    /**
     * (non-PHPdoc)
     * @see Asa_Service_Amazon_Request_Abstract::_send()
     */
    public function _send($request_url)
    {		    	
		if ($this->_rest_client === null) {
			$this->_rest_client = new AsaZend_Rest_Client();
		}
		
		$this->_rest_client->getHttpClient()->resetParameters();
		
		
		$url = parse_url($request_url);
		
		// set the rest uri
		$uri = $url['scheme'] . '://' . $url['host'];
		$this->_rest_client->setUri($uri);		
		
		// prepare options and get the response
		parse_str($url['query'], $options);
		return $this->_rest_client->restGet('/onca/xml', $options);
    }
}
?>
