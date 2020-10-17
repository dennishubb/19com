<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

//include('classes/Session.class.php');
//$user_session = new Session();

if($_SERVER['REQUEST_METHOD'] != 'OPTIONS')
	require_once(ROOT . DS . 'include' . DS . 'init.inc.php');