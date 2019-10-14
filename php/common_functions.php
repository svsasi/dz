<?php

error_reporting(0);

require_once('db_config.php');

function getMerchantEnvironment($mid){

	global $db;

	$query = "SELECT environment

				FROM merchant_processors_mid 

				WHERE merchant_id = ".$mid;

	
	$merchant_processors = $db->rawQuery($query);



	return $merchant_processors;



}

function getMonthlyTransactionData($month) {

	global $db;

	$db->where("id",$_SESSION['iid']);

	$data = $db->getOne("users");

	$mid = $data['merchant_id'];

	$thisyear = date("Y"); 

	//$thisyear = "2014"; 

	$month = (count($month) == 1)?"0".$month:$month;

	$query = "SELECT currency, SUM(if(currency = 'USD', amount, 0)) AS total, 

				 COUNT(if(currency = 'USD', amount, NULL)) AS num FROM transactions t 

			INNER JOIN actions a ON a.id_transaction_id = t.id_transaction_id

			WHERE merchant_id = ".$mid." AND action_type = 'sale' 

			AND DATE_FORMAT(transaction_date, '%Y-%m') = '".$thisyear."-".$month."'";

	 $data = $db->rawQuery($query);		

	 return $data[0]['num'];

	}

function getMonthlyChagebacksData($month) {

	global $db;

	$db->where("id",$_SESSION['iid']);

	$data = $db->getOne("users");

	$mid = $data['merchant_id'];

	$thisyear = date("Y"); 

	//$thisyear = "2013"; 

	$month = (count($month) == 1)?"0".$month:$month;

	$query = "SELECT COUNT(idchargebacks) AS num 

	FROM chargebacks 

	WHERE m_id = ".$mid." 

	AND DATE_FORMAT(cb_date, '%Y-%m') = '".$thisyear."-".$month."'";

	 $data = $db->rawQuery($query);		

	 return $data[0]['num'];

}

function getUserMerchants($iid){

global $db;

	$db->where("id",$iid);

	$data = $db->getOne("users");

	$user_type = $data['user_type'];

	//if usertype = merchant

	//sql here

	

	//if usertype = agent

	$query = "SELECT DISTINCT(idmerchants), merchant_name, users.agent_id FROM users

	 INNER JOIN merchants ON users.agent_id = merchants.affiliate_id";

	//if usertype = admin

	 if($user_type != 1) {

		$query .= " WHERE users.id = ".$iid; 

	}

	$agent_merchants = $db->rawQuery($query);

 return $agent_merchants;

}

function getUserAgents($iid){

global $db;

	$db->where("id",$iid);

	$data = $db->getOne("users");

	$user_type = $data['user_type'];

	//if usertype = merchant

	//sql here

	

	//if usertype = agent

	$query = "SELECT DISTINCT(idagents), agentname, users.agent_id FROM users

	 INNER JOIN agents ON users.agent_id = agents.idagents";

	//if usertype = admin

	 if($user_type != 1) {

		$query .= " WHERE users.id = ".$iid; 

	}

	$agent_merchants = $db->rawQuery($query);

 return $agent_merchants;

}

function getUserPermissions($user_type){

	switch ($user_type) {

		case 1:

			$user_permitions = "M A C F R S B V";

			break;

		case 2:

			$user_permitions = "M A F R S";

			break;

		case 3:

			$user_permitions = "M A R S";

			break;

		case 4:

			$user_permitions = "R B V";

			break;

		case 5:

			$user_permitions = "R S B V";

			break;

		case 6:

			$user_permitions = "V";

			break;

		case 7:

			$user_permitions = "M A C F R S B";

			break;

		default:

			$user_permitions = "";

			break;

	}

	return $user_permitions;

}

function getUserType($id){

global $db;

	$db->where("id",$id);

    $data = $db->getOne("users");

	return $data['user_type'];

}

function getUserdata($userid){

global $db;

	$db->where("id",$userid);

    $data = $db->getOne("users");

	return $data;

}

function getAgentsofUser($agent_id){

global $db;

	$db->where("agent_id",$agent_id);

	$db->orderBy("username","Asc");

    $data = $db->get("users");

	return $data;

}

function getAgentsofAUser($agent_id){

global $db;

	$db->where("agent_id", NULL, '<=>');

	$db->orderBy("username","Asc");

    $data = $db->get("users");

	return $data;

}

function getAgentsInfo($agent_id){

global $db;

	$db->where("idagents",$agent_id);

	$db->orderBy("agentname","Asc");

    $data = $db->get("agents");

	return $data;

}

function getAffiliationofAgents($agent_id){

global $db;

	$db->where("affiliation",$agent_id);

	$db->orderBy("agentname","Asc");

    $data = $db->get("agents");

	return $data;

}

function getMerchantsofAgents($agent_id){

global $db;

	$db->where("affiliate_id",$agent_id);

	$db->orderBy("merchant_name","Asc");

    $data = $db->get("merchants");

	return $data;

}

function getUsersofmerchant($merchant_id){

global $db;

	$db->where("merchant_id",$merchant_id);

	$db->orderBy("username","Asc");

    $data = $db->get("users");

	return $data;

}

function getUsersofAdmin(){

global $db;

	//$db->where("merchant_id",$merchant_id);

	$db->orderBy("username","Asc");

    $data = $db->get("users");

	return $data;

}

function getAgentsofAdmin(){

global $db;

	$db->where("affiliation", NULL, '<=>');

	$db->orderBy("agentname","Asc");

    $data = $db->get("agents");

	return $data;

}

function getAgentsAffiliateofAdmin(){

global $db;

	$db->where("affiliation", NULL);

	$db->orderBy("agentname","Asc");

    $data = $db->get("agents");

	return $data;

}

function getMerchantsofAdmin(){

global $db;

	//$db->where("affiliate_id",$agent_id);

	$db->orderBy("merchant_name","Asc");

    $data = $db->get("merchants");

	return $data;

}
function getUsermerchant(){
	
	global $db;
	
	$db->orderBy("username","Asc");

    $data = $db->get("users");

	return $data;
}

function getAllAgents(){

global $db;

	$db->orderBy("agentname","Asc");

    $data = $db->get("agents");

	return $data;

}
function getSmerchant($id){
	global $db;
	
   
	$query = "SELECT idmerchants,merchant_name 

				FROM merchants

				INNER JOIN users ON users.id = merchants.userid 

				WHERE users.id =".$id;"";

	$data = $db->rawQuery($query);	
	

	return $data;
	
}

function getVTMerchantsofAdmin(){

	global $db;

	/*$query = "SELECT DISTINCT(idmerchants), merchant_name  

			  FROM merchant_processors_mid

			  INNER JOIN merchants ON merchant_processors_mid.merchant_id=merchants.idmerchants

			  ORDER BY merchant_name ASC";*/
	$query = "SELECT idmerchants,merchant_name 

				FROM merchants";

				
			 
	$data = $db->rawQuery($query);	
	
	

	return $data;

}

function getUserMerchantName($userid){

	global $db;

	$query = "SELECT merchant_name 

				FROM users

				INNER JOIN merchants ON users.merchant_id = merchants.idmerchants 

				WHERE users.id =".$userid;

	//return $query;

	$data = $db->rawQuery($query);	

	

	return $data[0]["merchant_name"];

}

function getUserMerchantProcessors($mid){

	global $db;

	$query = "SELECT p.p_id as processor_id, p.processor_name as processor_name, g.p_id as gateway_id, g.processor_name as gateway_name

				FROM merchant_processors_mid mpm

				INNER JOIN processors p ON p.p_id = mpm.processor_id

				INNER JOIN processors g ON g.p_id = mpm.gateway_id

				WHERE merchant_id = ".$mid;

	$merchant_processors = $db->rawQuery($query);
	
	

	return $merchant_processors;

}

?>