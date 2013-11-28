<?php

form_security_validate( 'plugin_DatabaseCleanup_config_edit' );

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_expiration = gpc_get_string( 'expiration', 0 );
$f_minimum_status = gpc_get_int( 'minimum_status' );
$f_reference_date = gpc_get_string('reference_date');

if ( $f_expiration != "0" && strtotime("- $f_expiration") === false) {
	# conversion failed
    plugin_error( 'InvalidPeriodString', ERROR );
} else if ( plugin_config_get( 'default_expiration_period' ) != $f_expiration ) {
	plugin_config_set( 'default_expiration_period', $f_expiration );
}

if ( plugin_config_get( 'minimum_status' ) != $f_minimum_status ) {
	plugin_config_set( 'minimum_status', $f_minimum_status );
}

if ( plugin_config_get( 'reference_date' ) != $f_reference_date ) {
	plugin_config_set( 'reference_date', $f_reference_date );
}

form_security_purge( 'plugin_DatabaseCleanup_config_edit' );

print_successful_redirect( plugin_page( 'config', true ) );
