<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 * @subpackage subscriber
 * @file
 */

/**
 * Basic PartyCal Logging.
 */
require_once 'Log.php';

/**
 * Logging for Subscriber Messages.
 *
 * @class
 */
class Log_Subscriber_PartyCal extends Log_PartyCal { 

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * a new record was posted.
	 *
	 * @param String $subscriber
	 * @param Integer $event_id
	 * @return void
	 *
	 * @todo spruce up log messages with more details
	 */
	public function posted( $subscriber , $event_id ) {
		$this->log( '[Subscriber] The event ' . $event_id . ' was added to the subscriber "' . $subscriber . '".' );
	}

	/**
	 *
	 *
	 * @return void
	 */
	public function problem() {
	}

}

?>
