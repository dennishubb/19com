<?php

    class balance extends dbObject {
        protected $relations = Array(
            'user' 		=> Array('hasOne', 'user', 'user_id'),
            'credit'    => Array('hasOne', 'credit', 'credit_id'),
        );
        
        protected $dbFields = array(
            'user_id'   	=> array('int'),
			'credit_id'		=> array('int'),
            'date'     		=> array('text'),
            'balance'       => array('double'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
    }

?>