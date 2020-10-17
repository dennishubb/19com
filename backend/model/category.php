<?php

    class category extends dbObject {
		
		protected $relations = Array(
			//'upload'		=> Array('hasOne', 'upload', 'upload_id'),
        );
		
        protected $dbFields = array(
			'url'			=> array('text'),
            'name'       	=> array('text'),
            'display'       => array('text'),
            'description'   => array('text'),
			'type'			=> array('text'),
            'parent_id'  	=> array('int'),
			'upload_id'		=> array('int'),
			'sorting'		=> array('int'),
			'disabled'	 	=> array('int'),
            'created_at' 	=> array('datetime'),
            'updated_at' 	=> array('datetime'),
        );
		
		public $memberFields = array('category.id', 'category.display', 'category.parent_id', 'category.sorting', 'category.url', 'category.name');
		
		public $adminFields = array('category.id', 'category.display', 'category.parent_id', 'category.sorting', 'category.name', 'category.description', 'category.type', 'category.disabled', 'category.created_at');

		protected $joinFields = array('id', 'name', 'display');
		
    }

?>