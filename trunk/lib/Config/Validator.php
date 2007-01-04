<?php
/**
 * Configuration Validator for partycal.ini Files.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Config
 *
 * @todo write documentation
 * @todo implement Config_Validator_AbstractNode_PartyCal
 * @todo implement Config_Validator_CoreNode_PartyCal
 * @todo implement Config_Validator_SubscriberListingNode_PartyCal
 * @todo implement Config_Validator_ProviderListingNode_PartyCal
 * @todo implement Provider_Petzi_Config_Validator_Node_PartyCal
 */

/**
 *
 */
require_once 'Config/Validator/CoreNode.php';

/**
 *
 */
require_once 'Config/Validator/SubscriberListingNode.php';

/**
 *
 */
require_once 'Config/Validator/ProviderListingNode.php';

/**
 *
 */
define('CONFIG_VALIDATOR_PARTYCAL_MODE_SCAN', 1);

/**
 * Config Validation Class
 */
class Config_Validator_PartyCal {

	/**
	 * @var Config_PartyCal Config Object being validated.
	 */
	public $config;

	/**
	 * @var Array chain of Config_Validator_Nodes/Classnames.
	 */
	public $validatorchain;

	/**
	 * @var Array Dirs to scan in loadValidatorsByDir.
	 */
	public $validatorscandirs = array( 'ext/providers/' , 'ext/subscribers' );

	/**
	 * Store config and load validators.
	 *
	 * Basic validation comes from registerCoreValidators(). you can choose to let Config_Validator 
	 * do its own scanning in the dirs in $this->validatorscandirs. 
	 * $flag may be:
	 * - CONFIG_VALIDATOR_PARTYCAL_MODE_SCAN
	 *
	 * @param Config_PartyCal $config
	 * @param Array $validators Validators to load
	 * @param Flag $flag more settings
	 */
	public function __construct( $config, $flag = 0 , $validators = NULL )
	{
		$this->validatorchain = new ArrayObject();

		$this->config = $config;

		$this->registerCoreValidators();

		if ( $flag == CONFIG_VALIDATOR_PARTYCAL_MODE_SCAN ) {
			$this->loadValidatorsByDirs( $this->validatorscandirs );
		}

		if ( is_string( $validators ) ) {
			$this->append( $validators );
		}
	}

	/**
	 * Dispatch validation to registered validator classes.
	 * 
	 * @throws Config_Exception_PartyCal
	 * 
	 * The Exception might get flaggable someday maybe.
	 */
	public function validate()
	{
		$i = $this->validatorchain->getIterator();

		while ( $i->valid() ) {

			$class = $i->current();

			$v = new $class( $this );
			$v->validate();

			$i->next();
		}
	}

	/**
	 * Register the core Validators.
	 *
	 * Core validators get included manually at the top of this file.
	 */
	public function registerCoreValidators()
	{
		$this->append('Config_Validator_CoreNode_PartyCal');
		$this->append('Config_Validator_SubscriberListingNode_PartyCal');
		$this->append('Config_Validator_ProviderListingNode_PartyCal');
	}

	/**
	 * dynamically load and append validators to the validatorchain.
	 *
	 * @param Array $scandirs Dirs to scan for Config/Validator subdirectories. 
	 *
	 * @todo implement support for dynamic loading/registering validators like Provider_Petzi_Config_Validator_Node_PartyCal
	 */
	public function loadValidatorsByDirs( $scandirs )
	{
		foreach ( $scandirs AS $dir ) {
			$this->loadValidatorsByDir( $dir );
		}
	}

	public function loadValidatorsByDir( $dir )
	{
		$dir = new DirectoryIterator( $dir );

		while ( $dir->valid() ) {
			if ( !$dir->isDot() && $dir->isDir() ) {

				$validatorfile  = $dir->getPath() . '/' . $dir->current() . '/Validator.php';
				$validatorclass = 'Config_Validator_' . $dir->current() . 'Node_PartyCal';
				
				$this->loadValidator( $validatorclass , $validatorfile );
			}

			$dir->next();
		}
	}

	public function loadValidator( $validatorclass , $validatorfile ) {

		$file = new SplFileInfo( $validatorfile );

		if ( $file->isReadable() ) {
echo $validatorclass , $validatorfile ."\n";

			require_once $validatorfile;
			$this->append( $validatorclass );
		}
	}

	/**
	 * Add a validator to the validatorchain
	 */
	public function append( $validator )
	{
		$this->validatorchain->append( $validator );
	}
}

?>
