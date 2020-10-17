<?php

	$input = $params;

    include_once('model/upload.php');

    do{
        
        $ads_button = new ads_button();
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
                    $ads_buttonObj = $ads_button->with('upload')->byId($params['id']);
                    $return = $ads_buttonObj->data;            
                    if($ads_buttonObj->upload){
                        $return['upload_url']   = $ads_buttonObj->upload->url;
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
							
                            $ads_button->where($ads_button->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $ads_button->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$ads_button->where($field, $value, $operator);
                        }
                    }
                    
					$ads_button_copy = $ads_button->db->copy();
                    $result = $ads_button->with('upload')->get($limit);
					$ads_button->db = $ads_button_copy;
					$totalRecords = $ads_button->with('upload')->getValue("count(ads_button.id)");
                    
                    $ads_button_array = array();
                    if($result){
                        foreach($result as $ads_buttonObj){
                            $return = $ads_buttonObj->data;
                            if($ads_buttonObj->upload){
                                $return['upload_url']   = $ads_buttonObj->upload->url;
							}
                            $ads_button_array[] = $return;
                        } 
                    }

                    $response['data']   = $ads_button_array;
                    
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
                    $ads_button->$key = $value;
                }

                $ads_button->created_at = $date;
                $ads_button->updated_at = $date;
                
                $id = $ads_button->save();
                if(!$id){
                    responseFail($error, $ads_button->getLastError());
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
                $ads_button = $ads_button->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
                    $ads_button->$key = $value;
                }

                $ads_button->updated_at = $date;
                $ads_button->save();
                
                $response['id'] 		= $params['id'];
				$response['redirect'] 	= true;
				$response['data']	  	= $ads_button->data;

                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $ads_button = $ads_button->byId($params['id']);
                $ads_button->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$ads_button = $ads_button->byId($data['id']);
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
					
					foreach($data as $key => $value){
						$ads_button->$key = $value;
					}

					if($action == 'delete'){
						$ads_button->delete();
					}else{
						$ads_button->updated_at = $date;
						$id = $ads_button->save();
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

    function upload(&$params){
        $upload = new upload();
        foreach($params['image_data'] as $key => $value){
            $upload->$key = $value;
        }

        $upload->created_at = date("Y-m-d H:i:s");

        $upload_id = $upload->save();
        unset($params['image_data']);

        $params['image_upload_id'] = $upload_id;
    }

	function uploadId(&$params){
		$upload	= new upload();
		$uploadObj = $upload->where('url', $params['upload_url'])->getOne();

		if($uploadObj){
			$params['image_upload_id']	= $uploadObj->id;
		}
	}

?>