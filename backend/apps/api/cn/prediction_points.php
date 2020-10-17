<?php

	$input = $params;

    do{
        
        $prediction_points = new prediction_points();
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
                    $prediction_pointsObj = $prediction_points->byId($params['id']);
                    $return = $prediction_pointsObj->data;
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
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$prediction_points->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_points->orderBy($data['field'], $data['sort']);
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
							
							$prediction_points->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_points_copy = $prediction_points->db->copy();
                    $result = $prediction_points->get($limit);
					$prediction_points->db = $prediction_points_copy;
					$totalRecords = $prediction_points->getValue("count(prediction_points.id)");
                    
                    $prediction_points_array = array();
                    if($result){
                        foreach($result as $prediction_pointsObj){
                            $return = $prediction_pointsObj->data;
                            $prediction_points_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_points_array;
                    
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
//                $params = validate($params, $additional_fields);
//                if(is_string($params)){
//                    responseFail($error, $params);
//                    break;
//                }
//				
//				//check points balance, event prediction_points_end_at
//                
//                foreach($params as $key => $value){
//                    $prediction_points->$key = $value;
//                }
//
//                $prediction_points->created_at 	= $date;
//                $prediction_points->updated_at 	= $date;
//
//                $id = $prediction_points->save();
//                if(!$id){
//                    responseFail($error, $prediction_points->getLastError());
//                    break;
//                }
//                
//                $response['id'] = $id;
//				$response['redirect'] = true;

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $prediction_points = $prediction_points->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_points->$key = $value;
				}

                $prediction_points->updated_at = $date;
                $prediction_points->save();
                
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
                $prediction_points = $prediction_points->byId($params['id']);
                $prediction_points->delete();
                
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
						$prediction_points = $prediction_points->byId($data['id']);
					}else{
						$prediction_points->isNew = true;
						
						$prediction_points->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_points->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_points->$key = $value;
					}

					$prediction_points->updated_at = $date;
					$id = $prediction_points->save();
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