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
 * Base class for subscriber extensions.
 *
 * This gets included here for extension programmers convenience only.
 */
require_once 'Subscriber/Sync/UserSubscribe.php';

/**
 * Main Subscriber Class for Google Calendar Data.
 */
class GoogleCalendar_Subscribe_PartyCal extends Subscriber_Sync_UserSubscribe_PartyCal {

	public $client;
	public $cal;

	/** 
	 * Create and authenticate a new Zend Gdata Client and Calendar.
	 *
	 * The parent constructor called below stores config for now. it must be called and will 
	 * setup access to more of partycal in the future.
	 *
	 * You may create a Validator.php file in your app, this is not yet implemented.
	 *
	 * @param Config_PartyCal $config a config object containg the subscribers node in partycal.ini. Content is defined by Extension.
	 */
	public function __construct( Config_PartyCal $config ) {

		parent::__construct( $config );

		$this->makeAuthedClient();

		$this->cal = new Zend_Gdata_Calendar( $this->client );
	}

	/**
	 * Create a new client with auth credentials from config.
	 *
	 * see rest-evdb for a more extensive auth example.
	 */
	public function makeAuthedClient() {

		$this->client = Zend_Gdata_ClientLogin::getHttpClient( $this->config->email ,
								       $this->config->passwd ,
								       'cl' );
	}

	/**
	 * Add a completly new record to a subscribing system.
	 *
	 * I prefer to create xml data with DOMDocument. After stringing together whatever syntax a 
	 * subscriber implements it gets submitted through the respective client.
	 *
	 * If something fails you may rollback (return false) and the event will get added on the 
	 * next tool run.
	 * 
	 * @param Array $item
	 * @return boolean TRUE if record was inserted, FALSE otherwise.
	 */
	public function insertNewRecord( $item ) {

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

		$e->appendChild( $content = $d->createElement('content') );
		$content->setAttribute( 'type' , 'text' );
		$content->appendChild( $d->createCDATASection($item['desc_text'] ) );

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

	/**
	 * adds a new comment to an event.
	 * 
	 * commenting may get used to post updates to events. cool things would be 
	 * sold-out infos or maybe crosslinks to calendars with active threads about 
	 * the same event.
	 */
	public function addRecordComment()
	{
	}

	/**
	 * cancels or withdraws an already posted event.
	 * 
	 * This is the most drastic action for removing an event from a subscriber.
	 * 
	 * If a subscriber doesn't support deleting record the event may be marked canceled 
	 * or a cancel note may be posted to the event through addRecordComment().
	 */
	public function cancelRecord()
	{
	}
}

?>
