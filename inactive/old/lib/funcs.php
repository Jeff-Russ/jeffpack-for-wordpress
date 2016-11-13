<?php


// Functions ////

// arg1 is a string of a path, www or local
// arg2 ca be 'dirname', 'basename', 'extension' or 'filename'
// but 'filename' is only since PHP 5.2.0
function path_substr($path, $part='basename') {
	$path_parts = pathinfo($path);
	return $path_parts[$part];
}

function filename_base ($path) {
	$i = strrpos($path, "/");
	if ($i) $filename = substr($path, $i + 1);
	else $filename = $path;
	$i = strpos($filename, ".");
	if ($i) $return = substr($filename, 0, $i);
	else $return = $filename;
	return $return;
}

// For displaying octal number without converting
function octal2str ($octal_num) { return sprintf("%04o", $octal_num); }

// For displaying booleans
function bool2str ($bool_val) { return $bool_val ? 'true' : 'false'; }

// Note that this is not what you should use for integer indexed arrays.
function named_opts_defaults ($defaults, $opts_arr) {
	if (is_null($defaults)):     return $opts_arr;
	elseif (is_null($opts_arr)): return $defaults;
	else: return array_merge($defaults, $opts_arr);
	endif;
}

// return string after final /
function str_after_bslash ($string) {
	return array_pop(explode('/', $string));
}

function enforce_versions( $min_wp=null, $min_php=null ) {
	global $wp_version;
	global $JP;
	if ( is_null($min_wp) ) $min_wp = $JP['min_wp'];
	if ( is_null($min_php) ) $min_php = $JP['min_php'];
	if ( version_compare( PHP_VERSION, $min_php, '<' ) )
		$flag = 'PHP';
	elseif
		( version_compare( $wp_version, $min_wp, '<' ) )
		$flag = 'WordPress';
	else
		return false;
	$version = 'PHP' == $flag ? $min_php : $min_wp;
	$tempvar = debug_backtrace();
	$plugin_file = $tempvar[0]['file']; // <- value of __FILE__ in calling script
	$tempvar = get_plugin_data($plugin_file);
	$basen = plugin_basename( $plugin_file );
	$plugin_name = $tempvar['Name'];
	deactivate_plugins( $basen );
	$opts = array( 'response'=>200, 'back_link'=>TRUE );
	wp_die ("<p><strong>$basen</strong> requires ".$flag.' version '.$version.' or greater.</p>','Plugin Activation Error', $opts);
}

// Merges two integer indexed arrays with second arg overriding first
// Therefore the arg1 array could be for defaults values.
// The arg2 array is written to by reference and returned. 
function array_defaults ($int_idx_def_arr, &$int_idx_arr) {
	if (is_null($int_idx_def_arr)): return $int_idx_arr;
	elseif (is_null($int_idx_arr)): return $int_idx_def_arr;
	else:
		$int_idx_arr = $int_idx_arr+$int_idx_def_arr;
		return $int_idx_arr;
	endif;
}

// Send a command (arg1) to the shell and return it's output.
// Arg2 will have the exit code written to it.
// Arg3 (optional) will have the return + exit code appended to it.
function capshell ($cmd, &$exit_code, &$log="") {
	exec($cmd, $output, $exit_code);
	$log .= "\n\nRunning exec($cmd)\nOutput:\n" . print_r($output, true) . "\nExit Code: $exit_code\n";
	return $output;
}

// Make directory (arg1) if it does not exit.
// Result info is appended to arg2 string.
// Return is true if dir did not exist and mkdir was called. 
// Remaining args are forwarded to mkdir()
function jr_mkdir ($mkdir_args, &$log="") {
	$defaults = array("./new_directory", 0755, true);
	$args = array_defaults($defaults, $mkdir_args);
	if ( ! file_exists($args[0]) ):
		$log .= "\nnRunning mkdir($args[0],".octal2str($args[1]).",".bool2str($args[2]).")";
		mkdir($args[0], $args[1], $args[2]);
		return true;
	else:
		$log .= "\nThe directory $args[0] already existed";
		return false;
	endif;
}

// fopen file (arg1) if it does not exit.
// Result info is appended to arg2 string.
// Return is return of fopen if called or false if not.
// Optional arg3 will be written to the file if provided.
// File will remain open after call and should be closed.
function jr_mkfile ($path, $content=null, &$log="") {
	if ( ! file_exists($path) ) {
		$log .= "\nCreating file: $path"; 
		$file = fopen("$path", "w") or die("Unable to create $path file!");
		if ( isset($content) ):
			fwrite($file, $content);
			fclose($file);
			return true;
		else: return $file;
		endif;
	} else {
		$log .= "\nThe file $path existed";
		return false;
	}
}

// same as above but will overwrite file if found
function jr_setfile ($path, $content="", &$log="") {
	if ( file_exists($path) ) unlink($path);
	$log .= "\nCreating file: $path"; 
	$file = fopen("$path", "w") or die("Unable to create $path file!");
	if ( isset($content) ):
		fwrite($file, $content);
		fclose($file);
		return true;
	else: return $file;
	endif;
}

// Create .htaccess in directory (arg1) 
// and set it to not allow any www access at all. 
// Result info is appended to arg2 string.
function ht_restrict ($path, &$log="") {
	jr_mkfile("$path/.htaccess", "Options -Indexes\ndeny from all\n", $log);
}

// Makes the directory jp_bin/ at the root of wordpress 
// Also restricts it's www access and returns it's path.
// Result info is appended to arg1 string.
function make_jp_bin (&$log="") {
	global $JP;
	$path = $JP['wp_path'] . "jp-bin"; // $bin = dirname(__FILE__) . "/bin";
	jr_mkdir([$path], $log); 
	ht_restrict ($path, $log);
	return $path;
}

// Installs gits bash script (see github.com/jeff-russ/gits) at location (arg1)
// The script is made executable but must be called by specifying path
// which is the return value of this function.
// Result info is appended to arg2 string.
function install_gits($path, &$log="") {

	if ( file_exists("$path/gits") ) {
		$log .= "\nThe file$path/gits already existed. Checking for updates... "; 
		capshell("$path/gits --update --bin-path $path", $exit_code, $log);
	}
	if ( ! file_exists("$path/gits") || $exit_code != 0 ) {
		$log .= "\nThe file $bin/gits not found. Downloading gits-installer... ";
		$log .= shell_exec("cd $path; curl -O https://raw.githubusercontent.com/Jeff-Russ/gits/master/gits-installer");
		$log .= "\nchmod 755 $path/gits-installer... ";
		$log .= shell_exec("chmod 755 $path/gits-installer");
		$log .= "\nRunning: $path/gits-installer --update --bin-path $path.... ";
		$log .= shell_exec("$path/gits-installer --update --bin-path $path");
		$log .= "\nDeleting installer...";
		unlink("$path/gits-installer");
	}
	return "$path/gits";
}

function curl_executable ($path, $url, &$log="") {
	$filename = str_after_bslash($url);
	if ( file_exists("$path/$filename") ) {
		$log .= "\Download/install of $filename canceled since it already existed.";
		return false;
	} else {
		$log .= "\Downloading and installing of $filename since it was not found.";
		chdir($path);
		capshell("curl -O $url", $exit_code, $log);
		if ($exit_code != 0) return false;
		capshell("chmod 755 ./$filename", $exit_code, $log);
		if ($exit_code != 0): return false;
		else: return true;
		endif;
	}
}

// Goes to path (arg1) and and clones a git repository.
// Arg2 is the domain ("github.com" for example).
// Arg3 is the username on github or whatever which would appear in URL.
// Arg4 is repository name which would appear in URL.
// Result info is appended to arg5 string.
// NOTE: clone won't run if a directory exists matching repo name.
// false is returned if clone fails or not called, otherwise true is returned.
function git_clone ($path, $domain, $username, $reponame, &$log="") {
	if ( file_exists("$path/$reponame") ) {
		$log .= "\nClone canceled since directory already existed.";
		return false;
	} else {
		chdir($path);
		capshell("git clone git@$domain:$username/$reponame.git", $exit_code, $log);
		if ($exit_code != 0) return false;
		else return true;
	}
}

// https://halfelf.org/2016/debugging-unexpected-output/
function debug_unexpected_output ($bool=false) {
	if ($bool) {
		function myplugin_activated_plugin_error() {
			update_option( 'myplugin_error',  ob_get_contents() );
		}
		function myplugin_deactivated_plugin_error() {
			delete_option( 'myplugin_error' );
		}
		add_action( 'activated_plugin', 'myplugin_activated_plugin_error' );
		add_action( 'deactivated_plugin', 'myplugin_deactivated_plugin_error' );
		 
		function myplugin_message_plugin_error() {
		?>
		<div class="notice notice-error">
			<p><?php echo get_option( 'myplugin_error' ); ?></p>
		</div>
		<?php
		}
		if( get_option( 'myplugin_error' ) ) {
			add_action( 'admin_notices', 'myplugin_message_plugin_error' ); 
		}
	}
}

function dl_and_install_plugin ($url, &$log) {
	global $JP;
	$plugins = $JP['wp_path'] . '/wp-content/plugins';
	$filename_w_ext = path_substr($url, 'basename');
	$zipFile = $plugins . $filename_w_ext;
	$log .= shell_exec("cd $plugins; curl -O $url");

	// $zip = new ZipArchive;
	// if($zip->open($zipFile) != "true"){
	//  $log .= "\nError!!!  Unable to open the Zip File";
	// } 
	// $zip->extractTo($plugins);
	// $zip->close();
}

// $wordpress_path = "/path/to/my/wordpress/install";    
// require_once( $wordpress_path . "/wp-load.php" ); //not sure if this line is needed
// //activate_plugin() is here:
// require_once(  $wordpress_path . "/wp-admin/includes/plugin.php");
// $plugins = array("cforms",  "w3-total-cache",  "wordpress-seo");
// foreach ($plugins as $plugin){
// $plugin_path = $wordpress_path."wp-content/plugins/{$plugin}.php";
//   activate_plugin($plugin_path);
// }
// function install_plugin_from_gh () {

// }

