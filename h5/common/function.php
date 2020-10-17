<?php
	function httpGet($accessUrl) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$accessUrl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	function httpPost($accessUrl, $params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$accessUrl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	function echoJson($param) {
		header('Content-type: application/json');
		echo json_encode($param);
	}

	function array_sort ($array, $field, $sort = 'ASC') {
		$tmp = array();
		foreach ($array as $key => $value) {
			$tmp[$value[$field].mt_rand(1000, 9999)] = $value;
		}
		if ($sort == 'ASC') {
			ksort($tmp);
		}
		else if ($sort == 'DESC') {
			krsort($tmp);
		}

		$new_array = array();
		foreach ($tmp as $key => $value) {
			array_push($new_array, $value);
		}
		return $new_array;
	}
	
	function encrypt($data, $secret) {
	    $key = md5(utf8_encode($secret), true);
	    $key .= substr($key, 0, 8);
	    $block_size = @mcrypt_get_block_size('tripledes', 'ecb');
	    $length = strlen($data);
	    $pad = $block_size - ($length % $block_size);
	    $data .= str_repeat(chr($pad), $pad);
	    $encrypted_data = @mcrypt_encrypt('tripledes', $key, $data, 'ecb');
	    return base64_encode($encrypted_data);
	}

	function decrypt($data, $secret) {
	    $key = md5(utf8_encode($secret), true);
	    $key .= substr($key, 0, 8);
	    $data = base64_decode($data);
	    $data = @mcrypt_decrypt('tripledes', $key, $data, 'ecb');
	    $block = @mcrypt_get_block_size('tripledes', 'ecb');
	    $length = strlen($data);
	    $pad = ord($data[$length-1]);
	    return substr($data, 0, strlen($data) - $pad);
	}

	// function verify_user ($euid, $key) {
	// 	$euid = decrypt($euid, $key);
	// 	if (is_null(json_decode($euid))) {
	// 		$data['status'] = -201;
	// 		$data['message'] = '非法用户';
	// 		echoJson($data);
	// 		exit(0);
	// 	}
	// 	$user_json = json_decode($euid, true);
	// 	$user_id = $user_json['id'];
	// 	$user_name = $user_json['username'];

	// 	$user = $database->get('user', ['id', 'username'], ['id' => $user_id, 'username' => $user_name]);
	// 	if (!$user) {
	// 		$data['status'] = -202;
	// 		$data['message'] = '非法用户';
	// 		echoJson($data);
	// 		exit(0);
	// 	}
	// 	return $user;
	// }
?>