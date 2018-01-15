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

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

layout_page_header( plugin_lang_get( 'title' ) );
layout_page_begin();
print_manage_menu();


echo "<h1>Preliminary checks</h1>";

// check if enough time has passed since last run
$t_run_delay = plugin_config_get('run_delay');
$t_last_cleanup_run = plugin_config_get('last_cleanup_run', 0);

echo "<p>Runs delay expired: ";
if ( strtotime('now') - $t_last_cleanup_run < $t_run_delay * 60 * 60 ){
    echo "NO</p>";
}
else {
    echo "YES</p>";
}

// check remote key and signature
echo "<p>Key paramenter present: ";
$f_key = gpc_get_string('key', '');
if ( empty($f_key) ) {
    echo "NO</p>";
}
else {
    echo "YES</p>";
}

echo "<p>Signature paramenter present: ";
$f_signature = gpc_get_string('sig', '');
if ( empty($f_signature) ) {
    echo "NO</p>";
}
else {
    echo "YES</p>";
}

//verify signature
echo "<p>Signature verification pass: ";
$t_secret_key = plugin_config_get('secret_key');
if ( $f_signature != md5($t_secret_key.$f_key) ) {
    echo "NO</p>";
}
else {
    echo "YES</p>";
}

echo "<h1>Simulations results</h1>";

$t_issues_to_delete = create_bug_list();

echo "<p>Found " . count($t_issues_to_delete) . " issues to delete</p>";
foreach ($t_issues_to_delete as $t_issue) {
    echo "<p>";
    echo "Project: $t_issue->project_id " . '<a href="' . string_get_bug_view_url($t_issue->id) . '"' . ">issue: $t_issue->id</a>";
    echo "</p>";
    //echo "<pre>"; print_r($t_issue); echo "</pre>";
}

$t_csv = create_csv($t_issues_to_delete);

echo '<pre>';
echo implode("\r\n", $t_csv);
echo '</pre>';

layout_page_end();

