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
require_once 'ArrayIterator.php';

/**
 *
 */
require_once 'Subscriber.php';

/**
 *
 */
class ArrayIterator_Subscriber_PartyCal extends ArrayIterator_PartyCal { 

	public function current()
	{
		return new Subscriber_PartyCal( $this->key() );
	}
}

?>
