<?php

	include_once('model/gift.php');
	include_once('model/user.php');
	include_once('model/credit.php');

	$input = $params;

    do{

        $gift_redeem = new gift_redeem();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $gift_redeem->adminFields : $gift_redeem->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                if(array_key_exists('id', $params)){
                    $gift_redeemObj = $gift_redeem->with('user')->with('admin')->with('gift')->byId($params['id']);
                    $return = $gift_redeemObj->data;
                    if($gift_redeemObj->user){
                        $return['user_data'] = $gift_redeemObj->user->data;
                    }
					if($gift_redeemObj->admin){
                        $return['admin_data'] = $gift_redeemObj->admin->data;
                    }
					if($gift_redeemObj->gift){
                        $return['gift_data'] = $gift_redeemObj->gift->data;
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
                    
//                    if(array_key_exists('search', $params)){
//                        foreach($params['search'] as $key => $value){
//							if(strlen($value) < 0){
//								continue;
//							}
//							
//                            $gift_redeem->where($gift_redeem->dbTable.".".$key, "%$value%", 'LIKE');
//                        }
//                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $gift_redeem->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$gift_redeem->where($field, $value, $operator);
                        }
                    }

					$gift_redeem_copy = $gift_redeem->db->copy();
                    $result = $gift_redeem->with('user')->with('admin')->with('gift')->get($limit, $selectFields);
					$gift_redeem->db = $gift_redeem_copy;
					$totalRecords = $gift_redeem->with('user')->with('admin')->with('gift')->getValue("count(gift_redeem.id)");
						
                    $gift_redeem_array = array();
                    if($result){
                        foreach($result as $gift_redeemObj){
                            $return = $gift_redeemObj->data;
							if($gift_redeemObj->user){
								$return['user_data'] = $gift_redeemObj->user->data;
								unset($return['user']);
							}
							if($gift_redeemObj->admin){
								$return['admin_data'] = $gift_redeemObj->admin->data;
								unset($return['admin']);
							}
							if($gift_redeemObj->gift){
								$return['gift_data'] = $gift_redeemObj->gift->data;
								unset($return['gift']);
							}
							
                            $gift_redeem_array[] = $return;
                        } 
                    }

                    $response['data'] = $gift_redeem_array;

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
                    $gift_redeem->$key = $value;
                }

				$gift_redeem->user_id 	= $login_user->id;
				
				if($gift_redeem->quantity <= 0){
					responseFail($error, "Invalid");
				}
				
				$gift = new gift();
				$gift = $gift->byId($gift_redeem->gift_id);
				if(!$gift){
					responseFail($error, "失效礼品"); //invalid gift
                    break;
				}
				
				$credit = new credit();
				$credit_id = $credit->where('disabled', '0')->where('gift_redeem', '1')->getValue('id');
				
				$points = $gift->points * $gift_redeem->quantity;
				$balance = getBalance($gift_redeem->user_id, $credit_id);
				if($balance < $points){
					responseFail($error, "积分不足"); //insufficient points balance
                    break;
				}
				
				if(strlen($login_user->name) == 0){
					responseFail($error, "请输入您的姓名", 0, null, true); //please input name
                    break;
				}
				
				if(strlen($login_user->address) == 0){
					responseFail($error, "请输入您的地址", 0, null, true); //please input address
                    break;
				}
				
				if(strlen($login_user->phone) == 0){
					responseFail($error, "请输入您的手机号", 0, null, true); //please input phone
                    break;
				}
				
				$gift_redeem->name	  	= $login_user->name;
				$gift_redeem->address	= $login_user->address;
				$gift_redeem->phone		= $login_user->phone;
				$gift_redeem->status	= 'pending';
				
                $gift_redeem->created_at = $date;
                $gift_redeem->updated_at = $date;

                $id = $gift_redeem->save();
                if(!$id){
                    responseFail($error, $gift_redeem->getLastError());
                    break;
                }
				
				$user = new user();
				$gift_redeem_id = $user->where('username', 'gift redeem')->getValue('id');
				
				insertTransaction($gift_redeem->user_id, $points, $id, $credit_id, $gift_redeem->user_id, $gift_redeem_id, '', "gift redeem");
                
                $response['data'] = $id;
				$response['redirect'] = true;
				$response['message'] = '成功下单';
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $gift_redeem = $gift_redeem->byId($params['id']);
				
                foreach($params as $key => $value){
                    $gift_redeem->$key = $value;
                }

				$gift_redeem->admin_id	 = $login_user->id;
                $gift_redeem->updated_at = $date;
				
                $id = $gift_redeem->save();
                if(!$id){
                    responseFail($error, $gift_redeem->getLastError());
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
				if($gift_redeem->status == 'approve'){
					$user = new user();
					$user->updateCustom(array('gift_redeem_count' => $user->db->inc(1)), array('id' => $gift_redeem->user_id));
				}
				
                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $gift_redeem = $gift_redeem->byId($params['id']);
                $gift_redeem->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$gift_redeem = $gift_redeem->byId($data['id']);
					foreach($data as $key => $value){
						$gift_redeem->$key = $value;
					}

					if($action == 'delete'){
						$gift_redeem->delete();
					}else{
						$gift_redeem->updated_at = $date;
						$id = $gift_redeem->save();
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

?>