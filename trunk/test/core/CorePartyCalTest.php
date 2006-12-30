<?php

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once 'Core.php';

class CorePartyCalTest extends PHPUnit_Extensions_OutputTestCase
{

	public function testHelpMessageOutputWorks() {
		 $this->expectOutputString(
		 	'Usage: test [command] [subcommand]'."\n\n"
		      . 'help: output global help (this) or subcommand help');

		$argv = array();
		$argv[0] = 'test';
		$argv[1] = 'help';

		$listings['providers']   = $this;
		$listings['subscribers'] = $this;

		$o = new Core_PartyCal( $argv , $this , $listings , $this );
		$o->main();

		$this->assertTrue($this->configProviderListingCalled);
	}

	/**
	 * Shunts for Config.
	 */
	public $configProviderListingCalled;
	public function getProviderListing() {
		$this->providerListingCalled = true;
	}

	/**
	 * Shunts for ArrayObject
	 */
	public function shuntsome() {}
}
?>
