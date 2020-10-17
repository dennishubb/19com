<?php

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (isset($_POST['tempfile']) && empty($_POST['tempfile'])) {
        $response['code'] = 0;
        $response['message'] = "Tempfile is required";
        goto data_output;
    }

    if (isset($_POST['filename']) && empty($_POST['filename'])) {
        $response['code'] = 0;
        $response['message'] = "Filename is required";
        goto data_output;
    }

    if (!file_exists($_POST['tempfile'])) {
        $response['code'] = 0;
        $response['message'] = "File does not exist.";
        goto data_output;
    }

    $oldDest = $_POST['tempfile'];
    $newDest = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_POST['filename'];

    rename($oldDest, str_replace('_temp/', '', $newDest));

    $response['code'] = 1;
    $response['redirect'] = true;
    $response['data'] = array(
        'oldDest' => $oldDest,
        'newDest' => $newDest,
        'statusOld' => file_exists($oldDest),
        'statusNew' => file_exists($newDest),
    );

} else {
    $response['code'] = 0;
    $response['message'] = "Invalid Method";
}


data_output:

$response['post'] = $_POST;

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
exit();