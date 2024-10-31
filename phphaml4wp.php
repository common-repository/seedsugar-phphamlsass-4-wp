<?php

/*
Plugin Name: PHPHAML for WordPress
Description: Let's you replace your .php templates with .haml templates seamlessly (Based on phamlp plugin for wordpress. Uses Baldrs/phphaml for parsing HAML, and phamlp/sass)
Author: derDoc
Version: 0.1
Author URI: http://derdoc.info
*/

/*
 * Config
 */


define( 'CHECK_4_HAML', get_option('ph4wp_option_haml') );
define( 'CHECK_4_SASS', get_option('ph4wp_option_sass') );


define( 'PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'THEME_DIR', get_stylesheet_directory() );

define('HAML_PATH', THEME_DIR );
define('SASS_PATH', THEME_DIR );

define('COMPILED_PATH', PLUGIN_PATH . 'tmp');
define('COMP_PHP_PATH', PLUGIN_PATH . 'tmp/php');
define('COMP_SASS_PATH', PLUGIN_PATH . 'tmp/css');
define('TEMPLATE_LAYOUT', HAML_PATH . '/layout.php.haml');

/*
 * Template handling
 */

require_once dirname(__FILE__) . "/helpers.php";
require_once dirname(__FILE__) . "/activate_hooks.php";
$haml_parser = null;

if (CHECK_4_HAML){
	require_once dirname(__FILE__) . "/template_hooks.php";
	require_once dirname(__FILE__) . '/includes/haml/HamlParser.class.php';
	add_filter('template_include', 'ph4wp_template_include');
	$haml_parser = new HamlParser(HAML_PATH, COMP_PHP_PATH);
}
if (CHECK_4_SASS){
	require_once dirname(__FILE__) . "/phphaml4wp_sass.php";
	
	
	//start OB
	ob_start('ph4wp_ob_cb');
	// var_dump(ob_get_status(true));
	
	//register the shutdown to stop (flush) output buffer
	add_action( 'shutdown',  'ph4wp_stop_ob');
}

function ph4wp_stop_ob(){
	// trigger_error ( "parse_sass_to_file check 1!", E_USER_ERROR );
	//flush buffer
	ob_flush();
}

function ph4wp_ob_cb($buffer){

	//now we have the complete page before its sent to the browser, check it for sass/scss links
	return ph4wp_check_sass_str($buffer);
}


/**
  * $template_layout is set by the template if it wishes to use a custom layout. 
  *
  * The loader compiles and executes the template, saves its output to $template_output,
  * and then compiles and executes the layout. The layout calls yield() to include the
  * content of the template.
  */

$content_for = array();
$template_output_file = null;
$global_wp_var_names = array( 'posts', 'post', 'wp_did_header', 'wp_did_template_redirect', 'wp_query', 'wp_rewrite', 'wpdb', 'wp_version', 'wp', 'id', 'comment', 'user_ID');


//Admin menu
// Adds our options submenu
function ph4wp_initialize() {
	// But only if that function actually exists
	if ( function_exists('add_options_page') ) {
		// We'll use a longer title for the TITLE element and a shorter one for the options page link
		add_options_page( __( 'PHPHaml 4 Wordpress', 'PHPHaml4WP' ), __( 'PHPHaml4WP', 'PHPHaml4WP' ), 'manage_options', 'phphaml4wp/ph4wp_admin.php');
	}
}
// Register the options menu
add_action( 'admin_menu', 'ph4wp_initialize' );


/**
  * Intercepts template includes using our new filter and looks for a HAML alternative.
  */
  
function ph4wp_template_include($template)
{
   // Globalise the stuff we need
   global $template_output_file, $template_layout, $content_for, $haml_parser, $global_wp_var_names;

	// echo "php4wp template include called!\n";
	
	// Globalise the Wordpress environment
	foreach ($global_wp_var_names as $v){
		global $$v;
	}
   	$wp_vars = compact($global_wp_var_names);

   
	$haml_template = $php_template = null;

   // Is there a haml template?
   if(substr($template, -9) == '.php.haml')
   {
      	$haml_template = $template;
		$php_template = str_replace(".php.haml", ".php", $template);

   }
   else
   {
      	$haml_template = str_replace(".php", ".php.haml", $template);
		$php_template = $template;
   }
   
	if(!file_exists($haml_template))
		//theres no haml template, just return the normal one
		return $template;


	$haml_parser->setFile($haml_template);
	//parse to file, don't execute!
	$comp_tpl_filename = $haml_parser->render(array(), false);

	
	//if theres no layout, return the filename
	if(!file_exists(TEMPLATE_LAYOUT))
		return $comp_tpl_filename;
		
	//else we compile the layout and execute, avoid caching that because it will contain the template output of the template compiled b4 this
   	$template_output_file = $comp_tpl_filename;

	
	$vars = array_merge($wp_vars, array('template_output' => wpphamlp_capture_eval(file_get_contents($template_output_file), $wp_vars)));
	
	$haml_parser->setFile(TEMPLATE_LAYOUT);
	
	//parse to file, don't execute!
	$layout_filename = $haml_parser->render(array(), false);
	
	
	//and return that file's name
	return $layout_filename;
	
	
}

function wpphamlp_capture_eval($string, $vars = array()) {
	extract($vars);
	ob_start();
	eval('?>' . $string);
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

/*
 * Create haml alternatives for the get_* functions
 */

function use_layout($name)
{
   global $template_layout;

   $layout = HAML_PATH . "/layout-$name.php.haml";
   
   if(!file_exists($layout))
   {
      trigger_error("The specified layout could not be found: <em>$layout</em>", E_USER_ERROR);
      die();
   }
   
   $template_layout = $layout;
}
function render($n, $ret = false){
	return render_partial($n, $ret);
}


function render_partial($name, $return = false)
{
	global $haml_parser, $global_wp_var_names;
	// Globalise the Wordpress environment
	foreach ($global_wp_var_names as $v){
		global $$v;
	}
	
	//now store them in an array
	$wp_vars = compact($global_wp_var_names);
   
	$partial_template = HAML_PATH . "/_$name.php.haml";
   
	   if(!file_exists($partial_template))
	   {
	      	$partial_template = HAML_PATH . "/partials/_$name.php.haml";
			if(!file_exists($partial_template)){
				trigger_error("The specified partial could not be found: <em>$partial_template</em>", E_USER_ERROR);
	      		die();
			}
	   }
   
   // Execute the template and save its output   
	$haml_parser->setFile($partial_template);
	$comp_tpl_filename = $haml_parser->render($wp_vars, false);

   
   	if(!$return)
		return display_haml($comp_tpl_filename, $wp_vars, true, true);
	
	
	//else we need to return the string
	return wpphamlp_capture_eval($comp_tpl_filename, $wp_vars);
	
    
}

function content_for($name, $tpl){
	global $content_for,$haml;
	
	
	$ret = $haml->hamlStr2PHP($tpl);
	$cap = wpphamlp_capture_eval($ret);
	$content_for[$name] .= $cap;
		
	// echo "cont4[$name]= '$cap'\n<br>";
	
}

function yield( $content = null, $return = true )
{
	
	global $template_output_file, $content_for, $global_wp_var_names;
	//and store them all in a var
	// Globalise the Wordpress environment
	foreach ($global_wp_var_names as $v){
		global $$v;
	}
	
	//now store them in an array
	$wp_vars = compact($global_wp_var_names);
	$tpl = null;

	if ($content != null)
		$tpl = $content_for[$content];
	
	else if($template_output_file != null)
		$tpl = file_get_contents($template_output_file);

	
	if ($tpl == null){
    	trigger_error("<tt>yield</tt> had no output to emit (\$template_output is empty). Did your template do anything?", E_USER_NOTICE);
    	die();
   	}

   
	//now either exec or return the tpl
	if(!$return){
	   	return display_haml($template_output_file, $wp_vars, true, true);
	}
	
	
		
	//else we need to return the string
	return wpphamlp_capture_eval($tpl, $wp_vars);
   	
}






