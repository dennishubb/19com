<?php

    class level extends dbObject {
		
		protected $relations = Array(
            'upload' 	=> Array("hasOne", "upload", 'upload_id'),
			'user'		=> Array('hasMany', 'user', 'level_id')
        );
        
        protected $dbFields = array(
            'name'          		=> array('text'),
            'description'   		=> array('text'),
			'reward_description'	=> array('text'),
            'points'        		=> array('double'),
			'voucher'				=> array('double'),
			'ticket'				=> array('double'),
			'sorting'				=> array('int'),
            'top_ten'       		=> array('int'),
            'upload_id'     		=> array('int'),
            'system'     			=> array('int'),
            'created_at'    		=> array('datetime'),
            'updated_at'    		=> array('datetime'),
        );
		
		protected $joinFields = array('id', 'name');
    }

?>