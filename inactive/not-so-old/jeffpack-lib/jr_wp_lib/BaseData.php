<?php
/**
 * BaseData is a set of methods which which gather all information needed by 
 * a plugin or theme and stores them to $this->info, an array. It is meant to
 * be extended with either plugin or theme specific information. 
 * 
 * Since the theme or plugin's identity is typically determined via the location 
 * of the file being exectuted, this class's ultimate extending class must be 
 * constructed ($ob = new Whatever(); ) from the main php file (the one 
 * detailing the plugin information in the header or, in the case of a theme, 
 * functions.php)
 * 
 * @package     JeffPack
 * @subpackage  WordPress Libraries
 * @access      public
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */
if ( ! class_exists('BaseData'))
{
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';

	/**
	 * BaseData is a set of methods which which gather all information needed by 
	 * both plugins or themes and stores them to $this->info, an array. It also has a 
	 * $this->logs, also an array, which can be populated with information to be output to the 
	 * admin panel or a file, etc and can be used to enforce minimum PHP and 
	 * WordPress versions. 
	 */
	class BaseData extends PersistArgs # extends HelperModule
	{
		#__ Properties ____________________________________________________________

		/** @var array holding plugin, WordPress, PHP and server data */
		public $info = null; # array holding all info

		/** @var array to be populated with log info for output to the admin panel, file, etc.*/
		public $logs = null; 

		#### Helpers ##############################################################

		/**
		* returns the numeric IP (as string) from which the WordPress  site 
		* is being served. This is helpful in differing behavior when on localhost.
		*
		* @access public
		*/
		public function getServerIP()
		{
			if ( array_key_exists('SERVER_ADDR', $_SERVER) ) 
				$server_ip = $_SERVER['SERVER_ADDR'];
			elseif ( array_key_exists('LOCAL_ADDR', $_SERVER) )
				$server_ip = $_SERVER['LOCAL_ADDR'];
			elseif ( array_key_exists('SERVER_NAME', $_SERVER) )
				$server_ip = gethostbyname($_SERVER['SERVER_NAME']);
			else {
				if(stristr(PHP_OS, 'WIN')) {
					$server_ip = gethostbyname(php_uname("n"));
				} else {
					$ifconfig = shell_exec('/sbin/ifconfig eth0');
					preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
					$server_ip = $match[1];
				}
			}
			return $server_ip;
		}

		/**
		* returns a large html string displaying the contents of the $GLOBALS
		* array 
		* @return string representing contents of the $GLOBALS array
		* @access public
		*/
		public function displayGLOBALS() {return $this->summarizeArray($GLOBALS, '$GLOBALS'); }

		/**
		* returns a large html string displaying the contents of the $this->info
		* It DOES display the contents nested arrays but not the contents of objects
		* or double nested arrays, etc
		*
		* @param  array   $allow  (Optional) array of allowed nested arrays to display (omitting displays all)
		* @return string  representing array with markup
		* @access public
		*/
		public function displayInfo($allow=true) {
			return $this->display2dArray($this->info, '$this->info', $allow);
		}
	}
}
