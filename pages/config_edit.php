<?php

form_security_validate( 'plugin_DatabaseCleanup_config_edit' );

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_expiration = gpc_get_int( 'expiration', 0 );
$f_minimum_status = gpc_get_int( 'minimum_status' );

if ( plugin_config_get( 'default_expiration_period' ) != $f_expiration ) {
	plugin_config_set( 'default_expiration_period', $f_expiration );
}

if ( plugin_config_get( 'minimum_status' ) != $f_minimum_status ) {
	plugin_config_set( 'minimum_status', $f_minimum_status );
}

form_security_purge( 'plugin_DatabaseCleanup_config_edit' );

print_successful_redirect( plugin_page( 'config', true ) );
