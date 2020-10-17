<?php

    include_once('model/category.php');
    include_once('model/area.php');
	include_once('model/country.php');
	include_once('model/top_ten_rate.php');

	$input = $params;

    do{
        
        $league = new league();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $league->adminFields : $league->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $leagueObj = $league->with('category')->join('top_ten_rate', 'id', 'LEFT', 'top_ten_rate.league_id')->byId($params['id'], $selectFields);
					$return = $leagueObj->data;
					
					if($leagueObj->top_ten_rate && $leagueObj->top_ten_rate['id'] != null){
						$return['top_ten_rate'] = $leagueObj->top_ten_rate;
						$return['top_ten_rate_flag'] = true;
					}else{
						$return['top_ten_rate_flag'] = false;
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
							
                            $league->where($key, "$value", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $league->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$league->where($field, $value, $operator);
                        }
                    }
                    
					$league_copy = $league->db->copy();
					
					if($login_user->type == 'Admin')
                    	$result = $league->with('category')->join('top_ten_rate', 'id', 'LEFT', 'top_ten_rate.league_id')->get($limit, $selectFields);
					else
						$result = $league->with('category')->get($limit, $selectFields);
					
					$league->db = $league_copy;
					
					if($login_user->type == 'Admin')
                    	$totalRecords = $league->with('category')->join('top_ten_rate', 'id', 'LEFT', 'top_ten_rate.league_id')->getValue("count(league.id)");
					else
						$totalRecords = $league->with('category')->getValue("count(league.id)");
					
                    $league_array = array();
                    if($result){
                        foreach($result as $leagueObj){
                            $return = $leagueObj->data;
							
							if($leagueObj->category){
								$return['category_display'] = $leagueObj->category->display;
								unset($return['category']);
							}
							
							if(isset($return['top_ten_rate']) && isset($return['top_ten_rate']['id'])){
								$return['top_ten_rate_flag'] = true;
							}else{
								$return['top_ten_rate_flag'] = false;
								unset($return['top_ten_rate']);
							}
							
                            $league_array[] = $return;
                        } 
                    }

                    $response['data']   = $league_array;
                    
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
                
                foreach($params as $key => $value){
                    $league->$key = $value;
                }

                $league->created_at 	= $date;
                $league->updated_at 	= $date;

                $id = $league->save();
                if(!$id){
                    responseFail($error, $league->getLastError());
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
				
                $league = $league->byId($params['id']);
                
                foreach($params as $key => $value){
                    $league->$key = $value;
				}

                $league->updated_at = $date;
                $league->save();
                
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
                $league = $league->byId($params['id']);
				
				if($league->category_id != 4){
					responseFail($error, "cannot delete non-esports league");
                    break;
				}
				
				if($league->has_event == 1){
                    responseFail($error, "无法删除此联赛，此联赛已有比赛和队伍");
                    break;
				}
				
                $league->delete();
                
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
						$league = $league->byId($data['id']);
					}else{
						$league->isNew = true;
						
						$league->created_at = $date;
					}
					
					if($action == 'delete'){
						$league->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$league->$key = $value;
					}

					$league->updated_at = $date;
					$id = $league->save();
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