<?php

include('./init.php');
	

$iid = $_SESSION['iid'];
$user_type = getUserType($iid);

//user type
if($user_type != 1) {
	$db->join("agents", "agents.idagents = users.agent_id", "LEFT");
	$db->where("id",$iid);
	$data = $db->getOne("users");
	
	$user_type = $data['user_type'];
	
	$user_agent 		=  $data["agent_id"];
	$user_agent_name 	= $data["agentname"];
	//user merchants
	// $query = "SELECT DISTINCT(idmerchants), merchant_name, users.agent_id FROM users
	// 		  INNER JOIN merchants ON users.agent_id = merchants.affiliate_id";
	$query = "SELECT DISTINCT(idmerchants), merchant_name, users.agent_id FROM users
			  INNER JOIN merchants ON users.merchant_id = merchants.idmerchants";
	$query .= " WHERE users.id = ".$iid;	
	$user_merchants = $db->rawQuery($query);

	//user agents
	$user_subagents = getAffiliationofAgents($user_agent);
} else {
	$user_subagents = $db->get("agents");
	$user_merchants = $db->get("merchants");
}

//user processors
$query = "SELECT DISTINCT(processor_id) as processor_id, processors.processor_name 
			FROM agent_bank_fees 
			INNER JOIN processors ON processors.p_id = agent_bank_fees.processor_id
			WHERE 1=1 ";


			
if($user_type != 1) {
	$db->where("id",$iid);
	$data = $db->getOne("users");
	if(($data['agent_id'])> 0)
	{
		$a_id = $data['agent_id'];
	} else {
		$mid = $data['merchant_id'];
		$db->where("idmerchants",$mid);
		$data = $db->getOne("merchants");
		$a_id = $data['affiliate_id'];
	}
	$query .= " AND agent_id = ".$a_id;
	$user_processors = $db->rawQuery($query);
	
	
}	else {
	$db->where("gateway_or_bank", 0);
	$user_processors = $db->get("processors");
	//return $user_processors;
}



?>