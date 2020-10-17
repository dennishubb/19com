<?php
do {

    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'email', 'label' => '账号', 'required' => true);
    $fields[] = array('index' => 'password', 'label' => '密码', 'required' => true);
    $fields[] = array('index' => 'captcha', 'label' => '驗證碼', 'required' => true);
    $fields[] = array('index' => 'bypass_captcha', 'label' => '驗證碼');

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    if (empty($cv['bypass_captcha']) && isset($_SESSION['captcha']) && ($_SESSION['captcha'] !== $cv['captcha'])) {
        responseFail($error, "驗證碼错误，请重试。");
        break;
    }

    $user = new users($dbc);
    if (!$user->searchUser($cv['email'])) {
        // Username Not Found
        responseFail($error, "账号不存在");
        break;
    }

    if (!PasswordHasher::VerifyHashedPassword($user->getPassword(), $cv['password'])) {
        // Incorrect Password
        responseFail($error, "密码错误");
        break;
    }

    $auth_token = JWTAuth::build($user->getId(), $user->getEmail());
    if (!$auth_token) {
        // Sumtingwong with Token Builder
        responseFail($error, "服務器出現錯誤");
        break;
    }

    $response['redirect'] = true;
    $response['login_id'] = $user->getId();
    $response['login_name'] = $user->getName();
    $response['login_email'] = $user->getEmail();
    $response['auth_token'] = $auth_token;


} while (0);
