<?php

	include_once('model/article.php');

    do{
        
        $category = new category();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $category->adminFields : $category->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
				$article = new article();
				
				$main_categories = $category->where('parent_id', 0)->get();
				foreach($main_categories as $data){
					$main_category[$data->id] = $data->display;
				}
				
                if(array_key_exists('id', $params)){
                    $categoryObj = $category->byId($params['id'], $selectFields);
                    $return = $categoryObj->data;   
				
					if($categoryObj->upload){
                        $return['upload_description']  = $categoryObj->upload->alt;
                    }					
					
					$return['parent_category'] = isset($main_category[$categoryObj->parent_id]) ? $main_category[$categoryObj->parent_id]:"";

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
							
                            $category->where($category->dbTable.".".$key, "$value", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $category->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(is_string($value) && strlen($value) == 0){
								continue;
							}else if(is_array($value) && count($value) == 0){
								continue;
							}
							
							$category->where($field, $value, $operator);
                        }
                    }
					
					$category->orderBy('CASE WHEN category.parent_id = 0 THEN category.id ELSE category.parent_id END', 'asc');
					$category->orderBy('category.parent_id', 'asc');
					$category->orderBy('category.id', 'asc');
					$category->orderBy('category.sorting', 'asc');
                    
					$category_copy = $category->db->copy();
                    $result = $category->get($limit, $selectFields);
					$category->db = $category_copy;
					$totalRecords = $category->getValue("count(category.id)");
                    
                    $category_array = array();
                    if($result){
                        foreach($result as $categoryObj){
                            $return = $categoryObj->data;
							if($categoryObj->upload){
								$return['upload_description']  = $categoryObj->upload->alt;
							}
							
							$return['parent_category'] = isset($main_category[$categoryObj->parent_id]) ? $main_category[$categoryObj->parent_id]:"";
							
                            $category_array[] = $return;
                        } 
                    }

                    $response['data']   = $category_array;
                    
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
                    $category->$key = $value;
                }
				
				$category->created_at = $date;
				$category->updated_at = $date;
				
				$category->rawQuery("UPDATE category SET sorting = sorting + 1 WHERE `parent_id` = ".$category->parent_id." AND sorting >= ".$category->sorting);
                
                $id = $category->save();
                if(!$id){
                    responseFail($error, json_encode($category->errors[0]));
                    break;
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
				rebuildXunSearch();

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $category = $category->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('name', $params)){
					if($category->parent_id == 0 && $params['name'] != $category->name){
						responseFail($error, "cannot edit main category name");
                    	break;
					}
				}
				
				if(array_key_exists('sorting', $params) && $params['sorting'] != $category->sorting){
					$category->rawQuery("UPDATE category SET sorting = sorting + 1 WHERE `parent_id` = ".$category->parent_id." AND sorting >= ".$params['sorting']);
				}
                
                foreach($params as $key => $value){
                    $category->$key = $value;
                }

                $category->updated_at = $date;
                $category->save();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
				
				rebuildXunSearch();

                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $category = $category->byId($params['id']);
                $category->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
				
				rebuildXunSearch();
				
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					if(array_key_exists('id', $data)){
						$category = $category->byId($data['id']);
					}else{
						$category->isNew = true;
						
						$category->created_at = $date;
					}
					
					if($action == 'delete'){
						$category->delete();
						continue;
					}
					
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
						
					foreach($data as $key => $value){
						$category->$key = $value;
					}

					$category->updated_at = $date;
					$id = $category->save();
				}
                
				$response['redirect'] = true;
				
				rebuildXunSearch();
				
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

	function rebuildXunSearch(){
		include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");

        $XS = new XS('category');
        $index = $XS->index;

        $index->clean();

        $category = new category();
        $category_result = $category->where('parent_id', '0', '>')->get();

        foreach($category_result as $categoryObj){
			$doc = new XSDocument();
			$doc->setFields(array("id" => $categoryObj->id, "parent_id"=>$categoryObj->parent_id, "display"=>$categoryObj->display));

			$res = $index->add($doc);
        }
	}

?>