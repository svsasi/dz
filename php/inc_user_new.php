<?php

//get user informetion

$resetPassword = false;

$editUserDetails = true;

$first_name = "";

$last_name = "";

$title = "";

$email = "";

$username = "";

$type = "";

$resetpass = '';

$iiduserdata = getUserDetail($user_id); // getUserdata($iid);

// echo $iid;
// echo "<br>";

if(isset($_POST['from']) && isset($_POST['token'])) {
	echo "<pre>";
	print_r($_POST); exit;
}


$msg = '<span style="color:red">';

if(isset($_GET['userid']) && $_GET['userid'] > 0 ) {

	$userid = sanitizeInput($_GET['userid']);
	$user   = getUserDetail($userid);

	if($user) {
		//security check
		if($usertype != 1){

			$user_users = array();
			// $db->where("id", $iid);
			// $user = $db->getOne("users");

			$the_merchant =  $user["merchant_id"];
			$the_agent    =  $user["agent_id"];

			//get all merchant users	
			if($the_merchant != "" &&  $the_merchant != NULL ) {

				$db->where("merchant_id",$the_merchant);
				$merchant_users = $db->get("users");
				foreach($merchant_users as $merchant_user) {
					$user_users[] = array(
								"id" 			=> $merchant_user["id"],
								"username" 		=> $merchant_user["username"],
								"first_name" 	=> $merchant_user["first_name"],
								"last_name"	 	=> $merchant_user["last_name"]
								);
				}
			}

			if($the_agent != "" &&  $the_agent != NULL ) {
				$user_users[] = getUserByAffiliation($db, $the_agent);
			}
			$security = true;

			foreach($user_users[0] as $uu) {
				if($uu['id'] == $userid) {
					$security = false;
				}
			}

			if($security) {
				$msg .= ' You are not authorized to edit this account.';
				die(' You are not authorized to edit this account.');
			}
		}

		$userlang = 'Edit User';

		$userinfo = getUserDetail($userid);
		if ($userinfo) {
			switch ($userinfo['user_type']) {
				case '1': //Master Administrator
					$userinfo['user_roles'] = ['M','A','C','F','R','S','B','V','U'];
					break;
				case '2': //Agent Administrator
					$userinfo['user_roles'] = ['M','A','F','R','S'];
					break;
				case '3': //Agent
					$userinfo['user_roles'] = ['M','A','R','S'];
					break;
				case '4': //Merchant Administrator
					$userinfo['user_roles'] = ['R','B','V'];
					break;
				case '5': //Merchant
					$userinfo['user_roles'] = ['R','S','B','V'];
					break;
				case '6': //Merchant CSR
					$userinfo['user_roles'] = ['V'];
					break;
				case '7': //Super Agent
					$userinfo['user_roles'] = [];
					break;
				default:
					$userinfo['user_roles'] = [];
					break;
			}
		}
		

		$edituserpage = '<input type="hidden" name="edituserform" id="edituserform" />';

		$resetpass = '<form role="form" id="resetpass" action="" method="post"><input type="hidden" id="reset_pass" name="reset_pass" /><button type="submit" id="resetpass" class="btn btn-md btn-primary pull-right m-t-n-xs"><strong>Reset User Password</strong></button></form>';

		if(isset($_POST['edituserform'])){

			$edituser = edituser($userid, $_POST['first_name'], $_POST['last_name'], $_POST['email']);
			if($edituser) {
				$msg .= ' The User has been edited';
			} else {
				$msg .= ' Failed to update user info';
			}
		}

		if(isset($_POST['reset_pass'])){
				
				$resetPassword = true;

				$editUserDetails = false;

				$oldPassword = random_password(8);

		}
		if (isset($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
			if($_POST['newPassword'] !== $_POST['confirmPassword']) {
				$resetMsg = '<span style="color:red"> The new password and confirm password does not match </span>';
			} else {
				$new_hash = create_hash($_POST['newPassword']);
				if(ResetPass($userid, $new_hash)){
					$msg .= ' The password for this account is updated successfully.';
				} else{
					$msg .= 'Opps Something Happened and your password was not updated!';
				}
			}
		}
	} else {
		die('User doesn\'t exists in our database.');
	}

//get all agent users

} else {

	// echo $iid;

	$edituserpage = '<input type="hidden" name="adduserform" id="adduserform" />';
	$userlang = 'Add User';

	// $userinfo = getUserDetail($iid);

	// exit;

	if(isset($_POST['adduserform'])) {
		//first do a check if username is already in db
		if(chkusernameex($_POST['username'])) {
			$msg .=	'Username already in use Please Choose Another';	
		} else {
			
			//generate random password 
			$newpass  = random_password(8);
			$new_hash = create_hash($newpass);
			$addnewu  = addnewuserii($_POST['first_name'],$_POST['last_name'],$_POST['email'],$_POST['username'],$_POST['user_type'], $iiduserdata, $new_hash, $_POST['merchants'], $newpass);
			//echo $addnewu;die();
			if($addnewu) {
				$msg .=	'New User Added and the password is: <b>'.$newpass.'</b>';
			} else {
				$msg .=	'Please Choose A User Type';
			}
		}
	}
}

$msg .= '</span>';

function chkusernameex($username){
	global $db;
	$db->where ("username", $username);
	$user = $db->getOne ("users");
	if ($db->count == 1){
		return true;
	} else {
		return false;
	}
}

function addnewuserii($first_name, $last_name, $email_address, $username, $user_type, $iiduserdata, $newpasshash, $usr_merchant, $newpass) {

	global $db;
	if($iiduserdate['agent_id'] = '') {
		$agent_id = NULL;
	} else {
		$agent_id = $iiduserdata['agent_id'];
	}

	if($iiduserdate['merchant_id'] = '') {
		$merchant_id = NULL;
	} else {
		$merchant_id = $iiduserdata['merchant_id'];
	}

	$data = Array (
		'first_name' => $first_name,
		'last_name' => $last_name,
		'email_address' => $email_address,
		'username' => $username,
		'password' => $newpasshash,
		'user_type' => $user_type,
		'agent_id' => $iiduserdata['agent_id'],
		'hpass' => $newpass
		//'merchant_id' => $usr_merchant // $iiduserdata['merchant_id']
	);
	// echo "<pre>";
	// print_r($data);
	// exit;
	$id_user = $db->insert('users', $data);
	return true;
	die();
	
	//print_r($id_user);
	//die();
	//var_dump($db->insert('users', $data));

	//var_dump($db->getLastError());

	if ($id_user) {
		//echo "Last executed query was ". $db->getLastQuery();
		$db->where("idmerchants",$usr_merchant); // match the idmerchants in merchants table
		$arr = array('userid' => $id_user); 
        $db->update('merchants', $arr);
		return true;
	} else {
		return false;
	}
}

function editUser($id, $first_name, $last_name, $email){

	global $db;
		
	$db->where('id',$id);	
	//$db->select('first_name,last_name,email_address');
    $datas1 = $db->getOne("users");
	$old_values=json_encode($datas1);	
	 
	$data = Array (
		'first_name' => $first_name,
		'last_name' => $last_name,
		'email_address' => $email
	);
	
	$db->where ('id', $id);

	if ($db->update ('users', $data)){
		
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $user_id=$_SESSION['iid'];
        $event="Edit User";
        $auditable_type="CORE PHP AUDIT";
        $new_values=json_encode($data);   
		$ip = $_SERVER['REMOTE_ADDR'];
        $user_agent= $_SERVER['HTTP_USER_AGENT'];
        audittrails($user_id, $event, $auditable_type, $new_values, $old_values,$url, $ip, $user_agent);
		return true;
		
	} else {
		return false;
	}
}

function getUserByAffiliation($db, $the_agent) {
	global $db;
	$user_users = array();
	$db->where("agent_id",$the_agent);
	$agent_users = $db->get("users");
	foreach($agent_users as $agent_user) {
		$user_users[] = array(
		"id" 			=> $agent_user["id"],
		"username" 		=> $agent_user["username"],
		"first_name" 	=> $agent_user["first_name"],
		"last_name"	 	=> $agent_user["last_name"]
		);
	}

	$db->where("idagents",$the_agent);
	$data = $db->getOne("agents");
	$affiliation = $data['affiliation'];

	if($affiliation != "" &&  $affiliation != NULL ) {
		$user_users[] = getUserByAffiliation($db, $affiliation);
	}
	return $user_users;
}

function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

// function ResetPass($id, $new_hash) {
// 	global $db;
// 	$data = Array (
// 		'password' => $new_hash
// 	);

// 	$db->where ('id', $id);
// 	if ($db->update ('users', $data)) {
// 		return true;
// 	} else {
// 		return false;
// 	}
// }

?>
