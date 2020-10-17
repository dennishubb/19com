<?php

    class fan_zone extends dbObject {
        protected $relations = Array(
            'upload'  	=> Array('hasOne', 'upload', 'upload_id'),
        );

        protected $dbFields = array(
            'upload_id'   		=> array('int'),
            'sorting'		    => array('int'),
            'url'     			=> array('text'),
			'disabled'     		=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime')
        );
    }

?>