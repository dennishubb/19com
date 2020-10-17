<?php

    class gift extends dbObject {

        protected $relations = Array(
            'category' 		=> Array("hasOne", "category", 'category_id'),
			'sub_category'	=> Array('hasOne', 'category', 'sub_category_id', 'sub_category'),
			'upload'		=> Array('hasOne', 'upload', 'upload_id'),
        );
        
        protected $dbFields = array(
            'category_id'       => array('int'),
            'sub_category_id'	=> array('int'),
            'name'        		=> array('text'),
            'url'       		=> array('text'),
            'upload_id'     	=> array('int'),
            'points'     		=> array('double'),
			'size'     			=> array('text'),
			'size_type'			=> array('text'),
            'color'     		=> array('text'),
			'hot_category'		=> array('text'),
			'amount'     		=> array('double'),
            'start_at'     		=> array('datetime'),
			'end_at'			=> array('datetime'),
			'disabled'			=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );
		
		public $memberFields = array('gift.id', 'gift.category_id', 'gift.sub_category_id', 'gift.name', 'gift.url', 'gift.upload_id', 'points', 'gift.size', 'size_type', 'color', 'hot_category', 'amount');
		
		public $adminFields = array('gift.id', 'gift.category_id', 'gift.sub_category_id', 'gift.name', 'gift.url', 'gift.upload_id', 'points', 'gift.size', 'size_type', 'color', 'hot_category', 'amount', 'gift.start_at', 'gift.end_at', 'gift.disabled', 'gift.created_at', 'gift.updated_at', 'category.display', 'sub_category.display');
		
		public $joinFields = array('id', 'name');
    }

?>