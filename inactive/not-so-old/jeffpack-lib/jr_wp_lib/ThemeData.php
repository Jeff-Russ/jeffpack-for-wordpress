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
if ( ! class_exists('ThemeData'))
{
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
	// include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

	class ThemeData extends BaseData # extends PersistArgs extends HelperModule
	{

		#__ Properties ____________________________________________________________

		/** @var WP_Theme|null reference to WP_Theme object set when setInfo() is called */
		protected $wp_theme_obj = null;

		#__ Constructor ___________________________________________________________

		/**
		* Calls enforceVersions() and setInfo().
		*
		* @param  string  $min_php sets $this->info['env']['min_php'] or '4.4.0' if not provided
		* @param  string  $min_wp sets $this->info['env']['min_wp'] or '5.3.0' if not provided
		* @param  string  $calling_file should be __FILE__ from the functions.php file 
		* (which must be directly in your plugin directory, not in a sub-directory!)
		* and is determined via debug_backtrace()[0]['file'] if not provided. 
		* @access public
		*/
		public function __construct( $min_php=null, $min_wp=null, $calling_file=null )
		{
			$this->info = array(
				'env' => array(),
				'theme' => array(),
				// also: 'theme_template_files' => array(),
				// and:  'theme_stylesheet_files' => array(),
				// and: 'wp_theme_obj' => new stdClass(),
				// possibly: 'parent_theme_obj' => new stdClass(),
				// possibly: 'parent_theme' => array(),
				// possibly: 'parent_theme_template_files' => array(),
				// possibly: 'parent_theme_stylesheet_files' => array(),
			);
			$this->info['env']['min_php'] = ($min_php !== null ) ? $min_php : '4.4.0';
			$this->info['env']['min_wp'] = ($min_wp !== null ) ? $min_wp : '5.3.0';

			if ( $calling_file === null ) $calling_file = debug_backtrace()[0]['file'];

			// $this->info['theme']['functions_file'] = $calling_file;
			// $this->info['theme']['dir_path'] = dirname ($calling_file);
			// $this->info['theme']['slug'] = $this->strAfterBslash(dirname ($calling_file));

			$this->enforceVersions();
			$this->setInfo($calling_file);
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
				// $version = ($flag === 'PHP' ) ? $min_php : $min_wp;

				// # theme_basename() is located in wp-includes/plugin.php
				// $basen = theme_basename($this->info['functions_file']); # CHANGE FOR THEMES!

				// # deactivate_plugins() is located in wp-admin/includes/plugin.php
				// deactivate_plugins( $basen ); # CHANGE FOR THEMES!

				// $opts = array( 'response'=>200, 'back_link'=>TRUE );

				// # wp_die() is located in wp-includes/functions.php
				// wp_die ("<p><strong>$basen</strong> requires " 
				// 	. $flag . ' version ' . $version .' or greater.</p>',
				// 	'Plugin Activation Error', $opts); # CHANGE FOR THEMES!
			}
		}
		/**
		* Sets a miriad of data on $this->info  
		* See source to get a list of these.
		*
		* @param  string  $calling_file should be __FILE__ from the functions.php file 
		* (which must be directly in your plugin directory, not in a sub-directory!)
		* and is determined via debug_backtrace()[0]['file'] if not provided. 
		* @access public
		*/
		public function setInfo($calling_file=null)
		{
			$this->info['env']['server_ip'] = $this->getServerIP();

			if ( $calling_file === null ) $calling_file = debug_backtrace()[0]['file'];

			$this->info['wp_theme_obj'] =& wp_get_theme( $this->strAfterBslash( dirname ($calling_file) ) );
			$this->fetchThemeData($this->info['wp_theme_obj'], $this->info['theme']);
			$this->info['theme_template_files'] = $this->info['wp_theme_obj']->get_files( 'php', 1, true );
			$this->info['theme_stylesheet_files'] = $this->info['wp_theme_obj']->get_files( 'css', 1, true );

			if ( ! empty($this->info['theme']['parent_theme_name']) ):
				$this->info['parent_theme_obj'] =& $this->info['wp_theme_obj']->parent();
				$this->fetchThemeData($this->info['parent_theme_obj'], $this->info['parent_theme']);
				$this->info['parent_theme_template_files'] = $this->info['parent_theme_obj']->get_files( 'php', 1, true );
				$this->info['parent_theme_stylesheet_files'] = $this->info['parent_theme_obj']->get_files( 'css', 1, true );
			endif;
		}
		/**
		* private function used in populating $this->info['theme'], $this->info['parent_theme']
		* or really any array provided as the second argument (optional and by reference )
		* or returned as a new array. The first argument should be an object of the type 
		* WP_Theme such as the return from wp_get_theme()
		* 
		* See source to get a list of the data being set.
		*
		* @param  object  $wp_theme_obj WP_Theme object returned from wp_get_theme()
		* @param  array  (optional) array which will be written to by reference and returned
		* @access private
		*/
		private function fetchThemeData($wp_theme_obj, &$array=null)
		{
			if ( $array === null ) $array = array();
			// $array['name_translated'] = $wp_theme_obj->name_translated;
			// $array['errors'] = $wp_theme_obj->errors;
			// $array['textdomain_loaded'] = $wp_theme_obj->textdomain_loaded;

			$array['name']           = $wp_theme_obj->get('Name');
			$array['uri']            = $wp_theme_obj->get('ThemeURI');
			$array['author']         = $wp_theme_obj->display('Author', false);
			# note true for above returns the above wraped in <a href='$AuthorURI'>
			$array['author_uri']     = $wp_theme_obj->get('AuthorURI');
			$array['version']        = $wp_theme_obj->get('Version');
			$array['description']    = $wp_theme_obj->display('Description');
			$array['text_domain']    = $wp_theme_obj->get('TextDomain');
			$array['domain_path']    = $wp_theme_obj->get('DomainPath'); 
			$array['network']        = $wp_theme_obj->get('Network');
			$array['dir_path']       = $wp_theme_obj->get_stylesheet_directory();
			$array['slug']           = $wp_theme_obj->get_stylesheet();
			$array['prefix']         = $this->toSnakeCase( $array['slug'] );
			$array['template_dir']   = $wp_theme_obj->get_template_directory();
			$array['template']       = $wp_theme_obj->get_template();
			$array['screenshot']     = $wp_theme_obj->get_screenshot('relative');
			$array['tags']           = implode ( ',', $wp_theme_obj->get('Tags') );

			# the following could return something like:
			# '/Users/jeff/Documents/Websites/experiments.dev/wp-content/themes'
			$array['root']           = $wp_theme_obj->get_theme_root();

			# the follow could return something like http://experiments.dev/wp-content/themes
			$array['root_uri']       = $wp_theme_obj->get_theme_root_uri(); 

			$array['cache_hash']     = md5( $array['root'] . '/' . $array['stylesheet'] );

			if ( $wp_theme_obj->parent() )
				$array['parent_theme_name'] = $wp_theme_obj->parent()->get('Name');
			else
				$array['parent_theme_name'] = '';
			return $array;
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
