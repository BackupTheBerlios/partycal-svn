<?php
/**
 * Array Extension for PartyCal.
 */

/**
 * PartyCal Internal ArrayObject Class.
 */
class ArrayObject_PartyCal extends ArrayObject {

	public function __construct( $listing ) {

		parent::__construct( $listing->getData() , 
				     ArrayObject::ARRAY_AS_PROPS );

	}

	/**
	 * dump an array in some style that will get replaced sometimes soon
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
