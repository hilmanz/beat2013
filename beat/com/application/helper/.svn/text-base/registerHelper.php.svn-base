<?php
class registerHelper {
	
	var $_mainLayout="";
	
	var $login = false;
	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		
		$this->config = $CONFIG;
		
	
	}
	
	
	function registerPhase($mobile=false){
		$ok = false;
		global $CONFIG;
		$mobilePath = '';
		if($mobile) $mobilePath = '/mobile';
		
		if($this->apps->_p('register')==1){
		
			$this->logger->log('can register');
			if($this->doRegister()){
				$this->apps->log('register','');
				$this->apps->assign("msg","register-in.. please wait");
				$this->apps->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
				sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
				return $this->apps->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				die();
			}
				$this->logger->log('failed to register');
			if(!$ok){
				$this->apps->log('access_denied','');
				$this->apps->assign("msg","failed to register..");
				$this->apps->assign("link","{$CONFIG['BASE_DOMAIN']}login");
				sendRedirect("{$CONFIG['BASE_DOMAIN']}login");
				return $this->apps->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				die();
			}
		}
		$this->logger->log('can not register');
		return false;
	}
	
	function doRegister(){
		global $CONFIG;
		$this->logger->log('do register');

		$name = $this->apps->_p('name');
		$username = $this->apps->_p('username');
		$password = trim($this->apps->_p('password'));
		$repassword = trim($this->apps->_p('repassword'));
		$nickname = $this->apps->_p('name');
		$email = trim($this->apps->_p('email'));
		
		if($name==''||$name==null){
			$this->logger->log('name is null');
			return false;
		}
		if($password!=$repassword) {
			$this->logger->log('pass and re pass not match');
			return false;
		}
			
		$hashPass = sha1($password.$CONFIG['salt']);
		$sql = "
			INSERT INTO social_member (password,email,register_date,salt,n_status,name,nickname,username) 
			VALUES ('{$hashPass}','{$email}',NOW(),'{$CONFIG['salt']}',1,'{$name}','{$nickname}','{$username}')
		";
		$this->apps->query($sql);
		$lastID = $this->apps->getLastInsertId();
		if($lastID>0) {
			$sql = "
				INSERT INTO my_pages (ownerid,name,description,type,img,otherid,city,created_date,closed_date, 	n_status) 
				VALUES ('{$lastID}','{$name}','',1,'',1,1,NOW(),DATE_ADD(NOW(),INTERVAL 5 YEAR),1)
			";
			// pr($sql);exit;
			 $this->apps->query($sql);
				if($this->apps->getLastInsertId()>0)  return true;
		}		
		
 		return false;
	
	}
	
	
	
	
}
