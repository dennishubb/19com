<?php

    class role extends dbObject {     
        protected $dbFields = array(
            'name'			=> array('text'),
			'description' 	=> array('text'),
			'disabled'		=> array('int'),
			'created_at'	=> array('datetime'),
			'updated_at'	=> array('datetime'),
        );
    }

?>