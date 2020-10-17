<?php

    class message_like extends dbObject {
		
		protected $relations = Array(
            //'user' 		=> Array("hasOne", "user", 'user_id'),
			//'message' 	=> Array("hasOne", "message", 'message_id'),
			//'article' 	=> Array("hasOne", "article", 'chatroom_id'),
        );
        
        protected $dbFields = array(
            'user_id'       => array('int', 'required'),
            'message_id'   	=> array('int', 'required'),
            'created_at'    => array('datetime'),
			'updated_at'	=> array('datetime'),
        );
    }

?>