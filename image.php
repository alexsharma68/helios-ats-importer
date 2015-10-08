<?php
//echo phpinfo();
ini_set("display_errors", "1");
error_reporting(E_ALL); 
require_once('../../../wp-load.php');
require_once 'class/captcha.class.php';
$settings = array(
	'font_dir'		=> plugin_dir_path(__FILE__).'class/fonts',
	'wordlist'		=> plugin_dir_path(__FILE__).'class/wordlist.php',
	'log_dir'		=> plugin_dir_path(__FILE__).'class/logs'
);
////print_r($settings);
$c = new Captcha();
$c->set_variables($settings);
echo $c->create();
//echo "test";
?>