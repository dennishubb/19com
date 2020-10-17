<?php

define('DS', DIRECTORY_SEPARATOR);
define('SVR_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('LOG_TODAY', date("Ymd") . '.txt');
define('DEST_LOG', SVR_ROOT . DS . 'assets' . DS . 'php' . DS . '_log' . DS);

// extensions (lowercase)
define('EXT_MEDIA', array("png", "jpg", "jpeg", "ico", "mp4"));
define('EXT_IMAGE', array("png", "jpg", "jpeg", "ico"));
define('EXT_VIDEO', array("avi","flv","mov","mpeg","mp4"));
define('EXT_XML', "xml");
define('EXT_TXT', "txt");


// temporary folder
define('DEST_MEDIA', SVR_ROOT . DS . 'upload' . DS . 'media' . DS . '_temp' . DS);
define('DEST_MEDIA_NEW', SVR_ROOT . DS . 'upload' . DS . 'media' . DS);
define('DEST_MEDIA_BASE_URL', '/upload/media/');
define('DEST_BRAND', SVR_ROOT . DS . 'assets' . DS . 'branding' . DS . '_temp' . DS);
define('DEST_ROOT', SVR_ROOT . DS . '_temp' . DS);

function curl_post_request($request) {

}

function log_request($response) {

//    if (!file_exists(DEST_LOG)) {
//        mkdir(DEST_LOG, '0755', true);
//    }
//
//    @$data = fopen(DEST_LOG . LOG_TODAY, 'a+');
//
//    fwrite($data, "\n\n");
//    fwrite($data, date('Y-m-d H:i:s', time()) . ":\n");
//    fwrite($data, 'php://input' . ":\n");
//    fwrite($data, file_get_contents('php://input', 'r'));
//
//    fwrite($data, "\n\n");
//    fwrite($data, date('Y-m-d H:i:s', time()) . ":\n");
//    fwrite($data, 'POST' . ":\n");
//    fwrite($data, var_export($_POST, true));
//
//    fwrite($data, "\n\n");
//    fwrite($data, date('Y-m-d H:i:s', time()) . ":\n");
//    fwrite($data, 'GET' . ":\n");
//    fwrite($data, var_export($_GET, true));
//
//    fwrite($data, "\n\n");
//    fwrite($data, date('Y-m-d H:i:s', time()) . ":\n");
//    fwrite($data, 'header' . ":\n");
//    fwrite($data, var_export(getallheaders(), true));
//
//    fwrite($data, "\n\n");
//    fwrite($data, var_export($response, true));
//
//    fclose($data);

}