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

// check if enough time has passed since last run
$t_run_delay = plugin_config_get('run_delay');
$t_last_cleanup_run = plugin_config_get('last_cleanup_run', 0);

if ( strtotime('now') - $t_last_cleanup_run < $t_run_delay * 60 * 60 ){
    response('Not enough time passed since last run.', 403);
    die;
}


$t_issues_to_delete = create_bug_list();

function delete_issues($p_issues_list) {

    foreach ($p_issues_list as $key => $value) {
        # bug_delete
    }

}

plugin_config_set('last_cleanup_run', strtotime('now'));