<?php
	//*/1 * * * * /usr/local/php/bin/php season_matches_live_update.php
	include(__DIR__.'/include/config.php');
	include(__DIR__.'/include/function.php');
	include(__DIR__.'/../../include/shared_function.inc.php');
	include(__DIR__.'/../../config/config.localhost.inc.php');
	include(__DIR__.'/../../include/db.inc.php');
	include(__DIR__.'/../../model/season_matches.php');

	$season_matches	= new season_matches();

	$date = date('Y-m-d H:i:s');

	$url = "{$api_urls['basketball_detail_live']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	foreach ($result as $key => $value) {
		$match_id = $value['id'];
		// if (isset($value['tlive'])) {
		// 	foreach ($value['tlive'] as $tlive_key => $tlive_value) {
		// 		foreach ($tlive_value as $detail_key => $detail_value) {
		// 			$data = array();
		// 			//sample:
		// 			//"1^12:00^1^0^0-0^特里斯坦·汤普森 vs. 尼古拉·武切维奇 (乔纳森·艾萨克 得到球)^11362,11358,12353^0^0^0^0,0",
		// 			//"5^11:45^1^0^0-0^阿隆·戈登 两分投篮不中^12051^1^0^0^25,8",
		// 			//_^时间^主客队（0-中立，1-主队，2-客队）^0^客队比分-主队比分^说明^____

		// 			preg_match_all("/(\d+)\^([\s\S]*?)\^(\d+)\^(\d+)\^(\d+)-(\d+)\^([\s\S]*?)\^(\d+),(\d+),(\d+)\^(\d+)\^(\d+)\^(\d+)\^(\d+),(\d+)$/", $detail_value, $match);
				
		// 			$season_matchesObj	= $season_matches->where('match_id', $match_id)->getOne();
		// 			if($season_matchesObj){
		// 				$season_matchesObj->updated_at = $date;
		// 				$season_matchesObj->home_score	= $match[6][0];
		// 				$season_matchesObj->away_score	= $match[5][0];
		// 				$season_matchesObj->save();
		// 			}
		// 		}
		// 	}
		// }

		if (isset($value['score'])) {
			$home_score = 0;
			$away_score = 0;
			if (isset($value['score'][3][0])) 
				$home_score += intval($value['score'][3][0]);
			
			if (isset($value['score'][3][1])) 
				$home_score += intval($value['score'][3][1]);
			
			if (isset($value['score'][3][2])) 
				$home_score += intval($value['score'][3][2]);
			
			if (isset($value['score'][3][3])) 
				$home_score += intval($value['score'][3][3]);
			
			if (isset($value['score'][3][4])) 
				$home_score += intval($value['score'][3][4]);
			
			if (isset($value['score'][3][5]))
				$home_score += intval($value['score'][3][5]);
			if (isset($value['score'][3][6]))
				$home_score += intval($value['score'][3][6]);
			if (isset($value['score'][3][7]))
				$home_score += intval($value['score'][3][7]);
			if (isset($value['score'][3][8]))
				$home_score += intval($value['score'][3][8]);
			if (isset($value['score'][3][9]))
				$home_score += intval($value['score'][3][9]);

			if (isset($value['score'][4][0])) 
				$away_score += intval($value['score'][4][0]);
			
			if (isset($value['score'][4][1])) 
				$away_score += intval($value['score'][4][1]);
			
			if (isset($value['score'][4][2])) 
				$away_score += intval($value['score'][4][2]);
			
			if (isset($value['score'][4][3])) 
				$away_score += intval($value['score'][4][3]);
			
			if (isset($value['score'][4][4])) 
				$away_score += intval($value['score'][4][4]);
			
			if (isset($value['score'][4][5]))
				$away_score += intval($value['score'][4][5]);
			if (isset($value['score'][4][6]))
				$away_score += intval($value['score'][4][6]);
			if (isset($value['score'][4][7]))
				$away_score += intval($value['score'][4][7]);
			if (isset($value['score'][4][8]))
				$away_score += intval($value['score'][4][8]);
			if (isset($value['score'][4][9]))
				$away_score += intval($value['score'][4][9]);

			$season_matchesObj	= $season_matches->where('match_id', $match_id)->getOne();
			if($season_matchesObj){
				$season_matchesObj->updated_at = $date;
				$season_matchesObj->home_score	= $home_score;
				$season_matchesObj->away_score	= $away_score;
				$season_matchesObj->save();
			}

		}
	}

	$url = "{$api_urls['soccer_match_detail_live']}?user={$user}&secret={$secret}";
	$response = httpGet($url);
	$result = json_decode($response, true);

	foreach ($result as $key => $value) {
		$match_id = $value['id'];
		// if(isset($value['incidents'])){
		// 	$table = 'soccer_detail_live_incidents';
		// 	foreach ($value['incidents'] as $incident_key => $incident_value) {
		// 		$season_matchesObj	= $season_matches->where('match_id', $match_id)->getOne();
		// 		if($season_matchesObj){
		// 			$season_matchesObj->updated_at = $date;
		// 			$season_matchesObj->home_score	= $incident_value['home_score'];
		// 			$season_matchesObj->away_score	= $incident_value['away_score'];
		// 			$season_matchesObj->save();
		// 		}
		// 	}
		// }
		if(isset($value['score'])){
			$home_score = 0;
			$away_score = 0;
			$score_value = $value['score'];
			if (isset($score_value[2][0]))
				$home_score += intval($score_value[2][0]);
			if (isset($score_value[2][5]))
				$home_score += intval($score_value[2][5]);
			if (isset($score_value[2][6]))
				$home_score += intval($score_value[2][6]);
			

			if (isset($score_value[3][0]))
				$away_score += intval($score_value[3][0]);
			if (isset($score_value[3][5]))
				$away_score += intval($score_value[3][5]);
			if (isset($score_value[3][6]))
				$away_score += intval($score_value[3][6]);

			$season_matchesObj	= $season_matches->where('match_id', $match_id)->getOne();
			if($season_matchesObj){
				$season_matchesObj->updated_at = $date;
				$season_matchesObj->home_score	= $home_score;
				$season_matchesObj->away_score	= $away_score;
				$season_matchesObj->save();
			}
		}
	}

?>