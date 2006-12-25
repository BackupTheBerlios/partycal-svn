<?php
/**
 * Configuration Validator for partycal.ini Files.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package config
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
	public function __construct( $config , $validators = NULL, $flag = 0 )
	{
		$this->config = $config;

		$this->registerCoreValidators();

		if ( $scan ) {
			$this->loadValidatorsByDir( $this->validatorscandirs );
		}

		$this->append($validators);
	}

	/**
	 * 
	 * @throws Config_Exception_PartyCal
	 *
	 * @todo call core node validators etc
	 * @todo dispatch calls to registered node validators (name based binding on ini entry)
	 */
	public function validate()
	{
	}

	/**
	 * Register the core Validators.
	 *
	 * Core validators get included manually at the top of this file.
	 *
	 * @todo implement
	 */
	public function registerCoreValidators()
	{
	}

	/**
	 * 
	 *
	 * @param Array $scandirs Dirs to scan for Config/Validator subdirectories
	 *
	 * @todo implement support for dynamic loading/registering validators like Provider_Petzi_Config_Validator_Node_PartyCal
	 */
	public function loadValidatorsByDir( Array $scandirs )
	{
	}

	public function append($validators)
	{
		if ( is_string($validators) ) {
			$validators = array ( $validators );
		}

		if ( !empty( $this->validatorchain ) ) {
			$this->validatorchain = array_merge( $this->validatorchain , $validators );
		} else {
			$this->validatorchain = $validators;
		}
	}

	public function prepend($validators)
	{
		if ( !empty( $this->validatorchain ) ) {
			$this->validatorchain = array_merge( $validators , $this->validatorchain );
		} else {
			$this->validatorchain = $validators;
		}
	}
}

?>
