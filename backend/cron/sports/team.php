<?php
	//00 00 * * * /usr/local/php/bin/php team.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include('include/shared_function.inc.php');
	include('config/config.localhost.inc.php');
	include('include/db.inc.php');
	include('model/category.php');
	include('model/team.php');
	include('model/league.php');

	$category = new category();
	$team 	  = new team();
	$league	  = new league();

	$dbc->where('parent_id', '0');
	$dbc->where('type', 'sport');
	$dbc->where('disabled', '0');

	$res =  $dbc->get('category');
	foreach($res as $category){
		$category_id[strtolower($category['name'])] = $category['id'];
	}

	$date = date('Y-m-d H:i:s');

	//Basketball Team list
	$url = "{$api_urls['basketball_team_list']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	foreach ($result as $key => $value) {
		
		if($value['matchevent_id'] <= 0) continue;
		
		$teamObj	= $team->where('category_id', $category_id['basketball'])->where('api_id', $value['id'])->getOne();
		if($teamObj){
			$teamObj->updated_at	= $date;
		}else{
			$teamObj				= $team;
			$teamObj->isNew			= true;
			$teamObj->created_at	= $date;
		}
		
		//need get league id from api id 
		if($value['matchevent_id'] != 0){
			
		}
		$league_id	=  $league->where('category_id', $category_id['basketball'])->where('api_id', $value['matchevent_id'])->getValue('id');
		
		$teamObj->league_id			= $league->where('category_id', $category_id['basketball'])->where('api_id', $value['matchevent_id'])->getValue('id');
		$teamObj->name_zh			= $value['name_zh'];
		$teamObj->name_zht			= $value['name_zht'];
		$teamObj->name_en			= $value['name_en'];
		$teamObj->short_name_zh		= $value['short_name_zh'];
		$teamObj->short_name_zht	= $value['short_name_zht'];
		$teamObj->short_name_en		= $value['name_en'];
		$teamObj->logo				= $value['logo'];
		$teamObj->api_id			= $value['id'];
		$teamObj->category_id		= $category_id['basketball'];
		$teamObj->save();
	}

	//Soccer Team list
	$url = "{$api_urls['soccer_team_list']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	foreach ($result as $key => $value) {
		if($value['matchevent_id'] <= 0) continue;
		
		$teamObj	= $team->where('category_id', $category_id['soccer'])->where('api_id', $value['id'])->getOne();
		if($teamObj){
			$teamObj->updated_at	= $date;
		}else{
			$teamObj				= $team;
			$teamObj->isNew			= true;
			$teamObj->created_at	= $date;
		}
		
		//need get league id from api id 
		$teamObj->league_id			= $league->where('category_id', $category_id['soccer'])->where('api_id', $value['matchevent_id'])->getValue('id');
		$teamObj->name_zh			= $value['name_zh'];
		$teamObj->name_zht			= $value['name_zht'];
		$teamObj->name_en			= $value['name_en'];
		$teamObj->short_name_zh		= $value['short_name_zh'];
		$teamObj->short_name_zht	= $value['short_name_zht'];
		$teamObj->short_name_en		= $value['name_en'];
		$teamObj->logo				= $value['logo'];
		$teamObj->api_id			= $value['id'];
		$teamObj->category_id		= $category_id['soccer'];
		$teamObj->save();
	}

?>