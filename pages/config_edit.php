<?php
# Copyright 2013 MTU Aero Engines AG
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#   http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

form_security_validate( 'plugin_DatabaseCleanup_config_edit' );

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_expiration = gpc_get_string( 'expiration', "0" );
$f_minimum_status = gpc_get_int( 'minimum_status' );
$f_reference_date = gpc_get_string('reference_date');
$f_admin_email = gpc_get_string('admin_email');
$f_run_delay = gpc_get_int( 'run_delay', 12 );

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

if ( plugin_config_get( 'run_delay' ) != $f_run_delay ) {
	plugin_config_set( 'run_delay', $f_run_delay );
}

if ( plugin_config_get( 'admin_email' ) != $f_admin_email ) {
	email_ensure_valid($f_admin_email);
	plugin_config_set( 'admin_email', $f_admin_email );
}

form_security_purge( 'plugin_DatabaseCleanup_config_edit' );

print_successful_redirect( plugin_page( 'config', true ) );
