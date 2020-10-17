<?php

    class message_report extends dbObject {
		
		protected $relations = Array(
            'user' 		=> Array("hasOne", "user", 'user_id'),
			'message' 	=> Array("hasOne", "message", 'message_id'),
        );
        
        protected $dbFields = array(
            'user_id'       => array('int', 'required'),
            'message_id'   	=> array('int', 'required'),
			'report'   		=> array('text', 'required'),
            'created_at'    => array('datetime'),
        );
    }

?>