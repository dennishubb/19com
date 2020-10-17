<?php

    class prediction_points extends dbObject {
        protected $dbFields = array(
            'handicap_win'   	=> array('int'),
            'over_under_win'    => array('int'),
			'single_win'     	=> array('int'),
			'points'			=> array('double'),
            'created_at'    	=> array('datetime'),
			'updated_at'		=> array('datetime'),
        );
    }

?>