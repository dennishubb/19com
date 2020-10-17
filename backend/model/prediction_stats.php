<?php

    class prediction_stats extends dbObject {
        protected $relations = Array(
            //'user'  	=> Array('hasOne', 'user', 'user_id'),
            //'category'  => Array('hasOne', 'category', 'category_id'),
        );

        protected $dbFields = array(
            'user_id'   					=> array('int', 'required'),
            'category_id'     				=> array('int', 'required'),
			'league_id'     				=> array('int', 'required'),
			'prediction_count'				=> array('int'),
			'prediction_total_count'		=> array('int'),
			'win_rate'						=> array('double'),
			'total_win_rate'				=> array('double'),
			'top_ten_count'					=> array('int'),
			'top_ten_total_count'			=> array('int'),
			'month'							=> array('int'),
			'year'							=> array('int'),
            'created_at'    				=> array('datetime'),
            'updated_at'    				=> array('datetime')
        );
		
		public $privacySetting = 1;
		
		public $memberFields = array('prediction_stats.id', 'prediction_count', 'prediction_total_count', 'win_rate', 'total_win_rate', 'top_ten_count', 'top_ten_total_count');
		
		public $adminFields = array('prediction_stats.id', 'prediction_count', 'prediction_total_count', 'win_rate', 'total_win_rate', 'top_ten_count', 'top_ten_total_count', 'year', 'month');
    }

?>