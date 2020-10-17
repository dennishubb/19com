<?php

    class league extends dbObject {
        protected $relations = Array(
            'category'  => Array('hasOne', 'category', 'category_id'),
            //'area'    	=> Array('hasOne', 'area', 'area_id'),
			//'country'  	=> Array('hasOne', 'country', 'country_id'),
        );
        
        protected $dbFields = array(
			'api_id'		=> array('int'),
            'category_id'   => array('int'),
			'area_id'		=> array('int'),
			'country_id'	=> array('int'),
            'name_en'     	=> array('text'),
            'name_zh'       => array('text'),
            'name_zht'   	=> array('text'),
			'shortname_en'  => array('text'),
            'shortname_zh'  => array('text'),
            'shortname_zht' => array('text'),
			'logo'			=> array('text'),
			'use_count'		=> array('int'),
			'has_event'		=> array('int'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
		
		public $memberFields = array('league.id', 'league.category_id', 'league.name_zh');
		
		public $adminFields = array('league.id', 'league.category_id', 'league.name_zh', 'top_ten_rate.min_rate', 'top_ten_rate.season_min_rate', 'top_ten_rate.prediction_count', 'top_ten_rate.id');
		
		protected $joinFields = array('id', 'name_zh');
    }

?>