<?php

    do{
        
        $role_permission = new role_permission();
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
                    $role_permissionObj = $role_permission->byId($params['id']);
                    $return = $role_permissionObj->data;            
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
							
                            $role_permission->where($role_permission->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $role_permission->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$role_permission->where($field, $value, $operator);
                        }
                    }
                    
					$role_permission_copy = $role_permission->db->copy();
					$totalRecords = $role_permission_copy->getValue("role_permission", "count(id)");
                    $result = $role_permission->get($limit);
                    
                    $role_permission_array = array();
                    if($result){
                        foreach($result as $role_permissionObj){
                            $return = $role_permissionObj->data;
                            $role_permission_array[] = $return;
                        } 
                    }

                    $response['data']   = $role_permission_array;
                    
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
                    $role_permission->$key = $value;
                }
                
                $id = $role_permission->save();
                if(!$id){
                    responseFail($error, $role_permission->getLastError());
                    break;
                }
				
				$role_permission->created_at = $date;
				$role_permission->updated_at = $date;
                
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
                $role_permission = $role_permission->byId($params['id']);
                
                foreach($params as $key => $value){
                    $role_permission->$key = $value;
                }

                $role_permission->updated_at = $date;
                $role_permission->save();
                
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
                $role_permission = $role_permission->byId($params['id']);
                $role_permission->delete();
                
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
						$role_permission = $role_permission->byId($data['id']);
					}else{
						$role_permission->isNew = true;
						
						$role_permission->created_at = $date;
					}
					
					if($action == 'delete'){
						$role_permission->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$role_permission->$key = $value;
					}

					$role_permission->updated_at = $date;
					$id = $role_permission->save();
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