<?php

    do{
        
        $season_player_stats_basketball = new season_player_stats_basketball();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $season_player_stats_basketball->adminFields : $season_player_stats_basketball->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                if(array_key_exists('id', $params)){
                    $season_player_stats_basketballObj = $season_player_stats_basketball->byId($params['id']);
                    $return = $season_player_stats_basketballObj->data;   

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
							
                            $season_player_stats_basketball->where($season_player_stats_basketball->dbTable.".".$key, "$value", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$season_player_stats_basketball->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$season_player_stats_basketball->orderBy($data['field'], $data['sort']);
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
							
							$season_player_stats_basketball->where($field, $value, $operator);
                        }
                    }
                    
					//$season_player_stats_basketball_copy = $season_player_stats_basketball->db->copy();
                    $result = $season_player_stats_basketball->get($limit);
					//$season_player_stats_basketball->db = $season_player_stats_basketball_copy;
					//$totalRecords = $season_player_stats_basketball->getValue("count(season_player_stats_basketball.id)");
                    
                    $season_player_stats_basketball_array = array();
                    if($result){
                        foreach($result as $season_player_stats_basketballObj){
                            $return = $season_player_stats_basketballObj->data;
							
                            $season_player_stats_basketball_array[] = $return;
                        } 
                    }

                    $response['data']   = $season_player_stats_basketball_array;
                    
//                    if(array_key_exists('page_number', $params)){
//                        $response['totalPage']      = ceil($totalRecords / $limit[1]);
//                        $response['pageNumber']     = $pageNumber;
//                        $response['totalRecord']    = $totalRecords;
//                        $response['numRecord']      = $pageLimit;
//                        $response['fromPage']       = $pageNumber > 1?($pageNumber - 1)*$pageLimit:"1";
//                        $response['toPage']         = ($pageNumber *$pageLimit) > $totalRecords?$totalRecords:($pageNumber *$pageLimit);
//                    }
                        
                }

                break;

            case 'POST':
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }

                foreach($params as $key => $value){
                    $season_player_stats_basketball->$key = $value;
                }
				
				$season_player_stats_basketball->created_at = $date;
				$season_player_stats_basketball->updated_at = $date;
                
                $id = $season_player_stats_basketball->save();
                if(!$id){
                    responseFail($error, json_encode($season_player_stats_basketball->errors[0]));
                    break;
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
                $season_player_stats_basketball = $season_player_stats_basketball->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $season_player_stats_basketball->$key = $value;
                }

                $season_player_stats_basketball->updated_at = $date;
                $season_player_stats_basketball->save();
                
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
                $season_player_stats_basketball = $season_player_stats_basketball->byId($params['id']);
                $season_player_stats_basketball->delete();
                
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
						$season_player_stats_basketball = $season_player_stats_basketball->byId($data['id']);
					}else{
						$season_player_stats_basketball->isNew = true;
						
						$season_player_stats_basketball->created_at = $date;
					}
					
					if($action == 'delete'){
						$season_player_stats_basketball->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$season_player_stats_basketball->$key = $value;
					}

					$season_player_stats_basketball->updated_at = $date;
					$id = $season_player_stats_basketball->save();
				}
                
				$response['redirect'] = true;
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
				if(in_array($key, $required_keys)){
					$required = true;
				}

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

?>