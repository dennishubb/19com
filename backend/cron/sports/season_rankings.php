<?php
	//30 00 * * * /usr/local/php/bin/php season_rankings.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include(__DIR__.'/../../include/shared_function.inc.php');
	include(__DIR__.'/../../config/config.localhost.inc.php');
	include(__DIR__.'/../../include/db.inc.php');
	include(__DIR__.'/../../model/category.php');
	include(__DIR__.'/../../model/season_list.php');
	include(__DIR__.'/../../model/league.php');
	include(__DIR__.'/../../model/team.php');
	include(__DIR__.'/../../model/season_ranking_basketball.php');
	include(__DIR__.'/../../model/season_ranking_soccer.php');


	$category 						= new category();
	$season_list					= new season_list();
	$season_ranking_basketball		= new season_ranking_basketball();
	$season_ranking_soccer			= new season_ranking_soccer();
	$league							= new league();
	$team							= new team();

	$dbc->where('parent_id', '0');
	$dbc->where('type', 'sport');
	$dbc->where('disabled', '0');

	$res =  $dbc->get('category');
	foreach($res as $category){
		$category_id[strtolower($category['name'])] = $category['id'];
	}

	$date = date('Y-m-d H:i:s');

	//basketball season rankings
	$season_list_result	= $season_list->where('category_id', $category_id['basketball'])->where('current', 1)->get(null, array('season_id'));
	foreach ($season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;
		$url = "{$api_urls['basketball_season_tables']}?user={$user}&secret={$secret}&season_id={$season_id}";

		$response = httpGet($url);
		$result = json_decode($response, true);
		
		$team_names	= array();
		if (isset($result['data']['teams'])) {
			foreach ($result['data']['teams'] as $teams_key => $teams_value) {
				$team_names[$teams_value['id']] = $teams_value['name_zh'];
			}
		}

		if (isset($result['data']['tables'])) {
			foreach ($result['data']['tables'] as $tables_key => $tables_value) {
				foreach ($tables_value['rows'] as $rows_key => $rows_value) {
					$team_id = $rows_value['team_id'];
					$league_id 	= $team->where('category_id', $category_id['basketball'])->where('api_id', $team_id)->getValue('league_id');
					
					$season_rankingObj	 = $season_ranking_basketball->where('season_id', $season_id)->where('team_id', $team_id)->getOne();
					if ($season_rankingObj) {
						$season_rankingObj->updated_at	= $date;
					}
					else {
						$season_rankingObj				= $season_ranking_basketball;
						$season_rankingObj->isNew		= true;
						$season_rankingObj->created_at	= $date;
					}
					
					$season_rankingObj->season_id			= $season_id;
					$season_rankingObj->league_id			= $league_id ? $league_id:0;
					$season_rankingObj->team_id				= $team_id;
					$season_rankingObj->team_name			= isset($team_names[$team_id])?$team_names[$team_id]:$team->where('api_id', $team_id)->getValue('name_zh');
					$season_rankingObj->scope				= $tables_value['scope'];
					$season_rankingObj->name				= $tables_value['name'];
					$season_rankingObj->position			= $rows_value['position'];
					$season_rankingObj->diff_avg			= isset($rows_value['detail']['diff_avg']) ? $rows_value['detail']['diff_avg']:'0';
					$season_rankingObj->streaks				= isset($rows_value['detail']['streaks']) ? $rows_value['detail']['streaks']:'0';
					$season_rankingObj->home				= isset($rows_value['detail']['home']) ? $rows_value['detail']['home']:'-';
					$season_rankingObj->points_avg			= isset($rows_value['detail']['points_avg']) ? $rows_value['detail']['points_avg']:'0';
					$season_rankingObj->points_against_avg	= isset($rows_value['detail']['points_against_avg']) ? $rows_value['detail']['points_against_avg']:'0';
					$season_rankingObj->won					= isset($rows_value['detail']['won']) ? $rows_value['detail']['won']:'0';
					$season_rankingObj->lost					= isset($rows_value['detail']['lost']) ? $rows_value['detail']['lost']:'0';
					$season_rankingObj->division			= isset($rows_value['detail']['division']) ? $rows_value['detail']['division']:'-';
					$season_rankingObj->game_back			= isset($rows_value['detail']['game_back']) ? $rows_value['detail']['game_back']:'-';
					$season_rankingObj->away				= isset($rows_value['detail']['away']) ? $rows_value['detail']['away']:'-';
					$season_rankingObj->win_rate			= isset($rows_value['detail']['won_rate']) ? $rows_value['detail']['won_rate']:'-';
					$season_rankingObj->conference			= isset($rows_value['detail']['conference']) ? $rows_value['detail']['conference']:'-';
					$season_rankingObj->last_ten			= isset($rows_value['detail']['last_10']) ? $rows_value['detail']['last_10']:'-';
					$season_rankingObj->save();
				}
			}
		}
	}

	//Soccer season rankings	
	$season_list_result	= $season_list->where('category_id', $category_id['soccer'])->where('current', 1)->get(null, array('season_id'));
	foreach ($season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;

		$url = "{$api_urls['soccer_season_detail']}?user={$user}&secret={$secret}&id={$season_id}";
		$response = httpGet($url);
		$result = json_decode($response, true);
		
		$team_names = array();
		if(isset($result['teams'])){
			$json_data 	= $result['teams'];
			foreach($json_data as $teams_key => $teams_value){
				$team_names[$teams_value['id']] = $teams_value['name_zh'];
			}
		}
		
		if(isset($result['table']['tables'])){
			$json_data = $result['table']['tables'];
			foreach($json_data as $key => $value) {

				$conference = $value['conference'];

				foreach($value['rows'] as $k => $v) {
					$team_id = $v['team_id'];
					$league_id 	= $team->where('category_id', $category_id['soccer'])->where('api_id', $team_id)->getValue('league_id');

					$season_rankingObj	 = $season_ranking_soccer->where('season_id', $season_id)->where('team_id', $team_id)->getOne();
					if ($season_rankingObj) {
						$season_rankingObj->updated_at	= $date;
					}
					else {
						$season_rankingObj				= $season_ranking_soccer;
						$season_rankingObj->isNew		= true;
						$season_rankingObj->created_at	= $date;
					}

					$season_rankingObj->season_id			= $season_id;
					$season_rankingObj->league_id			= $league_id ? $league_id:0;
					$season_rankingObj->team_id				= $team_id;
					$season_rankingObj->team_name			= isset($team_names[$team_id])?$team_names[$team_id]:$team->where('api_id', $team_id)->getValue('name_zh');
					$season_rankingObj->conference			= $conference;
					$season_rankingObj->points				= $v['points'];
					$season_rankingObj->deduct_points		= $v['deduct_points'];
					$season_rankingObj->note				= $v['note_zh'];
					$season_rankingObj->position			= $v['position'];
					$season_rankingObj->total				= $v['total'];
					$season_rankingObj->won					= $v['won'];
					$season_rankingObj->draw				= $v['draw'];
					$season_rankingObj->lost				= $v['lost'];
					$season_rankingObj->goals				= $v['goals'];
					$season_rankingObj->goals_against		= $v['goals_against'];
					$season_rankingObj->goals_diff			= $v['goal_diff'];
					$season_rankingObj->home_points			= $v['home_points'];
					$season_rankingObj->home_position		= $v['home_position'];
					$season_rankingObj->home_total			= $v['home_total'];
					$season_rankingObj->home_won			= $v['home_won'];
					$season_rankingObj->home_draw			= $v['home_draw'];
					$season_rankingObj->home_loss			= $v['home_loss'];
					$season_rankingObj->home_goals			= $v['home_goals'];
					$season_rankingObj->home_goals_against	= $v['home_goals_against'];
					$season_rankingObj->home_goals_diff		= $v['home_goal_diff'];
					$season_rankingObj->away_points			= $v['away_points'];
					$season_rankingObj->away_position		= $v['away_position'];
					$season_rankingObj->away_total			= $v['away_total'];
					$season_rankingObj->away_won			= $v['away_won'];
					$season_rankingObj->away_draw			= $v['away_draw'];
					$season_rankingObj->away_loss			= $v['away_loss'];
					$season_rankingObj->away_goals			= $v['away_goals'];
					$season_rankingObj->away_goals_against	= $v['away_goals_against'];
					$season_rankingObj->away_goals_diff		= $v['away_goal_diff'];
					$season_rankingObj->save();
				}
			}
		}
	}

?>