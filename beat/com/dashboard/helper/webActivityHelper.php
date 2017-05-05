<?php

class webActivityHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "athreesix";	
	}
	
	

	/* Top 10 (per user activity) Like */
	function topUserActLike(){
		$sql = "SELECT COUNT( * ) num , log.action_value, action.activityName, log.user_id 
				FROM tbl_activity_log as log
				LEFT JOIN tbl_activity_actions as action on action.id = log.action_id 
				WHERE log.action_id = 9 
				GROUP BY  log.user_id  
				ORDER BY num DESC";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topUserActComment(){
		$sql = "SELECT count( * ) num, log.action_value, log.user_id, action.activityName
				FROM tbl_activity_log AS log
				LEFT JOIN tbl_activity_actions AS
				ACTION ON action.id = log.action_id
				WHERE log.action_id = 10
				GROUP BY user_id
				ORDER BY num DESC";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topUserActUpload(){
		$sql = "SELECT count( * ) num, log.action_value, log.user_id, action.activityName
				FROM tbl_activity_log AS log
				LEFT JOIN tbl_activity_actions AS
				ACTION ON action.id = log.action_id
				WHERE log.action_id = 7
				GROUP BY user_id
				ORDER BY num DESC";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topUserActDownload(){
		$sql = "SELECT count( * ) num, log.action_value, log.user_id, action.activityName
				FROM tbl_activity_log AS log
				LEFT JOIN tbl_activity_actions AS
				ACTION ON action.id = log.action_id
				WHERE log.action_id = 22
				GROUP BY user_id
				ORDER BY num DESC";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	/* End of User Activities Tab */
	
	/* --------------------------- */

	/* Top Users Tab */
	
	function topViewMostTime(){
		$sql = "SELECT COUNT(*) num , log.user_id, sm.name
				FROM tbl_activity_log as log
				LEFT JOIN social_member AS sm ON log.user_id = sm.id
				WHERE log.action_id = 6 
				GROUP BY sm.id
				ORDER BY num DESC LIMIT 10";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	
	}
	
	function topTenSuperUser($type=30){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "SELECT count(*) num, DATE_FORMAT(log.date_time,'%Y-%m-%d') datetime , sm.name
				FROM `tbl_activity_log` log
				LEFT JOIN social_member sm ON sm.id = log.user_id
				WHERE log.action_id = 1 {$qTypeUser} GROUP BY sm.name,datetime {$qActiveUser} 
				ORDER BY datetime DESC LIMIT 10 "; 
		$qData = $this->apps->fetch($sql,1);
		return $qData;
		
	}
	
	function topTenUserWeekly($type=7){
		$qTypeUser = "";
		$qActiveUser = "";
		if($type!=0) {
			$startdate =  $this->apps->_g('startdate');
			if($startdate=='') $startdate = " DATE_SUB(NOW() , INTERVAL {$type} DAY )";
			$qTypeUser = " AND date_time BETWEEN {$startdate} AND NOW() ";
			$qActiveUser = " HAVING count(*) > 1 ";
		}
		
		$sql = "SELECT count(*) num, DATE_FORMAT(log.date_time,'%Y-%m-%d') datetime , sm.name
				FROM `tbl_activity_log` log
				LEFT JOIN social_member sm ON sm.id = log.user_id
				WHERE log.action_id = 1 {$qTypeUser} GROUP BY sm.name,datetime {$qActiveUser} 
				ORDER BY datetime DESC LIMIT 10 "; 
		$qData = $this->apps->fetch($sql,1);
		return $qData;
		
	}
	
	/* End of Top User Tab */
	
	/* --------------------------- */
	
	/* Top Visited Page */
	
	function topTenVisitedPage(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 10";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topThreeVisitMusic(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2 AND c.type = 'music'
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 3";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topThreeVisitDJ(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2 AND c.type = 'dj'
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 3";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topThreeVisitVisualart(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2 AND c.type = 'visualart'
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 3";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topThreeVisitFashion(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2 AND c.type = 'fashion'
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 3";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topThreeVisitPhotography(){
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id IS NOT NULL AND a.action_id = 2 AND c.type = 'photography'
				GROUP BY a.action_value 
				ORDER BY a.date_time LIMIT 3";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	/* End of Top Visited Page */
	
	/* --------------------------- */
	
	/* Top Content Page */
	
	function topFiveLiked() {
		$sql = "SELECT COUNT(*) num, a.action_id, b.id, b.title, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id AND a.action_id = 9
				GROUP BY a.action_value 
				ORDER BY a.date_time"; 
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function topFiveCommented() {
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, b.title, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id AND a.action_id = 10
				GROUP BY a.action_value 
				ORDER BY a.date_time ";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function basedOnContent() {
		$sql = "SELECT COUNT( * ) num, a.action_id, b.id, c.type 
				FROM tbl_activity_log as a 
				LEFT JOIN athreesix_news_content as b on a.action_value = b.id
				LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
				WHERE b.id AND c.type IS NOT NULL GROUP BY c.content_name ORDER BY a.date_time";
		$qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	/* End of Top Visited Page */
	
	/* --------------------------- */
	
	/* Top Content Page */
	
	
	/* Top Most Viewed Artist */
	
	function mostViewedArtist() {
		$sql = "SELECT num , rawTable.id , rawTable.type ,fromwho, authorid ,
						  CASE WHEN fromwho = 0 THEN gm.name
						  WHEN  fromwho = 1 THEN sm.name 
						  ELSE pages.name END  as thename 
						  , 
						  CASE WHEN fromwho = 0 THEN gm.image
						  WHEN  fromwho = 1 THEN sm.img 
						  ELSE pages.img END  as theimages 
				FROM (
						SELECT COUNT( * ) num, a.action_id, b.id, c.type ,a.action_value , b.fromwho, b.authorid
						FROM tbl_activity_log as a 
							LEFT JOIN athreesix_news_content as b on a.action_value = b.id
							LEFT JOIN athreesix_news_content_type as c on b.articleType = c.id
						WHERE b.id IS NOT NULL AND a.action_id = 2
							GROUP BY action_value 
							ORDER BY num DESC LIMIT 10
				) rawTable
					LEFT JOIN gm_member gm ON gm.id=rawTable.authorid 
					LEFT JOIN social_member sm ON sm.id=rawTable.authorid 
					LEFT JOIN my_pages pages ON pages.id = rawTable.authorid 
				WHERE rawTable.type IS NOT NULL
				GROUP BY rawTable.type";
		$qData = $this->apps-fetch($sql,1);
		return $qData;
		
	}
	/* ------------------------------------ */
	
}

?>