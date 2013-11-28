<?php
# Database Cleanup plugin for Mantis
# TODO add license

//require_once( 'MantisPlugin.class.php' );

class DatabaseCleanupPlugin extends MantisPlugin {

    function register() {
        $this->name        = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');
 
        $this->version     = '0.1';
        $this->requires    = array(
          'MantisCore'       => '1.2.0',
        );
     
        $this->author      = 'Gianluca Sforna';
        $this->contact     = 'giallu@gmail.com';
        $this->url         = 'TODO';
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
            'minimum_status' => _NEW,
            'admin_email' => '',
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