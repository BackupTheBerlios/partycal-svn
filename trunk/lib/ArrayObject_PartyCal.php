<?php
/**
 * Array Extension for PartyCal.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 * PartyCal Internal ArrayObject Class.
 */
class ArrayObject_PartyCal extends ArrayObject {

	/**
	 * create array object with props set.
	 *
	 * @param $array Array data for populating the array
	 */
	public function __construct( $array ) {

		parent::__construct( $array , 
				     ArrayObject::ARRAY_AS_PROPS );

	}

	/**
	 * string output.
	 *
	 * @return String
	 *
	 * @todo replace this with something sensible
	 */
	public function __toString() {
		$i = $this->getIterator();

		$s = '';
		while($i->valid()) {
			$s .= $i->key()."\n";
			$i->next();
		}

		return $s;
	}
}

?>
