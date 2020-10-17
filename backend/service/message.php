<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');

	$action = $_REQUEST['action'];

	switch ($action) {
		case 'get_comments':
			$now = time();
			$limit = 20;
			$page = intval($_POST['page']);
			$parent_id = intval($_POST['parent_id']);
			$chatroom_id = intval($_POST['chatroom_id']);
			$sorting = intval($_POST['sorting']);

			if (!$page) {
				$page = 1;
			}
			if (!$parent_id) {
				$parent_id = 0;
			}
			if (!$chatroom_id) {
				exit(0);
			}
			if (!$sorting) {
				$sorting = 1;
			}

			$begin = (intval($page) - 1) * $limit;

			if ($sorting == 1) {
				$where['created_at[!]'] = null;
				$where['ORDER'] = ['created_at' => 'DESC'];
			}
			else {
				$where['like_count[>=]'] = 0;
				$where['ORDER'] = ['like_count' => 'DESC'];
			}
			$where['chatroom_id'] = $chatroom_id;
			$where['parent_id'] = $parent_id;
			$where['status'] = 'approve';

			$where['LIMIT'] = [$begin, $limit];

			$datas = $database->select('message', ['id', 'user_id', 'chatroom_id', 'message', 'parent_id', 'like_count', 'created_at'], $where);
			foreach ($datas as $key => $value) {
				$user_info = $database->get('user', ['username', 'thumbnail'], ['id' => $value['user_id']]);
				$datas[$key]['username'] = $user_info['username'];
				if (in_array($user_info['username'], $admin_accounts)) {
					$datas[$key]['username'] .= '<img src="'.$img_url.'/assets/images/admin_icon.png" style="width: 50px;">';
				}
				if ($user_info['thumbnail']) {
					$datas[$key]['thumbnail'] = $user_info['thumbnail'];
				}
				else {
					$datas[$key]['thumbnail'] = '/assets/images/default_user_image.png';
				}

				$datas[$key]['created_at'] = maktimes(strtotime($value['created_at']));
				$datas[$key]['sub_comments_count'] = $database->count('message', ['created_at[!]' => null, 'chatroom_id' => $chatroom_id, 'parent_id' => $value['id'], 'status' => 'approve']);
			}
			echoJson($datas);
			break;
			
		case 'get_comments_h5':
			$now = time();
			//$limit = 20;
			//$page = intval($_POST['page']);
			//$parent_id = intval($_POST['parent_id']);
			$chatroom_id = intval($_POST['chatroom_id']);
			$sorting = intval($_POST['sorting']);

//			if (!$page) {
//				$page = 1;
//			}
//			if (!$parent_id) {
//				$parent_id = 0;
//			}
			if (!$chatroom_id) {
				exit(0);
			}
			if (!$sorting) {
				$sorting = 1;
			}

			$begin = (intval($page) - 1) * $limit;

			if ($sorting == 1) {
				$where['created_at[!]'] = null;
				$where['ORDER'] = ['created_at' => 'DESC'];
			}
			else {
				$where['like_count[>=]'] = 0;
				$where['ORDER'] = ['like_count' => 'DESC'];
			}
			$where['chatroom_id'] = $chatroom_id;
			$where['parent_id'] = 0;
			$where['status'] = 'approve';

			//$where['LIMIT'] = [$begin, $limit];

			$datas = $database->select('message', ['id', 'user_id', 'chatroom_id', 'message', 'parent_id', 'like_count', 'created_at'], $where);
			foreach ($datas as $key => $value) {
				$user_info = $database->get('user', ['username', 'thumbnail'], ['id' => $value['user_id']]);
				$datas[$key]['username'] = $user_info['username'];
				if (in_array($user_info['username'], $admin_accounts)) {
					$datas[$key]['adminImg'] = '<img src="'.$img_url.'/assets/images/admin_icon.png" style="width: 50px;">';
				}
				if ($user_info['thumbnail']) {
					$datas[$key]['thumbnail'] = $user_info['thumbnail'];
				}
				else {
					$datas[$key]['thumbnail'] = '/assets/images/default_user_image.png';
				}

				$datas[$key]['created_at'] = maktimes(strtotime($value['created_at']));
				
				$where['parent_id'] = $value['id'];
				$sub_datas = $database->select('message', ['id', 'user_id', 'chatroom_id', 'message', 'parent_id', 'like_count', 'created_at'], $where);
				foreach ($sub_datas as $subkey => $subvalue) {
					$user_info = $database->get('user', ['username', 'thumbnail'], ['id' => $subvalue['user_id']]);
					$sub_datas[$subkey]['username'] = $user_info['username'];
					if (in_array($user_info['username'], $admin_accounts)) {
						$sub_datas[$subkey]['username'] .= '<img src="/assets/images/admin_icon.png" style="width: 50px;">';
					}
					if ($user_info['thumbnail']) {
						$sub_datas[$subkey]['thumbnail'] = $user_info['thumbnail'];
					}
					else {
						$sub_datas[$subkey]['thumbnail'] = '/assets/images/default_user_image.png';
					}

					$sub_datas[$subkey]['created_at'] = maktimes(strtotime($subvalue['created_at']));
				}
				
				$datas[$key]['sub_comments'] = $sub_datas;
			}
			
			$response['list'] = $datas;
			$response['total_records'] = count($datas) > 0 ? count($datas) : 0;
			echoJson($response);
			break;
		case 'thumbup_comments':
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

			$message_id = intval($_POST['message_id']);
			if (!$message_id) {
				exit(0);
			}

			$message_like = $database->get('message_like', ['created_at'], ['user_id' => $user_id, 'message_id' => $message_id]);
			if (!$message_like) {
				$new_data['user_id'] = $user_id;
				$new_data['message_id'] = $message_id;
				$new_data['created_at'] = date('Y-m-d H:i:s');
				$new_data['updated_at'] = date('Y-m-d H:i:s');
				$database->insert('message_like', $new_data);
				$database->update('message', ['like_count[+]' => 1], ['id' => $message_id]);
			}
			else {
				if (time() - strtotime($message_like['created_at']) > 1800) {
					$database->update('message_like', ['updated_at' => date('Y-m-d H:i:s', time())], ['user_id' => $user_id, 'message_id' => $message_id]);
					$database->update('message', ['like_count[+]' => 1], ['id' => $message_id]);
				}
				else {
					$data['status'] = -200;
					$data['message'] = '点赞过于频繁';
					echoJson($data);
					exit(0);
				}
			}

			$data['status'] = 200;
			$data['message'] = '点赞成功';
			$data['like_count'] = $database->get('message', 'like_count', ['id' => $message_id]);
			echoJson($data);
			break;
		case 'add_comments':
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

			$chatroom_id = $_POST['chatroom_id'];
			$message = $_POST['message'];
			$parent_id = $_POST['parent_id'];

			if (!$chatroom_id || !$message) {
				exit(0);
			}
			
			if(!is_numeric($parent_id)){
				exit(0);
			}

			$new_data['user_id'] = $user_id;
			$new_data['chatroom_id'] = $chatroom_id;
			$new_data['message'] = $message;
			$new_data['parent_id'] = $parent_id;
			$new_data['type'] = $database->get('chatroom', 'type', ['id' => $chatroom_id]);

			$new_data['status'] = 'approve';

			$illegal_words = $database->select('illegal_words', ['word', 'regex'], ['disabled' => 0]);
			foreach ($illegal_words as $key => $value) {
				if ($value['regex'] == 1) {
					$exp = "/".$value['word']."/";
					preg_match_all($exp, $message, $matches);

					if (count($matches[0]) > 0) {
						$response['status'] = -200;
						$response['message'] = '留言包含敏感词';
						echoJson($response);
						exit(0);
					}
				}
				else {
					if (strpos(strtolower($message), strtolower($value['word']))) {
						$response['status'] = -200;
						$response['message'] = '留言包含敏感词';
						echoJson($response);
						exit(0);
					}
				}
			}

			$new_data['like_count'] = 0;
			$new_data['created_at'] = date('Y-m-d H:i:s', time());
			$new_data['updated_at'] = date('Y-m-d H:i:s', time());
			$database->insert('message', $new_data);

			$message_id = $database->id();

			if ($type == 'article') {
				$database->update("article", ["comment_count[+]" => 1], ["chatroom_id" => $chatroom_id]);
			}
			else if ($type == 'event') {

			}

			$response['status'] = 200;
			$response['message_id'] = $message_id;
			$response['message'] = '留言成功';
			echoJson($response);
			break;
		case 'delete_comments':
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

			$message_id = intval($_POST['message_id']);
			if (!$message_id) {
				exit(0);
			}

			$id = $database->get('message', 'id', ['id' => $message_id, 'user_id' => $user_id]);
			if ($id) {
				$database->delete('message', ['id' => $id]);
			}
			$response['status'] = 200;
			$response['message'] = '删除留言成功';
			echoJson($response);
			break;

		case 'report_comments':
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

			$message_id = intval($_POST['message_id']);
			if (!$message_id) {
				exit(0);
			}
			$type = $_POST['type'];
			if (!in_array($type, ['ads', 'abusive', 'copyright', 'politics', 'marketingSpam'])) {
				exit(0);
			}

			$id = $database->get('message_report', 'id', ['user_id' => $user_id, 'message_id' => $message_id]);
			if ($id) {
				$response['status'] = -200;
				$response['message'] = '您已经举报过这条信息了';
				echoJson($response);
			}
			else {
				$new_data['user_id'] = $user_id;
				$new_data['message_id'] = $message_id;
				$new_data['report'] = $type;
				$new_data['created_at'] = date('Y-m-d H:i:s', time());
				$database->insert('message_report', $new_data);

				$response['status'] = 200;
				$response['message'] = '举报成功';
				echoJson($response);
			}
			break;
		case 'get_collected_message':
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

			$page = 1;
			if (isset($_POST['page'])) {
				$page = intval($_POST['page']);
			}
			if (!$page) {
				$page = 1;
			}

			$limit = 10;

			$date = '';
			if (isset($_POST['date'])) {
				$date = $_POST['date'];
			}

			$sorting = intval($_POST['sorting']);
			if (!$sorting) {
				$sorting = 2;
			}

			if ($date) {
				$where['created_at[<>]'] = [$date, date("Y-m-d",strtotime("+1 day",strtotime($date)))];
			}
			else {
				if (isset($_POST['year']) && isset($_POST['month'])) {
					$year = $_POST['year'];
					$month = $_POST['month'];
					$begin = $year . '-' . $month . '-01';
					$where['created_at[<>]'] = [$begin, date('Y-m-t',strtotime($begin))];
				}
				else {
					$where['created_at[!]'] = null;
				}
			}
			$where['user_id'] = $user_id;

			$total_count = $database->count('message_like', $where);
			$total_page = ceil($total_count / $limit);

			if ($sorting == 2) {
				$where['ORDER'] = ['created_at' => 'DESC'];
			}
			else {
				$where['ORDER'] = ['created_at' => 'ASC'];
			}

			$begin = (intval($page) - 1) * $limit;

			$where['LIMIT'] = [$begin, $limit];

			$message_ids = $database->select('message_like', ['id', 'message_id', 'created_at'], $where);

			$list = array();

			foreach ($message_ids as $key => $value) {
				$message = $database->get('message', ['message', 'article_id', 'chatroom_id'], ['id' => $value['message_id']]);

				if ($message['chatroom_id']) {
					$message['article_title'] = $database->get('article', 'title', ['chatroom_id' => $message['chatroom_id']]);
				}
				$message['created_at'] = $value['created_at'];
				$message['id'] = $value['id'];
				array_push($list, $message);
			}

			$response['list'] = $list;
			$response['current_page'] = $page;
			$response['total_page'] = $total_page;
			echoJson($response);
			break;
		case 'cancel_colleted_message':
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

			$ids = $_POST['ids'];

			if (!$ids) {
				exit(0);
			}

			$database->delete('message_like', ['id' => explode(',', $ids), 'user_id' => $user_id]);

			$response['status'] = 200;
			$response['message'] = '取消收藏成功';
			echoJson($response);
			break;
		default:
			break;
	}
?>