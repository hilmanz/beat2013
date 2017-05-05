<?php
class login extends App{
		
	function beforeFilter(){
		$this->loginHelper = $this->useHelper('loginHelper');
		
	}
	
	function main(){
		$this->local();
	}
	
	function local(){
		
		global $CONFIG,$logger;
		if($CONFIG['LOCAL_DEVELOPMENT']){
		}else{			
		sendRedirect("{$CONFIG['LOGIN_PAGE']}");
		exit;

		}
		
	
	
		$basedomain = $CONFIG['DASHBOARD_DOMAIN'];
		
		$this->assign('basedomain',$basedomain);
		
		
			$res = $this->loginHelper->loginSession();
			
			if($res){
				
					$this->log('login','welcome');
					$this->assign("msg","login-in.. please wait");
					$this->assign("link","{$CONFIG['DASHBOARD_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
					sendRedirect("{$CONFIG['DASHBOARD_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
					return $this->out(TEMPLATE_DOMAIN_DASHBOARD . '/login_message.html');
					die();
			}
	
		$this->assign("msg","failed to login..");
			// pr(TEMPLATE_DOMAIN_DASHBOARD .'login.html');
		print $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'login.html');
		exit;
	}
}
?>