<?php
/**
 * My Plugin
 *
 * @package     my-plugin
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name: My Plugin
 * Plugin URI:  https://github.com/jeff-russ/
 * Description: 
 * Version:     0.1
 * Author:      Jeff Russ
 * Author URI:  https://github.com/jeff-russ/
 * Text Domain: jp-dribbble
 * License:     GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

include_once 'jeffpack-core/WpPlugin.php';

// include_once 'jeffpack-core/php_classes/HelperModule.php';
// include_once 'jeffpack-core/php_classes/PersistArgs.php';
// include_once 'jeffpack-core/wp_classes/PluginData.php';
// include_once 'jeffpack-core/wp_classes/SettingsHelper.php';
// include_once 'jeffpack-core/wp_classes/WpPlugin.php';


// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

// add_action( 'activated_plugin', function( $plugin, $network_activation ) {
// 	add_filter('admin_footer_text', function () use ($plugin) { echo "$plugin | "; });
	
// }, 10, 2 ); # priority, number of args for callback


// add_filter('admin_footer_text', function () {
// 	echo "$plugin | ";
// });

// delete_option('my_options_name');
// delete_option('prefix_option_name');
// delete_option('JR_settings_option_name');
// delete_option('JR_settings_option_name_2');
// delete_option('my-plugin_menu_option_name');
// delete_option('my_plugin_menu_option_name');
// delete_option('_menu_option_name');
// delete_option('myplugin_menu_option_name');

class PluginBase extends WpPlugin {}

$my_plugin = new PluginBase();


$return = $my_plugin
	->addSettingsPage("menu", "PAGE", "MENU", $out)
	->addSettingsSection("Section name", $out)
	->addSetting("Setting1", "Setting1def");

// $my_plugin->addSettingsSection("Section2");
// $my_plugin->addSetting("Setting2", "Setting3def", '<input type="text" name="$name" value="$value">');
// $my_plugin->addSetting("Setting3", "Setting3def", function($args) {
// 	extract($args);
// 	echo $setting_id;
// });

add_action( 'wp_dashboard_setup', function() use ($out)
{
	wp_add_dashboard_widget( # arg1 is css class and key in db.
	'get-dribdble', __( 'My Plugin' ), function() use ($out)
	{
		echo "<h3>Hello from My Plugin</h3>";


		// echo var_dump(basename(__FILE__, ".php"));
		echo var_dump($out);
		// echo $my_plugin->logs['info'];
		// echo $my_plugin->logs['GLOBALS'];
		// echo $my_plugin->logs['settings_pages'];
	});
});
