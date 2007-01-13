<?php
/**
 * Logging for Eventful.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Subscriber
 * @subpackage rest-evdb
 * @file
 */

/**
 * Logging for Eventful.
 *
 * @class
 */
class Log_Eventful_PartyCal extends Log_Subscriber_PartyCal { 

	public function missingVenue( $venue_name , $city_name , $venue_link )
	{
		$this->problem( 'Missing Venue "'. $venue_name 
			      . '" in ' . $city_name 
			      . ' on Eventful (' . $venue_link . ')' );
	}
}

?>
