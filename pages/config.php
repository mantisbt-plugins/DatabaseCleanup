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

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( plugin_lang_get( 'title' ) );
print_manage_menu( );


$t_default_expiration_period = plugin_config_get( 'default_expiration_period' );
$t_minimum_status = plugin_config_get('minimum_status');
$t_reference_date = plugin_config_get('reference_date');
?>
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
        <?php echo plugin_lang_get( 'reference_date' )?>
    </td>
    <td class="center" colspan="2">
        <select name="reference_date">
<?php
$t_fields = array('date_submitted', 'last_updated');
foreach ( $t_fields as $t_key => $t_value) {
    echo '<option value="' . $t_value . '"';
        check_selected( $t_reference_date, $t_value );
        echo '>' . plugin_lang_get($t_value) . '</option>';
}
?>
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

<?php
html_page_bottom();
