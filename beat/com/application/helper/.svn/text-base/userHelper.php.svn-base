<?php 

class userHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbshema = "beat";	
	}

	function getUserProfile(){
		global $CONFIG;
	
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
			$sql = "
			SELECT sm.id,sm.name,sm.last_name,sm.img,sm.sex,sm.username,sm.nickname,sm.register_date,sm.StreetName,sm.phone_number,sm.email,sm.last_login,sm.n_status,sm.sex,sm.birthday,cityref.city as cityname FROM social_member sm
			LEFT JOIN {$this->dbshema}_city_reference cityref ON sm.city = cityref.id
			WHERE sm.id = {$uid} LIMIT 1";
			// pr($sql);
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);
			if(!$qData)return false;
			$sql ="
			SELECT rank.*
			FROM my_rank mrank
			LEFT JOIN {$this->dbshema}_rank_table ranktable ON ranktable.id = mrank.rank
			WHERE userid = {$uid} 
			AND n_status = 1 LIMIT 1		
			";
			
			$qRankData = $this->apps->fetch($sql);	
		
			if($qRankData){
						
						$qData['rank'] = $qRankData['rank'];
				
			}
			
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$qData['img']}")) $qData['img'] = false;
			// if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/crop{$qData['img']}")) $qData['img'] = "crop{$qData['img']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/original_{$qData['img']}")) $qData['imgoriginal']= "original_{$qData['img']}";
			else $qData['imgoriginal'] = false;
			
			// $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/";
			
			if($qData['img']) $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$qData['img'];
			else $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
			
			$plan = $this->apps->contentHelper->getArticleContent(null,10,4,array(0,3),"plan");
			
			$data['notification'] = $this->apps->activityHelper->getA360activity(0,5,false,false,false,'3',false);	
			$data['plan']['total'] = $plan['total'];
			$data['plan']['lists'] = $plan['result'];
			$data['challenge'] = false;
			$data['entourage'] = $this->apps->entourageHelper->getEntourage();
			$data['inbox'] = $this->apps->messageHelper->getMessage(0,2);
		
			if($data) $qData = array_merge($qData,$data);
			// pr($qData);
			return $qData;
		}
		return false;
	}
	
	function getTotalEngagement() {
	global $CONFIG;
	
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		
		$sql = "SELECT COUNT(*) FROM my_rank WHERE userid ={$uid} LIMIT 1";
			// pr($sql);
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);
	}
	
	function getUserAttribute(){		
		$sql = "
		SELECT sum(ancr.point) rank,categoryid ,category
		FROM axis_news_content_rank ancr
		LEFT JOIN axis_news_content_category ancc ON ancc.id= ancr.categoryid
		WHERE userid={$this->uid} 
		GROUP BY categoryid ORDER BY rank DESC LIMIT 5 ";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
	
		if($qData){
			$mostLike = null;
			foreach($qData as $val){
				$mostLike[] = $val['category'];		
			}
			$userLikeCategory = implode(' , ',$mostLike);
		}
		$sql = "
			SELECT art.rank titleRank,art.id levelRank FROM my_rank sr
			LEFT JOIN social_media_account sma ON sma.userid=sr.userid
			LEFT JOIN axis_rank_table art ON art.id=sr.rank
			WHERE sr.userid = {$this->uid} AND sr.n_status = 1 limit 1		
		";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);
		if(isset($userLikeCategory)) $qData['userlike'] = $userLikeCategory;
		if($qData)	return $qData;
		else return false;
	
	}
	
	function getRankUser(){
		$sql ="
			SELECT * 
			FROM my_rank 
			WHERE userid = {$this->uid} 
			AND n_status = 1 LIMIT 1		
			";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);	
	
		if($qData){
			$lastPoint = $qData['point'];
			$lastDate  = $qData['date'];
	
			$qData = null;
			//cek new point // > tanggal
			$sql ="
				SELECT SUM(score) total 
				FROM tbl_exp_point 
				WHERE user_id = {$this->uid} AND date_time > '{$lastDate}'
				";
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			$point = $qData['total'];
			$qData = null;
					
			//klo ada point baru, setelah penginsert-an point sebelum nya , tambah point nya
			if($point==0)	return false;
				
			$newPoint = $lastPoint+$point;
					
			$sql = "
				SELECT id FROM {$this->dbshema}_rank_table 
				WHERE minPoint <= {$newPoint} AND maxPoint > {$newPoint} LIMIT 1";
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			$rank = $qData['id'];
			$qData = null;
			
			if($rank){
				$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",{$rank},{$newPoint},1) ";
				$this->logger->log($sql);
				$qData = $this->apps->query($sql);
				$lastID = $this->apps->getLastInsertId();
				$qData = null;
				if($lastID!=0 || $lastID!=null){
				
					$sql="UPDATE my_rank SET n_status = 0 WHERE userid={$this->uid} AND id <> {$lastID}  ";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);
					$qData = null;
				}else {
					//cek data if n_status 1 have duplicate value
					$sql = "
						SELECT count(*) total, id FROM my_rank 
						WHERE n_status = 1 AND userid={$this->uid} ORDER BY id DESC LIMIT 2";
						$this->logger->log($sql);
					$qData = $this->apps->fetch($sql);	
					
					if($qData['total']>=2){
						$qData = null;
						$sql = "
						SELECT id FROM my_rank 
						WHERE n_status = 1 AND userid={$this->uid} ORDER BY id DESC LIMIT 1";
						$this->logger->log($sql);
						$qData = $this->apps->fetch($sql);	
						$usingIDRank = intval($qData['id']);
						$qData = null;
						if($usingIDRank!=0){
							$sql="UPDATE my_rank SET n_status = 0 WHERE id <> {$usingIDRank} AND userid={$this->uid} ";
							$this->logger->log($sql);
							$qData = $this->apps->query($sql);
							$qData = null;
						} 
					}else return true;
				
				
				}
			}
			return false;
			
		}else{
			
			//cek klo uda ada activity brarti rollback rank nya
			$sql ="
					SELECT count(*) total 
					FROM tbl_exp_point 
					WHERE user_id = {$this->uid} 
					LIMIT 1	
					";
				$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			
			if($qData['total']<=0){
				//klo ga ada. insert ke social rank newbie
				$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",1,0,1) ";
				$this->logger->log($sql);
				$qData = $this->apps->query($sql);	
			}else{
				$qData = null;
				$sql ="
					SELECT SUM(score) total 
					FROM tbl_exp_point 
					WHERE user_id = {$this->uid} 
					";
					$this->logger->log($sql);
				$qData = $this->apps->fetch($sql);	
				$point = intval($qData['total']);
				$qData = null;
			
				$sql = "
					SELECT id FROM {$this->dbshema}_rank_table
					WHERE minPoint <= {$point} AND maxPoint >= {$point} LIMIT 1";
					$this->logger->log($sql);
				$qData = $this->apps->fetch($sql);	
				$rank = $qData['id'];
					
				if($rank!=0|| $rank!=null){
					$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",{$rank},{$point},1) ";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);		
					return true;					
				}
			}
		return false;
		}
		
	
	}
	
	
	function getPreferenceThemeUser(){
		$sql =" SELECT * FROM social_preference_page WHERE userid={$this->uid} AND n_status=1 LIMIT 1";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);
		// print_r( unserialize($qData['apperances']));exit;
		if($qData) return unserialize($qData['apperances']);
		else return false;
	}
	
	function savePreferenceThemeUser(){
		$data = $this->getPreferenceThemeUser();
		if($this->apps->Request->getPost('bodyColor')) $data['body']['color'] = $this->apps->Request->getPost('bodyColor');
		// if($this->apps->Request->getPost('bodyImage')) $data['body']['image'] = $this->apps->Request->getPost('bodyImage');
		// $data['content']['color'] = $this->apps->Request->getPost('contentColor');
		// $data['border']['color'] = $this->apps->Request->getPost('borderColor');
		// $data['header']['font']['family'] = $this->apps->Request->getPost('headerFontFamily');
		// $data['header']['font']['size'] = $this->apps->Request->getPost('headerFontSize');
		// $data['header']['font']['color'] = $this->apps->Request->getPost('headerFontColor');
		if( $this->apps->Request->getPost('contentFontFamily')) $data['content']['font']['family'] = $this->apps->Request->getPost('contentFontFamily');
		if( $this->apps->Request->getPost('contentFontSize')) $data['content']['font']['size'] = $this->apps->Request->getPost('contentFontSize');
		if( $this->apps->Request->getPost('contentFontColor')) $data['content']['font']['color'] = $this->apps->Request->getPost('contentFontColor');
				
		$dataPreference = serialize($data);
		
		$sql="INSERT INTO 
		social_preference_page (userid,apperances,date,n_status) VALUES ({$this->uid},'{$dataPreference}',NOW(),1) 
		ON DUPLICATE KEY UPDATE
		apperances = VALUES(apperances)
		";
		$this->logger->log($sql);
		$qData = $this->apps->query($sql);	
		
		
	}
	
	
	function updateUserProfile(){
	
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$this->logger->log('can update profile');
		//cek token valid

		$tokenize = strip_tags($this->apps->_p('tokenize'));
		$accepttoken = cektokenize($tokenize,$this->uid);		
		if(!$accepttoken) return false;
		
		//get user
		$sql = "SELECT * FROM social_member WHERE n_status=1 AND id={$this->uid} LIMIT 1";
		$this->logger->log($sql);
		$rs = $this->apps->fetch($sql);
		if(!$rs)return false;
		$rs = null;
		$name = strip_tags($this->apps->_p('name'));
		$influencer = strip_tags($this->apps->_p('influencer'));
		$StreetName = strip_tags($this->apps->_p('StreetName'));
		$sex = strip_tags($this->apps->_p('sex'));
		$birthday = strip_tags($this->apps->_p('birthday'));
		$description = strip_tags($this->apps->_p('description'));
		if($name!='') $arrQuery[] = " name='{$name}' ";
		if($influencer!='') $arrQuery[] = " influencer='{$influencer}' ";
		if($StreetName!='') $arrQuery[] = " StreetName='{$StreetName}' ";
		if($sex!='') $arrQuery[] = " sex='{$sex}' ";
		if($birthday!='') $arrQuery[] = " birthday='{$birthday}' ";
		if($description!='') $arrQuery[] = " description='{$description}' ";

			$strQuery = implode(',',$arrQuery);
			if(!$strQuery) return false;
			$this->logger->log($strQuery);
			
			$sql = "
			UPDATE social_member 
			SET {$strQuery} 
			WHERE id={$this->uid} LIMIT 1
			";
			// pr($influencer);exit;
			$this->logger->log($sql);

			$qData = $this->apps->query($sql);
			if($qData) {
					$sql = "
					SELECT *
					FROM social_member 
					WHERE 
					n_status=1 AND 
					id={$this->uid}
					LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
				return true;
			}else return false;
		
			
	
			
		}	
	
	function saveImage($widget){
		global $CONFIG,$LOCALE;
		$filename="";
		if($_FILES['myImage']['error']==0)	{
			if ($_FILES['myImage']['size'] <= 2560000) {
				$path = $widget=='photo_profile' ? $CONFIG['LOCAL_PUBLIC_ASSET']."user/photo/" : $CONFIG['LOCAL_PUBLIC_ASSET']."user/cover/";	
				$dataImage  = $this->apps->uploadHelper->uploadThisImage(@$_FILES['myImage'],$path,220,true);
				if($dataImage['result']){
					if ($widget=='photo_profile') {
						/* kata angga ga perlu otomatis ke update */
						/* 	$sql = "UPDATE social_member SET  img = '{$dataImage['arrImage']['filename']}' WHERE id={$this->uid} LIMIT 1";
							$this->logger->log($sql);
							
							$qData = $this->apps->query($sql);
							if($qData)	$filename = @$dataImage['arrImage']['filename'];
						*/
						$filename = @$dataImage['arrImage']['filename'];
					} elseif ($widget=='photo_cover') {
						$sql_cover = "INSERT INTO my_wallpaper (myid,image,type,datetime,n_status) 
							values ('{$this->uid}','{$dataImage['arrImage']['filename']}',0,NOW(),1)
						";
						$arrData = $this->apps->query($sql_cover);
						if($arrData) $filename = @$dataImage['arrImage']['filename'];
					}
				}
			} else {
				return false;
			}
		}
		return $filename;
	}
	
	function saveImageCover(){
		global $CONFIG;
		$filename="";
	// return array('result'=>true,'arrImage'=> $arrImageData);
		if($_FILES['myImage']['error']==0)	{
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."user/photo/";	
			$dataImage  = $this->apps->uploadHelper->uploadThisImage(@$_FILES['myImage'],$path);
			if($dataImage['result']){
			
				$sql = "
				UPDATE social_member 
				SET  img = '{$dataImage['arrImage']['filename']}'
				WHERE id={$this->uid} LIMIT 1
				";
				$this->logger->log($sql);
				
				$qData = $this->apps->query($sql);
				if($qData)	$filename = @$dataImage['arrImage']['filename'];
			}
		}
		return $filename;
	}
	
	
	
	function saveCropImage(){
				global $CONFIG;
				
				$loginHelper = $this->apps->useHelper('loginHelper');
				
				$files['source_file'] = $this->apps->_p("imageFilename");
				$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/";
				$arrFilename = explode('.',$files['source_file']);
				if($files==null) return false;
				$targ_w = $this->apps->_p('w');
				$targ_h =$this->apps->_p('h');
				$jpeg_quality = 90;
				
				if($files['source_file']=='') return false;
				
				//check is img have original char
						
				$arrOriginal = explode("_",$files['source_file']);
				if(is_array($arrOriginal)){
					if($arrOriginal[0]=='original') {						
						$files['source_file'] = $arrOriginal[1];
						unlink($files['url'].$files['source_file']);
						copy($files['url']."original_".$files['source_file'],$files['url'].$files['source_file']);
					}
					
				}				
			
				$src = 	$files['url'].$files['source_file'];
				copy($src, $files['url']."original_".$files['source_file']);
			
				try{
					
					$img_r = false;
					if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) $img_r = imagecreatefromjpeg($src);
					if($arrFilename[1]=='png' ) $img_r = imagecreatefrompng($src);
					if($arrFilename[1]=='gif' ) $img_r = imagecreatefromgif($src);
					if(!$img_r) return false;
					$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

					imagecopyresampled($dst_r,$img_r,0,0,$this->apps->_p('x'),$this->apps->_p('y'),	$targ_w,$targ_h,$this->apps->_p('w'),$this->apps->_p('h'));

					// header('Content-type: image/jpeg');
					if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) imagejpeg($dst_r,$files['url'].$files['source_file'],$jpeg_quality);
					if($arrFilename[1]=='png' ) imagepng($dst_r,$files['url'].$files['source_file']);
					if($arrFilename[1]=='gif' ) imagegif($dst_r,$files['url'].$files['source_file']);
					
				}catch (Exception $e){
					return false;
				}
				include_once '../engines/Utility/phpthumb/ThumbLib.inc.php';
					
				try{
					$thumb = PhpThumbFactory::create($files['url'].$files['source_file']);
				}catch (Exception $e){
					// handle error here however you'd like
				}
				list($width, $height, $type, $attr) = getimagesize($files['url'].$files['source_file']);
				$maxSize = 400;
				if($width>=$maxSize){
					if($width>=$height) {
						$subs = $width - $maxSize;
						$percentageSubs = $subs/$width;
					}
				}
				if($height>=$maxSize) {
					if($height>=$width) {
						$subs = $height - $maxSize;
						$percentageSubs = $subs/$height;
					}
				}
				if(isset($percentageSubs)) {
				 $width = $width - ($width * $percentageSubs);
				 $height =  $height - ($height * $percentageSubs);
				}
				
				$w_small = $width - ($width * 0.5);
				$h_small = $height - ($height * 0.5);
				$w_tiny = $width - ($width * 0.7);
				$h_tiny = $height - ($height * 0.7);
				
				//resize the image
				$thumb->adaptiveResize($width,$height);
				$big = $thumb->save(  "{$files['url']}".$files['source_file']);
				$thumb->adaptiveResize($width,$height);
				$prev = $thumb->save(  "{$files['url']}prev_".$files['source_file']);
				$thumb->adaptiveResize($w_small,$h_small);
				$small = $thumb->save( "{$files['url']}small_".$files['source_file'] );
				$thumb->adaptiveResize($w_tiny,$h_tiny);
				$tiny = $thumb->save( "{$files['url']}tiny_".$files['source_file']);
								
				if(is_file($files['url'].$files['source_file'])){
					//saveit
					$sql = "
					UPDATE social_member 
					SET  img = '{$files['source_file']}'
					WHERE id={$this->uid} LIMIT 1
					";
					$this->logger->log($sql);
					
					$qData = $this->apps->query($sql);
					if($qData){
							$sql = "
							SELECT *
							FROM social_member 
							WHERE 
							n_status=1 AND id={$this->uid} LIMIT 1 ";
						$rs = $this->apps->fetch($sql);	
						if(!$rs)return false;
						$rs['img'] = $files['source_file'];
						//how to update the session on on fly
						if($rs) $loginHelper->setdatasessionuser($rs); 
						else return false;
						return $files['source_file'];
					}else return false;
					
				}else return false;
				
	}
	
	function saveCropCoverImage(){
		global $CONFIG;
		
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$files['source_file'] = $this->apps->_p("imageFilename");
		$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}user/cover/";
		$arrFilename = explode('.',$files['source_file']);
		if($files==null) return false;
		$targ_w = $this->apps->_p('w');
		$targ_h =$this->apps->_p('h');
		$jpeg_quality = 90;
		
		if($files['source_file']=='') return false;		
		
		//check is img have original char						
		$arrOriginal = explode("_",$files['source_file']);
		if(is_array($arrOriginal)){
			if($arrOriginal[0]=='original') {						
				$files['source_file'] = $arrOriginal[1];
				unlink($files['url'].$files['source_file']);
				copy($files['url']."original_".$files['source_file'],$files['url'].$files['source_file']);
			}
			
		}				
	
		$src = 	$files['url'].$files['source_file'];
		copy($src, $files['url']."original_".$files['source_file']);
		
		try{
			$img_r = false;
			$arrFilename[1] = strtolower($arrFilename[1]);
			if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) $img_r = imagecreatefromjpeg($src);
			if($arrFilename[1]=='png' ) $img_r = imagecreatefrompng($src);
			if($arrFilename[1]=='gif' ) $img_r = imagecreatefromgif($src);
			if(!$img_r) return false;
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$this->apps->_p('x'),$this->apps->_p('y'),	$targ_w,$targ_h,$this->apps->_p('w'),$this->apps->_p('h'));

			// header('Content-type: image/jpeg');
			if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) imagejpeg($dst_r,$files['url'].$files['source_file'],$jpeg_quality);
			if($arrFilename[1]=='png' ) imagepng($dst_r,$files['url'].$files['source_file']);
			if($arrFilename[1]=='gif' ) imagegif($dst_r,$files['url'].$files['source_file']);
			
		}catch (Exception $e){
	
			return false;
		}
		include_once '../engines/Utility/phpthumb/ThumbLib.inc.php';
			
		try{
			$thumb = PhpThumbFactory::create($files['url'].$files['source_file']);
		}catch (Exception $e){
			// handle error here however you'd like
		}
		list($width, $height, $type, $attr) = getimagesize($files['url'].$files['source_file']);
		$maxSize = 400;
		if($width>=$maxSize){
			if($width>=$height) {
				$subs = $width - $maxSize;
				$percentageSubs = $subs/$width;
			}
		}
		if($height>=$maxSize) {
			if($height>=$width) {
				$subs = $height - $maxSize;
				$percentageSubs = $subs/$height;
			}
		}
		if(isset($percentageSubs)) {
		 $width = $width - ($width * $percentageSubs);
		 $height =  $height - ($height * $percentageSubs);
		}
		
		$w_small = $width - ($width * 0.5);
		$h_small = $height - ($height * 0.5);
		$w_tiny = $width - ($width * 0.7);
		$h_tiny = $height - ($height * 0.7);
		
		//resize the image
		$thumb->adaptiveResize($width,$height);
		$big = $thumb->save(  "{$files['url']}".$files['source_file']);
		$thumb->adaptiveResize($width,$height);
		$prev = $thumb->save(  "{$files['url']}prev_".$files['source_file']);
		$thumb->adaptiveResize($w_small,$h_small);
		$small = $thumb->save( "{$files['url']}small_".$files['source_file'] );
		$thumb->adaptiveResize($w_tiny,$h_tiny);
		$tiny = $thumb->save( "{$files['url']}tiny_".$files['source_file']);
						
		if(is_file($files['url'].$files['source_file'])){
			$sql = "UPDATE my_wallpaper SET image = '{$files['source_file']}' WHERE myid={$this->uid} AND type=0 ORDER BY datetime DESC LIMIT 1";
			$this->logger->log($sql);			
			$qData = $this->apps->query($sql);
			if($qData){
				return $files['source_file'];
			} else return false;			
		} else return false;				
	}
	
	function isFriends($fid=null,$all=false){
		$fid = strip_tags($fid);
		if($fid=='') return false;
		if($this->uid==0) return false;
			
		$sql = "SELECT * FROM my_circle WHERE userid= {$this->uid} AND friendid IN ({$fid}) AND n_status<>0";

		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data['total'] = count($qData);
		$data['result'] = $qData;
		
		if($data['total']>0) {
			if(!$all)return true;
			else return $data['result'];
		}else return false;
		
	}
	
	function getGroupUser(){
			$uid = strip_tags($this->apps->_request('uid'));
			if(!$uid) $uid = intval($this->uid);
			if($uid!=0 || $uid!=null) {
				$sql = "SELECT * FROM my_circle_group WHERE userid IN ({$uid}) AND n_status = 1 ORDER BY datetime DESC ";
				
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					foreach($qData as $val){
						$groupCircle[$val['id']] = $val['name'];
					}	
					if($groupCircle)	return $groupCircle;
					else return false;
				}
				
				
			}
			return false;
	}
	function getFriends($all=true,$limit=8,$start=0,$useGroup=true){
	//global user id, for list of friend of friend : 21,23,1,5,3
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_p('start'));
		$group = intval($this->apps->_request('groupid'));
		$circle = false;
		if(!$uid) $uid = intval($this->uid);
		
		if($uid!=0 || $uid!=null) {
		
				
			//get circle group
			if($useGroup){
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
					
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}
				
				
				if($group!=0){			
					$strGroupid = $group;
				}else {	
					array_push($arrGroupId,0);
					$strGroupid = implode(',',$arrGroupId);
				}
			}else $strGroupid=0;
			// get all friend of this user
			$sql =	" SELECT count(*) total FROM ( SELECT friendid FROM my_circle WHERE userid IN ({$uid}) AND n_status = 1 GROUP BY friendid,ftype ) a";
			// pr($sql);
			$friends = $this->apps->fetch($sql);
	// pr($sql);
			if(!$friends) return false;
			if($friends['total']==0) return false;
			
			if($all) $qAllQData = " LIMIT {$start},{$limit} ";
			else  $qAllQData = "";
			$circle =false;
			//get circle
			$sql =	" SELECT * FROM my_circle WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND n_status = 1 GROUP BY friendid,ftype  ORDER BY id DESC {$qAllQData}";
		
			$qData = $this->apps->fetch($sql,1);
			if($qData) {
			$arrSocialFid = false;
			$arrEntourageFid = false;
				foreach($qData as $val){	
					/* BA */	
					if($val['ftype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['ftype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$circledata[$val['ftype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['ftype'] = 1;
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					$entouragefid = $this->entouragedata($strentouragefid);
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['ftype'] = 0;
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				

				
			
				if(!$circledata) return false;
				
				// pr($socialdata);
				// pr($entouragedata);
				// pr($circledata);
				//merge data
				foreach($circledata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
					if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype]))  $circle[] = $socialdata[$keyftype][$key];
					if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))  $circle[] = $entouragedata[$keyftype][$key];
					 // pr($val);
					}
					
				}
			
			
				
			
			}
			// pr($circle);
			if($circle) $data['result'] = true;
			else  $data['result'] = false;
			$data['data'] = $circle;
			
			
			// pr($data);
			return $data;
			
			
		}
		return false;
	}
	
	function getCircleUser($all=true,$limit=8,$start=0){
		//global user id, for list of friend of friend : 21,23,1,5,3
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_request('start'));

		
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
		
				
			//get circle group
		
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
						
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}else array_push($arrGroupId,0);
		
				
				$strGroupid = implode(',',$arrGroupId);
			
			// get all friend of this user
			$sql =	" SELECT count(*) total FROM ( SELECT friendid FROM my_circle WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND n_status = 1 GROUP BY friendid ) a";
		
			$friends = $this->apps->fetch($sql);
			if(!$friends) return false;
			
			//get circle
			$sql =	" SELECT * FROM my_circle WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND n_status = 1 ORDER BY id DESC  ";

			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			foreach($qData as $val){			
				$arrFriendId[ $val['friendid']] = $val['friendid'];
				$circledata[]= $val;
			}
		
			if(!$arrFriendId) return false;
			$strFriendId = implode(',',$arrFriendId);
			if($all) $qAllQData = " LIMIT {$limit} ";
			else  $qAllQData = "";
			//get friend detail
			$sql =	" SELECT id,name,img,sex,last_name FROM social_member WHERE id IN ({$strFriendId}) AND  n_status = 1 {$qAllQData} ";
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $val){
				$frienduser[$val['id']] = $val;
			}
			
			if(!$circledata&&!$frienduser) return false;
			
			//merge data
			foreach($circledata as $key => $val){
				if(array_key_exists($val['friendid'],$frienduser)) $circledata[$key]['frienddetail'] = $frienduser[$val['friendid']];
				else  $circledata[$key]['frienddetail'] = false;			
			}
			
			//create new array
			foreach($circledata as $key => $val){
				$circle[$val['userid']][$val['groupid']][] = $val;
			}
			
			if(!$circle) return false;
			
			// pr($circle);
			$data['result'] = $circle;
			$data['total'] = $friends['total'];	
			
		// pr($data);
			return $data;
			
			
		}
		return false;
	
	}
	
	function createCircleUser(){
		$name = preg_replace("/_/i"," ",strip_tags($this->apps->_request('name')));
		$groupid = intval($this->apps->_p('groupid'));
		if($name=='') return false;
		if($groupid!=0){
			$sql = "
			UPDATE my_circle_group SET name=\"{$name}\"
			WHERE id={$groupid} LIMIT 1;
			";
			// pr($sql);
			$this->apps->query($sql);
			return true;
		}else{
			$sql = "
			INSERT INTO my_circle_group (name,userid,datetime,n_status)
			VALUES ('{$name}',{$this->uid},NOW(),1)
			ON DUPLICATE KEY UPDATE n_status=1;
			";		
			$this->apps->query($sql);
			if($this->apps->getLastInsertId()) return array("result"=>true,"content"=>$this->apps->getLastInsertId());
			else return false;
		}

		
	
	}
	
	function uncreateCircleUser(){
		$circleid = strip_tags($this->apps->_p('circleid'));
		// $name = str_replace("_"," ",strip_tags($this->apps->_request('name')));
		$sql = "
		UPDATE my_circle_group SET n_status=0
		WHERE id= {$circleid} AND userid={$this->uid}
		LIMIT 1
		";
		
		$result = $this->apps->query($sql);
		if($result) {
			$sql = "
			UPDATE my_circle SET groupid = 0
			WHERE userid = {$this->uid} AND groupid={$circleid}
			";
			$result = $this->apps->query($sql);			
			if($result)return true;
			else {
				$sql = "
					DELETE FROM my_circle WHERE groupid <> 0 AND userid = {$this->uid} AND groupid={$circleid}
				";
				$result = $this->apps->query($sql);	
				if($result)return true;
				else return false;
			}
		}else return false;
	
	}
	
	function addCircleUser(){
		$uid = intval($this->apps->_request('fid'));
		$ftype = intval($this->apps->_request('ftype'));
		$groupid = intval($this->apps->_request('groupid'));

		//cek default circle , friends on circle
		if($this->uid==$uid) return false;
		$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype={$ftype} AND groupid=0 LIMIT 1";
			
		$qData = $this->apps->fetch($sql);
		
		if(!$qData) return false;
		if($qData['total']>0){
		$oldid = $qData['id'];
		//if found, use update to move friend
			//check they have other group
				$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype={$ftype} AND groupid = {$groupid} LIMIT 1";
				$qData = $this->apps->fetch($sql);
			
				if(!$qData) return false;
				if($qData['total']>0){
				//if found, update the status to true
					$sql = "
					UPDATE my_circle SET n_status = 1
					WHERE userid = {$this->uid} AND friendid={$uid} AND id={$qData['id']} AND ftype={$ftype} LIMIT 1
					";
					
					$result = $this->apps->query($sql);	
					if($result) return true;
					else return false;
				}else{
					//if really not found, then use insert
					$sql = "
					INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
					VALUES ('{$uid}',{$this->uid},{$ftype},{$groupid},NOW(),1)
					ON DUPLICATE KEY UPDATE groupid = {$groupid}, n_status=1
					";
/* 					$sql = "
					UPDATE my_circle SET groupid = {$groupid} , n_status = 1
					WHERE userid = {$this->uid} AND friendid={$uid} AND id={$oldid} LIMIT 1
					";
 */					$result = $this->apps->query($sql);	
					if($result) return true;
				}
		}else{
		//if not found, re-check other id
			$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND groupid = {$groupid} AND ftype={$ftype} LIMIT 1";
			$qData = $this->apps->fetch($sql);
			if(!$qData) return false;
			if($qData['total']>0){
				//if found, update the status to true
				$sql = "
				UPDATE my_circle SET n_status = 1
				WHERE userid = {$this->uid} AND friendid={$uid} AND id={$qData['id']} AND ftype={$ftype} LIMIT 1
				";
				
				$result = $this->apps->query($sql);	
				if($result) return true;
				else return false;
				
			}else{
				//if really not found, then use insert
				$sql = "
				INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
				VALUES ('{$uid}',{$this->uid},{$ftype},{$groupid},NOW(),1)
				ON DUPLICATE KEY UPDATE groupid = {$groupid}, n_status=1
				";
				
				$this->apps->query($sql);
				
				if($this->apps->getLastInsertId()) return true;
				else return false;
			}
		}		
		
		return false;
		
	
	}
	
	function deGroupCircleUser(){
		$uid = intval($this->apps->_request('uid'));
		$groupid = intval($this->apps->_request('groupid'));
		//cek friend on circle
		$sql = "SELECT count(*) total FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND groupid={$groupid} LIMIT 1";
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0){
		//if found, use update to move friend
			$sql = "
			UPDATE my_circle SET n_status = 0
			WHERE userid = {$this->uid} AND friendid={$uid} AND groupid={$groupid} LIMIT 1
			";
			$result = $this->apps->query($sql);	
			if($result) return true;
			else return false;
		
		}else return false;
		
		
	
	}
	
	function unAddCircleUser(){
		$uid = intval($this->apps->_request('fid'));
		$groupid = intval($this->apps->_request('groupid'));
		//cek friend on circle
		$sql = "SELECT count(*) total FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype=1 LIMIT 1";
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0){
		//if found, use update to move friend
			$sql = "
			UPDATE my_circle SET n_status = 0
			WHERE userid = {$this->uid} AND friendid={$uid} AND ftype=1
			";
			$result = $this->apps->query($sql);	
			if($result) return true;
			else return false;
		}else return false;
		
		
	
	}
	
	function attending($attendartype=0){
		
		$contentid = intval($this->apps->_request('contentid'));
		if($contentid==0) return false;
		
		if($attendartype!=0) {
			
			//select to my_pages_type as what
			$otherid = 0;
		}else $otherid = $this->uid;
		if($otherid==0) return false;
	
		$sql = "SELECT count(*) total FROM my_contest WHERE otherid={$otherid}  AND  mypagestype={$attendartype} AND contestid={$contentid} LIMIT 1";
			// pr($sql);
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0) return false;
			
		$sql = "INSERT INTO my_contest (contestid,otherid,mypagestype,join_date,n_status) VALUES ({$contentid},{$otherid},{$attendartype},NOW(),1)";

		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) return true;
		return false;
		
	}
	
	function getUserFavorite(){
		
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_request('start'));	
		$limit = 9;
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
				$sql ="
					SELECT contentid FROM {$this->dbshema}_news_content_favorite WHERE n_status=  1 AND userid IN ({$uid})  GROUP BY contentid
					";
		
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					foreach($qData as $val){
						$favoriteData[$val['contentid']]=$val['contentid'];
					}
					
				if(!$favoriteData) return false;
				$strContentId = implode(',',$favoriteData);
				
					$sql = "
						SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid 
						FROM {$this->dbshema}_news_content 
						WHERE AND n_status<>3  AND id IN ({$strContentId}) 
						ORDER BY posted_date DESC , id DESC
						LIMIT {$start},{$limit}";
					
					$rqData = $this->apps->fetch($sql,1);
					if(!$rqData) return false;
					//cek detail image from folder
						//if is article, image banner do not shown
					foreach($rqData as $key => $val){
						if(file_exists("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) $rqData[$key]['banner'] = false;
						else $rqData[$key]['banner'] = true;		
					}
				
				if($rqData) $qData=	$this->getStatistictArticle($rqData);
				
				return $qData;
				}
		}
		return false;
	}
	
	
	function getSearchFriends(){
		$limit = 16;
		$start= intval($this->apps->_request('start'));
		$searchKeyOn = array("name","email","last_name");
		$keywords = strip_tags($this->apps->_p('keywords'));	
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
		$sql = "SELECT count(*) total FROM social_member WHERE n_status =1  {$qKeywords} ORDER BY name ASC ";
		$total = $this->apps->fetch($sql);
		if(!$total) return false;
		
		$sql = "SELECT id,name,img,email,IF(last_name IS NULL,'',last_name) last_name FROM social_member WHERE n_status =1  {$qKeywords} ORDER BY name ASC, last_name ASC LIMIT {$start},{$limit}";
		$qData = $this->apps->fetch($sql,1);
	
		if(!$qData) return false;
		foreach($qData as $key => $val){
			$arrFriends[$val['id']] = $val['id']; 
		}
		// search friends
		if(!$arrFriends) return false;
		$strFriends = implode(',',$arrFriends);
		$friendsData = $this->isFriends($strFriends,true);
		$arrFriends = false;
		if($friendsData){
			foreach($friendsData as $val){
				$arrFriends[$val['friendid']] = $val['friendid'];
			}
		}
		foreach($qData as $key => $val){
			$qData[$key]['isFriends'] =false;
			if($arrFriends) {
				if(array_key_exists($val['id'],$arrFriends))$qData[$key]['isFriends'] = true;
			}
			
		}
		if($qData){
			$data['result'] = $qData;
			$data['total'] = $total['total'];
			$data['myid'] = intval($this->uid);
		}
		return $data;
		
	}
	
	
	function socialdata($strsocialfid=false){
			
			if(!$strsocialfid)return false;
			global $CONFIG;
					//get friend detail
			$sql =	" SELECT id,name,img,sex,last_name FROM social_member WHERE id IN ({$strsocialfid}) AND  n_status = 1  ";
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $key => $val){
			
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
			}
			return $qData;
			
	}
	
	function entouragedata($strentouragefid=false){
			
			if(!$strentouragefid)return false;
				global $CONFIG;

			//get friend detail
			$sql =	" SELECT id,name,img,sex,last_name FROM my_entourage WHERE id IN ({$strentouragefid}) AND referrerbybrand = {$this->uid} AND  n_status = 1  ";
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $key => $val){
					if(is_file($CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'])) $qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'];
					else  $qData[$key]['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
				
			}
			return $qData;
			
	}
	
	function changepassword(){
		
		$oldpass = strip_tags($this->apps->_p('oldpass'));
		$newpass = strip_tags($this->apps->_p('newpass'));
		$confirmnewpass = strip_tags($this->apps->_p('confirmnewpass'));
		
		if($newpass!=$confirmnewpass) return false;
				
		$sql = "SELECT * FROM social_member WHERE id={$this->uid} LIMIT 1";
		// pr($sql);exit;
		$rs = $this->apps->fetch($sql);
		if(!$rs) return false;
		
		$oldhashpass = sha1($oldpass.$rs['salt']);
		
		if($oldhashpass!=$rs['password']) return false;
			
		$hashpass = sha1($newpass.$rs['salt']);
				
		$sql ="UPDATE social_member SET password='{$hashpass}' WHERE id={$this->uid} LIMIT 1";
		$rs = $this->apps->query($sql);
		// pr($sql);exit;
		if($rs){
			$sql ="UPDATE social_member SET last_login=now(),login_count=login_count+1 WHERE id={$this->uid} LIMIT 1";
			$rs = $this->apps->query($sql);
			// pr($sql);exit;
			return true;
		}
		
		return false;
		
	}
	
	
	
}

?>

