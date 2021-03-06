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
 * @package Core
 * @subpackage Core
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
	 *
	 */
	public function __construct( Config_PartyCal $config ) {

		parent::__construct( $config->getArrayCopy() );
	}

	/**
	 * Return an ArrayIterator by value.
	 *
	 * @param String value to search for
	 * @return ArrayIterator
	 */
	function getIteratorByValue( $value ) {

		$o = new ArrayObject( array_keys( $this->getArrayCopy() , $value ) );
		return $o->getIterator();
	}
}

?>
