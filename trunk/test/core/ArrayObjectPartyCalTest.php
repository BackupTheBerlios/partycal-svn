<?php

require_once 'PHPUnit/Framework.php';

require_once 'lib/ArrayObject.php';
 
class ArrayObjectPartyCalTest extends PHPUnit_Framework_TestCase
{
	public $sut_classname = 'ArrayObject_PartyCal';
	public function setUp()
	{
		$this->fixtures = array();
		$this->fixtures[0] = new $this->sut_classname ( array ('name' => 'value' ) );
	}

	public function tearDown()
	{
		unset($this->fixtures);
	}

	public function testIsArrayAsPropWorking() 
	{
		$this->assertEquals($this->fixtures[0]->name, 'value');
	}
}

?>
