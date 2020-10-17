<?php

    class credit extends dbObject {
        
        protected $dbFields = array(
            'name'      	=> array('text'),
            'display'   	=> array('text'),
            'disabled'      => array('double'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );
		
    }

?>