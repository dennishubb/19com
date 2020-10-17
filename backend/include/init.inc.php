<?php

require_once(ROOT . DS . 'include' . DS . 'shared_function.inc.php');

require_once(ROOT . DS . 'config' . DS . 'app.config.inc.php');
require_once(ROOT . DS . 'config' . DS . 'config.' . SERVER_STATE . '.inc.php');

require_once(ROOT . DS . 'include' . DS . 'common.inc.php');
require_once(ROOT . DS . 'include' . DS . 'db.inc.php');
require_once(ROOT . DS . 'include' . DS . 'accounting.inc.php');

$session = new Session();
$session->setCookie();
$url = parse_path();

//router must be the last to call
require_once(ROOT . DS . 'include' . DS . 'router.inc.php');
