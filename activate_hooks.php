<?php

/*
 * Setup and teardown
 */

register_activation_hook( dirname(__FILE__) . "/phphaml4wp.php", function() {
  add_option('Activated_Plugin','ph4wp');
});

add_action('admin_init', function () {
    if(is_admin()&&get_option('Activated_Plugin')=='ph4wp') {
    	delete_option('Activated_Plugin');
     	/* do stuff once right after activation */
		ph4wp_activate();
    }
});

// register_activation_hook(dirname(__FILE__) . "/phphaml4wp.php", 'ph4wp_activate');
// register_deactivation_hook(__FILE__, 'wpphamlp_deactivate');

function ph4wp_activate()
{
	// echo "Activation hook called!\n";
	
   if(!ph4wp_check_writeable(COMPILED_PATH))
   {  
      add_action('admin_notices', 'ph4wp_comp_warning');
   }
   else if(!ph4wp_check_writeable(COMPILED_PATH . '/php'))
   {  
      add_action('admin_notices', 'ph4wp_php_warning');
   }

   else if(!ph4wp_check_writeable(COMPILED_PATH . '/css'))
   {  
      add_action('admin_notices', 'ph4wp_css_warning');
   }
}

function ph4wp_check_writeable($f){
	
	if (!file_exists($f)){
		//check if parent is writable first
		if (!is_writable(dirname($f)))
			return false;
		return mkdir($f);
	}
	return ( is_dir($f) && is_writable($f) );
}

function ph4wp_deactivate()
{
}

function ph4wp_comp_warning() 
{
   echo "<div class='updated fade'><p>PHPHaml4WP will currently not work: You need to create <em>" . COMPILED_PATH . "</em> and make sure it's writeable by your webserver (if the parent dir were writable, I would have created it for you).</p></div>";
}

function ph4wp_php_warning() 
{
   echo "<div class='updated fade'><p>PHPHaml4WP will currently not work: You need to create <em>" . COMPILED_PATH . "/php</em> and make sure it's writeable by your webserver (if the parent dir were writable, I would have created it for you).</p></div>";
}
function ph4wp_css_warning() 
{
   echo "<div class='updated fade'><p>PHPHaml4WP works, but for sass templates to be parsed you will need to create <em>" . COMPILED_PATH . "/css</em> and make sure it's writeable by your webserver (if the parent dir were writable, I would have created it for you).</p></div>";
}