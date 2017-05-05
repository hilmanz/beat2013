<?php
class baperformance extends App{

	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
	}
	
	function main(){
		$this->log('surf','baperformance');
		
		$social = $this->userHelper->getFriends(true,16);
		
		if($social) if($social['result'])$this->assign('social',$social['data']);
			$data = $this->userHelper->getUserProfile(); 
		$this->View->assign('userprofile',$data);
		
		$this->View->assign('acquisition',$this->setWidgets('acquisition'));
		$this->View->assign('age_relevancy',$this->setWidgets('age_relevancy'));
		$this->View->assign('brand_preference_relevancy',$this->setWidgets('brand_preference_relevancy'));
		$this->View->assign('gender',$this->setWidgets('gender'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/baperformance-pages.html');
	}
}
?>