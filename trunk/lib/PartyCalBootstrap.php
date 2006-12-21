<?php
/**
 * Basic Bootstrap Configuration.
 * 
 * This script should get you running fast. You may coose to comment this 
 * out and do most of it in php.ini and .profile, just these rules:
 * 
 * PARTYCAL_CONFIG must be set in your env.
 * The following needs to get added to your include_path:
 * - PARTYCAL_ROOT/lib
 * - Zend Framework library/ dir.
 * - PARTYCAL_ROOT/ext/providers/NEEDED_PROVIDERS
 * - PARTYCAL_ROOT/ext/subscribers/NEEDED_SUBSCRIBERS
 *
 * You may choose to ignore $_ENV['PARTYCAL_ROOT'] if you want to it only 
 * gets used here.
 *
 * Most users will just let this script make some informed guesses.
 */

if ( !isset( $_ENV['PARTYCAL_CONFIG'] ) ) {
	$_ENV['PARTYCAL_CONFIG'] = $_ENV['PARTYCAL_ROOT'] . '/config/partycal.ini';
}

$partycal_extensions = '';
$partycal_providers = dir( $_ENV['PARTYCAL_ROOT'] . '/ext/providers' );
while ( ( $partycal_provider_item = $partycal_providers->read()) !== false ) {

	if ( ( $partycal_provider_item == '.' )
	||   ( $partycal_provider_item == '..' )
	||   ( $partycal_provider_item == '.svn' ) )
	continue;

	$partycal_extensions .= $_ENV['PARTYCAL_ROOT'] 
			     . '/ext/providers/'
			     . $partycal_provider_item . ':';
}
$partycal_providers->close();

$partycal_subscribers = dir( $_ENV['PARTYCAL_ROOT'] . '/ext/subscribers' );
while ( ( $partycal_subscriber_item = $partycal_subscribers->read() ) !== false ) {

	if ( ( $partycal_subscriber_item == '.' )
	||   ( $partycal_subscriber_item == '..' )
	||   ( $partycal_subscriber_item == '.svn' ) )
	continue;

	$partycal_extensions .= $_ENV['PARTYCAL_ROOT'] 
			     . '/ext/providers/'
			     . $partycal_subscriber_item . ':';
}
$partycal_subscribers->close();

ini_set( 'include_path' , ini_get('include_path') . ':'
			. $_ENV['PARTYCAL_ROOT'] . '/lib:'
		        . $_ENV['PARTYCAL_ROOT'] . '/lib/zf:' 
			. $partycal_extensions );

?>
