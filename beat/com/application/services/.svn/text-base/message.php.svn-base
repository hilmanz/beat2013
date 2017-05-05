<?php
class message extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		
	
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function lists(){
		$data = $this->messageHelper->getMessage();
		return $data;
	}
	
	function create(){
		/*
		if($cid==null) $cid = intval($this->apps->_p('cid'));
		if($comment==null) $comment = $this->apps->_p('comment');
		*/
		$res = false;
		$data =$this->messageHelper->createMessage();	
		if($data) {
			$res['result'] = true;
			$res['parentid'] = $data;
		}else {
			$res['result'] = false;
			$res['parentid'] = 0;
		}
		return $res;
	}
	
	function readmessage(){
	
		$data = $this->messageHelper->readMessage();
		return $data;
		
	}
	
	function unlists(){
		$data = $this->messageHelper->uninboxmessage();
		return $data;
	}
	
}
?>