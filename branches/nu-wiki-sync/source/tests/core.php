<?php
/**
 * You may find some tests looking for php features in here.
 */


/**
 * actual core tests.
 *
 */
class Partycal_Test_Core extends PHPUnit_Framework_TestCase 
{

	// this tests is quite cheap, it makes shure the whole harness works though.
	function testTrue()
	{
		$this->assertTrue(true);
	}

	function testSqliteInstalled()
	{
		$this->assertTrue(function_exists('sqlite_open'));
	}

	function testStreamWrapperHttpExists()
	{
		$this->assertTrue(in_array('http', stream_get_wrappers()));
		$this->assertTrue(in_array('https', stream_get_wrappers()));
	}

}

?>
