<?php
class analytic extends App{
	
	
	function beforeFilter(){
		
		$this->googleAnalyticHelper = $this->useHelper("googleAnalyticHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		
		$this->assign('locale', $LOCALE[1]);
	
		
	}
	function main(){
		
		$loginData = $this->googleAnalyticHelper->userActivity();
		$dummy = $this->googleAnalyticHelper->dataDummy();
		
		$this->assign("loginData",json_encode($loginData));
		$this->assign("dataDummy", $dummy);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/analytic.html');
		
	}
	
}
?>