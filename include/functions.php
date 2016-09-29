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


class IssueData {
    public $id;
    public $project_id;
    public $status;
    public $date_submitted = '';
    public $last_updated = '';
    public $summary = '';
}

function get_project_expiration_period($p_project_id){
    $t_expiration_period = plugin_config_get( 'default_expiration_period' );
    $t_project_expiration_period = plugin_config_get( 'project_expiration_period', 0, false, null, $p_project_id);
    if ($t_project_expiration_period != "0") {
        // replace global expiration
        $t_expiration_period = $t_project_expiration_period;
    }
    return $t_expiration_period;
}

// create and return the list of issues matching the configured rules for deletion
function create_bug_list(){
    $t_issues_list = array();

    $t_default_expiration_period = plugin_config_get( 'default_expiration_period' );
    $t_reference_date_field = plugin_config_get('reference_date');
    if ($t_default_expiration_period == "0"){
        // Disabled, return an empty list
        return $t_issues_list;
    }

    $t_minimum_status = plugin_config_get('minimum_status');
    $t_desired_statuses = array();
    $t_available_statuses = MantisEnum::getValues( config_get( 'status_enum_string' ) );
    foreach( $t_available_statuses as $t_this_available_status ) {
        if( $t_this_available_status >= $t_minimum_status ) {
            $t_desired_statuses[] = $t_this_available_status;
        }
    }

    # foreach project
    $t_projects = project_get_all_rows();
    foreach ( $t_projects as $t_project_id => $t_project_data ) {
        $t_expiration_date = strtotime("- ". get_project_expiration_period($t_project_id));
        $t_selected_issues = do_query($t_project_id, $t_desired_statuses);
        foreach ($t_selected_issues as $t_issue) {
            if ($t_issue->$t_reference_date_field < $t_expiration_date ) {
                $t_issue->expiration_date = $t_expiration_date;
                $t_issues_list[] = $t_issue;
            }
        }
    }
    return $t_issues_list;
}


function do_query( $p_project_id, $p_desired_statuses){
    global $g_cache_bug;
    # create filter
    $t_filter = filter_get_default();
    $t_filter[FILTER_PROPERTY_STATUS] = $p_desired_statuses;
    $t_filter[FILTER_PROPERTY_PROJECT_ID] = $p_project_id;
    $t_filter['_view_type'] = 'advanced';

    # Get bug rows according to the current filter
    $t_page_number = -1;
    $t_per_page = 100;
    $t_bug_count = null;
    $t_page_count = null;
    $t_filter_result = array();

    $rows = filter_get_bug_rows( $t_page_number, $t_per_page, 
        $t_page_count, $t_bug_count, $t_filter);
    for ($t_page = 1; $t_page <= $t_page_count; ++$t_page){
        // nuke the bug cache, otherwise we hit memory limit on large projects
        $g_cache_bug = null;
        $rows = filter_get_bug_rows( $t_page, $t_per_page, 
            $t_page_count, $t_bug_count, $t_filter);
        foreach ($rows as $t_bug) {
            $t_new_data = new IssueData();
            $t_new_data->id = $t_bug->id;
            $t_new_data->project_id = $t_bug->project_id;
            $t_new_data->status = $t_bug->status;
            $t_new_data->date_submitted = $t_bug->date_submitted;
            $t_new_data->last_updated = $t_bug->last_updated;
            $t_filter_result[] = $t_new_data;
        }
    }

    if( $t_filter_result === false ) {
        echo "<p>FILTER FAILED!<p>";
    }

    return $t_filter_result;
}


function create_csv($p_issues_list){
    $t_reference_date_field = plugin_config_get('reference_date');
    $t_deletion_time = new DateTime();

    $t_result = array();
    $t_result[] = 'project,issue,status,summary,"deleted on",overaged,"expiration period"';
    foreach ($p_issues_list as $t_issue) {
        $t_reference_date = DateTime::createFromFormat("U", $t_issue->$t_reference_date_field);
        $t_age = $t_reference_date->diff( $t_deletion_time );
        $t_expiration_period = get_project_expiration_period($t_issue->project_id);
        $t_result[] = project_get_name($t_issue->project_id) . ','
            . $t_issue->id . ','
            . get_enum_element( 'status', $t_issue->status, NO_USER, $t_issue->project_id ) . ','
            . '"' . $t_issue->summary . '",'
            . $t_deletion_time->format('Y-m-d') . ','
            . '"' . $t_age->days . ' days",'
            . '"' . $t_expiration_period . '"';
    }
    return $t_result;
}


if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}
