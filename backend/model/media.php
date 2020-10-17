<?php

    class media extends dbObject {
        
        protected $relations = Array(
            'category'  => Array("hasOne", "category", 'category_id'),
			'upload'	=> Array('hasOne', 'upload', 'upload_id')
        );

        protected $dbFields = array(
            'category_id'	=> array('int', 'required'),
			'upload_id'  	=> array('int'),
            'disabled'      => array('int'),
			'created_at'   	=> array('datetime'),
            'updated_at'    => array('datetime'),
        );

    }

?>