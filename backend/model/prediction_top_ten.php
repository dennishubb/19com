<?php

    class prediction_top_ten extends dbObject {
        protected $relations = Array(
            'user'  	=> Array('hasOne', 'user', 'user_id'),
            'event'  	=> Array('hasOne', 'event', 'event_id'),
        );

        protected $dbFields = array(
            'user_id'   		=> array('int', 'required'),
            'event_id'     		=> array('int', 'required'),
            'created_at'    	=> array('datetime')
        );
    }

?>