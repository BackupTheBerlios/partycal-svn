<?php
/**
 * You may find some tests looking for php features in here.
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  GPL
 * @link     http://partycal.berlios.de
 */


/**
 * actual core tests.
 *
 * @class 
 */
class Partycal_Test_Core extends PHPUnit_Framework_TestCase 
{

    function testTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * check if sqlite is installed
     */
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
