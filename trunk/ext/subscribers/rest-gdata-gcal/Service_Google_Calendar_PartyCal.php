<?php
/**
 * Google Calendar Integration 
 *
 * proof-of-concept subscriber
 * @todo rework as reference provider implementation
 */

/**
 * Zend Framework Gdata
 */
require_once 'Zend/Gdata.php';
/**
 * Zend Framework Gdata ClientLogin
 */
require_once 'Zend/Gdata/ClientLogin.php';
/**
 * Zend Framework Gdata Calendar
 */
require_once 'Zend/Gdata/Calendar.php';
/**
 * Zend Framework Http Cookie
 */
require_once 'Zend/Http/Cookie.php';
/**
 * Zend Framework Http Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Main Provider Class for Google Calendar Data.
 */
class Service_Google_Calendar_PartyCal {

	public $client;
	public $cal;

	public function __construct( $confkey , $uri ) 
	{
		$this->conf = new Config_PartyCal( $confkey );
		$this->uri = $uri;

		$this->makeAuthedClient();
	}

	public function makeAuthedClient() 
	{
		$this->client = Zend_Gdata_ClientLogin::getHttpClient( $this->conf->email ,
								       $this->conf->passwd ,
								       'cl' );
		$this->cal = new Zend_Gdata_Calendar( $this->client );
	}

	/**
	 *
	 * @todo the xml generation part (hehe) must get changes soon
	 */
	public function createOrUpdate($item)
	{
		$xmlString = '<entry xmlns="http://www.w3.org/2005/Atom"
    xmlns:gd="http://schemas.google.com/g/2005">
  <category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/g/2005#event"/>
  <title type="text">'.$item['event_name'].'</title>
  <link rel="http://schemas.google.com/g/2005#onlineLocation" type="text/html" href="'.htmlspecialchars($item['link']).'"/>
<!--  <link rel="http://schemas.google.com/g/2005#image type="image/jpg" href="http://petzi.ch/images/logo_petzi.jpg"/>-->
  <content type="text">'.$item['shortdesc'].'
  -- 
  this event was posted by the Swiss Party Calendar Synchronizer
  http://partycal.wordpress.com/ 
  </content>
  <author>
    <name>PartyCal</name>
    <email>'.$this->conf->email.'</email>
  </author>
  <gd:where valueString="'.$item['location'].'"></gd:where>
  <gd:when startTime="'.$item['start_ts'].'"
    endTime="'.$item['end_ts'].'"></gd:when>
  <gd:eventStatus value="http://schemas.google.com/g/2005#event.confirmed"/>
  <gd:visibility value="http://schemas.google.com/g/2005#event.public"/> 
  <gd:transparency value="http://schemas.google.com/g/2005#event.transparent"/>
</entry>';
		$this->cal->post( $xmlString , $this->conf->feed );
	}
}

?>
