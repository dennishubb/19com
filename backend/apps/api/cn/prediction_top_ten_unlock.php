<?php

    include_once('model/user.php');
	include_once('model/event.php');
	include_once('model/credit.php');
	include_once('model/prediction_top_ten.php');

	$input = $params;

    do{
        
        $prediction_top_ten_unlock = new prediction_top_ten_unlock();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				if($login_user->type == 'Member'){
					$prediction_top_ten_unlock->where('user_id', $login_user->id, "=");
				}
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $prediction_top_ten_unlockObj = $prediction_top_ten_unlock->byId($params['id']);
                    $return = $prediction_top_ten_unlockObj->data;
                    if( $prediction_top_ten_unlockObj->user){
                        $return['user_data']     = $prediction_top_ten_unlockObj->user->data;
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
                        	$prediction_top_ten_unlock->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_top_ten_unlock->orderBy($data['field'], $data['sort']);
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
							
							$prediction_top_ten_unlock->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_top_ten_unlock_copy = $prediction_top_ten_unlock->db->copy();
                    $result = $prediction_top_ten_unlock->get($limit);
					$prediction_top_ten_unlock->db = $prediction_top_ten_unlock_copy;
					$totalRecords = $prediction_top_ten_unlock->getValue("count(prediction_top_ten_unlock.id)");
                    
                    $prediction_top_ten_unlock_array = array();
                    if($result){
                        foreach($result as $prediction_top_ten_unlockObj){
                            $return = $prediction_top_ten_unlockObj->data;
                            $prediction_top_ten_unlock_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_top_ten_unlock_array;
                    
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
				
				$prediction_top_ten		= new prediction_top_ten();
				$prediction_top_tenObj	= $prediction_top_ten->byId($params['prediction_top_ten_id']);
				if(!$prediction_top_tenObj){
					responseFail($error, 'record is deleted', 404);
                    break;
				}
				
				$credit = new credit();
				$credit_id = $credit->where('top_ten_unlock', '1')->getValue('id');
				
				$user = new user();
				$top_ten_unlock_id = $user->where('username', 'top ten unlock')->getValue('id');
				
				//check voucher balance
				$balance = getBalance($login_user->id, $credit_id);
				if($balance < 1){
					responseFail($error, 'insufficient voucher');
                    break;
				}
				
				$prediction_top_ten_unlockObj = $prediction_top_ten_unlock->where('prediction_top_ten_id', $params['prediction_top_ten_id'])->where('user_id', $login_user->id)->getValue('id');
				if($prediction_top_ten_unlockObj){
					responseFail($error, 'already unlocked');
                    break;
				}
                
                foreach($params as $key => $value){
                    $prediction_top_ten_unlock->$key = $value;
                }

				$prediction_top_ten_unlock->user_id		= $login_user->id;
                $prediction_top_ten_unlock->created_at 	= $date;

                $id = $prediction_top_ten_unlock->save();
                if(!$id){
                    responseFail($error, $prediction_top_ten_unlock->getLastError());
                    break;
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
				insertTransaction($login_user->id, 1, $id, $credit_id, $login_user->id, $top_ten_unlock_id, $date, "top ten unlock");

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $prediction_top_ten_unlock = $prediction_top_ten_unlock->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_top_ten_unlock->$key = $value;
				}

                $prediction_top_ten_unlock->updated_at = $date;
                $prediction_top_ten_unlock->save();
                
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
                $prediction_top_ten_unlock = $prediction_top_ten_unlock->byId($params['id']);
                $prediction_top_ten_unlock->delete();
                
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
						$prediction_top_ten_unlock = $prediction_top_ten_unlock->byId($data['id']);
					}else{
						$prediction_top_ten_unlock->isNew = true;
						
						$prediction_top_ten_unlock->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_top_ten_unlock->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_top_ten_unlock->$key = $value;
					}

					$prediction_top_ten_unlock->updated_at = $date;
					$id = $prediction_top_ten_unlock->save();
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