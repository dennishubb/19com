<?php

    class season_list extends dbObject {   
		
        protected $relations = Array(
			//'category'			=> array('hasOne', 'category', 'category_id'),
			//'league'			=> array('hasOne', 'league', 'league_id')
        );
		
        protected $dbFields = array(
			'season_id'			=> array('int'),
			'category_id'		=> array('int'),
			'league_id'			=> array('int'),
            'season'			=> array('text'),
			'current'			=> array('int'),
			'created_at'		=> array('datetime'),
			'updated_at'		=> array('datetime'),
        );
		
		public $memberFields = array('id', 'season_id');
		
		public $adminFields = array('id', 'season_id');
		
    }

?>