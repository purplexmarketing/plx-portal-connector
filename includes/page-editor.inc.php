<?php
// Create plugin configuration page
function plx_portal_editor_page() {

  //get current htaccess content and put into array
  $plx_portal_htaccess_content = @file(PLX_PORTAL_FILE_HTACCESS_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	//check if htaccess is managed by PLX Portal Connector
	if (strpos($plx_portal_htaccess_content[0], '# PLX Portal Connector') !== false) {

		//set current block to null
		$current_block = null;

		//create block arrays
		$block_redirects_array = array();
    $block_caching_array = array();
    $block_siteurl_array = array();
		$block_compression_array = array();
		$block_keepalive_array = array();
    $block_custombefore_array = array();
    $block_customafter_array = array();

		//loop through array of content
		foreach ($plx_portal_htaccess_content as &$plx_portal_line) {

			//check if this is the end of the site url block
			if (strpos($plx_portal_line, '# END Redirects') !== false) { $current_block = null; }

      //check if this is the end of the caching block
			if (strpos($plx_portal_line, '# END Page Caching') !== false) { $current_block = null; }

      //check if this is the end of the site url block
			if (strpos($plx_portal_line, '# END Site URL') !== false) { $current_block = null; }

			//check if this is the end of the compression block
			if (strpos($plx_portal_line, '# END Compression') !== false) { $current_block = null; }

			//check if this is the end of the keep-alive block
			if (strpos($plx_portal_line, '# END Keep-alive') !== false) { $current_block = null; }

      //check if this is the end of the custom before rewriterules block
			if (strpos($plx_portal_line, '# END Custom Before') !== false) { $current_block = null; }

      //check if this is the end of the custom after rewriterules block
			if (strpos($plx_portal_line, '# END Custom After') !== false) { $current_block = null; }

			//redirects block
			if ($current_block == 'redirects') {

				//create redirects block array
				array_push($block_redirects_array, $plx_portal_line);

			} //END redirects block

      //page caching block
			if ($current_block == 'caching') {

				//check if this is ExpiresDefault
				if (strpos($plx_portal_line, 'ExpiresDefault') !== false) {

					//set mime type
					$expiry_key = "default";

					//get expiry time
					$expiry_time = explode("\"", str_replace("access plus", "", $plx_portal_line));
					$block_caching_array[$expiry_key] = $expiry_time[1];

				} //END check if this is ExpiresDefault

				//check if this is ExpiresByType
				if (strpos($plx_portal_line, 'ExpiresByType') !== false) {

					//get mime type
					$mime_type = explode(" ", str_replace("ExpiresByType ", "", $plx_portal_line));
					$expiry_key = preg_replace("/[^a-zA-Z0-9]/", "", $mime_type[2]);

					//get expiry time
					$expiry_time = explode("\"", str_replace("access plus", "", $plx_portal_line));
					$block_caching_array[$expiry_key] = $expiry_time[1];

				} //END check if this is ExpiresByType

			} //END page caching block

      //site url block
			if ($current_block == 'siteurl') {

				//create site url block array
				array_push($block_siteurl_array, $plx_portal_line);

			} //END site url block

      //compression block
			if ($current_block == 'compression') {

				//create value
				$compression_value = str_replace("AddOutputFilterByType DEFLATE ", "", $plx_portal_line);

				//create key
				$compression_key = preg_replace("/[^a-zA-Z0-9]/", "", $compression_value);

				//create compression block array
				$block_compression_array[$compression_key] = $compression_value;

			} //END compression block

      //keep-alive block
			if ($current_block == 'keepalive') {

				//create keep-alive block array
				array_push($block_keepalive_array, $plx_portal_line);

			} //END keep-alive block

      //custom before rewriterules block
			if ($current_block == 'custombefore') {

				//create custom before rewriterules block array
				array_push($block_custombefore_array, $plx_portal_line);

			} //END custom before rewriterules block

      //custom before rewriterules block
			if ($current_block == 'customafter') {

				//create custom before rewriterules block array
				array_push($block_customafter_array, $plx_portal_line);

			} //END custom before rewriterules block

			//check if this is the start of the redirects block
			if (strpos($plx_portal_line, '# BEGIN Redirects') !== false) { $current_block = 'redirects'; }

      //check if this is the start of the caching block
			if (strpos($plx_portal_line, '# BEGIN Page Caching') !== false) { $current_block = 'caching'; }

      //check if this is the start of the site url block
			if (strpos($plx_portal_line, '# BEGIN Site URL') !== false) { $current_block = 'siteurl'; }

			//check if this is the start of the compression block
			if (strpos($plx_portal_line, '# BEGIN Compression') !== false) { $current_block = 'compression'; }

			//check if this is the start of the keep-alive block
			if (strpos($plx_portal_line, '# BEGIN Keep-alive') !== false) { $current_block = 'keepalive'; }

      //check if this is the start of the custom before rewriterules block
			if (strpos($plx_portal_line, '# BEGIN Custom Before') !== false) { $current_block = 'custombefore'; }

      //check if this is the start of the custom after rewriterules block
			if (strpos($plx_portal_line, '# BEGIN Custom After') !== false) { $current_block = 'customafter'; }

		} //END loop through array of content

		//set expiry types (mimetype => ("mime/type", "array id in block")
		$expiry_types = array(
			"default" => "default",
			"applicationjavascript" => "application/javascript",
			"applicationjson" => "application/json",
			"applicationrssxml" => "application/rss+xml",
			"applicationvndmsfontobject" => "application/vnd.ms-fontobject",
			"applicationxfontwoff" => "application/x-font-woff",
			"applicationxml" => "application/xml",
			"audioogg" => "audio/ogg",
			"fontopentype" => "font/opentype",
			"fonttruetype" => "font/truetype",
			"imagegif" => "image/gif",
			"imagejpg" => "image/jpg",
			"imagejpeg" => "image/jpeg",
			"imagesvgxml" => "image/svg+xml",
			"imagepng" => "image/png",
			"imagexicon" => "image/x-icon",
			"textcachemanifest" => "text/cache-manifest",
			"textcss" => "text/css",
			"texthtml" => "text/html",
			"textjavascript" => "text/javascript",
			"textxml" => "text/xml",
			"videomp4" => "video/mp4",
			"videoogg" => "video/ogg",
			"videowebm" => "video/webm",
		);

		//set expiry times
		$expiry_times = array(
			"0 seconds", "30 seconds", "1 minute", "5 minutes", "10 minutes", "15 minutes", "20 minutes", "30 minutes", "45 minutes", "1 hour", "2 hours", "3 hours", "4 hours", "5 hours", "6 hours", "7 hours", "8 hours", "9 hours", "10 hours", "11 hours", "12 hours", "13 hours", "14 hours", "15 hours", "16 hours", "17 hours", "18 hours", "19 hours", "20 hours", "21 hours", "22 hours", "23 hours", "1 day", "2 days", "3 days", "4 days", "5 days", "6 days", "1 week", "2 weeks", "3 weeks", "4 weeks", "1 month", "2 months", "3 months", "4 months", "5 months", "6 months", "7 months", "8 months", "9 months", "10 months", "11 months", "1 year", "2 years", "3 years", "4 years", "5 years",
		);
?>
	<form method="post">
		<div class="wrap">
			<h2>File Editor</h2>
			<h3>.htaccess</h3>
			<ul class="nav-tab-wrapper" style="border-bottom:1px solid #ccc; padding-top: 0;">
				<li>
		    	<a href="javascript:portalChangeTab(1);" id="plx-portal-nav-1" class="nav-tab nav-tab-active">Redirects</a>
				</li>
				<li>
		    	<a href="javascript:portalChangeTab(2);" id="plx-portal-nav-2" class="nav-tab">Page Caching</a>
				</li>
				<li>
		    	<a href="javascript:portalChangeTab(3);" id="plx-portal-nav-3" class="nav-tab">Site Settings</a>
				</li>
        <li>
		    	<a href="javascript:portalChangeTab(4);" id="plx-portal-nav-4" class="nav-tab">Custom</a>
				</li>
		  </ul>
      <table class="form-table tab-content" id="plx-portal-tab-1">
        <tr valign="top">
        	<th scope="row">RewriteRules</th>
					<td>
            <?php
            //rewriterules
            $RewriteRules = '';

            //loop through rewrite rules
            foreach ($block_redirects_array as &$rewriterule) {

              //output rewriterule
              $RewriteRules .= $rewriterule . PHP_EOL;

            } //END loop through rewrite rules
            ?>
						<textarea name="_plx_portal_file_htaccess_rewriterules" id="_plx_portal_file_htaccess_rewriterules" class="regular-text plx-full-width" rows="20"><?php echo $RewriteRules; ?></textarea>
            <p class="description">You should comment all new sets of RewriteRules in the following format: <code># DD/MM/YYYY - Full Name</code></p>
          </td>
        </tr>
      </table>
      <table class="form-table tab-content hidden" id="plx-portal-tab-2">
	      <?php
		    //loop through expiry types
		    foreach ($expiry_types as $type_key => &$type_value) {
		    ?>
        <tr valign="top">
	        <th scope="row"><?php echo $type_value; ?></th>
					<td>
						<select name="_plx_portal_file_htaccess_mime_<?php echo $type_key; ?>" id="_plx_portal_file_htaccess_mime_<?php echo $type_key; ?>"<?php if (!array_key_exists($type_key, $block_caching_array)) { ?> disabled="disabled" <?php } ?>>
							<?php
							//loop through expiry times
							foreach ($expiry_times as &$time_value) {
							?>
							<option value="<?php echo $time_value; ?>"<?php if (array_key_exists($type_key, $block_caching_array) && strtotime($block_caching_array[$type_key]) == strtotime($time_value)) { ?> selected="selected"<?php } ?>>access plus <?php echo $time_value; ?></option>
							<?php
							} //END loop through expiry times
							?>
						</select>
					</td>
        </tr>
        <?php
	      } //END loop through expiry types
	      ?>
      </table>
      <table class="form-table tab-content hidden" id="plx-portal-tab-3">
        <tr valign="top">
        	<th scope="row">Default Site URL</th>
					<td>
						<?php
						//regex for matching urls
						$url_regex = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}?/";

						//get url from RewriteRule
						preg_match($url_regex, $block_siteurl_array[3], $site_url);
						?>
						<input type="text" name="_plx_portal_file_htaccess_siteurl" id="_plx_portal_file_htaccess_siteurl" class="regular-text" value="<?php echo $site_url[0]; ?>">
            <p class="description">All other domains pointing to this site will be re-directed to what you set here</p>
					</td>
        </tr>
        <tr valign="top">
        	<th scope="row">Gzip  Compression</th>
					<td>
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_applicationjavascript" id="_plx_portal_file_htaccess_gzip_applicationjavascript" value="1"<?php if (array_key_exists('applicationjavascript', $block_compression_array)) { ?> checked="checked"<?php } ?>> application/javascript</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_applicationrssxml" id="_plx_portal_file_htaccess_gzip_applicationrssxml" value="1"<?php if (array_key_exists('applicationrssxml', $block_compression_array)) { ?> checked="checked"<?php } ?>> application/rss+xml</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_applicationxjavascript" id="_plx_portal_file_htaccess_gzip_applicationxjavascript" value="1"<?php if (array_key_exists('applicationxjavascript', $block_compression_array)) { ?> checked="checked"<?php } ?>> application/x-javascript</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_applicationxhtmlxml" id="_plx_portal_file_htaccess_gzip_applicationxhtmlxml" value="1"<?php if (array_key_exists('applicationxhtmlxml', $block_compression_array)) { ?> checked="checked"<?php } ?>> application/xhtml+xml</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_applicationxml" id="_plx_portal_file_htaccess_gzip_applicationxml" value="1"<?php if (array_key_exists('applicationxml', $block_compression_array)) { ?> checked="checked"<?php } ?>> application/xml</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_textcss" id="_plx_portal_file_htaccess_gzip_textcss" value="1"<?php if (array_key_exists('textcss', $block_compression_array)) { ?> checked="checked"<?php } ?>> text/css</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_texthtml" id="_plx_portal_file_htaccess_gzip_texthtml" value="1"<?php if (array_key_exists('texthtml', $block_compression_array)) { ?> checked="checked"<?php } ?>> text/html</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_textplain" id="_plx_portal_file_htaccess_gzip_textplain" value="1"<?php if (array_key_exists('textplain', $block_compression_array)) { ?> checked="checked"<?php } ?>> text/plain</label><br />
						<label><input type="checkbox" name="_plx_portal_file_htaccess_gzip_textxml" id="_plx_portal_file_htaccess_gzip_textxml" value="1"<?php if (array_key_exists('textxml', $block_compression_array)) { ?> checked="checked"<?php } ?>> text/xml</label>
					</td>
        </tr>
        <tr valign="top">
        	<th scope="row">Headers</th>
					<td>
						<label><input type="checkbox" name="_plx_portal_file_htaccess_keepalive" id="_plx_portal_file_htaccess_keepalive" value="1"<?php if (!empty($block_keepalive_array)) { ?> checked="checked"<?php } ?>>Connection keep-alive</label>
					</td>
        </tr>
      </table>
      <table class="form-table tab-content hidden" id="plx-portal-tab-4">
        <tr valign="top">
        	<th scope="row">Before RewriteRules</th>
					<td>
						<textarea name="_plx_portal_file_htaccess_custom" id="_plx_portal_file_htaccess_custom" class="regular-text plx-full-width" rows="10"></textarea>
					</td>
        </tr>
        <tr valign="top">
        	<th scope="row">After RewriteRules</th>
					<td>
						<textarea name="_plx_portal_file_htaccess_custom" id="_plx_portal_file_htaccess_custom" class="regular-text plx-full-width" rows="10"></textarea>
					</td>
        </tr>
      </table>
      <?php
      wp_nonce_field('update_file_htaccess', '_plx_portal_file_htaccess_nonce');
			submit_button();
			?>
		</div>
	</form>
<?php
	} else {
    //read data from htaccess file
    $plx_portal_htaccess_content = file_get_contents(ABSPATH . '.htaccess');
?>
	<form method="post">
		<div class="wrap">
			<h2>File Editor</h2>
			<table class="form-table">
        <tr valign="top">
        	<th scope="row">.htaccess</th>
					<td>
						<textarea name="_plx_portal_file_htaccess" id="_plx_portal_file_htaccess" class="regular-text plx-full-width" rows="30"><?php echo$plx_portal_htaccess_content; ?></textarea>
					</td>
        </tr>
      </table>
      <input type="hidden" name="_plx_portal_file_htaccess_type" id="_plx_portal_file_htaccess_type" value="unmanaged">
      <?php
      wp_nonce_field('update_file_htaccess', '_plx_portal_file_htaccess_nonce');
			submit_button();
			?>
		</div>
	</form>
<?php
	} //END check if htaccess is managed by PLX Portal Connector

} //END Create plugin configuration page
