<?php

    class site extends dbObject {   
		
        protected $relations = Array(
            //'logo'  	=> Array("hasOne", "upload", 'logo_upload_id', 'logo'),
			//'favicon'	=> Array('hasOne', 'upload', 'favicon_upload_id', 'favicon'),
			//'sitemap'	=> Array('hasOne', 'upload', 'sitemap_upload_id', 'sitemap'),
			//'robots'	=> Array('hasOne', 'upload', 'robots_upload_id', 'robots')
        );
		
        protected $dbFields = array(
			'title'				=> array('text'),
			'description'		=> array('text'),
			'type'				=> array('text'),
			'category_id'		=> array('int'),
			'sub_category_id'	=> array('int'),
			'permission_id'		=> array('int'),
			'module_id'			=> array('int'),
            'logo_upload_id'	=> array('text'),
			'favicon_upload_id' => array('text'),
			'sitemap_upload_id'	=> array('int'),
			'robots_upload_id'	=> array('int'),
			'keywords'			=> array('text'),
			'created_at'		=> array('datetime'),
			'updated_at'		=> array('datetime'),
        );
		
		public $memberFields = array('title', 'description', 'keywords');
		
		public $adminFields = array('site.id', 'title', 'description', 'keywords', 'type', 'category_id', 'sub_category_id');
		
    }

?>