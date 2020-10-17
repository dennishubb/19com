<?php

    class season_ranking_basketball extends dbObject {   
		
        protected $relations = Array(
			//'team'				=> array('hasOne', 'team', 'team_id'),
			//'league'			=> array('hasOne', 'league', 'league_id')
        );
		
        protected $dbFields = array(
			'season_id'			=> array('int'),
			'team_id'			=> array('int'),
			'league_id'			=> array('int'),
			'scope'				=> array('int'),
			'name'				=> array('text'),
			'team_name'			=> array('text'),
			'position'			=> array('int'),
			'diff_avg'			=> array('double'),
			'streaks'			=> array('int'),
			'won'				=> array('int'),
			'lost'				=> array('int'),
			'home'				=> array('text'),
			'away'				=> array('text'),
			'points_avg'		=> array('double'),
			'poins_against_avg'	=> array('double'),
			'last_ten'			=> array('text'),
			'division'			=> array('text'),
			'game_back'			=> array('text'),
			'conference'		=> array('text'),
			'win_rate'			=> array('double'),
			'created_at'		=> array('datetime'),
			'updated_at'		=> array('datetime'),
        );
		
		public $memberFields = array('id', 'team_name', 'position', 'won', 'lost');
		
		public $adminFields = array('id', 'team_name', 'position', 'won', 'lost');
		
    }

?>