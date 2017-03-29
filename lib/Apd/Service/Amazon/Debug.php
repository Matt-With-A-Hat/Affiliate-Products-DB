<?php
/**
 * AmazonSimpleAdmin - Wordpress Plugin
 * 
 * @author Timo Reith
 * @copyright Copyright (c) 2007-2011 Timo Reith (http://www.wp-amazon-plugin.com)
 * 
 * 
 */

require_once APD_LIB_DIR . 'Apd/Service/Amazon/Interface.php';
require_once APD_LIB_DIR . 'Apd/Service/Amazon/Exception.php';
require_once APD_LIB_DIR . 'Apd/Debugger.php';

class Apd_Service_Amazon_Debug implements Apd_Service_Amazon_Interface
{
    /**
     * Apd_Service_Amazon object
     * @var Apd_Service_Amazon
     */
    protected $_apd;

    /**
     * @var Apd_Debugger
     */
    protected $_debugger;
    

    /**
     * @param $apd
     */
    function __construct($apd)
    {
        $this->_apd = $apd;

        try {
            // init the debugger
            $this->_debugger = Apd_Debugger::factory();
        } catch (Exception $e) {
            
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Apd_Service_Amazon_Interface::itemLookup()
     */
    public function itemLookup($asin, array $options=array()) 
    {
        $result = $this->_apd->itemLookup($asin, $options);

        if ($this->_debugger instanceof Apd_Debugger) {
            $debug = 'Action: '. PHP_EOL . __METHOD__ . PHP_EOL . PHP_EOL;
            $debug .= 'ASIN: '. PHP_EOL . $asin . PHP_EOL . PHP_EOL;
            $debug .= 'Request: '. PHP_EOL . $this->_apd->getRequest()->getRequest() . PHP_EOL . PHP_EOL;
            $debug .= 'Response: ' . PHP_EOL . $this->_apd->getRequest()->getResponse()->getBody();
    
            $this->_debugger->write($debug);
        }

        if ($result != false) {
            $this->_debugResponse($this->_apd->getRequest()->getResponse()->getBody());
        }
        
        return $result;
    }    

    /**
     * (non-PHPdoc)
     * @see Apd_Service_Amazon_Interface::itemSearch()
     */
    public function itemSearch(array $options)
    {
        $result = $this->_apd->itemSearch($options);

        if ($this->_debugger instanceof Apd_Debugger) {
            $debug = 'Action: '. PHP_EOL . __METHOD__ . PHP_EOL . PHP_EOL;
            $debug .= 'Request: '. PHP_EOL . $this->_apd->getRequest()->getRequest() . PHP_EOL . PHP_EOL;
            $debug .= 'Response: ' . PHP_EOL . $this->_apd->getRequest()->getResponse()->getBody();
    
            $this->_debugger->write($debug);
        }
        
        if ($result != false) {
            $this->_debugResponse($this->_apd->getRequest()->getResponse()->getBody());
        }
        
        return $result;
    }    
    
    /**
     * (non-PHPdoc)
     * @see Apd_Service_Amazon_Interface::testConnection()
     */
    public function testConnection()
    {
        $exception = null;
        
        try {
            $result = $this->_apd->itemSearch(array('SearchIndex' => 'Books', 'Keywords' => 'php'));
        } catch (Exception $exception) {
            // catch the exception and throw it after writing the debugging information
        }

        if ($this->_apd->getRequest()->getResponse()->isError() && $this->_debugger instanceof Apd_Debugger) {
            $debug = 'Action: '. PHP_EOL . 'Test the connection to Amazon API by sending a search request' . PHP_EOL . PHP_EOL;
            $debug .= 'Request: '. PHP_EOL . $this->_apd->getRequest()->getRequest() . PHP_EOL . PHP_EOL;
            $debug .= 'Response: ' . PHP_EOL . $this->_apd->getRequest()->getResponse()->getBody();
    
            $this->_debugger->write($debug);
        }
        
        if ($result != false) {
            $this->_debugResponse($this->_apd->getRequest()->getResponse()->getBody());
        }
        
        if ($exception instanceof Exception) {
            // now throw the catched exception
            throw $exception;
        }
        
        return $result;       
    }    
    
    
    protected function _debugResponse($response)
    {
        $path = getenv('APD_DEBUG_PATH');
        
        if ( empty($path) || !is_dir($path) || !is_writable($path) ) {
            return false;
        }
        
        file_put_contents($path . date('YmdHis') . '.xml', substr(var_export($response, true), 1, -1));
    }
}


?>
