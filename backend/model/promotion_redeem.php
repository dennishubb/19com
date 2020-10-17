<?php

    class promotion_redeem extends dbObject {
		
		protected $relations = Array(
			'promotion'		=> Array('hasOne', 'promotion', 'promotion_id'),
			'user'			=> Array('hasOne', 'user', 'user_id'),
			'admin'			=> Array('hasOne', 'user', 'admin_id', 'admin')
        );
        
        protected $dbFields = array(
            'promotion_id'  => array('int'),
            'user_id'   	=> array('int'),
			'status'		=> array('text'),
			'admin_id'		=> array('int'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );
    }

?>