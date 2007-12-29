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


require_once 'source/Tests/Core.php';
require_once 'source/Tests/Logger.php';

/**
 * actual SuiteLoader
 *
 * @category Tests
 * @package  Partycal
 * @author   Lucas S. Bickel <hairmare@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GENERAL PUBLIC LICENSE
 * @link     http://partycal.berlios.de
 */
class Partycal_Test_Loader extends PHPUnit_Runner_StandardTestSuiteLoader
{
}

?>
