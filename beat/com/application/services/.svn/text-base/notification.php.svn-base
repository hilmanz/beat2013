<?php
class notification  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->wallpaperHelper = $this->useHelper('wallpaperHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->apnsHelper  = $this->useHelper('apnsHelper');
		$this->newsHelper = $this->useHelper('newsHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	function feeds(){
		$data = $this->activityHelper->getA360activity(0,10,false,false,false,'3',true);	
		return $data;		
		
	}
	
	function push(){
		$data = $this->apnsHelper->pushnotification();	
		return $data;		
		
	}
}
?>