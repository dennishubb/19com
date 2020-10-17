<?php

	require  '/usr/local/xunsearch/sdk/php/lib/XS.php';

	$so = scws_new(); //Create an object
	$so->set_charset('utf8'); //Set UTF8
	$so->set_ignore(true); //filter symbol
	$so->send_text($_POST['search_text']);
	$result = $so->get_result();
	$search_text = "";
	//print_r($result);
	foreach($result as $soObj){
			$search_text .= $soObj['word']." ";
	}
	$search_text .= $_POST['search_text'];

	$so->close();
	$xs = new XS('article');
	$search = $xs->search;

	$search->setFuzzy(true);
	$search->setQuery($search_text);
	$search->setSort('active_at', false);
	$search->setLimit(10, 0);

	$result = $search->search();

	$search_result = array();
	foreach($result as $XSObj){
			$temp['id'] = $XSObj->id;
			$temp['title'] = $XSObj->title;
			$temp['sub_category'] = $XSObj->sub_category;
			$temp['active_at'] = $XSObj->active_at;
			$temp['thumbnail'] = $XSObj->thumbnail;

			$search_result[] = $temp;
	}

	header('Content-type: application/json');
	echo json_encode($search_result);

?>