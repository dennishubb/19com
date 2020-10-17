<?php

    class permission extends dbObject {     
        protected $dbFields = array(
            'name'			=> array('text'),
			'description'  	=> array('text'),
            'type'         	=> array('text'),
            'parent_id'     => array('int'),
            'url' 	 		=> array('text'),
			'sorting' 		=> array('int'),
			'icon' 			=> array('text'),
			'disabled' 		=> array('int'),
			'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );
    }

?>