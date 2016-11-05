<?php
/**
 * PluginData is a set of methods which which gather all information needed by 
 * a plugin and stores them to $this->info, an array. It also has a $this->logs, 
 * also an array, which can be populated with information to be output to the 
 * admin panel or a file, etc and can be used to enforce minimum PHP and 
 * WordPress versions. 
 * 
 * Since the plugin's identity is typically determined via the file being 
 * exectuted, this class must be constructed ($ob = new Whatever(); ) from the 
 * main php file (the one detailing the plugin information via the comment 
 * header).  
 * 
 * @package     JeffPack
 * @subpackage  WordPress Classes
 * @access      public
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */
if ( ! class_exists('PluginData'))
{
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

	/**
	 * PluginData is a set of methods which which gather all information needed by 
	 * a plugin and stores them to $this->info, an array. It also has a $this->logs, 
	 * also an array, which can be populated with information to be output to the 
	 * admin panel or a file, etc and can be used to enforce minimum PHP and 
	 * WordPress versions. 
	 */
	class PluginData extends PersistArgs # extends HelperModule
	{
		#__ Properties ____________________________________________________________

		/** @var array holding plugin, WordPress, PHP and server data */
		public $info = array(); # array holding info server data

		/** @var array to be populated with log info for output to the admin panel, file, etc.*/
		public $logs = array(); 

		#__ Constructor ___________________________________________________________

		/**
		* Calls enforceVersions() and setInfo().
		*
		* @param  string  $min_php sets $this->['min_php'] or '4.4.0' if not provided
		* @param  string  $min_wp sets $this->['min_wp'] or '5.3.0' if not provided
		* @param  string  $plugin_file should be __FILE__ from main php file and 
		* is determined via debug_backtrace()[0]['file'] if not provided. It is used 
		* to set $this->info['plugin_file'] 
		* @access public
		*/
		public function __construct( $min_php=null, $min_wp=null, $plugin_file=null )
		{
			$this->info['min_php'] = ($min_php === null ) ? $min_php : '4.4.0';
			$this->info['min_wp'] = ($min_wp === null ) ? $min_wp : '5.3.0';

			if ($plugin_file === null ) {
				$this->info['plugin_file'] = debug_backtrace()[0]['file'];
			} else {
				$this->info['plugin_file'] = $plugin_file;
			}
			$this->enforceVersions();
			$this->setInfo();
		}

		#### Helpers ##############################################################

		/**
		* Calls wp_die if minimum WordPress or PHP version are not met.
		*
		* @param  string  $min_php resets $this->['min_php'] only if provided
		* @param  string  $min_wp resets $this->['min_wp'] only if provided
		* @param  string  $plugin_file resets $this->['plugin_file'] only if provided
		* @access public
		*/
		public function enforceVersions( $min_php=null, $min_wp=null, $plugin_file=null )
		{
			if ($min_php !== null ) $this->info['min_php'] = $min_php;
			if ($min_wp !== null ) $this->info['min_wp'] = $min_wp;
			if ($plugin_file !== null ) $this->info['plugin_file'] = $plugin_file;

			global $wp_version;
			if (version_compare(PHP_VERSION, $min_php, '<' )) $flag='PHP';
			elseif (version_compare($wp_version, $min_wp, '<' )) $flag='WordPress';
			else $flag = false;

			if ($flag !== false) {
				$version = ($flag === 'PHP' ) ? $min_php : $min_wp;

				# plugin_basename() is located in wp-includes/plugin.php
				$basen = plugin_basename($this->info['plugin_file']);

				# deactivate_plugins() is located in wp-admin/includes/plugin.php
				deactivate_plugins( $basen );

				$opts = array( 'response'=>200, 'back_link'=>TRUE );

				# wp_die() is located in wp-includes/functions.php
				wp_die ("<p><strong>$basen</strong> requires " 
					. $flag . ' version ' . $version .' or greater.</p>',
					'Plugin Activation Error', $opts);
			}
		}

		/**
		* Sets a miriad of keys on $this->info - 'plugin_name', 'plugin_uri', 
		* 'plugin_version', 'plugin_description', 'plugin_author',
		* 'plugin_author_uri', 'plugin_text_domain', 'plugin_domain_path', 
		* 'plugin_network', 'plugin_title', 'plugin_author_name', 
		* 'plugin_author_name', 'plugin_basename', 'plugin_path',
		* 'plugin_slug', 'plugin_prefix', 'plugin_url', 'server_ip',
		* and, if provided as arguments, 'min_php', 'min_wp' and 'plugin_file'
		*
		* @param  string  $min_php resets $this->['min_php'] only if provided
		* @param  string  $min_wp resets $this->['min_wp'] only if provided
		* @param  string  $plugin_file resets $this->['plugin_file'] only if provided
		* @access public
		*/
		public function setInfo( $min_php=null, $min_wp=null, $plugin_file=null )
		{
			if ($min_php !== null ) $this->info['min_php'] = $min_php;
			if ($min_wp !== null ) $this->info['min_wp'] = $min_wp;
			if ($plugin_file !== null ) $this->info['plugin_file'] = $plugin_file;

			# get_plugin_data() is located in wp-admin/includes/plugin.php
			$plugin_data = get_plugin_data($this->info['plugin_file']);
			$this->info['plugin_name'] = $plugin_data['Name'];
			$this->info['plugin_uri'] = $plugin_data['PluginURI'];
			$this->info['plugin_version'] = $plugin_data['Version'];
			$this->info['plugin_description'] = $plugin_data['Description'];
			$this->info['plugin_author'] = $plugin_data['Author'];
			$this->info['plugin_author_uri'] = $plugin_data['AuthorURI'];
			$this->info['plugin_text_domain'] = $plugin_data['TextDomain'];
			$this->info['plugin_domain_path'] = $plugin_data['DomainPath']; 
			$this->info['plugin_network'] = $plugin_data['Network'];
			$this->info['plugin_title'] = $plugin_data['Title'];
			$this->info['plugin_author_name'] = $plugin_data['AuthorName'];
			# plugin_basename() is located in wp-includes/plugin.php
			$this->info['plugin_basename'] = plugin_basename($this->info['plugin_file']);
			$this->info['plugin_path'] = dirname($this->info['plugin_file']);
			$this->info['plugin_slug'] = basename($this->info['plugin_file'], ".php");
			$this->info['plugin_prefix'] = $this->toSnakeCase(
										basename($this->info['plugin_slug']) );
			$this->info['plugin_url'] = WP_PLUGIN_URL.'/'.$this->info['plugin_slug'];
			$this->info['server_ip'] = $this->getServerIP();
		}

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
		* returns a large string displaying the contents of the $GLOBALS
		* array 
		* 
		* @return string representing contents of the $GLOBALS array
		* @access public
		*/
		public function viewGLOBALS()
		{
			$objects = '<br>Objects in $GLOBALS<br>';
			$arrays = '<br>Arrays in $GLOBALS<br>';
			$true = '<br>True in $GLOBALS<br>';
			$false = '<br>False in $GLOBALS<br>';
			$nulls = '<br>NULL in $GLOBALS<br>';
			$nums = '<br>Numbers in $GLOBALS<br>';
			$strs = '<br>Strings in $GLOBALS<br>';
			$else = '<br>others in $GLOBALS<br>';
			foreach ($GLOBALS as $key => $value) {
				if (is_object($value)) $objects .= " '$key',";
				elseif (is_array($value)) $arrays .= " '$key',";
				elseif (true === $value) $true .= " '$key',";
				elseif (false === $value) $false .= " '$key'";
				elseif (is_null($value)) $nulls .= " '$key',";
				elseif (is_numeric($value)) $nums .= " '$key' => $value,<br>";
				elseif (is_string($value)) $strs .= " '$key' => '$value',<br>";
				else $else += " '$key' => $value,";
			}
			return $objects . '<br>' . $arrays . '<br>' . $true . '<br>' 
				. $false . '<br>' . $nulls . '<br>' . $nums . $strs . $else;
		}

		/**
		* Saves viewGLOBALS to $this->logs['GLOBALS']
		* 
		* @return string representing contents of the $GLOBALS array
		* @access public
		*/
		public function writeLogs() {
			$this->logs['info'] = "<h2>Plugin->info</h2>"
				. $this->arrayToString($this->info);
			$this->logs['GLOBALS'] = '<h2>$GLOBAL</h2>'.$this->viewGLOBALS();
		}
	}
}
