<?php

	//patch season_id into event table
	//run this in the root directory

	include('include/shared_function.inc.php');
	include('config/app.config.inc.php');
	include('config/config.'.SERVER_STATE.'.inc.php');
	include('include/db.inc.php');
	include('model/event.php');
	include('model/season_matches.php');
	include('model/season_list.php');	

	$event = new event();
	$season_matches = new season_matches();
	$season_list = new season_list();

	$date = date("Y-m-d H:i:s");
	$game_season = "2020/07-2020/12";

	$event_result = $event->get();
	foreach($event_result as $eventObj){
		if($eventObj->category_id == 1 || $eventObj->category_id == 2){
			$season_result = $season_matches->where('league_id', $eventObj->league_id)->where('match_time', strtotime($eventObj->match_at), '>=')->orderBy('match_time', 'asc')->getOne();
			$season_id = $season_result->season_id;
		}
		
		if($eventObj->category_id == 4){
			$season_id = $season_list->where('league_id', $eventObj->league_id)->where('current', 1)->getValue('season_id');
			
			if(!$season_id){
				$season_list->isNew = true;
				$season_list->created_at = $date;
				$season_list->category_id = $eventObj->category_id;
				$season_list->league_id	= $eventObj->league_id;
				$season_list->season = $game_season;
				$season_list->current = 1;
				$season_id = $season_list->save();
				
				$season_list->updateCustom(array("season_id" => $season_id), array("id" => $season_id));
			}
		}
		
		if($season_id > 0){
			$eventObj->isNew = false;
			$eventObj->season_id = $season_id;
			$eventObj->save();
		}
	}
	
?>