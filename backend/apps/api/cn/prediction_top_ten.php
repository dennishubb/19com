<?php

    include_once('model/user.php');
	include_once('model/event.php');
	include_once('model/upload.php');

	$input = $params;

    do{
        
        $prediction_top_ten = new prediction_top_ten();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $prediction_top_tenObj = $prediction_top_ten->with('user')->byId($params['id']);
                    $return = $prediction_top_tenObj->data;
                    if( $prediction_top_tenObj->user){
                        $return['user_data']     = $prediction_top_tenObj->user->data;
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
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$prediction_top_ten->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_top_ten->orderBy($data['field'], $data['sort']);
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
							
							$prediction_top_ten->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_top_ten_copy = $prediction_top_ten->db->copy();
                    $result = $prediction_top_ten->with('user')->get($limit);
                    $prediction_top_ten->db = $prediction_top_ten_copy;
					$totalRecords = $prediction_top_ten->with('user')->getValue("count(prediction_top_ten.id)");
					
                    $prediction_top_ten_array = array();
                    if($result){
						$upload	= new upload();
                        foreach($result as $prediction_top_tenObj){
                            $return = $prediction_top_tenObj->data;
                            if( $prediction_top_tenObj->user){
								$return['user_data']     = $prediction_top_tenObj->user->data;
								
								if($prediction_top_tenObj->user->upload_id > 0){
									$uploadObj = $upload->byId($prediction_top_tenObj->user->upload_id);
									if($uploadObj)
										$return['user_upload_url']	= $uploadObj->url;
								}
							}
                            $prediction_top_ten_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_top_ten_array;
                    
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
				
				//check points balance, event prediction_top_ten_end_at
                
                foreach($params as $key => $value){
                    $prediction_top_ten->$key = $value;
                }

                $prediction_top_ten->created_at 	= $date;
                $prediction_top_ten->updated_at 	= $date;

                $id = $prediction_top_ten->save();
                if(!$id){
                    responseFail($error, $prediction_top_ten->getLastError());
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
				
                $prediction_top_ten = $prediction_top_ten->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_top_ten->$key = $value;
				}

                $prediction_top_ten->updated_at = $date;
                $prediction_top_ten->save();
                
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
                $prediction_top_ten = $prediction_top_ten->byId($params['id']);
                $prediction_top_ten->delete();
                
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
						$prediction_top_ten = $prediction_top_ten->byId($data['id']);
					}else{
						$prediction_top_ten->isNew = true;
						
						$prediction_top_ten->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_top_ten->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_top_ten->$key = $value;
					}

					$prediction_top_ten->updated_at = $date;
					$id = $prediction_top_ten->save();
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