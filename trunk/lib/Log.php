<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 *
 */
require_once 'Zend/Log.php';

/**
 *
 */
require_once 'Zend/Log/Adapter/File.php';

/**
 *
 */
class Log_PartyCal { 

	public function __construct() {
		
		static $setupCalled = false;

		if ( !$setupCalled ) {
			Log_PartyCal::registerLoggers();
			$setupCalled = true;
		}
	}

	static function registerLoggers() {
		Zend_Log::registerLogger(new Zend_Log_Adapter_File('logs/partycal.log'));
	}

	public function error() {
	}

	public function warn( $msg ) {

		Zend_Log::log($msg, Zend_Log::LEVEL_WARNING);
	}

}

?>
