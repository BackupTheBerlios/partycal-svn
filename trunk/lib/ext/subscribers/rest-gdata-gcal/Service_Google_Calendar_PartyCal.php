<?php
/**
 * Google Calendar Integration 
 *
 * proof-of-concept subscriber
 * @todo rework as reference provider implementation
 *
 * @package Subscriber
 * @subpackage GoogleCalendar
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
 *
 */
require_once 'Subscriber/Sync/UserSubscribe.php';

/**
 * Main Subscriber Class for Google Calendar Data.
 */
class GoogleCalendar_Subscribe_PartyCal extends Subscriber_Sync_UserSubscribe_PartyCal {

	public $client;
	public $cal;

	public function __construct( $config ) 
	{
		parent::__construct( $config );

		$this->uri = $this->config->feed;

		$this->makeAuthedClient();
	}

	public function makeAuthedClient() 
	{
		$this->client = Zend_Gdata_ClientLogin::getHttpClient( $this->config->email ,
								       $this->config->passwd ,
								       'cl' );
		$this->cal = new Zend_Gdata_Calendar( $this->client );
	}

	/**
	 *
	 * 
	 *
	 * @todo remove partycal specific code and but it somwhere generic in the loading phase
	 */
	public function insertNewRecord( $item )
	{
		$d = new DOMDocument('1.0');

		$d->appendChild($e = $d->createElementNS('http://www.w3.org/2005/Atom' , 'entry'));
		$e->setAttribute('xmlns:gd', 'http://schemas.google.com/g/2005');

		$e->appendChild($c = $d->createElement('category'));
		$c->setAttribute('scheme' , 'http://schemas.google.com/g/2005#kind');
		$c->setAttribute('term' , 'http://schemas.google.com/g/2005#event');

		$e->appendChild($t = $d->createElement('title' , $item['event_name']));
		$t->setAttribute('type' , 'text');

		$e->appendChild($l = $d->createElement('link'));
		$l->setAttribute('rel' , 'http://schemas.google.com/g/2005#onlineLocation');
		$l->setAttribute('type' , 'text/html');
		$l->setAttribute('href' , $item['link']);

		$e->appendChild($l = $d->createElement('link'));
		$l->setAttribute('rel' , 'http://schemas.google.com/g/2005#image');
		$l->setAttribute('type' , 'image/jpg');
		$l->setAttribute('href' , 'http://petzi.ch/images/logo_petzi.jpg');

		$s  = $item['shortdesc']."\n";
		$s .= '-- '."\n";
		$s .= 'this event was posted by the Swiss Party Calendar Synchronizer'."\n";
		$s .= 'http://partycal.wordpress.com/'."\n";

		$e->appendChild($content = $d->createElement('content' , $s));
		$content->setAttribute('type' , 'text/html');

		$e->appendChild($a = $d->createElement('author'));
		$a->appendChild( $d->createElement('name','PartyCal'));
		$a->appendChild( $d->createElement('email',$this->config->email));

		$e->appendChild($loc = $d->createElement('gd:where'));
		$loc->setAttribute('valueString',$item['location']);

		$e->appendChild($time = $d->createElement('gd:when'));
		$time->setAttribute('startTime',$item['start_ts']);
		$time->setAttribute('endTime',$item['end_ts']);

		$e->appendChild($status = $d->createElement('gd:eventStatus'));
		$status->setAttribute('value' , 'http://schemas.google.com/g/2005#event.confirmed');
		$e->appendChild($v = $d->createElement('gd:visibility'));
		$v->setAttribute('value' , 'http://schemas.google.com/g/2005#event.public');
		$e->appendChild($tr = $d->createElement('gd:transparency'));
		$tr->setAttribute('value' , 'http://schemas.google.com/g/2005#event.public');

		try {
			$this->cal->post( $d->saveXML() , $this->config->feed );
		} catch (Zend_Gdata_Exception $e) {
			return false;
		}
		return true;
	}

	public function addRecordComment()
	{
	}

	public function cancelRecord()
	{
	}
}

?>
