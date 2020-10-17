<?php
	include __DIR__.'/../common/Medoo.php';

	include '/usr/local/xunsearch/sdk/php/lib/XS.php';

	$datas = $database->select('article', ['id', 'title',  'content', 'tags', 'category', 'sub_category', 'category_id', 'sub_category_id', 'thumbnail_small', 'thumbnail_small_h5', 'active_at']);

	foreach ($datas as $key => $value) {
		$data = array(
		    'id' => $value['id'],
		    'title' => strtoupper($value['title']),
		    'content' => $value['content'],
		    'category_id' => strtoupper($value['category_id']),
		    'sub_category_id' => strtoupper($value['sub_category_id']),
		    'category' => strtoupper($value['category']),
		    'sub_category' => strtoupper($value['sub_category']),
		    'tags' => strtoupper($value['tags']),
		    'thumbnail_web' => $value['thumbnail_small'],
		    'thumbnail_h5' => $value['thumbnail_small_h5'],
		    'active_at' => $value['active_at']
		);


		$doc = new XSDocument;
		$doc->setFields($data);
		
		$xs = new XS('article');
				      
		$index = $xs->index;
		$index->add($doc);
	}
?>


