<?php
/**
 * 
 * @package     JeffPack
 * @subpackage  WordPress Libraries
 * @access      public
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */
if ( ! class_exists('PluginData'))
{
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

	class PluginData extends BaseData # extends PersistArgs extends HelperModule
	{
		#__ Constructor ___________________________________________________________

		/**
		* Calls enforceVersions() and setInfo()
		*
		* @param  string  $min_php sets $this->info['env']['min_php'] or '4.4.0' if not provided
		* @param  string  $min_wp sets $this->info['env']['min_wp'] or '5.3.0' if not provided
		* @param  string  $plugin_file should be __FILE__ from main php file and 
		* is determined via debug_backtrace()[0]['file'] if not provided. It is used 
		* to set $this->info['plugin']['file'] 
		* @access public
		*/
		public function __construct( $min_php=null, $min_wp=null, $plugin_file=null )
		{
			$this->info = array(
				'env' => array(),
				'plugin' => array(),
				'themes' => array(),  # for interacting with themes
			);
			$this->info['env']['min_php'] = ($min_php !== null ) ? $min_php : '4.4.0';
			$this->info['env']['min_wp'] = ($min_wp !== null ) ? $min_wp : '5.3.0';

			$this->enforceVersions();
			if ($plugin_file === null ) $plugin_file = debug_backtrace()[0]['file'];
			$this->setInfo($plugin_file);
		}
		#### Helpers ##############################################################

		/**
		* Calls wp_die if minimum WordPress or PHP version are not met.
		*
		* @param  string  $min_php resets $this->info['env']['min_php'] if provided
		* @param  string  $min_wp resets $this->info['env']['min_wp'] if provided
		* @access public
		*/
		public function enforceVersions( $min_php=null, $min_wp=null )
		{
			if ($min_php !== null ) $this->info['env']['min_php'] = $min_php;
			if ($min_wp !== null ) $this->info['env']['min_wp'] = $min_wp;

			global $wp_version;
			$this->info['env']['php_version'] = PHP_VERSION;
			$this->info['env']['wp_version'] = $wp_version;
			
			if (version_compare(PHP_VERSION, $min_php, '<' )) $flag='PHP';
			elseif (version_compare($wp_version, $min_wp, '<' )) $flag='WordPress';
			else $flag = false;

			if ($flag !== false) {
				$version = ($flag === 'PHP' ) ? $min_php : $min_wp;

				# plugin_basename() is located in wp-includes/plugin.php
				$basen = plugin_basename($this->info['plugin']['file']); 

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
		* Sets a miriad of data on $this->info  
		* See source to get a list of these.
		*
		* @access public
		*/
		public function setInfo($plugin_file=null)
		{
			if ($plugin_file === null ) $plugin_file = debug_backtrace()[0]['file'];

			# get_plugin_data() is located in wp-admin/includes/plugin.php
			$plugin_data = get_plugin_data($plugin_file);
			$this->info['plugin']['name'] = $plugin_data['Name'];
			$this->info['plugin']['uri'] = $plugin_data['PluginURI'];
			$this->info['plugin']['author'] = $plugin_data['AuthorName'];
			# note that $plugin_data['Author'] returns the above wraped in <a href='$PluginURI'>
			$this->info['plugin']['author_uri'] = $plugin_data['AuthorURI'];
			$this->info['plugin']['version'] = $plugin_data['Version'];
			$this->info['plugin']['description'] = $plugin_data['Description'];
			$this->info['plugin']['text_domain'] = $plugin_data['TextDomain'];
			$this->info['plugin']['domain_path'] = $plugin_data['DomainPath']; 
			$this->info['plugin']['network'] = $plugin_data['Network'];
			# plugin_basename() is located in wp-includes/plugin.php
			$this->info['plugin']['dir_path'] = dirname($plugin_file);
			$this->info['plugin']['main_php'] = basename($plugin_file, ".php");
			$this->info['plugin']['slug'] = trim(dirname(plugin_basename($plugin_file)), '/');
			$this->info['plugin']['prefix'] = $this->toSnakeCase(
										basename($this->info['plugin']['slug']) );
			$this->info['plugin']['url'] = WP_PLUGIN_URL.'/'.$this->info['plugin']['slug'];
			$this->info['plugin']['file_path'] = $plugin_file;
			$this->info['plugin']['basename'] = plugin_basename($plugin_file);

			$this->info['env']['server_ip'] = $this->getServerIP();

			$this->info['themes']['active_theme_dir'] = get_stylesheet_directory();

			$this->info['plugin']['??'] = trim(dirname(plugin_basename(__FILE__)), '/');
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