<?php
/**
 * eventful.com integration.
 * 
 * work in progress, veeeeeeery beta.
 */

/**
 * Zend Framework Rest Services.
 */
require_once 'Zend/Service/Rest.php';

/**
 *
 */
require_once dirname(__FILE__) . '/Log.php';

/**
 * Main eventful class
 */
class Service_Eventful_PartyCal {

	public function __construct( $config ) {

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
	 * $this->login_string may be used as args to anything needing digested auth from here on
	 */
	public function makeAuthedClient() {

		$this->login_string = 'app_key=' 
		                    . $this->config->api_key;

		$this->rest = new Zend_Service_Rest();

		$this->rest->setURI( 'http://api.evdb.com' );
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

	public function loadCategoryList() {

		//$s = $this->rest->restGet( '/rest/categories/list' , $this->login_string );
		//var_dump($s->getBody());
		return;
	}

	/**
	 * @todo call performerExists
	 */
	public function insertNewRecord( $data ) {

		if ( !$this->venueExists( $data ) ) {

			return false;

		} else {
			
			return $this->insertEvent( $data );
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

		$this->logger->missingVenue( $data['venue_name'] , $data['city_name'] );
		$missing_venue_cache[ $data['city_name'] ][ $data['venue_name'] ] = true;

		return false;
	}

	/**
	 * 
	 * @todo implement when better info from evenful is available (event - performer mapping)
	 */
	public function performerExists()
	{
		return true;
	}

	/**
	 *
	 *
	 */
	public function insertEvent( $data ) {

		$rq = $this->login_string
		    . '&title=' . urlencode($data['event_name'])
		    . '&start_time=' . $data['start_ts']
		    . '&stop_time' . $data['end_ts']
		    . '&tz_olson_path=Europe/Zurich'
		    . '&all_day=0'
		    . '&privacy=2' //private
		    . '&description=' . urlencode( $data['desc_html'] )
		    . '&tags=' . $data['tags']
		    . '&free=0' //change to read from feed
		    . '&price=' . $data['price']
		    . '&venue_id=' . $data['venue_id']
		    . '&category_id=music';


		$s = $this->rest->restGet( '/rest/events/new' , $rq );

		if ( $s->isSuccessful() ) {

var_dump($s->getBody());
			$xml = new SimpleXMLElement($s->getBody());
			$evdb_id = $xml->id;

			$this->insertCategory( $evdb_id , $data );
			$this->insertEventLinks( $evdb_id , $data );
			$this->insertTags( $evdb_id , $data );
			$this->insertImage( $evdb_id , $data );
			$this->reindexEvent( $evdb_id );
			return true;
		} else {
			return false;
		}
	}

	public function insertTags( $evdb_id , $data )
	{
		$tags = '"Switzerland" "Music" "Party"';

		if ( !empty( $data['style_tags'] ) ) {
			$tags .= ' "' . implode( '" "' , explode( ', ', $data['style_tags'] ) ) .'"';
		}

		$rq = $this->login_string
		    . '&id=' . $evdb_id
		    . '&tags=' . urlencode($tags);

		$s = $this->rest->restGet( '/rest/events/tags/new' , $rq );
		if ( $s->isSuccessful() ) {
			var_dump($s->getBody());
			return true;
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
		var_dump($s->getBody());
		return $s->isSuccessful();
	}

	public function insertCategory( $evdb_id , $data ) {
		
		if ( empty( $data['category_name'] ) ) {
			$data['category_name'] = 'music';
		}

		$rq = $this->login_string
		    . '&id=' . $evdb_id
		    . '&category_id=' . $data['category_name'];

		var_dump($rq);
		$s = $this->rest->restGet( '/rest/events/categories/add' , $rq );
		var_dump($s->getBody());
		return $s->isSuccessful();
	}

	/**
	 *
	 * @todo implement provider_image
	 */
	public function insertImage( $evdb_id , $data ) {
		
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
			var_dump($s->getBody());
			return $s->isSuccessful();
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
		var_dump($s->getBody());
		return $s->isSuccessful();
	}
}
?>
