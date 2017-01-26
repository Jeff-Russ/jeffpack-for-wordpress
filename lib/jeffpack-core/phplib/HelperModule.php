<?php
/**
 * HelperModule is a collection of static methods mostly for general php useages
 * 
 * @package     JeffPack
 * @subpackage  General PHP Libraries
 * @access      public
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */
if ( ! class_exists('HelperModule')) {
	/**
	 * The HelperModule class is a collection of static methods mostly for general php useages
	 */
	class HelperModule { 

		/**
		* This static method for displaying booleans as strings
		*
		* @param  boolean $boolean a boolean variable
		* @return string either 'true' or 'false'
		* @access public
		*/
		static public function bool2str ($boolean) { return $boolean ? 'true' : 'false'; }

		/**
		* This static method for displaying octal number without converting
		*
		* @param  integer $octal_num an octal number
		* @return string  representing decimal number
		* @access public
		* example
		*/
		static public function octal2str ($octal_num) { return sprintf("%04o", $octal_num); }

		/**
		* This static method to get string after final backslash (/)
		*
		* @param  string  $string typically a local path or uri
		* @return string  portion after final backslash (/)
		* @access public
		*/
		static public function strAfterBslash ($string) {return array_pop(explode('/', $string)); }

		/**
		* This static method that captures var_dump of array as string, trims the fat and returns string
		*
		* @param  array   $array 
		* @return string  representing array
		* @access public
		*/
		static public function arrayToString ($array)
		{
			ob_start();
			var_dump($array);
			$result = ob_get_clean();
			$deletes = array(
				'/<i>(.*?)<\/i>/',
				'/ <small>(.*?)<\/small>/',
				'/(.*?)<b>(.*?)<\/b>(.*?)\n/',
			);
			$result = preg_replace($deletes, '', $result);
			return $result; 
		}

		/**
		* This static method converts a string (path or file) to a snake_case string
		* by
		* 1. removing ".php"
		* 2. replacing  spaces, dashes dots and backslashes with underscores 
		* 3. removing all other non-alphanumeric other than 'underscores 
		* 4. converting to all apha characters to lower-case
		* 5. if the first character(s) is a number it's removed
		*
		* @param  string  $str example: "dir/my-plugin.php"
		* @return string  "dir/my-plugin.php" would return: "dir_my_plugin"
		* @access public
		*/
		static public function toSnakeCase( $str ) 
		{
			$str = preg_replace("/(.+)\.php$/", "$1", $str);
			$baddies = array(' ', '-', '.', '/');
			$str = str_replace($baddies, '_', $str);
			$str = preg_replace("/[^A-Za-z0-9_]/", '', $str );
			$str = strtolower($str);
			while ( is_numeric($str[0] ) ) $str = substr($str, 1);
			return $str;
		}
		/**
		* This static method takes an array and returns an html string
		* It does display the contents of nested objects or nested arrays,
		* only their names (keys).
		*
		* @param  array   $array  the array
		* @param  string  $name   the name of the array for display purposes
		* @return string  representing array with markup
		* @access public
		*/
		static public function summarizeArray($array, $name)
		{
			$objects = "<br>Objects in $name<br>";
			$arrays = "<br>Arrays in $name<br>";
			$true = "<br>True in $name<br>";
			$false = "<br>False in $name<br>";
			$nulls = "<br>NULL in $name<br>";
			$nums = "<br>Numbers in $name<br>";
			$strs = "<br>Strings in $name<br>";
			$else = "<br>others in $name<br>";
			foreach ($array as $key => $value) {
				if (is_object($value)) $objects .= " '$key',";
				elseif (is_array($value)) $arrays .= " '$key',";
				elseif (true === $value) $true .= " '$key',";
				elseif (false === $value) $false .= " '$key'";
				elseif (is_null($value)) $nulls .= " '$key',";
				elseif (is_numeric($value)) $nums .= " '$key' => $value,<br>";
				elseif (is_string($value)) $strs .= " '$key' => '$value',<br>";
				else $else += " '$key' => $value,";
			}
			return "<h2>$name</h2>".$objects . '<br>' . $arrays . '<br>' . $true . '<br>' 
				. $false . '<br>' . $nulls . '<br>' . $nums . $strs . $else;
		}
		/**
		* This static method takes an array and returns an html string
		* It DOES display the contents nested arrays but not the contents of objects
		* or double nested arrays, etc
		*
		* @param  array   $array  the array
		* @param  string  $name   the name of the array for display purposes
		* @param  array   $allow  (Optional) array of allowed nested arrays to display (omitting displays all)
		* @return string  representing array with markup
		* @access public
		*/
		static public function display2dArray($array, $name, $allow=true)
		{
			$contents = "";
			foreach ($array as $key => $value) {
				if (is_object($value))
					$contents .= "'$key' => (Object),<br>";
				elseif (is_array($value)) {
					if ($allow === true || in_array($key, $allow)) {
						$arrays .= "<h1>".$name."['".$key."']</h1>";
						foreach ($value as $inner_key => $inner_value) {
							if (is_object($inner_value)):
								$arrays .= "<b>'$inner_key' => (Object),<br>";
							elseif (is_array($inner_value)):
								$arrays .= "<b>'$inner_key' => (Array),<br>";
							else:
								$arrays .= "<b>'$inner_key'</b> => $inner_value,<br>";
							endif;
						}
					}
				} 
				else $contents .= "<b>$key</b> => $value,<br>";
			}
			return "<h1>$name</h1>".$contents.$arrays ;
		}
		/**
		* This static method determines if argument is an Closure object
		*
		* @param  mixed   $var of any type
		* @return boolean true if $var is a Closure object
		* @access public
		*/
		static public function isClosure($var) {
			return is_object($var) && ($var instanceof Closure);
		}

		/**
		* This static method take no arguments and 
		* returns full path of WordPress home path.
		* Use when get_home_path() might not be available.
		* MUST be called from somewhere inside /wp-content
		*
		* @return string  home path of WordPress installation
		* @access public
		*/
		static public function wpHomePath() { # just like get_home_path() from WP
			return substr( __FILE__, 0, strpos(__FILE__, "wp-content") );
		}
		/**
		* Returns true of argument 1 path (string) contains argument 2 (string).
		* Duplicate backslashes are removed before comparison is made.
		*
		* @param  string  $path file path or url
		* @param  string  $hash path substring
		* @return bool    true or false
		* @access public
		*/
		static public function pathHas ($path, $has) {
		    $path = preg_replace('#/+#','/',$path); # turn // to /
		    return (strpos($path, $has) !== false) ? true : false;
		}
	}
}

