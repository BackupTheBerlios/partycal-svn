<?php
/**
 * Array of subscribers.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 *
 */
require_once 'ArrayObject/Listing.php';

/**
 *
 */
require_once 'ArrayIterator/Subscriber.php';

/**
 *
 */
class Listing_Subscriber_PartyCal extends ArrayObject_Listing_PartyCal {

	public function getIterator( )
	{
		return new ArrayIterator_Subscriber_PartyCal( $this );
	}
}

?>
