<?php

    class promotion extends dbObject {
		
		protected $relations = Array(
			'upload_small'		=> Array('hasOne', 'upload', 'upload_id_small', 'upload_small'),
			'upload_medium'		=> Array('hasOne', 'upload', 'upload_id_medium', 'upload_medium'),
			'upload_big'		=> Array('hasOne', 'upload', 'upload_id_big', 'upload_big')
        );
        
        protected $dbFields = array(
						'name'          	=> array('text'),
						'sorting'		    => array('int'),
			'type'				=> array('text'),
            'upload_id_small'   => array('int'),
            'upload_id_medium'  => array('int'),
            'upload_id_big'     => array('int'),
            'start_at'     		=> array('datetime'),
            'end_at'     		=> array('datetime'),
			'settle_at'			=> array('datetime'),
			'level_id'			=> array('text'),
			'limitation'		=> array('text'),
			'limitation_count'	=> array('int'),
			'introduction'		=> array('text'),
			'disabled'			=> array('int'),
			'display_method'	=> array('text'),
			'sign_up'			=> array('int'),
			'url'				=> array('text'),
			'points'			=> array('double'),
			'voucher'			=> array('double'),
			'system'			=> array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
        );
		
		public $memberFields = array('promotion.id', 'promotion.name', 'upload_id_small', 'upload_id_medium', 'upload_id_big', 'introduction', 'display_method', 'sign_up', 'promotion.url');
		
		public $adminFields = array('promotion.id', 'promotion.name', 'upload_id_small', 'upload_id_medium', 'upload_id_big', 'introduction', 'display_method', 'sign_up', 'promotion.url', 'start_at', 'sorting', 'end_at', 'settle_at', 'level_id', 'limitation', 'limitation_count', 'promotion.disabled', 'points', 'voucher', 'promotion.system', 'promotion.created_at');
    }

?>