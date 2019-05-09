<?php
// function to check maintenance mode on the portal
function plx_portal_check_maintenance_mode() {

  //set api key for call
  $plx_api_key = get_option('_plx_portal_api_key');

  //set current website url
  $plx_domain = str_replace(array('http://', 'https://'), '', home_url());

  //set domain of this website
  $plx_post_array = array(
    'type' => 'maintenance',
    'domain' => $plx_domain
  );

  //make call to api
  $plx_api_result = plx_portal_api($plx_api_key, 'v1/hosting', $plx_post_array);

  //decode response
  $plx_api_response = json_decode($plx_api_result, true);

  //check if this was a valid connector key
  if ($plx_api_response['request_status'] == 1) {

    //check if site hold is enabled
    if (rawurldecode($plx_api_response['request_data']['app_sitehold']) == 1) {

      //set site hold
      $plx_site_hold = 1;

      //set site hold message
      $plx_site_hold_msg = "This site has been disabled for editing by " . rawurldecode($plx_api_response['request_data']['app_siteholdby']);

    } else {

      //set site hold
      $plx_site_hold = 0;

      //set site hold message
      $plx_site_hold_msg = "";

    } //check if site hold is enabled

    //update the site hold status and message
    update_option('_plx_portal_maintenance_mode', $plx_site_hold);
    update_option('_plx_portal_maintenance_mode_msg', $plx_site_hold_msg);

  } //END check if this was a valid api key

}
add_action('admin_head', 'plx_portal_check_maintenance_mode', 1);

// Function to load maintenance styles
function plx_portal_maintenance_styles() {

  //get maintenance mode and message
  $plx_maintenance_mode = get_option('_plx_portal_maintenance_mode');
  $plx_maintenance_mode_msg = get_option('_plx_portal_maintenance_mode_msg');

  $html = '
  <style>
    #wpadminbar {
      z-index: 99998;
    }
    .plx-overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.7);
      z-index: 99999;
    }
      .plx-overlay .plx-maintenance-msg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        -ms-transform: translate(-50%,-50%);
        text-align: center;
      }
        .plx-overlay .plx-maintenance-msg h1, .plx-overlay .plx-maintenance-msg p {
          color: #fff;
        }
  </style>
  <div class="plx-overlay">
    <div class="plx-maintenance-msg">
      <h1>Site Hold/Being Worked On</h1>
      <p>' . $plx_maintenance_mode_msg . '<p>
    </div>
  </div>
  ';

  //check if maintenance mode is enabled
  if ($plx_maintenance_mode == 1) {
    echo $html;
  }
}

//add maintenance notice and styles
add_action('admin_head', 'plx_portal_maintenance_styles', 2);
