<?php

	include_once('model/message.php');

	$input = $params;

    do{

        $message_report = new message_report();
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
                    $message_reportObj = $message_report->with('user')->with('message')->byId($params['id']);
					if($message_reportObj->user){
						$return['user_name']    	= $message_reportObj->user->name;
						$return['user_username']    = $message_reportObj->user->username;
					}
					if($message_reportObj->message){
						$return['message']    		= $message_reportObj->message->message;
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
							
                            $message_report->where($message_report->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $message_report->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$message_report->where($field, $value, $operator);
                        }
                    }

					$message_report_copy = $message_report->db->copy();
                    $result = $message_report->with('user')->with('message')->get($limit);
					$message_report->db = $message_report_copy;
					$totalRecords = $message_report->with('user')->with('message')->getValue("count(message_report.id)");
                    
                    $message_report_array = array();
                    if($result){
                        foreach($result as $message_reportObj){
                            $return = $message_reportObj->data;
							
							if($message_reportObj->user){
								$return['user_name']    	= $message_reportObj->user->name;
								$return['user_username']    = $message_reportObj->user->username;
							}
							if($message_reportObj->message){
								$return['message']    		= $message_reportObj->message->message;
							}
							
                            $message_report_array[] = $return;
                        } 
                    }

                    $response['data'] = $message_report_array;

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
                    $message_report->$key = $value;
                }

				$message_report->user_id	= $login_user->id;
                $message_report->created_at = $date;

                $id = $message_report->save();
                if(!$id){
                    responseFail($error, $message_report->getLastError());
                    break;
                }
                
                $response['data'] = $id;
				$response['redirect'] = true;
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $message_report = $message_report->byId($params['id']);
				
                foreach($params as $key => $value){
                    $message_report->$key = $value;
                }

                $message_report->updated_at = $date;
                $id = $message_report->save();
                if(!$id){
                    responseFail($error, $message_report->getLastError());
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $message_report = $message_report->byId($params['id']);
				
				//do not allow deleting system row
				if($message_report->system){
					responseFail($error, 'action forbidden');
					break;
				}
				
                $message_report->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$message_report = $message_report->byId($data['id']);
					foreach($data as $key => $value){
						$message_report->$key = $value;
					}

					if($action == 'delete'){
						if($message_report->system){
							continue;
						}
						
						$message_report->delete();
					}else{
						$message_report->updated_at = $date;
						$id = $message_report->save();
					}
				}
                
				$response['redirect'] = true;
				break;
				
        }
        
    } while(0);

	$response['extra'] = $input;

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