<?php
/**
* This is the template of the shield settings page, where some settings related to the plugins are specified.
*/

?>
<div class="wrap">
<h2>Shield Custom post Plugin settings</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Delete all data on deactivation ?</th>
<td><input type="checkbox" name="delete_option" value="true" <?php if (get_option('delete_option')== 'true')
{
	echo "checked";
}  ?> /></td>
</tr>
 


</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="delete_option" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>