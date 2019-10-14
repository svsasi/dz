<?php

include('./init.php');
// require_once('common_functions.php');



$iid = $_SESSION['iid'];

$user_users = array();

$db->where("id",$iid);

$user = $db->getOne("users");




$the_merchant =  $user["merchant_id"];

$the_agent =  $user["agent_id"];




//get all merchant users	

if($the_merchant != "" &&  $the_merchant != NULL )

{

	$db->where("merchant_id",$the_merchant);

	$db->orderBy("username","Asc");

	$merchant_users = $db->get("users");

	foreach($merchant_users as $merchant_user) {

		$user_users[] = array(

					"id" 			=> $merchant_user["id"],

					"username" 		=> $merchant_user["username"],

					"first_name" 	=> $merchant_user["first_name"],

					"last_name"	 	=> $merchant_user["last_name"],

					"user_type"	 	=> $merchant_user["user_type"]

					);

	}

}



//get all agent users

// function getUserByAffiliation3($db, $the_agent)

// {

// 	$db->where("agent_id",$the_agent);

// 	$db->orderBy("username","Asc");

// 	$agent_users = $db->get("users");

// 	foreach($agent_users as $agent_user) {

// 		$user_users[] = array(

// 					"id" 			=> $agent_user["id"],

// 					"username" 		=> $agent_user["username"],

// 					"first_name" 	=> $agent_user["first_name"],

// 					"last_name"	 	=> $agent_user["last_name"],

// 					"user_type"	 	=> $agent_user["user_type"]

// 					);

// 	}

	

// 	$db->where("idagents",$the_agent);

// 	$data = $db->getOne("agents");

// 	$affiliation = $data['affiliation'];

// 	if($affiliation != "" &&  $affiliation != NULL )

// 	{

// 		$user_users[] = getUserByAffiliation3($db, $affiliation);

// 	}

// 	return $user_users;

// }





if($the_agent != "" &&  $the_agent != NULL )

{

	$user_users[] = getUserByAffiliation3($db, $the_agent);

}

//get usersofuser



// var_dump($_SESSION['iid']);

$userdata = getUserdata2($_SESSION['iid']);


// echo $_SESSION['iid'];
// echo "<br>";
// print_r($userdata);
// die();

if($userdata['user_type'] == 1){

	// $usersofuser= array();

	$usersofuser = getUsersofAdmin();

	

} else {


	//$usersofuser = $AgentsofUser;

	$usersofuser = $user_users[0];

	// echo $user_users[0];
	
}

// function getUsersofAdmin(){

// 	global $db;

// 	//$db->where("merchant_id",$merchant_id);

// 	$db->orderBy("username","Asc");

//     $data = $db->get("users");

// 	return $data;

// }

// function getUserPermissions($user_type){

// 	switch ($user_type) {

// 		case 1:

// 			$user_permitions = "M A C F R S B V";

// 			break;

// 		case 2:

// 			$user_permitions = "M A F R S";

// 			break;

// 		case 3:

// 			$user_permitions = "M A R S";

// 			break;

// 		case 4:

// 			$user_permitions = "R B V";

// 			break;

// 		case 5:

// 			$user_permitions = "R S B V";

// 			break;

// 		case 6:

// 			$user_permitions = "V";

// 			break;

// 		case 7:

// 			$user_permitions = "M A C F R S B";

// 			break;

// 		default:

// 			$user_permitions = "";

// 			break;

// 	}

// 	return $user_permitions;

// }
// echo "<pre>";

// 	print_r($usersofuser);

// 	die();


				  // sort alphabetically by name

//usort($usersofuser, 'compare_lastname');

?>