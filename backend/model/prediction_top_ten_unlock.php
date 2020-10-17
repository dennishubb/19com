<?php

    class prediction_top_ten_unlock extends dbObject {
        protected $relations = Array(
            'user'  				=> Array('hasOne', 'user', 'user_id'),
            'prediction_top_ten'  	=> Array('hasOne', 'prediction_top_ten', 'prediction_top_ten_id'),
        );

        protected $dbFields = array(
            'user_id'   			=> array('int', 'required'),
			'event_id'				=> array('int'),
            'prediction_top_ten_id' => array('int'),
            'created_at'    		=> array('datetime')
        );
    }

?>