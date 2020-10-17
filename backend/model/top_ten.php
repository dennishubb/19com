<?php

    class top_ten extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'category'  => Array('hasOne', 'category', 'category_id'),
        );

        protected $dbFields = array(
            'user_id'   		=> array('int', 'required'),
            'category_id'     	=> array('int'),
			'league_id'			=> array('int'),
			'points'			=> array('double'),
			'prediction_count'	=> array('int'),
			'rank'				=> array('int'),
			'month'				=> array('int'),
			'year'				=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
    }

?>