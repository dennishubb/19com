<?php

	include_once('model/credit.php');
	include_once('model/transaction.php');
	include_once('model/accounting.php');
	include_once('model/user.php');
	include_once('model/credit.php');

	$input = $params;

    do{

        $adjustment = new adjustment();
		$credit		= new credit();
		$user 		= new user();
		
		$adjustment_id  = $user->where('name', 'adjustment')->getValue('id');		
		$credit_array	= $credit->where('adjustment', '1')->get();
		
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Member' ? $adjustment->memberFields : $adjustment->adminFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $adjustmentObj = $adjustment->with('user')->with('admin')->byId($params['id'], $selectFields);
                    $return = $adjustmentObj->data;
                    if($adjustmentObj->user){
                        $return['user_data'] = $adjustmentObj->user->data;
                    }
					if($adjustmentObj->admin){
                        $return['admin'] = $adjustmentObj->admin->name;
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
							
                            $adjustment->where($adjustment->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $adjustment->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$adjustment->where($field, $value, $operator);
                        }
                    }

					$adjustment_copy = $adjustment->db->copy();
                    $result = $adjustment->with('user')->with('admin')->get($limit, $selectFields);
					$adjustment->db = $adjustment_copy;
					$totalRecords = $adjustment->with('user')->with('admin')->getValue("count(adjustment.id)");
                    
                    $adjustment_array = array();
                    if($result){
                        foreach($result as $adjustmentObj){
                            $return = $adjustmentObj->data;
							if($adjustmentObj->user){
								$return['user_data'] = $adjustmentObj->user->data;
								unset($return['user']);
							}
							if($adjustmentObj->admin){
								$return['admin'] = $adjustmentObj->admin->name;
							}
							
							$points_balance = getBalance($adjustmentObj->user_id, $adjustmentObj->points_id);
							$return['points_balance'] = $points_balance;
							
                            $adjustment_array[] = $return;
                        } 
                    }

                    $response['data'] = $adjustment_array;

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
				
				//check balance
				foreach($credit_array as $creditObj){
					$credit_name	= $creditObj->name;
					$credit_amount 	= $params[$credit_name];
					$credit_id		= $params[$credit_name."_id"];
					
					$balance = checkBalance($params['user_id'], $credit_id, $credit_amount);
					
					if($credit_amount < 0 && $balance === false){
						responseFail($error, "Insufficient $credit_name balance");
						break 2;
					}
					
					$params[$credit_name.'_before'] 	= $balance;
					$params[$credit_name.'_after'] 		= $balance + $credit_amount;
				}
				
				$adjustment->updateCustom(array('latest' => 0), array('user_id' => $params['user_id'], 'latest' => 1));

                foreach($params as $key => $value){
                    $adjustment->$key = $value;
                }

				$adjustment->admin_id	= $login_user->id ? $login_user->id : 0;
				$adjustment->latest 	= 1;
                $adjustment->created_at = $date;

                $id = $adjustment->save();
                if(!$id){
                    responseFail($error, $adjustment->getLastError());
                    break;
                }
				
				foreach($credit_array as $creditObj){
					$credit_name	= $creditObj->name;
					$credit_amount 	= $params[$credit_name];
					$credit_id		= $params[$credit_name."_id"];
					
					if($credit_amount == 0) continue;
					
					if($credit_amount < 0){
						insertTransaction($params['user_id'], abs($credit_amount), $id, $credit_id, $params['user_id'], $adjustment_id, $params['remark'], "adjustment");
					}else{
						insertTransaction($params['user_id'], abs($credit_amount), $id, $credit_id, $adjustment_id, $params['user_id'], $params['remark'], "adjustment");
					}
				}
                
                $response['data'] = $id;
				$response['redirect'] = true;
				
				$user->updateCustom(array('adjustment_count' => $user->db->inc(1)), array('id' => $adjustment->user_id));
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $adjustment = $adjustment->byId($params['id']);
				
                foreach($params as $key => $value){
                    $adjustment->$key = $value;
                }

				$adjustment->admin_id	= $login_user->id ? $login_user->id : 0;
                $adjustment->updated_at = $date;
                $id = $adjustment->save();
                if(!$id){
                    responseFail($error, $adjustment->getLastError());
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
                $adjustment = $adjustment->byId($params['id']);
                $adjustment->delete();
                
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
						$adjustment = $adjustment->byId($data['id']);
					}else{
						
						//check balance
						foreach($credit_array as $creditObj){
							$credit_name	= $creditObj->name;
							$credit_amount 	= $data[$credit_name];
							$credit_id		= $data[$credit_name."_id"];

							$balance = checkBalance($data['user_id'], $credit_id, $credit_amount);

							if($credit_amount < 0 && $balance === false){
								continue 2;
							}

							$data[$credit_name.'_before'] 	= $balance;
							$data[$credit_name.'_after'] 	= $balance + $credit_amount;
						}
						
						$adjustment->updateCustom(array('latest' => 0), array('user_id' => $data['user_id'], 'latest' => 1));
						
						$adjustment->isNew = true;
						$adjustment->created_at = $date;
						$adjustment->latest = 1;
						$adjustment->admin_id = $login_user->id ? $login_user->id : 0;
					}
					
					if($action == 'delete'){
						$adjustment->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$adjustment->$key = $value;
					}

					$id = $adjustment->save();
					
					if(!array_key_exists('id', $data)){
						foreach($credit_array as $creditObj){
							$credit_name	= $creditObj->name;
							$credit_amount 	= $data[$credit_name];
							$credit_id		= $data[$credit_name."_id"];

							if($credit_amount == 0) continue;

							if($credit_amount < 0){
								insertTransaction($data['user_id'], abs($credit_amount), $id, $credit_id, $data['user_id'], $adjustment_id, $data['remark'], "adjustment");
							}else{
								insertTransaction($data['user_id'], abs($credit_amount), $id, $credit_id, $adjustment_id, $data['user_id'], $data['remark'], "adjustment");
							}
						}
						
						$user->updateCustom(array('adjustment_count' => $user->db->inc(1)), array('id' => $data['user_id']));
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

	function checkBalance($user_id, $credit_id, $amount){
		$credit_balance = getBalance($user_id, $credit_id);
		if($credit_balance < abs($amount)){
			return false;
		}
		
		return $credit_balance;
	}
?>