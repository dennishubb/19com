<?php

	$input = $params;

	include_once('model/user.php');
    include_once('model/category.php');
	include_once('model/league.php');
	include_once('model/season_list.php');
	include_once('model/season_matches.php');
	include_once('model/prediction.php');
	include_once('model/event.php');
	include_once('model/top_ten_rate.php');

    do{
        
        $prediction_rate = new prediction_rate();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Member' ? $prediction_rate->memberFields : $prediction_rate->adminFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $prediction_rateObj = $prediction_rate->byId($params['id'], $selectFields);
                    $return = $prediction_rateObj->data;
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
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$prediction_rate->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_rate->orderBy($data['field'], $data['sort']);
							}
						}
                    }
					
					$league_id = "";
					$year = "";
					$user_id = "";
					
					if(array_key_exists('filter', $params)){	
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							if(strpos($field, 'league_id') !== false) $league_id = $value;
							if(strpos($field, 'year') !== false) $year = $value;
							if(strpos($field, 'user_id') !== false) $user_id = $value;
							
							$prediction_rate->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_rate_copy = $prediction_rate->db->copy();
					
					if($login_user->type == 'Member')
						$result = $prediction_rate->get($limit, $selectFields);
					else
                    	$result = $prediction_rate->with('user')->with('category')->with('league')->get($limit, $selectFields);
					
					$prediction_rate->db = $prediction_rate_copy;
					
					if($login_user->type == 'Member')
						$totalRecords = $prediction_rate->getValue("count(prediction_rate.id)");
					else
                    	$totalRecords = $prediction_rate->with('user')->with('category')->with('league')->getValue("count(prediction_rate.id)");
    
                    $prediction_rate_array = array();
                    if($result){
						$top_ten_rate_setting = array();
						
						$top_ten_rate = new top_ten_rate;
						$top_ten_rate_results = $top_ten_rate->get();
						if($top_ten_rate_results){
							foreach($top_ten_rate_results as $top_ten_rateObj){
								$top_ten_rate_setting[$top_ten_rateObj->category_id][$top_ten_rateObj->league_id] = $top_ten_rateObj->data;
							}
						}
						
                        foreach($result as $prediction_rateObj){
                            $return = $prediction_rateObj->data;
							
						    if($prediction_rateObj->user){
								$return['user_name']     = $prediction_rateObj->user->name;
								$return['user_username'] = $prediction_rateObj->user->username;
								unset($return['user']);
							}
						    if($prediction_rateObj->category){
								$return['category_name']    = $prediction_rateObj->category->name;
								$return['category_display'] = $prediction_rateObj->category->display;
								unset($return['category']);
							}
							if($prediction_rateObj->league){
								$return['league_name_zh']    = $prediction_rateObj->league->name_zh;
								unset($return['league']);
							}
							
							if($return['top_ten_season_rate']  <= 0){
								$return['top_ten_season_rate'] = isset($top_ten_rate_setting[$return['category_id']][$return['league_id']]['season_min_rate']) ? $top_ten_rate_setting[$return['category_id']][$return['league_id']]['season_min_rate'] : 50;
							}
							
							if($return['top_ten_rate']  <= 0){
								$return['top_ten_rate'] = isset($top_ten_rate_setting[$return['category_id']][$return['league_id']]['min_rate']) ? $top_ten_rate_setting[$return['category_id']][$return['league_id']]['min_rate'] : 50;
							}
							
							if($return['top_ten_prediction_count']  <= 0){
								$return['top_ten_prediction_count'] = isset($top_ten_rate_setting[$return['category_id']][$return['league_id']]['prediction_count']) ? $top_ten_rate_setting[$return['category_id']][$return['league_id']]['prediction_count'] : 100;
							}
							
                            $prediction_rate_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_rate_array;
                    
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
//                $params = validate($params, $additional_fields);
//                if(is_string($params)){
//                    responseFail($error, $params);
//                    break;
//                }
//				
//				//check points balance, event prediction_rate_end_at
//                
//                foreach($params as $key => $value){
//                    $prediction_rate->$key = $value;
//                }
//
//                $prediction_rate->created_at 	= $date;
//                $prediction_rate->updated_at 	= $date;
//
//                $id = $prediction_rate->save();
//                if(!$id){
//                    responseFail($error, $prediction_rate->getLastError());
//                    break;
//                }
//                
//                $response['id'] = $id;
//				$response['redirect'] = true;

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $prediction_rate = $prediction_rate->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_rate->$key = $value;
				}

                $prediction_rate->updated_at = $date;
                $prediction_rate->save();
                
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
                $prediction_rate = $prediction_rate->byId($params['id']);
                $prediction_rate->delete();
                
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
						$prediction_rate = $prediction_rate->byId($data['id']);
					}else{
						$prediction_rate->isNew = true;
						
						$prediction_rate->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_rate->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_rate->$key = $value;
					}

					$prediction_rate->updated_at = $date;
					$id = $prediction_rate->save();
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

	$response['extra'] = $input;

?>