<?php
	//30 00 * * * /usr/local/php/bin/php season_matches.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include(__DIR__.'/../../include/shared_function.inc.php');
	include(__DIR__.'/../../config/config.localhost.inc.php');
	include(__DIR__.'/../../include/db.inc.php');
	include(__DIR__.'/../../model/category.php');
	include(__DIR__.'/../../model/season_list.php');
	include(__DIR__.'/../../model/season_matches.php');
	include(__DIR__.'/../../model/league.php');

	$category 		= new category();
	$season_list	= new season_list();
	$season_matches = new season_matches();
	$league			= new league();

	$dbc->where('parent_id', '0');
	$dbc->where('type', 'sport');
	$dbc->where('disabled', '0');

	$res =  $dbc->get('category');
	foreach($res as $category){
		$category_id[strtolower($category['name'])] = $category['id'];
	}

	$date = date('Y-m-d H:i:s');

	//basketball season matches
	$basketball_season_list_result = $season_list->where('current', 1)->where('category_id', $category_id['basketball'])->get(null, array('season_id'));
	foreach ($basketball_season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;
		$url = "{$api_urls['basketball_season_matches']}?user={$user}&secret={$secret}&season_id={$season_id}";
		$response = httpGet($url);
		$result = json_decode($response, true);
		if(isset($result['data']['matches'])){
			foreach ($result['data']['matches'] as $match_key => $match_value) {
				$match_id = $match_value['id'];

				$season_matchesObj = $season_matches->where('match_id', $match_id)->getOne();
				if ($season_matchesObj) {
					$season_matchesObj->updated_at		= $date;
					if($season_matchesObj->status == 10 || $season_matchesObj->status == 12 || $season_matchesObj->status == 14) continue; //dont need to update when the game is finished/cut/canceled
				}
				else {
					$season_matchesObj	= $season_matches;
					$season_matchesObj->isNew			= true;
					$season_matchesObj->created_at		= $date;
				}

				//change to get id from league table?
				$season_matchesObj->league_id		= $league->where('category_id', $category_id['basketball'])->where('api_id', $match_value['comp'])->getValue('id');
				$season_matchesObj->season_id		= $season_id;
				$season_matchesObj->match_type		= $match_value['kind'];
				$season_matchesObj->status			= $match_value['status_id'];
				$season_matchesObj->match_time		= $match_value['match_time'];
				$season_matchesObj->home_team_id	= $match_value['home_team']['id'];
				$season_matchesObj->home_team_name	= $match_value['home_team']['name_zh'];
				$season_matchesObj->away_team_id	= $match_value['away_team']['id'];
				$season_matchesObj->away_team_name	= $match_value['away_team']['name_zh'];
				$season_matchesObj->home_score		= $match_value['home_score'];
				$season_matchesObj->away_score		= $match_value['away_score'];
				$season_matchesObj->match_id		= $match_id;

				$venue_id = 0;
				if (is_array($match_value['venue'])) {
					if (isset($match_value['venue']['id'])) {
						$venue_id = $match_value['venue']['id'];
					}
				}

				$season_matchesObj->venue_id 		= $venue_id;
				$season_matchesObj->round_stage_id	= isset($match_value['round']['stage_id']) ? $match_value['round']['stage_id'] : 0;
				$season_matchesObj->group_num		= isset($match_value['round']['group_num']) ? $match_value['round']['group_num'] : 0;
				$season_matchesObj->round_num		= isset($match_value['round']['round_num']) ? $match_value['round']['round_num'] : 0;
				$season_matchesObj->category_id		= $category_id['basketball'];
				$season_matchesObj->save();

	//			if ($venue_id) {$i++;
	//				$table_venue = 'basketball_venue';
	//				$item_venue = $database->select($table_venue, ['id'], ['id'=>$venue_id]);
	//
	//				$venue_data = array();
	//				$venue_data['name_en'] = $match_value['venue']['name_en'];
	//				$venue_data['name_zh'] = $match_value['venue']['name_zh'];
	//				$venue_data['capacity'] = $match_value['venue']['capacity'];
	//				$venue_data['city'] = $match_value['venue']['city'];
	//				if ($item_venue) {
	//					$venue_where['id'] = $venue_id;
	//					$venue_data['update_time'] = time();
	//					$database->update($table_venue, $venue_data, $venue_where);
	//				}
	//				else {
	//					$venue_data['id'] = $venue_id;
	//					$venue_data['create_time'] = time();
	//					$database->insert($table_venue, $venue_data);
	//				}
	//			}
			}
		}
		
	}

	//Soccer season matches
	$soccer_season_list_result = $season_list->where('current', 1)->where('category_id', $category_id['soccer'])->get(null, array('season_id'));
	foreach ($soccer_season_list_result as $season_listObj) {
		$season_id = $season_listObj->season_id;
		
		$url = "{$api_urls['soccer_season_detail']}?user={$user}&secret={$secret}&id={$season_id}";
		$response = httpGet($url);
		$result = json_decode($response, true);
		
		if(isset($result['teams'])){
			$json_data = $result['teams'];
			$team_names = array();
			foreach($json_data as $key => $value){
				$team_names[$value['id']] = $value['name_zh'];
			}
		}

		if(isset($result['matches'])){
			$json_data = $result['matches'];
			foreach($json_data as $k => $v) {
				$match_id	= $v['id'];

				$season_matchesObj = $season_matches->where('match_id', $match_id)->getOne();
				if ($season_matchesObj) {
					$season_matchesObj->updated_at		= $date;
					
					if($season_matchesObj->status == 8 || $season_matchesObj->status == 11 || $season_matchesObj->status == 12) continue; //dont need to update when the game is finished/cut/canceled
				}
				else {
					$season_matchesObj	= $season_matches;
					$season_matchesObj->isNew			= true;
					$season_matchesObj->created_at		= $date;
				}

				$season_matchesObj->league_id		= $league->where('category_id', $category_id['soccer'])->where('api_id', $result['competition']['id'])->getValue('id');
				$season_matchesObj->match_id		= $match_id;
				$season_matchesObj->season_id		= $v['season_id'];
				$season_matchesObj->status			= $v['status_id'];
				$season_matchesObj->home_team_id	= $v['home_team_id'];
				$season_matchesObj->away_team_id	= $v['away_team_id'];
				$season_matchesObj->home_team_name	= isset($team_names[$v['home_team_id']])?$team_names[$v['home_team_id']]:$team->where('api_id', $v['home_team_id'])->getValue('name_zh');
				$season_matchesObj->away_team_name	= isset($team_names[$v['away_team_id']])?$team_names[$v['away_team_id']]:$team->where('api_id', $v['away_team_id'])->getValue('name_zh');
				$season_matchesObj->match_time		= $v['match_time'];
				$season_matchesObj->round_stage_id	= $v['round']['stage_id'];
				$season_matchesObj->round_num		= $v['round']['round_num'];
				$season_matchesObj->group_num		= $v['round']['group_num'];
				$season_matchesObj->home_position	= $v['position']['home'];
				$season_matchesObj->away_position	= $v['position']['away'];
				$season_matchesObj->home_score		= $v['home_scores'][0];
				$season_matchesObj->away_score		= $v['away_scores'][0];
				$season_matchesObj->match_id		= $match_id;
				$season_matchesObj->category_id		= $category_id['soccer'];
				$season_matchesObj->save();
			}
		}
		
	}

?>