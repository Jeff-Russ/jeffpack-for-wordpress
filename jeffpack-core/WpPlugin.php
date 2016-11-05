<?php
/**
 * Include this file and gain access to everything you need for a plugin via the
 * WpPlugin class, which extends SettingsHelper, PluginData, PersistArgs 
 * and HelperModule
 * 
 * @package     JeffPack
 * @subpackage  WordPress Classes
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */

include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';
include_once 'php_classes/HelperModule.php';
include_once 'php_classes/PersistArgs.php';
include_once 'wp_classes/PluginData.php';
include_once 'wp_classes/SettingsHelper.php';

if ( ! class_exists('WpPlugin') ) {
	/*
	 * Include this file and gain access to everything you need for a plugin via the
	 * WpPlugin class, which extends SettingsHelper, PluginData, PersistArgs 
	 * and HelperModule
	 */ 
	class WpPlugin extends SettingsHelper {}
}