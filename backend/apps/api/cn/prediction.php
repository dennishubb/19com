<?php

    include_once('model/event.php');
	include_once('model/category.php');
	include_once('model/league.php');
    include_once('model/user.php');
	include_once('model/transaction.php');
	include_once('model/balance.php');
	include_once('model/prediction_top_ten.php');
	include_once('model/top_ten.php');
	include_once('model/prediction_stats.php');

	$input = $params;

    do{
        
        $prediction = new prediction();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $prediction->adminFields : $prediction->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $predictionObj = $prediction->with('user')->with('event')->byId($params['id'], $selectFields);
                    $return = $predictionObj->data;
                    if( $predictionObj->user){
                        $return['user_data']     = $predictionObj->user->data;
                    }
					if( $predictionObj->event){
                        $eventObj    	= $predictionObj->event;
						$eventObj		= $eventObj->with('league')->with('category')->byId($eventObj->id);
						$return['league_data'] = $eventObj->league->data;
						$return['category_data'] = $eventObj->category->data;
						$return['result_data'] = $eventObj->result[0]->data;
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
						if(isset($params['search']['event_id'])){
							$event = new event();
							$event = $event->byId($params['search']['event_id']);
							$response['event_data'] = $event->data;
						}
						
                        foreach($params['search'] as $key => $value){
							if($key == 'date_from'){
								$event->where($event->dbTable.".created_at", $value, '>=');
								continue;
							}
							
							if($key == 'date_to'){
								$event->where($event->dbTable.".created_at", $value." 23:59:59", '<=');
								continue;
							}
							
							if(strlen($value) > 0){
                            	$prediction->where($key, "%$value%", 'LIKE');
							}
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$prediction->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction->orderBy($data['field'], $data['sort']);
							}
						}
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(is_string($value) && strlen($value) == 0){
								continue;
							}else if(is_array($value) && count($value) == 0){
								continue;
							}
							
							$prediction->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_copy = $prediction->db->copy();
                    $result = $prediction->with('user')->with('event')->get($limit, $selectFields);
					$prediction->db = $prediction_copy;
					$totalRecords = $prediction->with('user')->with('event')->getValue("count(prediction.id)");
                    
                    $prediction_array = array();
                    if($result){
                        foreach($result as $predictionObj){
                            $return = $predictionObj->data;
                            if( $predictionObj->user){
								$return['user_data']     = $predictionObj->user->data;
								unset($return['user']);
							}
							if($predictionObj->event){
								$eventObj    	= $predictionObj->event;
								$event_data 	= $eventObj->data;
								$eventObj		= $eventObj->with('league')->with('category')->with('home_team')->with('away_team')->byId($eventObj->id);
								$return['event_data']		= $event_data;
								$return['league_data'] 		= $eventObj->league->data;
								$return['category_data'] 	= $eventObj->category->data;
								$return['home_team_data'] 	= $eventObj->home_team->data;
								$return['away_team_data'] 	= $eventObj->away_team->data;
								if($eventObj->result){
									$return['result_data'] 		= $eventObj->result[0]->data;
								}
								unset($return['event']);
							}
                            $prediction_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_array;
                    
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
				
				//check points balance, event prediction_end_at
				$prediction_id = $prediction->where('event_id', $params['event_id'])->where('user_id', $params['user_id'])->getValue('id');
                if($prediction_id){
					responseFail($error, "已经预测过了");
                    break;
				}
				
				$event 		= new event();
				$eventObj 	= $event->where('id', $params['event_id'])->getOne();
				if(strtotime($eventObj->prediction_end_at) < time()){
					responseFail($error, "达到预测期限");
                    break;
				}
				
				if(strtotime($eventObj->match_at) < time()){
					responseFail($error, "投注最后期限已过");
                    break;
				}
				
				updatePredictionCount($prediction, $params, $eventObj);
				
                foreach($params as $key => $value){
                    $prediction->$key = $value;
                }

                $prediction->created_at 	= $date;
                $prediction->updated_at 	= $date;

                $id = $prediction->save();
                if(!$id){
                    responseFail($error, $prediction->getLastError());
                    break;
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
				$event 			= new event();
				$eventObj 		= $event->where('id', $prediction->event_id)->getOne();
				$category_id	= $eventObj->category_id;
				$league_id		= $eventObj->league_id;
				
				$top_ten = new top_ten();
				$top_tenObj = $top_ten->where('user_id', $prediction->user_id)->where('category_id', $category_id)->where('league_id', $league_id)->getValue('id');
				if($top_tenObj){
					$prediction_top_ten = new prediction_top_ten();
					$prediction_top_tenObj	= $prediction_top_ten->where('user_id', $prediction->user_id)->where('event_id', $prediction->event_id)->getValue('id');
					
					if(!$prediction_top_tenObj){
						$prediction_top_ten->user_id 	= $prediction->user_id;
						$prediction_top_ten->event_id 	= $prediction->event_id;
						$prediction_top_ten->created_at = $date;
						$prediction_top_ten_id	= $prediction_top_ten->save();
						
						//auto unlock for top ten user himself
						$prediction_top_ten_unlock	= new prediction_top_ten_unlock();
						$prediction_top_ten_unlock->user_id		= $prediction->user_id;
						$prediction_top_ten_unlock->event_id	= $prediction->event_id;
						$prediction_top_ten_unlock->prediction_top_ten_id = $prediction_top_ten_id;
						$prediction_top_ten_unlock->save();
					}
				}
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $prediction = $prediction->byId($params['id']);
				
				if($prediction->status == 'predicted'){
					responseFail($error, "此赛事已送出，无法修改", 406);
                    break;
				}
				
				$event 		= new event();
				$eventObj 	= $event->where('id', $prediction->event_id)->getOne();
				if(strtotime($eventObj->prediction_end_at) < time()){
					responseFail($error, "达到预测期限");
                    break;
				}
				
				if(strtotime($eventObj->match_at) < time()){
					responseFail($error, "投注最后期限已过");
                    break;
				}
				
				updatePredictionCount($prediction, $params, $eventObj);
                
                foreach($params as $key => $value){
                    $prediction->$key = $value;
				}

                $prediction->updated_at = $date;			
                $prediction->save();
                
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
                $prediction = $prediction->byId($params['id']);
				
				$event 		= new event();
				$eventObj 	= $event->where('id', $prediction->event_id)->getOne();
				if(strtotime($eventObj->prediction_end_at) < time()){
					responseFail($error, "prediction deadline reached");
                    break;
				}
				
				updatePredictionCount($prediction, $params, $eventObj);
				
                $prediction->delete();
				
				$prediction_top_ten 	= new prediction_top_ten();
				$prediction_top_tenObj  = $prediction_top_ten->where('user_id', $prediction->user_id)->where('event_id', $prediction->event_id)->getOne();
				
				if(!$prediction_top_tenObj){
					
				}else{
					$prediction_top_tenObj->delete();
				}
				
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
						$prediction = $prediction->byId($data['id']);
					}else{
						$prediction->isNew = true;
						
						$prediction->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction->$key = $value;
					}

					$prediction->updated_at = $date;
					$id = $prediction->save();
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

	function updatePredictionCount($prediction, $params, $eventObj){
		$date			= date("Y-m-d H:i:s");
		$current_month 	= date('n');
		$current_year	= date('Y');
		$bet_type		= array('handicap', 'over_under', 'single');
		
		$previous_count = 0;
		$current_count = 0;

		if(isset($prediction->data)){
			foreach($bet_type as $type){
				if(isset($prediction->data[$type.'_home']) && $prediction->data[$type.'_home'] == 1) $previous_count++;
				if(isset($prediction->data[$type.'_away']) && $prediction->data[$type.'_away'] == 1) $previous_count++;
				if(isset($prediction->data[$type.'_tie']) && $prediction->data[$type.'_tie'] == 1) $previous_count++;
			}
		}

		foreach($bet_type as $type){
			if(isset($params[$type.'_home']) && $params[$type.'_home'] == 1) $current_count++;
			if(isset($params[$type.'_away']) && $params[$type.'_away'] == 1) $current_count++;
			if(isset($params[$type.'_tie']) && $params[$type.'_tie'] == 1) $current_count++;
		}

		$prediction_count	= $current_count - $previous_count;
		
		//if event prediction ends at another month/year
		if(date('n', strtotime($eventObj->prediction_end_at)) != $current_month || date('Y', strtotime($eventObj->prediction_end_at)) != $current_year){
			$current_month 	= date('n', strtotime($eventObj->prediction_end_at));
			$current_year	= date('Y', strtotime($eventObj->prediction_end_at));
		}

		$prediction_stats			= new prediction_stats();
		$prediction_statsObj		= $prediction_stats->where('user_id', isset($params['user_id']) ? $params['user_id'] : $prediction->user_id)->where('league_id', $eventObj->league_id)->where('month', $current_month)->where('year', $current_year)->where('category_id', $eventObj->category_id)->getOne();

		if(!$prediction_statsObj){
			$event	= new event();
			$total_event = $event->where('category_id', $eventObj->category_id)->where('league_id', $eventObj->league_id)->where('MONTH(prediction_end_at)', $current_month)->where('YEAR(prediction_end_at)', $current_year)->getValue('count(id)');
			
			if($eventObj->category_id == 1 || $eventObj->category_id == 2){
				$total_event = $total_event * 3;
			}

			$top_ten				= new top_ten();
			$top_ten_total_count	= $top_ten->where('category_id', $eventObj->category_id)->where('league_id', $eventObj->league_id)->groupBy('created_at')->getValue('count(id)');

			$top_ten_count			= $top_ten->where('user_id', isset($params['user_id']) ? $params['user_id'] : $prediction->user_id)->where('category_id', $eventObj->category_id)->getValue('count(id)');

			$prediction_stats->user_id					= isset($params['user_id']) ? $params['user_id'] : $prediction->user_id;
			$prediction_stats->category_id				= $eventObj->category_id;
			$prediction_stats->league_id				= $eventObj->league_id;
			$prediction_stats->prediction_count			= $prediction_count;
			$prediction_stats->prediction_total_count	= $total_event;
			$prediction_stats->win_rate					= 0;
			$prediction_stats->top_ten_count			= $top_ten_count ? $top_ten_count : 0;
			$prediction_stats->top_ten_total_count		= $top_ten_total_count ? $top_ten_total_count : 0;
			$prediction_stats->month					= $current_month;
			$prediction_stats->year						= $current_year;
			$prediction_stats->created_at				= $date;
			$prediction_stats->save();
		}else{
			$prediction_statsObj->prediction_count	= $prediction_statsObj->prediction_count + $prediction_count;
			$prediction_statsObj->updated_at		= $date;
			$prediction_statsObj->save();
		}
	}

	$response['extra'] = $input;

?>