<?php

    class season_ranking_soccer extends dbObject {   
		
        protected $relations = Array(
			//'team'				=> array('hasOne', 'team', 'team_id'),
			//'league'			=> array('hasOne', 'league', 'league_id')
        );
		
        protected $dbFields = array(
			'season_id'				=> array('int'),
			'team_id'				=> array('int'),
			'league_id'				=> array('int'),
			'name'					=> array('text'),
			'team_name'				=> array('text'),
			'position'				=> array('int'),
			'conference'			=> array('text'),
			'points'				=> array('int'),
			'deduct_points'			=> array('int'),
			'note'					=> array('text'),
			'won'					=> array('int'),
			'draw'					=> array('int'),
			'lost'					=> array('int'),
			'total'					=> array('int'),
			'goals'					=> array('int'),
			'goals_against'			=> array('int'),
			'goals_diff'			=> array('int'),
			'home_points'			=> array('int'),
			'home_position'			=> array('int'),
			'home_total'			=> array('int'),
			'home_won'				=> array('int'),
			'home_draw'				=> array('int'),
			'home_loss'				=> array('int'),
			'home_goals'			=> array('int'),
			'home_goals_against'	=> array('int'),
			'home_goals_diff'		=> array('int'),
			'away_points'			=> array('int'),
			'away_position'			=> array('int'),
			'away_total'			=> array('int'),
			'away_won'				=> array('int'),
			'away_draw'				=> array('int'),
			'away_loss'				=> array('int'),
			'away_goals'			=> array('int'),
			'away_goals_against'	=> array('int'),
			'away_goals_diff'		=> array('int'),
			'created_at'			=> array('datetime'),
			'updated_at'			=> array('datetime'),
        );
		
		public $memberFields = array('id', 'team_name', 'position', 'won', 'lost', 'draw', 'points');
		
		public $adminFields = array('id', 'team_name', 'position', 'won', 'lost', 'draw', 'points');
		
    }

?>