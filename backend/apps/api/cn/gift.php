<?php

    include_once('model/category.php');
    include_once('model/upload.php');

	$input = $params;

    do{
        
        $gift = new gift();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $gift->adminFields : $gift->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $giftObj = $gift->with('category')->with('sub_category')->with('upload')->byId($params['id'], $selectFields);
                    $return = $giftObj->data;
                    if( $giftObj->category){
                        $return['category']     = $giftObj->category->display;
                    }
					if( $giftObj->sub_category){
						$return['sub_category']     = $giftObj->sub_category->display;
					}
                    if($giftObj->upload){
                        $return['upload_url']   = $giftObj->upload->url;
                    }
                    $return['size'] 		= json_decode($giftObj->size);
					$return['color'] 		= json_decode(str_replace("\"\"", "", $giftObj->color));
					$return['hot_category']	= json_decode($giftObj->hot_category);
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
							
                            $gift->where($key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $gift->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$gift->where($field, $value, $operator);
                        }
                    }
                    
					$gift_copy = $gift->db->copy();
                    $result = $gift->with('category')->with('sub_category')->with('upload')->get($limit, $selectFields);
					$gift->db = $gift_copy;
					$totalRecords = $gift->with('category')->with('sub_category')->with('upload')->getValue("count(gift.id)");
                    
                    $gift_array = array();
                    if($result){
                        foreach($result as $giftObj){
                            $return = $giftObj->data;
                            if( $giftObj->category){
                                $return['category']     = $giftObj->category->display;
                            }
							if( $giftObj->sub_category){
                                $return['sub_category']     = $giftObj->sub_category->display;
                            }
                            if($giftObj->upload){
                                $return['upload_url']   = $giftObj->upload->url;
                            }
							$return['size'] 		= json_decode($giftObj->size);
							$return['color'] 		= json_decode(str_replace("\"\"", "", $giftObj->color));
							$return['hot_category']	= json_decode($giftObj->hot_category);
                            $gift_array[] = $return;
                        } 
                    }

                    $response['data']   	= $gift_array;
					$hot_categories	 		= $gift->getValue('DISTINCT(hot_category)', null);
					$unique_hot_category	= array();
					if($hot_categories){
						foreach($hot_categories as $hot_category){
							$hot_category_array = json_decode($hot_category);
							if(is_array($hot_category_array)){
								$unique_hot_category = array_unique(array_merge($unique_hot_category, $hot_category_array));
							}
						}
					}
 					
					
					$response['hot_category_tags'] = array_values($unique_hot_category);
					
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
					if($key == 'size' || $key == 'color' || $key == 'hot_category'){
						$value = array_values(array_unique($value));
						$value = json_encode($value, JSON_UNESCAPED_UNICODE); 
					}
					
                    $gift->$key = $value;
                }

                $gift->created_at 	= $date;
                $gift->updated_at 	= $date;

                $id = $gift->save();
                if(!$id){
                    responseFail($error, $gift->getLastError());
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
                $gift = $gift->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
					if($key == 'size' || $key == 'color' || $key == 'hot_category'){
						$value = array_values(array_unique($value));
						$value = json_encode($value, JSON_UNESCAPED_UNICODE); 
					}
                    $gift->$key = $value;
                }

                $gift->updated_at = $date;
                $gift->save();
                
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
                $gift = $gift->byId($params['id']);
                $gift->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$gift = $gift->byId($data['id']);
					
					if($action == 'delete'){
						$gift->delete();
						continue;
					}
						
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
					
					if(array_key_exists('tags', $data)){
						$problem = "";

						if(strlen($problem) > 0){
							continue;
						}else{
							$data['tags'] = $result;
						}
					}
					
					foreach($data as $key => $value){
						if($key == 'size' || $key == 'color' || $key == 'hot_category'){
							$value = array_values(array_unique($value));
							$value = json_encode($value); 
						}
						$gift->$key = $value;
					}
					
					$gift->updated_at = $date;
					$id = $gift->save();
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