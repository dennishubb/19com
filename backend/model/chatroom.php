<?php

    class chatroom extends dbObject {
        
        protected $relations = Array(
            'user'      => Array("hasOne", "user", 'user_id'),
			'article'	=> Array('hasOne', 'article', 'article_id'),
			'message'	=> Array('hasMany', 'message', 'chatroom_id'),
        );

        protected $dbFields = array(
            'user_id'       => array('int'),
			'article_id'	=> array('int'),
            'title'         => array('text'),
            'status'        => array('text'),
			'type'			=> array('text'),
            'unread_count'  => array('int'),
            'created_at'    => array('datetime'),
            'updated_at'    => array('datetime'),
        );

    }

?>