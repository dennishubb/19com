<?php

    class event extends dbObject {
        protected $relations = Array(
            'category'  		=> Array('hasOne', 'category', 'category_id'),
            'league'    		=> Array('hasOne', 'league', 'league_id'),
			'home_team'  		=> Array('hasOne', 'team', 'home_team_id', 'home_team'),
			'away_team'  		=> Array('hasOne', 'team', 'away_team_id', 'away_team'),
			'home_team_upload'	=> Array('hasOne', 'upload', 'home_team_upload_id', 'home_team_upload'),
			'away_team_upload'	=> Array('hasOne', 'upload', 'away_team_upload_id', 'away_team_upload'),
			'winning_team'  	=> Array('hasOne', 'team', 'winning_team_id', 'winning_team'),
			'chatroom_id'		=> Array('hasOne', 'chatroom', 'chatroom_id'),
			'message'			=> Array('hasMany', 'message', 'chatroom_id', 'chatroom_id'),
			'prediction'		=> Array('hasMany', 'prediction', 'event_id'),
			'result'			=> Array('hasMany', 'result', 'event_id')
        );
        
        protected $dbFields = array(
            'category_id'   		=> array('int'),
            'league_id'     		=> array('int'),
			'season_id'				=> array('int'),
            'home_team_id'      	=> array('int'),
            'away_team_id'   		=> array('int'),
			'home_team_upload_id'	=> array('int'),
			'away_team_upload_id'	=> array('int'),
			'match_at'				=> array('datetime'),
            'prediction_end_at'		=> array('datetime'),
            'round'        			=> array('text'),
            'handicap_home_bet'     => array('text'),
			'handicap_home_odds'	=> array('text'),
            'handicap_away_bet'     => array('text'),
			'handicap_away_odds'	=> array('text'),
            'over_under_home_bet'   => array('text'),
			'over_under_home_odds'	=> array('text'),
			'over_under_away_bet'   => array('text'),
			'over_under_away_odds'	=> array('text'),
            'single_home'       	=> array('text'),
            'single_tie'        	=> array('text'),
            'single_away'       	=> array('text'),
			'winning_team_id'		=> array('int'),
			'ended'					=> array('int'),
            'editor_note'       	=> array('text'),
            'chatroom_id'      		=> array('int'),
            'disabled'     			=> array('int'),
            'created_at'    		=> array('datetime'),
            'updated_at'    		=> array('datetime')
        );
		
		public $memberFields = array('event.id', 'event.category_id', 'event.league_id', 'event.home_team_id', 'event.away_team_id', 'event.home_team_upload_id', 'event.away_team_upload_id', 'match_at', 'prediction_end_at', 'round', 'handicap_home_bet', 'handicap_home_odds', 'handicap_away_bet', 'handicap_away_odds', 'over_under_home_bet', 'over_under_home_odds', 'over_under_away_bet', 'over_under_away_odds', 'single_home', 'single_tie', 'single_away', 'editor_note', 'event.chatroom_id');
		
		public $adminFields = array('event.id', 'event.category_id', 'event.league_id', 'event.home_team_id', 'event.away_team_id', 'event.home_team_upload_id', 'event.away_team_upload_id', 'match_at', 'prediction_end_at', 'round', 'handicap_home_bet', 'handicap_home_odds', 'handicap_away_bet', 'handicap_away_odds', 'over_under_home_bet', 'over_under_home_odds', 'over_under_home_odds', 'over_under_away_bet', 'over_under_away_odds', 'single_home', 'single_tie', 'single_away', 'editor_note', 'event.chatroom_id', 'event.disabled', 'event.created_at', 'event.updated_at');
		
		public $joinFields = array('id', 'match_at', 'round', 'handicap_home_bet', 'handicap_home_odds', 'handicap_away_bet', 'handicap_away_odds', 'over_under_home_bet', 'over_under_home_odds', 'over_under_home_odds', 'over_under_away_bet', 'over_under_away_odds', 'single_home', 'single_tie', 'single_away',);
    }

?>