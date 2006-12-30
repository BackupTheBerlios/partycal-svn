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

/**
 *
 */
class Provider_PartyCal { 
	
	public function __construct( $name )
	{
		$this->config = new Config_PartyCal( 'provider-'.$name );
		$this->name = $name;
	}

	public function __get( $name ) 
	{
		if ( isset( $this->$name ) ) {

			return $this->$name;

		} else if ( isset( $this->config->$name ) ) {

			return $this->config->$name;
		}
	}
}

?>
