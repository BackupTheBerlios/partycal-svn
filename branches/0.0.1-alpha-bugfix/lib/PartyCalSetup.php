<?php

ini_set('include_path', '.:/home/hairmare/dev/php/zf/library/');

if ( !isset( $_ENV['PARTYCAL_CONFIG'] ) ) {
	$_ENV['PARTYCAL_CONFIG'] = '/home/hairmare/partycal/config/partycal.ini';
}

?>
