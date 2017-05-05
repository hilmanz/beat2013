<?php 
global $ENGINE_PATH;
require_once $ENGINE_PATH.'Utility/nuSOAP/nusoap.php';
include_once $ENGINE_PATH."Utility/Debugger.php";
require_once $ENGINE_PATH."Utility/phpseclib/Math/BigInteger.php";
require_once $ENGINE_PATH."Utility/phpseclib/Crypt/Random.php";
require_once $ENGINE_PATH."Utility/phpseclib/Crypt/Hash.php";
require_once $ENGINE_PATH."Utility/phpseclib/Crypt/RSA.php";

class MOPClient extends SQLData{
	var $View;
	var $Config;
	var $client; //NuSOAP client
	var $msg;
	var $session;
	var $pageRef;
	
	function MOPClient($req,$forceDebug=1){
		parent::SQLData();
		$this->Request = $req;
		$this->forceDebug = $forceDebug;
		$this->View = new BasicView();
	
	}	
	function getConfig(){
		$this->open();
		$rs = $this->fetch("SELECT * FROM mop_config",1);
		for($i=0;$i<sizeof($rs);$i++){
			$list[$rs[$i]['configName']] = $rs[$i]['configValue'];
		}
		//print_r($list);
		$this->close();
			return $list;
	}
	
	function curlGet($req_url){
	
		/**
		* Initialize the cURL session
		*/
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL,$req_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); 
		curl_setopt($ch, CURLOPT_USERPWD, "hosting\pmimopID:Pm1jkd!");		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		$response = curl_exec ($ch);
		$info = curl_getinfo($ch);
		curl_close ($ch);
		return $response;
	
	}
	
	function curlPOST($url,$params="",$timeout=15,$header=true){
		
		global $CONFIG;
	
		$data_string = http_build_query($params);		
		$ch = curl_init($url);                 
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);           
		curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERPWD, "hosting\pmimopID:Pm1jkd!");	
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec ($ch);
		$info = curl_getinfo($ch);		
		curl_close ($ch);
		return $response;
		
	}
	
	function curlXml($url,$param=false,$credential=false,$header=true){
		
		global $CONFIG;
		if(!$param) return false;
		if(!$credential) return false;
		
		if($header){
			
			$soapAction = $param['soapAction'];
			$soapRequest = $param['soapRequest'];
			if(array_key_exists("xml",$param)) $xml = $param['xml'];
			else $xml = "";
			
			$privatekey = file_get_contents($CONFIG['LOCAL_ASSET'].'key/PublicKey.xml');
			$rsa = new Crypt_RSA();
			$rsa->loadKey($privatekey);
			$rsa->setPublicKey();
			$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
			$encryptedeviceid = $rsa->encrypt($credential['serialNumber']);
			$headers =  base64_encode($encryptedeviceid);
		}
				
		$url = "https://staging-aws-mop-id.es-dm.com/dm.mop.admin.webservice/centralAdminwebservice.asmx";
				
		$rawxml ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
		$rawxml .="<soap:Header>";
		$rawxml .='<SecurityToken xmlns="http://tempuri.org/"><token>'.$headers.'</token></SecurityToken>';
		$rawxml .="</soap:Header>";
		$rawxml .="<soap:Body>";
		$rawxml .='<'.$soapAction.' xmlns="http://tempuri.org/">';
		$rawxml .= $soapRequest;
		$rawxml .="</".$soapAction.">";
		$rawxml .="</soap:Body>";
		$rawxml .="</soap:Envelope>";
		// echo $rawxml;exit;
		$curlheader = array(             
				"Content-type: text/xml;charset=\"utf-8\"", 
				"Accept: text/xml",
				"SOAPAction: \"".$soapAction."\"",				
				"SecurityToken: ".$headers."",		
				"xml: ".$xml."",		
				"Content-length: ".strlen($rawxml).""				
			); 
	
		$ch = curl_init($url);  	
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");   
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheader );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $rawxml);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);			
		$response = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);		
		$body = substr($response, $header_size);		
		curl_close ($ch);
		// ob_clean();
		// header("Content-type: text/xml; charset=utf-8");
		// echo $response;
		// exit;
		return $response;
		
	}
	
	function checkReferral($url=null,$sessid=false,$credential=false){
		
		global $CONFIG;
		if($sessid==false) return false;
		if($credential==false) return false;
				
		if($url==null) $url = $CONFIG['MOP_URL'];
		
		$data['soapAction'] = "AdminCheckReferral";
		$data['soapRequest'] = "<sessionID>".$sessid."</sessionID>";
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;

	}

	function CheckLogin($url=null,$username=false,$pwd=false,$credential=false){
		
		global $CONFIG;

		if($username==null) return false;
		if($pwd==null) return false;
		if($credential==null) return false;
		
		if($url==null) $url = $CONFIG['MOP_URL'];
		
		$xmlformat = "<userName>".$username."</userName>";
		$xmlformat .= "<password>".$pwd."</password>";	
		
		$data['soapAction'] = "AdminCheckLogin";		
		$data['soapRequest'] = $xmlformat;
		
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;
		 

	
	}
	
	function CheckLogin_($url=null,$username=false,$pwd=false,$credential=false){
		
		global $CONFIG;

		if($username==null) return false;
		if($pwd==null) return false;
		if($credential==null) return false;
		
		if($url==null) $url = $CONFIG['MOP_URL'];
		
		$xmlformat = "<userName>".$username."</userName>";
		$xmlformat .= "<password>".$pwd."</password>";	
		
		$data['userName'] = $username;		
		$data['password'] = $pwd;
		
		$response = $this->curlPOST($url."/AdminCheckLogin",$data);
		
		return $response;
		 

	
	}
	
	function AdminEndSession($url=null,$sessionid=null,$credential=false){
		
		global $CONFIG;
	
		if($sessionid==null) return false;
		if($credential==null) return false;
		if($url==null) $url = $CONFIG['MOP_URL'];
		
		$xmlformat = "<sessionID>".$sessionid."</sessionID>";
			
		$data['soapAction'] = "AdminEndSession";		
		$data['soapRequest'] = $xmlformat;
		
		$response = $this->curlXml($url,$data,$credential);
		return $response;
		 

	
	}
	
	function registerAdminUser($type="AdminRegisterProfile",$url=null,$sessid=false,$xml=false,$credential=false){
		
		if($sessid==false) return false;
		if($xml==false) return false;
		global $CONFIG;
		if($credential==null) return false;
		if($url==null) $url = $CONFIG['MOP_URL'];
				
		$xmlformat = "<SessionID>".$sessid."</SessionID>";
		$xmlformat .= "<xml></xml>";
			
		$data['soapAction'] = $type;		
		$data['soapRequest'] = $xmlformat;
		$data['xml'] = $xml;
		
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;

	
	}
	
	
	function getProfileUser($url=null,$sessid=false,$xml=false,$credential=false){
		
		global $CONFIG;
	
		if($credential==null) return false;
		if($sessid==null) return false;
		if($xml==null) return false;
		if($url==null) $url = $CONFIG['MOP_URL'];
				
		$xmlformat = "<sessionID>".$sessid."</sessionID>";
		$xmlformat .= "<xml></xml>";
			
		$data['soapAction'] = "AdminGetProfile";		
		$data['soapRequest'] = $xmlformat;
		$data['xml'] = $xml;
		
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;
		 

	
	}

	function searchProfileUser($url=null,$sessid=false,$xml=false,$credential=false){
		
		global $CONFIG;

		if($credential==null) return false;
		if($sessid==null) return false;
		if($xml==null) return false;
		if($url==null) $url = $CONFIG['MOP_URL'];
				
		$xmlformat = "<SessionID>".$sessid."</SessionID>";
		$xmlformat .= "<xml></xml>";
			
		$data['soapAction'] = "AdminSearchProfile";		
		$data['soapRequest'] = $xmlformat;
		$data['xml'] = $xml;
			
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;
		 

	
	}
			
	function AdminGetProfileonGiid($url=null,$sessid=false,$xml=false,$credential=false){
		
		global $CONFIG;

		if($credential==null) return false;
		if($xml==null) return false;
		if($sessid==null) return false;
		if($url==null) $url = $CONFIG['MOP_URL'];
				
		$xmlformat = "<sessionID>".$sessid."</sessionID>";
		$xmlformat .= "<xml></xml>";
			
		$data['soapAction'] = "AdminGetProfileOnGIID";		
		$data['soapRequest'] = $xmlformat;
		$data['xml'] = $xml;
		
		$response = $this->curlXml($url,$data,$credential);
		
		return $response;
		 

	}	

	
	function registerDeviceAdmin($url=null,$credential=false){
		
		global $CONFIG;
		if($credential==null) return false;		
		if($url==null) $url = $CONFIG['MOP_URL'];
				
			
		$xmlformat ="<userName>".$credential['userName']."</userName>";
		$xmlformat .="<password>".$credential['password']."</password>";
		$xmlformat .="<deviceId>".$credential['deviceId']."</deviceId>";
		$xmlformat .="<deviceDescription>".$credential['deviceDescription']."</deviceDescription>";
		$xmlformat .="<serialNumber>".$credential['serialNumber']."</serialNumber>";
			
		$data['soapAction'] = "AdminRegisterDevice";		
		$data['soapRequest'] = $xmlformat;
		
		$response = $this->curlXml($url,$data,$credential);

		return $response;
		 

	
	}
		
}

?>
