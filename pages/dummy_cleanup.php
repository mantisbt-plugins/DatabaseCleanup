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

html_page_top( plugin_lang_get( 'title' ) );
print_manage_menu();

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

html_page_bottom();

