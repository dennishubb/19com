<?php

    class accounting extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'from'    	=> Array('hasOne', 'user', 'from_id', 'from'),
			'to'    	=> Array('hasOne', 'user', 'to_id', 'to'),
			'credit'    => Array('hasOne', 'credit', 'credit_id'),
        );
        
        protected $dbFields = array(
            'credit'   		=> array('double'),
			'debit'			=> array('double'),
            'user_id'     	=> array('int'),
            'from_id'       => array('int'),
            'to_id'   		=> array('int'),
			'reference_id'  => array('int'),
            'credit_id'  	=> array('int'),
            'deleted' 		=> array('int'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
		
		protected $privacySetting = 1;
		
    }

?>