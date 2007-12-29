<?php

class test_core extends PHPUnit_Framework_TestCase {

	// this tests is quite cheap, it makes shure the whole harness works though.
	function testTrue()
	{
		$this->assertTrue(true);
	}

	function testSqliteInstalled()
	{
		$this->assertTrue(function_exists('sqlite_open'));
	}

}

?>
