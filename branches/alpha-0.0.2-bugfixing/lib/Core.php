<?php
/**
 * Abstract Core for PartyCal Interfaces.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Controller
 * @file
 *
 * @todo evaluate porting this to zend_controller, do it
 */

/**
 * Interface that gets implemented here.
 */
require_once 'Core/Interface/Controller.php';

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
 * This class is not defined abstract but should get used that way anyhow.
 *
 * @class
 */
class Core_PartyCal implements Core_Interface_Controller_PartyCal {

	/**
	 * @var Config_PartyCal Basic Configuration for the interface
	 */
	public $config;

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
	 * All params except $argv are optional. This is mainly so for unit-testing with Mock Objects.
	 * It also makes it easy to override any given part of partycal with your own libs.
	 *
	 * @param $argv Array
	 * @param $config Config_PartyCal Object
	 * @param $listings Array containing Listing_PartyCal Objects in [providers] and [subscribers]
	 * @param $pdo PDO Connection Object
	 * @param $config_node String only gets used when $config is null
	 */
	public function __construct( $argv , $config = NULL , $listings = NULL , $pdo = NULL , $config_node = 'partycal' ) {

		$this->argv = $argv;

		if ( empty( $config ) ) {
			$this->config = new Config_PartyCal( $config_node );
		} else {
			$this->config = $config;
		}

		if ( empty( $listings ) ) {
			$this->providers   = new Listing_Provider_PartyCal( new Config_PartyCal( 'provider-listing' ) );
			$this->subscribers = new Listing_Subscriber_PartyCal( new Config_PartyCal( 'subscriber-listing' ) );
		} else {
			$this->providers   = $listings['providers'];
			$this->subscribers = $listings['subscribers'];
		}

		if ( empty( $pdo ) ) {
			$this->pdo = new PDO( $this->config->db_dso , null, null,
			     array(PDO::ATTR_PERSISTENT => true));
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			$this->pdo = $pdo;
		}
	}

	/**
	 * Main Program Startingpoint.
	 *
	 * We parse the first argument in $this->argv and use it to decide which action to call.
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
	public function actionHelp() {

		$s = 'Usage: '.$this->argv[0].' [command] [subcommand]'."\n\n"
		   . 'help: output global help (this) or subcommand help'."\n";
		file_put_contents( 'php://output' , $s );
	}
}
?>
