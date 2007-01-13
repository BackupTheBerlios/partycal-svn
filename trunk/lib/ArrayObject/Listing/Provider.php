<?php
/**
 * Provider Array Object.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Provider
 */

/**
 *
 */
require_once 'ArrayObject/Listing.php';

/**
 *
 */
require_once 'ArrayIterator/Provider.php';

/**
 *
 */
class Listing_Provider_PartyCal extends ArrayObject_Listing_PartyCal { 

	public function getIterator( )
	{
		return new ArrayIterator_Provider_PartyCal( $this );
	}
}

?>
