<?php
	include(__DIR__ . '/common/Medoo.php');
	include(__DIR__ . '/common/function.php');

	$action = $_GET['action'];

	switch ($action) {
		case 'get_fans_zone':
			$datas = $database->select('fan_zone', ['upload_id', 'url'], ['disabled' => '0','ORDER' => ['sorting' => 'ASC'],'LIMIT' => 3]);

			foreach ($datas as $key => $value) {
				$url = $database->get('upload', 'url', ['id' => $value['upload_id']]);
				$datas[$key]['image'] = '/' . $url;
			}
			echoJson($datas);
			break;
		case 'get_category':
			$datas = array();
			$category_id = intval($_GET['category_id']);
			$type = 'sport';
			if (isset($_GET['type'])) {
				$type = $_GET['type'];
				if ($type == 'gift') {
					$type = ['gift', 'gift-hot'];
				}
			}
			$datas = $database->select('category', ['id', 'display'], ['parent_id' => $category_id, 'type' => $type, 'disabled' => 0, 'ORDER' => ['sorting' => 'ASC', 'id' => 'ASC']]);
			echoJson($datas);
			break;
		case 'get_hot_gift_category':
			$datas = array();
			$category = $database->select('category', ['id', 'display'], ['type' => 'gift-hot', 'disabled' => 0, 'ORDER' => ['sorting' => 'ASC', 'id' => 'ASC']]);
			foreach ($category as $key => $value) {
				if (!in_array($value['display'], $datas)) {
					array_push($datas, $value['display']);
				}
			}
			echoJson($datas);
			break;
		case 'get_single_category':
			$datas = array();
			$category_id = intval($_GET['category_id']);
			if ($category_id > 0) {
				$datas = $database->get('category', ['id', 'display', 'parent_id'], ['id' => $category_id]);
			}
			echoJson($datas);
			break;
		case 'get_seo_info':
			$category_id = intval($_GET['category_id']);
			$sub_category_id = intval($_GET['sub_category_id']);
			$type = $_GET['type'];

			if (!$category_id) {
				$category_id = 0;
			}
			if (!$sub_category_id) {
				$sub_category_id = 0;
			}
			if (!$type) {
				$type = '';
			}

			if ($sub_category_id && !$category_id) {
				$category_id = $database->get('category', 'parent_id', ['id' => $sub_category_id]);
			}

			$where['category_id'] = $category_id;
			$where['sub_category_id'] = $sub_category_id;
			$where['type'] = $type;

			$data = $database->get('site', ['title', 'description', 'keywords'], $where);

			echoJson($data);
			break;
		default:
			break;
	}

?>