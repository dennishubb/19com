<?php

	$input = $params;

    include_once('model/upload.php');
	include_once('model/level.php');
	include_once('model/user.php');
	include_once('model/promotion_redeem.php');
	include_once('model/credit.php');

    do{
        
        $promotion = new promotion();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $promotion->adminFields : $promotion->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $promotionObj = $promotion->with('upload_small')->with('upload_medium')->with('upload_big')->byId($params['id'], $selectFields);
					$return = $promotionObj->data; 
					if($promotionObj->upload_small){
                        $return['upload_small_url'] = $promotionObj->upload_small->url;
						unset($return['upload_small']);
                    }
					if($promotionObj->upload_medium){
                        $return['upload_medium_url'] = $promotionObj->upload_medium->url;
						unset($return['upload_medium']);
                    }
                    if($promotionObj->upload_big){
                        $return['upload_big_url'] = $promotionObj->upload_big->url;
						unset($return['upload_big']);
                    }
					$return['level_id'] = json_decode($promotionObj->level_id);
					
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
							
                            $promotion->where($promotion->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $promotion->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$promotion->where($field, $value, $operator);
                        }
                    }
                    
					$promotion_copy = $promotion->db->copy();
                    $result = $promotion->with('upload_small')->with('upload_medium')->with('upload_big')->get($limit, $selectFields);
					$promotion->db = $promotion_copy;
					$totalRecords = $promotion->with('upload_small')->with('upload_medium')->with('upload_big')->getValue("count(promotion.id)");
                    
                    $promotion_array = array();
                    if($result){
                        foreach($result as $promotionObj){
							$return = $promotionObj->data;
							if($promotionObj->upload_small){
								$return['upload_small_url']  = $promotionObj->upload_small->url;
								$return['upload_small_data'] = $promotionObj->upload_small->data;
								unset($return['upload_small']);
							}
							if($promotionObj->upload_medium){
								$return['upload_medium_url']  = $promotionObj->upload_medium->url;
								$return['upload_medium_data'] = $promotionObj->upload_medium->data;
								unset($return['upload_medium']);
							}
							if($promotionObj->upload_big){
								$return['upload_big_url']  = $promotionObj->upload_big->url;
								$return['upload_big_data'] = $promotionObj->upload_big->data;
								unset($return['upload_big']);
							}
							$return['level_id'] = json_decode($promotionObj->level_id);
                            $promotion_array[] = $return;
                        } 
                    }

                    $response['data']   = $promotion_array;
                    
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
				
				if(array_key_exists('upload_url_big', $params) || array_key_exists('upload_url_medium', $params) || array_key_exists('upload_url_small', $params)){
                    uploadId($params);
                }
                
                foreach($params as $key => $value){
					if($key == 'level_id'){
						$value = json_encode($value); 
					}
					
                    $promotion->$key = $value;
                }

                $promotion->created_at = $date;
                $promotion->updated_at = $date;
				
				$promotion->rawQuery("UPDATE promotion SET sorting = sorting + 1 WHERE `system` != ".($promotion->system == 0 ? 1 : 0)." AND sorting >= ".$promotion->sorting);
                
                $id = $promotion->save();
                if(!$id){
                    responseFail($error, $promotion->getLastError());
                    break;
                }
				
				if($promotion->sign_up == 1){
					$credit		= new credit();
					$user = new user();
					$points_id 	= $credit->where('name', 'points')->getValue('id');
					$voucher_id	= $credit->where('name', 'voucher')->getValue('id');
					$payout_id	= $user->where('name', 'payout')->getValue('id');
					
					$promotion_redeem = new promotion_redeem();
					$results = $user->where('type', 'Member')->where('deleted', 0)->where('disabled', 0)->get(null, array('id'));
					foreach($results as $userObj){
						$promotion_redeem->isNew 		= true;
						$promotion_redeem->promotion_id = $id;
						$promotion_redeem->user_id 		= $userObj->id;
						$promotion_redeem->status 		= 'approve';
						$promotion_redeem->created_at 	= $date;
						$promotion_redeem_id = $promotion_redeem->save();
						
						if($promotion->points > 0){
							insertTransaction($userObj->id, $promotion->points, $promotion_redeem_id, $points_id, $payout_id, $userObj->id, $date, $promotion->name);
						}
						
						if($promotion->voucher > 0){
							insertTransaction($userObj->id, $promotion->voucher, $promotion_redeem_id, $voucher_id, $payout_id, $userObj->id, $date, $promotion->name);
						}
					}
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
                $promotion = $promotion->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('upload_url_big', $params) || array_key_exists('upload_url_medium', $params) || array_key_exists('upload_url_small', $params)){
                    uploadId($params);
                }
				
				if(array_key_exists('sorting', $params)){
					if($params['sorting'] != $promotion->sorting){
						$promotion->rawQuery("UPDATE promotion SET sorting = sorting + 1 WHERE `system` != ".($promotion->system == 0 ? 1 : 0)." AND sorting >= ".$params['sorting']);
					}
				}
                
                foreach($params as $key => $value){
					if($key == 'level_id'){
						$value = json_encode($value); 
					}
					
                    $promotion->$key = $value;
                }

                $promotion->updated_at = $date;
                $promotion->save();
                
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
                $promotion = $promotion->byId($params['id']);
                $promotion->delete();
                
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
						$promotion = $promotion->byId($data['id']);
						$promotion->updated_at = $date;
					}else{
						$promotion->isNew = true;
						$promotion->created_at = $date;
					}
					
					if($action == 'delete'){
						$promotion->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$promotion->$key = $value;
					}

					$id = $promotion->save();
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
			
			$params['upload_id_'.$key] = $upload_id;
        };
		
        unset($params['image_data']);
    }

	function uploadId(&$params){
		$upload	= new upload();
		
		if(array_key_exists('upload_url_big', $params)){
			$uploadObjBig = $upload->where('url', $params['upload_url_big'])->getOne();

			if($uploadObjBig){
				$params['upload_id_big']	= $uploadObjBig->id;
			}
		}

		if(array_key_exists('upload_url_medium', $params)){
			$uploadObjMedium = $upload->where('url', $params['upload_url_medium'])->getOne();

			if($uploadObjMedium){
				$params['upload_id_medium']	= $uploadObjMedium->id;
			}
		}
		
		if(array_key_exists('upload_url_small', $params)){
			$uploadObjSmall = $upload->where('url', $params['upload_url_small'])->getOne();

			if($uploadObjSmall){
				$params['upload_id_small']	= $uploadObjSmall->id;
			}
		}
	}

?>