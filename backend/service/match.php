<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');

	$action = $_GET['action'];

	switch ($action) {
		case 'get_league_ranking':
			$datas = array();
			$nba_season = $database->select('season_list', 'season_id', ['league_id' => 1, 'ORDER' => ['season_id' => 'DESC'], 'LIMIT' => 2]);
			$epl_season = $database->select('season_list', 'season_id', ['league_id' => 1423, 'ORDER' => ['season_id' => 'DESC'], 'LIMIT' => 2]);

			$nba_current_season_id = $nba_season[0];
			$epl_current_season_id = $epl_season[0];
			$nba_last_season_id = $nba_season[1];
			$epl_last_season_id = $epl_season[1];

			$nba_team_ranking = $database->select('season_ranking_basketball', ['team_name', 'won', 'lost'], ['win_rate[>]' => 0, 'season_id' => $nba_current_season_id, 'scope[>]' => 0, 'ORDER' => ['win_rate' => 'DESC'],'LIMIT' => 10]);
			if (count($nba_team_ranking) == 0) {
				$nba_team_ranking = $database->select('season_ranking_basketball', ['team_name', 'won', 'lost'], ['win_rate[>]' => 0, 'season_id' => $nba_last_season_id, 'scope[>]' => 0, 'ORDER' => ['win_rate' => 'DESC'],'LIMIT' => 10]);
			}
			$datas['nba_team_ranking'] = $nba_team_ranking;

			$nba_shooter_ranking  = $database->select('season_player_stats_basketball', ['player_name', 'team_name', 'points_avg'], ['points_avg[>]' => 0, 'season_id' => $nba_current_season_id, 'scope' => 6, 'ORDER' => ['points_avg' => 'DESC'],'LIMIT' => 10]);
			if (count($nba_shooter_ranking) == 0) {
				$nba_shooter_ranking  = $database->select('season_player_stats_basketball', ['player_name', 'team_name', 'points_avg'], ['points_avg[>]' => 0, 'season_id' => $nba_last_season_id, 'scope' => 6, 'ORDER' => ['points_avg' => 'DESC'],'LIMIT' => 10]);
			}
			$datas['nba_shooter_ranking'] = $nba_shooter_ranking;

			$epl_team_ranking = $database->select('season_ranking_soccer', ['team_name', 'won', 'draw', 'lost', 'points'], ['points[>]' => 0, 'season_id' => $epl_current_season_id, 'ORDER' => ['points' => 'DESC'],'LIMIT' => 10]);
			if (count($epl_team_ranking) == 0) {
				$epl_team_ranking = $database->select('season_ranking_soccer', ['team_name', 'won', 'draw', 'lost', 'points'], ['points[>]' => 0, 'season_id' => $epl_last_season_id, 'ORDER' => ['points' => 'DESC'],'LIMIT' => 10]);
			}
			$datas['epl_team_ranking'] = $epl_team_ranking;

			$epl_shooter_ranking = $database->select('season_player_stats_soccer', ['player_name', 'team_name', 'goals', 'penalty'], ['goals[>]' => 0, 'season_id' => $epl_current_season_id, 'ORDER' => ['goals' => 'DESC'],'LIMIT' => 10]);
			if (count($epl_shooter_ranking) == 0) {
				$epl_shooter_ranking = $database->select('season_player_stats_soccer', ['player_name', 'team_name', 'goals', 'penalty'], ['goals[>]' => 0, 'season_id' => $epl_last_season_id, 'ORDER' => ['goals' => 'DESC'],'LIMIT' => 10]);
			}
			$datas['epl_shooter_ranking'] = $epl_shooter_ranking;
			echoJson($datas);
			break;
		case 'get_live_matches':
			$datas = array();

			$league_list = array(
				'popular' => '热门赛事',
				'football' => '足球',
				'basketball' => '篮球',
				'1' => 'NBA',
				'3' => 'CBA',
				'1423' => '英超',
				'1461' => '西甲',
				'1469' => '德甲',
				'1449' => '意甲',
				'1482' => '法甲',
				'1877' => '中超',
				'1388' => '欧冠杯',
				'1827' => '亚冠杯',
				'1387' => '欧洲杯'
			);

			$league_id = $_GET['league_id'];
			if (!$league_id) {
				exit(0);
			}
			$today = strtotime(date('Y-m-d'));
			$today_3 = strtotime(date('Y-m-d', $today + 3*24*60*60));

			if ($league_id == 'popular') {
				$data_1 = getLiveMatches(1423, $today, $today_3);
				$data_2 = getLiveMatches(1461, $today, $today_3);
				$data_3 = getLiveMatches(1469, $today, $today_3);
				$data_4 = getLiveMatches(1449, $today, $today_3);
				$data_5 = getLiveMatches(1482, $today, $today_3);
				$data_6 = getLiveMatches(1877, $today, $today_3);
				$data_7 = getLiveMatches(1388, $today, $today_3);
				$data_8 = getLiveMatches(1827, $today, $today_3);
				$data_9 = getLiveMatches(1387, $today, $today_3);
				$data_10 = getLiveMatches(1, $today, $today_3);
				$data_11 = getLiveMatches(3, $today, $today_3);

				$datas = array_merge($data_1, $data_2, $data_3, $data_4, $data_5, $data_6, $data_7, $data_8, $data_9, $data_10, $data_11);
				$datas = array_sort ($datas, 'match_time', 'ASC');
			}
			else if ($league_id == 'football') {
				$data_1 = getLiveMatches(1423, $today, $today_3);
				$data_2 = getLiveMatches(1461, $today, $today_3);
				$data_3 = getLiveMatches(1469, $today, $today_3);
				$data_4 = getLiveMatches(1449, $today, $today_3);
				$data_5 = getLiveMatches(1482, $today, $today_3);
				$data_6 = getLiveMatches(1877, $today, $today_3);
				$data_7 = getLiveMatches(1388, $today, $today_3);
				$data_8 = getLiveMatches(1827, $today, $today_3);
				$data_9 = getLiveMatches(1387, $today, $today_3);

				$datas = array_merge($data_1, $data_2, $data_3, $data_4, $data_5, $data_6, $data_7, $data_8, $data_9);
				$datas = array_sort ($datas, 'match_time', 'ASC');
			}
			else if ($league_id == 'basketball') {
				$data_1 = getLiveMatches(1, $today, $today_3);
				$data_2 = getLiveMatches(3, $today, $today_3);

				$datas = array_merge($data_1, $data_2);
				$datas = array_sort ($datas, 'match_time', 'ASC');
			}
			else {
				$datas = getLiveMatches($league_id, $today, $today_3);
			}

			foreach ($datas as $key => $value) {
				$datas[$key]['league_name'] = $league_list[$league_id];
			}

			echoJson($datas);
			break;
		case 'get_leagues':
			$category_id = intval($_GET['category_id']);
			if (!$category_id) {
				exit(0);
			}
			$datas = $database->select('league', ['id', 'name_zh'], ['category_id' => $category_id, 'has_event' => 1]);
			echoJson($datas);
			break;
		default:
			break;
	}

	function getLiveMatches($league_id, $from, $to) {
		global $database;
		$today = $from;
		$today_3 = $to;

		$where['match_time[>=]'] = $today;
		$where['match_time[<=]'] = $today_3;
		$where['ORDER'] = ['match_time' => 'ASC'];

		$season = $database->select('season_list', 'season_id', ['league_id' => $league_id, 'ORDER' => ['season_id' => 'DESC'], 'LIMIT' => 2]);

		$current_season_id = $season[0];
		$last_season_id = $season[1];

		$where['season_id'] = $current_season_id;

		$fields = ['category_id', 'match_type', 'home_team_name', 'match_time', 'away_team_name', 'home_score', 'away_score', 'round_stage_id'];

		$datas = $database->select('season_matches', $fields, $where);

		if (count($datas) == 0) {
			if (isset($where)) {
				unset($where);
			}
			$where['match_time[>]'] = 0;
			$where['league_id'] = $league_id;
			$where['home_score[>]'] = 0;
			$where['ORDER'] = ['match_time' => 'DESC'];
			$last_match_time = $database->get('season_matches', ['match_time'], $where);
			
			if ($last_match_time) {
				if (isset($where)) {
					unset($where);
				}
				$where['match_time'] = $last_match_time;
				$where['league_id'] = $league_id;
				$datas = $database->select('season_matches', $fields, $where);
			}
		}

		foreach ($datas as $key => $value) {
			if ($value['category_id'] == 2) {
				if ($value['match_type'] == 1) {
					$datas[$key]['match_type_name'] = '常规赛';
				}
				else if ($value['match_type'] == 2) {
					$datas[$key]['match_type_name'] = '季后赛';
				}
				else if ($value['match_type'] == 3) {
					$datas[$key]['match_type_name'] = '季前赛';
				}
				else if ($value['match_type'] == 4) {
					$datas[$key]['match_type_name'] = '全明星';
				}
				else if ($value['match_type'] == 5) {
					$datas[$key]['match_type_name'] = '杯赛';
				}
			}
			else if ($value['category_id'] == 1) {
				$datas[$key]['match_type_name'] = $database->get('t_soccer_matchevent_stage', 'name_zh', ['id' => $value['round_stage_id']]);
			}

			unset($datas[$key]['match_type']);
			unset($datas[$key]['round_stage_id']);
		}
		return $datas;
	}

?>