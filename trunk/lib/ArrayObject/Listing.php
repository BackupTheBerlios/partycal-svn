<?php
/**
 * Listing Array Object.
 *
 * Listing are formatted like so:
 * <pre>
 * [name-listing]
 * ; active element
 * elementname1=active
 * ; element is deactivated
 * elementname2=inactive
 * ; element is commented out, hidden from system
 * ;elementname3=active
 * </pre>
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 *
 */
require_once 'ArrayObject.php';

/**
 *
 */
class ArrayObject_Listing_PartyCal extends ArrayObject_PartyCal {

	/**
	 * .
	 *
	 * uses data from ini.
	 *
	 * @todo implement
	 */
	public function getActiveSubscriberIterator()
	{
	}

	/**
	 * .
	 *
	 * uses data from ini.
	 *
	 * @todo implement
	 */
	public function getInactiveSubscriberIterator()
	{
	}

}

?>
