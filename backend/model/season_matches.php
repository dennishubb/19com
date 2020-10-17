<?php

    class season_matches extends dbObject {   
		
        protected $relations = Array(
			//'category'			=> array('hasOne', 'category', 'category_id'),
			//'league'			=> array('hasOne', 'league', 'league_id'),
			//'home_team'			=> array('hasOne', 'team', 'home_team_id', 'home_team'),
			//'away_team'			=> array('hasOne', 'team', 'away_team_id', 'away_team'),
        );
		
        protected $dbFields = array(
			'match_id'			=> array('int'),
			'season_id'			=> array('int'),
			'category_id'		=> array('int'),
			'league_id'			=> array('int'),
			'match_type'		=> array('int'),
			'status'			=> array('int'),
			'match_time'		=> array('int'),
			'home_team_id'		=> array('int'),
			'away_team_id'		=> array('int'),
			'home_team_name'	=> array('text'),
			'away_team_name'	=> array('text'),
			'home_score'		=> array('int'),
			'away_score'		=> array('int'),
			'venue_id'			=> array('int'),
			'round_stage_id'	=> array('int'),
			'round_num'			=> array('int'),
			'group_num'			=> array('int'),
			'home_position'		=> array('int'),
            'season'			=> array('text'),
			'created_at'		=> array('datetime'),
			'updated_at'		=> array('datetime'),
        );
		
		public $memberFields = array('id', 'match_type', 'status', 'match_time', 'home_team_name', 'away_team_name', 'home_score', 'away_score');
		
		public $adminFields = array('id', 'match_type', 'status', 'match_time', 'home_team_name', 'away_team_name', 'home_score', 'away_score');
		
    }

?>