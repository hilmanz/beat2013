<?php

class messageHelper {
	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline()) {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
		}
		else $this->uid = 0;
		$this->dbshema = "athreesix";
	}	
	
	function getMessage($start=0,$limit=10){
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==0) $start = intval($this->apps->_p('start'));
		
		$search = "";
		$startdate = "";
		$enddate = "";
		if ($this->apps->_p('search')) {
			if ($this->apps->_p('search')!="Search...") {
				$search = rtrim($this->apps->_p('search'));
				$search = ltrim($search);
				
				if(strpos($search,' ')) $parseSearch = explode(' ', $search);
				else $parseSearch = false;
				
				if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
				else  $search = trim($search);
				$search = "AND (msg.message REGEXP  '{$search}') ";
			}
		}
		if ($this->apps->_p('startdate')) {
			$start_date = $this->apps->_p('startdate');
			$startdate = "AND msg.datetime >= '{$start_date}' ";
		}
		if ($this->apps->_p('enddate')) {
			$end_date = $this->apps->_p('enddate');
			$enddate = "AND msg.datetime < '{$end_date}' ";
		}
		
		$sql_total = "
			select count(a.id) total
			FROM (
				SELECT *
				FROM my_message msg	
				WHERE ( fromid={$this->uid} OR recipientid ={$this->uid} ) {$search} {$startdate} {$enddate} AND n_status=1 
				GROUP BY parentid ORDER BY datetime DESC
			) a
		";
		$total = $this->apps->fetch($sql_total);
		
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql =  "
			SELECT *
			FROM my_message msg	
			WHERE ( fromid={$this->uid} OR recipientid ={$this->uid} ) {$search} {$startdate} {$enddate} AND n_status=1 
			GROUP BY parentid ORDER BY datetime DESC LIMIT {$start},{$limit} 
		";
		//pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
			$sdata = false;
			$socialData = false;
			foreach($qData as $val){
				$sdata[$val['fromid']] = $val['fromid'];
			}
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
			}
			
			if($socialData){
				foreach($qData as $key => $val){
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			
			$result['result'] = $qData;
			$result['total'] = intval($total['total']);
			return $result;
		}
		return false;
	}
	
	function readmessage(){
		
		$id = intval($this->apps->_request('id'));
		if($id==0) return false;
			
		$sql =  "
		SELECT *
		FROM my_message msg	
		WHERE id={$id} AND n_status=1 
		ORDER BY datetime DESC LIMIT 1";
		$qData = $this->apps->fetch($sql,1);
		$data = false;
		if($qData) {	
			$sdata = false;
			$socialData = false;
			foreach($qData as $val){
				$sdata[$val['fromid']] = $val['fromid'];
			}
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
			}
			
			if($socialData){
				foreach($qData as $key => $val){
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			$data['message'] = $qData;
			$data['reply'] = $this->replymessage();
			// pr($qData);exit;
			return $data;		
		}
		
		return false;
	}

	function replymessage($start=0,$limit=10){	
		$id = intval($this->apps->_request('id'));
		if($start==0) $start = intval($this->apps->_p('start'));

		$sql =  "
		SELECT *
		FROM my_message msg	
		WHERE parentid={$id} AND id<>{$id} AND n_status=1 
		ORDER BY datetime DESC LIMIT {$start},{$limit} ";
		$qData = $this->apps->fetch($sql,1);
	
		if($qData) {	
			$sdata = false;
			$socialData = false;
			foreach($qData as $val){
				$sdata[$val['fromid']] = $val['fromid'];
			}
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
			}
			
			if($socialData){
				foreach($qData as $key => $val){
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			// pr($qData);exit;
			return $qData;		
		}
		
		return false;	
	}
	
	function createMessage($recipientid=false,$message=false){
		$datetime = date("Y-m-d H:i:s");
		
		$parentid = intval($this->apps->_p('parentid'));
		if(!$recipientid)$recipientid = intval($this->apps->_p('recipientid'));
		if(!$message)$message = strip_tags($this->apps->_p('message'));
			
		$sql ="
			INSERT INTO my_message (fromid,recipientid,message,datetime,n_status,fromwho,parentid) 
			VALUES ({$this->uid},{$recipientid},'{$message}','{$datetime}',1,1,{$parentid})
			";
		// pr($sql);exit;
		$this->apps->query($sql);
			
		if($this->apps->getLastInsertId()>0) {
			if($parentid==0) {
				$parentid = $this->apps->getLastInsertId();
				$sql ="
					UPDATE my_message set parentid={$parentid}
					WHERE
					id={$parentid} LIMIT 1					
					";
				$this->apps->query($sql);
				
				
			}
			$sql ="
					UPDATE my_message set datetime=NOW()
					WHERE
					id={$parentid} LIMIT 1					
					";
				$this->apps->query($sql);
			return $parentid;
		}
		return false;
	
	}
	
	function getSocialData($strsdata=false){
		global $CONFIG;
		if($strsdata==false) return false;
		$data =false;
		$sql ="SELECT id,name,last_name,img FROM social_member WHERE id IN ({$strsdata}) ";
		
			$sQdata = $this->apps->fetch($sql,1);	
			// pr($sQdata);	
			if($sQdata){
				foreach($sQdata as $key => $val){
							if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $val['img'] = false;
							if($val['img']) $sQdata[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
							else $sQdata[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
							$data[$val['id']]=$sQdata[$key];			
				}
			}
			
		return $data;
	
	}	
	
	function uninboxmessage(){
			$cid = $this->apps->_p('cid');
			$sql ="
					UPDATE my_message set n_status=3
					WHERE
					id={$cid} AND fromid={$this->uid} LIMIT 1	
					
					";
			if($this->apps->query($sql)) return true;
			else return false;
	
	}
	
}