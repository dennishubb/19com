<?php

    class result extends dbObject {
        protected $relations = Array(
            'event'  	=> Array('hasOne', 'event', 'event_id'),
			//'team'		=> Array('hasOne', 'team', 'team_id'),
        );
        
        protected $dbFields = array(
            'event_id'   		=> array('int'),
            'team_id'     		=> array('int'),
            'handicap_home'		=> array('int'),
			'handicap_away'		=> array('int'),
			'over_under_home'	=> array('int'),
			'over_under_away'	=> array('int'),
			'single_home'		=> array('int'),
			'single_away'		=> array('int'),
			'single_tie'		=> array('int'),
			'handicap_odds'		=> array('text'),
			'handicap_bet'		=> array('text'),
			'over_under_odds'	=> array('text'),
			'over_under_bet'	=> array('text'),
            'created_at'		=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
		
		public $memberFields = array('result.id', 'result.event_id', 'result.handicap_home', 'result.handicap_away', 'result.over_under_home', 'result.over_under_away', 'result.single_home', 'result.single_away', 'result.single_tie', 'result.handicap_odds', 'result.handicap_bet', 'result.over_under_odds', 'result.over_under_bet', 'result.created_at');
		
		public $adminFields = array('result.id', 'result.event_id', 'result.handicap_home', 'result.handicap_away', 'result.over_under_home', 'result.over_under_away', 'result.single_home', 'result.single_away', 'result.single_tie', 'result.handicap_odds', 'result.handicap_bet', 'result.over_under_odds', 'result.over_under_bet', 'result.created_at');
		
		public $joinFields = array('id', 'handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'handicap_odds', 'handicap_bet', 'over_under_odds', 'over_under_bet');
    }

?>