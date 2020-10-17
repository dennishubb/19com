<?php
	//00 00 * * * /usr/local/php/bin/php season_list.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include(__DIR__.'/../../include/shared_function.inc.php');
	include(__DIR__.'/../../config/config.localhost.inc.php');
	include(__DIR__.'/../../include/db.inc.php');
	include(__DIR__.'/../../model/category.php');
	include(__DIR__.'/../../model/season_list.php');
	include(__DIR__.'/../../model/league.php');

	$category 		= new category();
	$season_list 	= new season_list();
	$league			= new league();

	$dbc->where('parent_id', '0');
	$dbc->where('type', 'sport');
	$dbc->where('disabled', '0');

	$res =  $dbc->get('category');
	foreach($res as $category){
		$category_id[strtolower($category['name'])] = $category['id'];
	}

	$date = date('Y-m-d H:i:s');

	//Basketball season list
	$url = "{$api_urls['basketball_season_list']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	foreach ($result['data'] as $key => $value) {
		$matchevent_id = $value['id'];
		$count = 0;
		foreach ($value['seasons'] as $season_key => $season_value) {
			$count++;
			
			$season_listObj = $season_list->where('category_id', $category_id['basketball'])->where('season_id', $season_value['id'])->getOne();
			if ($season_listObj) {
				
				$season_listObj->updated_at		= $date;
			}
			else {
				$season_listObj	= $season_list;
				$season_listObj->isNew			= true;
				$season_listObj->created_at		= $date;
			}
			
			$season_listObj->season_id 		= $season_value['id'];
			$season_listObj->season 		= $season_value['season'];
			$season_listObj->league_id 		= $league->where('category_id', $category_id['basketball'])->where('api_id', $matchevent_id)->getValue('id');
			$season_listObj->category_id	= $category_id['basketball'];
			
			if(count($value['seasons']) == $count){
				$season_list->updateCustom(array('current' => 0), array('league_id' => $season_listObj->league_id));
				$season_listObj->current 	= 1;
			}
			
			$season_listObj->save();
		}
	}

	//Soccer Season list
	$url = "{$api_urls['soccer_season_list']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	$json_data = $result['competitions'];
	foreach($json_data as $k => $v) {
		$matchevent_id = $v['id'];
		
		$seasons_data = $v['seasons'];
		$count	= 0;
		foreach($seasons_data as $key => $season_value) {
			$count++;
			
			$season_listObj = $season_list->where('category_id', $category_id['soccer'])->where('season_id', $season_value['id'])->getOne();
			if ($season_listObj) {
				$season_listObj->updated_at		= $date;
			}
			else {
				$season_listObj	= $season_list;
				$season_listObj->isNew			= true;
				$season_listObj->created_at		= $date;
			}
			
			$season_listObj->season_id 		= $season_value['id'];
			$season_listObj->season 		= $season_value['season'];
			$season_listObj->league_id 		= $league->where('category_id', $category_id['soccer'])->where('api_id', $matchevent_id)->getValue('id');
			$season_listObj->category_id	= $category_id['soccer'];
			
			if($count == 1){
				$season_list->updateCustom(array('current' => 0), array('league_id' => $season_listObj->league_id));
				$season_listObj->current 	= 1;
			}
			
			$season_listObj->save();
		}

	}

?>