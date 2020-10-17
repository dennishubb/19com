<?php

    include_once('model/result.php');
	include_once('model/team.php');
	include_once('model/event.php');
	include_once('model/prediction_rate.php');
	include_once('model/credit.php');
	include_once('model/user.php');
	include_once('model/level.php');
	include_once('model/promotion_redeem.php');
	include_once('model/promotion.php');
	include_once('model/prediction_stats.php');
	include_once('model/season_matches.php');

	$input = $params;

    do{
        
        $result = new result();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $result->adminFields : $result->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $resultObj = $result->byId($params['id'], $selectFields);
                    $return = $resultObj->data;
                    if($resultObj->team){
                        $return['team_data']    = $resultObj->team->data;
						unset($return['team']);
                    }
					
                    $response['data'] = $return;
                }else{
                    $limit = null;
                    if(array_key_exists('limit', $params)){
                        $limit = $params['limit'];
                        if(array_key_exists('page_number', $params)){
                            $pageNumber = $params['page_number'] ? $params['page_number'] : 1;
                            $pageLimit = $limit;
                            $limit = array(($pageNumber - 1) * $pageLimit, $pageLimit);
                            $start_count = ($pageNumber - 1) * $pageLimit;
                        }
                    }
                    
                    if(array_key_exists('search', $params)){
                        foreach($params['search'] as $key => $value){
							if(strlen($value) < 0){
								continue;
							}
							
                            $result->where($result->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $result->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$result->where($field, $value, $operator);
                        }
                    }
                    
					$result_copy = $result->db->copy();
                    $res = $result->get($limit, $selectFields);
					$result->db = $result_copy;
					$totalRecords = $result->getValue("count(result.id)");
                    
                    $result_array = array();
                    if($res){
                        foreach($res as $resultObj){
                            $return = $resultObj->data;
                            if($resultObj->team){
								$return['team_data']    = $resultObj->team->data;
								unset($return['team']);
							}
							
                            $result_array[] = $return;
                        } 
                    }

                    $response['data']   = $result_array;
                    
                    if(array_key_exists('page_number', $params)){
                        $response['totalPage']      = ceil($totalRecords / $limit[1]);
                        $response['pageNumber']     = $pageNumber;
                        $response['totalRecord']    = $totalRecords;
                        $response['numRecord']      = $pageLimit;
                        $response['fromPage']       = $pageNumber > 1?($pageNumber - 1)*$pageLimit:"1";
                        $response['toPage']         = ($pageNumber *$pageLimit) > $totalRecords?$totalRecords:($pageNumber *$pageLimit);
                    }
                        
                }

                break;

            case 'POST':
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }				
				
				$resultObj = $result->where('event_id', $params['event_id'])->getOne();
                if($resultObj){
					responseFail($error, "Already created result for this event");
					break;
				}
				
                foreach($params as $key => $value){
                    $result->$key = $value;
                }

                $result->created_at 	= $date;
                $result->updated_at 	= $date;

                $id = $result->save();
                if(!$id){
                    responseFail($error, $result->getLastError());
                    break;
                }
				
				//update event ended
				$event 		= new event();
				$eventObj 	= $event->byId($result->event_id);
				$eventObj->ended = 1;
				$eventObj->save();
				
				$category_id	= $eventObj->category_id;
				$league_id		= $eventObj->league_id;
				$season_id		= $eventObj->season_id;
				
//				$new_season = false;
//				
//				//category_id 1 = Soccer, 2 = Basketball, 4 = Gaming/ESports
//				if($eventObj->category_id == 1 || $eventObj->category_id == 2){
//					$season_matches = new season_matches();
//					$season_id		= $season_matches->where('league_id', $league_id)->where('match_at', strtotime($eventObj->match_at), '>=')->orderBy('match_time', 'asc')->getValue('season_id');
//
//					if($season_id != $eventObj->season_id){
//						$new_season = true;
//					}
//				}else if($eventObj->category_id == 4){
//					$previous_event = $event->where('league_id', $league_id)->orderBy('id', 'desc')->get(array(1,1));
//					if($previous_event){
//						$previous_event = $previous_event[0];
//						$previous_event_month = date("m", strtotime($previous_event->match_at));
//						$current_event_month = date("m", strtotime($eventObj->match_at));
//						
//						if($previous_event_month > 6){
//							if($current_event_month >= 1 && $current_event_month <= 6){
//								$new_season = true;
//							}
//						}else{
//							if($current_event_month >= 7 && $current_event_month <= 12){
//								$new_season = true;
//							}
//						}
//					} 
//				}

				
				//update predictions, calculate and distribute points
				$bet_type_array = array();
				$sorting_array	= array();
				
				//gaming only single bet type
				if($category_id == 4){
					$bet_type_array = array('single');
					$sorting_array  = array('single' => '1',
									    	'total' => '2');
				}else{
					$bet_type_array = array('handicap', 'over_under', 'single');
					$sorting_array  = array('handicap' => '1',
											'over_under' => '2', 
											'single' => '3',
											'total' => '4');
				}	
				
				$prediction 		= new prediction();
				$prediction_rate	= new prediction_rate();
				$user				= new user();
				$credit				= new credit();
				$event				= new event();
				
				$payout_id 	= $user->where('username', 'payout')->getValue('id');
				$credit_id	= $credit->where('prediction_payout', '1')->getValue('id');
				$voucher_id	= $credit->where('name', 'voucher')->getValue('id');
				$this_month		= date('n');
				$this_year		= date('Y');
				
				$prediction_results = $prediction->where('event_id', $result->event_id)->where('status', 'predicted')->get();
				
				if($prediction_results){
					$promotion		= new promotion();
					$promotion_id	= $promotion->where('name', 'first time prediction')->where('disabled', 0)->where('start_at', $date, '<')->where('end_at', $date, '>')->getValue('id');
					
					$promotion_redeem	= new promotion_redeem();
					
					foreach($prediction_results as $predictionObj){
						$first_time_prediction	= false;
						
						if($promotion_id){
							$promotion_redeem_id = $promotion_redeem->where('promotion_id', $promotion_id)->where('user_id', $predictionObj->user_id)->getValue('id');
							if(!$promotion_redeem_id){
								$first_time_prediction	= true;
								$promotion_redeem->promotion_id	= $promotion_id;
								$promotion_redeem->user_id		= $predictionObj->user_id;
								$promotion_redeem->created_at	= $date;
								$promotion_redeem->status		= 'approve';
								$promotion_redeem->save();
							}
						}
						
						$win_amount = 0;
						$bet_count = array();
						$bet_count['total']['win_count'] 	= 0;
						$bet_count['total']['lose_count']	= 0;
						foreach($bet_type_array as $bet_type){
							if(!isset($bet_count[$bet_type]['win_count'])) $bet_count[$bet_type]['win_count'] = 0;
							if(!isset($bet_count[$bet_type]['lose_count'])) $bet_count[$bet_type]['lose_count'] = 0;
							getPredictionResult($predictionObj->{$bet_type.'_home'}, $predictionObj->{$bet_type.'_away'}, $predictionObj->{$bet_type.'_tie'}, $result->{$bet_type.'_home'}, $result->{$bet_type.'_away'}, $result->{$bet_type.'_tie'}, $bet_type, $predictionObj, $win_amount, $bet_count, $first_time_prediction);
						}

						$predictionObj->win_amount = $win_amount;
						$predictionObj->updated_at = $date;
						$predictionObj->win_at 	   = $date;

						if($predictionObj->win_amount < 0){
							$balance = getBalance($predictionObj->user_id, $credit_id);

							if(abs($predictionObj->win_amount) > $balance){
								$predictionObj->win_amount = -$balance;
							}

							insertTransaction($predictionObj->user_id, abs($predictionObj->win_amount), $id, $credit_id, $predictionObj->user_id, $payout_id, $date, "prediction result");
						}else{
							insertTransaction($predictionObj->user_id, abs($predictionObj->win_amount), $id, $credit_id, $payout_id, $predictionObj->user_id, $date, "prediction result");
						}

						foreach($bet_count as $key => $inner_count){
							$prediction_rateObj = $prediction_rate->where('user_id', $predictionObj->user_id)->where('league_id', $league_id)->where('category_id', $category_id)->where('type', $key)->where('month', $this_month)->where('year', $this_year)->getOne();

							if($prediction_rateObj){

							}else{
								$prediction_rateObj					= new prediction_rate();
								$prediction_rateObj->win_count		= 0;
								$prediction_rateObj->lose_count		= 0;
								$prediction_rateObj->total_count	= 0;
								$prediction_rateObj->rate			= 0;
								$prediction_rateObj->total_points	= 0;
								$prediction_rateObj->user_id		= $predictionObj->user_id;
								$prediction_rateObj->category_id	= $category_id;
								$prediction_rateObj->league_id		= $league_id;
								$prediction_rateObj->season_id		= $season_id;
								$prediction_rateObj->type			= $key;
								$prediction_rateObj->sorting		= $sorting_array[$key];
								$prediction_rateObj->created_at	 	= $date;
								$prediction_rateObj->month			= $this_month;
								$prediction_rateObj->year			= $this_year;
								
								$prediction_rateObj->isNew			= true;
							}
							
							//check if event season id exist in prediction_rate, if dont exist it's a new season
							$season_prediction_rateObj = $prediction_rate->where('user_id', $predictionObj->user_id)->where('league_id', $league_id)->where('category_id', $category_id)->where('type', $key)->where('season_id', $season_id)->orderBy('id', 'desc')->getOne();
							if($season_prediction_rateObj){
								$prediction_rateObj->season_win_count = $season_prediction_rateObj->season_win_count;
								$prediction_rateObj->season_lose_count = $season_prediction_rateObj->season_lose_count;
								$prediction_rateObj->season_total_count = $season_prediction_rateObj->season_total_count;
							}else{
								$prediction_rateObj->season_win_count = 0;
								$prediction_rateObj->season_lose_count = 0;
								$prediction_rateObj->season_total_count = 0;
								$prediction_rateObj->season_id = $season_id;
							}
							
							$prediction_rateObj->win_count		= $prediction_rateObj->win_count + $inner_count['win_count'];
							$prediction_rateObj->lose_count		= $prediction_rateObj->lose_count + $inner_count['lose_count'];
							$prediction_rateObj->total_count	= $prediction_rateObj->win_count + $prediction_rateObj->lose_count;
							if($prediction_rateObj->total_count == 0){
								$prediction_rateObj->rate		= 0;
							}else{
								$prediction_rateObj->rate		= ($prediction_rateObj->win_count/$prediction_rateObj->total_count) * 100;
							}
							
							$prediction_rateObj->season_win_count	= $prediction_rateObj->season_win_count + $inner_count['win_count'];
							$prediction_rateObj->season_lose_count	= $prediction_rateObj->season_lose_count + $inner_count['lose_count'];
							$prediction_rateObj->season_total_count	= $prediction_rateObj->season_win_count + $prediction_rateObj->season_lose_count;
							if($prediction_rateObj->season_total_count == 0){
								$prediction_rateObj->season_rate		= 0;
							}else{
								$prediction_rateObj->season_rate		= ($prediction_rateObj->season_win_count/$prediction_rateObj->season_total_count) * 100;
							}
							
							$prediction_rateObj->total_points	= $prediction_rateObj->total_points + $predictionObj->win_amount;
							
//							if($prediction_rateObj->rate >= 50){
//								$prediction_rateObj->qualified	= 1;
//							}else{
//								$prediction_rateObj->qualified	= 0;
//							}
							
							$prediction_rateObj->updated_at 	= $date;
							$prediction_rateObj->save();
							$predictionObj->save();
							
							if($key == 'total'){
								$user_prediction_rate_count = $prediction_rate->where('user_id', $predictionObj->user_id)->where('category_id', $category_id)->where('league_id', $league_id)->where('type', $key)->getValue('count(id)');
								$user_prediction_rate = $prediction_rate->where('user_id', $predictionObj->user_id)->where('category_id', $category_id)->where('league_id', $league_id)->where('type', $key)->getValue('SUM(rate)');
								
								$total_win_rate	= $user_prediction_rate/$user_prediction_rate_count;
								
								$prediction_stats = new prediction_stats();
								$prediction_stats->where('month', $this_month)->where('year', $this_year)->where('user_id', $predictionObj->user_id)->where('category_id', $category_id);
								$prediction_stats->updateWhere(array('win_rate' => $prediction_rateObj->rate, 'total_win_rate' => $total_win_rate));
								
								$user	= new user();
								$user->updateCustom(array('win_rate' => $total_win_rate), array('id' => $predictionObj->user_id));
							}

						}

					}
				}
                
                $response['id'] = $id;
				$response['redirect'] = true;

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $result = $result->byId($params['id']);
                
                foreach($params as $key => $value){
                    $result->$key = $value;
				}

                $result->updated_at = $date;
                $result->save();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;

                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $result = $result->byId($params['id']);
                $result->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					if(array_key_exists('id', $data)){
						$result = $result->byId($data['id']);
					}else{
						$result->isNew = true;
						
						$result->created_at = $date;
					}
					
					if($action == 'delete'){
						$result->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$result->$key = $value;
					}

					$result->updated_at = $date;
					$id = $result->save();
				}
				break;
        }
        
    } while(0);

    function validate($params = array(), $additional_fields = array()){
        $validator = new Validator;

        // fields info container
        $fields = array();
        $required_keys = array('id');

        if($params){
            foreach($params as $key => $value){
                $required = false;

                $fields[] = array('index' => $key, 'label' => $key, 'required' => $required);
            }
        }
        
        $fields = array_merge($fields, $additional_fields);

        $validator->formHandle($fields);
        $problem = $validator->getErrors();
        $cv = $validator->escape_val(); // get the form values

        if ($problem) {
            return $problem;
        }
        
        return $cv;
    }

	function getPredictionResult($home, $away, $tie, $home_result, $away_result, $tie_result, $type, &$predictionObj, &$win_amount, &$bet_count, &$first_time_prediction){
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
			}
		}
	}
	
	$response['extra'] = $input;

?>