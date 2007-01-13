<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Provider
 */

/**
 *
 */
require_once 'ArrayIterator.php';

/**
 *
 */
require_once 'Provider.php';

/**
 *
 */
class ArrayIterator_Provider_PartyCal extends ArrayIterator_PartyCal { 

	public function current()
	{
		return new Provider_PartyCal( $this->key() );
	}

}

?>
