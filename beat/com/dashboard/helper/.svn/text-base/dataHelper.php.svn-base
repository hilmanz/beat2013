<?php

class dataHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
	}
	
	
		
	function allUserRegistration(){
		$sql = "SELECT count(*) num , DATE_FORMAT(register_date,'%Y-%m-%d') datetime, sex FROM social_member GROUP BY  sex,datetime ORDER BY datetime DESC LIMIT 7 "; 
		$qData = $this->apps->fetch($sql,1);
		// pr($qData);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){
			$data['data'][$val['datetime']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];		
			
			
		// pr($val);
			
		}
		
		$data['count'] = count($qData);
		// pr($data);
		/* $query = "SELECT count(*) as jumlah, sex FROM social_member";
		$result = $this->apps->fetch($query);
		$data['jumlah'] = $result['jumlah'];
		
		$queryFemale = "SELECT count(*) as jumlah_female, sex FROM social_member where sex = 'F' ";
		$resultFemale = $this->apps->fetch($queryFemale);
		$data['jumlah_female'] = $resultFemale['jumlah_female'];
		
		$queryMale = "SELECT count(*) as jumlah_male, sex FROM social_member where sex = 'M' ";
		$resultMale = $this->apps->fetch($queryMale);
		$data['jumlah_male'] = $resultMale['jumlah_male'];
		// pr($data); */
		return $data;
		
	}
	
	function userUnverified(){
		$sql = "SELECT count(*) num , sex, DATE_FORMAT(register_date,'%Y-%m-%d') datetime FROM social_member WHERE n_status=0 GROUP BY  datetime ORDER BY datetime DESC LIMIT 7"; 
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['datetime']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];
			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];	
		}		
		
		// pr($data);
		return $data;
		
	}
	
	function loginUser($type=7){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND log.date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "
		SELECT count(*) num, DATE_FORMAT(log.date_time,'%Y-%m-%d') datetime , sm.sex
		FROM `tbl_activity_log` log
		LEFT JOIN social_member sm ON sm.id = log.user_id
		WHERE log.action_id = 1 {$qTypeUser} GROUP BY sm.sex,datetime {$qActiveUser} ORDER BY datetime DESC LIMIT 7  "; 
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['datetime']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];	
		}		
		
		// pr($data);
		return $data;
		
	}
	
	function activeUser($type=1){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "
		SELECT count(*) num, DATE_FORMAT(log.date_time,'%Y-%m-%d') datetime , sm.sex
		FROM `tbl_activity_log` log
		LEFT JOIN social_member sm ON sm.id = log.user_id
		WHERE log.action_id = 1 {$qTypeUser} GROUP BY sm.sex,datetime {$qActiveUser} ORDER BY datetime DESC LIMIT 7 "; 
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$data['count'] = 0;
		$data['jumlah_female'] = false;
		$data['jumlah_male'] = false;
		$data['unknown'] = false;
		$data['jumlah'] = false;
		foreach($qData as $val){	
			$data['data'][$val['datetime']] = $val['num'];
			if($val['sex']=="F") $data['jumlah_female']+= $val['num'];
			if($val['sex']=="M") $data['jumlah_male']+= $val['num'];			
			if($val['sex']!="M"&&$val['sex']!="F") $data['unknown']+= $val['num'];			
			
			$data['date'][$val['datetime']] = $val['datetime'];
			
			$data['jumlah']+= $val['num'];		
		}		
		
		return $data;
		
	}
	
	
	function superUser ($type=30){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "SELECT count(*) num, DATE_FORMAT(date_time,'%Y-%m-%d') datetime FROM `tbl_activity_log` WHERE action_id = 1 {$qTypeUser} GROUP BY datetime {$qActiveUser} ORDER BY datetime DESC LIMIT 7 "; 
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		foreach($qData as $val){	
			$data['data'][$val['datetime']] = $val['num'];
			$data['date'][$val['datetime']] = $val['datetime'];
		}		
		return $data;
		
	}
	
	
	function getChartDataOf($searchData=null){
		
		if($searchData==null) return false;
		
		if(is_array($searchData)) {
			foreach($searchData as $val){
				$nuArr[] = "'{$val}'";
			}
			if($nuArr)	$searchData = implode(',',$nuArr);
			else return false;
		}
		
		$theactivity = "{$searchData}";
		
		/* get activity ID */
		$actionnamedata = $this->getactivitytype($theactivity);

		if($actionnamedata) {
			
			$activityID = implode(',',$actionnamedata['id']);
		}else $activityID = false;
			
		$sql = "SELECT count(*) total, DATE(date_time) dateformatonly  FROM tbl_activity_log WHERE action_id IN ({$activityId}) ORDER BY dateformatonly GROUP BY dateformatonly LIMIT {$start},{$limit}";

		$getChartDataOf[$searchData] = $this->apps->fetch($sql);
		
		//pr($getChartDataOf);
		exit;

	}

	function getactivitytype($dataactivity=null,$id=false){
			if($dataactivity==null)return false;
			if($id) $qAppender = " id IN ({$dataactivity}) ";
			else $qAppender = " activityName IN ({$dataactivity})  ";
			$theactivity = false;
			/* get activity  id */	
			$sql = " SELECT * FROM tbl_activity_actions WHERE {$qAppender} ";

			$qData = $this->apps->fetch($sql,1);
				
			if($qData) {
				foreach($qData as $val){
					$theactivity['id'][$val['id']]=$val['id'];
					$theactivity['name'][$val['id']]=$val['activityName'];
					
				}
			}
			return $theactivity;
		}

}

?>