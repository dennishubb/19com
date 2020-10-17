<?php

    class season_player_stats_basketball extends dbObject {   
		
        protected $relations = Array(
			//'category'			=> array('hasOne', 'category', 'category_id'),
			//'league'			=> array('hasOne', 'league', 'league_id'),
			//'team'				=> array('hasOne', 'team', 'team_id'),
        );
		
        protected $dbFields = array(
			'category_id'			=> array('int'),
			'season_id'				=> array('int'),
			'player_id'				=> array('int'),
			'league_id'				=> array('int'),
			'team_id'				=> array('int'),
			'player_name'			=> array('text'),
			'team_name'				=> array('text'),
			'scope'					=> array('int'),
			'matches'				=> array('int'),
			'first'					=> array('int'),
			'court'					=> array('int'),
			'minutes_played'		=> array('int'),
			'points'				=> array('int'),
			'points_avg'			=> array('double'),
			'two_points_scored'		=> array('int'),
			'two_points_total'		=> array('int'),
			'two_points_accuracy'	=> array('double'),
			'three_points_scored'	=> array('int'),
			'three_points_total'	=> array('int'),
			'three_points_accuracy'	=> array('double'),
			'field_points_scored'	=> array('int'),
			'field_points_total'	=> array('int'),
			'field_points_accuracy'	=> array('double'),
			'free_throw_scored'		=> array('int'),
			'free_throw_total'		=> array('int'),
			'free_throw_accuracy'	=> array('double'),
			'personal_fouls'		=> array('int'),
			'rebounds'				=> array('int'),
			'defensive_rebounds'	=> array('int'),
			'offensive_rebounds'	=> array('int'),
			'assists'				=> array('int'),
			'turnovers'				=> array('int'),
			'steals'				=> array('int'),
			'blocks'				=> array('int'),
			'created_at'			=> array('datetime'),
			'updated_at'			=> array('datetime'),
        );
		
		public $memberFields = array('id', 'player_name', 'team_name', 'points', 'points_avg');
		
		public $adminFields = array('id', 'player_name', 'team_name', 'points', 'points_avg');
		
    }

?>