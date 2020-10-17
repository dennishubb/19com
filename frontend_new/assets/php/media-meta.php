<?php

require_once('module/getid3/getid3.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token, Content-Type');

include_once('config/config.php');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $dest = "";
    $fileName = "";
    $res = array(0, 0);
    $move_status = false;

    // Check Field Section
    $ck_editor = false;

    $f_type = (isset($_POST['type']) && !empty($_POST['type'])) ? $_POST['type'] : "";
    $f_folder = (isset($_POST['folder']) && !empty($_POST['folder'])) ? $_POST['folder'] : "";
    $f_alt = (isset($_POST['alt']) && !empty($_POST['alt'])) ? $_POST['alt'] : "";


    // Validation
    if (empty($_FILES)) {
        $response['code'] = 0;
        $response['message'] = "No file found";
        goto data_output;
    }


    if (isset($_FILES['upload']) && $_FILES['upload']) {
        $_FILES['file'] = $_FILES['upload'];
        $ck_editor = true;
    }


    $tempFile = $_FILES['file']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $checksum = md5_file($tempFile);

	if (empty($ext)) $ext = "png";
	
    if (in_array($ext, EXT_MEDIA)) {

        if (in_array($ext, EXT_IMAGE)) {

            if (!empty($f_type) && in_array($f_type, array("logo", "favicon"))) {
                $fileName = $f_type . '.' . $ext;
                $type = "image[" . $f_type . "]";
                $dest = DEST_BRAND;
            } else {
                $type = "image/" . $ext;
                $dest = DEST_MEDIA_NEW . $f_folder . DS . '_temp' . DS;
            }
            $res = getimagesize($tempFile);

        } else if (in_array($ext, EXT_VIDEO)) {

            $type = "video/" . $ext;
            $dest = DEST_MEDIA_NEW . $f_folder . DS . '_temp' . DS;

            $getID3 = new getID3;
            $file = $getID3->analyze($tempFile);

            $res[0] = $file['video']['resolution_x'];
            $res[1] = $file['video']['resolution_y'];

        }

    } else if (in_array($ext, array(EXT_XML, EXT_TXT)) && in_array($f_type, array("sitemap", "robots"))) {

        switch ($ext) {
            case EXT_XML:
                $type = "xml";
                $fileName = 'sitemap.' . $ext;
                break;
            case EXT_TXT:
                $type = "text";
                $fileName = 'robots.' . $ext;
                break;
        }

        $dest = DEST_ROOT;

    } else {
        $response['code'] = 0;
        $response['message'] = "Invalid File Type - [." . $ext . "]";
        goto data_output;
    }

    $dest = str_replace(DS . DS . "_temp", DS . "_temp", $dest);

    if (!file_exists($dest)) {
        mkdir($dest, 0755, true);
        chmod($dest, 0755);

    }

    if (empty($fileName)) {
        $fileName = $checksum . '.' . $ext;
    }

    if ($ck_editor) {
        $dest = str_replace("_temp" . DS, '', $dest);
    }

    $move_status = move_uploaded_file($tempFile, $dest . $fileName);

    $response['temp_dest'] = $dest;
    $response['temp_filename'] = $fileName;

    $non_temp_file = str_replace(SVR_ROOT . DS, '', $dest) . $fileName;
    $non_temp_file = str_replace("_temp" . DS, '', $non_temp_file);


    // Response Output
    $response['code'] = 1;
    if ($ck_editor) {

        $response['url'] = str_replace('\\', '/', $non_temp_file);
        $response['name'] = $fileName;
        $response['md5'] = str_replace('.' . $ext, '', $fileName);
        $response['type'] = $type;
        $response['filesize'] = filesize($dest . $fileName);
        $response['resolution'] = $res[0] . 'x' . $res[1];
        $response['extension'] = $ext;

    } else {

        $response['url'] = str_replace('\\', '/', $non_temp_file);
        $response['name'] = $fileName;
        $response['md5'] = str_replace('.' . $ext, '', $fileName);
        $response['type'] = $type;
        $response['filesize'] = filesize($dest . $fileName);
        $response['resolution'] = $res[0] . 'x' . $res[1];
        $response['extension'] = $ext;

    }

    $response['move'] = $move_status;
    $response['extra'] = $dest . $fileName;

} else {

    $response['code'] = 0;
    $response['message'] = "405 - Invalid Method";

}


data_output:

$response['post'] = $_POST;
//$response['post']['file'] = $_FILES['file'];

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
exit();
