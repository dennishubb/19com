<?php

    do{
        
        $season_list = new season_list();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Member' ? $season_list->memberFields : $season_list->adminFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                if(array_key_exists('id', $params)){
                    $season_listObj = $season_list->byId($params['id'], $selectFields);
                    $return = $season_listObj->data;   

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
							
                            $season_list->where($season_list->dbTable.".".$key, "$value", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $season_list->orderBy($sorting_data['field'], $sorting_data['sort']);               
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
							
							$season_list->where($field, $value, $operator);
                        }
                    }
                    
					//$season_list_copy = $season_list->db->copy();
                    $result = $season_list->get($limit, $selectFields);
					//$season_list->db = $season_list_copy;
					//$totalRecords = $season_list->getValue("count(season_list.id)");
                    
                    $season_list_array = array();
                    if($result){
                        foreach($result as $season_listObj){
                            $return = $season_listObj->data;
							
                            $season_list_array[] = $return;
                        } 
                    }

                    $response['data']   = $season_list_array;
                    
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
                    $season_list->$key = $value;
                }
				
				$season_list->created_at = $date;
				$season_list->updated_at = $date;
                
                $id = $season_list->save();
                if(!$id){
                    responseFail($error, json_encode($season_list->errors[0]));
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
                $season_list = $season_list->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $season_list->$key = $value;
                }

                $season_list->updated_at = $date;
                $season_list->save();
                
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
                $season_list = $season_list->byId($params['id']);
                $season_list->delete();
                
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
						$season_list = $season_list->byId($data['id']);
					}else{
						$season_list->isNew = true;
						
						$season_list->created_at = $date;
					}
					
					if($action == 'delete'){
						$season_list->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$season_list->$key = $value;
					}

					$season_list->updated_at = $date;
					$id = $season_list->save();
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