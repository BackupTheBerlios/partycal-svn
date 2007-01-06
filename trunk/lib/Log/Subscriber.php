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
require_once 'Log.php';

/**
 *
 */


/**
 *
 */
class Log_Subscriber_PartyCal extends Log_PartyCal { 

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * a new record was posted.
	 */
	public function posted( $subscriber , $event_id ) {
	}

}

?>
