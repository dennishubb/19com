<?php

    class gift_redeem extends dbObject {
		
		protected $relations = Array(
            'gift' 		=> Array("hasOne", "gift", 'gift_id'),
			'user'		=> Array('hasOne', 'user', 'user_id'),
			'admin'		=> Array('hasOne', 'user', 'admin_id', 'admin'),
        );
        
        protected $dbFields = array(
            'gift_id'       => array('int'),
            'user_id'   	=> array('int'),
            'admin_id'      => array('int'),
            'remark'       	=> array('text'),
            'tracking_no'   => array('text'),
            'status'     	=> array('text'),
			'phone'			=> array('text'),
			'name'			=> array('text'),
			'address'		=> array('text'),
			'quantity'		=> array('int'),
			'size'			=> array('text'),
			'color'			=> array('text'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );
		
		public $privacySetting = 1;
		
		public $memberFields = array('gift_redeem.id', 'gift_redeem.gift_id', 'gift_redeem.user_id', 'gift_redeem.admin_id', 'gift_redeem.remark', 'gift_redeem.status', 'gift_redeem.size', 'gift_redeem.quantity', 'gift_redeem.color', 'gift_redeem.created_at');
		
		public $adminFields = array('gift_redeem.id', 'gift_redeem.gift_id', 'gift_redeem.user_id', 'gift_redeem.admin_id', 'gift_redeem.remark', 'gift_redeem.status', 'gift_redeem.size', 'gift_redeem.quantity', 'gift_redeem.color', 'gift_redeem.created_at', 'gift_redeem.phone', 'gift_redeem.name', 'gift_redeem.address', 'gift_redeem.tracking_no', 'admin.id');
    }

?>