<?php
do {
    
    include_once('model/user.php');
	include_once('model/upload.php');

    $validator  = new Validator;
    $date       = date("Y-m-d H:i:s");

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'username', 'label' => '用户名', 'required' => true);
    $fields[] = array('index' => 'password', 'label' => '密码', 'required' => true);
    $fields[] = array('index' => 'captcha', 'label' => '驗證碼', 'required' => true);
	$fields[] = array('index' => 'type', 'label' => 'type', 'required' => true);
    $fields[] = array('index' => 'bypass_captcha', 'label' => '驗證碼');

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    if (empty($cv['bypass_captcha']) && isset($_SESSION['captcha']) && ($_SESSION['captcha'] !== $cv['captcha'])) {
        responseFail($error, "验证码错误，请再次输入");
        break;
    }

    $user = new user();
	$user = $user->where('username', $cv['username'])->where('type', $cv['type'])->getOne();
    if (!$user) {
        // Username Not Found
        responseFail($error, "账号不存在");
        break;
    }

    if (!PasswordHasher::VerifyHashedPassword($user->password, $cv['password'])) {
        // Incorrect Password
        responseFail($error, "密码错误");
        break;
    }
	
	if($user->deleted == 1){
		responseFail($error, "账号不存在");
        break;
	}
	
	if($user->disabled == 1){
		responseFail($error, "账号不存在");
        break;
	}

    $token = JWTAuth::build($user->id, $user->username);
    if (!$token) {
        // Sumtingwong with Token Builder
        responseFail($error, "服務器出現錯誤");
        break;
    }
    
    $user->login_at = $date;
    $user->token    = $token;
    $user->save();
	
	$user->password	= 0;
	
    $response['redirect'] 		= true;
	
	$userData = $user->data;
	
	if($user->upload_id != 0){
		$upload     = new upload();
		$uploadObj	= $upload->byId($user->upload_id);
		
		if($uploadObj)
			$userData['upload_url'] = $uploadObj->url;
	}
	
    $response['data']['user'] 	= $userData;

} while (0);

?>