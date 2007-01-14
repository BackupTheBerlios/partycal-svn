<?php

require_once 'PHPUnit/Framework.php';

$_ENV['PARTYCAL_ROOT'] = '.';
require_once 'lib/Bootstrap.php';
require_once 'lib/Config.php';
 
class ConfigPartyCalBasicTest extends PHPUnit_Framework_TestCase
{
	public $sut;

	public function setUp()
	{
		$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/config/config/partycal-testlistings.ini';
		$this->sut = new Config_PartyCal( 'partycal' );
	}

	public function tearDown()
	{
		unset( $this->sut );
	}
	
	public function testGetDataReturnsProperArray()
	{
		$this->assertEquals( $this->sut->getData() 
				   , array( 'db_dso' => 'sqlite://test/config/config/test.db' ) );
	}

	public function testGetListingReturnsArrayEmpty()
	{
		$this->assertEquals( $this->sut->getNodeListing( 'empty-listing' ) , array() );
	}

	public function testGetSubscriberListing()
	{
		$this->assertEquals( $this->sut->getSubscriberListing() 
				   , array( 'test1' => 'active' 
				          , 'test2' => 'inactive' 
					  , 'test3' => 'error'
					  )
				   );
	}

	public function testGetProviderListing()
	{
		$this->assertEquals( $this->sut->getProviderListing() 
				   , array( 'test1' => 'active' 
				          , 'test2' => 'inactive' 
					  , 'test3' => 'error'
					  )
				   );
	}
}

?>
