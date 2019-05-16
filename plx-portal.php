<?php
/**
* Plugin Name: PLX Portal Connector
* Description: Allows the Portal system to connect your WordPress site and provides some useful content management tools
* Version: 1.1.2
* Author: Purplex
* Author URI: http://plx.mk
* License: GPL3
*/

/*

												 TM
████████╗██╗     ███╗   ███╗
██╔═══██║██║      ███╗ ███╔╝
████████║██║       ██████╔╝
██╔═════╝██║      ███╔╝███╗
██║      ███████╗███╔╝  ███╗
╚═╝      ╚══════╝╚══╝   ╚══╝
    POWER YOUR WORDPRESS
       http://plx.mk

*/

// Set the path to this plugin
define('PLX_PORTAL_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('PLX_PORTAL_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('PLX_PORTAL_PLUGIN_URL', plugin_dir_url( __FILE__ ));

// Set other file paths used in the plugin
define('PLX_PORTAL_FILE_HTACCESS_PATH', ABSPATH . '.htaccess');

// Pull in the parts of the plugin
require_once(PLX_PORTAL_PLUGIN_PATH . 'includes/maintenance-mode.inc.php');
require_once(PLX_PORTAL_PLUGIN_PATH . 'includes/page-options.inc.php');
require_once(PLX_PORTAL_PLUGIN_PATH . 'includes/snippets.inc.php');
require_once(PLX_PORTAL_PLUGIN_PATH . 'includes/tinymce-plugins.inc.php');
require_once(PLX_PORTAL_PLUGIN_PATH . 'includes/web-content.inc.php');

// Function to add Purplex icon to Wordpress admin
function plx_icon() {
	wp_register_style('plx_icon', PLX_PORTAL_PLUGIN_URL . 'css/plx-icon-styles.css');
	wp_enqueue_style('plx_icon');
}
add_action('admin_enqueue_scripts', 'plx_icon');
//add_action('login_enqueue_scripts', 'plx_icon');
//add_action('wp_enqueue_scripts', 'plx_icon');

// Function to load admin scripts
function plx_portal_admin_scripts() {

  $html = '
  <script type="text/javascript">
		function portalChangeTab(tabId) {
			jQuery(".tab-content").hide();
			jQuery("#plx-portal-tab-" + tabId).show();
			jQuery(".nav-tab").removeClass("nav-tab-active");
			jQuery("#plx-portal-nav-" + tabId).addClass("nav-tab-active");
		}
  	jQuery(document).ready(function() {
			jQuery(".post-type-plx_web_content #post-body-content").css("margin-bottom", 0);
		});
  </script>
  ';

  echo $html;
}
add_action('admin_footer', 'plx_portal_admin_scripts');

// Create plugin menu items
function plx_portal_plugin_menu() {
	add_menu_page('PLX', 'PLX', 'manage_options', 'plx-portal', '', 'none');
	add_submenu_page('plx-portal', 'General Settings', 'General', 'manage_options', 'plx-portal', 'plx_portal_page');
	add_submenu_page('plx-portal', 'Snippets', 'Snippets', 'manage_options', 'edit.php?post_type=plx_snippet');
	add_submenu_page('plx-portal', 'Web Content', 'Web Content', 'manage_options', 'edit.php?post_type=plx_web_content');
}
add_action('admin_menu', 'plx_portal_plugin_menu');

// Function to send post requests to the Portal API
function plx_portal_api($APIKey = null, $RequestCall = 'check/', $PostArray = array('foo' => 'bar')) {

	//add api key to PostArray
	$PostArray['api_key'] = $APIKey;

	//set api url
	$plx_api_url = "https://portal.ascotgroup.co.uk/api/" . $RequestCall;

	//send request using wordpress http api
	$plx_portal_result = wp_remote_post( $plx_api_url, array( 'body' => $PostArray) );

	//check if the api request was successful
	if ( !(is_wp_error($plx_portal_result)) ) {

		//return result
		return $plx_portal_result['body'];

	} else {

		//create response array
		$response_array = array(
			'request_status' => 0,
			'request_data' => "API is currently offline, try again later"
		);

		//json encode the response
		$plx_portal_result_error = json_encode($response_array);

		//return result (error)
		return $plx_portal_result_error;

	} //END check if the api post request was successful

} //END Function to send post requests to the Portal API

// Function that creates universal short code for portal modules
function plx_portal_register_shortcode($atts, $content = null) {

	$a = shortcode_atts( array(
		'type' 		=> 'helloworld',
		'id'			=> null,
	), $atts );

	$plx_portal_type 		= esc_attr($atts['type']);
	$plx_portal_id 			= esc_attr($atts['id']);

	if ($plx_portal_type == 'helloworld') {

		$html = "Hello World!";
		return $html;

	} else if ($plx_portal_type == 'webcontent') {

		$html = plx_portal_output_web_content($atts);
		return $html;

	}

}
add_shortcode('plxportal', 'plx_portal_register_shortcode');
