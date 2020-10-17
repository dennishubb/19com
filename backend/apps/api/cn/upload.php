<?php

    include_once('model/category.php');
	include_once('model/article.php');

	$input = $params;

    do{

        $upload = new upload();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $upload->adminFields : $upload->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $uploadObj = $upload->with('article')->byId($params['id'], $selectFields);
                    $return = $uploadObj->data;
//                    if($uploadObj->category){
//                        $return['category_data'] = $uploadObj->category->data;
//                    }
//					if($uploadObj->sub_category){
//                        $return['sub_category_data'] = $uploadObj->sub_category->data;
//                    }
					if($uploadObj->article){
                        $return['article_data'] = $uploadObj->article->data;
						unset($return['article']);
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
							
                            $upload->where($upload->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $upload->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$upload->where($field, $value, $operator);
                        }
                    }

                    $result = $upload->with('article')->get($limit, $selectFields);
                    
                    $upload_array = array();
                    if($result){
                        foreach($result as $uploadObj){
                            $return = $uploadObj->data;
//                            if($uploadObj->category){
//								$return['category_data'] = $uploadObj->category->data;
//							}
//							if($uploadObj->sub_category){
//								$return['sub_category_data'] = $uploadObj->sub_category->data;
//							}
							if($uploadObj->article){
								$return['article_data'] = $uploadObj->article->data;
								unset($return['article']);
							}
                            $upload_array[] = $return;
                        } 
                    }

                    $response['data'] = $upload_array;

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
                    $upload->$key = $value;
                }

                $upload->created_at 	= $date;
                $upload->updated_at 	= $date;

                $id = $upload->save();
                if(!$id){
                    responseFail($error, $upload->getLastError());
                    break;
                }
                
                $response['data'] = $id;
				$response['redirect'] = true;
				
                break;

            case 'PUT':
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				if(array_key_exists('id', $params)){
					$upload = $upload->byId($params['id']);
					
					foreach($params as $key => $value){
						$upload->$key = $value;
					}

                	$upload->updated_at = $date;
                	$id = $upload->save();
					
					$response['id'] = $id;
				}else{
					if(array_key_exists('filter', $params)){
						foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';

							if(strlen($value) == 0){
								continue;
							}

							$upload->where($field, $value, $operator);
							$whereArray[$field] = str_replace("%", "", $value);
						}
					}
					
					if($params['action'] = 'replace'){
						$upload_result = $upload->get();
						
						if(!$upload_result){
							responseFail($error, "record not found");
							break;
						}
						
						foreach($upload_result as $uploadObj){
							foreach($params['data'] as $key => $value){
								$uploadObj->$key = str_replace($whereArray[$key], $value, $uploadObj->$key);
							}
							
							$uploadObj->save();
						}

					}else{
						$upload->updateWhere($params['data']);
					}
					
				}
         
				$response['redirect'] = true;
				
                break;

            case 'DELETE':
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                if(array_key_exists('id', $params)){
					$upload = $upload->byId($params['id']);
					$upload->delete();
					
					$response['id'] 		= $params['id'];
					$response['url']		= $upload->url;
				}else{
					if(array_key_exists('filter', $params)){
						$has_where = false;
						foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';

							if(strlen($value) == 0){
								continue;
							}

							$upload->where($field, $value, $operator);
							$has_where = true;
						}
						
						//only delete when where is present, else it could truncate the whole table
						if($has_where){
							$upload->deleteWhere();
						}
					}
				}
                

				$response['redirect'] 	= true;
				
                break;
				
			case 'PATCH':
				$action = "";
				$urls = array();
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$upload = $upload->byId($data['id']);
					foreach($data as $key => $value){
						$upload->$key = $value;
					}

					if($action == 'delete'){
						$upload->delete();
						$urls[] = $upload->url;
					}else{
						$upload->updated_at = $date;
						$id = $upload->save();
					}
				}
                
				$response['redirect'] = true;
				$response['urls'] = $urls;
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