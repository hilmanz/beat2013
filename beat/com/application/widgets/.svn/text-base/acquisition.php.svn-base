<?php
class acquisition{
	
	function __construct($apps=null){
		$this->apps = $apps;	
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){
		// $start = intval($this->_p('start'));
		$entourage = $this->apps->entourageHelper->getEntourage(null,0,5,$all=true);
		
		if($entourage){	
	
			foreach($entourage['result'] as $val){
				$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
				$dataentourage['datetimes'][]=$datetimes;				
				$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage[$val['n_status']][$datetimes]+1;
				
				
			 }
		 pr($dataentourage);
		 // pr($entourage);
		
		// $data = $dataentourage['data'];
		
		
		// foreach ($data as $key => $val) {
			 // $data[] = ($key);
			 // }
			
	
		
		// $this->apps->View->assign('entourage',$entourage['result']);
		$this->apps->View->assign('entourage', json_encode($dataentourage));
		}else  $this->View->assign('total',0);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/acquisition-chart.html");
	}
	
}
?>