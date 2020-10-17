<?php

    include_once('model/category.php');
    include_once('model/league.php');
	include_once('model/team.php');
	include_once('model/chatroom.php');
	include_once('model/message.php');
	include_once('model/prediction.php');
	include_once('model/upload.php');
	include_once('model/prediction_stats.php');
	include_once('model/season_list.php');
	include_once('model/season_matches.php');

	$input = $params;

    do{
        
        $event = new event();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $event->adminFields : $event->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $eventObj = $event->with('category')->with('league')->with('home_team')->with('away_team')->with('home_team_upload')->with('away_team_upload')->byId($params['id'], $selectFields);
                    $return = $eventObj->data;
                    if($eventObj->category){
                        $return['category_data']    = $eventObj->category->data;
						unset($return['category']);
                    }
					if($eventObj->league){
                        $return['league_data']     	= $eventObj->league->data;
						unset($return['league']);
                    }
					if($eventObj->home_team){
                        $return['home_team_data']   = $eventObj->home_team->data;
						unset($return['home_team']);
                    }
					if($eventObj->away_team){
                        $return['away_team_data']   = $eventObj->away_team->data;
						unset($return['away_team']);
                    }	
					if($eventObj->home_team_upload){
                        $return['home_team_upload_data']   = $eventObj->home_team_upload->data;
						unset($return['home_team_upload']);
                    }	
					if($eventObj->away_team_upload){
                        $return['away_team_upload_data']   = $eventObj->away_team_upload->data;
						unset($return['away_team_upload']);
                    }		
					if($eventObj->message){
						$return['comment_count'] = count($eventObj->message);
					}
					if($eventObj->prediction){
						$return['prediction_count'] = isset(array_count_values(array_column($eventObj->prediction, 'status'))['predicted']) ? array_count_values(array_column($eventObj->prediction, 'status'))['predicted'] : 0;
						$return['prediction_win_count'] = isset(array_count_values(array_column($eventObj->prediction, 'win'))[1]) ? array_count_values(array_column($eventObj->prediction, 'win'))[1] : 0; 
					}
					if($eventObj->result){
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
                        foreach($params['search'] as $key => $value){
							if(strlen($value) < 0){
								continue;
							}
							
							if($key == 'date_from'){
								$event->where($event->dbTable.".prediction_end_at", $value, '>=');
								continue;
							}
							
							if($key == 'date_to'){
								$event->where($event->dbTable.".prediction_end_at", $value." 23:59:59", '<=');
								continue;
							}
							
							if (strpos($value, 'id') !== false) {
								echo 'true';
							}
							
                            $event->where($event->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $event->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$event->where($field, $value, $operator);
                        }
                    }
                    
					$event_copy = $event->db->copy();
                    $result = $event->with('category')->with('league')->with('home_team')->with('away_team')->with('home_team_upload')->with('away_team_upload')->get($limit, $selectFields);
					$event->db = $event_copy;
					$totalRecords = $event->with('category')->with('league')->with('home_team')->with('away_team')->with('home_team_upload')->with('away_team_upload')->getValue("count(event.id)");
                    
                    $event_array = array();
                    if($result){
                        foreach($result as $eventObj){
                            $return = $eventObj->data;
                            if($eventObj->category){
								$return['category_data']    = $eventObj->category->data;
								unset($return['category']);
							}
							if($eventObj->league){
								$return['league_data']     	= $eventObj->league->data;
								unset($return['league']);
							}
							if($eventObj->home_team){
								$return['home_team_data']   = $eventObj->home_team->data;
								unset($return['home_team']);
							}
							if($eventObj->away_team){
								$return['away_team_data']   = $eventObj->away_team->data;
								unset($return['away_team']);
							}	
							if($eventObj->home_team_upload){
								$return['home_team_upload_data']   = $eventObj->home_team_upload->data;
								unset($return['home_team_upload']);
							}	
							if($eventObj->away_team_upload){
								$return['away_team_upload_data']   = $eventObj->away_team_upload->data;
								unset($return['away_team_upload']);
							}	
							if($eventObj->message){
								$return['comment_count'] = count($eventObj->message);
							}
							if($eventObj->prediction){
								$return['prediction_count'] = isset(array_count_values(array_column($eventObj->prediction, 'status'))['predicted']) ? array_count_values(array_column($eventObj->prediction, 'status'))['predicted'] : 0;
								$return['prediction_win_count'] = isset(array_count_values(array_column($eventObj->prediction, 'win'))[1]) ? array_count_values(array_column($eventObj->prediction, 'win'))[1] : 0; 
							}
							if($eventObj->result){
								$return['result_data'] = $eventObj->result[0]->data;
							}
                            $event_array[] = $return;
                        } 
                    }

                    $response['data']   = $event_array;
                    
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
				$additional_fields = getEventAdditionalFields($params['category_id']);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
				$chatroom = new chatroom();
				$chatroom->status 		= 'active';
				$chatroom->created_at 	= $date;
				$chatroom->updated_at 	= $date;
				$chatroom->type			= 'event';
				$chatroom_id = $chatroom->save();
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url_home', $params) || array_key_exists('upload_url_away', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
                    $event->$key = $value;
                }
				
				$event->season_id	= getSeasonId($event);
				$event->chatroom_id	= $chatroom_id;
                $event->created_at 	= $date;
                $event->updated_at 	= $date;

                $id = $event->save();
                if(!$id){
					echo "error: ".$event->getLastError();
                    responseFail($error, $event->getLastError());
                    break;
                }
				
				$league = new league();
				$update_data['has_event'] = 1;
				
				$team = new team();
				$team->updateCustom(array('use_count' => $team->db->inc(1)), array('id' => $event->home_team_id));
				$team->updateCustom(array('use_count' => $team->db->inc(1)), array('id' => $event->away_team_id));
				
				$league = new league();
				$league->updateCustom(array('use_count' => $league->db->inc(1)), array('id' => $event->league_id));
				$league->updateCustom(array('has_event' => '1'), array('id' => $event->league_id));
				
				updateTotalEvent($event);
                
                $response['id'] = $id;
				$response['redirect'] = true;

                break;

            case 'PUT':
				$additional_fields = getEventAdditionalFields($params['category_id']);
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $event = $event->byId($params['id']);
				
				if(array_key_exists('upload_url_home', $params) || array_key_exists('upload_url_away', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
                    $event->$key = $value;
				}
				
				$event->season_id	= getSeasonId($event);
                $event->updated_at = $date;
                $event->save();
                
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
                $event = $event->byId($params['id']);
                $event->delete();
				
				updateTotalEvent($event);
                
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
						$event = $event->byId($data['id']);
					}else{
						$event->isNew = true;
						
						$event->created_at = $date;
					}
					
					if($action == 'delete'){
						$event->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$event->$key = $value;
					}

					$event->updated_at = $date;
					$id = $event->save();
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

	function upload(&$params){
        $upload = new upload();
        foreach($params['image_data'] as $key => $value){
			foreach($value as $innerKey => $innerValue){
				$upload->$innerKey = $innerValue;
			}
			
			$upload->isNew = true;
			
            $upload->created_at = date("Y-m-d H:i:s");
        	$upload_id = $upload->save();
			
			$params[$key.'_upload_id'] = $upload_id;
        };
		
        unset($params['image_data']);
    }

	function uploadId(&$params){
		$upload	= new upload();
		$uploadObjHome = $upload->where('url', $params['upload_url_home'])->getOne();

		if($uploadObjHome){
			$params['home_team_upload_id']	= $uploadObjHome->id;
		}
		
		$uploadObjAway = $upload->where('url', $params['upload_url_away'])->getOne();

		if($uploadObjAway){
			$params['away_team_upload_id']	= $uploadObjAway->id;
		}
	}

	function updateTotalEvent($eventObj){
		$current_month 	= date('n', strtotime($eventObj->prediction_end_at));
		$current_year	= date('Y', strtotime($eventObj->prediction_end_at));
		
		$event	= new event();
		$total_event = $event->where('category_id', $eventObj->category_id)->where('league_id', $eventObj->league_id)->where('MONTH(prediction_end_at)', $current_month)->where('YEAR(prediction_end_at)', $current_year)->getValue('count(id)');
		
		if($eventObj->category_id == 1 || $eventObj->category_id == 2){
			$total_event = $total_event * 3;
		}
				
		$prediction_stats	= new prediction_stats();
		$prediction_stats->where('category_id', $eventObj->category_id)->where('league_id', $eventObj->league_id)->where('month', $current_month)->where('year', $current_year);
		$prediction_stats->updateWhere(array('prediction_total_count' => $total_event));
	}

	function getEventAdditionalFields($category_id){
		$category 		= new category();
		$basketball_id	= $category->where('name', 'Basketball')->getValue('id');
		$gaming_id		= $category->where('name', 'Gaming')->getValue('id');
		$football_id	= $category->where('name', 'Soccer')->getValue('id');
		
		$additional_fields = array();
		
		if($category_id == $football_id){
			$additional_fields[] = array('index' => 'handicap_home_bet', 'label' => '全场让球 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_home_odds', 'label' => '全场让球 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_away_bet', 'label' => '全场让球 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_away_odds', 'label' => '全场让球 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_home_bet', 'label' => '全场大小 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_home_odds', 'label' => '全场大小 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_away_bet', 'label' => '全场大小 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_away_odds', 'label' => '全场大小 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'single_home', 'label' => '全场独赢 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'single_tie', 'label' => '全场独赢 - 和', 'required' => true);
			$additional_fields[] = array('index' => 'single_away', 'label' => '全场独赢 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'category_id', 'label' => '体育球类', 'required' => true);
			$additional_fields[] = array('index' => 'league_id', 'label' => '联赛名称', 'required' => true);
			$additional_fields[] = array('index' => 'home_team_id', 'label' => '主队', 'required' => true);
			$additional_fields[] = array('index' => 'away_team_id', 'label' => '客队', 'required' => true);
			$additional_fields[] = array('index' => 'match_at', 'label' => '比赛日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'prediction_end_at', 'label' => '预测结束日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'round', 'label' => '轮数', 'required' => true);
		}else if($category_id == $basketball_id){
			$additional_fields[] = array('index' => 'handicap_home_bet', 'label' => '全场让球 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_home_odds', 'label' => '全场让球 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_away_bet', 'label' => '全场让球 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'handicap_away_odds', 'label' => '全场让球 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_home_bet', 'label' => '全场大小 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_home_odds', 'label' => '全场大小 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_away_bet', 'label' => '全场大小 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'over_under_away_odds', 'label' => '全场大小 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'single_home', 'label' => '全场独赢 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'single_away', 'label' => '全场独赢 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'category_id', 'label' => '体育球类', 'required' => true);
			$additional_fields[] = array('index' => 'league_id', 'label' => '联赛名称', 'required' => true);
			$additional_fields[] = array('index' => 'home_team_id', 'label' => '主队', 'required' => true);
			$additional_fields[] = array('index' => 'away_team_id', 'label' => '客队', 'required' => true);
			$additional_fields[] = array('index' => 'match_at', 'label' => '比赛日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'prediction_end_at', 'label' => '预测结束日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'round', 'label' => '轮数', 'required' => true);
		}else if($category_id == $gaming_id){
			$additional_fields[] = array('index' => 'single_home', 'label' => '全场独赢 - 主', 'required' => true);
			$additional_fields[] = array('index' => 'single_away', 'label' => '全场独赢 - 客', 'required' => true);
			$additional_fields[] = array('index' => 'match_at', 'label' => '比赛日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'prediction_end_at', 'label' => '预测结束日期时间', 'required' => true);
			$additional_fields[] = array('index' => 'category_id', 'label' => '体育球类', 'required' => true);
			$additional_fields[] = array('index' => 'league_id', 'label' => '联赛名称', 'required' => true);
		}

		return $additional_fields;
	}

	function getSeasonId($current_event){
		//for Soccer and Basketball, current season will be updated automatically daily
		//for gaming, if no season, create a season, if there's season, check if new event match date is a new season
		// new season = 01/01 - 07/01, 07/01 - 01/01
		$date = date("Y-m-d H:i:s");
		
		$season_list 	= new season_list();
		$event 			= new event();
		$season_id		= $season_list->where('league_id', $current_event->league_id)->where('current', 1)->getValue('season_id');

		//category_id 4 = gaming
		//if gaming has no season, create a season
		if($current_event->category_id == 4){
			if(!$season_id){
				$game_season = date("Y/01-Y/06");
				if(date("n") > 6){
					$game_season = date("Y/07-Y/12");
				}
				
				$season_list->isNew 		= true;
				$season_list->category_id 	= $current_event->category_id;
				$season_list->league_id 	= $current_event->league_id;
				$season_list->current 		= 1;
				$season_list->created_at 	= $date;
				$season_list->season		= $game_season;
				$season_id	= $season_list->save();

				$season_list->updateCustom(array("season_id" => $season_id), array("id" => $season_id));
			}else{
				$previous_event = $event->where('league_id', $current_event->league_id)->orderBy('id', 'desc')->get(array(1,1));
				if($previous_event){
					$new_season = false;
					$game_season = "";
					$previous_event = $previous_event[0];
					$previous_event_month = date("n", strtotime($previous_event->match_at));
					$current_event_month = date("n", strtotime($current_event->match_at));

					if($previous_event_month > 6){
						if($current_event_month >= 1 && $current_event_month <= 6){
							$new_season = true;
							$game_season = date("Y/01-Y/06");
						}
					}else{
						if($current_event_month >= 7 && $current_event_month <= 12){
							$new_season = true;
							$game_season = date("Y/07-Y/12");
						}
					}

					if($new_season){
						//update current to 0
						$season_list->updateCustom(array('current' => 0), array('league_id' => $current_event->league_id, 'category_id' => $current_event->category_id));

						$season_list->isNew 		= true;
						$season_list->category_id 	= $current_event->category_id;
						$season_list->league_id 	= $current_event->league_id;
						$season_list->current 		= 1;
						$season_list->created_at 	= $date;
						$season_list->season		= $game_season;
						$season_id	= $season_list->save();

						$season_list->season_id		= $season_id;
						$season_list->updateCustom(array("season_id" => $season_id), array("id" => $season_id));
					}
				} 
			}
		}

		return $season_id;
	}

	$response['extra'] = $input;

?>