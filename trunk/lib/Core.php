<?php
/**
 * Abstract Core for PartyCal Interfaces.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 * Config Parsing.
 */
require_once 'Config.php';

/**
 * Provider Listing.
 */
require_once 'ArrayObject/Listing/Provider.php';

/**
 * Subscriber Listing.
 */
require_once 'ArrayObject/Listing/Subscriber.php';

/**
 * Abstract Action Controller Class.
 *
 * @todo implement set action for reading and setting ini file settings
 */
abstract class Core_PartyCal {
	/**
	 * @var Config_PartyCal Basic Configuration for the interface
	 */
	public $conf;

	/**
	 * @var PDO Database Connection Object
	 */
	public $pdo;

	/**
	 * @var ProviderArray_PartyCal
	 */
	public $providers;

	/**
	 * @var SubscriberArray_PartyCal
	 */
	public $subscribers;

	/**
	 * Setup a new Interface Instance.
	 *
	 * Each instance gets to know how the world looks:
	 * - read arguments and parse configuration.
	 * - parse provider and subscriber listings
	 * - open persistent db connection
	 *
	 * the idea is to not read anything from the db yet,
	 * all setup comes from files.
	 *
	 * @param $mode String
	 * @param $argv Array
	 */
	public function __construct( $mode , $argv ) {

		$this->argv = $argv;
		$this->conf = new Config_PartyCal( $mode , 'partycal' );

		$this->providers = new Listing_Provider_PartyCal ( $this->conf->getProviderListing() );
		$this->subscribers = new Listing_Subscriber_PartyCal ( $this->conf->getSubscriberListing() );

		$this->pdo = new PDO( $this->conf->db_dso , null, null,
		     array(PDO::ATTR_PERSISTENT => true));
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Main Program Startingpoint.
	 *
	 * We parse the first argument in $this->argv and use it to decide what to do.
	 */
	public function main() {

		switch( $this->argv[1] ) {
			case NULL:
			case '-h':
			case '--help':
				$this->actionhelp();
				break;
			default:
				$func = 'action'.$this->argv[1];
				$this->$func();
		}
	}

	/**
	 * @var String some data that gets displayed in the help output
	 */
	public $helpString;

	/**
	 * Help Action.
	 *
	 * Display help content.
	 */
	public function actionhelp() {

		echo __CLASS__.' -- '.$this->helpString."\n\n";
		echo 'Usage: '.$this->argv[0].' [COMMAND] '."\n";
		echo '       COMMAND: '.'i need code to dump this from class'."\n";
	}
}
?>
