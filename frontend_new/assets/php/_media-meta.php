<?php

$date = date("YmdHis");

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (empty($_FILES)) {
        $response['code'] = 0;
        $response['message'] = "No file found";
        goto data_output;
    }

    $tempFile = $_FILES['media_file']['tmp_name'];
    $ext = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);

//    if (isset($_POST['category']) && empty($_POST['category'])) {
//        $response['code'] = 0;
//        $response['message'] = "Category is required";
//        goto data_output;
//    }

    if (in_array(strtolower($ext), array("png", "jpg", "jpeg"))) {

        $type = "image";
        $res = getimagesize($tempFile);

    } else if (in_array(strtolower($ext), array("mp4"))) {

        $type = "video";
        $res = array("N/A", "N/A");

    } else {

        $response['code'] = 0;
        $response['message'] = "Invalid File Extension";
        goto data_output;

    }

    $fileName = $date . '.' . $ext;

    $newFile = $_SERVER['DOCUMENT_ROOT'] . '/upload/media/_temp/' . $fileName;
    move_uploaded_file($tempFile, $newFile);

    $response['code'] = 1;
    $response['image_data'] = array(
        'url' => 'upload/media/' . $fileName,
        'name' => str_replace('.' . $ext, '', $fileName),
        'type' => $type,
        'filesize' => filesize($newFile),
        'resolution' => $res[0] . 'x' . $res[1],
        'alt' => str_replace('.' . $ext, '', $fileName),
        'extension' => '.' . $ext,
    );
    $response['extra'] = array(
        'tempfile' => $newFile,
        'filename' => 'upload/media/' . $fileName,
        'status' => file_exists($newFile),
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