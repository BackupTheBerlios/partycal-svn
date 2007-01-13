<?php

require_once 'PHPUnit/Framework.php';

$_ENV['PARTYCAL_ROOT'] = '.';
require_once 'lib/Bootstrap.php';
require_once 'lib/Config.php';
 
class ConfigValidatorPartyCalBasicTest extends PHPUnit_Framework_TestCase
{
	public $sut;

	public function setUp()
	{
	}

	public function tearDown()
	{
	}
	
	public function testValidatorCatchesErrorsInListings()
	{
		try {
			$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/config/config/partycal-testlistings.ini';
			new Config_PartyCal( 'partycal' );
		} catch (Config_Validator_Exception $e) {
			$this->assertEquals($e->getMessage(), 
				'[provider-listing]test3 must be active or inactive (error given)');
			return;
		}
		$this->fail('Errors in Listings not caught Properly');
	}

	public function testValidatorCatchesMissingNodes()
	{
		try {
			$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/config/config/partycal-missingnodeproviderlisting.ini';
			new Config_PartyCal( 'partycal' );
		} catch (Config_Validator_Exception $e) {
			$this->assertEquals($e->getMessage(),
				'Please define a [provider-listing] node in partycal.ini (see partycal.ini-example for info)');
			return;
		}
		$this->fail('Missing Provider-Listing not caught.');
	}

	public function testValidatorCatchesOphanedObjectsAndThrowsWarning()
	{
		try {
			$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/config/config/partycal-orphanedprovider.ini';
			new Config_PartyCal( 'partycal' );
		} catch (Config_Validator_Exception $e) {
			$this->assertEquals($e->getMessage(),
				'An orphan provider (orphan) has been found (add a [provider-orphan] section to partycal.ini])');
		}
		$this->fail('Orphan Provider not Caught');
	}

	public function testValidatorCatchesNonExistingDatabase()
	{
		try {
			$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/config/config/partycal-inexistantdb.ini';
			new Config_PartyCal( 'partycal' );
		} catch (Config_Validator_Exception $e) {
			$this->assertEquals($e->getMessage(),
				'A new SQLite DB has been created, you need to run partycal-sync install to populate it with data.');
		}
		$this->fail('Missing DB not Caught');
	}
}

?>
