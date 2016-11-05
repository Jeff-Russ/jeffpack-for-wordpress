<?php
include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

/*
include_once this class in any and all plugins and optionally extend it,
then instantiate from main php file. Your object has helper functions and a
massive $info multi-array holding just about everything you'll need. Your object
as well as $info, sub-arrays in $info or just values from $info can be passed to 
callback to the WordPress API, helping keep scopes clean and variables available.  

Methods help you avoid nested callback with WP by remembering the last operation.  
The next call to a method will use the context of the previous.  Alternatively 
you can save the returns from method calls which will provide what you need to 
follow up with what would be a nested call to WP later on.  

*/
if ( ! class_exists('PluginBase')) { # this is just so muliple plugins can share

class PluginBase {

	public $info = array(); # array holding info server data
	public $args = array();  # holds whatever variables are needed between calls
	public $logs = array();

	#### end properties  ######################################################
	#### begin constructor ####################################################

	public function __construct( $min_wp='4.4.0', $min_php='5.3.0' )
	{
		$caller_path = debug_backtrace()[0]['file'];
		$this->enforceVersions($caller_path, $min_wp, $min_php);
		$this->setInfo($caller_path);
		$this->writeLogs();
	}

	#### end constructor ######################################################
	#### begin helpers ########################################################

	public function enforceVersions($plugin_file, $min_wp, $min_php)
	{
		global $wp_version;
		if (version_compare(PHP_VERSION, $min_php, '<' )) $flag='PHP';
		elseif (version_compare($wp_version, $min_wp, '<' )) $flag='WordPress';
		else $flag = false;

		if ($flag !== false) {
			$version = ($flag === 'PHP' ) ? $min_php : $min_wp;
			$basen = plugin_basename($plugin_file);
			deactivate_plugins( $basen );
			$opts = array( 'response'=>200, 'back_link'=>TRUE );
			wp_die ("<p><strong>$basen</strong> requires " 
				. $flag . ' version ' . $version .' or greater.</p>',
				'Plugin Activation Error', $opts);
		}
	}

	public function setInfo($caller_path)
	{
		$plugin_data = get_plugin_data($caller_path);
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
		$this->info['plugin_file'] = $caller_path;
		$this->info['plugin_basename'] = plugin_basename($caller_path);
		$this->info['plugin_path'] = dirname($caller_path);
		$this->info['plugin_slug'] = basename($caller_path, ".php");
		$this->info['plugin_prefix'] = $this->toSnakeCase(
									basename($this->info['plugin_slug']) );
		$this->info['plugin_url'] = WP_PLUGIN_URL.'/'.$this->info['plugin_slug'];
		$this->info['server_ip'] = $this->getServerIP();
		$this->info['min_wp'] = $min_wp;
		$this->info['min_wp'] = $min_wp;
	}

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

	public function writeLogs() {
		$this->logs['info'] = "<h2>Plugin->info</h2>"
			. $this->arrayToString($this->info);
		$this->logs['GLOBALS'] = '<h2>$GLOBAL</h2>'.$this->viewGLOBALS();
	}

	#### end helpers ##########################################################
	#### begin Setting/Options related methods ################################

	public function addSettingsPage (
		$menu_location="menu",
		$page_title="Page Title",
		$menu_title="Menu Title",
		&$args=null
	) {
		if ($args === null) $args = array();
		$icon_url = $this->getArgOrNull($args, 'icon_url');
		$position = $this->getArgOrNull($args, 'position');

		switch ($menu_location) {
			case "menu":
				$page_slug = $this->info['plugin_prefix'].'_menu'; break;
			case "options":
				$page_slug = $this->info['plugin_prefix'].'_settings'; break;
			default:
				$snake_loc = $this->toSnakeCase($menu_location);
				$snake_title = $this->toSnakeCase($menu_title);
				$page_slug = $this->info['plugin_prefix']
					. "_${snake_loc}_${snake_title}";
		}
		$this->info['settings_pages'][$page_slug] = array(
			'menu_location' => $menu_location,
			'page_title' => $page_title,
			'menu_title' => $menu_title,
			'page_slug' => $page_slug,
			'icon_url' => $icon_url,
			'position' => $position,
			'option_group' => $page_slug.'_option_group',
			'option_name' => $page_slug.'_option_name',
			'sections' => array( $page_slug.'_default_section' )
		);
		$this->info['settings_sections'][$page_slug.'_default_section'] = array(
			'page_slug' => $page_slug,
			'section_id' => $page_slug.'_default_section',
			'section_title' => '', # no title!
			'option_group' => $page_slug.'_option_group',
			'option_name' => $page_slug.'_option_name',
			'indented' => false,
			'settings' => array(),
		);
		$args = $this->dualSyncArgs(
			$this->info['settings_pages'][$page_slug],
			$this->info['settings_sections'][$page_slug.'_default_section']
		);

		add_action("admin_menu", function() use ($args) { extract($args);
			
			if ($menu_location === 'menu'):
				add_menu_page( # add_menu_page adds page directly on the sidebar:
				$page_title, $menu_title, "manage_options", $page_slug, function() use ($args) {
					extract($args);
					?><div class="wrap">
						<h1><?php echo $page_title; ?></h1>
						<hr color='#333' size='4'>
						<form action="options.php" method="post"> <?php
							settings_fields( $option_group );
							do_settings_sections( $page_slug );
							submit_button();
						?><form>
					</div><?php
				}, $icon_url, $position);

			elseif ($menu_location === 'options'):
				add_options_page( # add menu under "Settings"
				$page_title, $menu_title, "manage_options", $page_slug, function() use ($args) {
					extract($args);
					?><div class="wrap">
						<h1><?php echo $page_title; ?></h1>
						<hr color='#333' size='4'>
						<form action="options.php" method="post"> <?php
							settings_fields( $option_group );
							do_settings_sections( $page_slug );
							submit_button();
						?><form>
					</div><?php
				}, $icon_url, $position);

			else:
				add_submenu_page( $menu_location, # add submenu under $menu_location
				$page_title, $menu_title, "manage_options", $page_slug, function() use ($args) {
					extract($args);
					?><div class="wrap">
						<h1><?php echo $page_title; ?></h1>
						<hr color='#333' size='4'>
						<form action="options.php" method="post"> <?php
							settings_fields( $option_group );
							do_settings_sections( $page_slug );
							submit_button();
						?><form>
					</div><?php
				}, $icon_url, $position);
			endif;
		});

		add_action( "admin_init", function() use ($args){
			extract($args);
			register_setting( $option_group, $option_name ); 
			add_settings_section( $section_id, "", function(){}, $page_slug );
		});

		return $this;
	}

	#==========================================================================

	public function addSettingsSection($section_title='', &$args=null)
	{
		if ($args === null) $args = array();
		$page_slug = $this->getArg($args, 'page_slug');
		$section_id = $page_slug . '_' . $this->toSnakeCase($section_title);

		array_push( $this->info['settings_pages'][$page_slug]['sections'], $section_id );

		$this->info['settings_sections'][$section_id] = array (
			'section_title' => $section_title,
			'page_slug' => $page_slug,
			'section_id' => $section_id,
			'option_group' => $this->getArg($args, 'option_group'),
			'option_name' => $this->getArg($args, 'option_name'),
			'indented' => $this->getArgOrDefault($args, 'indented', true),
			'settings'=> array(),
		);

		$args = $this->syncArgs($this->info['settings_sections'][$section_id] );

		if ($use['indented']):
			add_action( "admin_init", function() use ($args) { extract($args);
				add_settings_section( $section_id, $section_title,
				function(){echo'<div style="margin-left:8%">';}, $page_slug );

				add_settings_section('/'.$section_id,'', # dummy section to close div
				function(){echo"</div>\r\n";}, $page_slug ); 
			});
		else:
			add_action( "admin_init", function() use ($args) { extract($args);
				add_settings_section( $section_id, $section_title, function(){
				/* $section_title WILL be displayed! */}, $page_slug );
			});
		endif;

		$this->args = array_merge($this->args, $args);
		return $this;
	}

	#==========================================================================

	public function addSetting($setting_label, $default='', $source='', &$args=null)
	{
		if ($args === null) $args = array();
		$section_id = $this->getArg($args, 'section_id');
		$setting_id = "${section_id}_" . $this->toSnakeCase($setting_label);

		$section_info = $this->info['settings_sections'][$section_id];
		array_push( $this->info['settings_sections'][$section_id]['settings'], $setting_id );

		if ( empty($source) ) $source = '<input type="text" name="$name" value="$value">';

		$this->info['settings'][$setting_id] = array (
			'setting_id' => $setting_id,
			'setting_label' => $setting_label,
			'name' => $section_info['option_name']."[${setting_id}]",
			'source' => $source,
			'default' => $default,
			'page_slug' => $section_info['page_slug'],
			'section_id' => $section_info['section_id'],
			'section_title' => $section_info['section_title'],
			'option_group' => $section_info['option_group'],
			'option_name' => $section_info['option_name'],
			'indented' => $section_info['indented'],
		);
		# getSetting below cant be called until the array above is set.
		$this->info['settings'][$setting_id]['value'] = $this->getSetting($setting_id);

		$args = $this->dualSyncArgs($args, $this->info['settings'][$setting_id]);

		if ( is_callable($source) ):
			add_action( "admin_init", function() use($args) { extract($args);
				add_settings_field(
					$setting_id, $setting_label, $source, $page_slug, $section_id, $args);
			});
		else:
			add_action( "admin_init", function() use($args) { extract($args);
				add_settings_field( $setting_id, $setting_label, function() use($args) {
					extract($args);
					$source = str_replace('$name', $name, $source);
					$source = str_replace('$value', $value, $source);
					echo $source;
				}, $page_slug, $section_id );
			});
		endif;
		return $this;
	}

	public function getSetting($setting_id)
	{
		extract( $this->info['settings'][$setting_id] );
		$options = wp_parse_args(get_option($option_name), [$setting_id => $default] );
		return $options[$setting_id];
	}


	#==========================================================================

	// private function getSettingsInfo($arg, $key='page_slug', $this_info='')
	// {
	// 	if ( empty($this_info) ) $this_info = $this->info['settings_pages'];

	// 	if (is_string($arg)):
	// 		return $arg;
	// 	elseif ( is_array($arg) ):
	// 		return $arg[$key];
	// 	elseif (empty($arg) ):
	// 		# if they don't specify we'll assume last created
	// 		return end( $this_info )[$key];
	// 	endif;
	// }

	private function getArg($args, $key)
	{
		if ( is_array($args) ):
			if ( array_key_exists($key, $args) ):
				return $args[$key];
			// elseif ( array_key_exists($key, $this->args) ):
			else: # let it get error
				return $this->args[$key];
			endif;
		elseif ( $args !== null ):
			return $args;
		else:
			// if ( array_key_exists($key, $this->args) ) 
				# nm, let it error
				return $this->args[$key];
		endif;
	}
	private function getArgOrNull($args, $key)
	{
		if ( is_array($args) ):
			if ( array_key_exists($key, $args) ):
				return $args[$key];
			else:
				return null;
			endif;
		else:
			return $args;
		endif;
	}
	private function getArgOrDefault($args, $key, $default)
	{
		if ( is_array($args) ):
			if ( array_key_exists($key, $args) ):
				return $args[$key];
			else:
				return $default;
			endif;
		elseif ( empty($args) ):
			return $default;
		else:
			return $args;
		endif;
	}
	public function toArrayAsKey($args, $key) #PUBLIC!
	{
		if (! is_array($args) ): 
			if ($args !== null):
				$args = array($key => $args);
			else:
				$args = array();
			endif;
		elseif (array_key_exists(0, $args) && !array_key_exists($key, $args) ):
			$args[$key] = $args[0];
			unset($args[0]);
		endif;
		return $args;
	}

	private function getArgThenSync(&$args, $key)
	{
		if ( is_array($args) )
			 $this->args = array_merge($this->args, $args);
		else $this->args[$key] = $args;

		$args = $this->args;
		return $args[$key];
	}

	private function dualSyncArgs($args, $more_args) {
		if ( is_array($args) ):
			if ( is_array($more_args) )
				$this->args = array_merge($this->args, array_merge($args, $more_args) );
			else
				$this->args = array_merge($this->args, $args);
		endif;

		return $this->args;
	}

	private function syncArgs($args, $key=null) {
		if ( is_array($args) ):
			$this->args = array_merge($this->args, $args);
			$args = $this->args;
			if ( $key === null ):        return $this->args;
			elseif ( ! is_array($key) ): return $this->args[$key];
			endif;

		elseif ( $args === null && $key !== null ):
			return $this->args[$key];

		elseif ( $key !== null ): # and arg1 is non-array so we 
			$this->args[$key] = $args; # assume it's a value to assign.
			$args = $this->args;
			return $this->args[$key];  # just return the value

		endif;
	}

	#### end Setting/Options related methods ##################################
	#### begin assorted helper methods       ##################################


	// For displaying booleans
	public function bool2str ($bool_val) { return $bool_val ? 'true' : 'false'; }

	// For displaying octal number without converting
	public function octal2str ($octal_num) { return sprintf("%04o", $octal_num); }

	// return string after final /
	public function str_after_bslash ($string) {return array_pop(explode('/', $string)); }

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
	public function arrayToString ($var)
	{
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		$deletes = array(
			'/<i>(.*?)<\/i>/',
			'/ <small>(.*?)<\/small>/',
			'/(.*?)<b>(.*?)<\/b>(.*?)\n/',
		);
		$result = preg_replace($deletes, '', $result);
		return $result; 
	}
	public function toSnakeCase( $str ) 
	{
		$str = preg_replace("/(.+)\.php$/", "$1", $str);
		$baddies = array(' ', '-', '.', '/');
		$str = str_replace($baddies, '_', $str);
		$str = preg_replace("/[^A-Za-z0-9_]/", '', $str );
		$str = strtolower($str);
		return $str;
	}
	public function isClosure($t) {
		return is_object($t) && ($t instanceof Closure);
	}
}

}
