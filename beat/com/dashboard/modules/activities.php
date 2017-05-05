<?php
class activities extends App{
	
	
	function beforeFilter(){
	
		$this->webActivityHelper = $this->useHelper("webActivityHelper");
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['DASHBOARD_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_DASHBOARD']);
		
		$this->assign('locale', $LOCALE[1]);
	
	}
	
	function main(){
	
		$topUserActLike = $this->webActivityHelper->topUserActLike();
		$topUserActComment = $this->webActivityHelper->topUserActComment();
		$topUserActUpload = $this->webActivityHelper->topUserActUpload();
		$topUserActDownload = $this->webActivityHelper->topUserActDownload();
		
		$topViewMostTime = $this->webActivityHelper->topViewMostTime();
		$topTenSuperUser = $this->webActivityHelper->topTenSuperUser();
		$topTenUserWeekly = $this->webActivityHelper->topTenUserWeekly();
		
		$topTenVisitedPage = $this->webActivityHelper->topTenVisitedPage();
		$topThreeVisitMusic = $this->webActivityHelper->topThreeVisitMusic();
		$topThreeVisitDJ = $this->webActivityHelper->topThreeVisitDJ();
		$topThreeVisitVisualart = $this->webActivityHelper->topThreeVisitVisualart();
		$topThreeVisitFashion = $this->webActivityHelper->topThreeVisitFashion();
		$topThreeVisitPhotography = $this->webActivityHelper->topThreeVisitPhotography();
				
		/* -------------------------------------------------------------------- */
		
		$this->assign("topUserActLike",$topUserActLike);
		$this->assign("topUserActComment",$topUserActComment);
		$this->assign("topUserActUpload",$topUserActUpload);
		$this->assign("topUserActDownload",$topUserActDownload);
		
		$this->assign("topViewMostTime",$topViewMostTime);
		$this->assign("topTenSuperUser",$topTenSuperUser);
		$this->assign("topTenUserWeekly",$topTenUserWeekly);
		
		$this->assign("topTenVisitedPage",$topTenVisitedPage);
		$this->assign("topThreeVisitMusic",$topThreeVisitMusic);
		$this->assign("topThreeVisitDJ",$topThreeVisitDJ);
		$this->assign("topThreeVisitVisualart",$topThreeVisitVisualart);
		$this->assign("topThreeVisitFashion",$topThreeVisitFashion);
		$this->assign("topThreeVisitPhotography",$topThreeVisitPhotography);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_DASHBOARD .'apps/activities.html');
		
	}
	
}
?>