<?php 

class leaderboardHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
			
			
		}

		$this->dbshema = "beat";	
	}

	
	
	function getEntourageList($streid=null,$start=0,$limit=10){
		global $CONFIG;
		
		if(intval($this->apps->_request('start'))!=0) $start = intval($this->apps->_request('start'));
		if($streid){			
			$qEntourage = " AND id IN ({$streid}) ";
			$limit = 50;
		}else{
			$qEntourage = "";
		}
		
		$data['result'] = false;
		$data['total'] = 0;
		
		$sql = "SELECT count(*) total FROM my_entourage WHERE referrerbybrand={$this->uid} {$qEntourage} ";	
		$total = $this->apps->fetch($sql);		
		if(!$total)return false;
		$sql = "SELECT * FROM my_entourage WHERE referrerbybrand={$this->uid} {$qEntourage} LIMIT {$start},{$limit}";		
		
		
		$qData = $this->apps->fetch($sql,1);
		
		
		
		if($qData) {
			foreach($qData as $key => $val){
					if(is_file($CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'])) $qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'];
					else  $qData[$key]['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			}
			$data['result'] = $qData;
			
			$data['total'] = $total['total'];
		}
		// pr($data);exit;
		return $data;
		
		
	}
	
}

?>

