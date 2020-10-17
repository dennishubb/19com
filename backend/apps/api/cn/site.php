<?php

	$input = $params;

    include_once('model/upload.php');

    do{
        
        $site = new site();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $site->adminFields : $site->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $siteObj = $site->byId($params['id'], $selectFields);
					
                    $return = $siteObj->data;   
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
							
                            $site->where($site->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $site->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$site->where($field, $value, $operator);
                        }
                    }
					
					/*					$result_copy = $result->db->copy();
                    $res = $result->with('team')->get($limit);
					$result->db = $result_copy;
					$totalRecords = $result->with('team')->getValue("count(result.id)");*/
                    
					//$site_copy = $site->db->copy();
                    $result = $site->get($limit, $selectFields);
					//$site->db = $site_copy;
                    //$totalRecords = $site->getValue("count(site.id)");
                    
                    $site_array = array();
                    if($result){
                        foreach($result as $siteObj){
                            $return = $siteObj->data;

                            $site_array[] = $return;
                        } 
                    }

                    $response['data']   = $site_array;
                    
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
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $site->$key = $value;
                }

                $site->created_at = $date;
                $site->updated_at = $date;
                
                $id = $site->save();
                if(!$id){
                    responseFail($error, $site->getLastError());
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
                $site = $site->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $site->$key = $value;
                }

                $site->updated_at = $date;
                $site->save();
                
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
                $site = $site->byId($params['id']);
                $site->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$site = $site->byId($data['id']);
					
					if($action == 'delete'){
						$site->delete();
					}
					
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
					
					foreach($data as $key => $value){
						$site->$key = $value;
					}

					$site->updated_at = $date;
					$id = $site->save();
				}
                
				$response['redirect'] = true;
				break;
        }
        
    } while(0);

	$response['extra'] = $params;

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

    function upload(&$params){
        $upload = new upload();
        foreach($params['image_data'] as $key => $value){
			foreach($value as $innerKey => $innerValue){
				$upload->$innerKey = $innerValue;
			}
			
			$upload->isNew = true;
			
            $upload->created_at = date("Y-m-d H:i:s");
        	$upload_id = $upload->save();
			
			$params[$key.'_upload_id'] = $upload_id;
        };
		
        unset($params['image_data']);
    }

?>