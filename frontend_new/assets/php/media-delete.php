<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token, Content-Type');

include_once('config/config.php');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $filename = (isset($_POST['filename']) && !empty($_POST['filename'])) ? $_POST['filename'] : "";
    $filename = SVR_ROOT . DS . $_POST['filename'];


    if (!file_exists($filename)) {
        $response['code'] = 0;
        $response['message'] = "Provided File does not exist.";
        goto data_output;
    }


    $delete_status = unlink($filename);

    $response['code'] = 1;
    $response['redirect'] = true;
    $response['delete_success'] = $delete_status;

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