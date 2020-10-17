<?php

    class prediction_rate extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'category'  => Array('hasOne', 'category', 'category_id'),
			'league'	=> Array('hasOne', 'league', 'league_id'),
        );

        protected $dbFields = array(
            'user_id'   				=> array('int', 'required'),
            'category_id'     			=> array('int'),
			'league_id'     			=> array('int'),
			'season_id'     			=> array('int'),
			'type'		     			=> array('text'),
			'win_count'					=> array('int'),
			'lose_count'				=> array('int'),
			'total_points'				=> array('double'),
			'total_count'				=> array('int'),
			'season_win_count'			=> array('int'),
			'season_lose_count'			=> array('int'),
			'season_total_count'		=> array('int'),
			'season_rate'				=> array('double'),
			'rate'						=> array('double'),
			'rank'						=> array('int'),
			'top_ten_season_rate'		=> array('double'),
			'top_ten_rate'				=> array('double'),
			'top_ten_prediction_count'	=> array('int'),
			'sorting'					=> array('int'),
			'qualified'					=> array('int'),
			'disabled'					=> array('int'),
			'month'						=> array('int'),
			'year'						=> array('int'),
            'created_at'    			=> array('datetime'),
            'updated_at'    			=> array('datetime')
        );
		
		public $privacySetting = 1;
		
		public $memberFields = array('prediction_rate.id', 'prediction_rate.type', 'prediction_rate.win_count', 'prediction_rate.lose_count', 'prediction_rate.total_count', 'prediction_rate.rate', 'prediction_rate.rank', 'prediction_rate.sorting', 'season_rate', 'top_ten_season_rate', 'top_ten_rate', 'top_ten_prediction_count', 'prediction_rate.category_id', 'prediction_rate.league_id');
		
		public $adminFields = array('prediction_rate.id', 'prediction_rate.type', 'prediction_rate.win_count', 'prediction_rate.lose_count', 'prediction_rate.total_points', 'prediction_rate.total_count', 'prediction_rate.rate', 'prediction_rate.rank', 'prediction_rate.sorting', 'prediction_rate.user_id', 'prediction_rate.category_id', 'prediction_rate.league_id', 'prediction_rate.month', 'prediction_rate.year', 'top_ten_season_rate', 'top_ten_rate', 'top_ten_prediction_count',);
    }

?>