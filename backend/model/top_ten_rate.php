<?php

    class top_ten_rate extends dbObject {
        protected $relations = Array(
            'category'  => Array('hasOne', 'category', 'category_id'),
			'league'  => Array('hasOne', 'league', 'league_id'),
        );

        protected $dbFields = array(
            'category_id'     	=> array('int'),
			'league_id'			=> array('int'),
			'min_rate'			=> array('double'),
			'season_min_rate'	=> array('double'),
			'prediction_count'	=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
    }

?>