<?php 

class checkinHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbschema = "beat";	
		$this->radius = 100 / 10000;
		$this->dbshema = "beat";
	}


	function searchvenue(){
			
			// search vennue
			$limit = 10;
			$start= intval($this->apps->_request('start'));
			
			$coor= strip_tags($this->apps->_request('coor'));
			/* testing */
			
			$searchKeyOn = array("latitude","longitude");
			$keywords = strip_tags($this->apps->_p('keywords'));	
			$keywords = rtrim($keywords);
			$keywords = ltrim($keywords);
			if($coor=='') return false;
			$realkeywords = $keywords;
			$newcoor =false;
			$lon = 0;
			$lonmax = 0;
			$lat = 0;
			$latmax = 0;
			
			
				/* radius calc */
				$arrcoor = explode(',',$coor);
				if(is_array($arrcoor)){
					$lat = $arrcoor[0] + $this->radius;
					$latmax = $arrcoor[0] - $this->radius;
					$lon = $arrcoor[1] - $this->radius;
					$lonmax = $arrcoor[1] + $this->radius;
			
				}
				
				/*
				
					SELECT SUBSTR(coor,1, LOCATE(',',coor)-1) lon, SUBSTR(coor,LOCATE(',',coor)+1) lat FROM `beat_city_reference` WHERE 1
				*/
				foreach($searchKeyOn as $key => $val){
					$searchKeyOn[$key] = " {$val} like '{$realkeywords}%' ";
					if($val=="city") $searchKeyOn[$key] = " {$val} like '%{$realkeywords}%' ";
					
					if($val=="latitude"&&$lat!=0&&$latmax!=0) {
						$searchKeyOn[$key] = " 	{$val} >= '{$lat}' AND {$val} <= '{$latmax}' ";					
					}
					
					if($val=="longitude"&&$lon!=0&&$lonmax!=0) {
						$searchKeyOn[$key] = " {$val} >= '{$lon}' AND {$val} <= '{$lonmax}' ";					
										
					}
				}
		
			
			$strSearchKeyOn = implode(" AND ",$searchKeyOn);
			$qKeywords = " 	AND  ( {$strSearchKeyOn} )";
	
			$sql = "SELECT * FROM {$this->dbschema}_venue_master WHERE 1 {$qKeywords} ORDER BY venuename LIMIT {$start},{$limit}";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if($qData){
				return $qData;
			}			
			
			return false;
			
			
	}
	
	function checkin(){
		global $CONFIG;
		$venueid = intval($this->apps->_p('venueid'));
		$contentid = intval($this->apps->_p('cid'));
		$venue = $this->apps->_p('venue');
		$venuerefid = intval($this->apps->_p('venuerefid'));
		$coor = $this->apps->_p('coor');
		$mypagestype = $this->apps->_p('mypagestype');
		$friendtags = $this->apps->_p('fid');
		$friendtypetags = $this->apps->_p('ftype');
		$rating = intval($this->apps->_p('rating'));
		$prize = intval($this->apps->_p('prize'));
		$wifi = intval($this->apps->_p('wifi'));
		$smoking = intval($this->apps->_p('smoking'));
		$image = '';
		$type = 3;
		$fromwho = 1;
		if(!$this->uid) return false;
		$authorid = intval($this->uid);
		
		if($mypagestype==0) $mypagestype = 1;
		/* radius calc */
			$arrcoor = explode(',',$coor);
			if(is_array($arrcoor)){
				$lat = $arrcoor[0];				
				$lon = $arrcoor[1];
			
		
			}
		
		if(!$contentid){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->apps->uploadHelper->uploadThisImage($_FILES['image'],$path);
				
						if ($data['arrImage']!=NULL) {
								$image = $data['arrImage']['filename'];
						}
				}
			} 	
		
		
			$posted_date = date('Y-m-d H:i:s');		
			
			$sql ="
				INSERT INTO {$this->dbshema}_news_content (cityid,brief,title,content,tags,image,articleType,created_date,posted_date,authorid,fromwho,n_status,url) 
				VALUES ('{$venueid}','{$venue}','{$venue}','','','{$image}',{$type},NOW(),'{$posted_date}','{$authorid}','{$fromwho}',1,'')
				";
				
				// pr($sql);
		
			$this->apps->query($sql);
			if($this->apps->getLastInsertId())  $contentid = $this->apps->getLastInsertId();
		}
		
		 // Full texts 	id 	contentid 	userid	venue 	venueid 	venuerefid 	coor	mypagestype join_date 	n_status
		$sql = " INSERT INTO my_checkin(contentid ,	userid	,venue ,	venueid 	,venuerefid 	,latitude,longitude	,mypagestype ,rating ,prize ,wifi ,smoking ,join_date ,	n_status) VALUES 
		({$contentid},{$this->uid},\"{$venue}\",\"{$venueid}\",\"{$venuerefid}\",\"{$lat}\",\"{$lon}\",\"{$mypagestype}\",{$rating},{$prize},{$wifi},{$smoking},NOW(),1)
		";
		// pr($sql);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) {
			if($friendtags){
				$cid = $this->apps->getLastInsertId();
				$arrfid = explode(',',$friendtags);
				$arrftype = explode(',',$friendtypetags);
				$frienddata = false;
				if(is_array($arrfid)){
					foreach($arrfid as $key => $val){
						$frienddata[$key]['fid'] = $val;
						$frienddata[$key]['ftype'] = $arrftype[$key];
						if(array_key_exists($key,$arrftype)) $frienddata[$key]['ftype'] = $arrftype[$key];
					}
					
					if($frienddata){
				
						foreach($frienddata as $val){
							$this->apps->contentHelper->addFriendTags($cid,$val['fid'],$val['ftype']);
						}
					
					}
				}else{
					$this->apps->contentHelper->addFriendTags($cid,$friendtags,$friendtypetags);
				}
			}
			return true;
		}
		else return false;
	}
	
	function addvenue(){
		$venueid = intval($this->apps->_p('venueid'));
		$keywords = $this->apps->_p('keywords');	
		$coor = $this->apps->_p('coor');
		
	
				/* radius calc */
			$arrcoor = explode(',',$coor);
			if(is_array($arrcoor)){
				$lat = $arrcoor[0];				
				$lon = $arrcoor[1];	
			}
		
		$sql ="INSERT INTO {$this->dbschema}_venue_master 
		( provinceName ,	city ,	venuename ,	latitude ,	longitude ,	venuecategory ,	n_status )
		VALUES(\"{$keywords}\",\"{$keywords}\",\"{$keywords}\",\"{$lat}\",\"{$lon}\",0,1)
		";
			// pr($sql);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) {
			$venueid = $this->apps->getLastInsertId();
			$sql ="INSERT INTO {$this->dbschema}_venue_reference
			( venueid,	keywords ,latitude,longitude	, 	datetime ,	n_status )
			VALUES({$venueid},\"{$keywords}\",\"{$lat}\",\"{$lon}\",NOW(),1)
			";
			
			$this->apps->query($sql);
			if($this->apps->getLastInsertId()) 	{
					$data['result'] = true;
					$data['venueid'] = $venueid;
					$data['venuename'] = $keywords;
					$data['venuerefid'] = $this->apps->getLastInsertId();
					$data['coor'] = $coor;
					return $data;
			}
			else {
				$data['result'] = false;
				return $data;
			}
		}
		else return false;
		
		
	}
	
		
	function uncheckin(){
		$cid = intval($this->apps->_p('cid'));
		
		$sql = " UPDATE my_checkin SET n_status = 0 WHERE userid={$this->uid} AND id={$cid} LIMIT 1";
		// pr($sql);
		$qData = $this->apps->query($sql);
		if($qData) return true;
		else return false;
	}
	
	
}

?>

