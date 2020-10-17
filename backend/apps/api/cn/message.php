<?php
	
	$input = $params;

	include_once('model/upload.php');
    include_once('model/message.php');
	include_once('model/chatroom.php');
	include_once('model/illegal_words.php');
	include_once('model/user.php');
	include_once('model/article.php');
	include_once('model/event.php');

    do{
        
        $message = new message();
		$article = new article();
		$upload	 = new upload();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $message->adminFields : $message->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $messageObj = $message->with('user')->join('article', 'chatroom_id', 'LEFT', 'message.chatroom_id')->byId($params['id'], $selectFields);
					
                    $return = $messageObj->data;
                    if( $messageObj->user){
                        $return['user_name']     = $messageObj->user->name;
						$return['user_username'] = $messageObj->user->username;
                    }
                    if($messageObj->upload){
                        $return['upload_url']    = $messageObj->upload->url;
                    }
					if($messageObj->chatroom){
						$return['unread_count']  = $messageObj->chatroom->unread_count;
					}
					if(isset($return['article'])){
						$return['article_title'] = $return['article']['title'];
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
							
                            $message->where($message->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					$message->orderBy('CASE WHEN message.parent_id > 0 THEN message.created_at END', 'asc');
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$message->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$message->orderBy($data['field'], $data['sort']);
							}
						}
                    }
					
					$message->orderBy('CASE WHEN message.parent_id = 0 THEN message.id ELSE message.parent_id END', 'asc');
					$message->orderBy('message.parent_id', 'asc');
					$message->orderBy('message.id', 'asc');
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(strlen($value) == 0){
								continue;
							}
							
							$message->where($field, $value, $operator);
                        }
                    }
                    
					$message_copy = $message->db->copy();
                    $result = $message->with('user')->join('article', 'chatroom_id', 'LEFT', 'article.chatroom_id')->get($limit, $selectFields);
					$message->db = $message_copy;
					$totalRecords = $message->with('user')->join('article', 'chatroom_id', 'LEFT', 'article.chatroom_id')->getValue("count(message.id)");
           
                    $message_array = array();
                    if($result){
                        foreach($result as $messageObj){
                            $return = $messageObj->data;
                            if( $messageObj->user){
								$return['user_name']     	= $messageObj->user->name;
								$return['user_username']    = $messageObj->user->username;
								$return['user_admin'] 	 = $messageObj->user->user_admin;
								
								$uploadObj = $upload->byId($messageObj->user->upload_id);
								if($uploadObj)
									$return['user_upload_url']	= $uploadObj->url;
								
								unset($return['user']);
							}
							if($messageObj->upload){
								$return['upload_url']    = $messageObj->upload->url;
								unset($return['upload']);
							}
							if($messageObj->chatroom){
								$return['unread_count']  = $messageObj->chatroom->unread_count;
							}
							if(isset($return['article'])){
								$return['article_title'] = $return['article']['title'];
								$return['article_id'] = $return['article']['id'];
								unset($return['article']);
							}
			
                            $message_array[] = $return;
                        } 
                    }

                    $response['data']   = $message_array;
                    
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
				$additional_fields[] = array('index' => 'chatroom_id', 'label' => 'chatroom_id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $message->$key = $value;
                }
				
				$chatroom = new chatroom();
				$chatroom = $chatroom->byId($message->chatroom_id);

				$message->type			= $chatroom->type;
				$message->user_id 		= $login_user->id;
                $message->created_at 	= $date;
				$message->updated_at	= $date;
                
                $id = $message->save();
                if(!$id){
                    responseFail($error, $message->getLastError());
                    break;
                }
                
                $response['id'] = $id;
				$response['redirect'] = true;
				
				$user = new user();
				$user->updateCustom(array('comment_count' => $user->db->inc(1)), array('id' => $message->user_id));
				
				if($chatroom->type == 'article'){
					$article = new article();
					$article->updateCustom(array('comment_count' => $article->db->inc(1)), array('chatroom_id' => $chatroom->id));
				}
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $message = $message->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $message->$key = $value;
                }

                $message->updated_at = $date;
                $message->save();
                
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
                $message = $message->byId($params['id']);
                $message->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$message = $message->byId($data['id']);
					
					if($action == 'delete'){
						$message->delete();
					}
					
					if(array_key_exists('message', $data)){
						if(checkIllegalWords($data['message'])){
							$data['status'] = 'pending';
						}else{
							$data['status'] = 'approve';
						}
					}
					
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
					
					foreach($data as $key => $value){
						$message->$key = $value;
					}

					$message->updated_at = $date;
					$id = $message->save();
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
        $required_keys = array('id', 'chatroom_id');

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
		
		if(array_key_exists('message', $cv)){
			if(checkIllegalWords($cv['message'])){
				$cv['status'] = 'pending';
			}else{
				$cv['status'] = 'approve';
			}
		}

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

        $params['attachment_upload_id'] = $upload_id;
    }
			
	function checkIllegalWords($message){
		
		$illegal_words = new illegal_words();
		$result = $illegal_words->where('disabled', '0')->get();
		
		$illegal = false;
		
		if($result){
			foreach($result as $illegal_wordsObj){
				if($illegal_wordsObj->regex){
					if(preg_match($illegal_wordsObj->word, $message)){
						$illegal = true;
						break;
					}
				}else{
					if(preg_match("/{$illegal_wordsObj->word}/i", $message)) {
						$illegal = true;
						break;
					}
				}
			}
		}
		
		preg_match_all('@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@', $message, $matches);
		if(isset($matches[0]) && count($matches[0]) > 0){
			$illegal = true;
		}
		
		preg_match_all('/^http(s)?:\/\/.+/', $message, $matches);
		if(isset($matches[0]) && count($matches[0]) > 0){
			$illegal = true;
		}

		return $illegal;
	}

?>