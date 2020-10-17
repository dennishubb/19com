<?php

// custom error handler:
@session_start();
set_error_handler('my_error_handler');
spl_autoload_register('my_autoloader');
if (DEVELOPMENT_ENVIRONMENT == TRUE) {
    ini_set('display_errors', 1);
    error_reporting(~0);
}

/** Custom error handle that send mail if lived **/
function my_error_handler($e_number, $e_message, $e_file, $e_line, $e_vars)
{
    $message = "An error occurred in script '$e_file' on line $e_line:\n$e_message\n";
    $message .= "<pre>" . print_r(debug_backtrace(), 1) . "</pre>\n";
    if (DEVELOPMENT_ENVIRONMENT == TRUE) {
        var_dump($message);
    } else {
        if ($e_number != E_NOTICE) {
            echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div>';
        }
    }
    return true;
} // End of my_error_handler() definition.

/** Autoload any classes that are required **/
function my_autoloader($className)
{

    if (file_exists(ROOT . DS . 'classes' . DS . $className . '.class.php')) {
        require_once(ROOT . DS . 'classes' . DS . $className . '.class.php');
    } else if (file_exists(ROOT . DS . 'template' . DS . $className . '.class.php')) {
        require_once(ROOT . DS . 'template' . DS . $className . '.class.php');
    } else if (file_exists(ROOT . DS . 'model' . DS . $className . '.class.php')) {
        require_once(ROOT . DS . 'model' . DS . $className . '.class.php');
    } else if (file_exists(ROOT . DS . 'model' . DS . $className . '.php')) {
        require_once(ROOT . DS . 'model' . DS . $className . '.php');
    } else if (file_exists(ROOT . DS . 'model_payment_gateway' . DS . $className . '.class.php')) {
        require_once(ROOT . DS . 'model_payment_gateway' . DS . $className . '.class.php');
    } else {
        echo 'Class doesn\'t exist : ' . $className;
    }
}//add more else if(file_exist)(__classes_folder__) if needed