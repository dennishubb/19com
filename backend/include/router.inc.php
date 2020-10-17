<?php

$url = parse_path();

/** Trim last array if the value is "" **/
if (isset($url['call_parts'])) {
    $lastElement = end($url['call_parts']);
    if ($lastElement == "") {
        $key = key($url['call_parts']);
        unset($url['call_parts'][$key]);
    }
}


if (count($url['call_parts']) > 0) {
    find_routes_v2($url['call_parts']);
} else {
    include_once("apps/index.php");
}


function find_routes_v2($url_parts, $additional_dir = false)
{
	include_once('model/user.php');
	
	$login_user = new user();
	
    $url_path = implode(DS, $url_parts);
    $filepath = 'apps/' . ($additional_dir ? $additional_dir . '/' : '') . $url_path . ".php";


    /** START - You should work on this region **/
    global $dbc;

    $weblog = new weblog($dbc);

    $data_out = NULL;
    $response = array();
    $error = array();
    $user_id = 0;
    $timeStart = time();
    $route = $url_path;
	$route_array = explode('/', $route);
	$module_name = end($route_array);
	
	$allowed_post = array('login', 'user', 'forget_password', 'reset_password');
	$allowed_user_modules = array(
		'login' => array('POST'),
		'forget_password' => array('POST'),
		'reset_password' => array('POST'),
		'prediction' => array('PUT', 'POST', 'DELETE', 'PATCH'),
		'prediction_user_favourite' => array('PUT', 'POST', 'DELETE'),
		'prediction_top_ten_unlock' => array('POST'),
		'message' => array('POST', 'PUT'),
		'message_report' => array('POST'),
		'message_like' => array('POST', 'DELETE', 'PATCH'),
		'gift_redeem' => array('POST'),
		'promotion_redeem' => array('POST'),
		'user' => array('PUT')
	);
	
	if(array_key_exists('HTTP_AUTHORIZATION', $_SERVER)){
		$authorization_token = $_SERVER['HTTP_AUTHORIZATION'];
		
		if(strlen($authorization_token) > 0){
			$login_user->where('token', $authorization_token)->getOne();
		}
	}

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $params = $_GET;
		if($module_name != 'get-captcha'){
			$class = new $module_name;

			//allow view private data for logged in users only
			if($class->privacySetting == 1){
				if(!$login_user->id){
					if($module_name == 'gift_redeem'){
						responseFail($error, "Unauthorized", 401);
					}else{
						responseFail($error, "forbidden", 403);
					}
				}
			}
		}
    } else {
		if($login_user->id && $login_user->type == 'Member'){
			if(!array_key_exists($module_name, $allowed_user_modules)){
				//do not allow member from changing data other than allowed modules
				responseFail($error, "forbidden", 403);
			}else if(!in_array($_SERVER['REQUEST_METHOD'], $allowed_user_modules[$module_name])){
				//do not allow member from request that is not allowed
				responseFail($error, "forbidden", 403);
			}	
		}else if($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($module_name, $allowed_post)){
			
		}else{
			//do not allow CUD operations without login
			//allow access for ck-finder for upload table related changes only
			if($module_name == 'upload' && $_SERVER['HTTP_USER_AGENT'] == 'CKFinder Upload'){
				
			}else if(!$login_user->id){
				responseFail($error, "Unauthorized", 401);
			}
		}
		
        $params = json_decode(file_get_contents('php://input'), true);
        
        if(!$params){
            $params = $_POST;
        }
    }

    $weblog->insertData($params, $route, $login_user->id ? $login_user->id:0, $_SERVER['REQUEST_METHOD']);
    /** END - You should work on this region **/
	if(!empty($error)){
		
	}else if (file_exists($filepath)) {
        include_once($filepath);
    } else {
        call404(true);
    }


    /** START - You should work on this region **/
    $processedTime = time() - $timeStart;

    if (!empty($response) || !empty($error)) {
        if (!empty($error)) {
			// error - OK
            $data_out = array('status' => "failed", 'code' => -1);
            $data_out = array_merge($data_out, $error);
			
			if($data_out['code'] == 403){
				if($login_user->id){
					$login_user->token = '';
					$login_user->isNew = false;
					$login_user->save();
				}
			}
        } else {
            // response - OK
            $data_out = array('status' => "success", 'code' => 1);
            $data_out = array_merge($data_out, $response);
        }
    } else {
        $data_out = array('status' => "error", 'code' => 0, 'statusMsg' => "Command not found.", 'data' => '');
    }

    $weblog->updateData($data_out, $processedTime, $login_user->id ? $login_user->id:0);

    /** END - You should work on this region **/
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($data_out);
    exit();
}

function find_routes_v1($url_parts, $additional_dir = false)
{
    $url_path = implode(DS, $url_parts);
    $filepath = 'apps/' . ($additional_dir ? $additional_dir . '/' : '') . $url_path . ".php";

    if (file_exists($filepath)) {

        /** START - You should work on this region **/
        global $dbc;

        $weblog = new weblog($dbc);

        $data_out = NULL;
        $timeStart = time();
        $route = $url_path;

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $params = $_GET;
        } else {
            $params = json_decode(file_get_contents('php://input'), true);
        }

        $weblog->insertData($params, $route);


        include_once($filepath);


        $processedTime = time() - $timeStart;

        $weblog->updateData($data_out, $processedTime);

        if (!is_null($data_out)) {
            header("application/json");
            die(json_encode($data_out));
        }

        /** END - You should work on this region **/

    } else {
        call404();
    }

    exit();
}

?>