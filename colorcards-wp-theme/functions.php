<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

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

// Add Bootstrap JS 
if ( !function_exists('enqueue_bootstrap_js_cdn') ){
  // toggle cdn option via comment/uncomment!
  add_action('wp_enqueue_scripts', 'enqueue_bootstrap_js_cdn');
  function enqueue_bootstrap_js_cdn() {
    wp_enqueue_script('bs-js',
      '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js',
      array('jquery'), true);
  }
}
if ( !function_exists('enqueue_bootstrap_js_edit') ){
  // toggle local edit option via comment/uncomment!
  add_action('wp_enqueue_scripts', 'enqueue_bootstrap_js_edit');
  function enqueue_bootstrap_js_edit(){
    wp_enqueue_script('bs-js', get_stylesheet_directory_uri()
      .'/bootstraplet.js',array('jquery'), '', true );
  }
}

// Add Bootstrap CSS
if ( !function_exists('enqueue_bootstrap_css_edit') ){
  // toggle local edit option via comment/uncomment!
  add_action('wp_enqueue_style', 'enqueue_bootstrap_css_edit');
  function enqueue_bootstrap_css_edit(){
    wp_enqueue_style('bs-css', get_stylesheet_directory_uri()
      . '/bootstraplet.css');
  }
}
if ( !function_exists('enqueue_bootstrap_css_cdn') ){
  // toggle cdn option via comment/uncomment! 
  add_action('wp_enqueue_style', 'enqueue_bootstrap_css_cdn');
  function enqueue_bootstrap_css_cdn(){
    wp_enqueue_style('bs-css',
      '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );
  }
}

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'genericons' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_ext1', 'http://fonts.googleapis.com/css?family=Mate' );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css' );

// END ENQUEUE PARENT ACTION
?>