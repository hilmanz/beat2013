<?php
class plan extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user', $this->user);
		
		$social = $this->user->miniondetail;		
		if($social) {
			foreach($social as $key => $val){
				foreach($val as $keyval => $valval){
					$socialData[$key][$keyval] = $valval;
				}
			}
			$this->assign('social',$socialData);
		}
		
		$data = $this->userHelper->getUserProfile(); 		
		$this->View->assign('userprofile',$data);
		$articlelist = $this->contentHelper->getArticleContent(null,2,4,array(0,3),"plan");
		$this->View->assign('notification',$articlelist);
		// pr($this->user);exit;
		
		$this->executor=array(1,2);
		$this->planner=array(1,2,3,4,5);
		$this->approver=array(4);
		
		$this->assign('search',strip_tags($this->_p('search')));
		$this->assign('brand',strip_tags($this->_p('brand')));
		
		if(in_array($this->user->leaderdetail->type,$this->approver)) $this->assign('approver',true);
		if(in_array($this->user->leaderdetail->type,$this->planner)) $this->assign('planner',true);
		
	}
	
	function main(){
		global $CONFIG;
		//exit;
		
		$articlelist = $this->contentHelper->getArticleContent(null,10,4,array(0,3),"plan");
		$brands = strip_tags($this->_p('brand'));
		$arrBrand = array(3,4,5,6,100);
		$arrCocreation = array(1,2);
		
		if($articlelist){
			$colorpicker[0] = "#05A73E";
			$colorpicker[1] = "#05A73E";
			$colorpicker[2] = "#05A73E";
			$colorpicker[3] = "#05A73E"; 
			$colorpicker[4] = "#00ACEC";
			$colorpicker[5] = "#00ACEC";
			
			$colorpicker[1111] = "#FF0000";
			
			$data['plan']['total'] =intval($articlelist['total']);
			$plan = false;
			// pr($articlelist);
			if ($articlelist['result']) {
				foreach($articlelist['result'] as $key => $val){
					
					if($brands)if(!in_array($brands,$arrCocreation)) continue; 
					$plan[$key]['title'] =$val['title']; 
					$plan[$key]['start'] = $val['posted_date'];
					
					$plan[$key]['approver'] = false;
					$plan[$key]['planner'] = true;
					
					if($val['expired_date']!='0000-00-00') $plan[$key]['end'] = $val['expired_date'];
					else $plan[$key]['end'] = $val['posted_date'];
					
					$plan[$key]['color'] = $colorpicker[$val['author']['pagesdetail']['type']];
					
					if(!in_array($this->user->leaderdetail->type,$this->approver)) $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/edit/".$val['id'];
					else  {
						if($val['authorid']==$this->user->id) $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/edit/".$val['id'];
						else $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/approve/".$val['id'];
						
						$plan[$key]['approver'] = true;
					}
					
					if( $val['posted_date']< date("Y-m-d" ) ) {
						$plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/detail/".$val['id'];
						$plan[$key]['color'] = $colorpicker[1111];
					}
					
					if( $val['n_status'] == 1 ) {
						$plan[$key]['url']=$CONFIG['BASE_DOMAIN']."plan/detail/".$val['id'];
						
					}
				}
			}
			$data['plan']['posts'] = $plan;
		}else{
			$data['plan']['total'] = 0;
			$data['plan']['posts']= false;
		}
		// pr($articlelist);exit;
		// pr($articlelist);exit;
		$this->assign('plandata',$data);
	
		$this->log('surf','plan');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/plan-pages.html');
	}
	
	function create(){
		global $CONFIG;
		
		if(strip_tags($this->_p('upload'))=='simpan') {
			
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					
				}
			}
			$result = $this->contentHelper->addUploadImage($data);
				
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-plan.html');
	}
	
	function edit(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/edit-plan.html');
	}
	
	
	function invite(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/invite-plan.html');
	}
	
	
	function detail(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/detail-plan.html');
	}
	
	function approve(){
		// pr('masuk');
		if(!in_array($this->user->leaderdetail->type,$this->approver)) return false;
		
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/approve-plan.html');
	}
	
	function reason(){
	
		if(!in_array($this->user->leaderdetail->type,$this->approver)) return false;
		
		
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/reject-reason-plan.html');
	}
	
}
?>