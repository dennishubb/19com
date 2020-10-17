<?php
	//00 00 * * * /usr/local/php/bin/php league.php
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

	//Basketball league list
	$url = "{$api_urls['basketball_matchevent_list']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	$matchevents = $result['matchevents'];
	foreach ($matchevents as $key => $value) {
		$leagueObj	= $league->where('category_id', $category_id['basketball'])->where('api_id', $value['id'])->getOne();
		if($leagueObj){
			$leagueObj->updated_at	= $date;
		}else{
			$leagueObj				= $league;
			$leagueObj->isNew		= true;
			$leagueObj->created_at	= $date;
		}

		$leagueObj->area_id			= $value['area_id'];
		$leagueObj->country_id		= $value['country_id'];
		$leagueObj->name_zh			= $value['name_zh'];
		$leagueObj->name_zht		= $value['name_zht'];
		$leagueObj->name_en			= $value['name_en'];
		$leagueObj->short_name_zh	= $value['short_name_zh'];
		$leagueObj->short_name_zht	= $value['short_name_zht'];
		$leagueObj->short_name_en	= $value['name_en'];
		$leagueObj->logo			= $value['logo'];
		$leagueObj->api_id			= $value['id'];
		$leagueObj->category_id		= $category_id['basketball'];
		$leagueObj->save();
	}

	//Soccer League list
	$url = "{$api_urls['soccer_matchevent_list']}?user={$user}&secret={$secret}";

	$response = httpGet($url);
	$result = json_decode($response, true);

	$matchevents = $result['matchevents'];
	foreach ($matchevents as $key => $value) {
		$leagueObj	= $league->where('category_id', $category_id['soccer'])->where('api_id', $value['id'])->getOne();
		if($leagueObj){
			$leagueObj->updated_at	= $date;
		}else{
			$leagueObj				= $league;
			$leagueObj->isNew		= true;
			$leagueObj->created_at	= $date;
		}
		
		$leagueObj->area_id			= $value['area_id'];
		$leagueObj->country_id		= $value['country_id'];
		$leagueObj->name_zh			= $value['name_zh'];
		$leagueObj->name_zht		= $value['name_zht'];
		$leagueObj->name_en			= $value['name_en'];
		$leagueObj->short_name_zh	= $value['short_name_zh'];
		$leagueObj->short_name_zht	= $value['short_name_zht'];
		$leagueObj->short_name_en	= $value['name_en'];
		$leagueObj->logo			= $value['logo'];
		$leagueObj->api_id			= $value['id'];
		$leagueObj->category_id		= $category_id['soccer'];
		$leagueObj->save();
	}

?>