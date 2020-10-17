<?php

    class forget_password extends dbObject {
		protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
        );
		
        protected $dbFields = array(
			'user_id'			=> array('int'),
            'phone'   			=> array('text'),
            'verification_code' => array('text'),
			'type'				=> array('text'),
			'status'     		=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
    }

?>