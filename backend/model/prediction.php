<?php

    class prediction extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'event'    	=> Array('hasOne', 'event', 'event_id'),
        );
        
        protected $dbFields = array(
            'user_id'   		=> array('int', 'required'),
            'event_id'     		=> array('int', 'required'),
			'season_id'			=> array('int'),
			'selected_team_id'	=> array('int'),
			'handicap_home'		=> array('int'),
			'handicap_away'		=> array('int'),
			'over_under_home'	=> array('int'),
			'over_under_away'	=> array('int'),
			'single_home'		=> array('int'),
			'single_away'		=> array('int'),
			'single_tie'		=> array('int'),
			'handicap_win'		=> array('int'),
			'over_under_win'	=> array('int'),
			'single_win'		=> array('int'),
			'win'				=> array('int'),
			'win_amount'		=> array('double'),
            'amount'      		=> array('double'),
            'status'        	=> array('text'),
            'disabled	'     	=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
			'win_at'			=> array('datetime')
        );
		
		public $privacySetting = 1;
		
		public $memberFields = array('prediction.id', 'prediction.user_id', 'prediction.event_id', 'prediction.handicap_home', 'prediction.handicap_away', 'prediction.over_under_home', 'prediction.over_under_away', 'prediction.single_home', 'prediction.single_away', 'prediction.single_tie', 'prediction.handicap_win', 'prediction.over_under_win', 'prediction.single_win', 'win', 'win_amount', 'prediction.status', 'prediction.created_at');
		
		public $adminFields = array('prediction.id', 'prediction.user_id', 'prediction.event_id', 'prediction.handicap_home', 'prediction.handicap_away', 'prediction.over_under_home', 'prediction.over_under_away', 'prediction.single_home', 'prediction.single_away', 'prediction.single_tie', 'prediction.handicap_win', 'prediction.over_under_win', 'prediction.single_win', 'win', 'win_amount', 'prediction.status', 'prediction.created_at', 'prediction.disabled');
    }

?>