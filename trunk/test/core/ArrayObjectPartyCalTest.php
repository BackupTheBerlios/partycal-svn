<?php

require_once 'PHPUnit/Framework.php';

require_once 'lib/ArrayObject.php';
 
class ArrayObjectPartyCalTest extends PHPUnit_Framework_TestCase
{
	public function testIsArrayAsPropWorking() 
	{
		$o = new ArrayObject_PartyCal( array ('name' => 'value' ) );
		$this->assertEquals($o->name, 'value');
	}
}

?>
