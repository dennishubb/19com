<?php

	$input = $params;

    do{
        
        $top_ten = new top_ten();
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
                    $top_tenObj = $top_ten->with('user')->with('category')->byId($params['id']);
                    $return = $top_tenObj->data;
					if($top_tenObj->user){
                        $return['user_data']    = $top_tenObj->user->data;
                    }
					if($top_tenObj->category){
                        $return['category_data']    = $top_tenObj->category->data;
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
							if(strlen($value) > 0)
                            	$top_ten->where($key, $value);
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
							
							$top_ten->where($field, $value, $operator);
                        }
                    }
                    
					$top_ten_copy = $top_ten->db->copy();
					$totalRecords = $top_ten_copy->getValue("top_ten", "count(id)");
                    $result = $top_ten->get($limit);
                    
                    $top_ten_array = array();
                    if($result){
                        foreach($result as $top_tenObj){
                            $return = $top_tenObj->data;
							if($top_tenObj->user){
								$return['user_data']    = $top_tenObj->user->data;
							}
							if($top_tenObj->category){
								$return['category_data']    = $top_tenObj->category->data;
							}
                            $top_ten_array[] = $return;
                        } 
                    }

                    $response['data']   = $top_ten_array;
                    
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
                    $top_ten->$key = $value;
                }

                $top_ten->created_at 	= $date;
                $top_ten->updated_at 	= $date;

                $id = $top_ten->save();
                if(!$id){
                    responseFail($error, $top_ten->getLastError());
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
				
                $top_ten = $top_ten->byId($params['id']);
                
                foreach($params as $key => $value){
                    $top_ten->$key = $value;
				}

                $top_ten->updated_at = $date;
                $top_ten->save();
                
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
                $top_ten = $top_ten->byId($params['id']);
                $top_ten->delete();
                
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
						$top_ten = $top_ten->byId($data['id']);
					}else{
						$top_ten->isNew = true;
						
						$top_ten->created_at = $date;
					}
					
					if($action == 'delete'){
						$top_ten->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$top_ten->$key = $value;
					}

					$top_ten->updated_at = $date;
					$id = $top_ten->save();
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