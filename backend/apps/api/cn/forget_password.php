<?php

	include_once('model/user.php');

	$input = $params;

    do{
        
        $forget_password = new forget_password();
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
                    $forget_passwordObj = $forget_password->byId($params['id']);
                    $return = $forget_passwordObj->data;
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
							if(strlen($value) > 0)
                            	$forget_password->where($key, $value);
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
							
							$forget_password->where($field, $value, $operator);
                        }
                    }
					
					$forget_password_copy = $forget_password->db->copy();
                    $result = $forget_password->get($limit);
					$forget_password->db = $forget_password_copy;
					$totalRecords = $forget_password->getValue("count(forget_password.id)");
                    
                    $forget_password_array = array();
                    if($result){
                        foreach($result as $forget_passwordObj){
                            $return = $forget_passwordObj->data;
                            $forget_password_array[] = $return;
                        } 
                    }

                    $response['data']   = $forget_password_array;
                    
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
                
                foreach($params as $key => $value){
                    $forget_password->$key = $value;
                }
				
				//get user
				$user 		= new user();
				$userObj 	= $user->where('type', 'Member')->where('phone', $forget_password->phone)->getOne(array('id', 'username'));
				
				if(!$userObj){
					responseFail($error, "手机号未绑定，请联系客服");
                    break;
				}
				
				$user_id	= $userObj->id;
				$username	= $userObj->username;
				
//				$forget_passwordObj = $forget_password->where('user_id', $user_id)->where('status', 0)->getOne();
//				if(!$forget_passwordObj){
//					
//				}
				
				$forget_password->user_id			= $user_id;
				
				if($forget_password->type == 'password'){
					$forget_password->verification_code	= generateVerificationCode();
					$forget_password->status			= 0;
				}else{
					$forget_password->status			= 1;
				}
				
                $forget_password->created_at 		= $date;
				
				$sms_result = sendSms($forget_password->phone, $forget_password->verification_code, $username, $forget_password->type);
				//{"code":0,"message":"\u6210\u529f","data":[{"phone":"13926572431","result":"SUCCESS","message":"\u8bf7\u6c42\u6210\u529f","sn":"159374803174814287901392657243134210"}],"requestId":"02cd408b696fd1226b09f347545ce2a3"}
				if($sms_result['code'] != 0){
					responseFail($error, "发送失败，请稍后再试");
                    break;
				}

                $id = $forget_password->save();
                if(!$id){
                    responseFail($error, $forget_password->getLastError());
                    break;
                }
				
				if($forget_password->type == 'password'){
					$user	= new user();
					$user->byId($user_id);
					$user->password	= PasswordHasher::HashPassword($forget_password->verification_code);
					$user->save();
				}
                
                $response['id'] 		= $id;
				$response['user_id']	= $user_id;
				$response['redirect'] 	= false;

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
				
                $forget_password = $forget_password->byId($params['id']);
				
				if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $forget_password->$key = $value;
				}

                $forget_password->updated_at = $date;
                $forget_password->save();
                
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
                $forget_password = $forget_password->byId($params['id']);
                $forget_password->delete();
                
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
						$forget_password = $forget_password->byId($data['id']);
					}else{
						$forget_password->isNew = true;
						
						$forget_password->created_at = $date;
					}
					
					if($action == 'delete'){
						$forget_password->delete();
						continue;
					}
						
					foreach($data as $key => $value){
						$forget_password->$key = $value;
					}

					$forget_password->updated_at = $date;
					$id = $forget_password->save();
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

	function generateVerificationCode(){
		$verification_code = sprintf("%06d", mt_rand(1, 999999));
		
		return $verification_code;
	}

	function sendSms($phone, $verification_code = "", $username = "", $type = 'password'){
		$url            = 'https://api.yisu.com/sms/sendSms';
		$accessId       = 'a7yprid9UfpgJgSC';
		$accessSecret   = '604f065814c694f7d9ef0cc5a5a4f546';
		
		$params = [
			'timestamp' => time(),
			'nonce'     => mt_rand(0, 99999999),
			'accessId'  => $accessId
		];
		$params['phone'] = $phone;
		
		if($type == 'password'){
			$params['templateCode'] = 200001;
			$params['templateVars'] = json_encode(['code'=>$verification_code], true);
		}else{
			$params['templateCode'] = 100002;
			$params['templateVars'] = json_encode(['code'=>$username], true);
		}
		
		ksort($params);
		$signStr = "";
		foreach ( $params as $key => $value ) {
			$signStr = $signStr . $key . "=" . $value . "&";
		}
		$signStr = substr($signStr, 0, -1);
		$verifySignature = base64_encode(hash_hmac("sha1", $signStr, $accessSecret, true));

		$params['signature'] = $verifySignature;
		$data = http_build_query($params);
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type:application/x-www-form-urlencoded',
				'content' => $data

		));
		$context  = stream_context_create($options);
		$result = file_get_contents($url, FILE_TEXT, $context);
		
		return json_decode($result, true);
	}

	$response['extra'] = $input;

?>