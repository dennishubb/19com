<?php

    class user_level_up extends dbObject {

        protected $relations = Array(
            'user' 			=> Array("hasOne", "user", 'user_id'),
			'level' 		=> Array('hasOne', 'level', 'level_id'),
        );
        
        protected $dbFields = array(
			'user_id'			=> array('int', 'required'),
			'level_id'			=> array('int', 'required'),
			'claimed'			=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );

    }

?>