<?php

    do{
        
        $seo_module = new seo_module();
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
                    $seo_moduleObj = $seo_module->byId($params['id']);
                    $return = $seo_moduleObj->data;            
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
							
                            $seo_module->where($seo_module->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
                        foreach($params['sort'] as $key => $value){
							if(strlen($value) < 0){
								continue;
							}
							
                            $seo_module->orderBy($seo_module->dbTable.".".$key, $value);
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
							
							$seo_module->where($field, $value, $operator);
                        }
                    }
                    
					$seo_module_copy = $seo_module->db->copy();
					$totalRecords = $seo_module_copy->getValue("seo_module", "count(id)");
                    $result = $seo_module->get($limit);
                    
                    $seo_module_array = array();
                    if($result){
                        foreach($result as $seo_moduleObj){
                            $return = $seo_moduleObj->data;
                            $seo_module_array[] = $return;
                        } 
                    }

                    $response['data']   = $seo_module_array;
                    
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
                    $seo_module->$key = $value;
                }
                
                $id = $seo_module->save();
                if(!$id){
                    responseFail($error, $seo_module->getLastError());
                    break;
                }
				
				$seo_module->created_at = $date;
				$seo_module->updated_at = $date;
                
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
                $seo_module = $seo_module->byId($params['id']);
                
                foreach($params as $key => $value){
                    $seo_module->$key = $value;
                }

                $seo_module->updated_at = $date;
                $seo_module->save();
                
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
                $seo_module = $seo_module->byId($params['id']);
                $seo_module->delete();
                
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
						$seo_module = $seo_module->byId($data['id']);
					}else{
						$seo_module->isNew = true;
						
						$seo_module->created_at = $date;
					}
					
					if($action == 'delete'){
						$seo_module->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$seo_module->$key = $value;
					}

					$seo_module->updated_at = $date;
					$id = $seo_module->save();
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