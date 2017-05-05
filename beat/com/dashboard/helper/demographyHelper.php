<?php

class demographyHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
	}
	
	function provinceData() {
		$sql = "SELECT count(*) num, b.provinceName FROM social_member AS a LEFT JOIN athreesix_city_reference AS b ON a.city = b.id GROUP BY `provinceid` LIMIT 7 ";
		$qData = $this->apps->fetch($sql,1);
		// pr($qData);
		return $qData;
		
	}
	
	function cityData() {
		$sql = "SELECT count(*) num, b.city FROM social_member AS a LEFT JOIN athreesix_city_reference AS b ON a.city = b.id GROUP BY b.id LIMIT 7 ";
		$qData = $this->apps->fetch($sql,1);

		return $qData;
		
	}
	
	function genderData (){
				
		$sql = "SELECT count(*) as num, IF(sex IS NULL , 'unknown', sex) sex FROM social_member GROUP BY sex ";
		$qData = $this->apps->fetch($sql,1);
		$data = false;
		foreach($qData as $val){
			$data[$val['sex']] = $val['num'];
		}
		$data['unknown'] = 0;
		return $data;
		
	}
		
	
	
	function ageData (){
				
		$sql = "SELECT count(*) num, YEAR(CURRENT_TIMESTAMP) - YEAR(birthday) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(birthday, 5)) as age FROM social_member  GROUP by age";
		$qData = $this->apps->fetch($sql,1);
		
		// 18 -20
		// 21 - 26
		// 27 - 30++
		$data['null'] = 0;
		$data['youth']= 0;
		$data['middle']= 0;
		$data['older']= 0;
		foreach( $qData as $val ){
			if($val['age']==null ) $data['null']+= $val['num'];
			else{
			if($val['age']<=24 ) $data['youth'] += $val['num']; 
			if($val['age']>=25 && $val['age']<=28 ) $data['middle'] += $val['num'];
			if($val['age']>=29 ) $data['older']+= $val['num'];
			}
			
		}
		
		$data['null']=0;
		// pr($val);
		return $data;
	}
	

}

?>