<?php

	include_once("/usr/local/xunsearch/sdk/php/lib/XS.php");
	include(__DIR__.'/include/shared_function.inc.php');
        include(__DIR__.'/config/app.config.inc.php');
        include(__DIR__.'/config/config.'.SERVER_STATE.'.inc.php');
        include(__DIR__.'/include/db.inc.php');
        include(__DIR__.'/model/category.php');
	include(__DIR__.'/model/article.php');

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
		$category = $category_display[$articleObj->category_id];
		$sub_category = $category_display[$articleObj->sub_category_id];
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

?>
