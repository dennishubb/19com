<?php

    include_once('model/upload.php');

	$input = $params;

    do{

        $level = new level();
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
                    $levelObj = $level->with('upload')->byId($params['id']);
                    $return = $levelObj->data;
                    if($levelObj->upload){
                        $return['upload_url'] = $levelObj->upload->url;
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
							
                            $level->where($level->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $level->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$level->where($field, $value, $operator);
                        }
                    }

					$level_copy = $level->db->copy();
                    $result = $level->with('upload')->get($limit);
					$level->db = $level_copy;
					$totalRecords = $level->with('upload')->with('user')->getValue("count(level.id)");
                    
                    $level_array = array();
                    if($result){
                        foreach($result as $levelObj){
                            $return = $levelObj->data;
                            if($levelObj->upload){
                                $return['upload_url'] = $levelObj->upload->url;
								unset($return['upload']);
                            }
							$return['user_count'] = 0;
							if($levelObj->user){
                                $return['user_count'] = count($levelObj->user);
								unset($return['user']);
                            }
                            $level_array[] = $return;
                        } 
                    }

                    $response['data'] = $level_array;

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
                    $level->$key = $value;
                }

                $level->created_at = $date;
                $level->updated_at = $date;

                $id = $level->save();
                if(!$id){
                    responseFail($error, $level->getLastError());
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
                $level = $level->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url', $params)){
                    uploadId($params);
                }
				
                foreach($params as $key => $value){
                    $level->$key = $value;
                }

                $level->updated_at = $date;
                $id = $level->save();
                if(!$id){
                    responseFail($error, $level->getLastError());
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
                $level = $level->byId($params['id']);
				
				//do not allow deleting system row
				if($level->system){
					responseFail($error, 'action forbidden');
					break;
				}
				
                $level->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$level = $level->byId($data['id']);
					foreach($data as $key => $value){
						if($key == 'password'){
							$value = PasswordHasher::HashPassword($value);
						}
						$level->$key = $value;
					}

					if($action == 'delete'){
						if($level->system){
							continue;
						}
						
						$level->delete();
					}else{
						$level->updated_at = $date;
						$id = $level->save();
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
        $required_keys = array('id', 'levelname', 'email', 'password');

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

        $params['upload_id'] = $upload_id;
    }

	function uploadId(&$params){
		$upload	= new upload();
		$uploadObj = $upload->where('url', $params['upload_url'])->getOne();

		if($uploadObj){
			$params['upload_id']	= $uploadObj->id;
		}
	}

?>