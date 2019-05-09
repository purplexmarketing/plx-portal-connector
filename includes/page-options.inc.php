<?php
// Create plugin options link on plugins page
function plx_portal_plugin_page_link( $plx_portal_links ) {
  $plx_portal_link = '<a href="admin.php?page=plx-portal">Settings</a>';
  array_unshift( $plx_portal_links, $plx_portal_link);
  return $plx_portal_links;
}
add_filter('plugin_action_links_' . PLX_PORTAL_PLUGIN_BASENAME, 'plx_portal_plugin_page_link');

// Create options group for settings
function plx_portal_plugin_register_settings() {
  register_setting( 'plx-portal-plugin-settings-group', '_plx_portal_api_key' );
}
add_action( 'admin_init', 'plx_portal_plugin_register_settings' );

// Create plugin configuration page
function plx_portal_page() {

  global $plx_maintenance_mode;
?>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'plx-portal-plugin-settings-group' );
		do_settings_sections( 'plx-portal-plugin-settings-group' );
		?>
		<div class="wrap">
			<h2>Portal Settings</h2>
			<table class="form-table">
        <tr valign="top">
        	<th scope="row">API Key</th>
					<td>
						<?php
						//check if there is an api key saved
						if (get_option('_plx_portal_api_key')) {

							//set api key for call
							$plx_api_key = get_option('_plx_portal_api_key');

							//post call to api
							$plx_api_result = plx_portal_api($plx_api_key , 'check/');

							//decode response
							$plx_api_response = json_decode($plx_api_result, true);

							//check if this was a valid api key
							if ($plx_api_response['request_status'] == 1) {

								//set status
								$plx_key_status = '<p class="description"><span class="dashicons dashicons-yes" style="color: #13af23;"></span> API Key is valid</p>';

							} else {

								//set status
								$plx_key_status = '<p class="description"><span class="dashicons dashicons-no-alt" style="color: #cc1010;"></span>' . $plx_api_response['request_data'] . '</p>';

							} //END check if this was a valid api key

						} else {

							 //set status
							 $plx_key_status = "";

						} //END check if there is an api key saved
						?>
						<input type="text" name="_plx_portal_api_key" id="_plx_portal_api_key" value="<?php echo get_option('_plx_portal_api_key'); ?>" class="regular-text" /> <?php echo $plx_key_status; ?>
					</td>
        </tr>
        <tr valign="top">
        	<th scope="row">Maintenance</th>
					<td>
            <label>
						  <input type="checkbox" <?php if ($plx_maintenance_mode == 1) { echo " checked"; } ?> disabled /> Site Hold/Being Worked On
            </label>
					</td>
        </tr>
      </table>
      <?php
			submit_button();
			?>
		</div>
	</form>
<?php
}
