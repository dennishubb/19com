<?php

    class tutorial extends dbObject {
        protected $dbFields = array(
            'content'   	=> array('text'),
			'created_at'	=> array('datetime'),
            'updated_at'    => array('datetime'),
        );
    }

?>