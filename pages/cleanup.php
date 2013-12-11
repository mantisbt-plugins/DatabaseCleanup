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

$t_plugin_path = config_get( 'plugin_path' );
require_once($t_plugin_path . 'DatabaseCleanup/include/functions.php');

function response( $p_content, $p_status=200 ){
    http_response_code($p_status);
    echo $p_content;
}

# run script as administrator
$t_run_as_user = plugin_config_get('run_as_user');
auth_attempt_script_login($t_run_as_user);

// check if enough time has passed since last run
$t_run_delay = plugin_config_get('run_delay');
$t_last_cleanup_run = plugin_config_get('last_cleanup_run', 0);

if ( strtotime('now') - $t_last_cleanup_run < $t_run_delay * 60 * 60 ){
    response('Not enough time passed since last run.', 403);
    exit;
}

// check secret key
$t_secret_key = plugin_config_get('secret_key');
if ( empty($t_secret_key) ) {
    response('Secret key not found, please check plugin configuration', 503 );
    exit;
}

// check remote key and signature
$f_key = gpc_get_string('key', '');
if ( empty($f_key) ) {
    response('Remote key not found, be sure to add a "key" parameter with a random string', 400 );
    exit;
}
$f_signature = gpc_get_string('sig', '');
if ( empty($f_signature) ) {
    response('Signature not found', 400 );
    exit;
}

//verify signature
if ( $f_signature != md5($t_secret_key.$f_key) ){
    response('Invalid signature', 401 );
    exit;
}

$t_issues_to_delete = create_bug_list();

// send csv if needed
$t_admin_email = plugin_config_get('admin_email');
if ( ! empty($t_admin_email) && $t_issues_to_delete ){
    $t_csv = create_csv($t_issues_to_delete);
    email_store($t_admin_email, 'Mantis database cleanup report', implode("\r\n", $t_csv));
    email_send_all();
}

// disable email notification to avoid spamming
config_set_global('enable_email_notification', OFF);
foreach ($t_issues_to_delete as $t_issue) {
    bug_delete($t_issue->id);
}

echo count($t_issues_to_delete) . " issues deleted";

plugin_config_set('last_cleanup_run', strtotime('now'));
