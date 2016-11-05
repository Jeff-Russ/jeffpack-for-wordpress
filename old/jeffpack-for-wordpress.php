<?php

wp_plu
<?php
/**
* Plugin Name: Jeffpack for WordPress
* Description: Multi-Purpose Plugin/Theme Collection
* Plugin URI: http://github.com/jeff-russ/jeffpack-for-wordpress
* Author: Jeff Russ
* Author URI: http://github.com/jeff-russ
* Text Domain: jeffpack-for-wordpress
* Domain Path: 
* Version: 0.1
* License: GPL2
*/

/*
Copyright (C) 2016  Jeff Russ jeffreylynnruss@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action( 'plugins_loaded', array( 'Plugin_Class_Name', 'get_instance' ) );
register_activation_hook( __FILE__, array( 'Plugin_Class_Name', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Plugin_Class_Name', 'deactivate' ) );
// register_uninstall_hook( __FILE__, array( 'Plugin_Class_Name', 'uninstall' ) );

<?php
$_SERVER[SERVER_ADDR] = "127.0.0.1";
$wp_version = '4.4.0';
function get_home_path() {
    return "/Users/jeff/example";
}

function getServerAddress()
{
	if ( array_key_exists('SERVER_ADDR', $_SERVER) )
		return $_SERVER['SERVER_ADDR'];
	elseif ( array_key_exists('LOCAL_ADDR', $_SERVER) )
		return $_SERVER['LOCAL_ADDR'];
	elseif ( array_key_exists('SERVER_NAME', $_SERVER) )
		return gethostbyname($_SERVER['SERVER_NAME']);
	else {
		// Running CLI
		if(stristr(PHP_OS, 'WIN')) {
			return gethostbyname(php_uname("n"));
		} else {
			$ifconfig = shell_exec('/sbin/ifconfig eth0');
			preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
			return $match[1];
		}
	}
}

class JeffpackSingleton {

	public $min_wp  = '4.4.0';
	public $min_php = '5.3.0';
// 	public $wp_version = global $wp_version;
// 	public $wp_path = get_home_path();
	public $log = "";

	private static $instance = null;
	private static function getInstance()
	{
		if ( null === static::$instance ) static::$instance = new static();
		return static::$instance;
	}
	protected function __construct()
	{
		if ($_SERVER[SERVER_ADDR] == "127.0.0.1") {
			$JP['localhost'] = true;
		} else {
			$JP['localhost'] = false;
		}
	}
	private function __clone(){}  # block duplication
	private function __wakeup(){} # block unserializing

	public function enforceVersions( $min_wp=null, $min_php=null ) {

		global $wp_version;
		$this->$wp_version = $wp_version;

		if ( ! is_null($min_wp) ) $this->min_wp = $min_wp;
		if ( ! is_null($min_php) ) $this->min_php = $min_php;

		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) $flag = 'PHP';
		elseif ( version_compare( $wp_version, $min_wp, '<' ) ) $flag = 'WordPress';
		else return false;
		
		$version = ('PHP' == $flag) ? $min_php : $min_wp;
		$tempvar = debug_backtrace();
		$plugin_file = $tempvar[0]['file']; // <- value of __FILE__ in calling script
		$tempvar = get_plugin_data($plugin_file);
		$base_n = plugin_base_name( $plugin_file );
		$plugin_name = $tempvar['Name'];
		deactivate_plugins( $base_n );
		$opts = array( 'response'=>200, 'back_link'=>TRUE );
		wp_die ( "<p><strong>$base_n</strong> requires " . $flag .' version ' .
			$version . ' or greater.</p>', 'Plugin Activation Error', $opts );
	}

	public static function activate()
	{
	}

	public static function deactivate()
	{
	}

	// public static function uninstall() {
	// 	if ( __FILE__ != WP_UNINSTALL_PLUGIN )
	// 		return;
	// }

}

if ( ! defined( 'ABSPATH' ) ) exit;

// $this_plugin_path = strstr(__DIR__, '/wp-content');


// $JP['localhost'] = false;

$JP['localhost'] ? debug_unexpected_output(true) :'' ;

// HOOKS //////
function pre_activation_hook() {
	require 'lib/globs.php';
	define_jp_globs();
	global $JP;
	require_once 'lib/funcs.php';
	require_once $JP['wp_path'] . '/wp-admin/includes/plugin.php';
	require_once $JP['wp_path'] . '/wp-admin/includes/plugin.php';
	require_once $JP['wp_path'] . '/wp-load.php';
	enforce_versions();
}

register_activation_hook( __FILE__, 'pre_activation_hook');

register_activation_hook( __FILE__, function() use ($JP) {

	$log = "Activated ". date("Y/m/d");
	if ($JP['localhost'])
		$log .= "\nDEVMODE ON";
	else
		$log .= "\nDEVMODE off";
	$bin = make_jp_bin($log);
	$gits = install_gits($bin, $log);
	$updater_tool = "https://raw.githubusercontent.com/Jeff-Russ/add-wp-updater/master/add-wp-updater";
	curl_executable($bin, $updater_tool);
	// dl_and_install_plugin('https://downloads.wordpress.org/plugin/buddypress.2.7.0.zip', $log);

	jr_setfile("$bin/_ACTIVATION_LOG.txt", $log);

	if ($JP['localhost']) {
		$info = "Activated ". date("Y/m/d");
		$info .= "\nphpversion = " . phpversion();
		$info .= "\n\$_SERVER = ";
		$info .= print_r($_SERVER, true);
		jr_setfile("$bin/_ACTIVATION_INFO.txt",$info);
	}

});

// function register_uninstall_hook( $file, $callback ) {
// }