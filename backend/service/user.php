<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');
	include(__DIR__ . '/../classes/PasswordHasher.class.php');

	$action = $_REQUEST['action'];

	switch ($action) {
		// case 'getlogininfo':
		// 	$user_id = $_SESSION['user_id'];
		// 	var_dump($user_id);
		// 	break;
		case 'getuserinfo':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];
			$user_name = $user_json['username'];

			$user = $database->get('user', ['id', 'username', 'level_id', 'upload_id', 'points', 'total_points', 'voucher', 'token', 'thumbnail'], ['id' => $user_id, 'username' => $user_name]);
			if (!$user) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user['image'] = '/assets/images/default_user_image.png';
			if ($user['thumbnail'] != '') {
				$user['image'] = $user['thumbnail'];
			}
			else {
				if ($user['upload_id'] > 0) {
					$user['image'] = '/'.$database->get('upload', 'url', ['id' => $user['upload_id']]);
				}
			}
			unset($user['upload_id']);
			unset($user['thumbnail']);
			$user['level'] = $database->get('level', 'name', ['id' => $user['level_id']]);

			$date=new DateTime();
			$date->modify('this week');
			$first_day_of_week=$date->format('Y-m-d');
			$date->modify('this week +7 days');
			$end_day_of_week=$date->format('Y-m-d');

			$weekly_points = 0;
			$weekly_prediction = $database->select('prediction', 'win_amount', ['created_at[<>]' => [$first_day_of_week, $end_day_of_week], 'user_id' => $user_id]);
			foreach ($weekly_prediction as $key => $value) {
				$weekly_points += $value['win_amount'];
			}
			if ($weekly_points <= 0) {
				$weekly_points = 0;
			}
			$user['weekly_points'] = $weekly_points;

			$data['status'] = 200;
			$data['user'] = $user;
			$database->update('user', ['login_at' => date('Y-m-d H:i:s', time())], ['id' => $user_id]);
			echoJson($data);
			break;
		case 'getextrainfo':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];
			$user_name = $user_json['username'];

			$user = $database->get('user', ['name', 'phone', 'email', 'address', 'birth_at', 'weibo'], ['id' => $user_id, 'username' => $user_name]);
			if (!$user) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}

			$data['status'] = 200;
			$data['user'] = $user;

			echoJson($data);
			break;
		case 'login':
			$data = [];
			$captcha = trim($_GET['captcha']);
			if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {
				$data['status'] = -200;
				$data['message'] = '验证码错误，请重新输入';
				echoJson($data);
				exit(0);
			}
			$username = urldecode(trim($_GET['username']));
			if (!$username) {
				exit(0);
			}
			$password = trim($_GET['password']);
			if (!$password) {
				exit(0);
			}
			$user = $database->get('user', ['id', 'username', 'password'], ['username' => $username]);

			if ($user) {
			    if (!PasswordHasher::VerifyHashedPassword($user['password'], $password)) {
					$data['status'] = -200;
					$data['message'] = '密码错误，请重新输入';
			    }
			    else {
			    	unset($user['password']);
					$data['status'] = 200;
					$encrption_str = '{"id":"'.$user['id'].'","username":"'.$user['username'].'"}';
					// $user['encryption_id'] = encrypt($encrption_str, $key);
					$data['euid'] = encrypt($encrption_str, $key);
				}
			}
			else {
				$data['status'] = -200;
				$data['message'] = '此账户不存在';
			}
			$_SESSION['uid'] = $user['id'];
			echoJson($data);
			break;
		case 'register':
			$now = time();
			$captcha = trim($_GET['captcha']);
			if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {
				$data['status'] = -200;
				$data['message'] = '验证码错误，请重新输入';
				echoJson($data);
				exit(0);
			}
			$username = urldecode(trim($_GET['username']));
			if (!$username) {
				exit(0);
			}
			$password = trim($_GET['password']);
			if (!$password) {
				exit(0);
			}
			$confirm_password = trim($_GET['confirm_password']);
			if (!$confirm_password) {
				exit(0);
			}
			if ($password != $confirm_password) {
				exit(0);
			}
			$user = $database->get('user', ['id'], ['username' => $username]);
			if ($user) {
				$data['status'] = -200;
				$data['message'] = '此用户名已存在';
				echoJson($data);
				exit(0);
			}
			$new_user['username'] = $username;
			$new_user['password'] = PasswordHasher::HashPassword($password);
			$new_user['level_id'] = 1;
			$new_user['points'] = 100;
			$new_user['total_points'] = 100;
			$new_user['voucher'] = 3;
			$new_user['total_voucher'] = 3;
			$new_user['created_at'] = date('Y-m-d H:i:s', $now);
			$new_user['updated_at'] = date('Y-m-d H:i:s', $now);
			$database->insert('user', $new_user);
			$user_id = $database->id();

			$data['status'] = 200;
			$encrption_str = '{"id":"'.$user_id.'","username":"'.$username.'"}';
			$data['euid'] = encrypt($encrption_str, $key);
			echoJson($data);
			break;
		case 'update_password':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$password = $_POST['password'];
			if (!$password) {
				exit(0);
			}

			$data['password'] = PasswordHasher::HashPassword($password);
			$database->update('user', $data, ['id' => $user_id]);

			$response['status'] = 200;
			$response['message'] = '密码修改成功';
			echoJson($response);
			break;
		case 'update_userinfo':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$name = $_POST['name'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$weibo = $_POST['weibo'];
			$phone = $_POST['phone'];
			$birth_at = $_POST['birth_at'];

			if ($phone) {
				$exist_userid = $database->get('user', 'id', ['id[!]' => $user_id, 'phone' => $phone]);

				if ($exist_userid) {
					$response['status'] = -202;
					$response['message'] = '修改用户信息失败，此手机号码已存在';
					echoJson($response);
					exit(0);
				}
			}

			$data['name'] = $name;
			$data['email'] = $email;
			$data['address'] = $address;
			$data['weibo'] = $weibo;
			$data['phone'] = $phone;
			$data['birth_at'] = $birth_at;

			$database->update('user', $data, ['id' => $user_id]);

			//first time give 3 vouchers 

			$response['status'] = 200;
			$response['message'] = '用户信息修改成功';
			echoJson($response);
			break;
		case 'forget_password':
			$phone = trim($_POST['phone']);

			if (!$phone) {
				exit(0);
			}

			$captcha = trim($_POST['captcha']);
			if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {
				$data['status'] = -200;
				$data['message'] = '验证码错误，请重新输入';
				echoJson($data);
				exit(0);
			}

			$user_id = $database->get('user', 'id', ['phone' => $phone, 'type' => 'Member']);
			if (!$user_id) {
				$response['status'] = -200;
				$response['message'] = '手机号未绑定，请联系客服';
				echoJson($response);
				exit(0);
			}

			$url            = 'https://api.yisu.com/sms/sendSms';
			$accessId       = 'a7yprid9UfpgJgSC';
			$accessSecret   = '604f065814c694f7d9ef0cc5a5a4f546';
			
			$params = [
				'timestamp' => time(),
				'nonce'     => mt_rand(0, 99999999),
				'accessId'  => $accessId
			];
			$params['phone'] = $phone;
			
			$verification_code = mt_rand(100000, 999999);

			$params['templateCode'] = 200001;
			$params['templateVars'] = json_encode(['code'=>$verification_code], true);
			
			ksort($params);
			$signStr = "";
			foreach ( $params as $key => $value ) {
				$signStr = $signStr . $key . "=" . $value . "&";
			}
			$signStr = substr($signStr, 0, -1);
			$verifySignature = base64_encode(hash_hmac("sha1", $signStr, $accessSecret, true));

			$params['signature'] = $verifySignature;
			$data = http_build_query($params);
			$options = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-Type:application/x-www-form-urlencoded',
					'content' => $data
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, FILE_TEXT, $context);
			$sms_result = json_decode($result, true);

			if($sms_result['code'] == 0){
				// success
				$database->update('forget_password', ['status' => 1], ['user_id' => $user_id]);
				$database->insert('forget_password', ['user_id' => $user_id, 'phone' => $phone, 'verification_code' => $verification_code, 'type' => 'password', 'status' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

				$response['status'] = 200;
				$response['message'] = '验证码已发送到您手机，请输入验证码';
			}
			else {
				$response['status'] = -200;
				$response['message'] = '发送失败，请稍后再试';
			}
			echoJson($response);
			break;
		case 'reset_password':
			$verification_code = trim($_POST['verification_code']);
			if (!$verification_code) {
				exit(0);
			}
			$phone = trim($_POST['phone']);
			if (!$phone) {
				exit(0);
			}
			$password = trim($_POST['password']);
			if (!$password) {
				exit(0);
			}

			$verification_info = $database->get('forget_password', ['id', 'user_id'], ['verification_code' => $verification_code, 'phone' => $phone, 'type' => 'password', 'status' => 0, 'ORDER' =>['created_at' => 'DESC']]);
			if ($verification_info) {
				$data['password'] = PasswordHasher::HashPassword($password);
				$database->update('user', $data, ['id' => $verification_info['user_id']]);
				$database->update('forget_password', ['status' => 1], ['id' => $verification_info['id']]);

				$response['status'] = 200;
				$response['message'] = '重置密码成功，请重新登录';
			}
			else {
				$response['status'] = -200;
				$response['message'] = '验证码错误，请检查后重新输入';
			}
			echoJson($response);
			break;
		case 'forget_account':
			$phone = trim($_POST['phone']);

			if (!$phone) {
				exit(0);
			}

			$captcha = trim($_POST['captcha']);
			if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {
				$data['status'] = -200;
				$data['message'] = '验证码错误，请重新输入';
				echoJson($data);
				exit(0);
			}

			$username = $database->get('user', 'username', ['phone' => $phone, 'type' => 'Member']);
			if (!$username) {
				$response['status'] = -200;
				$response['message'] = '手机号未绑定，请联系客服';
				echoJson($response);
				exit(0);
			}

			$url            = 'https://api.yisu.com/sms/sendSms';
			$accessId       = 'a7yprid9UfpgJgSC';
			$accessSecret   = '604f065814c694f7d9ef0cc5a5a4f546';
			
			$params = [
				'timestamp' => time(),
				'nonce'     => mt_rand(0, 99999999),
				'accessId'  => $accessId
			];
			$params['phone'] = $phone;
			
			$params['templateCode'] = 100002;
			$params['templateVars'] = json_encode(['code'=>$username], true);
			
			ksort($params);
			$signStr = "";
			foreach ( $params as $key => $value ) {
				$signStr = $signStr . $key . "=" . $value . "&";
			}
			$signStr = substr($signStr, 0, -1);
			$verifySignature = base64_encode(hash_hmac("sha1", $signStr, $accessSecret, true));

			$params['signature'] = $verifySignature;
			$data = http_build_query($params);
			$options = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-Type:application/x-www-form-urlencoded',
					'content' => $data
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, FILE_TEXT, $context);
			$sms_result = json_decode($result, true);

			if($sms_result['code'] == 0){
				// success
				$response['status'] = 200;
				$response['message'] = '用戶名已发送到您手机，请重新登入。';
			}
			else {
				$response['status'] = -200;
				$response['message'] = '发送失败，请稍后再试';
			}
			echoJson($response);
			break;
		case 'get_prediction_stats':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$category_id = 0;
			if (isset($_POST['category_id'])) {
				$category_id = intval($_POST['category_id']);
			}
			if (!$category_id) {
				exit(0);
			}

			$league_id = 0;
			if (isset($_POST['league_id'])) {
				$league_id = intval($_POST['league_id']);
			}
			if (!$league_id) {
				exit(0);
			}

			$current_month	= date('n', time());
			$current_year	= date('Y', time());

			$stats = $database->get('prediction_stats', ['prediction_count', 'prediction_total_count', 'win_rate', 'total_win_rate', 'top_ten_count'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $current_month, 'year' => $current_year]);
			if (!$stats) {
				$stats['prediction_count'] = 0;
				$stats['prediction_total_count'] = 0;
				$stats['win_rate'] = 0;
				$stats['total_win_rate'] = 0;
				$stats['top_ten_count'] = 0;
			}

			$user = $database->get('user', ['points', 'voucher'], ['id' => $user_id]);

			$stats['points'] = $user['points'];
			$stats['voucher'] = $user['voucher'];

			echoJson($stats);
			break;
		case 'get_prediction_rate':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$category_id = 0;
			if (isset($_POST['category_id'])) {
				$category_id = intval($_POST['category_id']);
			}
			if (!$category_id) {
				exit(0);
			}

			$league_id = 0;
			if (isset($_POST['league_id'])) {
				$league_id = intval($_POST['league_id']);
			}
			if (!$league_id) {
				exit(0);
			}

			$time = '';
			if (isset($_POST['time'])) {
				$time = $_POST['time'];
			}
			if (!$time) {
				exit(0);
			}

			$time = explode('/', $time);
			$year = intval($time[0]);
			$month = intval($time[1]);

			$data = array();

			$handicap = $database->get('prediction_rate', ['win_count', 'lose_count', 'rate', 'rank'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $month, 'year' => $year, 'type' => 'handicap']);
			$over_under = $database->get('prediction_rate', ['win_count', 'lose_count', 'rate', 'rank'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $month, 'year' => $year, 'type' => 'over_under']);
			$single = $database->get('prediction_rate', ['win_count', 'lose_count', 'rate', 'rank'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $month, 'year' => $year, 'type' => 'single']);
			$total = $database->get('prediction_rate', ['win_count', 'lose_count', 'rate', 'rank'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $month, 'year' => $year, 'type' => 'total']);

			if (!$handicap) {
				$handicap['win_count'] = '-';
				$handicap['lose_count'] = '-';
				$handicap['rate'] = '-';
				$handicap['rank'] = '-';
			}
			if (!$over_under) {
				$over_under['win_count'] = '-';
				$over_under['lose_count'] = '-';
				$over_under['rate'] = '-';
				$over_under['rank'] = '-';
			}
			if (!$single) {
				$single['win_count'] = '-';
				$single['lose_count'] = '-';
				$single['rate'] = '-';
				$single['rank'] = '-';
			}
			if (!$total) {
				$total['win_count'] = '-';
				$total['lose_count'] = '-';
				$total['rate'] = '-';
				$total['rank'] = '-';
			}

			$data['handicap'] = $handicap;
			$data['over_under'] = $over_under;
			$data['single'] = $single;
			$data['total'] = $total;

			echoJson($data);
			break;
		case 'get_prediction_qualification':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$category_id = 0;
			if (isset($_POST['category_id'])) {
				$category_id = intval($_POST['category_id']);
			}
			if (!$category_id) {
				exit(0);
			}

			$league_id = 0;
			if (isset($_POST['league_id'])) {
				$league_id = intval($_POST['league_id']);
			}
			if (!$league_id) {
				exit(0);
			}

			$time = '';
			if (isset($_POST['time'])) {
				$time = $_POST['time'];
			}
			if (!$time) {
				exit(0);
			}

			$time = explode('/', $time);
			$year = intval($time[0]);
			$month = intval($time[1]);

			$data = $database->get('prediction_rate', ['rate', 'season_rate', 'total_count'], ['user_id' => $user_id, 'league_id' => $league_id, 'month' => $month, 'year' => $year, 'type' => 'total']);

			if (!$data) {
				$data['rate'] = '-';
				$data['season_rate'] = '-';
				$data['total_count'] = '-';
			}

			$criteria = $database->get('top_ten_rate', ['min_rate', 'season_min_rate', 'prediction_count'], ['category_id' => $category_id, 'league_id' => $league_id]);
			if (!$criteria) {
				$criteria['min_rate'] = '-';
				$criteria['season_min_rate'] = '-';
				$criteria['prediction_count'] = '-';
			}

			$data['top_ten_rate'] = $criteria['min_rate'];
			$data['top_ten_season_rate']  = $criteria['season_min_rate'];
			$data['top_ten_prediction_count']  = $criteria['prediction_count'];

			echoJson($data);
			break;
		case 'update_user_image':
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			
			$image_data = $_POST['image_data'];
			if(!is_array($image_data) || count($_POST['image_data']) == 0){
				exit(0);
			}
			
			if(!isset($image_data['url']) || !isset($image_data['name'])){
				exit(0);
			}
			
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$data['status'] = -201;
				$data['message'] = '非法用户';
				echoJson($data);
				exit(0);
			}
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];
			   
			$image_data['created_at'] = date("Y-m-d H:i:s");
			$database->insert('upload', $image_data);
			$upload_id = $database->id();
			
			$database->update('user', ['upload_id' => $upload_id], ['id' => $user_id]);
			
			$data['status'] = 200;
			echoJson($data);
			break;
		default:
			break;
	}

?>