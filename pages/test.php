<?php
# MantisBT - a php based bugtracking system
# Copyright (C) 2002 - 2013  MantisBT Team - mantisbt-dev@lists.sourceforge.net
# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( plugin_lang_get( 'title' ) );
print_manage_menu( );



$t_current_font_selected = array(
    'arial' => false,
    'verdana' => false,
    'trebuchet' => false,
    'verasans' => false,
    'times' => false,
    'georgia' => false,
    'veraserif' => false,
    'courier' => false,
    'veramono' => false,
);

$t_current_font = plugin_config_get( 'default_expiration_period' );
if ( isset( $t_current_font_selected[$t_current_font] ) ) {
    $t_current_font_selected[$t_current_font] = true;
} else {
    $t_current_font_selected['arial'] = true;
}

/**
 * Prints checked="checked" to the end of a HTML <option> tag if the supplied
 * font name matches the current font configuration value.
 * @param string The name of the font to check
 * @return string Either checked="checked" for a match or otherwise an empty string
 */
function print_font_checked( $p_font_name ) {
    global $t_current_font_selected;

    if ( isset( $t_current_font_selected[$p_font_name] ) ) {
        if ( $t_current_font_selected[$p_font_name] ) {
            return ' checked="checked"';
        }
    }

    return '';
}

?>

<br />
<form action="<?php echo plugin_page( 'config_edit' )?>" method="post">
<?php echo form_security_field( 'plugin_database_cleanup_config_edit' ) ?>
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
        <label><input type="text" name="expiration" value="<?php echo( plugin_config_get( 'default_expiration_period' )); ?>"></label>
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
