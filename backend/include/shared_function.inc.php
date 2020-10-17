<?php


function responseFail(&$error, $message, $code = 0, $data = NULL, $redirect = false)
{
    $error['code'] = $code;
    $error['message'] = $message;
	$error['redirect'] = $redirect;
    if (!is_null($data)) $error['data'] = $data;
}


function call404($dir = "")
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    if (!is_null($dir)) {
        $path = "apps/" . $dir . "/404.php";
        if (file_exists($path)) {
            if (empty($dir)) {
                redirect('index');
            } else {
                find_routes(array($dir, '404'));
            }
        } else {
            redirect('404');
        }
    } else {
        redirect('404');
    }
}

function today($type = true)
{
    if ($type) {
        return date("Y-m-d");
    } else {
        return date("Y-m-d H:i:s");
    }
}

function get_client_device()
{
    return $_SERVER['HTTP_USER_AGENT'];
}

function get_client_ip()
{
    return $_SERVER['REMOTE_ADDR'];
}

function generateHash($value)
{
    return md5($value . time());
}

//please use after session_start
function logout()
{

    // Destroy the session:
    $exception = array('viewer_id');

    foreach ($_SESSION as $key => $value) {
        if (!in_array($key, $exception)) {
            unset($_SESSION[$key]);
        }
    }

}

function redirect($destination, $protocol = '')
{

    $url = $protocol . BASE_URL . '/' . $destination; // Define the URL.
    header("Location: $url");
    exit(); // Quit the script.

} // End of redirect() function.

function getDomain()
{

    // so called safe version from stackoverflow
    if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
        (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
        $protocol = 'https';
    } else {
        $protocol = 'http';
    }

    return $protocol . '://' . $_SERVER['HTTP_HOST'];

}

function parse_path()
{

    $path = array();

    if (isset($_SERVER['REQUEST_URI'])) {

        $request_path = explode('?', $_SERVER['REQUEST_URI']);

        $path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');

        $path['call_utf8'] = substr(urldecode($request_path[0]), strlen($path['base']) + 1);

        $path['call'] = utf8_decode($path['call_utf8']);

        if ($path['call'] == basename($_SERVER['PHP_SELF'])) {
            $path['call'] = '';
        }

        $path['call_parts'] = explode('/', $path['call']);

        if (isset($request_path[1])) $path['query_utf8'] = urldecode($request_path[1]);
        if (isset($request_path[1])) $path['query'] = utf8_decode(urldecode($request_path[1]));

        if (isset($path['query'])) {

            $vars = explode('&', $path['query']);
            foreach ($vars as $var) {
                $t = explode('=', $var);
                if (isset($t[0]) && isset($t[1])) {
                    $path['query_vars'][$t[0]] = $t[1];
                }
            }
        }//isset query

    }//end of isset request url

    return $path;

}

function getDayOfWeekStringEn($int)
{
    $dayOfWeek = "";

    switch ($int) {
        case 1:
            $dayOfWeek = "Monday";
            break;
        case 2:
            $dayOfWeek = "Tuesday";
            break;
        case 3:
            $dayOfWeek = "Wednesday";
            break;
        case 4:
            $dayOfWeek = "Thursday";
            break;
        case 5:
            $dayOfWeek = "Friday";
            break;
        case 6:
            $dayOfWeek = "Saturday";
            break;
        case 7:
            $dayOfWeek = "Sunday";
            break;
    }

    return $dayOfWeek;
}

function getDayOfWeekStringZh($int){
    $dayOfWeek = "";
    
    switch($int){
        case 1:
            $dayOfWeek = "周一";
            break;
        case 2:
            $dayOfWeek = "周二";
            break;
        case 3:
            $dayOfWeek = "周三";
            break;
        case 4:
            $dayOfWeek = "周四";
            break;
        case 5:
            $dayOfWeek = "周五";
            break;
        case 6:
            $dayOfWeek = "周六";
            break;
        case 7:
            $dayOfWeek = "周日";
            break;
    }
    
    return $dayOfWeek;
}

function getMonthStringEn($int)
{
    $month = "";

    switch ($int) {
        case 1:
            $month = "January";
            break;
        case 2:
            $month = "February";
            break;
        case 3:
            $month = "March";
            break;
        case 4:
            $month = "April";
            break;
        case 5:
            $month = "May";
            break;
        case 6:
            $month = "June";
            break;
        case 7:
            $month = "July";
            break;
        case 8:
            $month = "August";
            break;
        case 9:
            $month = "September";
            break;
        case 10:
            $month = "October";
            break;
        case 11:
            $month = "November";
            break;
        case 12:
            $month = "December";
            break;
    }

    return $month;
}

function getMonthStringZh($int){
    $month = "";
    
    switch($int){
        case 1:
            $month = "01月";
            break;
        case 2:
            $month = "02月";
            break;
        case 3:
            $month = "03月";
            break;
        case 4:
            $month = "04月";
            break;
        case 5:
            $month = "05月";
            break;
        case 6:
            $month = "06月";
            break;
        case 7:
            $month = "07月";
            break;
        case 8:
            $month = "08月";
            break;
        case 9:
            $month = "09月";
            break;
        case 10:
            $month = "10月";
            break;
        case 11:
            $month = "11月";
            break;
        case 12:
            $month = "12月";
            break; 
    }
    
    return $month;
}

?>