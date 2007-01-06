<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 *
 */

/**
 *
 */
require_once 'Config.php';

/**
 *
 */
class Subscriber_PartyCal { 

	public function __construct( $name ) {

		$this->name = $name;
		$this->config = new Config_PartyCal( 'subscriber-' . $this->name );

		$classname = $this->config->classname;
		require_once $this->config->filename;

		if ( is_callable ( array ( $classname , 'singleton' ) ) ) {
			$this->implementor = call_user_func( array ( $classname , 'singleton' ) , $this->config );
		} else {
			$this->implementor = new $classname( $this->config );
		}

	}

	public function delegate( $functionname , $a = NULL ) {

		return $this->implementor->$functionname( $a );
		
	}
}

?>
