<?php
global $APP_PATH;
include_once $APP_PATH."/MOP/MOPClient_Admin.php";

class deviceMopHelper {
	var $mopClient;

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		$this->mopClient =  new MOPClient(null);
		$this->config = $CONFIG;
		$this->uid= 0;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);					
		}
		$this->mopurl = "https://staging-aws-mop-id.es-dm.com/dm.mop.admin.webservice/centralAdminwebservice.asmx";
		
	}
	
	function getCredential(){
			
		$username = "BEATServer";
		$password = "Beat123";
		$deviceid = "BEAT Device ID";
		$devicedesc = "Test Device";
		$serialnumber = "00000000-0000-000-00-0-BEAT01";
		
		$data['userName'] = $username;
		$data['password'] = $password;
		$data['deviceId'] = $deviceid;
		$data['deviceDescription'] = $devicedesc;
		$data['serialNumber'] = $serialnumber;
		
		return $data;
	}
	
	function loginAdminMop($data=false){
	
		
		$credential = $this->getCredential();
		
		$url =$this->mopurl;
		
		$xml= $this->mopClient->CheckLogin($url,$credential['userName'],$credential['password'],$credential);
				
		if($xml){
			$this->getmopdevicedata($credential['userName'],$credential['password']);
			$moparray = $this->XMLtoArray($xml);
			$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["ADMINCHECKLOGINRESPONSE"]["ADMINCHECKLOGIN"];
			$session_mop =(object) array();
			foreach($bigindexarray as $key => $val){
				$mykey = strtolower($key);
				$session_mop->$mykey = $val;
			}
			// pr($session_mop);exit;
			$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$session_mop);
		}else{
			$session_mop = false;
			$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",0);
		}
		return $session_mop;
	}
	
	
	function getmopdevicedata($username=null,$password=null){
		
		$sql ="INSERT INTO `social_device_member` 
		(`deviceid`, `password`, `userid`, `update_time`, `n_status`) 
		VALUES ('{$username}', '{$password}', '{$this->uid}', NOW(), '1')
		ON DUPLICATE KEY UPDATE  password='{$password}',update_time=NOW(),userid='{$this->uid}'
		";
		
		$rs = $this->apps->query($sql);
		
	}
	
	function checkReferralMop(){
		
	
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;

		if($session){
			$sessionid = $session->sessionid;
					
			$url =$this->mopurl;
			$xml= $this->mopClient->checkReferral($url,$sessionid,$credential);
			if($xml){
				$moparray = $this->XMLtoArray($xml);
				$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["ADMINCHECKREFERRALRESPONSE"]["ADMINCHECKREFERRAL"];
				$session_mop =(object) array();
				foreach($bigindexarray as $key => $val){
					$mykey = strtolower($key);
					$session_mop->$mykey = $val;
				}
				$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$session_mop);
			}else{
				$session_mop = false;
				$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",0);
			}
			return $session_mop;
		}return false;

	}
	
	
	function AdminEndSession(){
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;

		if($session){
			$sessionid = $session->sessionid;
			
			$url =$this->mopurl;
			$xml= $this->mopClient->AdminEndSession($url,$sessionid,$credential);
			if($xml){
				$moparray = $this->XMLtoArray($xml);
				$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["ADMINENDSESSIONRESPONSE"]["ADMINENDSESSION"];
				$session_mop =(object) array();
				foreach($bigindexarray as $key => $val){
					$mykey = strtolower($key);
					$session_mop->$mykey = $val;
				}
				
			}else{
				$session_mop = false;
				
			}
			return $session_mop;
		}return false;

	}
	
	function syncAdminUserRegistrant($type="AdminRegisterProfile"){
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		// $session = $this->apps->_request('sessionid') ;	
		
		if($session){
			$sessionid = $session->sessionid;
			// $sessionid = $session;
			$xml = $this->xmlRegisterProfile($type);
		
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->registerAdminUser($type,$url,$sessionid,$xml,$credential);
			// echo $xml;exit;
			if($xml){
				$moparray = $this->XMLtoArray($xml);
				$bigtypeindexname = strtoupper($type);
				$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["{$bigtypeindexname}RESPONSE"][$bigtypeindexname];
				$datamop =(object) array();
				foreach($bigindexarray as $key => $val){
					$mykey = strtolower($key);
					$datamop->$mykey = $val;
				}
				
				if($datamop->sessionid) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				
				$resdata['result'] =  (string)$datamop->result;
				$resdata['data'] =false;
				if($datamop->profilecollection){
						
						$profiledata = false;
						$n=0;
						foreach($datamop->profilecollection->PROFILE as $key => $mopdata){
										$mopattr = "@attributes";
										$profiledata[$n]["RegistrationID"] = (string)$mopdata["REGISTRATIONID"];
										$profiledata[$n]["ResponseDescription"] = (string)$mopdata["RESPONSEDESCRIPTION"];
								foreach($mopdata->FieldCollection->Field  as $fielddata){
										
										$profiledata[$n][(string)$fielddata["NAME"]] = (string)$fielddata->VALUE;
										$profiledata[$n]["STATUS"] = (string)$fielddata->STATUS;
								}
							$n++;
						}
						$resdata['data'] = $profiledata;
					
				}
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
		
	}
	
	function searchProfileUser(){
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->sessionid;
			
			$xml = $this->searchUserXML();
			
			$url =$this->mopurl;
			$xml= $this->mopClient->searchProfileUser($url,$sessionid,$xml,$credential);
			
			if($xml){
				
				$moparray = $this->XMLtoArray($xml);
				$bigtypeindexname = strtoupper("AdminSearchProfile");
				$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["{$bigtypeindexname}RESPONSE"][$bigtypeindexname];
				$datamop =(object) array();
				foreach($bigindexarray as $key => $val){
					$mykey = strtolower($key);
					$datamop->$mykey = $val;
				}
				if($datamop->sessionid) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				$resdata['result'] =  (string)$datamop->result;
				$resdata['data'] =false;
				$usertype['users'] = 0;
				if($datamop->profilecollection){					
					$profiledata = false;
					$n=0;
					foreach($datamop->profilecollection->PROFILE as $key => $mopdata){
									$mopattr = "@attributes";
									$profiledata[$n]["RegistrationID"] = (string)$mopdata["RegistrationID"];
							foreach($mopdata->FieldCollection->Field  as $fielddata){									
									$profiledata[$n][(string)$fielddata["Name"]] = (string)$fielddata->Value;
							}
						$n++;
					}
					$resdata['data'] = $profiledata;
				
				}
				if($resdata['result']==1)$usertype['users'] = 2;
				$this->apps->session->setSession($this->config['SESSION_NAME'],'USERTYPE',$usertype);
				
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
	}
	
	function AdminGetProfileonGiid(){		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->sessionid;
			
			$xml = $this->searchgiidxml();
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->AdminGetProfileonGiid($url,$sessionid,$xml,$credential);
			if($xml){
				$moparray = $this->XMLtoArray($xml);
				$bigtypeindexname = strtoupper("AdminGetProfileonGiid");
				$bigindexarray = $moparray["SOAP:ENVELOPE"]["SOAP:BODY"]["{$bigtypeindexname}RESPONSE"][$bigtypeindexname];
				$datamop =(object) array();
				foreach($bigindexarray as $key => $val){
					$mykey = strtolower($key);
					$datamop->$mykey = $val;
				}
					if($datamop->sessionid) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				$resdata['result'] =  (string)$datamop->Result;
				$resdata['data'] =false;
               $usertype['users'] = 0;
				if($datamop->ProfileCollection){
					$profiledata = false;
					$n=0;
					foreach($datamop->ProfileCollection->Profile as $key => $mopdata){
									$mopattr = "@attributes";
									$profiledata[$n]["RegistrationID"] = (string)$mopdata["RegistrationID"];
							if($mopdata->FieldCollection->Field){
								foreach($mopdata->FieldCollection->Field  as $fielddata){									
										$profiledata[$n][(string)$fielddata["Name"]] = (string)$fielddata->Value;
								}
							}
					$n++;	
					}
					$resdata['data'] = $profiledata;
				}
					if($resdata['result']==1) $usertype['users'] = 2;
				
					$this->apps->session->setSession($this->config['SESSION_NAME'],'USERTYPE',$usertype);
				
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
	}
	
	function getProfileUser(){
	
		$credential = $this->getCredential();
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->sessionid;
			$xml = $this->getProfileUserXML();
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->getProfileUser($url,$sessionid,$xml,$credential);
			if($xml){
				$session_mop=	simplexml_load_string($xml);
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
		
	}

	
	function registerDeviceAdmin(){
		 
		$credential = $this->getCredential();
		
		$url =$this->mopurl;
		$xml= $this->mopClient->registerDeviceAdmin($url,$credential);
		echo $xml;exit;
		if($xml){
			$session_mop=simplexml_load_string($xml);
		}else{
			$session_mop = false;
		}
		return $session_mop;
		
	}
	
	
	function xmlRegisterProfile($type=false){
		
		
		$Campaign = "ID1300DS2AWS";
		$Phase = "PH01";
		$Audience = "A001";
		$MediaCategory = "OBW";
		$OfferCode = "WEB088";
		$OfferCategory = "WEB";
		
		$firstname=$this->apps->_request("name");
		$registerid=intval($this->apps->_request("registerid"));
		$lastname=$this->apps->_request("lastname");
		$nickname=$this->apps->_request("nickname");
		$email=$this->apps->_request("email");
		$img=$this->apps->_request("img");
		$small_img=$this->apps->_request("small_img");
		$city=$this->apps->_request("city");
		$state=$this->apps->_request("state");
		$giidnumber=$this->apps->_request("giidnumber");
		$giidtype=$this->apps->_request("giidtype");
		$companymobile=$this->apps->_request("companymobile");
		$sex=$this->apps->_request("sex");
		$birthday=$this->apps->_request("birthday");
		$description=$this->apps->_request("description");
		$phone_number=$this->apps->_request("phone_number");
		
		$brand1=$this->apps->_request("Brand1_ID");
		$brandsub1=$this->apps->_request("Brand1SUB_ID");
		
		$ctypes=$this->apps->_request("ctypes");
		$ctypeu=$this->apps->_request("ctypeu");
	
		// $referrerbybrand = $this->uid; /* use on segment 8  */
			$usertype=intval(@$this->apps->session->getSession($this->config['SESSION_NAME'],'USERTYPE')->users);
			
		$confirm18=1;
		$receiveinfo=1;
		$n_status=1;
		$verified = 1;
			
		$data = '<ProfileCollection>';
		if($registerid!=0) $data .= '<Profile ID="" RegistrationID="'.$registerid.'" >';
		else $data .= '<Profile ID="">';
		$data .= '<FieldCollection>';
		if($registerid==0) $data .= '<Field Name="FirstName"><Value>'.$firstname.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="LastName"><Value>'.$lastname.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="DateOfBirth"><Value>'.$birthday.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="Gender"><Value>'.$sex.'</Value></Field>';
		if($email)$data .= '<Field Name="Email"><Value>'.$email.'</Value></Field>';
		if($phone_number)$data .= '<Field Name="Mobile"><Value>'.$phone_number.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="State"><Value>'.$state.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="City"><Value>'.$city.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="GIIDNumber"><Value>'.$giidnumber.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="GIIDType"><Value>'.$giidtype.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="CompanyMobile"><Value>'.$companymobile.'</Value></Field>';
		if($brand1)$data .= '<Field Name="SmokingPrefBrand1"><Value>'.$brand1.'</Value></Field>';
		if($brandsub1)$data .= '<Field Name="SmokingPrefSubBrand1"><Value>'.$brandsub1.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="ConfirmAbove18"><Value>'.$confirm18.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="ReceiveInfo"><Value>'.$receiveinfo.'</Value></Field>';
		
		if($ctypeu||$ctypes){
			if($ctypeu=="on"){
				$data .= '<Field Name="UCampaign"><Value>'.$Campaign.'</Value></Field>';
				$data .= '<Field Name="UPhase"><Value>'.$Phase.'</Value></Field>';
				$data .= '<Field Name="UAudience"><Value>'.$Audience.'</Value></Field>';
				$data .= '<Field Name="UMediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				$data .= '<Field Name="UOfferCode"><Value>'.$OfferCode.'</Value></Field>';
				$data .= '<Field Name="UOfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
			if($ctypes=="on"){
				if($registerid==0)$data .= '<Field Name="Campaign"><Value>'.$Campaign.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Phase"><Value>'.$Phase.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Audience"><Value>'.$Audience.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="MediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCode"><Value>'.$OfferCode.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
		}else{
		
			if($type=="AdminRegisterProfileDeDuplication" ){
				$data .= '<Field Name="UCampaign"><Value>'.$Campaign.'</Value></Field>';
				$data .= '<Field Name="UPhase"><Value>'.$Phase.'</Value></Field>';
				$data .= '<Field Name="UAudience"><Value>'.$Audience.'</Value></Field>';
				$data .= '<Field Name="UMediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				$data .= '<Field Name="UOfferCode"><Value>'.$OfferCode.'</Value></Field>';
				$data .= '<Field Name="UOfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
			if( $type=="AdminRegisterProfileDeDuplication" || $type=="AdminRegisterProfile"  || $type=="AdminUpdateProfile"){
				if($registerid==0)$data .= '<Field Name="Campaign"><Value>'.$Campaign.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Phase"><Value>'.$Phase.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Audience"><Value>'.$Audience.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="MediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCode"><Value>'.$OfferCode.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
		}
		
		$data .= '</FieldCollection>';
		$data .= '</Profile>';
		$data .= '</ProfileCollection>';
	// print $data;exit;
	return $data;
	}
	
	
	
	function searchUserXML(){
		
		$email = $this->apps->_request("email");
		$giidnumber = $this->apps->_request("giidnumber");
		
		$data = '<ProfileCollection>';
		$data .= '<Profile ID="" RegistrationID="">';
		if($email!='') $data .= '<FieldCollection><Field Name="Email"><Value>'.$email.'*</Value></Field></FieldCollection>';
		if($giidnumber!='') $data .= '<FieldCollection><Field Name="GIIDNumber"><Value>'.$giidnumber.'*</Value></Field></FieldCollection>';
		$data .= '</Profile></ProfileCollection>';

		return $data;
	}
	
	
	
	function getProfileUserXML(){
		$registerid = intval($this->apps->_request("registerid"));
		if($registerid==0) return false;
		$data = '<ProfileCollection><Profile ID="" RegistrationID="'.$registerid.'" ResponseCode=""></Profile></ProfileCollection>';
		return $data;
	}
	
	function searchgiidxml(){
		
		$giidnumber = $this->apps->_request("giidnumber");
		
		$data = '<ProfileCollection>';
		$data .= '<Profile GIIDNumber="'.$giidnumber.'" ResponseCode="" />';
		$data .= '</ProfileCollection>';
		// print $data;exit;
		return $data;
	}
	
	function XMLtoArray($XML)
{
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
  
    $_tmp='';
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_level!=1 && $x_type == 'close') {
            if (isset($multi_key[$x_tag][$x_level]))
                $multi_key[$x_tag][$x_level]=1;
            else
                $multi_key[$x_tag][$x_level]=0;
        }
        if ($x_level!=1 && $x_type == 'complete') {
            if ($_tmp==$x_tag)
                $multi_key[$x_tag][$x_level]=1;
            $_tmp=$x_tag;
        }
    }
   
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_type == 'open')
            $level[$x_level] = $x_tag;
        $start_level = 1;
        $php_stmt = '$xml_array';
        if ($x_type=='close' && $x_level!=1)
            $multi_key[$x_tag][$x_level]++;
        while ($start_level < $x_level) {
            $php_stmt .= '[$level['.$start_level.']]';
            if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
            $start_level++;
        }
        $add='';
        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
            if (!isset($multi_key2[$x_tag][$x_level]))
                $multi_key2[$x_tag][$x_level]=0;
            else
                $multi_key2[$x_tag][$x_level]++;
            $add='['.$multi_key2[$x_tag][$x_level].']';
        }
        if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
            if ($x_type == 'open')
                $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
            else
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
            eval($php_stmt_main);
        }
        if (array_key_exists('attributes', $xml_elem)) {
            if (isset($xml_elem['value'])) {
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            foreach ($xml_elem['attributes'] as $key=>$value) {
                $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                eval($php_stmt_att);
            }
        }
    }
    return $xml_array;
}
}
?>
