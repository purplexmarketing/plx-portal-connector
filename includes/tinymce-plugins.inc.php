<?php
// Create TinyMCE add table button
function plx_portal_add_the_table_button($buttons) {
  array_push( $buttons, 'separator', 'table' );
  return $buttons;
}
add_filter( 'mce_buttons', 'plx_portal_add_the_table_button' );

// Add the TinyMCE table plugin based on the current version of WordPress
function plx_portal_add_the_table_plugin( $plugins ) {
  global $wp_version;

  //set default version of tinymce
  $tinymce_version = '4.3.8';

  if ( version_compare( $wp_version, '4.5', '>=' ) && version_compare( $wp_version, '4.5.1', '<' ) ) {
    $tinymce_version = '4.3.8';
  } else if ( version_compare( $wp_version, '4.5.1', '>=' ) && version_compare( $wp_version, '4.6', '<' ) ) {
    $tinymce_version = '4.3.10';
  } else if ( version_compare( $wp_version, '4.6', '>=' ) && version_compare( $wp_version, '4.7', '<' ) ) {
    $tinymce_version = '4.4.1';
  } else if ( version_compare( $wp_version, '4.7', '>=' ) && version_compare( $wp_version, '4.8', '<' ) ) {
    $tinymce_version = '4.5.6';
  } else if ( version_compare( $wp_version, '4.8', '>=' ) && version_compare( $wp_version, '5.1.1', '<' ) ) {
    $tinymce_version = '4.6.2';
  } else if ( version_compare( $wp_version, '4.8', '>=' ) ) {
    $tinymce_version = '4.9.2';
  }

  $plugins['table'] = PLX_PORTAL_PLUGIN_URL . 'js/tinymce-plugins/table/' . $tinymce_version . '/plugin.min.js';
  return $plugins;
}
add_filter( 'mce_external_plugins', 'plx_portal_add_the_table_plugin' );
