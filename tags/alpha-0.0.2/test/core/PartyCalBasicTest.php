<?php
/**
 * The tests in here basically make sure that everything is go for further testing.
 */

require_once 'PHPUnit/Extensions/OutputTestCase.php';

class PartyCalBasicTest extends PHPUnit_Extensions_OutputTestCase
{
    public function testTestFrameworkIsUpAndRunning()
    {
        $this->assertTrue(true);
    }
 
    public function testPhpBootStrapScriptWithFixtureConfig()
    {
		try {
			$_ENV['PARTYCAL_ROOT'] = '.';
			$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/test/core/config/partycal-empty.ini';

			require_once 'bin/partycal';

		} catch( Zend_Config_Exception $e ) {
			return;
		}

		$this->fail('Empty config not generating Error');
    }
}
?>
