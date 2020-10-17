<?php

	//patch old 19com area/country/matchevent/team to new 19com equivalent tables
	//run this in the root directory
	include('include/shared_function.inc.php');
	include('config/config.localhost.inc.php');
	include('include/db.inc.php');

//	$dbc->where('parent_id', '0');
//	$dbc->where('type', 'sport');
//	$dbc->where('disabled', '0');
//
//	$res =  $dbc->get('category');
//	foreach($res as $category){
//		$categories[strtolower($category['name'])] = $category['id'];
//	}

	$categories['soccer'] 		= '1';
	$categories['basketball']	= '2';

	//area -> country -> matchevent -> team
	$date = date("Y-m-d H:i:s");

	foreach($categories as $category_name => $category_id){
		$country = $dbc->get('t_'.$category_name.'_country');
	
		$area = $dbc->get('t_'.$category_name.'_area');

		$matchevent = $dbc->get('t_'.$category_name.'_matchevent');

		$team = $dbc->get('t_'.$category_name.'_team');

		foreach($area as $old_area_data){
			$new_area_insert['name_en'] 	= $old_area_data['name_en'];
			$new_area_insert['name_zh'] 	= $old_area_data['name_zh'];
			$new_area_insert['name_zht']	= $old_area_data['name_zht'];
			$new_area_insert['created_at']	= $date;
			$new_area_insert['updated_at']	= $date;
			$new_area_insert['category_id']	= $category_id;
			

			$new_area_id = $dbc->insert('area', $new_area_insert);
			
			$new_old_area_map[$old_area_data['id']] = $new_area_id;

			unset($new_area_insert);
		}
		
		foreach($country as $old_country){
			
			$new_country_insert['category_id']	= $category_id;
			$new_country_insert['name_en']		= $old_country['name_en'];
			$new_country_insert['name_zh']		= $old_country['name_zh'];
			$new_country_insert['name_zht']		= $old_country['name_zht'];
			$new_country_insert['area_id']		= isset($new_old_area_map[$old_country['area_id']]) ? $new_old_area_map[$old_country['area_id']] : 0;
			$new_country_insert['created_at']	= $date;
			$new_country_insert['updated_at']	= $date;

			$new_country_id	= $dbc->insert('country', $new_country_insert);

			$new_old_country_map[$old_country['id']] = $new_country_id;

			unset($new_country_insert);
		}
		
		foreach($matchevent as $old_matchevent){
			$new_league_insert['category_id']	= $category_id;
			$new_league_insert['area_id']		= isset($new_old_area_map[$old_matchevent['area_id']]) ? $new_old_area_map[$old_matchevent['area_id']] : 0;
			$new_league_insert['country_id']	= isset($new_old_country_map[$old_matchevent['country_id']]) ? $new_old_country_map[$old_matchevent['country_id']] : 0;
			$new_league_insert['name_en']		= $old_matchevent['name_en'];
			$new_league_insert['name_zh']		= $old_matchevent['name_zh'];
			$new_league_insert['name_zht']		= $old_matchevent['name_zht'];
			$new_league_insert['shortname_en']	= $old_matchevent['short_name_en'];
			$new_league_insert['shortname_zh']	= $old_matchevent['short_name_zh'];
			$new_league_insert['shortname_zht']	= $old_matchevent['short_name_zht'];
			$new_league_insert['logo']			= $old_matchevent['logo'];
			$new_league_insert['created_at']	= $date;
			$new_league_insert['updated_at']	= $date;

			$new_league_id = $dbc->insert('league', $new_league_insert);
			
			$new_old_matchevent_map[$old_matchevent['id']] = $new_league_id;

			unset($new_league_insert);
		}
		
		foreach($team as $old_team){
			$new_team_insert['category_id']		= $category_id;
			$new_team_insert['league_id']		= isset($new_old_matchevent_map[$old_team['matchevent_id']]) ? $new_old_matchevent_map[$old_team['matchevent_id']] : 0;
			$new_team_insert['name_en']			= $old_team['name_en'];
			$new_team_insert['name_zh']			= $old_team['name_zh'];
			$new_team_insert['name_zht']		= $old_team['name_zht'];
			$new_team_insert['shortname_en']	= $old_team['short_name_en'];
			$new_team_insert['shortname_zh']	= $old_team['short_name_zh'];
			$new_team_insert['shortname_zht']	= $old_team['short_name_zht'];
			$new_team_insert['logo']			= $old_team['logo'];
			$new_team_insert['created_at']		= $date;
			$new_team_insert['updated_at']		= $date;

			$dbc->insert('team', $new_team_insert);

			unset($new_team_insert);
		}
		
		unset($team);
		unset($country);
		unset($matchevent);
		unset($area);
		unset($new_old_area_map);
		unset($new_old_country_map);
		unset($new_old_matchevent_map);

	}



	
?>