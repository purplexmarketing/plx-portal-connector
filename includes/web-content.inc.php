<?php
// Register the Web Content Post Type
function plx_register_cpt_plx_web_content() {

  $labels = array(
    'name' 									=> _x( 'Web Content', 'plx_web_content' ),
    'singular_name' 				=> _x( 'Web Content', 'plx_web_content' ),
    'add_new' 							=> _x( 'Add Content', 'plx_web_content' ),
    'add_new_item' 					=> _x( 'Add New Content', 'plx_web_content' ),
    'edit_item' 						=> _x( 'Edit Content', 'plx_web_content' ),
    'new_item' 							=> _x( 'New Content', 'plx_web_content' ),
    'view_item' 						=> _x( 'View Content', 'plx_web_content' ),
    'search_items' 					=> _x( 'Search Content', 'plx_web_content' ),
    'not_found' 						=> _x( 'No content found', 'plx_web_content' ),
    'not_found_in_trash' 		=> _x( 'No content found in Trash', 'plx_web_content' ),
    'parent_item_colon' 		=> _x( 'Parent Content:', 'plx_web_content' ),
    'menu_name'							=> _x( 'Web Content', 'plx_web_content' ),
    'name_admin_bar'				=> _x( 'Content', 'plx_web_content' ),
    'all_items' 						=> _x( 'All Content', 'plx_web_content'),
  );

  $args = array(
    'labels' 								=> $labels,
    'hierarchical' 					=> false,
    'supports' 							=> false,
    'public' 								=> false,
    'show_ui' 							=> true,
    'show_in_menu' 					=> false,
    'show_in_nav_menus'			=> false,
    'publicly_queryable' 		=> true,
    'exclude_from_search' 	=> true,
    'has_archive' 					=> false,
    'query_var' 						=> true,
    'can_export' 						=> false,
    'capability_type' 			=> 'post',
    'register_meta_box_cb'	=> 'plx_portal_web_content_metaboxes',
  );

  register_post_type( 'plx_web_content', $args );

}
add_action( 'init', 'plx_register_cpt_plx_web_content' );

// Create metabox for Main Content
function plx_portal_web_content_metabox_main() {
	global $post;

	if ( !($post->post_excerpt == '') ) {
		$html = '
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label">Title</label>
		</p>
		<input name="_plx_portal_web_content_title" type="text" style="width: 100%;" readonly="readonly" value="' . $post->post_title . '">
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label">Content</label>
		</p>
		<textarea name="_plx_portal_web_content_content" type="text" style="width: 100%;" rows="15" readonly="readonly">' . $post->post_content . '</textarea>
		<p>You can edit this content by logging into the Portal and browsing to <strong>Web</strong> &gt; <strong>Content</strong></p>
		';
	} else {
		$html = '
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label" for="_plx_portal_web_content_key">Connector Key</label>
		</p>
		<input name="_plx_portal_web_content_key" type="text" id="_plx_portal_web_content_key" style="width: 290px;" value="">
		<p>You can find your Connector Keys by logging into the Portal and browsing to <strong>Web</strong> &gt; <strong>Content</strong></p>
		';
	}
	$html .= '
		<input type="hidden" name="plx_meta_noncename" id="plx_meta_noncename" value="' . wp_create_nonce( PLX_PORTAL_PLUGIN_BASENAME ) . '" />
	';
	echo $html;
}

// Create metabox for Shortcodes
function plx_portal_web_content_metabox_side() {
	global $post;

	if ( !($post->post_excerpt == '') ) {
		$html = '
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label">Fields</label>
		</p>
		';
		$shortcode_array = explode(" ", $post->post_excerpt);

		foreach ($shortcode_array as &$shortcode) {
			$shortcode_text = str_replace("[", "", $shortcode);
			$shortcode_text = str_replace("]", "", $shortcode_text);
			$shortcode_name = str_replace("-", "_", $shortcode_text);

			if (!($shortcode_meta_value = get_post_meta($post->ID, '_plx_portal_web_content_shortcode_' . $shortcode_name, true))) {
				$shortcode_meta_value = $shortcode;
			}

			$html .= '
			<input name="_plx_portal_web_content_shortcode_' . $shortcode_name . '" type="text" style="width: 100%;" value="' . $shortcode_meta_value . '" placeholder="' . $shortcode_text . '">
			';
		}

		$html .= '
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label" for="_plx_portal_web_content_key">Connector Key</label>
		</p>
		<input name="_plx_portal_web_content_key" type="text" id="_plx_portal_web_content_key" style="width: 100%;" onmouseup="this.select();" readonly="readonly" value="' . $post->post_name . '">
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label" for="_plx_portal_web_content_shortcode">Shortcode</label>
		</p>
		<input name="_plx_portal_web_content_shortcode" type="text" id="_plx_portal_web_content_shortcode" style="width: 100%;" onmouseup="this.select();" readonly="readonly" value="[plxportal type=webcontent id=' . $post->ID . ']">
		';
	} else {
		$html = '<p>Add your Connector Key to get started</p>';
	}
	echo $html;
}

// Add metaboxes to web content post type
function plx_portal_web_content_metaboxes() {
	add_meta_box('plx_portal_web_content_connector_main', 'Web Content', 'plx_portal_web_content_metabox_main', 'plx_web_content', 'normal', 'high');
	add_meta_box('plx_portal_web_content_connector_side', 'Fields &amp; Attributes', 'plx_portal_web_content_metabox_side', 'plx_web_content', 'side', 'low');
}

// Download Web Content and populate Wordpress fields with data
function plx_download_and_inject_web_content($data, $postarr) {

  //check this is the plx_web_content post type
  if ($data['post_type'] == 'plx_web_content' && !($data['post_status'] == 'auto-draft')) {

	  //set api key for call
		$plx_api_key = get_option('_plx_portal_api_key');

		//check if the connector key has been posted
		if (isset($_POST['_plx_portal_web_content_key'])) {

			//clean up web connector key
			$plx_connector_key = preg_replace("/[^0-9a-z.]/", "", $_POST['_plx_portal_web_content_key']);

		} else {

			//get connector key
			$plx_connector_key = $data['post_name'];

		} //END check if the connector key has been posted

		//set connector key as post value
		$plx_post_array = array('hash' => $plx_connector_key);

	  //make call to api
	  $plx_api_result = plx_portal_api($plx_api_key, 'v1/hosting', $plx_post_array);

	  //decode response
		$plx_api_response = json_decode($plx_api_result, true);

	  //check if this was a valid connector key
		if ($plx_api_response['request_status'] == 1) {

			//data values
			$data['post_title'] = rawurldecode($plx_api_response['request_data']['webcontent_title']);
			$data['post_content'] = rawurldecode($plx_api_response['request_data']['webcontent_content']);
			$data['post_date'] = rawurldecode($plx_api_response['request_data']['webcontent_dateupdated']);
			$data['post_excerpt'] = rawurldecode($plx_api_response['request_data']['webcontent_shortcodes']);;
			$data['post_name'] = $plx_connector_key;

		} else {

			//data values
			$data['post_title'] = 'Invalid Connector Key: ' . $plx_connector_key;
			$data['post_status'] = 'trash';
			$data['post_name'] = $plx_connector_key;

		} //END check if this was a valid api key

  } //END check this is the plx_web_content post type

  //return modified data
  return $data;
}
add_filter('wp_insert_post_data', 'plx_download_and_inject_web_content', 99, 2);

// Save field attributes
function plx_field_attributes_metabox($post_id, $post) {

	//check this is the web content post type
	if (!(get_post_type($post_id) == 'plx_web_content')) {
		return $post->ID;
	}

	//check the nonce value wasn't invalid
	if ( isset($_POST['plx_meta_noncename']) && !wp_verify_nonce( $_POST['plx_meta_noncename'], PLX_PORTAL_PLUGIN_BASENAME )) {
		return $post->ID;
	}

	//check this isn't an autosave
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return $post->ID;
  }

  //check this user can edit
	if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}

	//turn shortcodes into an array
	$field_array = explode(" ", $post->post_excerpt);

	//check if the array contains fields
	if (!empty($field_array)) {

		//loop through each shortcode
		foreach ($field_array as &$field_value) {

			//breakdown shortcode into the field name suffix
			$field_value_text = str_replace("[", "", $field_value);
			$field_value_text = str_replace("]", "", $field_value_text);
			$field_value_name = str_replace("-", "_", $field_value_text);

			//check field value name contained content
			if (!($field_value_name == '') && isset($_POST['_plx_portal_web_content_shortcode_' . $field_value_name])) {

				//check the field is not the shortcode value (we don't want to save the value if it is)
				if (!($field_value == $_POST['_plx_portal_web_content_shortcode_' . $field_value_name])) {

					//add field value to the meta array
					$add_field_meta['_plx_portal_web_content_shortcode_' . $field_value_name] = $_POST['_plx_portal_web_content_shortcode_' . $field_value_name];

				} else {

					//delete post meta item
					delete_post_meta($post->ID, '_plx_portal_web_content_shortcode_' . $field_value_name);

				} //END check the field is not the shortcode value (we don't want to save the value if it is)

			} //END check field value name contained content

		} //END loop through each shortcode

	} //END check if the array contains fields

	//check if the meta array exists
	if (isset($add_field_meta) && is_array($add_field_meta)) {

		//loop through the field values in the meta array
		foreach ($add_field_meta as $key => $value) {

			//check the post_type is a revision
			if ($post->post_type == 'revision') {

				//return control back to function
				return;

			} //END check the post type is a revision

			//check if this post meta item already exists
			if (get_post_meta($post->ID, $key, false)) {

				//update post meta item
				update_post_meta($post->ID, $key, $value);

			} else {

				//add new post meta item
				add_post_meta($post->ID, $key, $value);

			} //END check if this post meta item already exists

			//check if the value is empty
			if (!$value) {

				//delete post meta item
				delete_post_meta($post->ID, $key);

			} //END check if the value is empty

		} //END loop through the field values in the meta array

	} //END check if the array exists

}
add_action('save_post', 'plx_field_attributes_metabox', 1, 2);

// Function to output web content
function plx_portal_output_web_content($atts) {

	//set update array
	$plx_update_array = array(
		'ID' => $atts['id']
	);

	//update post (to be replaced by ajax call)
	wp_update_post($plx_update_array);

	//get web content and shortcodes
	$webcontent_content = get_post_field('post_content', $atts['id']);
	$webcontent_shortcodes_array = explode(" ", get_post_field('post_excerpt', $atts['id']));

	//loop through shortcodes array
	foreach ($webcontent_shortcodes_array as &$shortcode) {

		//breakdown shortcode into the field name suffix
		$shortcode_name = str_replace("[", "", $shortcode);
		$shortcode_name = str_replace("]", "", $shortcode_name);
		$shortcode_name = str_replace("-", "_", $shortcode_name);

		//check if there is a meta value for this field
		if ($shortcode_content = get_post_meta($atts['id'], '_plx_portal_web_content_shortcode_' . $shortcode_name, true)) {

			//replace shortcode with specified content
			$webcontent_content = str_replace($shortcode, $shortcode_content, $webcontent_content);

		} //END check if there is a meta value for this field

	} //END loop through shortcodes array

	//return the content
	return do_shortcode($webcontent_content);
}
