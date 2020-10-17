<?php

    include_once('model/category.php');
    include_once('model/upload.php');
	include_once('model/chatroom.php');

	$input = $params;

    do{
        
        $article = new article();
        $date = date("Y-m-d H:i:s");

        $additional_fields = array();
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
				
				$selectFields = $login_user->type == 'Admin' ? $article->adminFields : $article->memberFields;
				
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                if(array_key_exists('id', $params)){
                    $articleObj = $article->with('category')->with('sub_category')->with('upload')->byId($params['id'], $selectFields);
                    $return = $articleObj->data;
                    if( $articleObj->category){
                        $return['category']     = $articleObj->category->display;
                    }
					if( $articleObj->sub_category){
						$return['sub_category']     = $articleObj->sub_category->display;
					}
                    if($articleObj->upload){
                        $return['upload_url']   = $articleObj->upload->url;
						$return['upload_data']	= $articleObj->upload->data;
                    }
                    $return['tags'] = json_decode($articleObj->tags);
                    $response['data'] = $return;
					
					if($login_user && $login_user->type == 'Admin'){
						
					}else{
						$article->updateCustom(array('view_count' => $article->db->inc(1)), array('id' => $articleObj->id));
					}
					
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
							
                            $article->where($key, "%$value%", 'LIKE');
                        }
                    }
					
					if(array_key_exists('sort', $params)){
						$sorting_data = $params['sort'];
						if(array_key_exists('sort', $sorting_data)){
                        	$article->orderBy($sorting_data['field'], $sorting_data['sort']);
						}else{
							foreach($sorting_data as $data){
								$article->orderBy($data['field'], $data['sort']);
							}
						}
                    }
					
					if(array_key_exists('filter', $params)){
                        foreach($params['filter'] as $search){
							$field 		= $search['field'];
							$value		= $search['value'];
							$operator	= $search['operator'] ? $search['operator']:'LIKE';
							
							if(is_string($value) && strlen($value) == 0){
								continue;
							}else if(is_array($value) && count($value) == 0){
								continue;
							}
							
							$article->where($field, $value, $operator);
                        }
                    }
                    
					$article_copy = $article->db->copy();
                    $result = $article->with('category')->with('sub_category')->with('upload')->get($limit, $selectFields);
					$article->db = $article_copy;
					$totalRecords = $article->with('category')->with('sub_category')->with('upload')->getValue("count(article.id)");
                    
                    $article_array = array();
                    if($result){
                        foreach($result as $articleObj){
                            $return = $articleObj->data;
                            if( $articleObj->category){
                                $return['category']     = $articleObj->category->display;
                            }
							if( $articleObj->sub_category){
                                $return['sub_category']     = $articleObj->sub_category->display;
                            }
                            if($articleObj->upload){
                                $return['upload_url']   = $articleObj->upload->url;
								$return['upload_data']	= $articleObj->upload->data;
                            }
                            $return['tags'] = json_decode($articleObj->tags);
							$return['strip_content'] = strip_tags($articleObj->content);
							$return['description'] = strip_tags($articleObj->description);
                            $article_array[] = $return;
                        } 
                    }

                    $response['data']   = $article_array;
                    
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
				
				$chatroom = new chatroom();
				$chatroom->status 		= 'active';
				$chatroom->created_at 	= $date;
				$chatroom->updated_at 	= $date;
				$chatroom->type			= 'article';
				$chatroom_id = $chatroom->save();
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $article->$key = $value;
                }

				$article->author		= $login_user->name;
				$article->chatroom_id	= $chatroom_id;
                $article->created_at 	= $date;
                $article->updated_at 	= $date;
                
                if(strlen($article->seo_title) <= 0){
                    $article->seo_title = $article->title;
                }
                
                if(strlen($article->description) <= 0){
					//remove inner text from video
					$exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";
					$new_content = preg_replace_callback($exp, function ($m) {
							$pos = strrpos($m[0], $m[4]);
							return substr_replace($m[0], '', $pos, strlen($m[4]));
					}, $article->content);
                    $article->description = substr(html_entity_decode(strip_tags($new_content)), 0, 170);
                }
				
				$article->description = strip_tags(html_entity_decode($article->description));

                $id = $article->save();
                if(!$id){
                    responseFail($error, json_encode($article->errors[0]));
                    break;
                }

				uploadFiles($article, $id);
				
				$category = new category();
				$category->updateCustom(array('article_count' => $category->db->inc(1)), array('id' => $article->category_id));
				$category->updateCustom(array('article_count' => $category->db->inc(1)), array('id' => $article->sub_category_id));
                
                $response['id'] = $id;
				$response['redirect'] = true;	
				
				addXunSearch($article);

                break;

            case 'PUT':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $article = $article->byId($params['id']);
                
                if(array_key_exists('image_data', $params)){
                    upload($params);
                }
                
                foreach($params as $key => $value){
                    $article->$key = $value;
                }
				
                if(strlen($article->seo_title) <= 0){
                    $article->seo_title = $article->title;
                }
                
                if(strlen($article->description) <= 0){
					//remove inner text from video
					$exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";
					$new_content = preg_replace_callback($exp, function ($m) {
							$pos = strrpos($m[0], $m[4]);
							return substr_replace($m[0], '', $pos, strlen($m[4]));
					}, $article->content);
                    $article->description = substr(html_entity_decode(strip_tags($new_content)), 0, 170);
                }

				$article->author	 = $login_user->name;
                $article->updated_at = $date;
                $article->save();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;

				uploadFiles($article, $params['id']);
				
				updateXunSearch($article);

                break;

            case 'DELETE':
                $additional_fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);
                $params = validate($params, $additional_fields);
                if(is_string($params)){
                    responseFail($error, $params);
                    break;
                }
                $article = $article->byId($params['id']);
                $article->delete();
                
                $response['id'] = $params['id'];
				$response['redirect'] = true;
				
				deleteXunSearch($article);
				
                break;
				
			case 'PATCH':
				$action = "";
				if(array_key_exists('action', $params)){
					$action = $params['action'];
				}
				
				foreach($params['data'] as $data){	
					$article = $article->byId($data['id']);
					
					if($action == 'delete'){
						$article->deleted = 1;
						$article->updated_at = $date;
						$article->save();
						
						deleteXunSearch($article);
						continue;
					}
					
					if(array_key_exists('image_data', $data)){
						upload($data);
					}
					
					if(array_key_exists('tags', $data)){
						$problem = "";
						$result = checkTags($data['tags'], $problem);

						if(strlen($problem) > 0){
							continue;
						}else{
							$data['tags'] = $result;
						}
					}
					
					foreach($data as $key => $value){
						$article->$key = $value;
					}
					
					if(strlen($article->seo_title) <= 0){
						$article->seo_title = $article->title;
					}

					if(strlen($article->description) <= 0){
						$article->description = substr($article->content, 0, 170);
					}
					
					$article->updated_at = $date;
					$id = $article->save();
					
					updateXunSearch($article);
				}
                
				$response['redirect'] = true;
				
				rebuildXunSearch();
				
				break;
        }
        
    } while(0);

    function validate($params = array(), $additional_fields = array()){
        $validator = new Validator;

        // fields info container
        $fields = array();
        $required_keys = array('title', 'category_id', 'content');
		$keys_label = array('title' => '标题', 'category_id' => '分类目录', 'content' => '內容');

        if($params){
            foreach($params as $key => $value){
                $required = false;
                if(array_key_exists('draft', $params) && !$params['draft']){
                    if(in_array($key, $required_keys)){
                        $required = true;
                    }
                }

                $fields[] = array('index' => $key, 'label' => isset($keys_label[$key]) ? $keys_label[$key] : $key, 'required' => $required);
            }
        }
        
        $fields = array_merge($fields, $additional_fields);

        $validator->formHandle($fields);
        $problem = $validator->getErrors();
        $cv = $validator->escape_val(); // get the form values
		
		if(array_key_exists('tags', $cv)){
			$result = checkTags($cv['tags'], $problem);
	
			$cv['tags'] = $result;
		}

        if ($problem) {
            return $problem;
        }
        
        return $cv;
    }

	$response['extra'] = $input;

    function upload(&$params){
		$upload	 = new upload();
        foreach($params['image_data'] as $key => $value){
            $upload->$key = $value;
        }

        $upload->created_at = date("Y-m-d H:i:s");

        $upload_id = $upload->save();
        unset($params['image_data']);

        $params['upload_id'] = $upload_id;
    }

	function checkTags($tags, &$problem){
		//$problem = '';
		if(!is_array($tags)){
			$problem = "please pass tags as array";
		}else{
			if(count($tags) > 5){
				$problem = "maximum 5 tags per article";
			}else{
			   $tag_string = "";
				foreach($tags as $tag){
					if(strlen($tag) > 16){
						$problem = "each tag maximum 16 characters";
						break;
					}
				}   

				$tags = json_encode($tags, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); 
			}
		}
		
		return $tags;
	}

	function uploadFiles($article, $id){
		//match images
		$matches = array();
		$set_upload_id = false;
		preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/", stripcslashes($article->content), $matches);

		if(count($matches) > 0){
			if(isset($matches[3])){	
				$upload	 = new upload();
				foreach($matches[3] as $url){
					if (substr($url, 0, 1) === '/') { 
						$url = substr($url, 1);
					}
					$uploadObj = $upload->where('url', $url)->getOne();

					if($uploadObj){
						$uploadObj->category_id 	= $article->category_id ? $article->category_id:0;
						$uploadObj->sub_category_id	= $article->sub_category_id ? $article->sub_category_id:0;
						$uploadObj->article_id		= $id;
						$uploadObj->save();

						if(!$set_upload_id){
							$article->upload_id = $uploadObj->id;
							$article->isNew = false;
							$article->save();

							$set_upload_id = true;
						}
					}
				}
			}
		}

		//match videos
		$matches = array();
		preg_match_all("/<a([^>]*)\s*href=('|\")([^'\"]+)('|\")/", stripcslashes($article->content), $matches);

		if(count($matches) > 0){
			if(isset($matches[3])){	
				$upload	 = new upload();
				foreach($matches[3] as $url){
					if (substr($url, 0, 1) === '/') { 
						$url = substr($url, 1);
					}
					$uploadObj = $upload->where('url', $url)->getOne();

					if($uploadObj){
						$uploadObj->category_id 	= $article->category_id ? $article->category_id:0;
						$uploadObj->sub_category_id	= $article->sub_category_id ? $article->sub_category_id:0;
						$uploadObj->article_id		= $id;
						$uploadObj->save();

						if(!$set_upload_id){
							$article->upload_id = $uploadObj->id;
							$article->isNew = false;
							$article->save();

							$set_upload_id = true;
						}
					}
				}
			}
		}
	}

	function addXunSearch($articleObj){
		include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");

		$XS = new XS('article');
		$index = $XS->index;

		$category = new category();
		
		$main_category = $category->where('id', $articleObj->category_id)->getValue('display');
		$sub_category = $category->where('id', $articleObj->sub_category_id)->getValue('display');

		$data["id"] = $articleObj->id;
		$data['category'] = $main_category;
		$data['sub_category'] = $sub_category;
		$data['active_at'] = $articleObj->active_at;
		$data['search_title'] = $articleObj->title." - ".$main_category." - ".$sub_category." - ".$articleObj->tags;
		$data['title'] = $articleObj->title;
		$data['active_at'] = $articleObj->active_at;
		$data['thumbnail'] = $articleObj->thumbnail_small_h5;
		$data['tags'] = $articleObj->tags;	

		// Create document object 
		$doc = new XSDocument();
		$doc->setFields($data);

		$res = $index->add($doc);
		
	}

	function updateXunSearch($articleObj){
		include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");

		$XS = new XS('article');
		$index = $XS->index;
		
		//soft deleted
		if($articleObj->deleted == 1){
			deleteXunSearch($articleObj);
			return;
		}

		$category = new category();
		
		$main_category = $category->where('id', $articleObj->category_id)->getValue('display');
		$sub_category = $category->where('id', $articleObj->sub_category_id)->getValue('display');

		$data["id"] = $articleObj->id;
		$data['category'] = $main_category;
		$data['sub_category'] = $sub_category;
		$data['active_at'] = $articleObj->active_at;
		$data['search_title'] = $articleObj->title." - ".$main_category." - ".$sub_category." - ".$articleObj->tags;
		$data['title'] = $articleObj->title;
		$data['active_at'] = $articleObj->active_at;
		$data['thumbnail'] = $articleObj->thumbnail_small_h5;
		$data['tags'] = $articleObj->tags;

		// Create document object 
		$doc = new XSDocument();
		$doc->setFields ($data) ;

		// Update to the index database 
		$index->update($doc);
	}

	function deleteXunSearch($articleObj){
		include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");

		$XS = new XS('article');
		$index = $XS->index;
		$index->del($articleObj->id);
	}

	function rebuildXunSearch(){
		include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");

		$XS = new XS('article');
		$index = $XS->index;

		$index->clean();

		$category = new category();
		$article = new article();
		$article_result = $article->where('disabled', '0')->where('deleted', '0')->where('draft', '0')->get();	
		$category_result = $category->where('type', 'sport')->get();
		foreach($category_result as $categoryObj){
			$category_display[$categoryObj->id] = $categoryObj->display;
		}

		foreach($article_result as $articleObj){
			$doc = new XSDocument();
			$category = isset($category_display[$articleObj->category_id]) ? $category_display[$articleObj->category_id] : "";
			$sub_category = isset($category_display[$articleObj->sub_category_id]) ? $category_display[$articleObj->sub_category_id] : "";
			$data["id"] = $articleObj->id;
			$data['category'] = $category;
			$data['sub_category'] = $sub_category;
			$data['active_at'] = $articleObj->active_at;
			$data['search_title'] = $articleObj->title." - ".$category." - ".$sub_category." - ".$articleObj->tags;
			$data['title'] = $articleObj->title;
			$data['active_at'] = $articleObj->active_at;
			$data['thumbnail'] = $articleObj->thumbnail_small_h5;
			$data['tags'] = $articleObj->tags;	

			$doc->setFields($data);

			$res = $index->add($doc);
		}
	}

?>