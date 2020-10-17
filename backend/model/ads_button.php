<?php

    class ads_button extends dbObject {
        
        protected $relations = Array(
            'upload'  => Array("hasOne", "upload", 'image_upload_id'),
        );

        protected $dbFields = array(
            'image_upload_id'	=> array('int'),
			'url'  				=> array('text', 'required'),
            'target'         	=> array('text'),
            'rel'        		=> array('text'),
            'type' 	 			=> array('text'),
			'display' 			=> array('text'),
			'qr_code' 			=> array('text'),
			'disabled' 			=> array('int'),
			'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );

		protected $memberFields = array('image_upload_id', 'url', 'target', 'rel', 'type', 'display', 'qr_code');
		
		protected $adminFields  = array('image_upload_id', 'url', 'target', 'rel', 'type', 'display', 'qr_code', 'disabled', 'created_at', 'updated_at');
		
    }

?>