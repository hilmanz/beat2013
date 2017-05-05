<?php
class home extends App{
	
	
	function beforeFilter(){
	
		$this->dataHelper = $this->useHelper("dataHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
				
		$this->assign('locale', $LOCALE[1]);
	
		
	}
	function main(){
		
		$alluser = $this->dataHelper->allUserRegistration();
		$loginData = $this->dataHelper->loginUser(0);
		$activeUser = $this->dataHelper->loginUser(7);
		$superUser = $this->dataHelper->loginUser(30);
		$userUnverified = $this->dataHelper->userUnverified();
		
		$this->assign("alluser",$alluser);
		$this->assign("loginData",$loginData);
		$this->assign("activeUser",$activeUser);
		$this->assign("superUser",$superUser);
		$this->assign("userUnverified",$userUnverified);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/user.html');
		
	}
		
}
?>