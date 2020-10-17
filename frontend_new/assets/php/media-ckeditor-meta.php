<?php
//error_reporting(E_ERROR | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token, Content-Type');

include_once('config/config.php');

$date = date("YmdHis");

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Validate File
    if (empty($_FILES)) {
        $response['code'] = 0;
        $response['message'] = "No file found";
        goto data_output;
    }
    // ckeditor
    if (isset($_FILES['upload']) && $_FILES['upload']) {
        $_FILES['file'] = $_FILES['upload'];
    }
    // ckeditor
    $tempFile = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

    $type = (isset($_POST['type']) && !empty($_POST['type'])) ? $_POST['type'] : "";
    $dest = "";
    $fileName = "";
    $res = array("N/A", "N/A");


    if (in_array($ext, EXT_IMAGE)) {

        if (!empty($type)) {
            switch (strtolower($type)) {
                case "logo" :
                case "favicon":
                    $dest = DEST_BRAND;
                    $fileName = $type . '.' . $ext;
                    $type = "image[" . $type . "]";
                    break;
            }
        } else {
            $type = "image";
        }
        $res = getimagesize($tempFile);

    } else if (in_array($ext, EXT_VIDEO)) {

        $type = "video";

    } else if (in_array($ext, EXT_XML) && $type == "sitemap") {

        $type = "xml";
        $fileName = 'sitemap.' . $ext;
        $dest = DEST_ROOT;

    } else if (in_array($ext, EXT_TXT) && $type == "robots") {

        $type = "text";
        $fileName = 'robots.' . $ext;
        $dest = DEST_ROOT;

    } else {

        $response['code'] = 0;
        $response['message'] = "Invalid File Type - [." . $ext . "]";
        goto data_output;

    }

    if (empty($dest)) {
        $dest = DEST_MEDIA;
    }

    if (!file_exists($dest)) {
        mkdir($dest, '0755', true);
    }

    if (empty($fileName)) {
        $fileName = $date . '.' . $ext;
    }

    $non_temp_file = DEST_MEDIA_BASE_URL . $fileName;
    //$non_temp_file = str_replace(SVR_ROOT . DS, '', $dest) . $fileName;
    //$non_temp_file = str_replace("_temp" . DS, '', $non_temp_file);

    if (isset($_FILES['upload']) && $_FILES['upload']) {

        $dest = str_replace("_temp" . DS, '', $dest);
        move_uploaded_file($tempFile, $dest . $fileName);

    } else {

        move_uploaded_file($tempFile, $dest . $fileName);

    }
    $response['code'] = 1;

    $response['data']['url'] = $non_temp_file;
    $response['data']['url'] = str_replace('\\', '/', $non_temp_file);
    $response['data']['name'] = str_replace('.' . $ext, '', $fileName);
    $response['data']['type'] = $type;
    $response['data']['filesize'] = filesize($dest . $fileName);
    $response['data']['resolution'] = $res[0] . 'x' . $res[1];
    $response['data']['alt'] = str_replace('.' . $ext, '', $fileName);
    $response['data']['extension'] = '.' . $ext;

    $response['extra'] = $dest . $fileName;

} else {

    $response['code'] = 0;
    $response['message'] = "405 - Invalid Method";

}

//log_request($response);

data_output:

$response['post'] = $_POST;
$response['post']['file'] = $_FILES['file'];
//ckeditor
if (isset($_FILES['upload']) && $_FILES['upload'])
    $response['url'] = $response['data']['url'];
//ckeditor
http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
exit();