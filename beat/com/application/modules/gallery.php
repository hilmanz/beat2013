<?php
class gallery extends App{

	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('acts', strip_tags($this->_g('act')));
		$this->assign('search', strip_tags($this->_p('search')));
		$this->assign('brand', strip_tags($this->_p('brand')));
		
		$this->assign('startdate', strip_tags($this->_p('startdate')));
		$this->assign('enddate', strip_tags($this->_p('enddate')));
		
		$social = $this->userHelper->getFriends(true,16);
		// pr($social); exit;
		
		if($social) if($social['result'])$this->assign('social',$social['data']);
		
	}
	
	function main(){
		
		$articlelist = $this->contentHelper->getArticleContent(null,10,4,array(0,3),"plan");
			$brands = strip_tags($this->_p('brand'));
		$arrBrand = array(3,4,5,6,100);
		$arrCocreation = array(1,2);
		
		if($articlelist){	
			$plandata['result'] = false;
			if ($articlelist['result']) {
				foreach($articlelist['result'] as $key => $val){
					if($brands)if(!in_array($brands,$arrCocreation)) continue; 
					
						$plandata['result'][] = $val;
					
				}
				$plandata['total'] = count($plandata['result']);
			}
		}
		$this->assign('plandata',$plandata);
		$this->log('surf','gallery');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-pages.html');
	}
	
	function plan(){
			
		$articlelist = $this->contentHelper->getArticleContent(null,10,4,array(0,3),"plan");
		
		$this->assign('plandata',$articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-plan-pages.html');
	}
	
	
	function detail(){
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		$this->assign('plandata',$articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-detail-pages.html');
	}
	
	function shows(){
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		$this->assign('plandata',$articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-show-pages.html');
	}
	
	function addphoto(){
		$this->assign('cid',$this->_request('cid'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/add-photo-pages.html');
	}
	
	function upload(){
		global $CONFIG;
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					if ($data['arrImage']!=NULL) {
						$result = $this->contentHelper->addUploadImageGallery($data);
						if($result) {
						
							$data = true;
						} else {
							$data = false;
						}
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;
			}
			
		
		$url = "gallery";
	
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . 'login_message.html');
		exit;
	}
	
}
?>