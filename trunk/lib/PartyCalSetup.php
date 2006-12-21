<?php
/**
 * Basic PHP Configuration.
 * 
 * Add your include paths here if your on hosting. Actually hack at will.
 *
 * @filesource
 */

ini_set('include_path', '.:/home/hairmare/dev/php/zf/library/');

if ( !isset( $_ENV['PARTYCAL_CONFIG'] ) ) {
	$_ENV['PARTYCAL_CONFIG'] = 'config/partycal.ini';
}

?>
