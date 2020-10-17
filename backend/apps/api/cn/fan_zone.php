<?php

	include_once('model/upload.php');

	$input = $params;

    do{
        
        $fan_zone = new fan_zone();
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
                    $fan_zoneObj = $fan_zone->with('upload')->byId($params['id']);
                    $return = $fan_zoneObj->data;
					if($fan_zoneObj->upload){
                        $return['upload_url']    = $fan_zoneObj->upload->url;
						unset($return['upload']);
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
                            	$fan_zone->where($key, $value);
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$fan_zone->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$fan_zone->orderBy($data['field'], $data['sort']);
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
							
							$fan_zone->where($field, $value, $operator);
                        }
                    }
                    
					$fan_zone_copy = $fan_zone->db->copy();
                    $result = $fan_zone->with('upload')->get($limit);
					$fan_zone->db = $fan_zone_copy;
					$totalRecords = $fan_zone->with('upload')->getValue("count(fan_zone.id)");
                    
                    $fan_zone_array = array();
                    if($result){
                        foreach($result as $fan_zoneObj){
                            $return = $fan_zoneObj->data;
							if($fan_zoneObj->upload){
								$return['upload_url']    = $fan_zoneObj->upload->url;
								unset($return['upload']);
							}
                            $fan_zone_array[] = $return;
                        } 
                    }

                    $response['data']   = $fan_zone_array;
                    
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
				
				if(array_key_exists('upload_url', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
                    $fan_zone->$key = $value;
                }

                $fan_zone->created_at 	= $date;
                $fan_zone->updated_at 	= $date;

                $id = $fan_zone->save();
                if(!$id){
                    responseFail($error, $fan_zone->getLastError());
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
				
                $fan_zone = $fan_zone->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
                    $fan_zone->$key = $value;
				}

                $fan_zone->updated_at = $date;
                $fan_zone->save();
                
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
                $fan_zone = $fan_zone->byId($params['id']);
                $fan_zone->delete();
                
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
						$fan_zone = $fan_zone->byId($data['id']);
					}else{
						$fan_zone->isNew = true;
						
						$fan_zone->created_at = $date;
					}
					
					if($action == 'delete'){
						$fan_zone->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$fan_zone->$key = $value;
					}

					$fan_zone->updated_at = $date;
					$id = $fan_zone->save();
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

	function upload(&$params){
        $upload = new upload();
        foreach($params['image_data'] as $key => $value){
            $upload->$key = $value;
        }

        $upload->created_at = date("Y-m-d H:i:s");

        $upload_id = $upload->save();
        unset($params['image_data']);

        $params['upload_id'] = $upload_id;
    }

	function uploadId(&$params){
		$upload	= new upload();
		$uploadObj = $upload->where('url', $params['upload_url'])->getOne();

		if($uploadObj){
			$params['upload_id']	= $uploadObj->id;
		}
	}

	$response['extra'] = $input;

?>