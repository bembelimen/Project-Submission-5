<?php

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER)) die;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

define('BASE_DIR', __DIR__);

include_once 'autoload.php';

AppConfig::readFile();

if ($argc == 1)
{
	// No Loader given
	echo '[DONE]';

	return;
}

$parameters = $argv;

// Remove the file path
array_shift($parameters);

$params = array();

foreach ($parameters as $param)
{
	$temp = explode('=', $param);

	if (isset($temp[0]) && isset($temp[1]))
	{
		$params[$temp[0]] = $temp[1];
	}
}

$loadbalancer = new \oneAndOne\LoadBalancer();
$loadbalancer->checkLoad($params['loader']);
