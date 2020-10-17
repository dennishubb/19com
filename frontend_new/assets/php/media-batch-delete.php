<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token, Content-Type');

include_once('config/config.php');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $filenames = (isset($_POST['filename']) && !empty($_POST['filename'])) ? $_POST['filename'] : "";

    $files = array();
    foreach ($filenames as $filename) {

        $formatted_filename = SVR_ROOT . DS . $filename;

        $exist = false;
        $deleted = false;
        if (file_exists($formatted_filename)) {
            $exist = true;
            $deleted = unlink($formatted_filename);
        }

        $item = array();
        $item['exist'] = $exist;
        $item['delete'] = $deleted;
        $item['filename'] = $formatted_filename;

        $files[] = $item;
    }

    $response['code'] = 1;
    $response['redirect'] = true;
    $response['_files'] = $files;

} else {

    $response['code'] = 0;
    $response['message'] = "405 - Invalid Method";

}

log_request($response);

data_output:

$response['post'] = $_POST;

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
exit();