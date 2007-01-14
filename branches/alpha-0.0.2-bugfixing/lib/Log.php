<?php
/**
 * Basic Logging for PartyCal.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Log
 * @file
 */

/**
 * Zend Framework Logging.
 */
require_once 'Zend/Log.php';

/**
 * Zend Framework File Logging.
 */
require_once 'Zend/Log/Adapter/File.php';

/**
 * Core Partycal Logging Class.
 *
 * @class
 */
class Log_PartyCal { 

	/**
	 * Constructor for setting up static Logging stuff on the first call.
	 */
	public function __construct() {
		
		static $setupCalled = false;

		if ( !$setupCalled ) {
			Log_PartyCal::registerLoggers();
			$setupCalled = true;
		}
	}

	/**
	 *
	 * @todo make logfile/loggers configurable
	 */
	static function registerLoggers( ) {
		Zend_Log::registerLogger( new Zend_Log_Adapter_File('logs/partycal.log') );
	}

	/**
	 * Logs a "debug" message
	 */
	public function log( $msg ) {
		Zend_Log::log( '[' . date(DATE_W3C) . '] ' . $msg );
	}

	public function error( $msg ) {
		Zend_Log::log( '[' . date(DATE_W3C) . '] ' . $msg, Zend_Log::LEVEL_ERROR );
	}

	public function warn( $msg ) {

		Zend_Log::log( '[' . date(DATE_W3C) . '] ' . $msg, Zend_Log::LEVEL_WARNING );
	}

}

?>
