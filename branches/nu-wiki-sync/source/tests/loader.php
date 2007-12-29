<?php
/**
 * A TestSuiteLoader Implementation for phpunit.
 *
 * PHP version 5
 * 
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */

require_once 'source/tests/core.php';
require_once 'source/tests/logger.php';

/**
 * actual SuiteLoader
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */
class Partycal_Test_Loader
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Partycal');
     
        $suite->addTestSuite('Partycal_Test_Core');
        $suite->addTestSuite('Partycal_Test_Logger');
      
        return $suite;
    }
}

?>
