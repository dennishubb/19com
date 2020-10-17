<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token, Content-Type');

include_once('media-config.php');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $tempfile = (isset($_POST['tempfile']) && !empty($_POST['tempfile'])) ? $_POST['tempfile'] : "";

    if (empty($tempfile)) {
        $response['code'] = 0;
        $response['message'] = "tempfile - is required.";
        goto data_output;

    }

    if (!file_exists($tempfile)) {
        $response['code'] = 0;
        $response['message'] = "Provided File does not exist.";
        goto data_output;
    }

    $rename_status = rename($tempfile, str_replace('_temp' . DS, '', $tempfile));

    $response['code'] = 1;
    $response['redirect'] = true;
    $response['move_success'] = $rename_status;
    $response['location'] = str_replace('_temp' . DS, '', $tempfile); 

} else {

    $response['code'] = 0;
    $response['message'] = "405 - Invalid Method";

}

//log_request($response);

data_output:

$response['post'] = $_POST;

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
exit();