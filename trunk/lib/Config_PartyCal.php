<?php
/**
 * Basic Configuration Handling
 */

/**
 * Zend Framework Config
 */
require_once 'Zend/Config.php';

/**
 * Zend Framework Config Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * Configuration Handler for PartyCal
 */
class Config_PartyCal extends Zend_Config {

	public function __construct( $node )
	{
		parent::__construct( new Zend_Config_Ini( $_ENV['PARTYCAL_CONFIG'], $node ) );
	}

	static function getProviderListing() {
		return new Config_PartyCal('provider-listing');
	}

	static function getSubscriberListing() {
		return new Config_PartyCal('subscriber-listing');
	}

	public function getData()
	{
		return (array) $this->_data;
	}
}

?>
