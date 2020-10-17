<?php

/** Configuration Variables **/
define('DEVELOPMENT_ENVIRONMENT', TRUE);

define('BASE_URL', getDomain() . '/hybrid');

define('DB_TYPE', 'mysql');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'paydb_hybrid');

//salt for any password encrytion
define('SALT', 'p@ssw0rd_h@sh');

//set timezone
date_default_timezone_set("Asia/Kuala_Lumpur");