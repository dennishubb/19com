<?php

    class area extends dbObject {    
        protected $dbFields = array(
            'category_id'   => array('int'),
            'name_en'     	=> array('text'),
            'name_zh'       => array('text'),
            'name_zht'   	=> array('text'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime')
        );
    }

?>