<?php

	include_once('model/user.php');
	include_once('model/forget_password.php');

	$input = $params;

    do{
        
        $reset_password = new reset_password();
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
                    $reset_passwordObj = $reset_password->byId($params['id']);
                    $return = $reset_passwordObj->data;
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
							if(strlen($value) > 0)
                            	$reset_password->where($key, $value);
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
							
							$reset_password->where($field, $value, $operator);
                        }
                    }
                    
					$reset_password_copy = $reset_password->db->copy();
                    $result = $reset_password->get($limit);
     				$reset_password->db = $reset_password_copy;
					$totalRecords = $reset_password->getValue("count(reset_password.id)");
					
                    $reset_password_array = array();
                    if($result){
                        foreach($result as $reset_passwordObj){
                            $return = $reset_passwordObj->data;
                            $reset_password_array[] = $return;
                        } 
                    }

                    $response['data']   = $reset_password_array;
                    
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
					if(strpos($key, 'password') !== false){
                        $value = PasswordHasher::HashPassword($value);
                    }
                    $reset_password->$key = $value;
                }
				
				$user		= new user();
				$userObj 	= $user->byId($reset_password->user_id);
				if(!$userObj){
					responseFail($error, "invalid user");
                    break;
				}
				
				$forget_password	= new forget_password();
				$forget_passwordObj = $forget_password->where('status', 0)->where('verification_code', $reset_password->verification_code)->getOne();
				if(!$forget_passwordObj){
					responseFail($error, "invalid verification code");
                    break;
				}
	
				$reset_password->old_password	= $userObj->password;
                $reset_password->created_at 	= $date;

                $id = $reset_password->save();
                if(!$id){
                    responseFail($error, $reset_password->getLastError());
                    break;
                }
				
				$userObj->password	= $reset_password->new_password;
				$userObj->save();
				
				$forget_passwordObj->status 	= 1;
				$forget_passwordObj->updated_at	= $date;
				$forget_passwordObj->save();
                
                $response['id'] 			= $id;
				$response['redirect'] 		= false;
				
				$userObj->password			= 0;
				
				$response['data']['user'] 	= $userObj->data;

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $reset_password = $reset_password->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $reset_password->$key = $value;
				}

                $reset_password->updated_at = $date;
                $reset_password->save();
                
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
                $reset_password = $reset_password->byId($params['id']);
                $reset_password->delete();
                
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
						$reset_password = $reset_password->byId($data['id']);
					}else{
						$reset_password->isNew = true;
						
						$reset_password->created_at = $date;
					}
					
					if($action == 'delete'){
						$reset_password->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$reset_password->$key = $value;
					}

					$reset_password->updated_at = $date;
					$id = $reset_password->save();
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