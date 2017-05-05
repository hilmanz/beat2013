<?php

class loginHelper {
	
	var $_mainLayout="";
	
	var $login = false;
	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		$this->deviceMopHelper = $this->apps->useHelper('deviceMopHelper');
		$this->config = $CONFIG;
		if( $this->apps->session->getSession($this->config['SESSION_NAME'],"WEB") ){
			
			$this->login = true;
		
		}
		$this->schema = "beat";
	}
	
	function checkLogin(){
		
		return $this->login;
	}
		
	/* below function used as local login only: on development phase without MOP */
	
	function loginSession($web=false){
		$ok = false;
		// pr($this->apps->_p('login'));
		if($this->apps->_p('login')==1){
			$result = $this->goLogin($web);
			return $result;
			
		}		
		return false;
	}
	
	
	
	function goLogin($web=false){
		global $logger, $APP_PATH;
		
		$mopfirst = false;/* jadiin true klo mop yang kemaren error uda jalan lagi */
		
		$username = trim($this->apps->_request('username'));
		$password = trim($this->apps->_request('password'));
		
		
		if($mopfirst){
			$mopdata = $this->deviceMopHelper->loginAdminMop();
			// pr($mopdata);exit;
			if($mopdata){
				if($mopdata->Result!=1){
						return false;
				}
			}else return false;
			
		}
		
		
		
		if($mopfirst) $sql = "SELECT * FROM social_member WHERE deviceid='{$username}' LIMIT 1";
		else {
			// if($web) $sql = "SELECT sm.*,pages.otherid FROM social_member sm LEFT JOIN my_pages pages ON pages.ownerid=sm.id WHERE username='{$username}' AND pages.type > 1 LIMIT 1";
			// else $sql = "SELECT sm.*,pages.otherid FROM social_member sm LEFT JOIN my_pages pages ON pages.ownerid=sm.id WHERE username='{$username}'  LIMIT 1";
			$sql = "SELECT sm.*,pages.otherid FROM social_member sm LEFT JOIN my_pages pages ON pages.ownerid=sm.id WHERE username='{$username}'  LIMIT 1";
		}
		$rs = $this->apps->fetch($sql);
		$logger->log($sql);
		// pr($sql);exit;
		//check password and phonumber , each field must be same of input value (higher security)
 		if($mopfirst){
				$this->setdatasessionuser($rs);
				$logger->log('can login');
				$this->login = true;
				return true;
		}else{
			
			$hash = sha1($password.$rs['salt']);
			if($rs['n_status']!=1) {
				$result['result'] = false;
				$result['message'] = " you are not verified ";
				return $result;
			}
			if($rs['password']==$hash){
			
				$this->setdatasessionuser($rs);
				
				$logger->log('can login');
				$this->login = true;
				$result['result'] = true;
				$result['message'] = " welcome ";
				return $result;
			}else {
			
				$this->add_try_login($rs);
				$logger->log("cannot login, password or username not exists ");
				$result['result'] = false;
				$result['message'] = " password or username not exists ";
				return $result;
			}
		}
	
	}
	
	function moploginsession(){
				$rs = @$this->apps->session->getSession($this->config['SESSION_NAME'],"WEB");
				if(!$rs) return false;
				$res = false;
				foreach($rs	as $key => $val){					
					$res[$key] = $val;				
				}
				// pr($res);exit;
				if(!$res) return false;
				$mopdata = $this->deviceMopHelper->loginAdminMop($rs);
				// pr($mopdata);exit;
				if($mopdata){
					if($mopdata->Result!=1){
							return false;
					}
				}else return false;
		
			return true;
	}
	
	/*
	lagi masang permission session
	
	*/
	
	function setdatasessionuser($rs=false){
		if(!$rs) return false;
		$rs['pageid'] = false;
		$rs['ownerid'] = false;
		$this->logger->log('can login');
		$id = intval($rs['id']);
		$leaderid = intval($rs['otherid']);
		if($rs['login_count']!=0)$this->add_stat_login($id);
		$pagestat = $this->getPagesStat($id,$leaderid);
		$this->reset_try_login($rs);
		if($pagestat)	{
			// $permissionPage = $this->getUserPagePermission($pagestat);			
			
			$rs = array_merge($rs,$pagestat);
		}
			
		// pr($rs);exit;
		$this->apps->session->setSession($this->config['SESSION_NAME'],"WEB",$rs);
	
	}
	
	function add_try_login($rs=false){
		
		if($rs==false) return false;	
	
		$sql ="UPDATE social_member SET last_login=now(),try_to_login=try_to_login+1 WHERE id='{$rs['id']}' LIMIT 1";
		$res = $this->apps->query($sql);
		
		$sql = "SELECT try_to_login FROM social_member WHERE id='{$rs['id']}' LIMIT 1";
		$res = $this->apps->fetch($sql);
		
		if($res){
			if($res['try_to_login']>5) {
				$sql ="UPDATE social_member SET n_status=9 WHERE id='{$rs['id']}' LIMIT 1";
				$res = $this->apps->query($sql);
			}
		}
	}
	
	function reset_try_login($rs=false){
		
		if($rs==false) return false;	
	
		$sql ="UPDATE social_member SET last_login=now(),try_to_login=0 WHERE id='{$rs['id']}' LIMIT 1";
		$res = $this->apps->query($sql);
				
	}
	
	function add_stat_login($user_id){
	
	
		// $sql ="UPDATE social_member SET last_login=now(),login_count=0 WHERE id={$user_id} LIMIT 1";
		$sql ="UPDATE social_member SET last_login=now(),login_count=login_count+1 WHERE id={$user_id} LIMIT 1";
		$rs = $this->apps->query($sql);

	
	}
	
	function getProfile(){
	
		$user = json_decode(urldecode64($this->apps->session->getSession($this->config['SESSION_NAME'],"WEB")));
		
		return $user;
	
	}
	
	function getPagesStat($user_id=null,$leader_id=null){
		
		if($user_id==null) return false;
		if($leader_id==null) return false;
		
		$pagedata['leaderdetail']  = false;
		$sql = "
		SELECT pages.name, pages.id ,pages.type ,pages.img,pages.ownerid ,pagetype.name pagetypename
		FROM my_pages pages
		LEFT JOIN my_pages_type pagetype ON pagetype.id=pages.type
		WHERE ownerid IN ({$user_id}) ";
	
		$data = $this->apps->fetch($sql,1);
		if($data) {
			foreach($data as $key => $val){
				$pagedata['leaderdetail'] = $val;
							
			}
			
			$miniondata = $this->getminion($user_id);
			$pagedata['miniondetail'] = $miniondata;	
			
			$masterdetail = $this->getmaster($leader_id);			
			$pagedata['masterdetail'] = $masterdetail;	
			
		}
						
		return $pagedata;
	}
	
	
	function getminion($strid=false){
	
		$miniondata = false;
		if($strid){
			$sql ="
					SELECT pages.name, pages.id ,pages.type ,pages.img,pages.ownerid ,pagetype.name pagetypename
					FROM my_pages pages
					LEFT JOIN my_pages_type pagetype ON pagetype.id=pages.type
					WHERE otherid IN ({$strid}) ";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if($qData){
				foreach($qData as $key => $val){
					$miniondata[$val['id']] =  $val;
				}
			
			}
		}
		return $miniondata;
		
	}
	
	function getmaster($strid=false){
	
		$masterdata = false;
		if($strid){
			$sql ="
					SELECT pages.name, pages.id ,pages.type ,pages.img,pages.ownerid ,pagetype.name pagetypename
					FROM my_pages pages
					LEFT JOIN my_pages_type pagetype ON pagetype.id=pages.type
					WHERE otherid IN ({$strid}) ";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if($qData){
				foreach($qData as $key => $val){
					$masterdata[$val['id']] =  $val;
				}
			
			}
		}
		return $masterdata;
		
	}
	
	function getUserPagePermission($pagetypeid=null){
			if($pagetypeid==null) return false;
			
			$sql = "SELECT * FROM {$this->schema}_news_content_permission_type WHERE n_status=1 AND pagetypeid ={$pagetypeid} LIMIT 1";
			$qData = $this->apps->fetch($sql);
			if(!$qData) return false;
				
			return $qData;
	}
	
}
