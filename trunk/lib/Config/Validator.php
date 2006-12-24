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
 * Config Validation Class
 */
class Config_Validator_PartyCal {

	/**
	 *
	 * @todo write construct method
	 * @todo add support for dynamic loading/registering validators like Provider_Petzi_Config_Validator_Node_PartyCal
	 */
	public function __construct( $config )
	{
		
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
}

?>
