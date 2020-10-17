<?php

    class season_player_stats_soccer extends dbObject {   
		
       protected $relations = Array(
			//'league'			=> array('hasOne', 'league', 'league_id'),
			//'team'				=> array('hasOne', 'team', 'team_id'),
        );
		
        protected $dbFields = array(
			'player_id'				=> array('int'),
			'season_id'				=> array('int'),
			'category_id'			=> array('int'),
			'league_id'				=> array('int'),
			'team_id'				=> array('int'),
			'player_name'			=> array('text'),
			'team_name'				=> array('text'),
			'rating'				=> array('int'),
			'matches'				=> array('int'),
			'first'					=> array('int'),
			'goals'					=> array('int'),
			'penalty'				=> array('int'),
			'minutes_played'		=> array('int'),
			'red_cards'				=> array('int'),
			'yellow_cards'			=> array('int'),
			'shots'					=> array('int'),
			'shots_on_target'		=> array('int'),
			'dribble'				=> array('int'),
			'dribble_success'		=> array('int'),
			'clearances'			=> array('int'),
			'blocked_shots'			=> array('int'),
			'interceptions'			=> array('int'),
			'tackles'				=> array('int'),
			'passes'				=> array('int'),
			'passes_accuracy'		=> array('int'),
			'key_passes'			=> array('int'),
			'crosses'				=> array('int'),
			'crosses_accuracy'		=> array('int'),
			'long_balls'			=> array('int'),
			'long_balls_accuracy'	=> array('int'),
			'duels'					=> array('int'),
			'duels_won'				=> array('int'),
			'dispossessed'			=> array('int'),
			'fouls'					=> array('int'),
			'was_fouled'			=> array('int'),
			'saves'					=> array('int'),
			'punches'				=> array('int'),
			'runs_out'				=> array('int'),
			'runs_out_success'		=> array('int'),
			'good_high_claim'		=> array('int'),
			'created_at'			=> array('datetime'),
			'updated_at'			=> array('datetime'),
        );
		
		public $memberFields = array('id', 'player_name', 'team_name', 'rating', 'goals', 'penalty');
		
		public $adminFields = array('id', 'player_name', 'team_name', 'rating', 'goals', 'penalty');
		
    }

?>