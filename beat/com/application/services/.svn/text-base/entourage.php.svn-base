<?php
class entourage extends ServiceAPI{

	
	function beforeFilter(){
	
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->deviceMopHelper = $this->useHelper('deviceMopHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function lists(){
	
		return $this->entourageHelper->getEntourage(null,0,5,true);

	}
	
	function profile(){		
			$userprofile =  $this->entourageHelper->entourageProfile();	
			return $userprofile;

	}
	
		
	function register(){
		GLOBAL $CONFIG;
		$success = false;
		if($this->_request('register')==1){		
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/";
			
			if (isset($_FILES['img'])&&$_FILES['img']['name']!=NULL) {
				if (isset($_FILES['img'])&&$_FILES['img']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['img'],$path);
						if ($data['arrImage']!=NULL) {
							$data = $this->entourageHelper->addEntourage($data['arrImage']['filename']);
				
							if($data) $success = true;		
							else $success = false;
						} else {
							$success = false;
						}
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
		}
		return $success;
	}
	
	function search(){
		$data = $this->entourageHelper->getSearchEntourage();
		return $data;
	}
	
	function checkemail(){
			$result = false;
			$data = $this->entourageHelper->checkentourage();	
			
			if($data) {
				if($data['result']) $result = $data;
				else{
					$data = $this->deviceMopHelper->searchProfileUser();
					if($data) if($data['result']) $result = $data;
				}
			}else{
					$data = $this->deviceMopHelper->searchProfileUser();
					if($data) if($data['result']) $result = $data;
				}
			return $result;
	}
	
	function checkgiid(){
			$result = false;
			$data = $this->entourageHelper->checkentourage();	
			if($data) {
				// pr($data);exit;
				if($data['result']) $result = $data;
				else {
					$data = $this->deviceMopHelper->AdminGetProfileonGiid();
					if($data) if($data['result']) $result = $data;
				}
			}else{
					$data = $this->deviceMopHelper->AdminGetProfileonGiid();
					if($data) if($data['result']) $result = $data;
			}
			return $result;
	}
}
?>