<?php

    class role_permission extends dbObject {   
        protected $relations = Array(
            'role'  		=> Array("hasOne", "role", 'role_id'),
			'permission'	=> Array('hasOne', 'permission', 'permission_id')
        );
		
        protected $dbFields = array(
            'role_id'		=> array('int'),
			'permission_id' => array('int'),
			'disabled' 		=> array('int'),
			'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );
    }

?>