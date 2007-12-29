<?php
/**
 * contains tests for the logging infrastructure.
 *
 * PHP version 5
 * 
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */

require_once 'source/Logger.php';

/**
 * logger tests.
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */
class Partycal_Test_Logger extends PHPUnit_Framework_TestCase
{
    function testNewLogger()
    {
        $this->assertType('Partycal_Logger', new Partycal_Logger());
    }
}
?>
