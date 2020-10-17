<?php

    class feedback extends dbObject {

        protected $relations = Array(

        );
        
        protected $dbFields = array(
			'type'				=> array('text'),
			'message'			=> array('text'),
			'email'				=> array('text'),
			'status'			=> array('text'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );

    }

?>