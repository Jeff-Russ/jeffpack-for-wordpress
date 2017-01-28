<?php
/**
 * Jeffpack for WordPress
 *
 * @package     Jeffpack for WordPress
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 *
 * @jeffpack
 * Plugin Name: Jeffpack for WordPress
 * Description: Multi-Purpose Plugin/Theme Collection
 * Plugin URI: http://github.com/jeff-russ/jeffpack-for-wordpress
 * Author: Jeff Russ
 * Author URI: http://github.com/jeff-russ
 * Text Domain: jeffpack
 * Version:     0.1
 * License:     GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

include_once 'lib/include.php';
// include_once 'jeffpack-include/jeffpack-lib/z_old/scss.inc.php'; // Sass Compiler (vendor)
// include_once 'jeffpack-include/jeffpack-lib/jr_wp_lib/WpScss.php'; // Compiling Manager
// include_once 'jeffpack-plugin/include.php'; // Compiling Manager

// include_once 'jeffpack-plugin/JpScssSettings.php'; // Options page class

$jp_plugin = new JeffPack();
if( is_admin() ) {
  $jp_plugin->addScssOptionsPage();
  include_once 'lib/wp-scss.php';
}

add_action( 'wp_dashboard_setup', function() use ($jp_plugin)
{
  wp_add_dashboard_widget( # arg1 is css class and key in db.
  'jeffpack-main', 'Jeffpack', function() use ($jp_plugin)
  {
    echo "<h3>Hello from My Thingsss</h3>";
    echo $jp_plugin->info['theme']['dir_path'];
    // echo var_dump($jp_plugin->info['admin_menu']);
  //   echo var_dump($jp_plugin->info['settings_pages']);
  //   // echo "WPSCSS_PLUGIN_DIR: " . WPSCSS_PLUGIN_DIR;
  //   // echo $jp_plugin->logs['info'];
  //   // echo $jp_plugin->logs['GLOBALS'];
  //   // echo $jp_plugin->logs['settings_pages'];
  });
});



// add_action( 'activated_plugin', function( $plugin, $network_activation ) {
// 	add_filter('admin_footer_text', function () use ($plugin) { echo "$plugin | "; });
// }, 10, 2 ); # priority, number of args for callback

// add_filter('admin_footer_text', function () {echo "$plugin | "; });

// delete_option('my_options_name');


// $jp_plugin->addEmptyMenu(['menu_title' => "Menu I"]);

$menu_slug = $jp_plugin
  ->addAdminMenu()
  ->addAdminPage("Subpage1", "Submenu1")
  ->addSetting("Setting1", "Setting1def")
  ->addAdminPage("Subpage2", "Submenu2")
  ->addSetting("Setting2", "Setting3def");



// $jp_plugin
// 	->addSettingsPage("menu", "PAGE", "MENU", $out)
// 	->addSettingsSection("Section name", $out)
// 	->addSetting("Setting1", "Setting1def");

// $jp_plugin->addSettingsSection("Section2");
// $jp_plugin->addSetting("Setting2", "Setting3def", '<input type="text" name="$name" value="$value">');
// $jp_plugin->addSetting("Setting3", "Setting3def", function($args) {
// 	extract($args);
// 	echo $setting_id;
// });


