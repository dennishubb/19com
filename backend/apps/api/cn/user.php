<?php

    include_once('model/role.php');
	include_once('model/credit.php');
	include_once('model/adjustment.php');
	include_once('model/prediction.php');
	include_once('model/promotion.php');
	include_once('model/promotion_redeem.php');
	include_once('model/user_level_up.php');
	include_once('model/transaction.php');

    do{

        $user 	= new user();
		$credit	= new credit();
        $date 	= date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Member' ? $user->memberFields : $user->adminFields;
				
				$credits = $credit->where('disabled', '0')->get();
				foreach($credits as $creditObj){
					$creditArray[$creditObj->id] = $creditObj->name;
				}
				
				$user->where('user.system', '0');
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
					//do not allow other user to get other user data
					if($login_user->type == 'Member'){
						if($login_user->id != $params['id']){
							responseFail($error, "forbidden", 403);
							break;
						}
					}
					
                    $userObj = $user->with('role')->with('level')->with('upload')->byId($params['id'], $selectFields);
                    $return = $userObj->data;
                    $return['role'] = "";
                    if($userObj->role){
                        $return['role'] = $userObj->role->name;
                    }
					if($userObj->level){
						$return['level'] = $userObj->level->name;
					}
					if($userObj->upload){
						$return['upload_url'] = $userObj->upload->url;
						unset($return['upload']);
					}
					
					$total_points_this_week 	= 0;
					$total_voucher_this_week	= 0;
					$transaction = new transaction();
					$start_date = date('Y-m-d H:i:s', strtotime('monday this week'));
					$user_transactions = $transaction->where('to_id', $userObj->id)->where('created_at', $start_date, '>=')->get();
					if($user_transactions){
						foreach($user_transactions as $transactionObj){
							if($transactionObj->credit_id == 1){
								$total_points_this_week 	+= $transactionObj->amount;
							}else{
								$total_voucher_this_week 	+= $transactionObj->amount;
							}
							
						}
					}
					
					$return['total_points_this_week'] 	= $total_points_this_week;
					$return['total_voucher_this_week'] 	= $total_voucher_this_week;
					
                    unset($return['password']);
                    unset($return['token']);
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
							
							if($key == 'id'){
								if($login_user->type == 'Member'){
									if($login_user->id != $value){
										responseFail($error, "forbidden", 403);
										break;
									}
								}
							}
							
                            $user->where($user->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
                        $user->orderBy($sorting_data['field'], $sorting_data['sort']);               
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							if($field == 'id'){
								if($login_user->type == 'Member'){
									if($login_user->id != $value){
										responseFail($error, "forbidden", 403);
										break;
									}
								}
							}
							
							$user->where($field, $value, $operator);
                        }
                    }
					
					$user_copy	= $user->db->copy();
                    $result = $user->with('role')->with('level')->with('upload')->get($limit, $selectFields);
					$user->db = $user_copy;
					$totalRecords = $user->with('role')->with('level')->with('upload')->getValue("count(user.id)");
                    
                    $user_array = array();
                    if($result){
                        foreach($result as $userObj){
                            $return = $userObj->data;
                            $return['role'] = "";
                            if($userObj->role){
                                $return['role'] = $userObj->role->name;
                            }
							if($userObj->level){
                                $return['level'] = $userObj->level->name;
                            }
							if($userObj->upload){
								$return['upload_url'] = $userObj->upload->url;
								unset($return['upload']);
							}
							
                            unset($return['password']);
                            unset($return['token']);
                            $user_array[] = $return;
                        } 
                    }

                    $response['data'] = $user_array;

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
				
				if(!array_key_exists('type', $params)){
					responseFail($error, "invalid parameters");
					break;
				}
				
				$typeArray	= array('Member', 'Admin');
				if(!in_array($params['type'], $typeArray)){
					responseFail($error, "invalid parameters");
					break;
				}
				
				if($params['from'] == 'Member'){
					if(!array_key_exists('captcha', $params)){
						responseFail($error, "验证码错误，请再次输入");
						break;
					}

					if(!isset($_SESSION['captcha'])){
						responseFail($error, "验证码错误，请再次输入");
						break;
					}

					if ($_SESSION['captcha'] !== $params['captcha']){
						responseFail($error, "验证码错误，请再次输入");
						break;
					}
				}

				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
                foreach($params as $key => $value){
                    if($key == 'password'){
                        $value = PasswordHasher::HashPassword($value);
                    }
                    $user->$key = $value;
                }
				
				if(array_key_exists('username', $params) && strlen($params['username']) > 0){
					$user_exist = $user->where('username', $params['username'])->getValue('id');
					if($user_exist){
						responseFail($error, "用户名已存在");
						break;
					}
				}
				
				if(array_key_exists('phone', $params) && strlen($params['phone']) > 0){
					$user_exist = $user->where('phone', $params['phone'])->getValue('id');
					if($user_exist){
						responseFail($error, "手机号已存在");
						break;
					}
				}

                $user->created_at 	= $date;
                $user->updated_at 	= $date;
				
				if(strtolower($user->type) == 'member'){
					$user->level_id		= 1;
				}

                $id = $user->save();
                if(!$id){
                    responseFail($error, $user->getLastError());
                    break;
                }
				
				if(strtolower($user->type) == 'member'){
					//create empty adjustment record
					$adjustment 			= new adjustment();
					$adjustment->user_id 	= $id;
					$adjustment->latest		= 1;
					$adjustment->created_at = $date;
					$adjustment->save();
					
					//check for sign up promotion
					$credit		= new credit();
					$points_id 	= $credit->where('name', 'points')->getValue('id');
					$voucher_id	= $credit->where('name', 'voucher')->getValue('id');
					$payout_id	= $user->where('name', 'payout')->getValue('id');
					
					$promotion	= new promotion();
					$promotion_result = $promotion->where("(limitation = 'sign up' OR sign_up = 1)")->where('disabled', 0)->where('start_at', $date, '<')->where('end_at', $date, '>')->get();
					
					$promotion_redeem	= new promotion_redeem();
					if($promotion_result){
						foreach($promotion_result as $promotionObj){
							$points 	= $promotionObj->points;
							$voucher	= $promotionObj->voucher;

							$promotion_redeem->isNew 		= 1;
							$promotion_redeem->promotion_id	= $promotionObj->id;
							$promotion_redeem->user_id		= $id;
							$promotion_redeem->created_at	= $date;
							$promotion_redeem->status		= 'approve';
							$promotion_redeem_id			= $promotion_redeem->save();

							if($points > 0){
								insertTransaction($id, $points, $promotion_redeem_id, $points_id, $payout_id, $id, $date, $promotionObj->name);
							}

							if($voucher > 0){
								insertTransaction($id, $voucher, $promotion_redeem_id, $voucher_id, $payout_id, $id, $date, $promotionObj->name);
							}
						}
					}

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
                $user = $user->byId($params['id']);
				
				//do not allow deleting system row
				if($user->system){
					responseFail($error, 'action forbidden');
					break;
				}
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
				
				if(array_key_exists('old_password', $params)){
					if (!PasswordHasher::VerifyHashedPassword($login_user->password, $params['old_password'])) {
						responseFail($error, "密码错误");
						break;
					}
				}
				
                foreach($params as $key => $value){
                    if($key == 'password'){
                        $value = PasswordHasher::HashPassword($value);
                    }
                    $user->$key = $value;
                }
				
				if(array_key_exists('username', $params) && strlen($params['username']) > 0){
					$user_exist = $user->where('username', $params['username'])->getValue('id');
					if($user_exist){
						responseFail($error, "用户名已存在");
						break;
					}
				}
				
				if(array_key_exists('phone', $params) && strlen($params['phone']) > 0){
					$user_exist = $user->where('phone', $params['phone'])->where('id', $params['id'], '!=')->getValue('id');
					if($user_exist){
						responseFail($error, "手机号已存在");
						break;
					}
				}
				
                $user->updated_at = $date;
                $id = $user->save();
                if(!$id){
                    responseFail($error, $user->getLastError());
                }
				
				if(strtolower($user->type) == 'member'){
					$promotion		= new promotion();
					$promotionObj	= $promotion->where('name', 'profile update')->where('disabled', 0)->where('start_at', $date, '<')->where('end_at', $date, '>')->getOne();
					
					if($promotionObj){
						$promotion_redeem		= 	new promotion_redeem();
						$promotion_redeem_id	=	$promotion_redeem->where('promotion_id', $promotionObj->id)->where('user_id', $user->id)->getValue('id');
						//check if they've redeemed the promo before
						if(!$promotion_redeem_id){
							//check if all profile is updated
							if(strlen($user->email) > 0 && strlen($user->address) > 0 && strlen($user->name) > 0 && strlen($user->phone) > 0 && strlen($user->birth_at) != '00-00-0000 00:00:00' && strlen($user->weibo) > 0){
								//check for update profile promotion
								$credit		= new credit();
								$points_id 	= $credit->where('name', 'points')->getValue('id');
								$voucher_id	= $credit->where('name', 'voucher')->getValue('id');
								$payout_id	= $user->where('name', 'payout')->getValue('id');

								$points 	= $promotionObj->points;
								$voucher	= $promotionObj->voucher;

								$promotion_redeem->isNew  		= 1;
								$promotion_redeem->promotion_id	= $promotionObj->id;
								$promotion_redeem->user_id		= $user->id;
								$promotion_redeem->created_at	= $date;
								$promotion_redeem->status		= 'approve';
								$promotion_redeem_id			= $promotion_redeem->save();

								if($points > 0){
									insertTransaction($user->id, $points, $promotion_redeem_id, $points_id, $payout_id, $user->id, $date, $promotionObj->name);
								}

								if($voucher > 0){
									insertTransaction($user->id, $voucher, $promotion_redeem_id, $voucher_id, $payout_id, $user->id, $date, $promotionObj->name);
								}
							}
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
				
				//do not allow deleting system row
				if($user->system){
					responseFail($error, 'action forbidden');
					break;
				}
				
                $user->delete();
				
                $user = $user->byId($params['id']);
                $user->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
				
				$adjustment = new adjustment();
				$adjustment->updateCustom(array('latest' => 0), array('user_id' => $params['id']));
				
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$user = $user->byId($data['id']);
					foreach($data as $key => $value){
						if($key == 'password'){
							$value = PasswordHasher::HashPassword($value);
						}
						$user->$key = $value;
					}

					if($action == 'delete'){
						if($user->system){
							continue;
						}
						
						$user->delete();
						
						$adjustment = new adjustment();
						$adjustment->updateCustom(array('latest' => 0), array('user_id' => $data['id']));
					}else{
						$user->updated_at = $date;
						$id = $user->save();
					}
				}
                
				$response['redirect'] = true;
				break;
				
        }
        
    } while(0);

    function validate($params = array(), $additional_fields = array()){
        $validator = new Validator;

        // fields info container
        $fields = array();
        $required_keys = array('id', 'username', 'password');

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

?>