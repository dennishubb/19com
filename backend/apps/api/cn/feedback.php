<?php

	$input = $params;

    do{
        
        $feedback = new feedback();
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
                    $feedbackObj = $feedback->byId($params['id']);
                    $return = $feedbackObj->data;
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
							
                            $feedback->where($key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$feedback->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$feedback->orderBy($data['field'], $data['sort']);
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
							
							$feedback->where($field, $value, $operator);
                        }
                    }
                    
					$feedback_copy = $feedback->db->copy();
                    $result = $feedback->get($limit);
					$feedback->db = $feedback_copy;
					$totalRecords = $feedback->getValue("count(feedback.id)");
                    
                    $feedback_array = array();
                    if($result){
                        foreach($result as $feedbackObj){
                            $return = $feedbackObj->data;
                            $feedback_array[] = $return;
                        } 
                    }

                    $response['data']   = $feedback_array;
                    
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
				$additional_fields[] = array('index' => 'email', 'label' => 'email', 'required' => true);
				$additional_fields[] = array('index' => 'message', 'label' => 'message', 'required' => true);
				$additional_fields[] = array('index' => 'type', 'label' => 'type', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                
                foreach($params as $key => $value){
                    $feedback->$key = $value;
                }

                $feedback->created_at 	= $date;

                $id = $feedback->save();
                if(!$id){
                    responseFail($error, json_encode($feedback->errors[0]));
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
                $feedback = $feedback->byId($params['id']);
                
                foreach($params as $key => $value){
                    $feedback->$key = $value;
                }

                $feedback->updated_at = $date;
                $feedback->save();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;

				uploadFiles($feedback, $params['id']);

                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $feedback = $feedback->byId($params['id']);
                $feedback->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$feedback = $feedback->byId($data['id']);
					
					if($action == 'delete'){
						$feedback->delete();
						continue;
					}
					
					foreach($data as $key => $value){
						$feedback->$key = $value;
					}
					
					$feedback->updated_at = $date;
					$id = $feedback->save();
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
		
		if(array_key_exists('email', $cv) && strlen($cv['email']) > 0){
			if (!filter_var($cv['email'], FILTER_VALIDATE_EMAIL)) {
				$problem = 'Invalid Email';
			}
		}
		
		if(array_key_exists('message', $cv) && strlen($cv['message']) > 0){
			if(mb_strlen($cv['message']) > 500){
				$problem = 'Message must be within 500 characters';
			}
		}

        if ($problem) {
            return $problem;
        }
        
        return $cv;
    }

	$response['extra'] = $input;

    function upload(&$params){
		$upload	 = new upload();
        foreach($params['image_data'] as $key => $value){
            $upload->$key = $value;
        }

        $upload->created_at = date("Y-m-d H:i:s");

        $upload_id = $upload->save();
        unset($params['image_data']);

        $params['upload_id'] = $upload_id;
    }

?>