<?php

	include_once('model/promotion.php');
	include_once('model/user.php');
	include_once('model/credit.php');

	$input = $params;

    do{

        $promotion_redeem = new promotion_redeem();
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
                    $promotion_redeemObj = $promotion_redeem->with('user')->with('admin')->with('promotion')->byId($params['id']);
                    $return = $promotion_redeemObj->data;
                    if($promotion_redeemObj->user){
                        $return['user_data'] = $promotion_redeemObj->user->data;
						unset($return['user']);
                    }
					if($promotion_redeemObj->admin){
                        $return['admin_data'] = $promotion_redeemObj->admin->data;
						unset($return['admin']);
                    }
					if($promotion_redeemObj->promotion){
                        $return['promotion_data'] = $promotion_redeemObj->promotion->data;
						unset($return['promotion']);
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
							
                            $promotion_redeem->where($promotion_redeem->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $promotion_redeem->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$promotion_redeem->where($field, $value, $operator);
                        }
                    }

					$promotion_redeem_copy = $promotion_redeem->db->copy();
                    $result = $promotion_redeem->with('admin')->with('user')->with('promotion')->get($limit);
					$promotion_redeem->db = $promotion_redeem_copy;
					$totalRecords = $promotion_redeem->with('admin')->with('user')->with('promotion')->getValue("count(promotion_redeem.id)");
                    
                    $promotion_redeem_array = array();
                    if($result){
                        foreach($result as $promotion_redeemObj){
                            $return = $promotion_redeemObj->data;
							if($promotion_redeemObj->user){
								$return['user_data'] = $promotion_redeemObj->user->data;
								unset($return['user']);
							}
							if($promotion_redeemObj->admin){
								$return['admin_data'] = $promotion_redeemObj->admin->data;
								unset($return['admin']);
							}
							if($promotion_redeemObj->promotion){
								$return['promotion_data'] = $promotion_redeemObj->promotion->data;
								unset($return['promotion']);
							}
                            $promotion_redeem_array[] = $return;
                        } 
                    }

                    $response['data'] = $promotion_redeem_array;

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
				
				if(!array_key_exists('promotion_id', $params)){
					responseFail($error, '升级无效');
                    break;
				}
				
				$promotion		= new promotion();
				$promotionObj	= $promotion->byId($params['promotion_id']);
				if(!$promotionObj){
					responseFail($error, '升级无效');
                    break;
				}
				
				if(strtotime($promotionObj->end_at) < time()){
					responseFail($error, '促销截止日期');
                    break;
				}
				
				//check level
				if($promotionObj->level_id){
					$user		= new user();
					$userObj	= $user->byId($login_user->id);
					
					$level_id	= json_decode($promotionObj->level_id);
					if(is_array($level_id) && count($level_id) > 0 && !in_array($userObj->level_id, $level_id)){
						responseFail($error, '无效等级');
                    	break;
					}
				}
				
				//if once, check if redeem before
				if($promotionObj->limitation == 'once'){
					$promotion_redeem_id = $promotion_redeem->where('promotion_id', $promotionObj->id)->where('user_id', $login_user->id)->getValue('id');
					if($promotion_redeem_id){
						responseFail($error, '促销已兑换');
                    	break;
					}
				}
				
				//if daily, check if a day has past since last redeem, and check for limitation count
				if($promotionObj->limitation == 'daily' || $promotionObj->limitation == 'monthly'){
					//check for limitation count first
					$promotion_redeem_count = $promotion_redeem->where('promotion_id', $promotionObj->id)->where('user_id', $login_user->id)->getValue('count(id)');
					if($promotion_redeem_count >= $promotionObj->limitation_count){
						responseFail($error, '达到限制');
						break;
					}
					
					$promotion_redeemObj = $promotion_redeem->where('promotion_id', $promotionObj->id)->where('user_id', $login_user->id)->orderBy('created_at', 'DESC')->getOne();
					if($promotion_redeemObj){

						
						if($promotionObj->limitation == 'daily'){
							$previous_redeem_date = new DateTime(date('Y-m-d', strtotime($promotion_redeemObj->created_at)));
							$current_redeem_date = new DateTime(date('Y-m-d', strtotime($date)));
							$difference = $current_redeem_date->diff($previous_redeem_date);
							if($difference->d == 0){
								responseFail($error, '今天已经兑换了，明天再来');
								break;
							}
						}
						
						if($promotionObj->limitation == 'monthly'){
							$previous_month	= date('m', strtotime($promotion_redeemObj->created_at));
							$current_month	= date('m', strtotime($date));
							if($previous_month == $current_month){
								responseFail($error, '本月已经兑换过，下个月再来');
								break;
							}
						}
						
					}
				}
				
                foreach($params as $key => $value){
                    $promotion_redeem->$key = $value;
                }
				
				if($login_user->type == 'Member'){
					$promotion_redeem->user_id 	= $login_user->id;
				}

				$promotion_redeem->status	= 'pending';
                $promotion_redeem->created_at = $date;
                $promotion_redeem->updated_at = $date;

                $id = $promotion_redeem->save();
                if(!$id){
                    responseFail($error, $promotion_redeem->getLastError());
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
                $promotion_redeem = $promotion_redeem->byId($params['id']);
				
				if($promotion_redeem->status == 'approve' || $promotion_redeem->status == 'approve'){
					responseFail($error, "promotion is already redeemed");
                    break;
				}
				
                foreach($params as $key => $value){
                    $promotion_redeem->$key = $value;
                }

                $promotion_redeem->updated_at = $date;
				$promotion_redeem->admin_id	  = $login_user->id ? $login_user->id : 0;
				
                $id = $promotion_redeem->save();
                if(!$id){
                    responseFail($error, $promotion_redeem->getLastError());
                }
				
				if($params['status'] == 'approve'){
					//approve, send points and voucher to user
					$promotion		= new promotion();
					$promotionObj	= $promotion->byId($promotion_redeem->promotion_id);
					
					if($promotionObj){
						$points_id 	= $credit->where('name', 'points')->getValue('id');
						$voucher_id	= $credit->where('name', 'voucher')->getValue('id');
						$payout_id	= $user->where('name', 'payout')->getValue('id');
						
						if($promotionObj->points > 0){
							insertTransaction($promotion_redeem->user_id, $promotion_redeem->points, $promotion_redeem->id, $points_id, $payout_id, $params['user_id'], $promotion_redeem->user_id, $date, $promotionObj->name);
						}
						if($promotionObj->voucher > 0){
							insertTransaction($promotion_redeem->user_id, $promotion_redeem->voucher, $promotion_redeem->id, $voucher_id, $payout_id, $params['user_id'], $promotion_redeem->user_id, $date, $promotionObj->name);
						}
					}
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
                $promotion_redeem = $promotion_redeem->byId($params['id']);
                $promotion_redeem->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$promotion_redeem = $promotion_redeem->byId($data['id']);
					foreach($data as $key => $value){
						$promotion_redeem->$key = $value;
					}

					if($action == 'delete'){
						$promotion_redeem->delete();
					}else{
						$promotion_redeem->updated_at = $date;
						$id = $promotion_redeem->save();
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