<?php

    class team extends dbObject {
        protected $relations = Array(
            'category'  => Array('hasOne', 'category', 'category_id'),
            'league'    => Array('hasOne', 'league', 'league_id'),
        );
        
        protected $dbFields = array(
			'api_id'		=> array('int'),
            'category_id'   => array('int'),
			'league_id'		=> array('int'),
            'name_en'     	=> array('text'),
            'name_zh'       => array('text'),
            'name_zht'   	=> array('text'),
			'shortname_en'  => array('text'),
            'shortname_zh'  => array('text'),
            'shortname_zht' => array('text'),
			'logo'			=> array('text'),
			'use_count'		=> array('int'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
		
		protected $joinFields = array('id', 'name_zh');
    }

?>