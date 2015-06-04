<?php
	if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die(__('You are not allowed to call this page directly.', 'link-to-your-content')); }
	// If the current user is an admin don't allow them to see the page
	if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network('link-to-your-content/link_to_your_content.php'))  {
		if( !current_user_can( 'manage_network' ) ) { die(__('You don\'t have sufficient privileges to view this page.', 'link-to-your-content')); }
	}
	else {
		if( !current_user_can( 'moderate_comments' ) ) { die(__('You don\'t have sufficient privileges to view this page.', 'link-to-your-content')); }
	}

	if(function_exists('is_plugin_active_for_network') && is_plugin_active_for_network('link-to-your-content/link_to_your_content.php') && isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Options Saved', 'link-to-your-content') ?></strong></p></div>
	<?php endif; ?>
	<div class="wrap">
		<h2><?php _e('Link to Your Content Options', 'link-to-your-content') ?></h2>
	</div>

	<form method="post" action="<?php echo admin_url('options.php');?>">
		<?php
			settings_fields('ltyc-settings-group');
			$options = get_option('ltyc_options');
		?>
		<input type="hidden" name="ltyc_options[ltyc_db_version]" value="<?php echo $options['ltyc_db_version']; ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Link to Taxonomies:', 'link-to-your-content'); ?></th>
				<td>
					<?php _e('Yes', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_taxonomy]" value="1"<?php if($options['ltyc_taxonomy'] == '1') { echo ' checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php _e('No', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_taxonomy]" value="0"<?php if($options['ltyc_taxonomy'] == '0') { echo ' checked="checked"'; } ?> />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Link to Custom Post Types:', 'link-to-your-content'); ?></th>
				<td>
					<?php _e('Yes', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_custom_post_type]" value="1"<?php if($options['ltyc_custom_post_type'] == '1') { echo ' checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php _e('No', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_custom_post_type]" value="0"<?php if($options['ltyc_custom_post_type'] == '0') { echo ' checked="checked"'; } ?> />
				</td>
			</tr>
			<?php
				// Check to see if multi-site is enabled
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network('link-to-your-content/link_to_your_content.php')) {
				?>
					<tr valign="top">
						<th scope="row"><?php _e('Enable Multi-Site:', 'link-to-your-content'); ?></th>
						<td>
							<?php _e('Yes', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_multi_site]" value="1"<?php if($options['ltyc_multi_site'] == '1') { echo ' checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php _e('No', 'link-to-your-content'); ?> <input type="radio" name="ltyc_options[ltyc_multi_site]" value="0"<?php if($options['ltyc_multi_site'] == '0') { echo ' checked="checked"'; } ?> />
						</td>
					</tr>
				<?php
				}
			?>
			<tr valign="top">
				<th scope="row"><?php _e('Paginate results after:', 'link-to-your-content'); ?></th>
				<td>
					<input type="text" name="ltyc_options[ltyc_pagination]" value="<?php if(strlen(trim($options['ltyc_pagination'])) > '0') { echo $options['ltyc_pagination']; } ?>" />
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'link-to-your-content'); ?>" />
		</p>
	</form>