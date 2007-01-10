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
class Log_Eventful_PartyCal extends Log_PartyCal { 

	public function missingVenue( $venue_name , $city_name )
	{
		$this->warn( 'Missing Venue "'. $venue_name 
			   . '" in ' . $city_name 
			   . ' on Eventful (' . $venue_link . ')' );
	}
}

?>
