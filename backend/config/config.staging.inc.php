<?php

/** Configuration Variables **/
define('DEVELOPMENT_ENVIRONMENT', TRUE);

define('BASE_URL', getDomain());

define('DB_TYPE', 'mysql');
define('DB_USER', 'evdgpg_user');
define('DB_PASSWORD', 'bkST2pE9KFsx4KvC');
define('DB_HOST', '178.128.25.61');
define('DB_NAME', '19com');

//salt for any password encrytion
define('SALT', 'p@ssw0rd_h@sh');

//set timezone
date_default_timezone_set("Asia/Kuala_Lumpur");