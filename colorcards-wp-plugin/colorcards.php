<?php
/*
 * Plugin Name: Colorcards Styler Plugin
 * Plugin URI: http://github.com/Jeff-Russ/
 * Description: Beautify long post/page content and add ease of navigation with Colorcards Shortcodes!
 * Version: 0.1
 * Author: Jeff Russ
 * Author URI: http://www.jeffruss.com
 * License: GPL2
 */

// HOOKS //////

add_action( 'wp_footer', 'add_local_js_to_footer', 100 );
function add_local_js_to_footer() {
	$path = strstr(__DIR__, '/wp-content');
	echo "<script src='${path}/colorcards.min.js'></script>";
}

add_action( 'wp_head','add_local_css_to_head' );
function add_local_css_to_head() {
	$path = strstr(__DIR__, '/wp-content');
	echo "</p><link rel='stylesheet' type='text/css' href='${path}/colorcards.min.css'></p>";
}

// RE-USABLE FUNCTIONS //////

function replace_dashes($string) {
		$string = str_replace("-", " ", $string);
		
		return $string;
}
// shout out: http://cubiq.org/the-perfect-php-clean-url-generator
setlocale(LC_ALL, 'en_US.UTF8');
function toAscii($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}

// SHORTCODES //////

// Add jQuery
if ( !function_exists('enqueue_jquery_cdn') ){
	add_action('wp_enqueue_scripts', 'enqueue_jquery_cdn');
	function enqueue_jquery_cdn()
	{ // https://css-tricks.com/snippets/wordpress/include-jquery-in-wordpress-theme/
		if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
		function my_jquery_enqueue(){
			wp_deregister_script('jquery');
			wp_register_script('jquery',
				"http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "")
				. "://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js",
				false, null); // we may want to add a version number like: , array('jquery'), '1.10.2', true );
			wp_enqueue_script('jquery');
		}
	}
}



add_shortcode('collapsible', 'collapsible_cb');
function collapsible_cb($atts, $content) {

	$defaults = array(
		'title' => 'Click To View',
		'class' => 'card',
		'color' => 'default',
		'size' => '',
		'show' => 'false'
	);
	$atts = shortcode_atts( $defaults, $atts );

	$section_hash = toAscii( $atts['title'] );

	if ( $atts['show'] === 'false' ) $checked = '';
	else $checked = 'checked';

	$template = "	<div class='collapsible' id='$section_hash'>
		<input type='checkbox' id='ccc-checkbox-$section_hash' $checked/>
		<label class='{$atts['class']} {$atts['color']} clickable' for='ccc-checkbox-$section_hash'>{$atts['title']}</label>
		<section>
			$content
		</section>
	</div>";
	return $template;
}

