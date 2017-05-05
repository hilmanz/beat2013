<?php
class push extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function main(){
		return false;
	}
	
	function comment(){
		/*
		if($cid==null) $cid = intval($this->apps->_p('cid'));
		if($comment==null) $comment = $this->apps->_p('comment');
		*/
		$data = $this->contentHelper->addComment();
		
		return $data;
	}

		
	function emoticon(){
	/*
		$cid = intval($this->apps->_p("cid"));
		$emoid = intval($this->apps->_p("emoid"));
	*/
		$data = $this->contentHelper->addFavorite();
		return $data;
	}
	
	function checkin(){
		/*
				$venueid = intval($this->apps->_p('venueid'));
				$contentid = $this->apps->_p('contentid');
				$venue = $this->apps->_p('venue');
				$venuerefid = $this->apps->_p('venuerefid');
				$coor = $this->apps->_p('coor');
				$mypagestype = $this->apps->_p('mypagestype');
				$friendtags = $this->apps->_p('fid');
				$friendtypetags = $this->apps->_p('ftype');
		*/
		$data = $this->checkinHelper->checkin();
		return $data;
	}
	
	function friendTags(){
	/*
		$cid = intval($this->apps->_p("cid"));
		$fid = intval($this->apps->_p("fid"));
		$ftype = intval($this->apps->_p("ftype"));
	*/
		$data = $this->contentHelper->addFriendTags();
		return $data;
	}
	
	function friendUnTags(){
	/*
		$cid = intval($this->apps->_p("cid"));
		$fid = intval($this->apps->_p("fid"));
		$ftype = intval($this->apps->_p("ftype"));
	*/
		$data = $this->contentHelper->unFriendTags();
		return $data;
	}
	
	
	function friendTagsSearch(){
	/*
		$keywords = intval($this->apps->_p("keywords"));
	
	*/
		$data = $this->contentHelper->friendTagsSearch();
		return $data;;
	}
}
?>