<?php

    class message extends dbObject {
        
        protected $relations = Array(
            'user'      => Array("hasOne", "user", 'user_id'),
            //'upload'    => Array("hasOne", "upload", 'attachment_upload_id'),
			//'chatroom'	=> Array("hasOne", "chatroom", 'chatroom_id'),
        );

        protected $dbFields = array(
            'user_id'               => array('int', 'required'),
			'chatroom_id'  			=> array('int', 'required'),
			'article_id'			=> array('int'),
            'message'               => array('text'),
            'parent_id'             => array('int'),
            'attachment_upload_id'  => array('int'),
			'status'				=> array('text'),
			'type'					=> array('text'),
            'created_at'            => array('datetime'),
			'updated_at'			=> array('datetime'),
            'read'                  => array('int'),
        );
		
		public $memberFields = array('message.id', 'message.user_id', 'message.chatroom_id', 'message', 'message.parent_id', 'message.status', 'message.created_at');
		
		public $adminFields = array('message.id', 'message.user_id', 'message.chatroom_id', 'message', 'message.parent_id', 'message.status', 'message.created_at', 'message.type', 'article.title', 'article.id');
		
		public $joinFields = array('id', 'message');

    }

?>