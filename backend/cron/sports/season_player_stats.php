<?php
	//30 00 * * * /usr/local/php/bin/php season_player_stats.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include(__DIR__.'/../../include/shared_function.inc.php');
	include(__DIR__.'/../../config/config.localhost.inc.php');
	include(__DIR__.'/../../include/db.inc.php');
	include(__DIR__.'/../../model/category.php');
	include(__DIR__.'/../../model/season_list.php');
	include(__DIR__.'/../../model/league.php');
	include(__DIR__.'/../../model/team.php');
	include(__DIR__.'/../../model/season_player_stats_basketball.php');
	include(__DIR__.'/../../model/season_player_stats_soccer.php');


	$category 						= new category();
	$season_list					= new season_list();
	$season_player_stats_basketball	= new season_player_stats_basketball();
	$season_player_stats_soccer		= new season_player_stats_soccer();
	$league							= new league();

	$dbc->where('parent_id', '0');
	$dbc->where('type', 'sport');
	$dbc->where('disabled', '0');

	$res =  $dbc->get('category');
	foreach($res as $category){
		$category_id[strtolower($category['name'])] = $category['id'];
	}

	$date = date('Y-m-d H:i:s');

	//basketball season player stats
	$season_list_result	= $season_list->where('category_id', $category_id['basketball'])->where('current', 1)->get(null, array('season_id'));
	foreach ($season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;
		$url = "{$api_urls['basketball_season_players_stats']}?user={$user}&secret={$secret}&season_id={$season_id}";
		$response = httpGet($url);
		
		$result = json_decode($response, true);
		
		$player_names = array();
		if (isset($result['data']['players'])) {
			foreach($result['data']['players'] as $player_key => $player_value){
				$player_names[$player_value['id']] = $player_value['name_zh'];
			}
		}

		if (isset($result['data']['player_stats'])) {
			foreach ($result['data']['player_stats'] as $player_stats_key => $player_stats_value) {

				$player_id = $player_stats_value['player_id'];
				
				$player_statsObj = $season_player_stats_basketball->where('season_id', $season_id)->where('player_id', $player_id)->getOne();
				if ($player_statsObj) {
					$player_statsObj->updated_at	= $date;
				}
				else {
					$player_statsObj				= $season_player_stats_basketball;
					$player_statsObj->isNew			= true;
					$player_statsObj->created_at	= $date;
				}
				
				$player_statsObj->season_id				= $season_id;
				$player_statsObj->player_id				= $player_id;
				$player_statsObj->team_id				= $player_stats_value['team']['id'];
				$player_statsObj->league_id				= $league->where('category_id', $category_id['basketball'])->where('api_id', $player_stats_value['team']['competition_id'])->getValue('id');
				$player_statsObj->player_name			= isset($player_names[$player_id])?$player_names[$player_id]:'';
				$player_statsObj->team_name				= $player_stats_value['team']['name_zh'];
				$player_statsObj->scope					= $player_stats_value['scope'];
				$player_statsObj->matches				= $player_stats_value['matches'];
				$player_statsObj->first					= $player_stats_value['first'];
				$player_statsObj->court					= $player_stats_value['court'];
				$player_statsObj->minutes_played		= $player_stats_value['minutes_played'];
				$player_statsObj->points				= $player_stats_value['points'];
				$player_statsObj->points_avg			= $player_stats_value['points'] / $player_stats_value['matches'];
				$player_statsObj->free_throw_scored		= $player_stats_value['free_throws_scored'];
				$player_statsObj->free_throw_total		= $player_stats_value['free_throws_total'];
				$player_statsObj->free_throw_accuracy	= $player_stats_value['free_throws_accuracy'];
				$player_statsObj->two_points_scored		= $player_stats_value['two_points_scored'];
				$player_statsObj->two_points_total		= $player_stats_value['two_points_total'];
				$player_statsObj->two_points_accuracy	= $player_stats_value['two_points_accuracy'];
				$player_statsObj->three_points_scored	= $player_stats_value['three_points_scored'];
				$player_statsObj->three_points_total	= $player_stats_value['three_points_total'];
				$player_statsObj->three_points_accuracy	= $player_stats_value['three_points_accuracy'];
				$player_statsObj->field_goals_scored	= $player_stats_value['field_goals_scored'];
				$player_statsObj->field_goals_total		= $player_stats_value['field_goals_total'];
				$player_statsObj->field_goals_accuracy	= $player_stats_value['field_goals_accuracy'];
				$player_statsObj->personal_fouls		= $player_stats_value['personal_fouls'];
				$player_statsObj->rebounds				= $player_stats_value['rebounds'];
				$player_statsObj->defensive_rebounds	= $player_stats_value['defensive_rebounds'];
				$player_statsObj->offensive_rebounds	= $player_stats_value['offensive_rebounds'];
				$player_statsObj->assists				= $player_stats_value['assists'];
				$player_statsObj->turnovers				= $player_stats_value['turnovers'];
				$player_statsObj->steals				= $player_stats_value['steals'];
				$player_statsObj->blocks				= $player_stats_value['blocks'];
				$player_statsObj->save();
			}
		}
	}

	//Soccer season player stats	
	$season_list_result	= $season_list->where('category_id', $category_id['soccer'])->where('current', 1)->get(null, array('season_id'));
	foreach ($season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;

		$url = "{$api_urls['soccer_season_stats']}?user={$user}&secret={$secret}&id={$season_id}";
		$response = httpGet($url);
		$result = json_decode($response, true);

		if(isset($result['players_stats'])){
			$json_data = $result['players_stats'];
			foreach($json_data as $k => $v) {

				$player_id = $v['player']['id'];

				$player_statsObj = $season_player_stats_soccer->where('season_id', $season_id)->where('player_id', $player_id)->getOne();
				if ($player_statsObj) {
					$player_statsObj->updated_at	= $date;
				}
				else {
					$player_statsObj				= $season_player_stats_soccer;
					$player_statsObj->isNew			= true;
					$player_statsObj->created_at	= $date;
				}

				$player_statsObj->season_id				= $season_id;
				$player_statsObj->player_id				= $player_id;
				$player_statsObj->team_id				= $v['team']['id'];
				$player_statsObj->player_name			= $v['player']['name_zh'];
				$player_statsObj->team_name				= $v['team']['name_zh'];
				$player_statsObj->rating				= $v['rating'];
				$player_statsObj->matches				= $v['matches'];
				$player_statsObj->first					= $v['first'];
				$player_statsObj->goals					= $v['goals'];
				$player_statsObj->penalty				= $v['penalty'];
				$player_statsObj->assists				= $v['assists'];
				$player_statsObj->minutes_played		= $v['minutes_played'];
				$player_statsObj->red_cards				= $v['red_cards'];
				$player_statsObj->yellow_cards			= $v['yellow_cards'];
				$player_statsObj->shots					= $v['shots'];
				$player_statsObj->shots_on_target		= $v['shots_on_target'];
				$player_statsObj->dribble				= $v['dribble'];
				$player_statsObj->dribble_success		= $v['dribble_succ'];
				$player_statsObj->clearances			= $v['clearances'];
				$player_statsObj->blocked_shots			= $v['blocked_shots'];
				$player_statsObj->interceptions			= $v['interceptions'];
				$player_statsObj->tackles				= $v['tackles'];
				$player_statsObj->passes				= $v['passes'];
				$player_statsObj->passes_accuracy		= $v['passes_accuracy'];
				$player_statsObj->key_passes			= $v['key_passes'];
				$player_statsObj->crosses				= $v['crosses'];
				$player_statsObj->crosses_accuracy		= $v['crosses_accuracy'];
				$player_statsObj->long_balls			= $v['long_balls'];
				$player_statsObj->long_balls_accuracy	= $v['long_balls_accuracy'];
				$player_statsObj->duels					= $v['duels'];
				$player_statsObj->duels_won				= $v['duels_won'];
				$player_statsObj->dispossessed			= $v['dispossessed'];
				$player_statsObj->fouls					= $v['fouls'];
				$player_statsObj->was_fouled			= $v['was_fouled'];
				$player_statsObj->saves					= $v['saves'];
				$player_statsObj->punches				= $v['punches'];
				$player_statsObj->runs_out				= $v['runs_out'];
				$player_statsObj->runs_out_success		= $v['runs_out_succ'];
				$player_statsObj->good_high_claim		= $v['good_high_claim'];
				$player_statsObj->save();
			}
		}
	}

?>