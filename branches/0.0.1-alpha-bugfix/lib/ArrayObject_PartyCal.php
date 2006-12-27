<?php

class ArrayObject_PartyCal extends ArrayObject {

	public function __construct( $listing ) {

		parent::__construct( $listing->getData() , 
				     ArrayObject::ARRAY_AS_PROPS );

	}

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
