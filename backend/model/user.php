<?php

    class user extends dbObject {

        protected $relations = Array(
            'role' 			=> Array("hasOne", "role", 'role_id'),
			'level' 		=> Array('hasOne', 'level', 'level_id'),
			'upload'		=> Array('hasOne', 'upload', 'upload_id'),
        );
        
        protected $dbFields = array(
            'username'      	=> array('text', 'required'),
            'name'          	=> array('text'),
            'alias'         	=> array('text'),
            'phone'         	=> array('text'),
            'type'          	=> array('text'),
            'token'         	=> array('text'),
            'password'      	=> array('text', 'required'),
            'email'         	=> array('text'),
			'address'			=> array('text'),
			'birth_at'			=> array('datetime'),
			'gender'			=> array('text'),
			'weibo'				=> array('text'),
			'rank_id'			=> array('int'),
            'theme'         	=> array('text'),
            'role_id'       	=> array('int'),
			'upload_id'			=> array('int'),
			'level_id'			=> array('int'),
			'user_admin'		=> array('int'),
            'disabled'      	=> array('int'),
			'deleted'			=> array('int'),
			'system'			=> array('int'),
			'points'			=> array('double'),
			'voucher'			=> array('double'),
			'total_points'		=> array('double'),
			'total_voucher'		=> array('double'),
			'win_rate'			=> array('double'),
            'comment_count' 	=> array('int'),
            'article_count' 	=> array('int'),
			'gift_redeem_count' => array('int'),
            'created_at'    	=> array('datetime'),
            'updated_at'    	=> array('datetime'),
            'login_at'      	=> array('datetime'),
        );
		
		public $privacySetting	= 1;
		
		public $memberFields = array('user.id', 'user.name', 'user.username', 'user.phone', 'user.alias', 'user.email', 'user.address', 'user.birth_at', 'user.gender', 'user.weibo', 'user.points', 'user.voucher', 'user.total_points', 'user.total_voucher', 'user.win_rate', 'user.role_id', 'user.level_id', 'user.upload_id');
		
		public $adminFields = array('user.id', 'user.name', 'user.username', 'user.phone', 'user.email', 'user.address', 'user.alias', 'user.birth_at', 'user.gender', 'user.weibo', 'user.points', 'user.voucher', 'user.total_points', 'user.total_voucher', 'user.win_rate', 'user.role_id', 'user.level_id', 'user.upload_id', 'comment_count', 'article_count', 'gift_redeem_count', 'user.created_at', 'user.updated_at', 'login_at', 'user_admin', 'user.disabled');
		
		protected $joinFields = array('id', 'name', 'username', 'user_admin', 'total_points', 'total_voucher', 'gift_redeem_count', 'level_id', 'adjustment_count');

    }

?>