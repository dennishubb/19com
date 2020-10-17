<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');

	$action = $_REQUEST['action'];

	switch ($action) {
		case 'match_prediction_carousel':
			$datas = $database->select('event', ['id', 'league_id', 'home_team_id', 'away_team_id', 'home_team_upload_id', 'away_team_upload_id', 'match_at', 'prediction_end_at', 'round'], ['disabled' => '0', 'ended' => '0','ORDER' => ['match_at' => 'ASC']]);

			foreach ($datas as $key => $value) {
				$datas[$key]['home_team_image'] = '/' . $database->get('upload', 'url', ['id' => $value['home_team_upload_id']]);
				$datas[$key]['away_team_image'] = '/' . $database->get('upload', 'url', ['id' => $value['away_team_upload_id']]);
				$datas[$key]['league_name'] = $database->get('league', 'name_zh', ['id' => $value['league_id']]);
				$datas[$key]['home_team_name'] = $database->get('team', 'name_zh', ['id' => $value['home_team_id']]);
				$datas[$key]['away_team_name'] = $database->get('team', 'name_zh', ['id' => $value['away_team_id']]);
			}

			echoJson($datas);
			break;
		case 'get_prediction_info':
			$event_id = intval($_GET['event_id']);
			if (!$event_id) {
				exit(0);
			}
			$prediction = $database->get('event', ['id', 'league_id', 'home_team_id', 'away_team_id', 'home_team_upload_id', 'away_team_upload_id', 'match_at', 'prediction_end_at', 'round', 'editor_note', 'handicap_home_bet', 'handicap_home_odds', 'handicap_away_bet', 'handicap_away_odds', 'over_under_home_bet', 'over_under_home_odds', 'over_under_away_bet', 'over_under_away_odds', 'single_home', 'single_tie', 'single_away', 'chatroom_id', 'category_id'], ['id' => $event_id, 'disabled' => '0']);

			$prediction['home_team_image'] = '/' . $database->get('upload', 'url', ['id' => $prediction['home_team_upload_id']]);
			$prediction['away_team_image'] = '/' . $database->get('upload', 'url', ['id' => $prediction['away_team_upload_id']]);
			$prediction['league_name'] = $database->get('league', 'name_zh', ['id' => $prediction['league_id']]);
			$prediction['home_team_name'] = $database->get('team', 'name_zh', ['id' => $prediction['home_team_id']]);
			$prediction['away_team_name'] = $database->get('team', 'name_zh', ['id' => $prediction['away_team_id']]);

	      	$exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";
			preg_match_all($exp, $prediction['editor_note'], $matches);

			if (count($matches[0]) > 0) {
			    for ($i=0; $i < count($matches[0]); $i++) { 
			        $prediction['editor_note'] = str_replace($matches[0][$i], '<video width="640" height="360" controls><source src="'.$matches[4][$i].'" type="video/mp4">Your browser does not support the video tag.</video>', $prediction['editor_note']);
			    }
			}

			echoJson($prediction);
			break;
		case 'get_my_option':
			$event_id = intval($_POST['event_id']);
			$euid = $_POST['euid'];
			if (!$event_id) {
				exit(0);
			}
			if (!$euid) {
				echoJson(array());
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

			$data = $database->get('prediction', ['handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'status', 'ended'], ['user_id' => $user_id, 'event_id' => $event_id]);
			if ($data['status'] == '' && $data['ended'] == 1) {
				$data = [];
			}
			echoJson($data);
			break;
		case 'get_predictor_option':
			$event_id = intval($_POST['event_id']);
			$predictor_id = $_POST['predictor_id'];
			if (!$event_id) {
				exit(0);
			}
			if (!$predictor_id) {
				exit(0);
			}
			$euid = $_POST['euid'];
			if (!$euid) {
				// echoJson(array());
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

			$top_ten_id = $database->get('prediction_top_ten', 'id', ['user_id' => $predictor_id, 'event_id' => $event_id]);

			$top_ten_unlock = $database->get('prediction_top_ten_unlock', 'id', ['user_id' => $user_id, 'event_id' => $event_id, 'prediction_top_ten_id' => $top_ten_id]);
			if (!$top_ten_unlock) {
				$response['status'] = -202;
				$response['message'] = '您尚未解锁来自这位神级预言家对于这场比赛的预测';
				echoJson($response);
				exit(0);
			}

			$event_info = $database->get('event', ['league_id', 'home_team_id', 'away_team_id', 'round', 'category_id', 'handicap_home_bet', 'handicap_home_odds', 'handicap_away_bet', 'handicap_away_odds', 'over_under_home_bet', 'over_under_home_odds', 'over_under_away_bet', 'over_under_away_odds', 'single_home', 'single_tie', 'single_away'], ['id' => $event_id]);
			$league_name = $database->get('league', 'name_zh', ['id' => $event_info['league_id']]);
			$home_team_name = $database->get('team', 'name_zh', ['id' => $event_info['home_team_id']]);
			$away_team_name = $database->get('team', 'name_zh', ['id' => $event_info['away_team_id']]);

			$data = $database->get('prediction', ['user_id', 'handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie'], ['user_id' => $predictor_id, 'event_id' => $event_id]);

			$data['username'] = $database->get('user', 'username', ['id' => $predictor_id]);
			$data['league_name'] = $league_name;
			$data['home_team_name'] = $home_team_name;
			$data['away_team_name'] = $away_team_name;
			$data['category_id'] = $event_info['category_id'];

			$data['handicap_home_bet'] = $event_info['handicap_home_bet'];
			$data['handicap_home_odds'] = $event_info['handicap_home_odds'];
			$data['handicap_away_bet'] = $event_info['handicap_away_bet'];
			$data['handicap_away_odds'] = $event_info['handicap_away_odds'];
			$data['over_under_home_bet'] = $event_info['over_under_home_bet'];
			$data['over_under_home_odds'] = $event_info['over_under_home_odds'];
			$data['single_home_value'] = $event_info['single_home'];
			$data['single_tie_value'] = $event_info['single_tie'];
			$data['single_away_value'] = $event_info['single_away'];
			$data['over_under_away_bet'] = $event_info['over_under_away_bet'];
			$data['over_under_away_odds'] = $event_info['over_under_away_odds'];
			$data['event_id'] = $event_id;
			$data['round'] = $event_info['round'];

			$response['status'] = 200;
			$response['data'] = $data;
			echoJson($response);
			break;
		case 'get_cart_list':
			$euid = $_POST['euid'];
			if (!$euid) {
				// echoJson(array());
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

			$data = $database->select('prediction', ['id', 'event_id', 'handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie'], ['user_id' => $user_id, 'status' => '', 'ended' => 0, 'ORDER' => ['id' => 'DESC']]);
			// var_dump($database->last());

			foreach ($data as $key => $value) {
				$prediction = $database->get('event', ['league_id', 'home_team_id', 'away_team_id', 'match_at', 'handicap_home_bet', 'handicap_away_bet', 'over_under_home_bet', 'over_under_away_bet'], ['id' => $value['event_id']]);
				$data[$key]['league_name'] = $database->get('league', 'name_zh', ['id' => $prediction['league_id']]);
				$data[$key]['home_team_name'] = $database->get('team', 'name_zh', ['id' => $prediction['home_team_id']]);
				$data[$key]['away_team_name'] = $database->get('team', 'name_zh', ['id' => $prediction['away_team_id']]);
				$data[$key]['match_at'] = $prediction['match_at'];
				$data[$key]['handicap_home_value'] = $prediction['handicap_home_bet'];
				$data[$key]['handicap_away_value'] = $prediction['handicap_away_bet'];
				$data[$key]['over_under_home_value'] = $prediction['over_under_home_bet'];
				$data[$key]['over_under_away_value'] = $prediction['over_under_away_bet'];
				// $data[$key]['single_home_value'] = $prediction['single_home'];
				// $data[$key]['single_away_value'] = $prediction['single_tie'];
				// $data[$key]['single_tie_value'] = $prediction['single_away'];
			}
			echoJson($data);
			break;
		case 'proceed_prediction';
			$now = time();
			$event_id = intval($_POST['event_id']);
			$euid = $_POST['euid'];
			if (!$event_id) {
				exit(0);
			}
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

			$event_info = $database->get('event', ['prediction_end_at', 'ended'], ['id' => $event_id]);
			if ($now > strtotime($event_info['prediction_end_at'])) {
				$data['status'] = -202;
				$data['message'] = '最后预测期限为赛前1分钟，请耐心等候赛果哟！';
				echoJson($data);
				exit(0);
			}

			if ($event_info['ended'] == 1) {
				$data['status'] = -202;
				$data['message'] = '此比赛已结束，无法预测';
				echoJson($data);
				exit(0);
			}

			$handicap_home = intval($_POST['handicap_home']);
			$handicap_away = intval($_POST['handicap_away']);
			$over_under_home = intval($_POST['over_under_home']);
			$over_under_away = intval($_POST['over_under_away']);
			$single_home = intval($_POST['single_home']);
			$single_away = intval($_POST['single_away']);
			$single_tie = intval($_POST['single_tie']);

			$new_data['event_id'] = $event_id;
			$new_data['user_id'] = $user_id;
			$new_data['handicap_home'] = $handicap_home;
			$new_data['handicap_away'] = $handicap_away;
			$new_data['over_under_home'] = $over_under_home;
			$new_data['over_under_away'] = $over_under_away;
			$new_data['single_home'] = $single_home;
			$new_data['single_away'] = $single_away;
			$new_data['single_tie'] = $single_tie;
			$new_data['status'] = '';
			$new_data['updated_at'] = date('Y-m-d H:i:s', $now);

			$prediction_id = $database->get('prediction', 'id', ['user_id' => $user_id, 'event_id' => $event_id]);

			if ($prediction_id) {
				$database->update('prediction', $new_data, ['id' => $prediction_id]);
			}
			else {
				$new_data['created_at'] = date('Y-m-d H:i:s', $now);
				$database->insert('prediction', $new_data);
			}

			$data['status'] = 200;
			$data['message'] = '已加入预测选单！\r\n记得点选右上角 “查看” 按钮，并确认送出选项哟～';
			echoJson($data);
			break;
		case 'get_top_five';
			$event_id = intval($_POST['event_id']);
			if (!$event_id) {
				exit(0);
			}
			$euid = $_POST['euid'];
			$user_id = 0;
			if ($euid) {
				$euid = decrypt($euid, $key);
				if (is_null(json_decode($euid))) {
					$data['status'] = -201;
					$data['message'] = '非法用户';
					echoJson($data);
					exit(0);
				}
				$user_json = json_decode($euid, true);
				$user_id = $user_json['id'];
			}

			$limit = 5;
			$data = $database->select('prediction_top_ten', ['id', 'user_id', 'event_id'], ['event_id' => $event_id]);
			foreach ($data as $key => $value) {
				$user = $database->get('user', ['username', 'thumbnail'], ['id' => $value['user_id']]);
				$data[$key]['image'] = '/assets/images/default_user_image.png';
				if ($user['thumbnail']) {
					$data[$key]['image'] = $user['thumbnail'];
				}
				$data[$key]['username'] = $user['username'];
				if ($user_id > 0) {
					$data[$key]['unlocked'] = $database->count('prediction_top_ten_unlock', ['user_id' => $user_id, 'event_id' => $event_id, 'prediction_top_ten_id' => $value['id']]);
				}
				else {
					$data[$key]['unlocked'] = 0;
				}
			}
			echoJson($data);
			break;
		case 'cancel_prediction':
			$now = time();
			$event_id = intval($_POST['event_id']);
			$euid = $_POST['euid'];
			if (!$event_id) {
				exit(0);
			}
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

			$event_info = $database->get('event', ['prediction_end_at', 'ended'], ['id' => $event_id]);
			if ($now > strtotime($event_info['prediction_end_at'])) {
				$data['status'] = -202;
				$data['message'] = '最后预测期限已过，无法取消';
				echoJson($data);
				exit(0);
			}

			if ($event_info['ended'] == 1) {
				$data['status'] = -202;
				$data['message'] = '此比赛已结束，无法取消';
				echoJson($data);
				exit(0);
			}
			$database->delete('prediction', ['user_id' => $user_id, 'event_id' => $event_id]);
			$database->delete('prediction_top_ten', ['user_id' => $user_id, 'event_id' => $event_id]);

			$data['status'] = 200;
			echoJson($data);
			break;
		case 'confirm_prediction';
			$last_month	= date('n', strtotime('last month'));
			$last_year	= date('Y', strtotime('last month'));
			$now = time();
			$euid = $_POST['euid'];
			if (!$euid) {
				// echoJson(array());
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

			$success = 0;
			$failure = 0;

			$data = $database->select('prediction', ['id', 'event_id'], ['user_id' => $user_id, 'status' => '', 'ended' => 0, 'ORDER' => ['id' => 'DESC']]);
			foreach ($data as $key => $value) {
				$event_id = intval($value['event_id']);
				$event_info = $database->get('event', ['prediction_end_at', 'league_id'], ['id' => $event_id]);
				if ($now > strtotime($event_info['prediction_end_at'])) { 
					$database->update('prediction', ['ended' => 1], ['id' => $value['id']]);
					$failure++;
				}
				else {
					$database->update('prediction', ['status' => 'predicted'], ['id' => $value['id']]);

					$top_ten_id = $database->get('top_ten', 'id', ['user_id' => $user_id, 'league_id' => $event_info['league_id'], 'month' => $last_month, 'year' => $last_year]);

					if ($top_ten_id) {
						$ptt_info = $database->get('prediction_top_ten', 'id', ['user_id' => $user_id, 'event_id' => $event_id]);
						if (!$ptt_info) {
							$database->insert('prediction_top_ten', ['user_id' => $user_id, 'event_id' => $event_id, 'created_at' => date('Y-m-d H:i:s', $now)]);
						}
					}
					
					$success++;
				}
			}

			//calcualte prediction_stats

			$league_list = $database->select('league', ['id', 'category_id'], ['has_event' => 1]);

			$start_time = date('Y-m-01', $now);
			$end_time = date('Y-m-d', strtotime("+1 month", strtotime($start_time)));

			foreach ($league_list as $league_key => $league_value) {
				$prediction_total_count = $database->count('event', ['match_at[<>]' => [$start_time, $end_time], 'category_id' => $league_value['category_id'], 'league_id' => $league_value['id']]);
				if (in_array($league_value['category_id'], [1, 2])) {
					$prediction_total_count = $prediction_total_count*3;
				}

				if ($prediction_total_count > 0) {
					$prediction_count = 0;
					$prediction_list = $database->select('prediction', ['handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_tie', 'single_away'], ['user_id' => $user_id, 'category_id' => $league_value['category_id'], 'league_id' => $league_value['id'], 'status' => 'predicted']);

					foreach ($prediction_list as $prediction_key => $prediction_value) {
						if ($prediction_value['handicap_home'] > 0 || $prediction_value['handicap_away'] > 0) {
							$prediction_count++;
						}
						if ($prediction_value['over_under_home'] > 0 || $prediction_value['over_under_away'] > 0) {
							$prediction_count++;
						}
						if ($prediction_value['single_home'] > 0 || $prediction_value['single_tie'] > 0 || $prediction_value['single_away'] > 0) {
							$prediction_count++;
						}
					}

					$stats_id = $database->get('prediction_stats', 'id', ['user_id' => $user_id, 'category_id' => $league_value['category_id'], 'league_id' => $league_value['id'], 'month' => date('n', $now), 'year' => date('Y', $now)]);

					$stats_data['user_id'] = $user_id;
					$stats_data['category_id'] = $league_value['category_id'];
					$stats_data['league_id'] = $league_value['id'];
					$stats_data['prediction_count'] = $prediction_count;
					$stats_data['prediction_total_count'] = $prediction_total_count;
					$stats_data['month'] = date('n', $now);
					$stats_data['year'] = date('Y', $now);
					$stats_data['updated_at'] = date('Y-m-d H:i:s', $now);

					if ($stats_id) {
						$database->update('prediction_stats', $stats_data, ['id' => $stats_id]);
					}
					else {
						$stats_data['created_at'] = date('Y-m-d H:i:s', $now);
						$database->insert('prediction_stats', $stats_data);
					}
				}
			}

			$response['status'] = 200;
			$response['message'] = '已成功送出'.$success.'笔预测！' . ($failure > 0 ? '失败'.$failure.'笔（原因：超过最后预测期限）' : '');
			echoJson($response);
			break;
		case 'unlock_predictor';
			$event_id = $_POST['event_id'];
			if (!$event_id) {
				exit(0);
			}
			$top_ten_id = $_POST['top_ten_id'];
			if (!$top_ten_id) {
				exit(0);
			}
			$euid = $_POST['euid'];
			if (!$euid) {
				// echoJson(array());
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

			$top_ten_unlock = $database->get('prediction_top_ten_unlock', 'id', ['user_id' => $user_id, 'event_id' => $event_id, 'prediction_top_ten_id' => $top_ten_id]);
			if ($top_ten_unlock) {
				$response['status'] = 201;
				echoJson($response);
				exit(0);
			}

			$voucher = $database->get('user', 'voucher', ['id' => $user_id]);
			if ($voucher < 1) {
				$response['status'] = -202;
				$response['message'] = '由于您的券数余额不足，解锁失败';
				echoJson($response);
				exit(0);
			}

			$database->insert('prediction_top_ten_unlock', ['user_id' => $user_id, 'event_id' => $event_id, 'prediction_top_ten_id' => $top_ten_id, 'created_at' => date('Y-m-d H:i:s', time())]);
			$database->update("user", ["voucher[-]" => 1], ["id" => $user_id]);

			$response['status'] = 200;
			$response['message'] = '解锁成功！';
			echoJson($response);
			break;
		case 'get_match_result':
			$category_id = intval($_GET['category_id']);
			$league_id = intval($_GET['league_id']);
			$year = intval($_GET['year']);
			$month = intval($_GET['month']);
			if ($month && !$year) {
				$year = date('Y', time());
			}
			$sorting = 2;
			if (isset($_GET['sorting'])) {
				$sorting = intval($_GET['sorting']);
			}
			if (!in_array($sorting, [1, 2])) {
				exit(0);
			}

			$page = 1;
			if (isset($_GET['page'])) {
				$page = intval($_GET['page']);
			}

			$limit = 10;

			$begin = (intval($page) - 1) * $limit;

			if ($year || $month) {
				if ($month) {
					$month = str_pad($month, 2, "0", STR_PAD_LEFT);
					$start_time = $year . '-' . $month . '-01';
					$end_time = date('Y-m-d', strtotime("+1 month", strtotime($start_time)));
				}
				else {
					$start_time = $year . '-01-01';
					$end_time = ($year+1) . '-01-01';
				}
				$where['match_at[<>]'] = [$start_time, $end_time];
			}
			else {
				$where['match_at[!]'] = null;
			}

			if ($category_id) {
				$where['category_id'] = $category_id;
			}
			else {
				$where['category_id[>]'] = 0;
			}

			if ($league_id) {
				$where['league_id'] = $league_id;
			}
			else {
				$where['league_id[>]'] = 0;
			}

			$where['ended'] = 1;
			$where['disabled'] = 0;

			$total_count = $database->count('event', $where);

			$total_page = ceil($total_count / $limit);

			if ($sorting == 1) {
				$where['ORDER'] = ['match_at' => 'ASC'];
			}
			else {
				$where['ORDER'] = ['match_at' => 'DESC'];
			}
			$where['LIMIT'] = [$begin, $limit];

	      	$exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";

			$datas = $database->select('event', ['id', 'category_id', 'match_at', 'league_id', 'home_team_id', 'away_team_id', 'editor_note'], $where);
			foreach ($datas as $key => $value) {
				$datas[$key]['league_name'] = $database->get('league', 'name_zh', ['id' => $value['league_id']]);
				$datas[$key]['home_team_name'] = $database->get('team', 'name_zh', ['id' => $value['home_team_id']]);
				$datas[$key]['away_team_name'] = $database->get('team', 'name_zh', ['id' => $value['away_team_id']]);
				$result = $database->get('result', ['handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'handicap_odds', 'handicap_bet', 'over_under_odds', 'over_under_bet', 'single_odds'], ['event_id' => $value['id']]);
				if ($result['single_home'] == 1) {
					$datas[$key]['win_team_name'] = $datas[$key]['home_team_name'];
					$datas[$key]['single'] = '主';
				}
				else if ($result['single_away'] == 1) {
					$datas[$key]['win_team_name'] = $datas[$key]['away_team_name'];
					$datas[$key]['single'] = '客';
				}
				else if ($result['single_tie'] == 1) {
					$datas[$key]['win_team_name'] = '-';
					$datas[$key]['single'] = '和';
				}

				if ($value['category_id'] == 4) {
					$datas[$key]['handicap'] = '-';
					$datas[$key]['over_under'] = '-';
				}
				else {
					if ($result['handicap_home'] == 1) {
						$datas[$key]['handicap'] = $datas[$key]['home_team_name'] . ' 主 ' . $result['handicap_bet'] . '/' . $result['handicap_odds'];
					}
					else if ($result['handicap_away'] == 1) {
						$datas[$key]['handicap'] = $datas[$key]['away_team_name'] . ' 客 ' . $result['handicap_bet'] . '/' . $result['handicap_odds'];
					}

					if ($result['over_under_home'] == 1) {
						$datas[$key]['over_under'] = $datas[$key]['home_team_name'] . ' 主 ' . $result['over_under_bet'] . '/' . $result['over_under_odds'];
					}
					else if ($result['over_under_away'] == 1) {
						$datas[$key]['over_under'] = $datas[$key]['away_team_name'] . ' 客 ' . $result['over_under_bet'] . '/' . $result['over_under_odds'];
					}
				}

				preg_match_all($exp, $prediction['editor_note'], $matches);

				if (count($matches[0]) > 0) {
				    for ($i=0; $i < count($matches[0]); $i++) { 
				        $datas[$key]['editor_note'] = str_replace($matches[0][$i], '<video width="640" height="360" controls><source src="'.$matches[4][$i].'" type="video/mp4">Your browser does not support the video tag.</video>', $value['editor_note']);
				    }
				}
			}
			$response['list'] = $datas;
			$response['current_page'] = $page;
			$response['total_page'] = $total_page;
			echoJson($response);
			break;
		case 'get_prediction_history';
			$now = time();
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

			$category_id = intval($_POST['category_id']);
			$league_id = intval($_POST['league_id']);
			$year = intval($_POST['year']);
			$month = intval($_POST['month']);
			if ($month && !$year) {
				$year = date('Y', time());
			}
			$sorting1 = 2;
			if (isset($_POST['sorting1'])) {
				$sorting1 = intval($_POST['sorting1']);
			}
			if (!in_array($sorting1, [0, 1, 2])) {
				exit(0);
			}
			$sorting2 = 2;
			if (isset($_POST['sorting2'])) {
				$sorting2 = intval($_POST['sorting2']);
			}
			if (!in_array($sorting2, [0, 1, 2])) {
				exit(0);
			}
			$sorting3 = 2;
			if (isset($_POST['sorting3'])) {
				$sorting3 = intval($_POST['sorting3']);
			}
			if (!in_array($sorting3, [0, 1, 2])) {
				exit(0);
			}

			$page = 1;
			if (isset($_POST['page'])) {
				$page = intval($_POST['page']);
			}

			$limit = 10;

			$begin = (intval($page) - 1) * $limit;

			$where['user_id'] = $user_id;

			if ($year || $month) {
				if ($month) {
					$month = str_pad($month, 2, "0", STR_PAD_LEFT);
					$start_time = $year . '-' . $month . '-01';
					$end_time = date('Y-m-d', strtotime("+1 month", strtotime($start_time)));
				}
				else {
					$start_time = $year . '-01-01';
					$end_time = ($year+1) . '-01-01';
				}
				$where['match_at[<>]'] = [$start_time, $end_time];
			}
			else {
				$where['match_at[!]'] = null;
			}

			if ($category_id) {
				$where['category_id'] = $category_id;
			}
			else {
				$where['category_id[>]'] = 0;
			}

			if ($league_id) {
				$where['league_id'] = $league_id;
			}
			else {
				$where['league_id[>]'] = 0;
			}

			$where['status'] = 'predicted';
			// $where['ended'] = 1;

			$total_count = $database->count('prediction', $where);

			$total_page = ceil($total_count / $limit);

			// $where['ORDER'] = array();

			$order_arr = array();

			if ($sorting1 == 1) {
				// array_push($where['ORDER'], ['created_at' => 'ASC']);
				$order_arr['created_at'] = 'ASC';
			}
			else if ($sorting1 == 2) {
				// array_push($where['ORDER'], ['created_at' => 'DESC']);
				$order_arr['created_at'] = 'DESC';
			}
			if ($sorting2 == 1) {
				// array_push($where['ORDER'], ['match_at' => 'ASC']);
				$order_arr['match_at'] = 'ASC';
			}
			else if ($sorting2 == 2) {
				// array_push($where['ORDER'], ['match_at' => 'DESC']);
				$order_arr['match_at'] = 'DESC';
			}
			if ($sorting3 == 1) {
				// array_push($where['ORDER'], ['win_amount' => 'ASC']);
				$order_arr['win_amount'] = 'ASC';
			}
			else if ($sorting3 == 2) {
				// array_push($where['ORDER'], ['win_amount' => 'DESC']);
				$order_arr['win_amount'] = 'DESC';
			}

			$where['ORDER'] = $order_arr;
			$where['LIMIT'] = [$begin, $limit];

	      	$exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";

			$datas = $database->select('prediction', ['id', 'category_id', 'match_at', 'league_id', 'event_id', 'ended', 'handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'created_at', 'win_amount'], $where);

			foreach ($datas as $key => $value) {
				$datas[$key]['league_name'] = $database->get('league', 'name_zh', ['id' => $value['league_id']]);

				$event_info = $database->get('event', ['home_team_id', 'away_team_id', 'handicap_home_bet','handicap_home_odds','handicap_away_bet','handicap_away_odds','over_under_home_bet','over_under_home_odds','over_under_away_bet','over_under_away_odds'], ['id' => $value['event_id']]);

				$home_team_name = $database->get('team', 'name_zh', ['id' => $event_info['home_team_id']]);
				$away_team_name = $database->get('team', 'name_zh', ['id' => $event_info['away_team_id']]);

				$datas[$key]['predicted_at'] = $value['created_at'];

				if ($value['ended'] == 1) {
					$datas[$key]['status'] = '已结束';
				}
				else {
					if ($now < strtotime($value['match_at'])) {
						$datas[$key]['status'] = '未开赛';
					}
					else {
						$datas[$key]['status'] = '比赛中';
					}
				}

				if ($value['single_home'] == 1) {
					$datas[$key]['single'] = '主';
				}
				else if ($value['single_away'] == 1) {
					$datas[$key]['single'] = '客';
				}
				else if ($value['single_tie'] == 1) {
					$datas[$key]['single'] = '和';
				}
				else {
					$datas[$key]['single'] = '-';
				}

				if ($value['category_id'] == 4) {
					$datas[$key]['handicap'] = '-';
					$datas[$key]['over_under'] = '-';
				}
				else {
					if ($value['handicap_home'] == 1) {
						$datas[$key]['handicap'] = $home_team_name . ' 主 ' . $event_info['handicap_home_bet'] . '/' . $event_info['handicap_home_odds'];
					}
					else if ($value['handicap_away'] == 1) {
						$datas[$key]['handicap'] = $away_team_name . ' 客 ' . $event_info['handicap_away_bet'] . '/' . $event_info['handicap_away_odds'];
					}
					else {
						$datas[$key]['handicap'] = '-';
					}

					if ($value['over_under_home'] == 1) {
						$datas[$key]['over_under'] = $home_team_name . ' 主 ' . $event_info['over_under_home_bet'] . '/' . $event_info['over_under_home_odds'];
					}
					else if ($value['over_under_away'] == 1) {
						$datas[$key]['over_under'] = $away_team_name . ' 客 ' . $event_info['over_under_away_bet'] . '/' . $event_info['over_under_away_odds'];
					}
					else {
						$datas[$key]['over_under'] = '-';
					}
				}
			}
			$response['list'] = $datas;
			$response['current_page'] = $page;
			$response['total_page'] = $total_page;
			echoJson($response);
			break;
		default:
			break;
	}

?>