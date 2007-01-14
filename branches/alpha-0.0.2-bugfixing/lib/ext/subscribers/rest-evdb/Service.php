<?php
/**
 * eventful.com integration.
 * 
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Subscriber
 * @subpackage rest-evdb
 * @file
 */

/**
 * Zend Framework Rest Services.
 */
require_once 'Zend/Service/Rest.php';

/**
 * Logging for Eventful.
 */
require_once dirname(__FILE__) . '/Log.php';

/**
 * Main eventful Sync Class.
 *
 * @class
 */
class Service_Eventful_PartyCal {

	/**
	 * Setup Eventful Client and load base data.
	 *
	 * @param Config_PartyCal $config
	 * @return void
	 */
	public function __construct( Config_PartyCal $config ) {

		//parent::__construct( $config );

		$this->config = $config;
		$this->uri = $this->config->feed;

		$this->logger = new Log_Eventful_PartyCal();

		$this->makeAuthedClient();
		$this->loadCategoryList();
	}

	/**
	 * login to eventful 
	 *
	 * $this->login_string may be used as arg to anything needing digested auth from here on.
	 *
	 * @return void
	 */
	public function makeAuthedClient() {

		$this->login_string = 'app_key=' 
		                    . $this->config->api_key;

		$this->rest = new Zend_Service_Rest();

		$this->rest->setURI( $this->config->api_url );
		$r = $this->rest->restGet( '/rest/users/login' , $this->login_string );

		if ( $r->isSuccessful() ) {

			$xml = new SimpleXMLElement($r->getBody());
			$nonce = $xml->nonce;

			$response = strtolower( md5( $nonce . ':' . strtolower( md5($this->config->passwd) ) ) );

			$this->login_string .= '&user=' . $this->config->user;

			$auth_req = $this->login_string
			          . '&nonce=' . $nonce
			          . '&response=' . $response;

			$s = $this->rest->restGet( '/rest/users/login' , $auth_req );

			if ($s->isSuccessful()) {

				$xml = new SimpleXMLElement($s->getBody());
				$user_key = $xml->user_key;

				$this->login_string .= '&user_key='.$user_key;
			}
		}
	}

	/**
	 * Loads a list of categories for events.
	 *
	 * as i havent quite gotten how my events get added to cats or not im waiting with implementing this.
	 *
	 * @todo implement
	 */
	public function loadCategoryList() {

		//$s = $this->rest->restGet( '/rest/categories/list' , $this->login_string );
		//var_dump($s->getBody());
		return;
	}

	/**
	 * 
	 * 
	 * @return Integer EVDB Id.
	 *
	 * @todo call performerExists
	 */
	public function insertNewRecord( $data ) {

		if ( !$this->venueExists( $data ) ) {
			$this->logger->missingVenue( $data['venue_name'] , $data['city_name'] , $data['venue_link'] );
			return false;
		}

		if ( !$this->performerExists( $data ) ) {
			$this->logger->missingPerformer();
			return false;
		}

		if ( $evdb_id = $this->insertEvent( $data ) ) {

			$this->insertCategory( $evdb_id , $data );
			$this->insertEventLinks( $evdb_id , $data );
			$this->insertTags( $evdb_id , $data );
			$this->insertImage( $evdb_id , $data );
			$this->reindexEvent( $evdb_id );
			return true;
		}
	}

	/**
	 *
	 */
	public function venueExists( &$data ) {

		static $missing_venue_cache = array();

		if ( $missing_venue_cache[ $data['city_name'] ][ $data['venue_name'] ] == true ) {
			return false;
		}

		$rq = $this->login_string
		    . '&keywords=' . urlencode($data['venue_name'])
		    . '&location=' . urlencode($data['city_name']);

		$s = $this->rest->restGet( '/rest/venues/search' , $rq );

		if ($s->isSuccessful()) {

			$xml = new SimpleXMLElement($s->getBody());

			if ( $xml->total_items == 1 ) {

				$venue_id = $xml->venues->venue['id'];
				$data['venue_id'] = $venue_id;

				return true;
			}
		}

		$missing_venue_cache[ $data['city_name'] ][ $data['venue_name'] ] = true;

		return false;
	}

	/**
	 * 
	 * @todo implement when better info from evenful is available (event - performer mapping)
	 */
	public function performerExists() {
		return true;
	}

	/**
	 *
	 * @param Array $data
	 * @return Integer New EVDB Id or False on error.
	 *
	 * @todo refractor hardcoded category_id
	 */
	public function insertEvent( $data ) {

		$rq = $this->login_string
		    . '&title=' . urlencode($data['event_name'])
		    . '&start_time=' . $data['start_ts']
		    . '&stop_time' . $data['end_ts']
		    . '&tz_olson_path=Europe/Zurich'
		    . '&all_day=0'
		    . '&privacy=' . $this->config->post_privacy
		    . '&description=' . urlencode( $data['desc_html'] )
		    . '&tags=' . $data['tags']
		    . '&free=' . $data['free']
		    . '&price=' . urlencode( $data['cost_text'] )
		    . '&venue_id=' . $data['venue_id']
		    . '&category_id=music';

		$s = $this->rest->restGet( '/rest/events/new' , $rq );

		if ( $s->isSuccessful() ) {
			$xml = new SimpleXMLElement( $s->getBody() );
			if ( !$this->checkStatus( $xml ) ) return false;
			return $xml->id;
		} else {
			return false;
		}
	}

	/**
	 *
	 *
	 * @todo implement bug reporting
	 */
	public function checkStatus( $xml ) {
		return $xml['status'] == 'ok';
	}

	public function insertTags( $evdb_id , $data ) {

		$tags = '"Switzerland" "Music" "Party"';

		if ( !empty( $data['style_tags'] ) ) {
			$tags .= ' "' . implode( '" "' , explode( ', ', $data['style_tags'] ) ) .'"';
		}

		$rq = $this->login_string
		    . '&id=' . $evdb_id
		    . '&tags=' . urlencode($tags);

		$s = $this->rest->restGet( '/rest/events/tags/new' , $rq );
		if ( $s->isSuccessful() ) {
			return $this->checkStatus( new SimpleXMLElement( $s->getBody() ) );
		} else {
			return false;
		}
	}
	
	public function insertEventLinks( $evdb_id , $data )
	{
		$tix = array();
		$tix['link'] = urlencode($data['link']);
		$tix['description'] = 'Tickets+and+Infos';
		$tix['type'] = 6;
		$this->insertEventLink( $evdb_id , $tix);

		$partycal = array();
		$partycal['link'] = urlencode('http://partycal.wordpress.com');
		$partycal['description'] = 'The+Swiss+Party+Calendar+Synchronizer';
		$partycal['type'] = 16; // Other
		$this->insertEventLink( $evdb_id , $partycal );
	}

	public function insertEventLink( $evdb_id , $data )
	{
	/* Types:
		1	Info
		17	Official Site
		6	Tickets
		19	Website
		16	Other
	*/
		$rq = $this->login_string
		    . '&id=' . $evdb_id 
		    . '&link=' . $data['link']
		    . '&description=' . $data['description']
		    . '&link_type_id=' . $data['type'];
		
		$s = $this->rest->restGet( '/rest/events/links/new' , $rq );

		if ($s->isSuccessful()) {
			return $this->checkStatus( new SimpleXMLElement( $s->getBody() ) );
		}
		return false;
	}

	public function insertCategory( $evdb_id , $data ) {
		
		if ( empty( $data['category_name'] ) ) {
			$data['category_name'] = 'music';
		}

		$rq = $this->login_string
		    . '&id=' . $evdb_id
		    . '&category_id=' . $data['category_name'];

		$s = $this->rest->restGet( '/rest/events/categories/add' , $rq );

		if ($s->isSuccessful()) {
			return $this->checkStatus( new SimpleXMLElement( $s->getBody() ) );
		}
		return false;
	}

	/**
	 *
	 * @todo implement provider_image
	 */
	public function insertImage( $evdb_id , $data ) {
		return true;
		if ( empty( $data['provider_image'] ) ) {
			$data['provider_image'] = 'http://petzi.ch/images/logo_petzi.jpg';
			$data['provider_image_caption'] = 'petzi.ch';
		}

		$rq = $this->login_string
		    . '&image_url=' . $data['provider_image']
		    . '&caption=' . $data['provider_image_caption'];

		$s = $this->rest->restGet( '/rest/images/new' , $rq );
		if ($s->isSuccessful()) {
			$xml = new SimpleXMLElement($s->getBody());

			$image_id = $xml->id;

			$rq = $this->login_string
			    . '&id=' . $evdb_id
			    . '&image_id=' . $image_id;

			$s = $this->rest->restGet( '/rest/events/images/add' , $rq );

			return $this->checkStatus( new SimpleXMLElement( $s->getBody() ) );
		}
		return false;
	}

	/**
	 * @todo implement calendars (granularity?, needed?)
	 */
	public function addToCalendars( $evdb_id , $data ) {
		//explode(',', $this->config['calendars'])
	}

	public function reindexEvent( $evdb_id ) {

		$rq = $this->login_string
		    . '&id=' . $evdb_id;

		$s = $this->rest->restGet( '/rest/events/reindex' , $rq );

		if ($s->isSuccessful()) {
			return $this->checkStatus( new SimpleXMLElement( $s->getBody() ) );
		}
		return false;
	}
}
?>
