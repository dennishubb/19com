<?php

    include_once('model/user.php');
	include_once('model/prediction.php');

	$input = $params;

    do{
        
        $prediction_user_favourite = new prediction_user_favourite();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				if($login_user->type == 'Member'){
					$prediction_user_favourite->where('user_id', $login_user->id, "=");
				}
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $prediction_user_favouriteObj = $prediction_user_favourite->byId($params['id']);
                    $return = $prediction_user_favouriteObj->data;
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
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$prediction_user_favourite->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_user_favourite->orderBy($data['field'], $data['sort']);
							}
						}
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$prediction_user_favourite->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_user_favourite_copy = $prediction_user_favourite->db->copy();
                    $result = $prediction_user_favourite->get($limit);
					$prediction_user_favourite->db = $prediction_user_favourite_copy;
					$totalRecords = $prediction_user_favourite->getValue("count(prediction_user_favourite.id)");
                    
                    $prediction_user_favourite_array = array();
                    if($result){
                        foreach($result as $prediction_user_favouriteObj){
                            $return = $prediction_user_favouriteObj->data;
                            $prediction_user_favourite_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_user_favourite_array;
                    
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
                    $prediction_user_favourite->$key = $value;
                }

				$prediction_user_favourite->user_id		= $login_user->id;
                $prediction_user_favourite->created_at 	= $date;

                $id = $prediction_user_favourite->save();
                if(!$id){
                    responseFail($error, $prediction_user_favourite->getLastError());
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
				
                $prediction_user_favourite = $prediction_user_favourite->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_user_favourite->$key = $value;
				}

                $prediction_user_favourite->updated_at = $date;
                $prediction_user_favourite->save();
                
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
                $prediction_user_favourite = $prediction_user_favourite->byId($params['id']);
                $prediction_user_favourite->delete();
                
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
						$prediction_user_favourite = $prediction_user_favourite->byId($data['id']);
					}else{
						$prediction_user_favourite->isNew = true;
						
						$prediction_user_favourite->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_user_favourite->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_user_favourite->$key = $value;
					}

					$prediction_user_favourite->updated_at = $date;
					$id = $prediction_user_favourite->save();
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