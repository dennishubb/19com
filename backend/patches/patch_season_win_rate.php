<?php

	//patch prediction_rate season_win_rate and win/lose/total count
	//run this in the root directory
	include('include/shared_function.inc.php');
	include('config/app.config.inc.php');
	include('config/config.'.SERVER_STATE.'.inc.php');
	include('include/db.inc.php');
	include('model/event.php');
	include('model/prediction_rate.php');
	include('model/prediction.php');
	include('model/result.php');
	include('model/promotion_redeem.php');
	include('model/season_list.php');

	$event = new event();
	$prediction_rate = new prediction_rate();
	$prediction = new prediction();
	$result = new result();
	$promotion_redeem = new promotion_redeem();
	$season_list = new season_list();

	$date = date('Y-m-d H:i:s');

	$prediction_rate_result = $prediction_rate->orderBy('month', 'asc')->get();

	$first_month = true;
	$previous_value = array();

	foreach($prediction_rate_result as $prediction_rateObj){
		
		$season_id = $season_list->where('league_id', $prediction_rateObj->league_id)->where('current', 1)->getValue('season_id');
		
		if(!$current_month) $current_month = $prediction_rateObj->month;
		if($prediction_rateObj->month > $current_month){
			$current_month = $prediction_rateObj->month;
			$first_month = false;
		}
		
		if(!$first_month){
			$prediction_rateObj->season_win_count = $previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_win_count'];
			$prediction_rateObj->season_lose_count = $previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_lose_count'];
			$prediction_rateObj->season_total_count = $previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_total_count'];
		}
		
		$prediction_rateObj->season_win_count += $prediction_rateObj->win_count;
		$prediction_rateObj->season_lose_count += $prediction_rateObj->lose_count;
		$prediction_rateObj->season_total_count += $prediction_rateObj->total_count;
		$prediction_rateObj->season_total_count	= $prediction_rateObj->season_win_count + $prediction_rateObj->season_lose_count;
		if($prediction_rateObj->season_total_count == 0){
			$prediction_rateObj->season_rate		= 0;
		}else{
			$prediction_rateObj->season_rate		= ($prediction_rateObj->season_win_count/$prediction_rateObj->season_total_count) * 100;
		}
		
		$previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_win_count'] = $prediction_rateObj->season_win_count;
		$previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_lose_count'] = $prediction_rateObj->season_lose_count;
		$previous_value[$prediction_rateObj->user_id][$prediction_rateObj->league_id][$prediction_rateObj->type]['season_total_count'] = $prediction_rateObj->season_total_count;
		
		$prediction_rateObj->season_id = $season_id;
		$prediction_rateObj->updated_at = $date;
		$prediction_rateObj->save();
	}

//	$first_time_user = array();
//
//	$user_promo_redeem = array();
//	$promotion_redeem_result = $promotion_redeem->where('promotion_id', 2)->get();
//	foreach($promotion_redeem_result as $promotion_redeemObj){
//		$user_promo_redeem[$promotion_redeemObj->user_id] = $promotion_redeemObj->created_at;
//	}
//
//	$event_result = $result->orderBy('created_at', 'asc')->get();
//	foreach($event_result as $resultObj){
//		
//		$eventObj = $event->byId($resultObj->event_id);
//		
//		$category_id = $eventObj->category_id;
//		$league_id 	= $eventObj->league_id;
//		$season_id	= $eventObj->season_id;
//		
//		$bet_type_array = array();
//		$sorting_array	= array();
//
//		if($category_id == 4){
//			$bet_type_array = array('single');
//			$sorting_array  = array('single' => '1',
//									'total' => '2');
//		}else{
//			$bet_type_array = array('handicap', 'over_under', 'single');
//			$sorting_array  = array('handicap' => '1',
//									'over_under' => '2', 
//									'single' => '3',
//									'total' => '4');
//		}
//		
//		$this_month = date('m', strtotime($eventObj->match_at));
//		$this_year = date('Y', strtotime($eventObj->match_at));
//		
//		$prediction_results = $prediction->where('event_id', $resultObj->event_id)->where('status', 'predicted')->get();
//				
//		if($prediction_results){
//			foreach($prediction_results as $predictionObj){
//				$first_time_prediction	= true;
//				
//				if(in_array($predictionObj->user_id, $first_time_user)) {
//					$first_time_prediction = false;
//				}
//				
//				if($resultObj->created_at == $user_promo_redeem[$predictionObj->user_id]) {
//					$first_time_prediction = true;
//				}
//
//				$win_amount = 0;
//				$bet_count = array();
//				$bet_count['total']['win_count'] 	= 0;
//				$bet_count['total']['lose_count']	= 0;
//				foreach($bet_type_array as $bet_type){
//					if(!isset($bet_count[$bet_type]['win_count'])) $bet_count[$bet_type]['win_count'] = 0;
//					if(!isset($bet_count[$bet_type]['lose_count'])) $bet_count[$bet_type]['lose_count'] = 0;
//					getPredictionResult($predictionObj->{$bet_type.'_home'}, $predictionObj->{$bet_type.'_away'}, $predictionObj->{$bet_type.'_tie'}, $resultObj->{$bet_type.'_home'}, $resultObj->{$bet_type.'_away'}, $resultObj->{$bet_type.'_tie'}, $bet_type, $predictionObj, $win_amount, $bet_count, $first_time_prediction, $first_time_user);
//				}
//				
//				foreach($bet_count as $key => $inner_count){
//					$prediction_rateObj = $prediction_rate->where('user_id', $predictionObj->user_id)->where('league_id', $league_id)->where('category_id', $category_id)->where('type', $key)->where('month', $this_month)->where('year', $this_year)->getOne();
//
//					if(!$prediction_rateObj){
//						echo $predictionObj->id." - ".$predictionObj->user_id." - ".$eventObj->id."\n";
//						continue;
//					}
//
//					$season_prediction_rateObj = $prediction_rate->where('user_id', $predictionObj->user_id)->where('league_id', $league_id)->where('category_id', $category_id)->where('type', $key)->where('season_id', $season_id)->orderBy('id', 'desc')->getOne();
//					if($season_prediction_rateObj){
//						$prediction_rateObj->season_win_count = $season_prediction_rateObj->season_win_count;
//						$prediction_rateObj->season_lose_count = $season_prediction_rateObj->season_lose_count;
//						$prediction_rateObj->season_total_count = $season_prediction_rateObj->season_total_count;
//					}else{
//						$prediction_rateObj->season_win_count = 0;
//						$prediction_rateObj->season_lose_count = 0;
//						$prediction_rateObj->season_total_count = 0;
//					}
//
//					$prediction_rateObj->season_win_count	= $prediction_rateObj->season_win_count + $inner_count['win_count'];
//					$prediction_rateObj->season_lose_count	= $prediction_rateObj->season_lose_count + $inner_count['lose_count'];
//					$prediction_rateObj->season_total_count	= $prediction_rateObj->season_win_count + $prediction_rateObj->season_lose_count;
//					if($prediction_rateObj->season_total_count == 0){
//						$prediction_rateObj->season_rate		= 0;
//					}else{
//						$prediction_rateObj->season_rate		= ($prediction_rateObj->season_win_count/$prediction_rateObj->season_total_count) * 100;
//					}
//					
////					if($prediction_rateObj->user_id == 133){
////						print_r($prediction_rateObj->data);
////					}
//
//					$prediction_rateObj->updated_at 	= $date;
//					$prediction_rateObj->season_id		= $season_id;
//					$prediction_rateObj->save();
//				}
//			}
//		}
//	}

	function getPredictionResult($home, $away, $tie, $home_result, $away_result, $tie_result, $type, &$predictionObj, &$win_amount, &$bet_count, &$first_time_prediction, &$first_time_user){
		if($type != 'single' && ($home == 0 && $away == 0)) return false;
		if($type == 'single' && ($home == 0 && $away == 0 && $tie == 0)) return false;
		
		$win_type = $type.'_win';
		if($home == 1 && $home_result == 1){ $predictionObj->$win_type = 1; $win_amount += 100; $predictionObj->win = 1; $bet_count['total']['win_count']++; $bet_count[$type]['win_count']++; }
		else if($away == 1 && $away_result == 1){ $predictionObj->$win_type = 1; $win_amount += 100; $predictionObj->win = 1; $bet_count['total']['win_count']++; $bet_count[$type]['win_count']++; }
		else if($tie == 1 && $tie_result == 1){ $predictionObj->$win_type = 1; $win_amount += 100; $predictionObj->win = 1; $bet_count['total']['win_count']++; $bet_count[$type]['win_count']++; }
		else { 
			if(!$first_time_prediction){
				$win_amount -= 80; $bet_count['total']['lose_count']++; $bet_count[$type]['lose_count']++; 
			} else{
				$first_time_prediction	= false;
				array_push($first_time_user, $predictionObj->user_id);
			}
		}
	}

?>