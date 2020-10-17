<?php

    class article extends dbObject {
        protected $relations = Array(
            'category'  	=> Array('hasOne', 'category', 'category_id'),
			'sub_category'	=> Array('hasOne', 'category', 'sub_category_id', 'sub_category'),
            'upload'    	=> Array('hasOne', 'upload', 'upload_id'),
			'chatroom'  	=> Array('hasOne', 'chatroom', 'chatroom_id'),
        );
        
        protected $dbFields = array(
            'title'         	=> array('text'),
            'seo_title'     	=> array('text'),
            'author'        	=> array('text'),
            'category_id'   	=> array('int'),
			'sub_category_id'	=> array('int'),
			'chatroom_id'		=> array('int'),
            'content'       	=> array('text'),
            'status'        	=> array('text'),
            'tags'          	=> array('text'),
            'upload_id'     	=> array('int'),
            'description'   	=> array('text'),
            'keywords'      	=> array('text'),
            'sorting'       	=> array('int'),
            'hot'           	=> array('int'),
            'popular'       	=> array('int'),
            'draft'         	=> array('int'),
			'type'				=> array('text'),
            'disabled'      	=> array('int'),
			'deleted'			=> array('int'),
			'view_count'		=> array('int'),
			'comment_count'		=> array('int'),
            'active_at'     	=> array('datetime'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
		
		public $memberFields = array('article.id', 'article.title', 'article.seo_title', 'author', 'article.category_id', 'article.sub_category_id', 'article.chatroom_id', 'content', 'tags', 'article.upload_id', 'article.description', 'article.keywords', 'article.view_count', 'article.active_at');
		
		public $adminFields = array('article.id', 'article.title', 'article.seo_title', 'author', 'article.category_id', 'article.sub_category_id', 'article.chatroom_id', 'content', 'tags', 'article.upload_id', 'article.description', 'article.keywords', 'article.view_count', 'article.active_at', 'article.created_at', 'article.hot', 'article.popular', 'article.draft', 'article.comment_count', 'article.disabled', 'article.sorting', 'category.display', 'sub_category.display');
		
		public $joinFields = array('id', 'title', 'active_at', 'tags', 'content', 'description');
    }

?>