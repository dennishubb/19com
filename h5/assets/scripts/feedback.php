<?php

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		$email		= $_POST['email'];
		$message	= $_POST['message'];
		$type		= $_POST['type'];
		$captcha	= $_POST['captcha'];
		
		if(!isset($_SESSION['captcha'])){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "验证码错误，请再次输入", 'data' => ''));
			exit();
		}
		
		if($_SESSION['captcha'] != $captcha){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "验证码错误，请再次输入", 'data' => ''));
			exit();
		}
		
		if(strlen($email) <= 0 || $email == 'undefined'){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "请输入邮箱！", 'data' => ''));
			exit();
		}

		if(strlen($message) <= 0 || $message == 'undefined'){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "请输入反馈内容！.", 'data' => ''));
			exit();
		}
		
		if(strlen($type) <= 0 || $type == 'undefined'){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "请点击类别！", 'data' => ''));
			exit();
		}
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "邮箱不符合！", 'data' => ''));
			exit();	
		}
		
		if(mb_strlen($message) > 500){
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "反馈内容请控制500字以内!", 'data' => ''));
			exit();	
		}
		
		$message	= $message." - ".$email;
		$header		= "FROM: 19com<".$_SERVER['SERVER_NAME'].">\r\n";;
		$success	= mail("yjfankui19@163.com", $type, $message, $header);
		
		if($success){
			echo json_encode(array('status' => "success", 'code' => 1, 'statusMsg' => "意见反馈发送成功！", 'data' => ''));
			exit();	
		}else{
			echo json_encode(array('status' => "error", 'code' => 0, 'statusMsg' => "意见反馈发送失败！", 'data' => ''));
			exit();	
		}
	}

?>
