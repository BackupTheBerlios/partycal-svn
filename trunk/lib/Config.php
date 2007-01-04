<?php
/**
 * Basic Configuration Handling
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Config
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
 * PartyCal Config Validator
 */
require_once 'Config/Validator.php';

/**
 * Generate Exceptions on Error.
 */
define( 'CONFIG_PARTYCAL_MODE_EXCEPTIONS' , 1 );

/**
 * Write Errors to stderr.
 */
define( 'CONFIG_PARTYCAL_MODE_STDERR' , 2 );

/**
 * Configuration Handler for PartyCal
 *
 * @class
 */
class Config_PartyCal extends Zend_Config {

	/**
	 * Open and validate a config file.
	 *
	 * uses CONFIG_PARTYCAL_MODE_EXCEPTIONS and CONFIG_PARTYCAL_MODE_STDERR to 
	 * throw exceptions or output errors
	 *
	 * @throws Config_Exception_PartyCal
	 *
	 * @param $node String needed node
	 * @param $mode Long mode for error handling, Exceptions are default
	 *
	 * @todo implement Exception/sdterr handling
	 */
	public function __construct( $node , $mode = NULL)
	{
		parent::__construct( new Zend_Config_Ini( $_ENV['PARTYCAL_CONFIG'] , $node ) );

		$config_validator = new Config_Validator_PartyCal ( $_ENV['PARTYCAL_CONFIG'] , CONFIG_VALIDATOR_PARTYCAL_MODE_SCAN );
		try {
			$config_validator->validate();
		} catch (Exception $e) {
			//check mode
			//write to stderr
		}
	}

	/**
	 * provider listing as an array
	 *
	 * @return Array subscriber listing
	 */
	static function getProviderListing() {
		return Config_PartyCal::getNodeListing( 'provider-listing' );
	}

	/**
	 * provider listing as an array
	 *
	 * @return Array subscriber listing
	 */
	static function getSubscriberListing() {
		return Config_PartyCal::getNodeListing( 'subscriber-listing' );
	}

	/**
	 * Return a ini node as an Array.
	 *
	 * @param $node String node name
	 * @return Array
	 */
	static function getNodeListing( $node )
	{
		$n = new Config_PartyCal( $node );

		return $n->getData();
	}

	/**
	 * expose internal _data Array
	 * 
	 * @return Array (array) cast of $this->_data
	 *
	 * @todo refactor callers, remove function
	 * @deprecated pass around Config Objects instead of using this.
	 */
	public function getData()
	{
		return (array) $this->_data;
	}
}

?>
