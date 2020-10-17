<?php

    class seo_module extends dbObject {
        protected $dbFields = array(
            'name'   		=> array('text'),
			'created_at'	=> array('datetime'),
            'updated_at'    => array('datetime'),
        );
    }

?>