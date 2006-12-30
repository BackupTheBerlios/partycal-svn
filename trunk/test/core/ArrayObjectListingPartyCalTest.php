<?php

require_once 'PHPUnit/Framework.php';

require_once 'test/core/ArrayObjectPartyCalTest.php';
require_once 'lib/ArrayObject/Listing.php';
 
class ArrayObjectListingPartyCalTest extends ArrayObjectPartyCalTest
{
	public $sut_classname = 'ArrayObject_Listing_PartyCal';
	public function setUp()
	{
		$this->fixtures = array();
		$this->fixtures[0] = new $this->sut_classname( array ('name' => 'value' ) );
		$this->fixtures[1] = new $this->sut_classname( array(
			'activetest' => 'active',
			'inactivetest' => 'inactive'
		) );
	}

	public function testGetIteratorByValueActive()
	{
		$i = $this->fixtures[1]->getIteratorByValue( 'active' );
		$this->assertType( 'ArrayIterator' , $i );
		while ( $i->valid() ) {
			$this->assertEquals( 'activetest' , $i->current() );
			$i->next();
		}

		$j = $this->fixtures[1]->getIteratorByValue( 'inactive' );
		$this->assertType( 'ArrayIterator' , $j );
		while ( $i->valid() ) {
			$this->assertEquals( 'inactivetest' , $j->current() );
			$i->next();
		}
	}
}

?>
