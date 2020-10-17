<?php

    class transaction extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'from'    	=> Array('hasOne', 'user', 'from_id', 'from'),
			'to'    	=> Array('hasOne', 'user', 'to_id', 'to'),
			'credit'    => Array('hasOne', 'credit', 'credit_id'),
        );
        
        protected $dbFields = array(
            'user_id'   	=> array('int'),
			'amount'		=> array('double'),
            'reference_id'  => array('int'),
            'credit_id'     => array('int'),
            'subject'   	=> array('text'),
			'remark'  		=> array('text'),
            'from_id'  		=> array('int'),
            'to_id' 		=> array('int'),
			'deleted'		=> array('text'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
    }

?>