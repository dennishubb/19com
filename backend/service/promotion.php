<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');

	$action = $_REQUEST['action'];

	switch ($action) {
		case 'promotion_list':
			$datas = array();

			$now = date('Y-m-d H:i:s', time());

			$where['sorting[!]'] = 0;

			$where['start_at[<=]'] = $now;
			$where['end_at[>]'] = $now;

			$where['disabled'] = 0;
			$where['system'] = 0;
			$where['ORDER'] = ['sorting' => 'ASC', 'created_at' => 'DESC'];

			$datas = $database->select('promotion', ['id', 'name', 'introduction', 'display_method', 'url', 'upload_id_big', 'upload_id_small'], $where);

			foreach ($datas as $key => $value) {
				if ($value['upload_id_big'] > 0) {
					$file_path = '/' . $database->get('upload', 'url', ['id' => $value['upload_id_big']]);
					if (is_file($root_folder.$file_path)) {
						$datas[$key]['thumbnail_big'] = $file_path;
					}
					else {
						$datas[$key]['thumbnail_big'] = '/assets/images/grey.gif';
					}
				}
				else {
					$datas[$key]['thumbnail_big'] = '/assets/images/grey.gif';
				}

				if ($value['upload_id_small'] > 0) {
					$file_path = '/' . $database->get('upload', 'url', ['id' => $value['upload_id_small']]);
					if (is_file($root_folder.$file_path)) {
						$datas[$key]['thumbnail_small'] = $file_path;
					}
					else {
						$datas[$key]['thumbnail_small'] = '/assets/images/grey.gif';
					}
				}
				else {
					$datas[$key]['thumbnail_small'] = '/assets/images/grey.gif';
				}

				$datas[$key]['introduction'] = mb_substr($value['introduction'], 0, 40, 'utf-8');

				unset($datas[$key]['upload_id_big']);
				unset($datas[$key]['upload_id_small']);
			}

			echoJson($datas);
			break;
		case 'get_promotion':
			$id = intval($_GET['id']);
			if (!$id) {
				exit(0);
			}

			$datas = $database->get('promotion', ['name', 'introduction', 'sign_up'], ['id' => $id]);

			$temp_data = htmlentities(trim($datas['introduction']), ENT_QUOTES, "utf-8");
			$temp_data = nl2br($temp_data);
			$datas['introduction'] = str_replace("","/n",$temp_data);

			echoJson($datas);
			break;

		case 'redeem_promotion':
			$response = array();
			$promotion_id = 0;
			if (isset($_POST['promotion_id'])) {
				$promotion_id = intval($_POST['promotion_id']);
			}
			if (!$promotion_id) {
				exit(0);
			}
			$euid = $_POST['euid'];
			if (!$euid) {
				exit(0);
			}
			$euid = decrypt($euid, $key);
			if (is_null(json_decode($euid))) {
				$response['status'] = -201;
				$response['message'] = '非法用户';
				echoJson($response);
				exit(0);
			}
			$now = time();

			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$user = $database->get('user', ['level_id'], ['id' => $user_id]);

			$user_level = $user['level_id'];

			$promotion = $database->get('promotion', ['start_at', 'end_at', 'limitation', 'limitation_count', 'level_id'], ['id' => $promotion_id]);

			$start_at = strtotime($promotion['start_at']);
			$end_at = strtotime($promotion['end_at']);

			if ($start_at > $now || $end_at < $now) {
				$response['status'] = -202;
				$response['message'] = '此活动已过期';
				echoJson($response);
				exit(0);
			}

			$promo_level = json_decode($promotion['level_id']);
			if (!in_array($user_level, $promo_level)) {
				$response['status'] = -203;
				$response['message'] = '您尚未达到此活动的等级要求';
				echoJson($response);
				exit(0);
			}

			if ($promotion['limitation'] == 'once') {
				$count = $database->count('promotion_redeem', ['user_id' => $user_id, 'promotion_id' => $promotion_id]);
				if ($count > 0) {
					$response['status'] = -204;
					$response['message'] = '您已经参与了此活动，不可重复参与';
					echoJson($response);
					exit(0);
				}
			}
			else if ($promotion['limitation'] == 'daily') {
				$day_start = date('Y-m-d', time());
				$day_end = date("Y-m-d",strtotime("+1 day"));
				$count = $database->count('promotion_redeem', ['user_id' => $user_id, 'promotion_id' => $promotion_id, 'created_at[<>]' => [$day_start, $day_end]]);
				if ($count > 0) {
					$response['status'] = -204;
					$response['message'] = '您今天已经参与了此活动，不可重复参与';
					echoJson($response);
					exit(0);
				}
			}
			else if ($promotion['limitation'] == 'monthly') {
				$month_start = date('Y-m-01', time());
				$month_end = date('Y-m-d', strtotime("$month_start +1 month"));
				$count = $database->count('promotion_redeem', ['user_id' => $user_id, 'promotion_id' => $promotion_id, 'created_at[<>]' => [$month_start, $month_end]]);
				if ($count > 0) {
					$response['status'] = -204;
					$response['message'] = '您本月已经参与了此活动，不可重复参与';
					echoJson($response);
					exit(0);
				}
			}

			$data = array();
			$data['promotion_id'] = $promotion_id;
			$data['user_id'] = $user_id;
			$data['status'] = 'pending';
			$data['admin_id'] = 0;
			$data['created_at'] = date('Y-m-d H:i:s', time());
			$database->insert('promotion_redeem', $data);

			$response['status'] = 200;
			$response['message'] = '活动已申请成功，请耐心等待审核';
			echoJson($response);
			break;
		default:
			break;
	}

?>