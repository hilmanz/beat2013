<?php
class mop  extends ServiceAPI{
	
	function beforeFilter(){
		$this->deviceMopHelper= $this->useHelper('deviceMopHelper');
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		

	}
	
	function login(){
		
		$user = $this->deviceMopHelper->loginAdminMop(); 
		return $user;
		
	}
	
	function endsessionmop(){
		$user = $this->deviceMopHelper->AdminEndSession(); 
		return $user;
		
	}
	
	function checkreferral(){
		$user = $this->deviceMopHelper->checkReferralMop(); 
		return $user;
		
	}
	
	function registeruser(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfile"); 
		return $user;
		
	}
	
	function changeuserprofile(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminUpdateProfile"); 
		return $user;
		
	}
	
	function registeruserdeduplicate(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication"); 
		return $user;
		
	}
	
	function searchuser(){
		$user = $this->deviceMopHelper->searchProfileUser(); 
		return $user;
		
	}
	
	function searchusergiid(){
		$user = $this->deviceMopHelper->AdminGetProfileonGiid(); 
		return $user;
		
	}
	function getuser(){
		$user = $this->deviceMopHelper->getProfileUser(); 
		return $user;
		
	}
	
	function registerdevice(){
		$user = $this->deviceMopHelper->registerDeviceAdmin(); 
		return $user;
		
	}
	
	function secrettoken(){
	
			global $CONFIG,$APP_PATH,$ENGINE_PATH;
			
			require_once '/var/www/beat/engines/Utility/nuSOAP/nusoap.php';
			require_once "/var/www/beat/engines/Utility/phpseclib/Crypt/Random.php";
			require_once "/var/www/beat/engines/Utility/phpseclib/Crypt/Hash.php";
			require_once "/var/www/beat/engines/Utility/phpseclib/Math/BigInteger.php";
			require_once "/var/www/beat/engines/Utility/phpseclib/Crypt/RSA.php";
		
			
			$username = "ndianlira";
			$password = "ndianlir";
			$deviceid = $this->_p('deviceid');		
			$devicedesc =  $this->_p('devicedesc');
			$serialnumber = $this->_p('serialnumber');
		
			$privatekey = file_get_contents($CONFIG['LOCAL_ASSET'].'key/PublicKey.xml');
			$rsa = new Crypt_RSA();
			$rsa->loadKey($privatekey);
			$rsa->setPublicKey();
			$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
			$encryptedeviceid = $rsa->encrypt($serialnumber);
			$headers =  base64_encode($encryptedeviceid);			
			return array('usethis' => $headers);
	}
	
	
}
?>