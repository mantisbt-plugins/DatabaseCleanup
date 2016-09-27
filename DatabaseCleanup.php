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

# Database Cleanup plugin for Mantis
class DatabaseCleanupPlugin extends MantisPlugin {

    function register() {
        $this->name        = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');

        $this->version     = '1.1';
        $this->requires    = array(
          'MantisCore'       => '1.3.0',
        );

        $this->author      = 'Gianluca Sforna';
        $this->contact     = 'giallu@gmail.com';
        $this->url         = 'https://github.com/mantisbt-plugins/DatabaseCleanup';
        $this->page        = 'config';
    }

    /**
     * Default plugin configuration.
     */
    function config() {
        return array(
            // in days, 0 means disabled
            'default_expiration_period' => 0,
            'reference_date' => 'date_submitted',
            'minimum_status' => NEW_,
            'run_as_user' => '',
            'admin_email' => '',
            'run_delay' => 12,
            'last_cleanup_run' => 0,
            'secret_key' => ''
        );
    }

    function init() {
        plugin_event_hook( 'EVENT_MANAGE_PROJECT_UPDATE_FORM', 'project_options' );
        plugin_event_hook( 'EVENT_MANAGE_PROJECT_UPDATE', 'project_options_update' );
    }


    public function errors() {
        return array(
            'InvalidPeriodString' => 'Invalid period string. Please use something like "4 days", "6 months" or "1 year"',
        );
    }


    function project_options( $p_event, $p_project_id ) {
        $t_project_expiration_period = plugin_config_get( 'project_expiration_period', 0, false, null, $p_project_id);
        echo '<tr ' . helper_alternate_class() . '>';
        echo '<td class="category">';
        echo plugin_lang_get( 'project_expiration_period' );
        echo '</td>';
        echo '<td>';
        echo '<input type="text" name="expiration" value="' . $t_project_expiration_period . '"/>';
        echo '</td>';
        echo '</tr>';
    }


    function project_options_update( $p_event, $p_project_id ) {
        $f_project_expiration_period = gpc_get_string('expiration');

        if ($f_project_expiration_period == "0") {
            plugin_config_delete('project_expiration_period', NO_USER, $p_project_id);
            return;
        }

        if ( strtotime("- $f_project_expiration_period") === false) {
            # conversion failed
            plugin_error( 'InvalidPeriodString', ERROR );
        }
        else if (plugin_config_get( 'project_expiration_period', 0, false, null, $p_project_id) != $f_project_expiration_period) {
            plugin_config_set( 'project_expiration_period', $f_project_expiration_period, NO_USER, $p_project_id);
        }

    }

}