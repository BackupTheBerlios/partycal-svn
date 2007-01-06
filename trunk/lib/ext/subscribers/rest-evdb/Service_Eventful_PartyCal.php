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
 * Main eventful class
 */
class Service_Eventful_PartyCal {

	public function __construct( $config ) {

		parent::__construct( $config );

		$this->config = $config;
		$this->uri = $this->config->feed;

		$this->makeAuthedClient();
	}

	/**
	 * login to eventful 
	 *
	 * $this->login_string may be used as args to anything needing digested auth from here on
	 */
	public function makeAuthedClient() {

		$this->login_string = 'app_key=' 
		                    . $this->conf->api_key;

		$this->rest = new Zend_Service_Rest();

		$this->rest->setURI( 'http://api.evdb.com' );
		$r = $this->rest->restGet( '/rest/users/login' , $this->login_string );

		if ( $r->isSuccessful() ) {

			// $nonce = ; read out nonce from $r->getBody();

			$response = strtolower( md5( $nonce . ':' . strtolower( md5($this->conf->passwd) ) ) );

			$this->login_string .= '&user=' . $this->conf->user;

			$auth_req = $this->login_string
			          . '&nonce=' . $nonce
			          . '&response=' . $response;

			$s = $this->rest->restGet( '/rest/users/login' , $auth_req );

			if ($s->isSuccessful()) {

				// $user_key = ; get user key from $s->getBody();
				$this->login_string .= '&user_key='.$user_key;
			}

		}
	}

	public function addNewRecord( $data )
	{
		if ( !$this->venueExists() && !$this->performerExists() ) {

			$logger->inexistantVenueOrPerformerMsg();

			return;

		} else {
			
// insert event by means of zend rest
			$this->insertEvent( $data );
		}
	}

	public function venueExists()
	{
		// find club id -> /search/venues/venue@id
		// uses mapping data in partycal.ini
		// venue stuff is very manual... with good reporting thats no problem
		// lots of subscribers will make me automaize this later on

		// http://api.evdb.com/rest/venues/search?app_key=wHsVzgXLc4GdQFcT&user=hairmare&password=lemmein&keywords=Dachstock&location=Bern+Switzerland
		// clubs will need to get added by hand, this should not be to hard. maybe reporting for missing clubs should be added.

	}

	public function performerExists()
	{
	}

	public function insertEvent()
	{
	}
}
?>
