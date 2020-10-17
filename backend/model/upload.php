<?php

    class upload extends dbObject {
		protected $relations = Array(
            //'category'  	=> Array('hasOne', 'category', 'category_id'),
            //'sub_category'  => Array('hasOne', 'category', 'sub_category_id', 'sub_category'),
			'article'		=> Array('hasOne', 'article', 'article_id'),
        );

        protected $dbFields = array(
			'category_id'		=> array('int'),
			'sub_category_id'	=> array('int'),
			'article_id'		=> array('int'),
            'url'           	=> array('text', 'required'),
			'md5'				=> array('text'),
            'name'          	=> array('text'),
            'type'          	=> array('text'),
            'size'          	=> array('int'),
            'resolution'    	=> array('text'),
            'alt'           	=> array('text'),
            'extension'     	=> array('text'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );
		
		public $memberFields = array('upload.id', 'article_id', 'upload.url', 'alt', 'md5', 'upload.name', 'upload.type', 'size', 'resolution', 'extension');
		
		public $adminFields = array('upload.id', 'article_id', 'upload.url', 'alt', 'md5', 'upload.name', 'upload.type', 'size', 'resolution', 'extension', 'upload.created_at');

		protected $joinFields = array('id', 'url', 'article_id');
    }

?>