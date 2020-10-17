<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');

	$action = $_REQUEST['action'];

	switch ($action) {
		case 'get_gift_list':
			$limit = 21;
			$page = 1;
			if (isset($_GET['page'])) {
				$page = intval($_GET['page']);
			}

			$sorting = 1;
			if (isset($_GET['sorting'])) {
				$sorting = intval($_GET['sorting']);
			}

			if (!in_array($sorting, [1,2])) {
				$sorting = 1;
			}

			$points_from = 0;
			$points_to = 0;

			if (isset($_GET['points_from'])) {
				$points_from = intval($_GET['points_from']);
			}
			if (isset($_GET['points_to'])) {
				$points_to = intval($_GET['points_to']);
			}

			$category_id = 0;
			$sub_category_id = 0;

			if (isset($_GET['category_id'])) {
				$category_id = intval($_GET['category_id']);
			}
			if (isset($_GET['sub_category_id'])) {
				$sub_category_id = intval($_GET['sub_category_id']);
			}

			$hot_tag = '';
			if (isset($_GET['hot_tag'])) {
				$hot_tag = urldecode(trim($_GET['hot_tag']));
			}

			$now = date('Y-m-d H:i:s', time());

			if (!$hot_tag) {
				if ($points_from == 0 && $points_to == 0) {
					$where['points[>=]'] = 0;
				}
				else {
					if ($points_from > 0 && $points_to > 0) {
						$where['points[<>]'] = [$points_from, $points_to];
					}
					else if ($points_from > 0 && $points_to == 0) {
						$where['points[>=]'] = $points_from;
					}
					else if ($points_from == 0 && $points_to > 0) {
						$where['points[<=]'] = $points_to;
					}
				}
			}

			if ($hot_tag) {
				$where['category_id[>]'] = 0;
				$sub_category_id = $database->select('category', 'id', ['display' => $hot_tag, 'type' => ['gift', 'gift-hot'], 'disabled' => 0]);
				$where['sub_category_id'] = $sub_category_id;
			}
			
			$where['end_at[>]'] = $now;
			$where['start_at[<=]'] = $now;

			if (!$hot_tag) {
				if ($category_id) {
					$where['category_id'] = $category_id;
				}

				if ($sub_category_id) {
					$where['sub_category_id'] = $sub_category_id;
				}
			}

			$where['disabled'] = 0;
			if ($sorting == 1) {
				$where['ORDER'] = ['points' => 'ASC'];
			}
			else if ($sorting == 2) {
				$where['ORDER'] = ['points' => 'DESC'];
			}
			
			$begin = (intval($page) - 1) * $limit;

			$where['LIMIT'] = [$begin, $limit];
			$data = $database->select('gift', ['id', 'name', 'upload_id', 'points'], $where);
			// var_dump($database->last());
			foreach ($data as $key => $value) {
				$file_path = '/' . $database->get('upload', 'url', ['id' => $value['upload_id']]);
				if (is_file($root_folder.$file_path)) {
					$data[$key]['thumbnail'] = $file_path;
				}
				else {
					$data[$key]['thumbnail'] = '/assets/images/grey.gif';
				}
				unset($data[$key]['upload_id']);
			}
			$response = array();
			$response['list'] = $data;
			$response['page'] = $page;
			echoJson($response);
			break;
		case 'get_gift_redeem':
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

			$limit = 10;
			$page = 1;
			if (isset($_POST['page'])) {
				$page = intval($_POST['page']);
			}

			$begin = (intval($page) - 1) * $limit;

			$where['user_id'] = $user_id;
			$where['LIMIT'] = [$begin, $limit];
			$where['ORDER'] = ['created_at' => 'DESC'];

			// $user = $database->get('user', ['id'], ['id' => $user_id, 'username' => $user_name]);
			// if (!$user) {
			// 	$data['status'] = -201;
			// 	$data['message'] = '非法用户';
			// 	echoJson($data);
			// 	exit(0);
			// }

			$list = $database->select('gift_redeem', ['created_at', 'status', 'quantity', 'remark', 'gift_id', 'size', 'color'], $where);
			foreach ($list as $key => $value) {
				$list[$key]['user_name'] = $user_name;
				$list[$key]['gift_name'] = $database->get('gift', 'name', ['id' => $value['gift_id']]);
				if ($value['status'] == 'approve') {
					$list[$key]['status'] = '已批准';
				}
				else if ($value['status'] == 'reject') {
					$list[$key]['status'] = '拒绝';
				}
				else {
					$list[$key]['status'] = '待审核';
				}
			}

			unset($where['LIMIT']);
			unset($where['ORDER']);

			$totalcount = $database->count('gift_redeem', $where);

			$data = array();
			$data['status'] = 200;
			$data['list'] = $list;
			$data['current_page'] = $page;
			$data['total_page'] = ceil($totalcount / $limit);
			echoJson($data);
			break;
		case 'get_gift_redeem_h5':
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

			$limit = 10;
			$page = 1;
			if (isset($_POST['page'])) {
				$page = intval($_POST['page']);
			}

			$begin = (intval($page) - 1) * $limit;
			
			if (isset($_POST['year']) && isset($_POST['month'])) {
				$year = $_POST['year'];
				$month = $_POST['month'];
				$start = $year . '-' . $month . '-01';
				$where['created_at[<>]'] = [$start, date('Y-m-t 23:59:59',strtotime($start))];
			}else if (isset($_POST['year']) && !isset($_POST['month'])) {
				$year = $_POST['year'];
				$start = $year . '-01-01';
				$end = $year . '-12-01';
				$where['created_at[<>]'] = [$start, date('Y-m-t 23:59:59',strtotime($end))];
			}
			else {
				$where['created_at[!]'] = null;
			}

			$where['user_id'] = $user_id;
			$where['LIMIT'] = [$begin, $limit];
			$where['ORDER'] = ['created_at' => 'DESC'];

			$list = $database->select('gift_redeem', ['created_at', 'status', 'quantity', 'remark', 'gift_id', 'size', 'color'], $where);
			foreach ($list as $key => $value) {
				$list[$key]['user_name'] = $user_name;
				$gift_data = $database->get('gift', ['name', 'points', 'upload_id'], ['id' => $value['gift_id']]);
				$list[$key]['gift_name'] = $gift_data['name'];
				$list[$key]['points'] = $gift_data['points'];
				
				$file_path = '/' . $database->get('upload', 'url', ['id' => $gift_data['upload_id']]);
				if (is_file($root_folder.$file_path)) {
					$list[$key]['thumbnail'] = $file_path;
				}
				else {
					$list[$key]['thumbnail'] = '/assets/images/grey.gif';
				}
				
				if ($value['status'] == 'approve') {
					$list[$key]['status'] = '已批准';
				}
				else if ($value['status'] == 'reject') {
					$list[$key]['status'] = '拒绝';
				}
				else {
					$list[$key]['status'] = '待审核';
				}
			}

			unset($where['LIMIT']);
			unset($where['ORDER']);

			$totalcount = $database->count('gift_redeem', $where);

			$data = array();
			$data['status'] = 200;
			$data['list'] = $list;
			$data['current_page'] = $page;
			$data['total_page'] = ceil($totalcount / $limit);
			echoJson($data);
			break;
		case 'get_gift':
			$id = intval($_GET['id']);
			if (!$id) {
				exit(0);
			}

			$exp = "/\"(.*?)\"/";

			$gift = $database->get('gift', ['name', 'size', 'color', 'points'], ['id' => $id, 'disabled' => 0]);

			preg_match_all($exp, $gift['size'], $matches1);
			$gift['size'] = $matches1[1];
			$gift['color'] = str_replace("\"\"", "", $gift['color']);
			preg_match_all($exp, $gift['color'], $matches2);
			$gift['color'] = $matches2[1];

			echoJson($gift);
			break;
		case 'get_gift_h5':
			$id = intval($_GET['id']);
			if (!$id) {
				exit(0);
			}

			$exp = "/\"(.*?)\"/";

			$gift = $database->get('gift', ['name', 'size', 'color', 'points', 'upload_id'], ['id' => $id, 'disabled' => 0]);

			preg_match_all($exp, $gift['size'], $matches1);
			$gift['size'] = $matches1[1];
			$gift['color'] = str_replace("\"\"", "", $gift['color']);
			preg_match_all($exp, $gift['color'], $matches2);
			$gift['color'] = $matches2[1];
			
			$file_path = '/' . $database->get('upload', 'url', ['id' => $gift['upload_id']]);
			if (is_file($root_folder.$file_path)) {
				$gift['thumbnail'] = $file_path;
			}
			else {
				$gift['thumbnail'] = '/assets/images/grey.gif';
			}

			echoJson($gift);
			break;
		case 'gift_redeem':
			$response = array();
			$gift_id = 0;
			if (isset($_POST['gift_id'])) {
				$gift_id = intval($_POST['gift_id']);
			}
			if (!$gift_id) {
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
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$user = $database->get('user', ['name', 'address', 'phone', 'points'], ['id' => $user_id]);
			$name = trim($user['name']);
			$address = trim($user['address']);
			$phone = trim($user['phone']);
			$user_points = trim($user['points']);

			if ($name == '' || $address == '' || $phone == '') {
				$response['status'] = -203;
				$response['message'] = '请先完善个人信息（姓名、地址、手机号吗）';
				echoJson($response);
				exit(0);
			}

			$size = "";
			if (isset($_POST['size'])) {
				if ($_POST['size'] != "0") {
					$size = urldecode($_POST['size']);
				}
			}
			$color = "";
			if (isset($_POST['color'])) {
				if ($_POST['color'] != "0") {
					$color = urldecode($_POST['color']);
				}
			}
			$quantity = intval($_POST['quantity']);
			if (!$quantity) {
				exit(0);
			}
			if ($quantity <= 0) {
				exit(0);
			}

			$gift_points = $database->get('gift', 'points', ['id' => $gift_id]);
			$gift_points = $gift_points * $quantity;

			if ($user_points < $gift_points) {
				$response['status'] = -204;
				$response['message'] = '您的积分不足，下单失败';
				echoJson($response);
				exit(0);
			}

			$data['gift_id'] = $gift_id;
			$data['user_id'] = $user_id;
			$data['name'] = $name;
			$data['phone'] = $phone;
			$data['address'] = $address;
			$data['quantity'] = $quantity;
			$data['size'] = $size;
			$data['color'] = $color;
			$data['status'] = 'pending';
			$data['created_at'] = date('Y-m-d H:i:s', time());
			$data['updated_at'] = date('Y-m-d H:i:s', time());
			$database->insert('gift_redeem', $data);
			$database->update("user", ["points[-]" => $gift_points], ["id" => $user_id]);

			$response['status'] = 200;
			$response['item_points'] = $gift_points;
			$response['message'] = '下单成功，请耐心等待配送';
			echoJson($response);
			break;
		case 'gift_redeem_h5':
			//multiple gift redeem in a single API
			$response = array();
			
			$gift_redeem = array();
			if (isset($_POST['gift_redeem'])) {
				$gift_redeem = $_POST['gift_redeem'];
			}
			if(count($gift_redeem) == 0){
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
			$user_json = json_decode($euid, true);
			$user_id = $user_json['id'];

			$user = $database->get('user', ['name', 'address', 'phone', 'points'], ['id' => $user_id]);
			$name = trim($user['name']);
			$address = trim($user['address']);
			$phone = trim($user['phone']);
			$user_points = trim($user['points']);

			if ($name == '' || $address == '' || $phone == '') {
				$response['status'] = -203;
				$response['message'] = '请先完善个人信息（姓名、地址、手机号吗）';
				echoJson($response);
				exit(0);
			}
			
			$total_gift_points = 0;
			$gift_points_array = array();
			foreach($gift_redeem as $gift_redeem_data){
				
				$gift_id = $gift_redeem_data['id'];
				
				$quantity = intval($gift_redeem_data['quantity']);
				if (!$quantity) {
					continue;
				}
				if ($quantity <= 0) {
					continue;
				}

				$gift_points = $database->get('gift', 'points', ['id' => $gift_id]);
				$gift_points = $gift_points * $quantity;

				$total_gift_points += $gift_points;
				$gift_points_array[$gift_id] = $gift_points;
			}
			
			if ($user_points < $total_gift_points) {
				$response['status'] = -204;
				$response['message'] = '您的积分不足，下单失败';
				echoJson($response);
				exit(0);
			}
			
			foreach($gift_redeem as $gift_redeem_data){
				
				$gift_id = $gift_redeem_data['id'];
				
				$size = "";
				if (isset($gift_redeem_data['size'])) {
					if ($gift_redeem_data['size'] != "0") {
						$size = urldecode($gift_redeem_data['size']);
					}
				}
				$color = "";
				if (isset($gift_redeem_data['color'])) {
					if ($gift_redeem_data['color'] != "0") {
						$color = urldecode($gift_redeem_data['color']);
					}
				}
				
				$quantity = intval($gift_redeem_data['quantity']);
				if (!$quantity) {
					continue;
				}
				if ($quantity <= 0) {
					continue;
				}

				$gift_points = $gift_points_array[$gift_id];

				$data['gift_id'] = $gift_id;
				$data['user_id'] = $user_id;
				$data['name'] = $name;
				$data['phone'] = $phone;
				$data['address'] = $address;
				$data['quantity'] = $quantity;
				$data['size'] = $size;
				$data['color'] = $color;
				$data['status'] = 'pending';
				$data['created_at'] = date('Y-m-d H:i:s', time());
				$data['updated_at'] = date('Y-m-d H:i:s', time());
				$database->insert('gift_redeem', $data);
			}
			
			$database->update("user", ["points[-]" => $total_gift_points], ["id" => $user_id]);
			
			$response['status'] = 200;
			$response['item_points'] = $total_gift_points;
			$response['message'] = '下单成功，请耐心等待配送';
			echoJson($response);
			break;
		default:
			break;
	}

?>