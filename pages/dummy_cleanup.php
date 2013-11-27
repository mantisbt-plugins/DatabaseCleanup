<?php

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( plugin_lang_get( 'title' ) );
print_manage_menu();


$t_default_expiration_period = plugin_config_get( 'default_expiration_period' );

// create and return the list of issues matching the configured rules for deletion
function create_bug_list(){
    $t_issues_list = array();

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
        # determine expiration_period
        
        $t_selected_issues = do_query($t_project_id, $t_desired_statuses);
        foreach ($t_selected_issues as $t_issue) {
            $t_issues_list[] = $t_issue;
        }

                # if bug_age > expiration_period
                    # $t_bug_list[] = $t_bug_id
    }
    return $t_issues_list;

}


function do_query( $p_project_id, $p_desired_statuses){

    #create filter
    $t_filter = filter_get_default();
    $t_filter[FILTER_PROPERTY_STATUS_ID] = $p_desired_statuses;
    $t_filter[FILTER_PROPERTY_PROJECT_ID] = $p_project_id;
    $t_filter['_view_type'] = 'advanced';

    # Get bug rows according to the current filter
    $t_page_number = 1;
    $t_per_page = -1;
    $t_bug_count = null;
    $t_page_count = null;
    $t_filter_result = filter_get_bug_rows( $t_page_number, $t_per_page, 
        $t_page_count, $t_bug_count, $t_filter);

    if( $t_filter_result === false ) {
        echo "<p>FILTER FAILED!<p>";
        $t_filter_result = array();
    }

    return $t_filter_result;

}


$t_issues_to_delete = create_bug_list();

echo "<p>Found " . count($t_issues_to_delete) . " issues to delete</p>";
foreach ($t_issues_to_delete as $t_issue) {
    echo "<pre>";
    echo "Project: $t_issue->project_id issue: $t_issue->id";
    echo "</pre>";
}
/*
<br />
<form action="<?php echo plugin_page( 'config_edit' )?>" method="post">
<?php echo form_security_field( 'plugin_DatabaseCleanup_config_edit' ) ?>
<table align="center" class="width75" cellspacing="1">

<tr>
    <td class="form-title" colspan="3">
        <?php echo plugin_lang_get( 'title' ) . ': ' 
        . plugin_lang_get( 'config' )?>
    </td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
    <td class="category">
        <?php echo plugin_lang_get( 'expiration_period' )?>
    </td>
    <td class="center" colspan="2">
        <label><input type="text" name="expiration" value="<?php echo $t_default_expiration_period; ?>"></label>
    </td>
</tr>

<tr <?php echo helper_alternate_class( )?>>
    <td class="category">
        <?php echo plugin_lang_get( 'minimum_status' )?>
    </td>
    <td class="center" colspan="2">
        <select name="minimum_status">
            <?php print_enum_string_option_list( 'status', $t_minimum_status ) ?>
        </select>  
    </td>
</tr>


<tr class="spacer"><td></td></tr>

<tr>
    <td class="center" colspan="3">
        <input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' )?>" />
    </td>
</tr>

</table>
<form>
*/

html_page_bottom();

