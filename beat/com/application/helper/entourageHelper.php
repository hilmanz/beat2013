<?php 

class entourageHelper {

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
			
			
		}
		
		$this->config = $CONFIG;
		$this->dbshema = "beat";	
	}


	
	function getEntourage($streid=null,$start=0,$limit=5,$all=false){
		global $CONFIG;
		
		if(intval($this->apps->_request('start'))!=0) $start = intval($this->apps->_request('start'));
		if($streid){			
			$qEntourage = " AND id IN ({$streid}) ";
			$limit = 50;
		}else{
			$qEntourage = "";
		}
		if($all){
			$qLimit = "";
		}else $qLimit = " LIMIT {$start},{$limit} ";
		
		$filter = strip_tags($this->apps->_p('filter'));
		$totalengagement = intval($this->apps->_p('totalengagement'));
		
		$qFilter ="";
		if($filter=="pending") $qFilter = " AND n_status = 0 ";
		if($filter=="accept") $qFilter = " AND n_status = 1 ";
		if($filter=="reject") $qFilter = " AND n_status = 2 ";
		if($filter=="engagement") 	{
			if($totalengagement==0) $qFilter = " AND stat.total<>{$totalengagement} ";
			else  $qFilter = " AND stat.total ={$totalengagement} ";
		}

		
		$data['result'] = false;
		$data['total'] = 0;
		
		$sql = "	
		SELECT COUNT(*) total FROM my_entourage entou 
		LEFT JOIN (SELECT count(*) total,friendid
		FROM {$this->dbshema}_news_content_tags
		WHERE userid={$this->uid} AND n_status=1 AND friendtype = 0 GROUP BY friendid ) stat ON stat.friendid= entou.id
		WHERE referrerbybrand={$this->uid} {$qEntourage} {$qFilter} ";	
		$total = $this->apps->fetch($sql);		
		// pr($total);
		if(!$total)return false;
		$sql = "
		SELECT entou.*, IF(stat.total IS NULL,0,stat.total) total FROM my_entourage entou 
		LEFT JOIN (SELECT count(*) total,id,friendid
		FROM {$this->dbshema}_news_content_tags
		WHERE userid={$this->uid} AND n_status=1 AND friendtype = 0 GROUP BY friendid ) stat ON stat.friendid= entou.id 
		WHERE referrerbybrand={$this->uid} {$qEntourage} {$qFilter} ORDER BY register_date DESC  {$qLimit} ";		
		
		$qData = $this->apps->fetch($sql,1);
		// pr($list);

		
		if($qData) {
		
			$arrentourage = false;
			$strentourage = false;
			$entouragedata = false;
			foreach($qData as $val){
				$arrentourage[$val['id']] = $val['id'];
			}
			if($arrentourage){
				$strentourage = implode(',',$arrentourage);
				$entouragedata = $this->entouragestatistic($strentourage);
			}
			
			
			
			foreach($qData as $key => $val){
			
					if(is_file($CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'])) {
						$qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'];
					}else  $qData[$key]['image_full_path'] =  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/small_".$val['img'];
					
					if($entouragedata){
						if(array_key_exists($val['id'],$entouragedata))  $qData[$key]['entouragedata']= $entouragedata[$val['id']];
						else  $qData[$key]['entouragedata']= false;
					}else  $qData[$key]['entouragedata']= false;
			}
			$data['result'] = $qData;
			
			$data['total'] = $total['total'];
		}
		// pr($data);exit;
		
		return $data;
		// return $list;
		
		
	}
	
	function entourageDetail(){
		global $CONFIG;
		$id=$this->apps->_g('id');
		
		$sql = "SELECT * FROM my_entourage WHERE id={$id} LIMIT 1 ";		
		$qData = $this->apps->fetch($sql);
		
		// pr($qData);
		
		if($qData)	{
			if(is_file( $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
		}
		// pr($qData);
		
		if($qData) 	return $qData;
		return false;
		
		
	}
	
	function entourageProfile(){
		global $CONFIG;
		$id=$this->apps->_request('id');
		$uid = intval($this->uid);
		
		$sql = "SELECT * FROM my_entourage WHERE referrerbybrand={$this->uid} AND id={$id} LIMIT 1 ";		
		$qData = $this->apps->fetch($sql);
		
		// pr($qData);
		
		if($qData)	{
			if(is_file( $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
		}
		pr($qData);
		
		if($qData) 	return $qData;
		return false;
		
		
	}
	
	function addEntourage($img=false){
		
		$firstname=$this->apps->_request("name");
		$lastname=$this->apps->_request("lastname");
		$nickname=$this->apps->_request("nickname");
		$email=$this->apps->_request("email");
	
		$city=intval($this->apps->_request("city"));
		$state=$this->apps->_request("state");
		$giidnumber=$this->apps->_request("giidnumber");
		$giidtype=$this->apps->_request("giidtype");
		$companymobile=$this->apps->_request("companymobile");
		$sex=$this->apps->_request("sex");
		$birthday=$this->apps->_request("birthday");
		$description=$this->apps->_request("description");
		$StreetName=$this->apps->_request("StreetName");
		$phone_number=$this->apps->_request("phone_number");		
		$brand1=$this->apps->_request("Brand1_ID");
		$brandsub1=$this->apps->_request("Brand1SUB_ID");
		$socialaccount=$this->apps->_request("socialaccount");
		$socialaccount_sub=$this->apps->_request("socialaccount_sub");
		

		$usertype=intval(@$this->apps->session->getSession($this->config['SESSION_NAME'],'USERTYPE')->users);
		
		$referrerbybrand = $this->uid; /* use on segment 8  */
		
			
		$confirm18=1;
		$receiveinfo=1;
		$n_status=1;
		$verified = 1;
		// pr($img);exit;
		$sql ="
		INSERT INTO my_entourage 
		(registerid ,name ,	nickname ,	email ,	register_date ,	img ,	small_img ,	city 	,sex ,	birthday ,	description, 	last_name ,	StreetName, 	phone_number ,	n_status ,	Brand1_ID ,	referrerbybrand ,verified, usertype,giidnumber,giidtype,socialaccount,socialaccount_sub) 
		VALUES
		('',\"{$firstname}\",\"{$nickname}\",\"{$email}\",NOW(),\"{$img}\",\"{$img}\",\"{$city}\",\"{$sex}\",\"{$birthday}\",\"{$description}\",\"{$lastname}\",\"{$StreetName}\",\"{$phone_number}\",{$n_status},\"{$brand1}\",{$referrerbybrand},{$verified},{$usertype},\"{$giidnumber}\",\"{$giidtype}\",\"{$socialaccount}\",\"{$socialaccount_sub}\")	
		ON DUPLICATE KEY UPDATE phone_number=\"{$phone_number}\",name=\"{$firstname}\",img=\"{$img}\",small_img=\"{$img}\",Brand1_ID=\"{$brand1}\",socialaccount=\"{$socialaccount}\"
		";
		
		// pr($sql);exit;
		$qData = $this->apps->query($sql);
		$data['result'] = false;
		$entourageid = false;
		if($this->apps->getLastInsertId())	$entourageid = $this->apps->getLastInsertId();
		else{
			$sql =" SELECT * FROM my_entourage WHERE email=\"{$email}\" LIMIT 1";
			$entorourage = $this->apps->fetch($sql);
			
			if($entorourage) $entourageid = $entourage['id'];
			else return false;
		}
				
		$data['result'] = true;
		$data['savedb'] = true;
		$data['savefriends'] = false;
		$data['savemop'] = false;
		
	
			$sql = "
			INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
			VALUES ('{$entourageid}',{$this->uid},0,0,NOW(),1)
			ON DUPLICATE KEY UPDATE n_status=1
			";
		
			$this->apps->query($sql);
			
			if($this->apps->getLastInsertId()) 	$data['savefriends'] = true;
			
		
			$mop = $this->apps->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication");
			// pr($mop);
			if($mop['result']==1) {
				$sql = "UPDATE my_entourage SET registerid='{$mop['data'][0]['RegistrationID']}' WHERE id={$entourageid} LIMIT 1 ";
				$qData = $this->apps->query($sql);
				if($qData) $data['savemop'] = true;
			
			}
			// pr($data);
			return $data;	
		
				
	}
	
	
	function getSearchEntourage(){
		$limit = 16;
		$start= intval($this->apps->_request('start'));
		$searchKeyOn = array("name","email","last_name");
		$keywords = strip_tags($this->apps->_request('keywords'));	
		$keywords = rtrim($keywords);
		$keywords = ltrim($keywords);
		
		$realkeywords = $keywords;
		$keywords = '';
		
		if(strpos($keywords,' ')) $parseKeywords = explode(' ', $keywords);
		else $parseKeywords = false;
		
		if(is_array($parseKeywords)) $keywords = $keywords.'|'.trim(implode('|',$parseKeywords));
		else  $keywords = trim($keywords);
		
		if(!$realkeywords){
			if($keywords!=''){
				foreach($searchKeyOn as $key => $val){
					$searchKeyOn[$key] = " {$val} REGEXP '{$keywords}' ";
				}
				$strSearchKeyOn = implode(" OR ",$searchKeyOn);
				$qKeywords = " 	AND  ( {$strSearchKeyOn} )";
			}else $qKeywords = " ";
		}else{
			foreach($searchKeyOn as $key => $val){
				$searchKeyOn[$key] = " {$val} like '{$realkeywords}%' ";
				if($val=="email") $searchKeyOn[$key] = " {$val} = '{$realkeywords}' ";
				if($val=="last_name") $searchKeyOn[$key] = " {$val} like '%{$realkeywords}%' ";
				
			}
			$strSearchKeyOn = implode(" OR ",$searchKeyOn);
			$qKeywords = " 	AND  ( {$strSearchKeyOn} )";
		}
		$sql = "SELECT count(*) total FROM my_entourage WHERE n_status =1  {$qKeywords} ORDER BY name ASC ";
		$total = $this->apps->fetch($sql);
		if(!$total) return false;
		
		$sql = "SELECT id,name,img,email,IF(last_name IS NULL,'',last_name) last_name , referrerbybrand FROM my_entourage WHERE n_status =1  {$qKeywords} ORDER BY name ASC, last_name ASC LIMIT {$start},{$limit}";
	
		$qData = $this->apps->fetch($sql,1);
	
		if(!$qData) return false;
		foreach($qData as $key => $val){
			$arrFriends[$val['id']] = $val['id']; 
			if($val['referrerbybrand']==$this->uid) $qData[$key]['isFriends'] = true;
			else $qData[$key]['isFriends'] =false;
		}
		
		if($qData){
			$data['result'] = $qData;
			$data['total'] = $total['total'];
			$data['myid'] = intval($this->uid);
		}
		return $data;
		
	}
	
	
	function entouragestatistic($strentourage=null){
			if($strentourage==null) return false;
			global $CONFIG;
			//check ba checkin with plan --> it engagement
			$sql = " SELECT  * FROM my_checkin WHERE userid={$this->uid} AND contentid <> 0 AND n_status=1 ";
		
			$qData = $this->apps->fetch($sql,1);
			
			if(!$qData) return false;
			$arrcid = false;
			$strcid = false;
			foreach($qData as $val){
				$arrcid[$val['contentid']] = $val['contentid'];
			}
			if($arrcid){
				$strcid = implode(',',$arrcid);
			}
			//get contentid detail
			$sql="SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid ,articleType,can_save
			FROM {$this->dbshema}_news_content anc
			WHERE id IN ({$strcid}) ";
			
			$qData = $this->apps->fetch($sql,1);
			foreach($qData as $key => $val){
				$qData[$key]['imagepath'] = false;
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";					
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$qData[$key]['banner'] = false;
				else $qData[$key]['banner'] = true;
								
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image']}")) $qData[$key]['image'] = "small_{$val['image']}";
				
				//PARSEURL FOR VIDEO THUMB
				$video_thumbnail = false;
				if($val['url']!='')	{
					//PARSER URL AND GET PARAM DATA
					$parseUrl = parse_url($val['url']);
					if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
					else $parseQuery = false;
					if($parseQuery) {
						if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
					} 
					$qData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $qData[$key]['video_thumbnail'] = false;
				
				if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				$contentdata[$val['id']] =  $qData[$key];
			}
			
			//get enggement of entourage
			$sql ="SELECT *
			FROM {$this->dbshema}_news_content_tags
			WHERE friendid IN ({$strentourage}) AND userid={$this->uid} AND contentid IN ({$strcid}) AND n_status=1 AND friendtype = 0
	
			";	
			$qData = $this->apps->fetch($sql,1);
			
			if(!$qData) return false;
			$arrfid = false;
			$strcid = false;
			foreach($qData as $key => $val){
				$arrfid[$val['friendid']][$key] = $val;
				if(array_key_exists($val['contentid'],$contentdata)) $arrfid[$val['friendid']][$key]['contentdetail'] = $contentdata[$val['contentid']];
				else  $arrfid[$val['friendid']][$key]['contentdetail']  = false;
			}
			if($arrfid) return $arrfid;
			
			return false;
	
			
		// i need check how many entourage of this BA
		// check how many times the entourage has engagement
	}
	
	function checkentourage(){
		global $CONFIG;
		$email= $this->apps->_request('email');
		$giid= $this->apps->_request('giidnumber');
		$filter = false;
		
		if($email) $filter[] = " email =\"{$email}\" ";
		if($giid) $filter[] = " giidnumber = \"{$giid}\" ";
		
		if($filter) $qFilter =	implode(" AND ",$filter);
		else $qFilter="";
		
		if($qFilter=="") return false;
		
		$sql = "SELECT * FROM my_entourage WHERE {$qFilter} LIMIT 1 ";		
				// pr($sql);
		$qData = $this->apps->fetch($sql);
		if($qData)	{
			if(is_file( $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
		}
		// pr($qData);
		
		if($qData) 	return array('result'=>true,'data'=>$qData);
		return array('result'=>false,'data'=>false);
	}
	
}

?>

