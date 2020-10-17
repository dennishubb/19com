<?php

    class adjustment extends dbObject {
		
		protected $relations = Array(
			'user'		=> Array('hasOne', 'user', 'user_id'),
			'admin'		=> Array('hasOne', 'user', 'admin_id', 'admin'),
        );
        
        protected $dbFields = array(
            'user_id'          	=> array('int'),
            'reference_id'   	=> array('int'),
            'points_before'     => array('double'),
            'points'       		=> array('double'),
            'points_after'     	=> array('double'),
            'points_id'     	=> array('int'),
			'voucher_before'    => array('double'),
            'voucher'       	=> array('double'),
            'voucher_after'     => array('double'),
            'voucher_id'     	=> array('int'),
			'admin_id'			=> array('int'),
			'adjustment_count'	=> array('int'),
			'remark'			=> array('text'),
			'latest'			=> array('int'),
            'created_at'    	=> array('datetime'),
        );
		
		public $privacySetting	= 1;
		
		public $adminFields	= array('adjustment.user_id', 'points_before', 'adjustment.points', 'points_after', 'points_id', 'voucher_before', 'adjustment.voucher', 'voucher_after', 'voucher_id', 'adjustment.admin_id', 'adjustment.created_at', 'admin.name'); 
		
    }

?>