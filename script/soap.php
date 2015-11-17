<?php

class soap {
	var $client;
	
	function __construct() {
		$this->client = new SoapClient(  
						"http://erp-prd.ifobl.if.energy.gov.ua:8000/sap/bc/srt/wsdl/bndg_E3581699EA116CF1AF0D005056C00008/wsdl11/allinone/standard/document?sap-client=500",
			array('login' => 'ws_user', 'password' => 'Web-serv1ces', 'trace' => true)
		);
	}

// пошук серед всіх працівників (json)
	function search_client($prizv, $tab) {	
		$rez = array('LastnameM' => $prizv, 'EmployeeId' => $tab, 'OutTab' => array());
		$rezult = $this->client->ZsearchPerson($rez);
		if(count($rezult->OutTab->item)=='') {
			echo json_encode(array());
		}
		if(count($rezult->OutTab->item)==1) {
			//echo '['.json_encode($rezult->OutTab->item).']';
			$json = json_encode($rezult->OutTab->item);
			echo '['.str_replace("0000", "", $json).']';
		} 
		if(count($rezult->OutTab->item)>1) {
			//echo json_encode($rezult->OutTab->item);
			$json = json_encode($rezult->OutTab->item);
			echo str_replace("0000", "", $json);
		}
	}
}	

$object = new soap;
if($_POST['task'] == "search_user") {
	$object->search_client($_POST['user']."*", '');
}
?>