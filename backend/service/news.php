<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');
	include(__DIR__ . '/config/config.php');

	$action = $_GET['action'];

	switch ($action) {
		case 'get_banner_news_h5':
			$exp = "/\"(.*?)\"/";

			$datas_article = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'description', 'media_type', 'thumbnail_big_h5', 'tags'], ['media_type' => 1, 'ORDER' => ['active_at' => 'DESC'], 'LIMIT' => 2]);
			$datas_video = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'description', 'media_type', 'thumbnail_big_h5', 'tags'], ['media_type' => 2, 'ORDER' => ['active_at' => 'DESC'], 'LIMIT' => 2]);
			$datas = array();

			array_push($datas, $datas_article[0]);
			array_push($datas, $datas_video[0]);
			array_push($datas, $datas_article[1]);
			array_push($datas, $datas_video[1]);
			
			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}
				preg_match_all($exp, $datas[$key]['tags'], $matches);
				$datas[$key]['tags'] = $matches[1];
			}
			echoJson($datas);
			break;
		case 'get_hot_news':
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 4;
			}
			if ($limit > 20) {
				$limit = 4;
			}
			$category_id = 0;
			if (isset($_GET['category_id'])) {
				$category_id = intval($_GET['category_id']);
			}
			$sub_category_id = 0;
			if (isset($_GET['sub_category_id'])) {
				$sub_category_id = intval($_GET['sub_category_id']);
			}
			$media_type = 0;
			if (isset($_GET['media_type'])) {
				$media_type = intval($_GET['media_type']);
			}

			$where = array();

			$where['active_timestamp[>]'] = 0;
			// $where['type'] = 'latest_news';

			if ($category_id == 0) {
				$where['category_id[>]'] = '0';
			}
			else {
				if ($category_id == 9999) {
					$where['category_id'] = [5, 6, 7, 8];
				}
				else {
					$where['category_id'] = $category_id;
				}
			}
			
			if ($sub_category_id > 0) {
				$where['sub_category_id'] = $sub_category_id;
			}
			else {
				$where['sub_category_id[>=]'] = 0;
			}

			$where['disabled'] = 0;
			$where['draft'] = 0;
			$where['hot'] = 1;
			if ($media_type > 0) {
				$where['media_type'] = $media_type;
			}
			else {
				$where['media_type[>]'] = 0;
			}
			// if ($category_id == 0) {
			// 	$where['popular'] = 0;
			// }
			$where['ORDER'] = ['active_at' => 'DESC'];
			$where['LIMIT'] = $limit;

			$exp = "/\"(.*?)\"/";

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'description', 'media_type', 'thumbnail_small2', 'thumbnail_medium', 'thumbnail_medium3', 'thumbnail_big', 'thumbnail_small_h5', 'thumbnail_medium_h5', 'thumbnail_big_h5', 'tags'], $where);
			
			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_small2'] == '') {
					$datas[$key]['thumbnail_small2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium'] == '') {
					$datas[$key]['thumbnail_medium'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big'] == '') {
					$datas[$key]['thumbnail_big'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}

				preg_match_all($exp, $datas[$key]['tags'], $matches);
				$datas[$key]['tags'] = $matches[1];
			}
			echoJson($datas);
			break;
		case 'get_popular_news':
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 3;
			}
			if ($limit > 20) {
				$limit = 3;
			}
			$category_id = 0;
			if (isset($_GET['category_id'])) {
				$category_id = intval($_GET['category_id']);
			}

			$media_type = 0;
			if (isset($_GET['media_type'])) {
				$media_type = intval($_GET['media_type']);
			}

			$where = array();

			$where['active_timestamp[>]'] = 0;
			$where['type[!]'] = '';

			if ($category_id == 0) {
				$where['category_id[>]'] = '0';
			}
			else {
				if ($category_id == 9999) {
					$where['category_id'] = [5, 6, 7, 8];
				}
				else {
					$where['category_id'] = $category_id;
				}
			}
			if ($media_type > 0) {
				$where['media_type'] = $media_type;
			}
			else {
				$where['media_type[>]'] = 0;
			}
			$where['disabled'] = 0;
			$where['draft'] = 0;
			$where['popular'] = 1;
			$where['ORDER'] = ['active_at' => 'DESC'];
			$where['LIMIT'] = $limit;

			$exp = "/\"(.*?)\"/";

			$exp2 = "/<a href=\"(.*?)(\.mp4)\"(.*?)>(.*?)<\/a>/i";

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'description', 'media_type', 'thumbnail_small', 'thumbnail_small2', 'thumbnail_medium', 'thumbnail_medium2', 'thumbnail_medium3', 'thumbnail_big', 'thumbnail_small_h5', 'thumbnail_medium_h5', 'thumbnail_big_h5', 'tags', 'content'], $where);
			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_small'] == '') {
					$datas[$key]['thumbnail_small'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small2'] == '') {
					$datas[$key]['thumbnail_small2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium'] == '') {
					$datas[$key]['thumbnail_medium'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium2'] == '') {
					$datas[$key]['thumbnail_medium2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium3'] == '') {
					$datas[$key]['thumbnail_medium3'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big'] == '') {
					$datas[$key]['thumbnail_big'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}
				preg_match_all($exp2, $value['content'], $matches);
				$datas[$key]['video_url'] = $matches[4][0];

				// if ($value['thumbnail_small'] == '') {
				// 	if ($value['upload_id'] > 0) {
				// 		$datas[$key]['thumbnail'] = '/' . $database->get('upload', 'url', ['id' => $value['upload_id']]);
				// 	}
				// 	else {
				// 		preg_match_all($exp2, $value['content'], $matches);
				// 		$datas[$key]['thumbnail'] = $matches[1][0];
				// 	}
				// }
				preg_match_all($exp, $datas[$key]['tags'], $matches);
				$datas[$key]['tags'] = $matches[1];
				unset($datas[$key]['content']);
			}
			echoJson($datas);
			break;
		case 'get_latest_news':
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 6;
			}
			if ($limit > 30) {
				$limit = 6;
			}
			$category_id = 0;

			if (isset($_GET['category_id'])) {
				$category_id = intval($_GET['category_id']);
			}
			$where = array();
			$where['active_timestamp[>]'] = 0;
			// $where['type'] = 'latest_news';

			$top4_hotnews_id = array();
			
			if ($category_id == 0) {
				$where['category_id[>]'] = '0';
				$top4_hotnews_id_tmp = $database->select('article', ['id'], ['active_timestamp[>]' => 0, 'category_id[>]' => 0, 'disabled' => 0, 'draft' => 0, 'hot' => 1, 'ORDER' => ['active_at' => 'DESC'], 'LIMIT' => 4]);
				foreach ($top4_hotnews_id_tmp as $key => $value) {
					array_push($top4_hotnews_id, $value['id']);
				}
			}
			else {
				if ($category_id == 9999) {
					$where['category_id'] = [5, 6, 7, 8];
				}
				else {
					$where['category_id'] = $category_id;
					$top4_hotnews_id_tmp = $database->select('article', ['id'], ['active_timestamp[>]' => 0, 'category_id' => $category_id, 'disabled' => 0, 'draft' => 0, 'hot' => 1, 'ORDER' => ['active_at' => 'DESC'], 'LIMIT' => 4]);
					foreach ($top4_hotnews_id_tmp as $key => $value) {
						array_push($top4_hotnews_id, $value['id']);
					}
				}
			}
			$where['disabled'] = '0';
			$where['draft'] = '0';
			$where['media_type[>]'] = 0;
			if ($category_id == 0) {
				// $where['hot'] = '0';
				// $where['popular'] = '0';
			}

			if (count($top4_hotnews_id) > 0) {
				$where['id[!]'] = $top4_hotnews_id;
			}
			
			$where['ORDER'] = ['active_at' => 'DESC'];
			$where['LIMIT'] = $limit;

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'media_type', 'thumbnail_small', 'thumbnail_small_h5', 'thumbnail_medium_h5'], $where);

			// $exp = "/<a href=\"(.*?)\">/";
			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_small'] == '') {
					$datas[$key]['thumbnail_small'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
			}
			echoJson($datas);
			break;
		case 'get_category_news':
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 6;
			}
			if ($limit > 30) {
				$limit = 6;
			}
			$category_id = 0;
			if (!intval($_GET['category_id'])) {
				exit(0);
			}
			$category_id = intval($_GET['category_id']);
			
			$sub_category_id = intval($_GET['sub_category_id']);

			$media_type = 0;
			if (isset($_GET['media_type'])) {
				$media_type = intval($_GET['media_type']);
			}
			
			$type = '';
			if (isset($_GET['type'])) {
				$type = $_GET['type'];
			}

			$where = array();
			$where['active_timestamp[>]'] = 0;
			if ($type) {
				$where['type'] = $type;
			}
			else {
				$where['type[<>]'] = '';
			}
			if ($category_id == 9999) {
				$where['category_id'] = [5, 6, 7, 8];
			}
			else {
				$where['category_id'] = $category_id;
			}

			if ($sub_category_id > 0) {
				$where['sub_category_id'] = $sub_category_id;
			}
			else {
				$where['sub_category_id[>=]'] = 0;
			}

			$where['disabled'] = '0';
			$where['draft'] = '0';

			if ($media_type > 0) {
				$where['media_type'] = $media_type;
			}
			else {
				$where['media_type[>]'] = 0;
			}
			
			$where['ORDER'] = ['active_at' => 'DESC'];
			$where['LIMIT'] = $limit;

			$exp = "/\"(.*?)\"/";

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'media_type', 'thumbnail_small', 'thumbnail_small2', 'thumbnail_big', 'thumbnail_small_h5', 'thumbnail_medium_h5', 'thumbnail_big_h5', 'title', 'description', 'tags'], $where);

			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_small'] == '') {
					$datas[$key]['thumbnail_small'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small2'] == '') {
					$datas[$key]['thumbnail_small2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}

				preg_match_all($exp, $datas[$key]['tags'], $matches);
				$datas[$key]['tags'] = $matches[1];
			}
			echoJson($datas);
			break;
		case 'get_category_news_pagination':
			$response = array();
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 10;
			}
			if ($limit > 25) {
				$limit = 25;
			}
			$category_id = 0;
			if (!intval($_GET['category_id'])) {
				exit(0);
			}
			$category_id = intval($_GET['category_id']);
			
			$sub_category_id = intval($_GET['sub_category_id']);

			$media_type = 0;
			if (isset($_GET['media_type'])) {
				$media_type = intval($_GET['media_type']);
			}
			
			$page = 1;
			if (isset($_GET['page'])) {
				$page = intval($_GET['page']);
			}

			$type = '';
			if (isset($_GET['type'])) {
				$type = $_GET['type'];
			}

			$where = array();
			$where['active_timestamp[>]'] = 0;
			if ($type) {
				$where['type'] = $type;
			}
			else {
				$where['type[<>]'] = '';
			}
			if ($category_id == 9999) {
				$where['category_id'] = [5, 6, 7, 8];
			}
			else {
				$where['category_id'] = $category_id;
			}

			if ($sub_category_id > 0) {
				$where['sub_category_id'] = $sub_category_id;
			}
			else {
				$where['sub_category_id[>=]'] = 0;
			}

			$where['disabled'] = '0';
			$where['draft'] = '0';

			if ($media_type > 0) {
				$where['media_type'] = $media_type;
			}
			else {
				$where['media_type[>]'] = 0;
			}
			
			$where['ORDER'] = ['active_at' => 'DESC'];

			$begin = (intval($page) - 1) * $limit;

			$where['LIMIT'] = [$begin, $limit];

			$exp = "/\"(.*?)\"/";

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'media_type', 'thumbnail_medium4', 'thumbnail_small_h5', 'thumbnail_medium_h5', 'thumbnail_big_h5', 'title', 'description', 'tags'], $where);

			unset($where['ORDER']);
			unset($where['LIMIT']);
			$totalcount = $database->count('article', $where);

			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_medium4'] == '') {
					$datas[$key]['thumbnail_medium4'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}

				preg_match_all($exp, $datas[$key]['tags'], $matches);
				$datas[$key]['tags'] = $matches[1];
			}
			$response['data'] = $datas;
			$response['current_page'] = $page;
			$response['total_page'] = ceil($totalcount / $limit);
			echoJson($response);
			break;
		case 'get_featured_video':
			$limit = 0;
			if (isset($_GET['limit'])) {
				$limit = intval($_GET['limit']);
			}
			else {
				$limit = 3;
			}
			if ($limit > 20) {
				$limit = 3;
			}

			$where['media_type'] = '2';
			$where['popular'] = '1';
			$where['ORDER'] = ['active_at' => 'DESC'];
			$where['LIMIT'] = $limit;

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'media_type', 'thumbnail_medium2', 'thumbnail_medium4', 'thumbnail_small_h5', 'thumbnail_medium_h5', 'thumbnail_big_h5'], $where);
			
			$exp = "/<a href=\"(.*?)\">/";
			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_medium4'] == '') {
					$datas[$key]['thumbnail_medium4'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium2'] == '') {
					$datas[$key]['thumbnail_medium2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_big_h5'] == '') {
					$datas[$key]['thumbnail_big_h5'] = '/assets/images/grey.gif';
				}
			}
			echoJson($datas);
			break;
		case 'get_article_category':
			$id = intval($_GET['id']);
			if (!$id) {
				exit(0);
			}
			$where['id'] = $id;
			$article = $database->get('article', ['category_id', 'sub_category_id'], $where);
			$article['category'] = $database->get('category', 'display', ['id' => $article['category_id']]);
			$article['sub_category'] = $database->get('category', 'display', ['id' => $article['sub_category_id']]);
			echoJson($article);
			break;
		case 'get_article';
			$id = intval($_GET['id']);
			if (!$id) {
				exit(0);
			}
			$where['id'] = $id;
			$where['draft'] = 0;
			$where['disabled'] = 0;

			$exp = "/\"(.*?)\"/";

			$exp2 = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";

			$database->update("article", ["view_count[+]" => 1], ["id" => $id]);

			$article = $database->get('article', ['id', 'title', 'seo_title', 'author', 'category_id', 'sub_category_id', 'media_type', 'content', 'tags', 'view_count', 'active_at', 'description', 'keywords', 'chatroom_id'], $where);
			$content = $article['content'];

			preg_match_all($exp2, $content, $matches);

			if (count($matches[0]) > 0) {
			    for ($i=0; $i < count($matches[0]); $i++) { 
			    	$video_url = $root_folder . urldecode($matches[4][$i]);
			    	if (is_file($video_url)) {
			    		$file_md5 = md5_file($video_url);
			    		$chunk = $database->get('video_chunk_index', ['path', 'poster'], ['id' => $file_md5]);
			        	$content = str_replace($matches[0][$i], '<video class="video-js vjs-default-skin vjs-big-play-centered" poster="'.$img_url . $chunk['poster'].'" data-setup=\'{"fluid":true, "controls": true, "autoplay": false, "preload": "auto"}\'><source src="'.$img_url.$chunk['path'].'" type="application/x-mpegURL"></video>', $content);
			    	}
			    }
			}

			$article['content'] = $content;

			preg_match_all($exp, $article['tags'], $matches);
			$article['tags'] = $matches[1];
			echoJson($article);
			break;
		case 'get_tag_news':
			$tag = '';
			if (isset($_GET['tag'])) {
				$tag = trim($_GET['tag']);
			}
			$page = 1;
			if (isset($_GET['page'])) {
				$page = intval($_GET['page']);
			}
			$limit = 25;

			$article_ids = $database->select('article_tags', 'article_id', ['tag' => $tag]);

			$where = ['id' => $article_ids];

			$totalcount = $database->count('article', $where);

			$where['ORDER'] = ['active_at' => 'DESC'];

			$begin = (intval($page) - 1) * $limit;

			$where['LIMIT'] = [$begin, $limit];

			$datas = $database->select('article', ['id', 'title', 'category_id', 'sub_category_id', 'active_at', 'media_type', 'thumbnail_small2', 'thumbnail_small_h5', 'thumbnail_medium_h5'], $where);

			foreach ($datas as $key => $value) {
				$datas[$key]['category'] = $database->get('category', 'display', ['id' => $value['category_id']]);
				$datas[$key]['sub_category'] = $database->get('category', 'display', ['id' => $value['sub_category_id']]);
				if ($value['thumbnail_small2'] == '') {
					$datas[$key]['thumbnail_small2'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_small_h5'] == '') {
					$datas[$key]['thumbnail_small_h5'] = '/assets/images/grey.gif';
				}
				if ($value['thumbnail_medium_h5'] == '') {
					$datas[$key]['thumbnail_medium_h5'] = '/assets/images/grey.gif';
				}
			}
			$response['data'] = $datas;
			$response['current_page'] = $page;
			$response['total_page'] = ceil($totalcount / $limit);
			echoJson($response);
			break;
		case 'search':
			include_once('/usr/local/xunsearch/sdk/php/lib/XS.php');
			$keyword = trim($_GET['keyword']);
			$limit = 10;
			$page = intval($_GET['page']);

			if(!$page){
				$page = 1;
			}

			if(!$keyword){
				exit(0);
			}

			$begin = ($page - 1) * $limit;

			$keyword = strtoupper(urldecode($keyword));
			// $so = scws_new(); //Create an object
			// $so->set_charset('utf8'); //Set UTF8
			// $so->set_ignore(true); //filter symbol
			// $so->send_text($keyword);
			// $result = $so->get_result();
			// $search_text = "";
			// //print_r($result);
			// foreach($result as $soObj){
			// 	$search_text .= $soObj['word']." ";
			// }
			// $search_text .= $keyword;

			// $so->close();

		    $xs = new XS('article');
		      //$index = $xs->index;   //  获取索引对象
		    $search = $xs->search;   //  获取搜索对象
		    $search->setSort('active_at', false);
		    $search->setLimit($limit, $begin); 
		    $docs = $search->setQuery($keyword)->search();
		    $totalcount = $search->getLastCount();

		    $list = array();

		    foreach ($docs as $doc) {
		    	$item['id'] = $doc->id;
				$item['title'] = $doc->title;
				$item['content'] = $doc->content;
				$item['category'] = $doc->category;
				$item['sub_category'] = $doc->sub_category;
				$item['tags'] = $doc->tags;
				$item['thumbnail_web'] = $doc->thumbnail_web ? $doc->thumbnail_web : '/assets/images/grey.gif';
				$item['thumbnail_h5'] = $doc->thumbnail_h5 ? $doc->thumbnail_h5 : '/assets/images/grey.gif';
				$item['active_at'] = $doc->active_at;
				$item['category_id'] = $doc->category_id;
				$item['sub_category_id'] = $doc->sub_category_id;

				array_push($list, $item);
		    }

		    $data['list'] = $list;
		    $data['totalcount'] = $totalcount;
		    echoJson($data);
			break;
		default:
			break;
	}
?>