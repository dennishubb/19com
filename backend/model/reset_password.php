<?php

    class reset_password extends dbObject {
		protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
        );
		
        protected $dbFields = array(
			'user_id'			=> array('int'),
            'verification_code' => array('text'),
			'old_password'	    => array('text'),
			'new_password'    	=> array('text'),
            'created_at'    	=> array('datetime'),
        );
    }

?>