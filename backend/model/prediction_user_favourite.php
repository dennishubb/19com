<?php

    class prediction_user_favourite extends dbObject {
        protected $relations = Array(
            'user'  				=> Array('hasOne', 'user', 'user_id'),
            'prediction'  	=> Array('hasOne', 'prediction', 'prediction_id'),
        );

        protected $dbFields = array(
            'user_id'   			=> array('int', 'required'),
            'prediction_id'		 	=> array('int', 'required'),
			'prediction_type'		=> array('text', 'required'),
			'prediction_bet'		=> array('text', 'required'),
            'created_at'    		=> array('datetime')
        );
    }

?>