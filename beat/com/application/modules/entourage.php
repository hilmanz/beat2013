<?php
class entourage extends App{

	
	function beforeFilter(){
	
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function main(){
		$social = $this->userHelper->getFriends(true,16);
		$this->log('surf','entourage');	
		if($social) if($social['result'])$this->assign('social',$social['data']);
		$this->View->assign('userprofile',$data);
		$entourage = $this->entourageHelper->getEntourage();
		$this->assign('entourage',$entourage['result']);
		$this->View->assign('entourage_list',$this->setWidgets('entourage_list'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-pages.html');

	}
	
	function profile(){		
			$userprofile =  $this->entourageHelper->entourageDetail();	
			$this->assign('userprofile',$userprofile);
			$this->View->assign('entourage_detail',$this->setWidgets('entourage_detail'));
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-profile.html');

	}
	
	function editContent(){
		global $CONFIG,$LOCALE;
		if ($this->_p('authorid')==$this->user->id || $this->_p('authorid')==$this->user->pageid) {
			$data = $this->contentHelper->setEditContent();			
			if ($data) {
				$data;
			} else {
				$data= false;
			}
		} else {
			$data= false;
		}
		print json_encode($data);exit;
	}
	
	
	function register(){
	
		if($this->_g('register')==1){
			$data = $this->entourageHelper->addEntourage();
			$success = false;
			if($data) $succes = true;			
			$this->assign('success',$succes);
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-register.html');
	}
	
	function search(){
		$data = $this->entourageHelper->getSearchEntourage();
		print json_encode($data);exit;
	}
}
?>