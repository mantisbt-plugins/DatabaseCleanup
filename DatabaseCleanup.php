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
            'reference_date' => '',
        );
    }

    function init() {
        plugin_event_hook( 'EVENT_PLUGIN_INIT', 'header' );
    }

    /**
    * Handle the EVENT_PLUGIN_INIT callback.
    */
    function header() {
        header( 'X-Mantis: This Mantis has super cow powers.' );
    }

}