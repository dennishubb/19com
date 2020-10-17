<?php

	$input = $params;

    do{
        
        $prediction_stats = new prediction_stats();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Member' ? $prediction_stats->memberFields : $prediction_stats->adminFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $prediction_statsObj = $prediction_stats->byId($params['id'], $selectFields);
                    $return = $prediction_statsObj->data;
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
                        	$prediction_stats->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$prediction_stats->orderBy($data['field'], $data['sort']);
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
							
							$prediction_stats->where($field, $value, $operator);
                        }
                    }
                    
					$prediction_stats_copy = $prediction_stats->db->copy();
                    $result = $prediction_stats->get($limit, $selectFields);
					$prediction_stats->db = $prediction_stats_copy;
					$totalRecords = $prediction_stats->getValue("count(prediction_stats.id)");
                    
                    $prediction_stats_array = array();
                    if($result){
                        foreach($result as $prediction_statsObj){
                            $return = $prediction_statsObj->data;
							
							if($prediction_statsObj->prediction_count == 0 || $prediction_statsObj->prediction_total_count == 0){
								$return['prediction_participation_rate'] = 0;
							}else{
								$return['prediction_participation_rate'] = ($prediction_statsObj->prediction_count/$prediction_statsObj->prediction_total_count) * 100;
							}
							
							if($prediction_statsObj->top_ten_count == 0 || $prediction_statsObj->top_ten_total_count == 0){
								$return['top_ten_rate'] = 0;
							}else{
								$return['top_ten_rate'] = ($prediction_statsObj->top_ten_count/$prediction_statsObj->top_ten_total_count) * 100;
							}
							
                            $prediction_stats_array[] = $return;
                        } 
                    }

                    $response['data']   = $prediction_stats_array;
                    
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
//				//check points balance, event prediction_stats_end_at
//                
//                foreach($params as $key => $value){
//                    $prediction_stats->$key = $value;
//                }
//
//                $prediction_stats->created_at 	= $date;
//                $prediction_stats->updated_at 	= $date;
//
//                $id = $prediction_stats->save();
//                if(!$id){
//                    responseFail($error, $prediction_stats->getLastError());
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
				
                $prediction_stats = $prediction_stats->byId($params['id']);
                
                foreach($params as $key => $value){
                    $prediction_stats->$key = $value;
				}

                $prediction_stats->updated_at = $date;
                $prediction_stats->save();
                
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
                $prediction_stats = $prediction_stats->byId($params['id']);
                $prediction_stats->delete();
                
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
						$prediction_stats = $prediction_stats->byId($data['id']);
					}else{
						$prediction_stats->isNew = true;
						
						$prediction_stats->created_at = $date;
					}
					
					if($action == 'delete'){
						$prediction_stats->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$prediction_stats->$key = $value;
					}

					$prediction_stats->updated_at = $date;
					$id = $prediction_stats->save();
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