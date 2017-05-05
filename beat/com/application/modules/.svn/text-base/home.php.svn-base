<?php
class home extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->activityHelper = $this->useHelper('activityHelper');

		$this->messageHelper = $this->useHelper('messageHelper');

		$this->userHelper = $this->useHelper('userHelper');
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);
		$this->assign('user', $this->user);
		$data = $this->userHelper->getUserProfile(); 	
		// pr($data);
		$this->View->assign('userprofile',$data);
	}
	
	function main(){
	
		$this->View->assign('my_profile_box',$this->setWidgets('my_profile_box'));
		$this->View->assign('lates_engagement_box',$this->setWidgets('lates_engagement_box'));
		$this->View->assign('inbox_box',$this->setWidgets('inbox_box'));
		$this->View->assign('line_chart',$this->setWidgets('line_chart'));
		$this->View->assign('medium_banner',$this->setWidgets('medium_banner'));		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/home.html');		
	}
	
	
	function profileDetail(){
	$this->View->assign('profile_detail',$this->setWidgets('profile_detail'));
	return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/profile-detail.html');
	}
	
	function entourageList(){
		$this->View->assign('entourage_list',$this->setWidgets('entourage_list'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-list.html');
	}
	
	function entourageDetail(){
		$this->View->assign('entourage_detail',$this->setWidgets('entourage_detail'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-detail.html');
	}
	
	function ajax(){
		if($this->_p('action')=="a360activity") {
			$maxrecord = 2;
			$start = intval($this->_p('start'));
			$data = $this->activityHelper->getA360activity($start,$maxrecord);
			print json_encode($data['content']); exit;
		}
	}	
}
?>