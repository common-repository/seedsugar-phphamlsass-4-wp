<?php
require_once dirname(__FILE__) . "/includes/sass/SassParser.php";

$sass_parser = new SassParser();
$res = array( 
	"/[\"'](\\S+\\.scss)[\"']/", 
	"/[\"'](\\S+\\.sass)[\"']/"
);

//intercept stylesheet include and look if there is a .sass or .scss file
// add_filter('stylesheet_uri', 'wpphamlp_style_uri');

function ph4wp_check_sass_str($in)
{
	global $res;

	//do a quick check against .sass & .scss
	if (stripos($in, '.sass') === false && stripos($in, '.scss') === false)
		return $in;
		
	
	//match it
	foreach($res as $re){
		$in = find_replace_file($re, $in);
	}
	
	return $in;
	
}

//lets define an output handler and check it for sass, replacing them later
function ph4wp_check_sass_file($infile)
{	global $res;
	
	if (!CHECK_4_SASS) return;

	
	
	$file_cont = file_get_contents($infile);
	
	// echo "Check_sass called with '$infile':\n";
	// echo $file_cont . "\n";
	
	//do a quick check against .sass & .scss
	if (stripos($file_cont, '.sass') === false && stripos($file_cont, '.scss') === false)
		return;
		
	
	//match it
	foreach($res as $re){
		$file_cont = find_replace_file($re, $file_cont);
	}
	
	
	//now write back to template
	
	// echo "Would write back: '$file_cont'\n";
	
	file_put_contents($infile, $file_cont);

	
}

//strangely if I call this function from find_replace_file it turns on output buffering and crashes?
function parse_sass_to_file($fn, $relpath, $str){
	$hash = md5($fn);
	
	
	//parse
	$parsed = $sass_parser->toCss($fn);
	
	$new_file = COMP_SASS_PATH . '/' . $hash . '.css';
	//and replace
	
	if (file_put_contents($new_file, $parsed) !== false){
		$new_file_uri = strtr($new_file, array(ABSPATH => ''));
		$str = strtr($str, array($relpath => $new_file_uri));
	}
	return $str;
}



function find_replace_file($re, $str){
	global $sass_parser;
	
	if (preg_match_all(  $re , $str , $matches)){
		// echo "Found scss/sass:";
		// var_dump($matches);
		// echo "\n";

		//parse file and replace link
		foreach ($matches[1] as $v){
			//check if its a relative path
			$fn = ABSPATH . $v;
			
			//see if its a file
			if (file_exists($fn)){
				$hash = md5($fn);

				//parse
				$parsed = $sass_parser->toCss($fn);
				$relpath = $v;
				$new_file = COMP_SASS_PATH . '/' . $hash . '.css';
				//and replace

				if (file_put_contents($new_file, $parsed) !== false){
					$new_file_uri = strtr($new_file, array(ABSPATH => ''));
					$str = strtr($str, array($relpath => $new_file_uri));
				}
				return $str;
			}
			
			//if not, its an absolute URI, strip off the home_uri
			$relpath = strtr($v, array(site_url() => ''));
			$fn = ABSPATH . '/' . $relpath;
			
			if (file_exists($fn)){
				$hash = md5($fn);

				//parse
				$parsed = $sass_parser->toCss($fn);
				$new_file = COMP_SASS_PATH . '/' . $hash . '.css';
				//and replace

				if (file_put_contents($new_file, $parsed) !== false){
					$new_file_uri = strtr($new_file, array(ABSPATH => ''));
					$str = strtr($str, array($relpath => '/' . $new_file_uri));
				}
				return $str;
			}
			
		}
		
	}
	
	return $str;
}

?>