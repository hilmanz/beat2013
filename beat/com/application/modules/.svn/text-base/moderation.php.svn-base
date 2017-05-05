<?php
class moderation extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
	}
	
	function main(){
		$articlelist = $this->contentHelper->getArticleContent(null,10,0,array(0,3),"timeline");
		
		$time['time'] = '%H:%M:%S';
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($articlelist['total']));
		$this->assign('timeline',$articlelist['result']);
		$this->assign('time',$time);
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	}
	
	function detail(){
		$cidStr = intval($this->_request('id'));		
		$article = $this->contentHelper->getDetailArticle();
		$comment = $this->contentHelper->getComment($cidStr,false,0,10);
		
		$this->assign('detail',$article['result']);
		$this->assign('comment',$comment[$cidStr]);
		$this->assign('cidStr',$cidStr);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/inbox-detail.html');
	}
	
	function addComment(){
		global $CONFIG;
		
		$cid = intval($this->_p('cid'));
		$data = $this->contentHelper->addComment();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/detail/{$cid}");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function unpublish(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('type',3);
		
		$data = $this->contentHelper->unContentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function commentList(){
		$commentlist = $this->contentHelper->getCommentModeration(null,10,3);
		
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($commentlist['total']));
		$this->assign('comment',$commentlist['result']);
		$this->assign('act',"commentList");
		$this->log('surf','moderation');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/moderation-pages.html');
	}
	
	function uncomment(){
		global $CONFIG;
		
		$this->Request->setParamPost('id',intval($this->_request('id')));		
		$data = $this->contentHelper->unCommentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."moderation/commentList");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if($needs=="post_moderation") $data =  $this->contentHelper->getArticleContent($start,10,0,array(0,3),"timeline");
		if($needs=="comment_moderation") $data =  $this->contentHelper->getCommentModeration($start,10,3);
		
		print json_encode($data);exit;
	}
}
?>