=== PLX Portal Connector ===
Contributors: mattstone-plx
Tags: portal, purplex, plx, ascotgroup
Requires at least: 4.3
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 1.0.6
License: MIT
License URI: https://opensource.org/licenses/MIT

Connects your WordPress site with the Portal system.

== Description ==
This plugin allows your WordPress site to interact with the Portal system via it's API providing functionality such as centralised Web Content management. To use this plugin you will need an existing Portal account in order to setup the required API Key to connect.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/plx-portal` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. After successful activation go to the Portal->General page to start configuring your plugin.

== Screenshots ==
1. Portal Settings view
2. Web Content - Content list view
3. Web Content - Add New Web Content view
4. Web Content - Content Shortcode setup view

== Changelog ==
= 1.0.0 =
* Initial release

= 1.0.1 =
* Fixed issue where existing registered shortcodes were not being processed when outputting Web Content

= 1.0.2 =
* Changed Purplex icon to appear on the Portal menu rather than replacing the WordPress icon
* Fixed issue where styles were being enqueued on front-end when not logged in

= 1.0.3 =
* Fixed CSS selector issue when applying the Purplex icon

= 1.0.4 =
* Tested with WordPress 4.9.7
* Fixed CSS selector issue when applying the Purplex icon (again!)

= 1.0.5 =
* Tested with WordPress 4.9.8
* Fixed minor PHP notices that were visible when WP_DEBUG enabled

= 1.0.6 =
* Fixed bug with Web Content where field attributes were unexpectedly deleted when updating content using Update button