<?php

class Session {
	
	protected $session_id;
	
	function __construct()	
	{
		if(!isset($_SESSION)){
			@session_start();
		}
		//make a unique cookie id for later database use
	}
	
	public function setMessage($name,$msg){
	
			$_SESSION['message'][$name]=$msg;
		
	}
	
	public function getMessage($name){
			
			if(isset($_SESSION['message'][$name])){
				return $_SESSION['message'][$name];
			}else{
				return array();
			}
	}
	
	public function setCookie(){
		
		if (isset($_COOKIE['SESSION'])){
			$session_id = $_COOKIE['SESSION'];
		}else{
			$session_id = md5(uniqid('biped',true));
		}
		
		setcookie('SESSION', $session_id , time()+(60*60*24*30));
		
		$this->session_id=$session_id;
		
	}
	
	public function getCookie(){
		return $this->session_id;
	}


}