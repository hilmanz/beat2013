<?php
class inbox extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
	}
	
	function main(){
		$message = $this->messageHelper->getMessage();
		
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',$message['total']);
		$this->assign('message',$message['result']);
		$this->log('surf','inbox');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/inbox-pages.html');
	}
	
	function detail(){	
		$this->log('surf','detail inbox');
		$id = intval($this->_request('id'));
		$message =$this->messageHelper->readMessage();
		
		$this->assign('message',$message);
		$this->assign('parentid',$id);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/message-detail.html');
	}
	
	function create(){
		$social = $this->userHelper->getFriends(true,16);
		// pr($social); exit;
		if($social) if($social['result'])$this->assign('social',$social['data']);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-message.html');
	}
	
	function reply(){
		global $CONFIG;
		
		$data = $this->messageHelper->createMessage();	
		if($data) {
			$parentid = $data;
			$url = "inbox/detail/".$parentid;
		}else $url = "inbox";
		
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
		
	}
	
	function uninboxmessage(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		
		$data = $this->messageHelper->uninboxmessage();
		sendRedirect($CONFIG['BASE_DOMAIN']."inbox");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if($needs=="inbox") $data =  $this->messageHelper->getMessage($start,10);
		print json_encode($data);exit;
	}
}
?>