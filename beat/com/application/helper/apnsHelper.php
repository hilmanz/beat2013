<?php 

class apnsHelper  {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbschema = "beat";	
		$this->radius = 100 / 10000;
		$this->dbshema = "beat";
	}
	
	function curlJSON($url,$data,$uname,$pwd){
	$data_string = json_encode($data);
	// pr($data_string);exit;
	$ch = curl_init($url);                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
	curl_setopt($ch, CURLOPT_USERPWD, "$uname:$pwd");		
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	return array("response"=>$result,"info"=>$info);
	}

	function pushnotification(){
			// '{"device_tokens": ["<token>"], "aps": {"alert": "Hello!"}}' \
			// App Key : ZitLOOKcQKC3xhaMiMM-kA
			// App Secret : HPrj3kNLQnu0PJleNwppEQ
			// App Master Secret : Hlv0MWdDSDuzxPYhPyShpw
			
					
			$uname = "ZitLOOKcQKC3xhaMiMM-kA";
			$pwd = "Hlv0MWdDSDuzxPYhPyShpw";
			$notification = $this->apps->activityHelper->getA360activity(0,10,false,false,false,'3',true);	
			$data['device_tokens'] = strip_tags($this->apps->_p("devicetoken"));
			$data['aps'] = $notification['content'];
			$url = "https://go.urbanairship.com/api/push/";
			return $this->curlJSON($url,$data,$uname,$pwd);
			
			
	}
	
	
}

?>

