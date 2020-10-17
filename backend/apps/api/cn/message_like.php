<?php

	include_once('model/message.php');
	include_once('model/team.php');

	$input = $params;

    do{

        $message_like = new message_like();
		$team		  = new team();
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
                    $message_likeObj = $message_like->byId($params['id']);
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
							
                            $message_like->where($message_like->dbTable.".".$key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$message_like->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$message_like->orderBy($data['field'], $data['sort']);
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
							
							$message_like->where($field, $value, $operator);
                        }
                    }

					$message_like_copy = $message_like->db->copy();
                    $result = $message_like->join('message', 'message_id', 'LEFT', 'message.id')->join('article', 'article.chatroom_id', 'LEFT', 'message.chatroom_id')->join('event', 'event.chatroom_id', 'LEFT', 'message.chatroom_id')->get($limit, array('message_like.id', 'message_like.created_at', 'message.message', 'article.title', 'event.home_team_id', 'event.away_team_id'));
					$message_like->db = $message_like_copy;
					$totalRecords = $message_like->join('message', 'message_id', 'LEFT', 'message.id')->join('article', 'article.chatroom_id', 'LEFT', 'message.chatroom_id')->join('event', 'event.chatroom_id', 'LEFT', 'message.chatroom_id')->getValue("count(message_like.id)");
					
                    $message_like_array = array();
                    if($result){
                        foreach($result as $message_likeObj){
							$return = $message_likeObj->data;
							
							if(isset($return['home_team_id']) && isset($return['away_team_id'])){	
								$home_teamObj	= $team->byId($return['home_team_id']);
								$away_teamObj	= $team->byId($return['away_team_id']);
								
								$return['title'] = $home_teamObj->name_zh.' - '.$away_teamObj->name_zh;
							}
							
                            $message_like_array[] = $return;
                        } 
                    }

                    $response['data'] = $message_like_array;

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
                    $message_like->$key = $value;
                }
				
				$message_likeObj = $message_like->where('user_id', $login_user->id)->where('message_id', $message_like->message_id)->getOne();
				
				if(!$message_likeObj){
					$message_like->user_id	  = $login_user->id ? $login_user->id : 0;
					$message_like->created_at = $date;
					
                	$id = $message_like->save();
					if(!$id){
						responseFail($error, $message_like->getLastError());
						break;
					}
					
					$response['data'] = $id;
					
					$message = new message();
					$message->updateCustom(array('like_count' => $message->db->inc(1)), array('id' => $message_like->message_id));
				}else{
					responseFail($error, "点赞过于频繁", "406");
                    break;
				}
                
				$response['redirect'] = true;
				
                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $message_like = $message_like->byId($params['id']);
				
                foreach($params as $key => $value){
                    $message_like->$key = $value;
                }

                $message_like->updated_at = $date;
                $id = $message_like->save();
                if(!$id){
                    responseFail($error, $message_like->getLastError());
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

				$message_like = $message_like->byId($params['id']);
				$message_like->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$message_like = $message_like->byId($data['id']);
					foreach($data as $key => $value){
						$message_like->$key = $value;
					}

					if($action == 'delete'){
						if($message_like->system){
							continue;
						}
						
						$message_like->delete();
					}else{
						$message_like->updated_at = $date;
						$id = $message_like->save();
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