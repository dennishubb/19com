<?php

    class illegal_words extends dbObject {

        protected $dbFields = array(
            'word'       => array('text', 'required'),
			'regex'		 => array('int'),
			'disabled' 	 => array('int'),
            'created_at' => array('datetime'),
            'updated_at' => array('datetime'),
        );

    }

?>