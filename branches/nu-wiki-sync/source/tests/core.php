<?php
/**
 * You may find some tests looking for php features in here.
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */


/**
 * actual core tests.
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 * @class 
 */
class Partycal_Test_Core extends PHPUnit_Framework_TestCase
{

    /**
     * make shure eveything is go
     *
     * @return void
     */
    function testTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * check if sqlite is installed
     *
     * @return void
     */
    function testSqliteInstalled()
    {
        $this->assertTrue(function_exists('sqlite_open'));
    }

    /**
     * look for needed stream wrappers
     *
     * @return void
     */
    function testStreamWrapperHttpExists()
    {
        $this->assertTrue(in_array('http', stream_get_wrappers()));
        $this->assertTrue(in_array('https', stream_get_wrappers()));
    }

}

?>
