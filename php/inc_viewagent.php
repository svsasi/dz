<?php



session_start();
$q = '';
$iid = '';
$agentid = '';
$merchantid ='';
function add_to_array($array, $key, $value) {
    if(array_key_exists($key, $array)) {
        if(is_array($array[$key])) {
            $array[$key][] = $value;
        }
        else {
            $array[$key] = array($array[$key], $value);           
        }
    }
    else {
        $array[$key] = array($value);
    }
}

function redirect($url)
{
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';

    echo $string;
}
if(isset($_GET['agentid'])){
$_SESSION['vagentid'] 	= $_GET['agentid'];
}
if(isset($_GET['merchantid'])){
$_SESSION['vmerchantid'] = $_GET['merchantid'];
}
if(isset($_GET['q'])){
	$q = $_GET['q'];
	if(isset($_GET['iid'])){
		$_SESSION['iid'] = $_GET['iid'];
		echo '<meta http-equiv="refresh" content="0;url=dashboard.php">';
	}
	$iid = $_SESSION['iid'];
	//var_dump($_SESSION);
	if(isset($_GET['agentid']) || isset($_GET['merchantid'])){
	$agentid 	= $_GET['agentid'];
	$merchantid = $_GET['merchantid'];
	}
	switch ($q) {
		case 'agentinfo':
			echo getAgentInfo($iid, $agentid, $merchantid);
			break;
		case 'accinfo':
			echo getAccInfo($iid, $agentid, $merchantid);
			break;
		case 'processors':
			echo getProcessors($iid, $agentid, $merchantid);
			break;
		case 'fee':
			echo getFee($iid, $agentid, $merchantid);
			break;
		case 'agentstatus':
			echo getAgentStatus($iid, $agentid, $merchantid);
			break;
		case 'editagentinfo':
			echo editAgentInfo($iid, $agentid, $merchantid);
			break;
		case 'editagentstatus':
			echo editAgentStatus($iid, $agentid, $merchantid);
			break;
		case 'editfee':
			echo editFee($iid, $agentid, $merchantid);
			break;
	}
}
if(isset($_POST['agentsaveinfo'])){
	//save post data to database
	saveInfo();
}
function checkAccess($agentid, $merchantid){
	require_once('common_functions.php');
	//put to return true problem is drilling down to ub agents merchants come back as false
	return true;
	$user_type = getUserType($_SESSION['iid']);
	if($user_type == 1){
		return true;
	}
	//var_dump('merchantid-->'.$merchantid);var_dump('---agentid-->'.$agentid);	
	$userMerchants = getUserMerchants($_SESSION['iid']);
	$AffiliationofAgents = getUserAgents($_SESSION['iid']);
	//var_dump($userMerchants);
	foreach($userMerchants as $um){
		if($um["idmerchants"] == $merchantid){
			return true;break;
		}
	}
	foreach($AffiliationofAgents as $row1) {
			$MerchantsofAgents = getMerchantsofAgents($row1['idagents']);
			//var_dump($row1['idagents']);
				if($row1['idagents'] == $agentid){
					return true;break;
				}
			foreach($MerchantsofAgents as $row2) { 
			//var_dump($row2['idmerchants'] == $merchantid);
				if($row2['idmerchants'] == $merchantid){
					return true;break;
				}
			}
		}
		return false;
}
function saveInfo(){
	
	$iid = $_SESSION['iid'];
	//var_dump($_SESSION);
	if(isset($_GET['agentid']) || isset($_GET['merchantid'])){
	$agentid 	= $_GET['agentid'];
	$merchantid = $_GET['merchantid'];
	}
	
	if($agentid!==""){
	global $db;
	$db->where('idagents',$agentid);	
	//$db->select('first_name,last_name,email_address');
    $datas1 = $db->getOne("agents");
	$old_values=json_encode($datas1);	
	
	}
	else {
	global $db;
	$db->where('idmerchants',$merchantid);	
	//$db->select('first_name,last_name,email_address');
    $datas1 = $db->getOne("merchants");
	$old_values=json_encode($datas1);	
	}
	
	
	if(checkAccess($agentid, $merchantid)){
		if($agentid > 0){
			$database = 'agents';
			$id = $agentid;
			$data = Array (
				'agentname' => $_POST['agentname'],
				'address1' => $_POST['address1'],
				'address2' => $_POST['address2'],
				'city' => $_POST['city'],
				'us_state' => $_POST['us_state'],
				'country' => $_POST['country'],
				'zippostalcode' => $_POST['zippostalcode'],
				'csemail' => $_POST['csemail'],
				'csphone' => $_POST['csphone'],
				'website' => $_POST['website'],
				'routing' => $_POST['routing'],
				'account' => $_POST['account'],
				'timezone' => $_POST['tz_name'],
				'cs_first_name' => $_POST['cs_first_name'],
				'cs_last_name' => $_POST['cs_last_name'],
				'cs_fax' => $_POST['cs_fax'],
				'legal_name' => $_POST['legal_name'],
				'business_type' => $_POST['business_type'],
				'tax_id' => $_POST['tax_id']
			);
		}elseif($merchantid > 0){
			$database = 'merchants';
			$id = $merchantid;
			$data = Array (
				'merchant_name' => $_POST['agentname'],
				'address1' => $_POST['address1'],
				'address2' => $_POST['address2'],
				'city' => $_POST['city'],
				'us_state' => $_POST['us_state'],
				'country' => $_POST['country'],
				'zippostalcode' => $_POST['zippostalcode'],
				'csemail' => $_POST['csemail'],
				'csphone' => $_POST['csphone'],
				'website' => $_POST['website'],
				'routing' => $_POST['routing'],
				'account' => $_POST['account'],
				'timezone' => $_POST['tz_name'],
				'cs_first_name' => $_POST['cs_first_name'],
				'cs_last_name' => $_POST['cs_last_name'],
				'cs_fax' => $_POST['cs_fax'],
				'legal_name' => $_POST['legal_name'],
				'business_type' => $_POST['business_type'],
				'tax_id' => $_POST['tax_id']
			);
		}
		
		
		
		global $db;
		//var_dump($_POST);
		$db->where ('id'.$database, $id);
		$db->update ($database, $data);
		
		
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $user_id=$_SESSION['iid'];
        $event="view";
        $auditable_type="CORE PHP AUDIT";
        $new_values=json_encode($data);
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent= $_SERVER['HTTP_USER_AGENT'];
        audittrails1($user_id, $event, $auditable_type, $new_values, $old_values,$url, $ip, $user_agent);
		
		
	}else{
		echo 'security bypass attempted';
	}
}

function editAgentInfo($iid, $agentid, $merchantid){
	global $db;
	if(isset($agentid) && $agentid != ''){
		$db->where("idagents",$agentid);
		$agentdata = $db->getOne("agents");
		if($db->count > 0){
		//$datar = "'agentsaveinfo&agentid=".$agentid."&merchantid=".$merchantid."'";
			return '
			<div class="panel panel-primary">
		<div class="panel-heading">
			Agent Information
		</div>
		<div class="panel-body">
		<fieldset>
			<h2>Agent Information</h2>
			<div class="row">
			 <form class="m-t" role="form" action="" method="POST">
				<input name="agentsaveinfo" id="agentsaveinfo" value="agentsaveinfo" type="hidden"/>
				
				<div class="col-md-4">
					<div class="form-group">
						<label>Bussiness Name *</label>
						<input id="agentname" name="agentname" type="text" value="'.$agentdata['agentname'].'" class="form-control required">
					</div>
					<div class="form-group">
						<label>Country *</label>
						<select name="country" id="country" class="form-control required">
							<option value="US" '.($agentdata["country"] == "US"  ? "selected" : "").'>United States of America</option>
							<option value="AF" '.($agentdata["country"] == "AF"  ? "selected" : "").'>Afghanistan</option>
							<option value="AL" '.($agentdata["country"] == "AL"  ? "selected" : "").'>Albania</option>
							<option value="DZ" '.($agentdata["country"] == "DZ"  ? "selected" : "").'>Algeria</option>
							<option value="AS" '.($agentdata["country"] == "AS"  ? "selected" : "").'>American Samoa</option>
							<option value="AD" '.($agentdata["country"] == "AD"  ? "selected" : "").'>Andorra</option>
							<option value="AO" '.($agentdata["country"] == "AO"  ? "selected" : "").'>Angola</option>
							<option value="AI" '.($agentdata["country"] == "AI"  ? "selected" : "").'>Anguilla</option>
							<option value="AG" '.($agentdata["country"] == "AG"  ? "selected" : "").'>Antigua &amp; Barbuda</option>
							<option value="AR" '.($agentdata["country"] == "AR"  ? "selected" : "").'>Argentina</option>
							<option value="AA" '.($agentdata["country"] == "AA"  ? "selected" : "").'>Armenia</option>
							<option value="AW" '.($agentdata["country"] == "AW"  ? "selected" : "").'>Aruba</option>
							<option value="AU" '.($agentdata["country"] == "AU"  ? "selected" : "").'>Australia</option>
							<option value="AT" '.($agentdata["country"] == "AT"  ? "selected" : "").'>Austria</option>
							<option value="AZ" '.($agentdata["country"] == "AZ"  ? "selected" : "").'>Azerbaijan</option>
							<option value="BS" '.($agentdata["country"] == "BS"  ? "selected" : "").'>Bahamas</option>
							<option value="BH" '.($agentdata["country"] == "BH"  ? "selected" : "").'>Bahrain</option>
							<option value="BD" '.($agentdata["country"] == "BD"  ? "selected" : "").'>Bangladesh</option>
							<option value="BB" '.($agentdata["country"] == "BB"  ? "selected" : "").'>Barbados</option>
							<option value="BY" '.($agentdata["country"] == "BY"  ? "selected" : "").'>Belarus</option>
							<option value="BE" '.($agentdata["country"] == "BE"  ? "selected" : "").'>Belgium</option>
							<option value="BZ" '.($agentdata["country"] == "BZ"  ? "selected" : "").'>Belize</option>
							<option value="BJ" '.($agentdata["country"] == "BJ"  ? "selected" : "").'>Benin</option>
							<option value="BM" '.($agentdata["country"] == "BM"  ? "selected" : "").'>Bermuda</option>
							<option value="BT" '.($agentdata["country"] == "BT"  ? "selected" : "").'>Bhutan</option>
							<option value="BO" '.($agentdata["country"] == "BO"  ? "selected" : "").'>Bolivia</option>
							<option value="BL" '.($agentdata["country"] == "BL"  ? "selected" : "").'>Bonaire</option>
							<option value="BA" '.($agentdata["country"] == "BA"  ? "selected" : "").'>Bosnia &amp; Herzegovina</option>
							<option value="BW" '.($agentdata["country"] == "BW"  ? "selected" : "").'>Botswana</option>
							<option value="BR" '.($agentdata["country"] == "BR"  ? "selected" : "").'>Brazil</option>
							<option value="BC" '.($agentdata["country"] == "BC"  ? "selected" : "").'>British Indian Ocean Ter</option>
							<option value="BN" '.($agentdata["country"] == "BN"  ? "selected" : "").'>Brunei</option>
							<option value="BG" '.($agentdata["country"] == "BG"  ? "selected" : "").'>Bulgaria</option>
							<option value="BF" '.($agentdata["country"] == "BF"  ? "selected" : "").'>Burkina Faso</option>
							<option value="BI" '.($agentdata["country"] == "BI"  ? "selected" : "").'>Burundi</option>
							<option value="KH" '.($agentdata["country"] == "KH"  ? "selected" : "").'>Cambodia</option>
							<option value="CM" '.($agentdata["country"] == "CM"  ? "selected" : "").'>Cameroon</option>
							<option value="CA" '.($agentdata["country"] == "CA"  ? "selected" : "").'>Canada</option>
							<option value="IC" '.($agentdata["country"] == "IC"  ? "selected" : "").'>Canary Islands</option>
							<option value="CV" '.($agentdata["country"] == "CV"  ? "selected" : "").'>Cape Verde</option>
							<option value="KY" '.($agentdata["country"] == "KY"  ? "selected" : "").'>Cayman Islands</option>
							<option value="CF" '.($agentdata["country"] == "CF"  ? "selected" : "").'>Central African Republic</option>
							<option value="TD" '.($agentdata["country"] == "TD"  ? "selected" : "").'>Chad</option>
							<option value="CD" '.($agentdata["country"] == "CD"  ? "selected" : "").'>Channel Islands</option>
							<option value="CL" '.($agentdata["country"] == "CL"  ? "selected" : "").'>Chile</option>
							<option value="CN" '.($agentdata["country"] == "CN"  ? "selected" : "").'>China</option>
							<option value="CI" '.($agentdata["country"] == "CI"  ? "selected" : "").'>Christmas Island</option>
							<option value="CS" '.($agentdata["country"] == "CS"  ? "selected" : "").'>Cocos Island</option>
							<option value="CO" '.($agentdata["country"] == "CO"  ? "selected" : "").'>Colombia</option>
							<option value="CC" '.($agentdata["country"] == "CC"  ? "selected" : "").'>Comoros</option>
							<option value="CG" '.($agentdata["country"] == "CG"  ? "selected" : "").'>Congo</option>
							<option value="CK" '.($agentdata["country"] == "CK"  ? "selected" : "").'>Cook Islands</option>
							<option value="CR" '.($agentdata["country"] == "CR"  ? "selected" : "").'>Costa Rica</option>
							<option value="CT" '.($agentdata["country"] == "CT"  ? "selected" : "").'>Cote D Ivoire</option>
							<option value="HR" '.($agentdata["country"] == "HR"  ? "selected" : "").'>Croatia</option>
							<option value="CU" '.($agentdata["country"] == "CU"  ? "selected" : "").'>Cuba</option>
							<option value="CB" '.($agentdata["country"] == "CB"  ? "selected" : "").'>Curacao</option>
							<option value="CY" '.($agentdata["country"] == "CY"  ? "selected" : "").'>Cyprus</option>
							<option value="CZ" '.($agentdata["country"] == "CZ"  ? "selected" : "").'>Czech Republic</option>
							<option value="DK" '.($agentdata["country"] == "DK"  ? "selected" : "").'>Denmark</option>
							<option value="DJ" '.($agentdata["country"] == "DJ"  ? "selected" : "").'>Djibouti</option>
							<option value="DM" '.($agentdata["country"] == "DM"  ? "selected" : "").'>Dominica</option>
							<option value="DO" '.($agentdata["country"] == "DO"  ? "selected" : "").'>Dominican Republic</option>
							<option value="TM" '.($agentdata["country"] == "TM"  ? "selected" : "").'>East Timor</option>
							<option value="EC" '.($agentdata["country"] == "EC"  ? "selected" : "").'>Ecuador</option>
							<option value="EG" '.($agentdata["country"] == "EG"  ? "selected" : "").'>Egypt</option>
							<option value="SV" '.($agentdata["country"] == "SV"  ? "selected" : "").'>El Salvador</option>
							<option value="GQ" '.($agentdata["country"] == "GQ"  ? "selected" : "").'>Equatorial Guinea</option>
							<option value="ER" '.($agentdata["country"] == "ER"  ? "selected" : "").'>Eritrea</option>
							<option value="EE" '.($agentdata["country"] == "EE"  ? "selected" : "").'>Estonia</option>
							<option value="ET" '.($agentdata["country"] == "ET"  ? "selected" : "").'>Ethiopia</option>
							<option value="FA" '.($agentdata["country"] == "FA"  ? "selected" : "").'>Falkland Islands</option>
							<option value="FO" '.($agentdata["country"] == "FO"  ? "selected" : "").'>Faroe Islands</option>
							<option value="FJ" '.($agentdata["country"] == "FJ"  ? "selected" : "").'>Fiji</option>
							<option value="FI" '.($agentdata["country"] == "FI"  ? "selected" : "").'>Finland</option>
							<option value="FR" '.($agentdata["country"] == "FR"  ? "selected" : "").'>France</option>
							<option value="GF" '.($agentdata["country"] == "GF"  ? "selected" : "").'>French Guiana</option>
							<option value="PF" '.($agentdata["country"] == "PF"  ? "selected" : "").'>French Polynesia</option>
							<option value="FS" '.($agentdata["country"] == "FS"  ? "selected" : "").'>French Southern Ter</option>
							<option value="GA" '.($agentdata["country"] == "GA"  ? "selected" : "").'>Gabon</option>
							<option value="GM" '.($agentdata["country"] == "GM"  ? "selected" : "").'>Gambia</option>
							<option value="GE" '.($agentdata["country"] == "GE"  ? "selected" : "").'>Georgia</option>
							<option value="DE" '.($agentdata["country"] == "DE"  ? "selected" : "").'>Germany</option>
							<option value="GH" '.($agentdata["country"] == "GH"  ? "selected" : "").'>Ghana</option>
							<option value="GI" '.($agentdata["country"] == "GI"  ? "selected" : "").'>Gibraltar</option>
							<option value="GB" '.($agentdata["country"] == "GB"  ? "selected" : "").'>Great Britain</option>
							<option value="GR" '.($agentdata["country"] == "GR"  ? "selected" : "").'>Greece</option>
							<option value="GL" '.($agentdata["country"] == "GL"  ? "selected" : "").'>Greenland</option>
							<option value="GD" '.($agentdata["country"] == "GD"  ? "selected" : "").'>Grenada</option>
							<option value="GP" '.($agentdata["country"] == "GP"  ? "selected" : "").'>Guadeloupe</option>
							<option value="GU" '.($agentdata["country"] == "GU"  ? "selected" : "").'>Guam</option>
							<option value="GT" '.($agentdata["country"] == "GT"  ? "selected" : "").'>Guatemala</option>
							<option value="GN" '.($agentdata["country"] == "GN"  ? "selected" : "").'>Guinea</option>
							<option value="GY" '.($agentdata["country"] == "GY"  ? "selected" : "").'>Guyana</option>
							<option value="HT" '.($agentdata["country"] == "HT"  ? "selected" : "").'>Haiti</option>
							<option value="HW" '.($agentdata["country"] == "HW"  ? "selected" : "").'>Hawaii</option>
							<option value="HN" '.($agentdata["country"] == "HN"  ? "selected" : "").'>Honduras</option>
							<option value="HK" '.($agentdata["country"] == "HK"  ? "selected" : "").'>Hong Kong</option>
							<option value="HU" '.($agentdata["country"] == "HU"  ? "selected" : "").'>Hungary</option>
							<option value="IS" '.($agentdata["country"] == "IS"  ? "selected" : "").'>Iceland</option>
							<option value="IN" '.($agentdata["country"] == "IN"  ? "selected" : "").'>India</option>
							<option value="ID" '.($agentdata["country"] == "ID"  ? "selected" : "").'>Indonesia</option>
							<option value="IA" '.($agentdata["country"] == "IA"  ? "selected" : "").'>Iran</option>
							<option value="IQ" '.($agentdata["country"] == "IQ"  ? "selected" : "").'>Iraq</option>
							<option value="IR" '.($agentdata["country"] == "IR"  ? "selected" : "").'>Ireland</option>
							<option value="IM" '.($agentdata["country"] == "IM"  ? "selected" : "").'>Isle of Man</option>
							<option value="IL" '.($agentdata["country"] == "IL"  ? "selected" : "").'>Israel</option>
							<option value="IT" '.($agentdata["country"] == "IT"  ? "selected" : "").'>Italy</option>
							<option value="JM" '.($agentdata["country"] == "JM"  ? "selected" : "").'>Jamaica</option>
							<option value="JP" '.($agentdata["country"] == "JP"  ? "selected" : "").'>Japan</option>
							<option value="JO" '.($agentdata["country"] == "JO"  ? "selected" : "").'>Jordan</option>
							<option value="KZ" '.($agentdata["country"] == "KZ"  ? "selected" : "").'>Kazakhstan</option>
							<option value="KE" '.($agentdata["country"] == "KE"  ? "selected" : "").'>Kenya</option>
							<option value="KI" '.($agentdata["country"] == "KI"  ? "selected" : "").'>Kiribati</option>
							<option value="NK" '.($agentdata["country"] == "NK"  ? "selected" : "").'>Korea North</option>
							<option value="KS" '.($agentdata["country"] == "KS"  ? "selected" : "").'>Korea South</option>
							<option value="KW" '.($agentdata["country"] == "KW"  ? "selected" : "").'>Kuwait</option>
							<option value="KG" '.($agentdata["country"] == "KG"  ? "selected" : "").'>Kyrgyzstan</option>
							<option value="LA" '.($agentdata["country"] == "LA"  ? "selected" : "").'>Laos</option>
							<option value="LV" '.($agentdata["country"] == "LV"  ? "selected" : "").'>Latvia</option>
							<option value="LB" '.($agentdata["country"] == "LB"  ? "selected" : "").'>Lebanon</option>
							<option value="LS" '.($agentdata["country"] == "LS"  ? "selected" : "").'>Lesotho</option>
							<option value="LR" '.($agentdata["country"] == "LR"  ? "selected" : "").'>Liberia</option>
							<option value="LY" '.($agentdata["country"] == "LY"  ? "selected" : "").'>Libya</option>
							<option value="LI" '.($agentdata["country"] == "LI"  ? "selected" : "").'>Liechtenstein</option>
							<option value="LT" '.($agentdata["country"] == "LT"  ? "selected" : "").'>Lithuania</option>
							<option value="LU" '.($agentdata["country"] == "LU"  ? "selected" : "").'>Luxembourg</option>
							<option value="MO" '.($agentdata["country"] == "MO"  ? "selected" : "").'>Macau</option>
							<option value="MK" '.($agentdata["country"] == "MK"  ? "selected" : "").'>Macedonia</option>
							<option value="MG" '.($agentdata["country"] == "MG"  ? "selected" : "").'>Madagascar</option>
							<option value="MY" '.($agentdata["country"] == "MY"  ? "selected" : "").'>Malaysia</option>
							<option value="MW" '.($agentdata["country"] == "MW"  ? "selected" : "").'>Malawi</option>
							<option value="MV" '.($agentdata["country"] == "MV"  ? "selected" : "").'>Maldives</option>
							<option value="ML" '.($agentdata["country"] == "ML"  ? "selected" : "").'>Mali</option>
							<option value="MT" '.($agentdata["country"] == "MT"  ? "selected" : "").'>Malta</option>
							<option value="MH" '.($agentdata["country"] == "MH"  ? "selected" : "").'>Marshall Islands</option>
							<option value="MQ" '.($agentdata["country"] == "MQ"  ? "selected" : "").'>Martinique</option>
							<option value="MR" '.($agentdata["country"] == "MR"  ? "selected" : "").'>Mauritania</option>
							<option value="MU" '.($agentdata["country"] == "MU"  ? "selected" : "").'>Mauritius</option>
							<option value="ME" '.($agentdata["country"] == "ME"  ? "selected" : "").'>Mayotte</option>
							<option value="MX" '.($agentdata["country"] == "MX"  ? "selected" : "").'>Mexico</option>
							<option value="MI" '.($agentdata["country"] == "MI"  ? "selected" : "").'>Midway Islands</option>
							<option value="MD" '.($agentdata["country"] == "MD"  ? "selected" : "").'>Moldova</option>
							<option value="MC" '.($agentdata["country"] == "MC"  ? "selected" : "").'>Monaco</option>
							<option value="MN" '.($agentdata["country"] == "MN"  ? "selected" : "").'>Mongolia</option>
							<option value="MS" '.($agentdata["country"] == "MS"  ? "selected" : "").'>Montserrat</option>
							<option value="MA" '.($agentdata["country"] == "MA"  ? "selected" : "").'>Morocco</option>
							<option value="MZ" '.($agentdata["country"] == "MZ"  ? "selected" : "").'>Mozambique</option>
							<option value="MM" '.($agentdata["country"] == "MM"  ? "selected" : "").'>Myanmar</option>
							<option value="NA" '.($agentdata["country"] == "NA"  ? "selected" : "").'>Nambia</option>
							<option value="NU" '.($agentdata["country"] == "NU"  ? "selected" : "").'>Nauru</option>
							<option value="NP" '.($agentdata["country"] == "NP"  ? "selected" : "").'>Nepal</option>
							<option value="AN" '.($agentdata["country"] == "AN"  ? "selected" : "").'>Netherland Antilles</option>
							<option value="NL" '.($agentdata["country"] == "NL"  ? "selected" : "").'>Netherlands (Holland, Europe)</option>
							<option value="NV" '.($agentdata["country"] == "NV"  ? "selected" : "").'>Nevis</option>
							<option value="NC" '.($agentdata["country"] == "NC"  ? "selected" : "").'>New Caledonia</option>
							<option value="NZ" '.($agentdata["country"] == "NZ"  ? "selected" : "").'>New Zealand</option>
							<option value="NI" '.($agentdata["country"] == "NI"  ? "selected" : "").'>Nicaragua</option>
							<option value="NE" '.($agentdata["country"] == "NE"  ? "selected" : "").'>Niger</option>
							<option value="NG" '.($agentdata["country"] == "NG"  ? "selected" : "").'>Nigeria</option>
							<option value="NW" '.($agentdata["country"] == "NW"  ? "selected" : "").'>Niue</option>
							<option value="NF" '.($agentdata["country"] == "NF"  ? "selected" : "").'>Norfolk Island</option>
							<option value="NO" '.($agentdata["country"] == "NO"  ? "selected" : "").'>Norway</option>
							<option value="OM" '.($agentdata["country"] == "OM"  ? "selected" : "").'>Oman</option>
							<option value="PK" '.($agentdata["country"] == "PK"  ? "selected" : "").'>Pakistan</option>
							<option value="PW" '.($agentdata["country"] == "PW"  ? "selected" : "").'>Palau Island</option>
							<option value="PS" '.($agentdata["country"] == "PS"  ? "selected" : "").'>Palestine</option>
							<option value="PA" '.($agentdata["country"] == "PA"  ? "selected" : "").'>Panama</option>
							<option value="PG" '.($agentdata["country"] == "PG"  ? "selected" : "").'>Papua New Guinea</option>
							<option value="PY" '.($agentdata["country"] == "PY"  ? "selected" : "").'>Paraguay</option>
							<option value="PE" '.($agentdata["country"] == "PE"  ? "selected" : "").'>Peru</option>
							<option value="PH" '.($agentdata["country"] == "PH"  ? "selected" : "").'>Philippines</option>
							<option value="PO" '.($agentdata["country"] == "PO"  ? "selected" : "").'>Pitcairn Island</option>
							<option value="PL" '.($agentdata["country"] == "PL"  ? "selected" : "").'>Poland</option>
							<option value="PT" '.($agentdata["country"] == "PT"  ? "selected" : "").'>Portugal</option>
							<option value="PR" '.($agentdata["country"] == "PR"  ? "selected" : "").'>Puerto Rico</option>
							<option value="QA" '.($agentdata["country"] == "QA"  ? "selected" : "").'>Qatar</option>
							<option value="ME" '.($agentdata["country"] == "ME"  ? "selected" : "").'>Republic of Montenegro</option>
							<option value="RS" '.($agentdata["country"] == "RS"  ? "selected" : "").'>Republic of Serbia</option>
							<option value="RE" '.($agentdata["country"] == "RE"  ? "selected" : "").'>Reunion</option>
							<option value="RO" '.($agentdata["country"] == "RO"  ? "selected" : "").'>Romania</option>
							<option value="RU" '.($agentdata["country"] == "RU"  ? "selected" : "").'>Russia</option>
							<option value="RW" '.($agentdata["country"] == "RW"  ? "selected" : "").'>Rwanda</option>
							<option value="NT" '.($agentdata["country"] == "NT"  ? "selected" : "").'>St Barthelemy</option>
							<option value="EU" '.($agentdata["country"] == "EU"  ? "selected" : "").'>St Eustatius</option>
							<option value="HE" '.($agentdata["country"] == "HE"  ? "selected" : "").'>St Helena</option>
							<option value="KN" '.($agentdata["country"] == "KN"  ? "selected" : "").'>St Kitts-Nevis</option>
							<option value="LC" '.($agentdata["country"] == "LC"  ? "selected" : "").'>St Lucia</option>
							<option value="MB" '.($agentdata["country"] == "MB"  ? "selected" : "").'>St Maarten</option>
							<option value="PM" '.($agentdata["country"] == "PM"  ? "selected" : "").'>St Pierre &amp; Miquelon</option>
							<option value="VC" '.($agentdata["country"] == "VC"  ? "selected" : "").'>St Vincent &amp; Grenadines</option>
							<option value="SP" '.($agentdata["country"] == "SP"  ? "selected" : "").'>Saipan</option>
							<option value="SO" '.($agentdata["country"] == "SO"  ? "selected" : "").'>Samoa</option>
							<option value="AS" '.($agentdata["country"] == "AS"  ? "selected" : "").'>Samoa American</option>
							<option value="SM" '.($agentdata["country"] == "SM"  ? "selected" : "").'>San Marino</option>
							<option value="ST" '.($agentdata["country"] == "ST"  ? "selected" : "").'>Sao Tome &amp; Principe</option>
							<option value="SA" '.($agentdata["country"] == "SA"  ? "selected" : "").'>Saudi Arabia</option>
							<option value="SN" '.($agentdata["country"] == "SN"  ? "selected" : "").'>Senegal</option>
							<option value="RS" '.($agentdata["country"] == "RS"  ? "selected" : "").'>Serbia</option>
							<option value="SC" '.($agentdata["country"] == "SC"  ? "selected" : "").'>Seychelles</option>
							<option value="SL" '.($agentdata["country"] == "SL"  ? "selected" : "").'>Sierra Leone</option>
							<option value="SG" '.($agentdata["country"] == "SG"  ? "selected" : "").'>Singapore</option>
							<option value="SK" '.($agentdata["country"] == "SK"  ? "selected" : "").'>Slovakia</option>
							<option value="SI" '.($agentdata["country"] == "SI"  ? "selected" : "").'>Slovenia</option>
							<option value="SB" '.($agentdata["country"] == "SB"  ? "selected" : "").'>Solomon Islands</option>
							<option value="OI" '.($agentdata["country"] == "OI"  ? "selected" : "").'>Somalia</option>
							<option value="ZA" '.($agentdata["country"] == "ZA"  ? "selected" : "").'>South Africa</option>
							<option value="ES" '.($agentdata["country"] == "ES"  ? "selected" : "").'>Spain</option>
							<option value="LK" '.($agentdata["country"] == "LK"  ? "selected" : "").'>Sri Lanka</option>
							<option value="SD" '.($agentdata["country"] == "SD"  ? "selected" : "").'>Sudan</option>
							<option value="SR" '.($agentdata["country"] == "SR"  ? "selected" : "").'>Suriname</option>
							<option value="SZ" '.($agentdata["country"] == "SZ"  ? "selected" : "").'>Swaziland</option>
							<option value="SE" '.($agentdata["country"] == "SE"  ? "selected" : "").'>Sweden</option>
							<option value="CH" '.($agentdata["country"] == "CH"  ? "selected" : "").'>Switzerland</option>
							<option value="SY" '.($agentdata["country"] == "SY"  ? "selected" : "").'>Syria</option>
							<option value="TA" '.($agentdata["country"] == "TA"  ? "selected" : "").'>Tahiti</option>
							<option value="TW" '.($agentdata["country"] == "TW"  ? "selected" : "").'>Taiwan</option>
							<option value="TJ" '.($agentdata["country"] == "TJ"  ? "selected" : "").'>Tajikistan</option>
							<option value="TZ" '.($agentdata["country"] == "TZ"  ? "selected" : "").'>Tanzania</option>
							<option value="TH" '.($agentdata["country"] == "TH"  ? "selected" : "").'>Thailand</option>
							<option value="TG" '.($agentdata["country"] == "TG"  ? "selected" : "").'>Togo</option>
							<option value="TK" '.($agentdata["country"] == "TK"  ? "selected" : "").'>Tokelau</option>
							<option value="TO" '.($agentdata["country"] == "TO"  ? "selected" : "").'>Tonga</option>
							<option value="TT" '.($agentdata["country"] == "TT"  ? "selected" : "").'>Trinidad &amp; Tobago</option>
							<option value="TN" '.($agentdata["country"] == "TN"  ? "selected" : "").'>Tunisia</option>
							<option value="TR" '.($agentdata["country"] == "TR"  ? "selected" : "").'>Turkey</option>
							<option value="TU" '.($agentdata["country"] == "TU"  ? "selected" : "").'>Turkmenistan</option>
							<option value="TC" '.($agentdata["country"] == "TC"  ? "selected" : "").'>Turks &amp; Caicos Is</option>
							<option value="TV" '.($agentdata["country"] == "TV"  ? "selected" : "").'>Tuvalu</option>
							<option value="UG" '.($agentdata["country"] == "UG"  ? "selected" : "").'>Uganda</option>
							<option value="UA" '.($agentdata["country"] == "UA"  ? "selected" : "").'>Ukraine</option>
							<option value="AE" '.($agentdata["country"] == "AE"  ? "selected" : "").'>United Arab Emirates</option>
							<option value="GB" '.($agentdata["country"] == "GB"  ? "selected" : "").'>United Kingdom</option>
							<option value="UY" '.($agentdata["country"] == "UY"  ? "selected" : "").'>Uruguay</option>
							<option value="UZ" '.($agentdata["country"] == "UZ"  ? "selected" : "").'>Uzbekistan</option>
							<option value="VU" '.($agentdata["country"] == "VU"  ? "selected" : "").'>Vanuatu</option>
							<option value="VS" '.($agentdata["country"] == "VS"  ? "selected" : "").'>Vatican City State</option>
							<option value="VE" '.($agentdata["country"] == "VE"  ? "selected" : "").'>Venezuela</option>
							<option value="VN" '.($agentdata["country"] == "VN"  ? "selected" : "").'>Vietnam</option>
							<option value="VB" '.($agentdata["country"] == "VB"  ? "selected" : "").'>Virgin Islands (Brit)</option>
							<option value="VA" '.($agentdata["country"] == "VA"  ? "selected" : "").'>Virgin Islands (USA)</option>
							<option value="WK" '.($agentdata["country"] == "WK"  ? "selected" : "").'>Wake Island</option>
							<option value="WF" '.($agentdata["country"] == "WF"  ? "selected" : "").'>Wallis &amp; Futana Is</option>
							<option value="YE" '.($agentdata["country"] == "YE"  ? "selected" : "").'>Yemen</option>
							<option value="ZR" '.($agentdata["country"] == "ZR"  ? "selected" : "").'>Zaire</option>
							<option value="ZM" '.($agentdata["country"] == "ZM"  ? "selected" : "").'>Zambia</option>
							<option value="ZW" '.($agentdata["country"] == "ZW"  ? "selected" : "").'>Zimbabwe</option>
						</select>
					</div>
					<div class="form-group" id="statebox">
						<label>State</label>
						<select id="us_state" name="us_state" class="form-control">
							<option value="AL" '.($agentdata["us_state"] == "AL"  ? "selected" : "").'>Alabama</option>
							<option value="AK" '.($agentdata["us_state"] == "AK"  ? "selected" : "").'>Alaska</option>
							<option value="AZ" '.($agentdata["us_state"] == "AZ"  ? "selected" : "").'>Arizona</option>
							<option value="AR" '.($agentdata["us_state"] == "AR"  ? "selected" : "").'>Arkansas</option>
							<option value="CA" '.($agentdata["us_state"] == "CA"  ? "selected" : "").'>California</option>
							<option value="CO" '.($agentdata["us_state"] == "CO"  ? "selected" : "").'>Colorado</option>
							<option value="CT" '.($agentdata["us_state"] == "CT"  ? "selected" : "").'>Connecticut</option>
							<option value="DE" '.($agentdata["us_state"] == "DE"  ? "selected" : "").'>Delaware</option>
							<option value="DC" '.($agentdata["us_state"] == "DC"  ? "selected" : "").'>District Of Columbia</option>
							<option value="FL" '.($agentdata["us_state"] == "FL"  ? "selected" : "").'>Florida</option>
							<option value="GA" '.($agentdata["us_state"] == "GA"  ? "selected" : "").'>Georgia</option>
							<option value="HI" '.($agentdata["us_state"] == "HI"  ? "selected" : "").'>Hawaii</option>
							<option value="ID" '.($agentdata["us_state"] == "ID"  ? "selected" : "").'>Idaho</option>
							<option value="IL" '.($agentdata["us_state"] == "IL"  ? "selected" : "").'>Illinois</option>
							<option value="IN" '.($agentdata["us_state"] == "IN"  ? "selected" : "").'>Indiana</option>
							<option value="IA" '.($agentdata["us_state"] == "IA"  ? "selected" : "").'>Iowa</option>
							<option value="KS" '.($agentdata["us_state"] == "KS"  ? "selected" : "").'>Kansas</option>
							<option value="KY" '.($agentdata["us_state"] == "KY"  ? "selected" : "").'>Kentucky</option>
							<option value="LA" '.($agentdata["us_state"] == "LA"  ? "selected" : "").'>Louisiana</option>
							<option value="ME" '.($agentdata["us_state"] == "ME"  ? "selected" : "").'>Maine</option>
							<option value="MD" '.($agentdata["us_state"] == "MD"  ? "selected" : "").'>Maryland</option>
							<option value="MA" '.($agentdata["us_state"] == "MA"  ? "selected" : "").'>Massachusetts</option>
							<option value="MI" '.($agentdata["us_state"] == "MI"  ? "selected" : "").'>Michigan</option>
							<option value="MN" '.($agentdata["us_state"] == "MN"  ? "selected" : "").'>Minnesota</option>
							<option value="MS" '.($agentdata["us_state"] == "MS"  ? "selected" : "").'>Mississippi</option>
							<option value="MO" '.($agentdata["us_state"] == "MO"  ? "selected" : "").'>Missouri</option>
							<option value="MT" '.($agentdata["us_state"] == "MT"  ? "selected" : "").'>Montana</option>
							<option value="NE" '.($agentdata["us_state"] == "NE"  ? "selected" : "").'>Nebraska</option>
							<option value="NV" '.($agentdata["us_state"] == "NV"  ? "selected" : "").'>Nevada</option>
							<option value="NH" '.($agentdata["us_state"] == "NH"  ? "selected" : "").'>New Hampshire</option>
							<option value="NJ" '.($agentdata["us_state"] == "NJ"  ? "selected" : "").'>New Jersey</option>
							<option value="NM" '.($agentdata["us_state"] == "NM"  ? "selected" : "").'>New Mexico</option>
							<option value="NY" '.($agentdata["us_state"] == "NY"  ? "selected" : "").'>New York</option>
							<option value="NC" '.($agentdata["us_state"] == "NC"  ? "selected" : "").'>North Carolina</option>
							<option value="ND" '.($agentdata["us_state"] == "ND"  ? "selected" : "").'>North Dakota</option>
							<option value="OH" '.($agentdata["us_state"] == "OH"  ? "selected" : "").'>Ohio</option>
							<option value="OK" '.($agentdata["us_state"] == "OK"  ? "selected" : "").'>Oklahoma</option>
							<option value="OR" '.($agentdata["us_state"] == "OR"  ? "selected" : "").'>Oregon</option>
							<option value="PA" '.($agentdata["us_state"] == "PA"  ? "selected" : "").'>Pennsylvania</option>
							<option value="RI" '.($agentdata["us_state"] == "RI"  ? "selected" : "").'>Rhode Island</option>
							<option value="SC" '.($agentdata["us_state"] == "SC"  ? "selected" : "").'>South Carolina</option>
							<option value="SD" '.($agentdata["us_state"] == "SD"  ? "selected" : "").'>South Dakota</option>
							<option value="TN" '.($agentdata["us_state"] == "TN"  ? "selected" : "").'>Tennessee</option>
							<option value="TX" '.($agentdata["us_state"] == "TX"  ? "selected" : "").'>Texas</option>
							<option value="UT" '.($agentdata["us_state"] == "UT"  ? "selected" : "").'>Utah</option>
							<option value="VT" '.($agentdata["us_state"] == "VT"  ? "selected" : "").'>Vermont</option>
							<option value="VA" '.($agentdata["us_state"] == "VA"  ? "selected" : "").'>Virginia</option>
							<option value="WA" '.($agentdata["us_state"] == "WA"  ? "selected" : "").'>Washington</option>
							<option value="WV" '.($agentdata["us_state"] == "WV"  ? "selected" : "").'>West Virginia</option>
							<option value="WI" '.($agentdata["us_state"] == "WI"  ? "selected" : "").'>Wisconsin</option>
							<option value="WY" '.($agentdata["us_state"] == "WY"  ? "selected" : "").'>Wyoming</option>
						</select>
					</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
						<label>Customer Service Information:</label>
						<div class="row">
							<div class="col-md-6">
													<label>Fist Name *</label>
													<input id="cs_first_name" name="cs_first_name" type="text" value="'.$agentdata['cs_first_name'].'" class="form-control required"></div>
							<div class="col-md-6">
													<label>Last Name *</label>
													<input id="cs_last_name" name="cs_last_name" type="text" value="'.$agentdata['cs_last_name'].'" class="form-control required"></div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div><label>Business Information:</label>
						<div class="row">
							<div class="col-md-6">
													<label>Legal Name *</label>
													<input id="legal_name" name="legal_name" type="text" value="'.$agentdata['legal_name'].'" class="form-control required"></div>
							<div class="col-md-6">
													<label>Tax ID *</label>
													<input id="tax_id" name="tax_id" type="text" value="'.$agentdata['tax_id'].'" class="form-control required"></div>
						</div>
					</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Address *</label>
							<input id="address1" name="address1" value="'.$agentdata['address1'].'" type="text" class="form-control required">
						</div>
						<div class="form-group">
							<label>Address (Cont)</label>
							<input id="address2" name="address2" value="'.$agentdata['address2'].'" type="text" class="form-control">
						</div>
						<div class="form-group">
							<label>City *</label>
							<input id="city" name="city" type="text" value="'.$agentdata['city'].'" class="form-control required" aria-required="true">
						</div>
						<div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
							<label> </label>
							<div class="row">
								<div class="col-md-6">
														<label>Phone *</label>
														<input id="csphone" name="csphone" type="text" value="'.$agentdata['csphone'].'" class="form-control required"></div>
								<div class="col-md-6">
														<label>Fax *</label>
														<input id="cs_fax" name="cs_fax" type="text" value="'.$agentdata['cs_fax'].'" class="form-control required"></div>
							</div>
						</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
							<label> </label>
							<div class="row">
								<div class="col-md-6">
														<label>Routing # *</label>
														<input id="routing" name="routing" type="text" value="'.$agentdata['routing'].'" class="form-control required"></div>
								<div class="col-md-6">
														<label>Account # *</label>
														<input id="account" name="account" type="text" value="'.$agentdata['account'].'" class="form-control required"></div>
							</div>
						</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Zip/Postal Code *</label>
								<input id="zippostalcode" name="zippostalcode" value="'.$agentdata['zippostalcode'].'" type="text" class="form-control required" aria-required="true">
							</div>
							<div class="form-group">
								<label>Website Address</label>
								<input id="website" name="website" value="'.$agentdata['website'].'" type="text" class="form-control" aria-required="true">
							</div>
							<div class="form-group">
								<label>Time Zone</label>
								<select id="tz_name" name="tz_name" class="form-control required">
									<option value="-12.0" '.($agentdata["timezone"] == "-12.0"  ? "selected" : "").'>(GMT -12:00) Eniwetok, Kwajalein</option>
									<option value="-11.0" '.($agentdata["timezone"] == "-11.0"  ? "selected" : "").'>(GMT -11:00) Midway Island, Samoa</option>
									<option value="-10.0" '.($agentdata["timezone"] == "-10.0"  ? "selected" : "").'>(GMT -10:00) Hawaii</option>
									<option value="-9.0" '.($agentdata["timezone"] == "-9.0"  ? "selected" : "").'>(GMT -9:00) Alaska</option>
									<option value="-8.0" '.($agentdata["timezone"] == "-8.0"  ? "selected" : "").'>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
									<option value="-7.0" '.($agentdata["timezone"] == "-7.0"  ? "selected" : "").'>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
									<option value="-6.0" '.($agentdata["timezone"] == "-6.0"  ? "selected" : "").'>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
									<option value="-5.0" '.($agentdata["timezone"] == "-5.0"  ? "selected" : "").' '.($agentdata["timezone"] == NULL  ? "selected" : "").'>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
									<option value="-4.0" '.($agentdata["timezone"] == "-4.0"  ? "selected" : "").'>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
									<option value="-3.5" '.($agentdata["timezone"] == "-3.5"  ? "selected" : "").'>(GMT -3:30) Newfoundland</option>
									<option value="-3.0" '.($agentdata["timezone"] == "-3.0"  ? "selected" : "").'>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
									<option value="-2.0" '.($agentdata["timezone"] == "-2.0"  ? "selected" : "").'>(GMT -2:00) Mid-Atlantic</option>
									<option value="-1.0" '.($agentdata["timezone"] == "-1.0"  ? "selected" : "").'>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
									<option value="0.0" '.($agentdata["timezone"] == "0.0"  ? "selected" : "").'>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
									<option value="1.0" '.($agentdata["timezone"] == "1.0"  ? "selected" : "").'>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
									<option value="2.0" '.($agentdata["timezone"] == "2.0"  ? "selected" : "").'>(GMT +2:00) Kaliningrad, South Africa</option>
									<option value="3.0" '.($agentdata["timezone"] == "3.0"  ? "selected" : "").'>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
									<option value="3.5" '.($agentdata["timezone"] == "3.5"  ? "selected" : "").'>(GMT +3:30) Tehran</option>
									<option value="4.0" '.($agentdata["timezone"] == "4.0"  ? "selected" : "").'>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
									<option value="4.5" '.($agentdata["timezone"] == "4.5"  ? "selected" : "").'>(GMT +4:30) Kabul</option>
									<option value="5.0" '.($agentdata["timezone"] == "5.0"  ? "selected" : "").'>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
									<option value="5.5" '.($agentdata["timezone"] == "5.5"  ? "selected" : "").'>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
									<option value="5.75" '.($agentdata["timezone"] == "5.75"  ? "selected" : "").'>(GMT +5:45) Kathmandu</option>
									<option value="6.0" '.($agentdata["timezone"] == "6.0"  ? "selected" : "").'>(GMT +6:00) Almaty, Dhaka, Colombo</option>
									<option value="7.0" '.($agentdata["timezone"] == "7.0"  ? "selected" : "").'>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
									<option value="8.0" '.($agentdata["timezone"] == "8.0"  ? "selected" : "").'>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
									<option value="9.0" '.($agentdata["timezone"] == "9.0"  ? "selected" : "").'>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
									<option value="9.5" '.($agentdata["timezone"] == "9.5"  ? "selected" : "").'>(GMT +9:30) Adelaide, Darwin</option>
									<option value="10.0" '.($agentdata["timezone"] == "10.0"  ? "selected" : "").'>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
									<option value="11.0" '.($agentdata["timezone"] == "11.0"  ? "selected" : "").'>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
									<option value="12.0" '.($agentdata["timezone"] == "12.0"  ? "selected" : "").'>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
								</select>
							</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
								<label> </label>
								<div>
									<label>Email *</label>
									<input id="csemail" name="csemail" type="text" value="'.$agentdata['csemail'].'" class="form-control required">
									</span>
								</div>
							</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
								<label> </label>
								<div>
									<label>Business Type *</label>
									<input id="business_type" name="business_type" type="text" value="'.$agentdata['business_type'].'" class="form-control required">
									</span>
								</div>
							</div>
							</div>
							<div class="form-group">
							<button type="submit" class="btn btn-primary block full-width m-b">Save</button>
							</form>
						</div>
					</div>
		</fieldset>
		<script>
		$(document).ready(function() {
			$("#country").on("change", function() {
				var states;
				switch(this.value) {
					case "US":
						states = "<label>State *</label><select class="form-control required" name="us_state" id="us_state" aria-required="true"><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></select>";
						break;
					case "CA":
						states = "<label>State *</label><select class="form-control required" name="us_state" id="us_state" aria-required="true"><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland and Labrador</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon</option></select>";
						break;
					default:
						states = "<label>Providence *</label><input type="text" class="form-control required" name="us_state" id="us_state" aria-required="true">";
						break;
				} 
			  $("#statebox").html(states);
			});
		});	
	</script>
			';
		}else{
			return 'No Data Found';
		}
	}elseif(isset($merchantid) && $merchantid != ''){
			$db->where("idmerchants",$merchantid);
		$merchantdata = $db->getOne("merchants");
		if($db->count > 0){
			return '
			<div class="panel panel-primary">
		<div class="panel-heading">
			Merchant Information
		</div>
		<div class="panel-body">
		<fieldset>
			<h2>Merchant Information</h2>
			<div class="row">
			<form class="m-t" role="form" action="" method="POST">
				<input name="agentsaveinfo" id="agentsaveinfo" value="agentsaveinfo" type="hidden"/>
				<div class="col-md-4">
					<div class="form-group">
						<label>Bussiness Name *</label>
						<input id="agentname" name="agentname" type="text" value="'.$merchantdata['merchant_name'].'" class="form-control required">
					</div>
					<div class="form-group">
						<label>Country *</label>
						<select name="country" id="country" class="form-control required">
							<option value="US" '.($merchantdata["country"] == "US"  ? "selected" : "").'>United States of America</option>
							<option value="AF" '.($merchantdata["country"] == "AF"  ? "selected" : "").'>Afghanistan</option>
							<option value="AL" '.($merchantdata["country"] == "AL"  ? "selected" : "").'>Albania</option>
							<option value="DZ" '.($merchantdata["country"] == "DZ"  ? "selected" : "").'>Algeria</option>
							<option value="AS" '.($merchantdata["country"] == "AS"  ? "selected" : "").'>American Samoa</option>
							<option value="AD" '.($merchantdata["country"] == "AD"  ? "selected" : "").'>Andorra</option>
							<option value="AO" '.($merchantdata["country"] == "AO"  ? "selected" : "").'>Angola</option>
							<option value="AI" '.($merchantdata["country"] == "AI"  ? "selected" : "").'>Anguilla</option>
							<option value="AG" '.($merchantdata["country"] == "AG"  ? "selected" : "").'>Antigua &amp; Barbuda</option>
							<option value="AR" '.($merchantdata["country"] == "AR"  ? "selected" : "").'>Argentina</option>
							<option value="AA" '.($merchantdata["country"] == "AA"  ? "selected" : "").'>Armenia</option>
							<option value="AW" '.($merchantdata["country"] == "AW"  ? "selected" : "").'>Aruba</option>
							<option value="AU" '.($merchantdata["country"] == "AU"  ? "selected" : "").'>Australia</option>
							<option value="AT" '.($merchantdata["country"] == "AT"  ? "selected" : "").'>Austria</option>
							<option value="AZ" '.($merchantdata["country"] == "AZ"  ? "selected" : "").'>Azerbaijan</option>
							<option value="BS" '.($merchantdata["country"] == "BS"  ? "selected" : "").'>Bahamas</option>
							<option value="BH" '.($merchantdata["country"] == "BH"  ? "selected" : "").'>Bahrain</option>
							<option value="BD" '.($merchantdata["country"] == "BD"  ? "selected" : "").'>Bangladesh</option>
							<option value="BB" '.($merchantdata["country"] == "BB"  ? "selected" : "").'>Barbados</option>
							<option value="BY" '.($merchantdata["country"] == "BY"  ? "selected" : "").'>Belarus</option>
							<option value="BE" '.($merchantdata["country"] == "BE"  ? "selected" : "").'>Belgium</option>
							<option value="BZ" '.($merchantdata["country"] == "BZ"  ? "selected" : "").'>Belize</option>
							<option value="BJ" '.($merchantdata["country"] == "BJ"  ? "selected" : "").'>Benin</option>
							<option value="BM" '.($merchantdata["country"] == "BM"  ? "selected" : "").'>Bermuda</option>
							<option value="BT" '.($merchantdata["country"] == "BT"  ? "selected" : "").'>Bhutan</option>
							<option value="BO" '.($merchantdata["country"] == "BO"  ? "selected" : "").'>Bolivia</option>
							<option value="BL" '.($merchantdata["country"] == "BL"  ? "selected" : "").'>Bonaire</option>
							<option value="BA" '.($merchantdata["country"] == "BA"  ? "selected" : "").'>Bosnia &amp; Herzegovina</option>
							<option value="BW" '.($merchantdata["country"] == "BW"  ? "selected" : "").'>Botswana</option>
							<option value="BR" '.($merchantdata["country"] == "BR"  ? "selected" : "").'>Brazil</option>
							<option value="BC" '.($merchantdata["country"] == "BC"  ? "selected" : "").'>British Indian Ocean Ter</option>
							<option value="BN" '.($merchantdata["country"] == "BN"  ? "selected" : "").'>Brunei</option>
							<option value="BG" '.($merchantdata["country"] == "BG"  ? "selected" : "").'>Bulgaria</option>
							<option value="BF" '.($merchantdata["country"] == "BF"  ? "selected" : "").'>Burkina Faso</option>
							<option value="BI" '.($merchantdata["country"] == "BI"  ? "selected" : "").'>Burundi</option>
							<option value="KH" '.($merchantdata["country"] == "KH"  ? "selected" : "").'>Cambodia</option>
							<option value="CM" '.($merchantdata["country"] == "CM"  ? "selected" : "").'>Cameroon</option>
							<option value="CA" '.($merchantdata["country"] == "CA"  ? "selected" : "").'>Canada</option>
							<option value="IC" '.($merchantdata["country"] == "IC"  ? "selected" : "").'>Canary Islands</option>
							<option value="CV" '.($merchantdata["country"] == "CV"  ? "selected" : "").'>Cape Verde</option>
							<option value="KY" '.($merchantdata["country"] == "KY"  ? "selected" : "").'>Cayman Islands</option>
							<option value="CF" '.($merchantdata["country"] == "CF"  ? "selected" : "").'>Central African Republic</option>
							<option value="TD" '.($merchantdata["country"] == "TD"  ? "selected" : "").'>Chad</option>
							<option value="CD" '.($merchantdata["country"] == "CD"  ? "selected" : "").'>Channel Islands</option>
							<option value="CL" '.($merchantdata["country"] == "CL"  ? "selected" : "").'>Chile</option>
							<option value="CN" '.($merchantdata["country"] == "CN"  ? "selected" : "").'>China</option>
							<option value="CI" '.($merchantdata["country"] == "CI"  ? "selected" : "").'>Christmas Island</option>
							<option value="CS" '.($merchantdata["country"] == "CS"  ? "selected" : "").'>Cocos Island</option>
							<option value="CO" '.($merchantdata["country"] == "CO"  ? "selected" : "").'>Colombia</option>
							<option value="CC" '.($merchantdata["country"] == "CC"  ? "selected" : "").'>Comoros</option>
							<option value="CG" '.($merchantdata["country"] == "CG"  ? "selected" : "").'>Congo</option>
							<option value="CK" '.($merchantdata["country"] == "CK"  ? "selected" : "").'>Cook Islands</option>
							<option value="CR" '.($merchantdata["country"] == "CR"  ? "selected" : "").'>Costa Rica</option>
							<option value="CT" '.($merchantdata["country"] == "CT"  ? "selected" : "").'>Cote D Ivoire</option>
							<option value="HR" '.($merchantdata["country"] == "HR"  ? "selected" : "").'>Croatia</option>
							<option value="CU" '.($merchantdata["country"] == "CU"  ? "selected" : "").'>Cuba</option>
							<option value="CB" '.($merchantdata["country"] == "CB"  ? "selected" : "").'>Curacao</option>
							<option value="CY" '.($merchantdata["country"] == "CY"  ? "selected" : "").'>Cyprus</option>
							<option value="CZ" '.($merchantdata["country"] == "CZ"  ? "selected" : "").'>Czech Republic</option>
							<option value="DK" '.($merchantdata["country"] == "DK"  ? "selected" : "").'>Denmark</option>
							<option value="DJ" '.($merchantdata["country"] == "DJ"  ? "selected" : "").'>Djibouti</option>
							<option value="DM" '.($merchantdata["country"] == "DM"  ? "selected" : "").'>Dominica</option>
							<option value="DO" '.($merchantdata["country"] == "DO"  ? "selected" : "").'>Dominican Republic</option>
							<option value="TM" '.($merchantdata["country"] == "TM"  ? "selected" : "").'>East Timor</option>
							<option value="EC" '.($merchantdata["country"] == "EC"  ? "selected" : "").'>Ecuador</option>
							<option value="EG" '.($merchantdata["country"] == "EG"  ? "selected" : "").'>Egypt</option>
							<option value="SV" '.($merchantdata["country"] == "SV"  ? "selected" : "").'>El Salvador</option>
							<option value="GQ" '.($merchantdata["country"] == "GQ"  ? "selected" : "").'>Equatorial Guinea</option>
							<option value="ER" '.($merchantdata["country"] == "ER"  ? "selected" : "").'>Eritrea</option>
							<option value="EE" '.($merchantdata["country"] == "EE"  ? "selected" : "").'>Estonia</option>
							<option value="ET" '.($merchantdata["country"] == "ET"  ? "selected" : "").'>Ethiopia</option>
							<option value="FA" '.($merchantdata["country"] == "FA"  ? "selected" : "").'>Falkland Islands</option>
							<option value="FO" '.($merchantdata["country"] == "FO"  ? "selected" : "").'>Faroe Islands</option>
							<option value="FJ" '.($merchantdata["country"] == "FJ"  ? "selected" : "").'>Fiji</option>
							<option value="FI" '.($merchantdata["country"] == "FI"  ? "selected" : "").'>Finland</option>
							<option value="FR" '.($merchantdata["country"] == "FR"  ? "selected" : "").'>France</option>
							<option value="GF" '.($merchantdata["country"] == "GF"  ? "selected" : "").'>French Guiana</option>
							<option value="PF" '.($merchantdata["country"] == "PF"  ? "selected" : "").'>French Polynesia</option>
							<option value="FS" '.($merchantdata["country"] == "FS"  ? "selected" : "").'>French Southern Ter</option>
							<option value="GA" '.($merchantdata["country"] == "GA"  ? "selected" : "").'>Gabon</option>
							<option value="GM" '.($merchantdata["country"] == "GM"  ? "selected" : "").'>Gambia</option>
							<option value="GE" '.($merchantdata["country"] == "GE"  ? "selected" : "").'>Georgia</option>
							<option value="DE" '.($merchantdata["country"] == "DE"  ? "selected" : "").'>Germany</option>
							<option value="GH" '.($merchantdata["country"] == "GH"  ? "selected" : "").'>Ghana</option>
							<option value="GI" '.($merchantdata["country"] == "GI"  ? "selected" : "").'>Gibraltar</option>
							<option value="GB" '.($merchantdata["country"] == "GB"  ? "selected" : "").'>Great Britain</option>
							<option value="GR" '.($merchantdata["country"] == "GR"  ? "selected" : "").'>Greece</option>
							<option value="GL" '.($merchantdata["country"] == "GL"  ? "selected" : "").'>Greenland</option>
							<option value="GD" '.($merchantdata["country"] == "GD"  ? "selected" : "").'>Grenada</option>
							<option value="GP" '.($merchantdata["country"] == "GP"  ? "selected" : "").'>Guadeloupe</option>
							<option value="GU" '.($merchantdata["country"] == "GU"  ? "selected" : "").'>Guam</option>
							<option value="GT" '.($merchantdata["country"] == "GT"  ? "selected" : "").'>Guatemala</option>
							<option value="GN" '.($merchantdata["country"] == "GN"  ? "selected" : "").'>Guinea</option>
							<option value="GY" '.($merchantdata["country"] == "GY"  ? "selected" : "").'>Guyana</option>
							<option value="HT" '.($merchantdata["country"] == "HT"  ? "selected" : "").'>Haiti</option>
							<option value="HW" '.($merchantdata["country"] == "HW"  ? "selected" : "").'>Hawaii</option>
							<option value="HN" '.($merchantdata["country"] == "HN"  ? "selected" : "").'>Honduras</option>
							<option value="HK" '.($merchantdata["country"] == "HK"  ? "selected" : "").'>Hong Kong</option>
							<option value="HU" '.($merchantdata["country"] == "HU"  ? "selected" : "").'>Hungary</option>
							<option value="IS" '.($merchantdata["country"] == "IS"  ? "selected" : "").'>Iceland</option>
							<option value="IN" '.($merchantdata["country"] == "IN"  ? "selected" : "").'>India</option>
							<option value="ID" '.($merchantdata["country"] == "ID"  ? "selected" : "").'>Indonesia</option>
							<option value="IA" '.($merchantdata["country"] == "IA"  ? "selected" : "").'>Iran</option>
							<option value="IQ" '.($merchantdata["country"] == "IQ"  ? "selected" : "").'>Iraq</option>
							<option value="IR" '.($merchantdata["country"] == "IR"  ? "selected" : "").'>Ireland</option>
							<option value="IM" '.($merchantdata["country"] == "IM"  ? "selected" : "").'>Isle of Man</option>
							<option value="IL" '.($merchantdata["country"] == "IL"  ? "selected" : "").'>Israel</option>
							<option value="IT" '.($merchantdata["country"] == "IT"  ? "selected" : "").'>Italy</option>
							<option value="JM" '.($merchantdata["country"] == "JM"  ? "selected" : "").'>Jamaica</option>
							<option value="JP" '.($merchantdata["country"] == "JP"  ? "selected" : "").'>Japan</option>
							<option value="JO" '.($merchantdata["country"] == "JO"  ? "selected" : "").'>Jordan</option>
							<option value="KZ" '.($merchantdata["country"] == "KZ"  ? "selected" : "").'>Kazakhstan</option>
							<option value="KE" '.($merchantdata["country"] == "KE"  ? "selected" : "").'>Kenya</option>
							<option value="KI" '.($merchantdata["country"] == "KI"  ? "selected" : "").'>Kiribati</option>
							<option value="NK" '.($merchantdata["country"] == "NK"  ? "selected" : "").'>Korea North</option>
							<option value="KS" '.($merchantdata["country"] == "KS"  ? "selected" : "").'>Korea South</option>
							<option value="KW" '.($merchantdata["country"] == "KW"  ? "selected" : "").'>Kuwait</option>
							<option value="KG" '.($merchantdata["country"] == "KG"  ? "selected" : "").'>Kyrgyzstan</option>
							<option value="LA" '.($merchantdata["country"] == "LA"  ? "selected" : "").'>Laos</option>
							<option value="LV" '.($merchantdata["country"] == "LV"  ? "selected" : "").'>Latvia</option>
							<option value="LB" '.($merchantdata["country"] == "LB"  ? "selected" : "").'>Lebanon</option>
							<option value="LS" '.($merchantdata["country"] == "LS"  ? "selected" : "").'>Lesotho</option>
							<option value="LR" '.($merchantdata["country"] == "LR"  ? "selected" : "").'>Liberia</option>
							<option value="LY" '.($merchantdata["country"] == "LY"  ? "selected" : "").'>Libya</option>
							<option value="LI" '.($merchantdata["country"] == "LI"  ? "selected" : "").'>Liechtenstein</option>
							<option value="LT" '.($merchantdata["country"] == "LT"  ? "selected" : "").'>Lithuania</option>
							<option value="LU" '.($merchantdata["country"] == "LU"  ? "selected" : "").'>Luxembourg</option>
							<option value="MO" '.($merchantdata["country"] == "MO"  ? "selected" : "").'>Macau</option>
							<option value="MK" '.($merchantdata["country"] == "MK"  ? "selected" : "").'>Macedonia</option>
							<option value="MG" '.($merchantdata["country"] == "MG"  ? "selected" : "").'>Madagascar</option>
							<option value="MY" '.($merchantdata["country"] == "MY"  ? "selected" : "").'>Malaysia</option>
							<option value="MW" '.($merchantdata["country"] == "MW"  ? "selected" : "").'>Malawi</option>
							<option value="MV" '.($merchantdata["country"] == "MV"  ? "selected" : "").'>Maldives</option>
							<option value="ML" '.($merchantdata["country"] == "ML"  ? "selected" : "").'>Mali</option>
							<option value="MT" '.($merchantdata["country"] == "MT"  ? "selected" : "").'>Malta</option>
							<option value="MH" '.($merchantdata["country"] == "MH"  ? "selected" : "").'>Marshall Islands</option>
							<option value="MQ" '.($merchantdata["country"] == "MQ"  ? "selected" : "").'>Martinique</option>
							<option value="MR" '.($merchantdata["country"] == "MR"  ? "selected" : "").'>Mauritania</option>
							<option value="MU" '.($merchantdata["country"] == "MU"  ? "selected" : "").'>Mauritius</option>
							<option value="ME" '.($merchantdata["country"] == "ME"  ? "selected" : "").'>Mayotte</option>
							<option value="MX" '.($merchantdata["country"] == "MX"  ? "selected" : "").'>Mexico</option>
							<option value="MI" '.($merchantdata["country"] == "MI"  ? "selected" : "").'>Midway Islands</option>
							<option value="MD" '.($merchantdata["country"] == "MD"  ? "selected" : "").'>Moldova</option>
							<option value="MC" '.($merchantdata["country"] == "MC"  ? "selected" : "").'>Monaco</option>
							<option value="MN" '.($merchantdata["country"] == "MN"  ? "selected" : "").'>Mongolia</option>
							<option value="MS" '.($merchantdata["country"] == "MS"  ? "selected" : "").'>Montserrat</option>
							<option value="MA" '.($merchantdata["country"] == "MA"  ? "selected" : "").'>Morocco</option>
							<option value="MZ" '.($merchantdata["country"] == "MZ"  ? "selected" : "").'>Mozambique</option>
							<option value="MM" '.($merchantdata["country"] == "MM"  ? "selected" : "").'>Myanmar</option>
							<option value="NA" '.($merchantdata["country"] == "NA"  ? "selected" : "").'>Nambia</option>
							<option value="NU" '.($merchantdata["country"] == "NU"  ? "selected" : "").'>Nauru</option>
							<option value="NP" '.($merchantdata["country"] == "NP"  ? "selected" : "").'>Nepal</option>
							<option value="AN" '.($merchantdata["country"] == "AN"  ? "selected" : "").'>Netherland Antilles</option>
							<option value="NL" '.($merchantdata["country"] == "NL"  ? "selected" : "").'>Netherlands (Holland, Europe)</option>
							<option value="NV" '.($merchantdata["country"] == "NV"  ? "selected" : "").'>Nevis</option>
							<option value="NC" '.($merchantdata["country"] == "NC"  ? "selected" : "").'>New Caledonia</option>
							<option value="NZ" '.($merchantdata["country"] == "NZ"  ? "selected" : "").'>New Zealand</option>
							<option value="NI" '.($merchantdata["country"] == "NI"  ? "selected" : "").'>Nicaragua</option>
							<option value="NE" '.($merchantdata["country"] == "NE"  ? "selected" : "").'>Niger</option>
							<option value="NG" '.($merchantdata["country"] == "NG"  ? "selected" : "").'>Nigeria</option>
							<option value="NW" '.($merchantdata["country"] == "NW"  ? "selected" : "").'>Niue</option>
							<option value="NF" '.($merchantdata["country"] == "NF"  ? "selected" : "").'>Norfolk Island</option>
							<option value="NO" '.($merchantdata["country"] == "NO"  ? "selected" : "").'>Norway</option>
							<option value="OM" '.($merchantdata["country"] == "OM"  ? "selected" : "").'>Oman</option>
							<option value="PK" '.($merchantdata["country"] == "PK"  ? "selected" : "").'>Pakistan</option>
							<option value="PW" '.($merchantdata["country"] == "PW"  ? "selected" : "").'>Palau Island</option>
							<option value="PS" '.($merchantdata["country"] == "PS"  ? "selected" : "").'>Palestine</option>
							<option value="PA" '.($merchantdata["country"] == "PA"  ? "selected" : "").'>Panama</option>
							<option value="PG" '.($merchantdata["country"] == "PG"  ? "selected" : "").'>Papua New Guinea</option>
							<option value="PY" '.($merchantdata["country"] == "PY"  ? "selected" : "").'>Paraguay</option>
							<option value="PE" '.($merchantdata["country"] == "PE"  ? "selected" : "").'>Peru</option>
							<option value="PH" '.($merchantdata["country"] == "PH"  ? "selected" : "").'>Philippines</option>
							<option value="PO" '.($merchantdata["country"] == "PO"  ? "selected" : "").'>Pitcairn Island</option>
							<option value="PL" '.($merchantdata["country"] == "PL"  ? "selected" : "").'>Poland</option>
							<option value="PT" '.($merchantdata["country"] == "PT"  ? "selected" : "").'>Portugal</option>
							<option value="PR" '.($merchantdata["country"] == "PR"  ? "selected" : "").'>Puerto Rico</option>
							<option value="QA" '.($merchantdata["country"] == "QA"  ? "selected" : "").'>Qatar</option>
							<option value="ME" '.($merchantdata["country"] == "ME"  ? "selected" : "").'>Republic of Montenegro</option>
							<option value="RS" '.($merchantdata["country"] == "RS"  ? "selected" : "").'>Republic of Serbia</option>
							<option value="RE" '.($merchantdata["country"] == "RE"  ? "selected" : "").'>Reunion</option>
							<option value="RO" '.($merchantdata["country"] == "RO"  ? "selected" : "").'>Romania</option>
							<option value="RU" '.($merchantdata["country"] == "RU"  ? "selected" : "").'>Russia</option>
							<option value="RW" '.($merchantdata["country"] == "RW"  ? "selected" : "").'>Rwanda</option>
							<option value="NT" '.($merchantdata["country"] == "NT"  ? "selected" : "").'>St Barthelemy</option>
							<option value="EU" '.($merchantdata["country"] == "EU"  ? "selected" : "").'>St Eustatius</option>
							<option value="HE" '.($merchantdata["country"] == "HE"  ? "selected" : "").'>St Helena</option>
							<option value="KN" '.($merchantdata["country"] == "KN"  ? "selected" : "").'>St Kitts-Nevis</option>
							<option value="LC" '.($merchantdata["country"] == "LC"  ? "selected" : "").'>St Lucia</option>
							<option value="MB" '.($merchantdata["country"] == "MB"  ? "selected" : "").'>St Maarten</option>
							<option value="PM" '.($merchantdata["country"] == "PM"  ? "selected" : "").'>St Pierre &amp; Miquelon</option>
							<option value="VC" '.($merchantdata["country"] == "VC"  ? "selected" : "").'>St Vincent &amp; Grenadines</option>
							<option value="SP" '.($merchantdata["country"] == "SP"  ? "selected" : "").'>Saipan</option>
							<option value="SO" '.($merchantdata["country"] == "SO"  ? "selected" : "").'>Samoa</option>
							<option value="AS" '.($merchantdata["country"] == "AS"  ? "selected" : "").'>Samoa American</option>
							<option value="SM" '.($merchantdata["country"] == "SM"  ? "selected" : "").'>San Marino</option>
							<option value="ST" '.($merchantdata["country"] == "ST"  ? "selected" : "").'>Sao Tome &amp; Principe</option>
							<option value="SA" '.($merchantdata["country"] == "SA"  ? "selected" : "").'>Saudi Arabia</option>
							<option value="SN" '.($merchantdata["country"] == "SN"  ? "selected" : "").'>Senegal</option>
							<option value="RS" '.($merchantdata["country"] == "RS"  ? "selected" : "").'>Serbia</option>
							<option value="SC" '.($merchantdata["country"] == "SC"  ? "selected" : "").'>Seychelles</option>
							<option value="SL" '.($merchantdata["country"] == "SL"  ? "selected" : "").'>Sierra Leone</option>
							<option value="SG" '.($merchantdata["country"] == "SG"  ? "selected" : "").'>Singapore</option>
							<option value="SK" '.($merchantdata["country"] == "SK"  ? "selected" : "").'>Slovakia</option>
							<option value="SI" '.($merchantdata["country"] == "SI"  ? "selected" : "").'>Slovenia</option>
							<option value="SB" '.($merchantdata["country"] == "SB"  ? "selected" : "").'>Solomon Islands</option>
							<option value="OI" '.($merchantdata["country"] == "OI"  ? "selected" : "").'>Somalia</option>
							<option value="ZA" '.($merchantdata["country"] == "ZA"  ? "selected" : "").'>South Africa</option>
							<option value="ES" '.($merchantdata["country"] == "ES"  ? "selected" : "").'>Spain</option>
							<option value="LK" '.($merchantdata["country"] == "LK"  ? "selected" : "").'>Sri Lanka</option>
							<option value="SD" '.($merchantdata["country"] == "SD"  ? "selected" : "").'>Sudan</option>
							<option value="SR" '.($merchantdata["country"] == "SR"  ? "selected" : "").'>Suriname</option>
							<option value="SZ" '.($merchantdata["country"] == "SZ"  ? "selected" : "").'>Swaziland</option>
							<option value="SE" '.($merchantdata["country"] == "SE"  ? "selected" : "").'>Sweden</option>
							<option value="CH" '.($merchantdata["country"] == "CH"  ? "selected" : "").'>Switzerland</option>
							<option value="SY" '.($merchantdata["country"] == "SY"  ? "selected" : "").'>Syria</option>
							<option value="TA" '.($merchantdata["country"] == "TA"  ? "selected" : "").'>Tahiti</option>
							<option value="TW" '.($merchantdata["country"] == "TW"  ? "selected" : "").'>Taiwan</option>
							<option value="TJ" '.($merchantdata["country"] == "TJ"  ? "selected" : "").'>Tajikistan</option>
							<option value="TZ" '.($merchantdata["country"] == "TZ"  ? "selected" : "").'>Tanzania</option>
							<option value="TH" '.($merchantdata["country"] == "TH"  ? "selected" : "").'>Thailand</option>
							<option value="TG" '.($merchantdata["country"] == "TG"  ? "selected" : "").'>Togo</option>
							<option value="TK" '.($merchantdata["country"] == "TK"  ? "selected" : "").'>Tokelau</option>
							<option value="TO" '.($merchantdata["country"] == "TO"  ? "selected" : "").'>Tonga</option>
							<option value="TT" '.($merchantdata["country"] == "TT"  ? "selected" : "").'>Trinidad &amp; Tobago</option>
							<option value="TN" '.($merchantdata["country"] == "TN"  ? "selected" : "").'>Tunisia</option>
							<option value="TR" '.($merchantdata["country"] == "TR"  ? "selected" : "").'>Turkey</option>
							<option value="TU" '.($merchantdata["country"] == "TU"  ? "selected" : "").'>Turkmenistan</option>
							<option value="TC" '.($merchantdata["country"] == "TC"  ? "selected" : "").'>Turks &amp; Caicos Is</option>
							<option value="TV" '.($merchantdata["country"] == "TV"  ? "selected" : "").'>Tuvalu</option>
							<option value="UG" '.($merchantdata["country"] == "UG"  ? "selected" : "").'>Uganda</option>
							<option value="UA" '.($merchantdata["country"] == "UA"  ? "selected" : "").'>Ukraine</option>
							<option value="AE" '.($merchantdata["country"] == "AE"  ? "selected" : "").'>United Arab Emirates</option>
							<option value="GB" '.($merchantdata["country"] == "GB"  ? "selected" : "").'>United Kingdom</option>
							<option value="UY" '.($merchantdata["country"] == "UY"  ? "selected" : "").'>Uruguay</option>
							<option value="UZ" '.($merchantdata["country"] == "UZ"  ? "selected" : "").'>Uzbekistan</option>
							<option value="VU" '.($merchantdata["country"] == "VU"  ? "selected" : "").'>Vanuatu</option>
							<option value="VS" '.($merchantdata["country"] == "VS"  ? "selected" : "").'>Vatican City State</option>
							<option value="VE" '.($merchantdata["country"] == "VE"  ? "selected" : "").'>Venezuela</option>
							<option value="VN" '.($merchantdata["country"] == "VN"  ? "selected" : "").'>Vietnam</option>
							<option value="VB" '.($merchantdata["country"] == "VB"  ? "selected" : "").'>Virgin Islands (Brit)</option>
							<option value="VA" '.($merchantdata["country"] == "VA"  ? "selected" : "").'>Virgin Islands (USA)</option>
							<option value="WK" '.($merchantdata["country"] == "WK"  ? "selected" : "").'>Wake Island</option>
							<option value="WF" '.($merchantdata["country"] == "WF"  ? "selected" : "").'>Wallis &amp; Futana Is</option>
							<option value="YE" '.($merchantdata["country"] == "YE"  ? "selected" : "").'>Yemen</option>
							<option value="ZR" '.($merchantdata["country"] == "ZR"  ? "selected" : "").'>Zaire</option>
							<option value="ZM" '.($merchantdata["country"] == "ZM"  ? "selected" : "").'>Zambia</option>
							<option value="ZW" '.($merchantdata["country"] == "ZW"  ? "selected" : "").'>Zimbabwe</option>
						</select>
					</div>
					<div class="form-group" id="statebox">
						<label>State</label>
						<select id="us_state" name="us_state" class="form-control">
							<option value="AL" '.($merchantdata["us_state"] == "AL"  ? "selected" : "").'>Alabama</option>
							<option value="AK" '.($merchantdata["us_state"] == "AK"  ? "selected" : "").'>Alaska</option>
							<option value="AZ" '.($merchantdata["us_state"] == "AZ"  ? "selected" : "").'>Arizona</option>
							<option value="AR" '.($merchantdata["us_state"] == "AR"  ? "selected" : "").'>Arkansas</option>
							<option value="CA" '.($merchantdata["us_state"] == "CA"  ? "selected" : "").'>California</option>
							<option value="CO" '.($merchantdata["us_state"] == "CO"  ? "selected" : "").'>Colorado</option>
							<option value="CT" '.($merchantdata["us_state"] == "CT"  ? "selected" : "").'>Connecticut</option>
							<option value="DE" '.($merchantdata["us_state"] == "DE"  ? "selected" : "").'>Delaware</option>
							<option value="DC" '.($merchantdata["us_state"] == "DC"  ? "selected" : "").'>District Of Columbia</option>
							<option value="FL" '.($merchantdata["us_state"] == "FL"  ? "selected" : "").'>Florida</option>
							<option value="GA" '.($merchantdata["us_state"] == "GA"  ? "selected" : "").'>Georgia</option>
							<option value="HI" '.($merchantdata["us_state"] == "HI"  ? "selected" : "").'>Hawaii</option>
							<option value="ID" '.($merchantdata["us_state"] == "ID"  ? "selected" : "").'>Idaho</option>
							<option value="IL" '.($merchantdata["us_state"] == "IL"  ? "selected" : "").'>Illinois</option>
							<option value="IN" '.($merchantdata["us_state"] == "IN"  ? "selected" : "").'>Indiana</option>
							<option value="IA" '.($merchantdata["us_state"] == "IA"  ? "selected" : "").'>Iowa</option>
							<option value="KS" '.($merchantdata["us_state"] == "KS"  ? "selected" : "").'>Kansas</option>
							<option value="KY" '.($merchantdata["us_state"] == "KY"  ? "selected" : "").'>Kentucky</option>
							<option value="LA" '.($merchantdata["us_state"] == "LA"  ? "selected" : "").'>Louisiana</option>
							<option value="ME" '.($merchantdata["us_state"] == "ME"  ? "selected" : "").'>Maine</option>
							<option value="MD" '.($merchantdata["us_state"] == "MD"  ? "selected" : "").'>Maryland</option>
							<option value="MA" '.($merchantdata["us_state"] == "MA"  ? "selected" : "").'>Massachusetts</option>
							<option value="MI" '.($merchantdata["us_state"] == "MI"  ? "selected" : "").'>Michigan</option>
							<option value="MN" '.($merchantdata["us_state"] == "MN"  ? "selected" : "").'>Minnesota</option>
							<option value="MS" '.($merchantdata["us_state"] == "MS"  ? "selected" : "").'>Mississippi</option>
							<option value="MO" '.($merchantdata["us_state"] == "MO"  ? "selected" : "").'>Missouri</option>
							<option value="MT" '.($merchantdata["us_state"] == "MT"  ? "selected" : "").'>Montana</option>
							<option value="NE" '.($merchantdata["us_state"] == "NE"  ? "selected" : "").'>Nebraska</option>
							<option value="NV" '.($merchantdata["us_state"] == "NV"  ? "selected" : "").'>Nevada</option>
							<option value="NH" '.($merchantdata["us_state"] == "NH"  ? "selected" : "").'>New Hampshire</option>
							<option value="NJ" '.($merchantdata["us_state"] == "NJ"  ? "selected" : "").'>New Jersey</option>
							<option value="NM" '.($merchantdata["us_state"] == "NM"  ? "selected" : "").'>New Mexico</option>
							<option value="NY" '.($merchantdata["us_state"] == "NY"  ? "selected" : "").'>New York</option>
							<option value="NC" '.($merchantdata["us_state"] == "NC"  ? "selected" : "").'>North Carolina</option>
							<option value="ND" '.($merchantdata["us_state"] == "ND"  ? "selected" : "").'>North Dakota</option>
							<option value="OH" '.($merchantdata["us_state"] == "OH"  ? "selected" : "").'>Ohio</option>
							<option value="OK" '.($merchantdata["us_state"] == "OK"  ? "selected" : "").'>Oklahoma</option>
							<option value="OR" '.($merchantdata["us_state"] == "OR"  ? "selected" : "").'>Oregon</option>
							<option value="PA" '.($merchantdata["us_state"] == "PA"  ? "selected" : "").'>Pennsylvania</option>
							<option value="RI" '.($merchantdata["us_state"] == "RI"  ? "selected" : "").'>Rhode Island</option>
							<option value="SC" '.($merchantdata["us_state"] == "SC"  ? "selected" : "").'>South Carolina</option>
							<option value="SD" '.($merchantdata["us_state"] == "SD"  ? "selected" : "").'>South Dakota</option>
							<option value="TN" '.($merchantdata["us_state"] == "TN"  ? "selected" : "").'>Tennessee</option>
							<option value="TX" '.($merchantdata["us_state"] == "TX"  ? "selected" : "").'>Texas</option>
							<option value="UT" '.($merchantdata["us_state"] == "UT"  ? "selected" : "").'>Utah</option>
							<option value="VT" '.($merchantdata["us_state"] == "VT"  ? "selected" : "").'>Vermont</option>
							<option value="VA" '.($merchantdata["us_state"] == "VA"  ? "selected" : "").'>Virginia</option>
							<option value="WA" '.($merchantdata["us_state"] == "WA"  ? "selected" : "").'>Washington</option>
							<option value="WV" '.($merchantdata["us_state"] == "WV"  ? "selected" : "").'>West Virginia</option>
							<option value="WI" '.($merchantdata["us_state"] == "WI"  ? "selected" : "").'>Wisconsin</option>
							<option value="WY" '.($merchantdata["us_state"] == "WY"  ? "selected" : "").'>Wyoming</option>
						</select>
					</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
						<label>Customer Service Information:</label>
						<div class="row">
							<div class="col-md-6">
													<label>Fist Name *</label>
													<input id="cs_first_name" name="cs_first_name" type="text" value="'.$merchantdata['cs_first_name'].'" class="form-control required"></div>
							<div class="col-md-6">
													<label>Last Name *</label>
													<input id="cs_last_name" name="cs_last_name" type="text" value="'.$merchantdata['cs_last_name'].'" class="form-control required"></div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div><label>Business Information:</label>
						<div class="row">
							<div class="col-md-6">
													<label>Legal Name *</label>
													<input id="legal_name" name="legal_name" type="text" value="'.$merchantdata['legal_name'].'" class="form-control required"></div>
							<div class="col-md-6">
													<label>Tax ID *</label>
													<input id="tax_id" name="tax_id" type="text" value="'.$merchantdata['tax_id'].'" class="form-control required"></div>
						</div>
					</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Address *</label>
							<input id="address1" name="address1" value="'.$merchantdata['address1'].'" type="text" class="form-control required">
						</div>
						<div class="form-group">
							<label>Address (Cont)</label>
							<input id="address2" name="address2" value="'.$merchantdata['address2'].'" type="text" class="form-control">
						</div>
						<div class="form-group">
							<label>City *</label>
							<input id="city" name="city" type="text" value="'.$merchantdata['city'].'" class="form-control required" aria-required="true">
						</div>
						<div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
							<label> </label>
							<div class="row">
								<div class="col-md-6">
														<label>Phone *</label>
														<input id="csphone" name="csphone" type="text" value="'.$merchantdata['csphone'].'" class="form-control required"></div>
								<div class="col-md-6">
														<label>Fax *</label>
														<input id="cs_fax" name="cs_fax" type="text" value="'.$merchantdata['cs_fax'].'" class="form-control required"></div>
							</div>
						</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
							<label> </label>
							<div class="row">
								<div class="col-md-6">
														<label>Routing # *</label>
														<input id="routing" name="routing" type="text" value="'.$merchantdata['routing'].'" class="form-control required"></div>
								<div class="col-md-6">
														<label>Account # *</label>
														<input id="account" name="account" type="text" value="'.$merchantdata['account'].'" class="form-control required"></div>
							</div>
						</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Zip/Postal Code *</label>
								<input id="zippostalcode" name="zippostalcode" value="'.$merchantdata['zippostalcode'].'" type="text" class="form-control required" aria-required="true">
							</div>
							<div class="form-group">
								<label>Website Address</label>
								<input id="website" name="website" value="'.$merchantdata['website'].'" type="text" class="form-control" aria-required="true">
							</div>
							<div class="form-group">
								<label>Time Zone</label>
								<select id="tz_name" name="tz_name" class="form-control required">
									<option value="-12.0" '.($merchantdata["timezone"] == "-12.0"  ? "selected" : "").'>(GMT -12:00) Eniwetok, Kwajalein</option>
									<option value="-11.0" '.($merchantdata["timezone"] == "-11.0"  ? "selected" : "").'>(GMT -11:00) Midway Island, Samoa</option>
									<option value="-10.0" '.($merchantdata["timezone"] == "-10.0"  ? "selected" : "").'>(GMT -10:00) Hawaii</option>
									<option value="-9.0" '.($merchantdata["timezone"] == "-9.0"  ? "selected" : "").'>(GMT -9:00) Alaska</option>
									<option value="-8.0" '.($merchantdata["timezone"] == "-8.0"  ? "selected" : "").'>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
									<option value="-7.0" '.($merchantdata["timezone"] == "-7.0"  ? "selected" : "").'>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
									<option value="-6.0" '.($merchantdata["timezone"] == "-6.0"  ? "selected" : "").'>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
									<option value="-5.0" '.($merchantdata["timezone"] == "-5.0"  ? "selected" : "").' '.($merchantdata["timezone"] == NULL  ? "selected" : "").'>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
									<option value="-4.0" '.($merchantdata["timezone"] == "-4.0"  ? "selected" : "").'>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
									<option value="-3.5" '.($merchantdata["timezone"] == "-3.5"  ? "selected" : "").'>(GMT -3:30) Newfoundland</option>
									<option value="-3.0" '.($merchantdata["timezone"] == "-3.0"  ? "selected" : "").'>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
									<option value="-2.0" '.($merchantdata["timezone"] == "-2.0"  ? "selected" : "").'>(GMT -2:00) Mid-Atlantic</option>
									<option value="-1.0" '.($merchantdata["timezone"] == "-1.0"  ? "selected" : "").'>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
									<option value="0.0" '.($merchantdata["timezone"] == "0.0"  ? "selected" : "").'>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
									<option value="1.0" '.($merchantdata["timezone"] == "1.0"  ? "selected" : "").'>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
									<option value="2.0" '.($merchantdata["timezone"] == "2.0"  ? "selected" : "").'>(GMT +2:00) Kaliningrad, South Africa</option>
									<option value="3.0" '.($merchantdata["timezone"] == "3.0"  ? "selected" : "").'>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
									<option value="3.5" '.($merchantdata["timezone"] == "3.5"  ? "selected" : "").'>(GMT +3:30) Tehran</option>
									<option value="4.0" '.($merchantdata["timezone"] == "4.0"  ? "selected" : "").'>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
									<option value="4.5" '.($merchantdata["timezone"] == "4.5"  ? "selected" : "").'>(GMT +4:30) Kabul</option>
									<option value="5.0" '.($merchantdata["timezone"] == "5.0"  ? "selected" : "").'>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
									<option value="5.5" '.($merchantdata["timezone"] == "5.5"  ? "selected" : "").'>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
									<option value="5.75" '.($merchantdata["timezone"] == "5.75"  ? "selected" : "").'>(GMT +5:45) Kathmandu</option>
									<option value="6.0" '.($merchantdata["timezone"] == "6.0"  ? "selected" : "").'>(GMT +6:00) Almaty, Dhaka, Colombo</option>
									<option value="7.0" '.($merchantdata["timezone"] == "7.0"  ? "selected" : "").'>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
									<option value="8.0" '.($merchantdata["timezone"] == "8.0"  ? "selected" : "").'>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
									<option value="9.0" '.($merchantdata["timezone"] == "9.0"  ? "selected" : "").'>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
									<option value="9.5" '.($merchantdata["timezone"] == "9.5"  ? "selected" : "").'>(GMT +9:30) Adelaide, Darwin</option>
									<option value="10.0" '.($merchantdata["timezone"] == "10.0"  ? "selected" : "").'>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
									<option value="11.0" '.($merchantdata["timezone"] == "11.0"  ? "selected" : "").'>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
									<option value="12.0" '.($merchantdata["timezone"] == "12.0"  ? "selected" : "").'>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
								</select>
							</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
								<label> </label>
								<div>
									<label>Email *</label>
									<input id="csemail" name="csemail" type="text" value="'.$merchantdata['csemail'].'" class="form-control required">
									</span>
								</div>
							</div><div class="clearfix"></div>
					<div class="form-group">
						<br /><div class="clearfix"></div>
								<label> </label>
								<div>
									<label>Business Type *</label>
									<input id="business_type" name="business_type" type="text" value="'.$merchantdata['business_type'].'" class="form-control required">
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary block full-width m-b">Save</button>
							</form>
						</div>
					</div>
		</fieldset>
		<script>
		$(document).ready(function() {
			$("#country").on("change", function() {
				var states;
				switch(this.value) {
					case "US":
						states = "<label>State *</label><select class="form-control required" name="us_state" id="us_state" aria-required="true"><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></select>";
						break;
					case "CA":
						states = "<label>State *</label><select class="form-control required" name="us_state" id="us_state" aria-required="true"><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland and Labrador</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon</option></select>";
						break;
					default:
						states = "<label>Providence *</label><input type="text" class="form-control required" name="us_state" id="us_state" aria-required="true">";
						break;
				} 
			  $("#statebox").html(states);
			});
		});	
	</script>
			';
		}else{
			return 'No Data Found';
		}
	}
}
function getTimezone($timenumber){
		switch ($timenumber) {
			case '-12.0':
				$timezone = '(GMT -12:00) Eniwetok, Kwajalein';
				break;
			case '-11.0':
				$timezone = '(GMT -11:00) Midway Island, Samoa';
				break;
			case '-10.0':
				$timezone = '(GMT -10:00) Hawaii';
				break;
			case '-9.0':
				$timezone = '(GMT -9:00) Alaska';
				break;
			case '-8.0':
				$timezone = '(GMT -8:00) Pacific Time (US &amp; Canada)';
				break;
			case '-7.0':
				$timezone = '(GMT -7:00) Mountain Time (US &amp; Canada)';
				break;
			case '-6.0':
				$timezone = '(GMT -6:00) Central Time (US &amp; Canada), Mexico City';
				break;
			case '-5.0':
				$timezone = '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima';
				break;
			case '-4.0':
				$timezone = '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz';
				break;
			case '-3.5':
				$timezone = '(GMT -3:30) Newfoundland';
				break;
			case '-3.0':
				$timezone = '(GMT -3:00) Brazil, Buenos Aires, Georgetown';
				break;
			case '-2.0':
				$timezone = '(GMT -2:00) Mid-Atlantic';
				break;
			case '-1.0':
				$timezone = '(GMT -1:00 hour) Azores, Cape Verde Islands';
				break;
			case '0.0':
				$timezone = '(GMT) Western Europe Time, London, Lisbon, Casablanca';
				break;
			case '1.0':
				$timezone = '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris';
				break;
			case '2.0':
				$timezone = '(GMT +2:00) Kaliningrad, South Africa';
				break;
			case '3.0':
				$timezone = '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg';
				break;
			case '3.5':
				$timezone = '(GMT +3:30) Tehran';
				break;
			case '4.0':
				$timezone = '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi';
				break;
			case '4.5':
				$timezone = '(GMT +4:30) Kabul';
				break;
			case '5.0':
				$timezone = '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent';
				break;
			case '5.5':
				$timezone = '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi';
				break;
			case '5.75':
				$timezone = '(GMT +5:45) Kathmandu';
				break;
			case '6.0':
				$timezone = '(GMT +6:00) Almaty, Dhaka, Colombo';
				break;
			case '7.0':
				$timezone = '(GMT +7:00) Bangkok, Hanoi, Jakarta';
				break;
			case '8.0':
				$timezone = '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong';
				break;
			case '9.0':
				$timezone = '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk';
				break;
			case '9.5':
				$timezone = '(GMT +9:30) Adelaide, Darwin';
				break;
			case '10.0':
				$timezone = '(GMT +10:00) Eastern Australia, Guam, Vladivostok';
				break;
			case '11.0':
				$timezone = '(GMT +11:00) Magadan, Solomon Islands, New Caledonia';
				break;
			case '12.0':
				$timezone = '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka';
				break;
			default:
				$timezone = 'NOT SET';
				break;
		}
	return $timezone;
}
function getAgentInfo($iid, $agentid, $merchantid){
	global $db;
	if(!checkAccess($agentid, $merchantid)){
		return '
			<div class="panel panel-primary">
				<div class="panel-heading">
					Account Information
				</div>
				<div class="panel-body">
					<div class="form-group">Your account does not have access to this account.
					</div>
				</div>
			</div>
		';
	}
	$jsdata = "'editagentinfo&agentid=".$agentid."&merchantid=".$merchantid."'";
	if(isset($agentid) && $agentid != ''){
		$db->where("idagents",$agentid);
		$agentdata = $db->getOne("agents");
		if($db->count > 0){
		$timezonename = getTimezone($agentdata['timezone']);
								
			return '<div class="panel panel-primary">
		<div class="panel-heading">
			Agent Information
		</div>
		<div class="panel-body">

			<div class="form-group">
				<table style="border: 0px solid gray;" cellpadding="3" cellspacing="0" border="0" width="100%">
					<tbody>
						<tr>
							<td class="tableheader" style="border-bottom: 0px"></td>
						</tr>
						<tr>
							<td align="left" class="mainarea" style="padding: 10px;">
								<table border="0" width="100%">
									<tbody>
										<tr>
											<td width="20%"></td>
											<td width="30%"></td>
											<td width="20%"></td>
											<td width="100%"></td>
										</tr>
										<tr>
											<td><b>Support Contact Name:</b>
											</td>
											<td>'.$agentdata["cs_first_name"].' '.$agentdata["cs_last_name"].'</td>
											<td><b>Routing #:</b>
											</td>
											<td>'.$agentdata["routing"].'</td>
										</tr>
										<tr>
											<td><b>Company:</b>
											</td>
											<td>'.$agentdata["agentname"].'</td>
											<td><b>Account #:</b>
											</td>
											<td>'.$agentdata["account"].'</td>
										</tr>
										<tr>
											<td><b>Address:</b>
											</td>
											<td>'.$agentdata["address1"].'</td>
											<td>
											</td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td>'.$agentdata["address2"].'</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td>'.$agentdata["city"].', '.$agentdata["us_state"].' '.$agentdata["country"].' '.$agentdata["zippostalcode"].'</td>
										</tr>
										<tr>
											<td><b>Phone:</b>
											</td>
											<td>'.$agentdata["csphone"].'</td>
											<td><b>Website:</b>
											</td>
											<td>'.$agentdata["website"].'</td>
										</tr>
										<tr>
											<td><b>Skype ID:</b>
											</td>
											<td>'.$agentdata["skypeid"].'</td>
											<td>
												<b>Timezone:</b>
											</td>
											<td>'.$timezonename.'</td>
										</tr>
										<tr>
											<td><b>Support Email:</b>
											</td>
											<td>'.$agentdata["csemail"].'</td>
											<td><b>Logo File</b></td>
											<td>'.$agentdata["logofile"].'</td>
										</tr>
										<tr>
											<td><b>Support Phone:</b>
											</td>
											<td>'.$agentdata["csphone"].'</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td colspan="4" align="RIGHT">
												<input type="SUBMIT" onclick="showAgent('.$jsdata.')" value="Edit">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>';
		}else{
			return 'No Data Found';
		}
	}elseif(isset($merchantid) && $merchantid != ''){
		$db->where("idmerchants",$merchantid);
		$merchantdata = $db->getOne("merchants");
		if($db->count > 0){
		$timezonename = getTimezone($merchantdata['timezone']);
		if($merchantdata['qr_url']==""){
			 $datas="#";
			 $datas1="No QR_URL Found";
			 $s="";
		}else {
			$datas=$merchantdata['qr_url'];
			$datas1="Download QR_ULR Image";
			$s="download";
		}
		
			return '<div class="panel panel-primary">
		<div class="panel-heading">
			Merchant Information
		</div>
		<div class="panel-body">

			<div class="form-group">
				<table style="border: 0px solid gray;" cellpadding="3" cellspacing="0" border="0" width="100%">
					<tbody>
						<tr>
							<td class="tableheader" style="border-bottom: 0px"></td>
						</tr>
						<tr>
							<td align="left" class="mainarea" style="padding: 10px;">
								<table border="0" width="100%">
									<tbody>
										<tr>
											<td width="20%"></td>
											<td width="30%"></td>
											<td width="20%"></td>
											<td width="100%"></td>
										</tr>
										<tr>
											<td><b>Support Contact Name:</b>
											</td>
											<td>'.$merchantdata["cs_first_name"].' '.$merchantdata["cs_last_name"].'</td>
											<td><b>Routing #:</b>
											</td>
											<td>'.$merchantdata["routing"].'</td>
										</tr>
										<tr>
											<td><b>Company:</b>
											</td>
											<td>'.$merchantdata["merchant_name"].'</td>
											<td><b>Account #:</b>
											</td>
											<td>'.$merchantdata["account"].'</td>
										</tr>
										<tr>
											<td><b>Address:</b>
											</td>
											<td>'.$merchantdata["address1"].'</td>
											<td>
											</td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td>'.$merchantdata["address2"].'</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td>'.$merchantdata["city"].', '.$merchantdata["us_state"].' '.$merchantdata["country"].' '.$merchantdata["zippostalcode"].'</td>
										</tr>
										<tr>
											<td><b>Phone:</b>
											</td>
											<td>'.$merchantdata["csphone"].'</td>
											<td><b>Website:</b>
											</td>
											<td>'.$merchantdata["website"].'</td>
										</tr>
										<tr>
											<td><b>Skype ID:</b>
											</td>
											<td>'.$merchantdata["skypeid"].'</td>
											<td>
												<b>Timezone:</b>
											</td>
											<td>'.$timezonename.'</td>
										</tr>
										<tr>
											<td><b>Support Email:</b>
											</td>
											<td>'.$merchantdata["csemail"].'</td>
											<td><b>Logo File</b></td>
											<td>'.$merchantdata["logofile"].'</td>
										</tr>
										<tr>
											<td><b>Support Phone:</b>
											</td>
											<td>'.$merchantdata["csphone"].'</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td><b>QR URL</b>
											</td>
										
											<td><a href="'.$datas.'" '.$s.'>'.$datas1.'</a></td>
									
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td colspan="4" align="RIGHT">
												<input type="SUBMIT" onclick="showAgent('.$jsdata.')" value="Edit">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>';
		}else{
			return 'No Data Found';
		}
	}
}
function getAccInfo($iid, $agentid, $merchantid){
	global $db;
	// GREG!!! NOTE - do a security check
	if(!checkAccess($agentid, $merchantid)){
		return '
		<div class="panel panel-primary">
		    <div class="panel-heading">
		        Account Information
		    </div>
		    <div class="panel-body">
		        <div class="form-group">Your account does not have access to this account.
				</div>
			</div>
		</div>';
	}
	//these two if statements below needs to be reworked i don't think they are 100% right. - greg
	//rework done
	$user_users = array();
	if(isset($agentid) && $agentid != '')
	{
		$cols = Array ("users.id, users.username, users.user_type");
		$db->join("merchants", "merchants.idmerchants=users.merchant_id", "");
		$db->join("agents", "agents.idagents=merchants.affiliate_id", "");
		$db->where ("idagents", $agentid);
		//$db->where ("agent_id", $agentid);
		$db->orderBy("username","Asc");
		$users = $db->get ("users", null, $cols);
		foreach($users as $user) {
			$user_users[] = array(
					"id" 			=> $user["id"],
					"username" 		=> $user["username"],
					"user_type" 	=> $user["user_type"]
					);
		}
		$cols = Array ("id, username, user_type");
		//$db->join("merchants", "merchants.idmerchants=users.merchant_id", "");
		//$db->join("agents", "agents.idagents=merchants.affiliate_id", "");
		//$db->where ("idagents", $agentid);
		$db->where ("agent_id", $agentid);
		$db->orderBy("username","Asc");
		$users = $db->get ("users", null, $cols);
		foreach($users as $user) {
			$user_users[] = array(
					"id" 			=> $user["id"],
					"username" 		=> $user["username"],
					"user_type" 	=> $user["user_type"]
					);
		}
		/*
		SELECT id, username, user_type
		FROM users
		JOIN merchants ON merchants.idmerchants = users.merchant_id
		JOIN agents ON agents.idagents = merchants.affiliate_id
		where idagents = $agentid;
		*/
		//var_dump($users);
	}
	if(isset($merchantid) && $merchantid != '')
	{
		$cols = Array ("id, username, user_type");
		$db->join("merchants", "merchants.idmerchants=users.merchant_id", "");
		$db->join("agents", "agents.idagents=agents.affiliation", "");
		$db->where ("idmerchants", $merchantid);
		$db->orderBy("username","Asc");
		$users = $db->get ("users", null, $cols);
		foreach($users as $user) {
			$user_users[] = array(
					"id" 			=> $user["id"],
					"username" 		=> $user["username"],
					"user_type" 	=> $user["user_type"]
					);
		}
		$cols = Array ("id, username, user_type");
		//$db->join("merchants", "merchants.idmerchants=users.merchant_id", "");
		//$db->join("agents", "agents.idagents=merchants.affiliate_id", "");
		//$db->where ("idagents", $agentid);
		$db->where ("merchant_id", $merchantid);
		$db->orderBy("username","Asc");
		$users = $db->get ("users", null, $cols);
		foreach($users as $user) {
			$user_users[] = array(
					"id" 			=> $user["id"],
					"username" 		=> $user["username"],
					"user_type" 	=> $user["user_type"]
					);
		}
		//var_dump($users);die();
		/*
		SELECT id, username, user_type
		FROM users
		JOIN merchants ON merchants.idmerchants = users.merchant_id
		JOIN agents ON agents.idagents = agents.affiliate_id
		where idmerchants = $merchantid;
		*/
	}//die();
	//SELECT agent_id FROM users where agent_id IN (SELECT agent_id from users where id = 69)
	$html = '<div class="panel panel-primary">
    <div class="panel-heading">
        Account Information
    </div>
    <div class="panel-body">
        <div class="form-group">
			<table border="0" width="100%">
				<tbody>
					<tr>
						<td><b>Username</b></td>
						<td><b>User Type</b></td>
						<td><b>Password</b></td>
						<td></td>
					</tr>';
					foreach($user_users as $user) { 
						if($user["user_type"] == 1) {
							$UserType = "Master Administrator (".getUserPermissions(1).")";
						} else if($user["user_type"] == 2) {
							$UserType = "Agent Administrator (".getUserPermissions(2).")";
						} else if($user["user_type"] == 3) {
							$UserType = "Agent (".getUserPermissions(3).")";
						} else if($user["user_type"] == 4) {
							$UserType = "Merchant Administrator (".getUserPermissions(4).")";
						} else if($user["user_type"] == 5) {
							$UserType = "Merchant (".getUserPermissions(5).")";
						} else if($user["user_type"] == 6) {
							$UserType = "Merchant CSR (".getUserPermissions(6).")";
						}
						$primary = $UserType; // ($user["user_type"] == 4 || $user["user_type"] == 2)?"(Primary User)":"";

						$html .= '
							<tr>
							<td>'.$user["username"].'</td>
							<td>'.$primary.'</td>
							<td>********</td>
						</tr></form>';
						// <td align="right">
						// <input type="SUBMIT" onclick="impersonate('.$user["id"].')" value="Login">
						// </td>
					 } 
				$html .= '</tbody>
			</table>
        </div>
	</div>
</div>';
return $html;
	/*var_dump($users);die;
	$ids = $db->subQuery ();
	$ids->where ("id", $iid);
	$ids->get ("users", null, "agent_id");
	$cols = Array ("agent_id");
	$db->where ("agent_id", $ids, 'in');
	$agent_ids = $db->get ("users", null, $cols);
	//SELECT agent_id FROM users where agent_id IN (SELECT agent_id from users where id = 69)
	//var_dump($agent_ids);
	$idagentsall = array();
	foreach ($agent_ids as $aid) {
		$affiliation = $db->subQuery ();
		$affiliation->where ("idagents", $aid['agent_id']);
		$affiliation->orWhere ('affiliation', $aid['agent_id']);
		$affiliation->get ("agents", null, "affiliation");
		$idagents = $db->subQuery ();
		$idagents->where ("idagents", $affiliation, "IN");
		$idagents->get ("agents", null, "idagents");
		$cols = Array ("idagents");
		$db->where ("affiliation", $idagents, 'IN');
		$idagentsall[] = $db->get ("agents", null, $cols);
		//SELECT idagents FROM profitorius.agents where affiliation IN (SELECT idagents FROM agents where idagents IN (SELECT affiliation from agents where idagents = 11 OR affiliation = 11));
	}
	$x = '';
	foreach($idagentsall as $agentid)
	{
		foreach($agentid as $idagents) {
			$x .= $idagents["idagents"].',';
		}		
	}
	$x = rtrim($x, ",");
	$uniqueDep = implode(',', array_unique(explode(',', $x)));
	//var_dump($uniqueDep);die;
	$uniqueDep = preg_replace('/\s+/', '', $uniqueDep);
	//echo $uniqueDep;
	$db->where('affiliate_id', array($uniqueDep), 'IN');
	$results = $db->rawQuery('select idmerchants from merchants where affiliate_id IN ('.$uniqueDep.')');
	//select * from merchants where affiliate_id IN (11,14,15,16,17,18,19);
	//echo "Last executed query was ". $db->getLastQuery();
	//var_dump($results);die();
	foreach($idagentsall as $agentid)
	{
		foreach($agentid as $idagents) {
			$x .= $idagents["idagents"].',';
		}		
	}
*/
}
function getProcessors($iid, $agentid, $merchantid){
if(!checkAccess($agentid, $merchantid)){
	return '<div class="alert alert-danger"  data-animation="fadeIn">Your account does not have access to this account.</div>';
}
	
global $db;
$cols = Array ("m.id as id, p.processor_name as processor_name","g.processor_name as gateway_name", "is_for_moto", "is_active");
$db->join("processors p", "p.p_id = m.processor_id", "LEFT");
$db->join("processors g", "g.p_id = m.gateway_id", "LEFT");
$db->where("merchant_id",$merchantid);
$processordata = $db->get("merchant_processors_mid m", null, $cols);

$html = '
<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>Processor</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
							</div>
						</div>
						<div class="ibox-content">
<table class="table table-striped table-bordered table-hover dataTables-processors">
	<thead>
		<tr>
			<th>ID</th>
			<th>Processors</th>
			<th>Gateway</th>
			<th>Moto</th>
		</tr>
	</thead>
	<tbody>';

	foreach($processordata as $pdata) { 
		$isactive = $pdata['is_active'] == 1 ? 'checked' : '';
		$formoto = $pdata['is_for_moto'] == 1 ? 'yes' : 'no';
		
			$html .= '<tr class="gradeX">
				<td>'.$pdata['id'].'</td>
				<td>'.$pdata['processor_name'].'</td>
				<td>'.$pdata['gateway_name'].'</td>
				<td>'.$formoto.'</td>
			</tr>';
			} 
	$html .= '</tbody>
</table>
</div>
</div>';
	return $html;
}
function getFee($iid, $agentid, $merchantid){
	global $db;
	//SELECT agent_id FROM profitorius.users where id = 98;
	if($iid == null){
		var_dump('omgitsnull');die();
	}
	$db->where("id",$iid);
	$usersdata = $db->getOne("users");
	//get the id of the agent and assign it
	$agent_id = $agentid;
	//SELECT * FROM profitorius.agents where idagents = 24;
	$db->where("idagents",$agent_id);
	$agentsdata = $db->getOne("agents");
	//SELECT * FROM profitorius.merchants where affiliate_id = 24;
	$db->where("affiliate_id",$agent_id);
	$merchantsdata = $db->get("merchants");
	//---------------skipping below till I get more info on why we need these----------------
	//returns 52,53,57,58
	//todo get processors of merchants
	//SELECT p_id FROM profitorius.merchant_processors_mid where merchant_id in (52,53,57,58)
	//-->returns 66 ;12 times
	//SELECT * FROM profitorius.processors where p_id = 66;
	//-->returns processor name etc.
	//SELECT * FROM profitorius.bank_fees where merchant_id in (52,53,57,58) and processor_id = 66
	//-->returns merchant fees
	//SELECT * FROM profitorius.agent_bank_fees where merchant_id in (52,53,57,58) and processor_id = 66 and agent_id = 24
	//returns agent bank fees
	//SELECT * FROM profitorius.merchant_bank_fees where merchant_id in (52,53,57,58) and processor_id = 66
	//returns banks fees
	//-------------------end skipping----------------------
	//SELECT * FROM profitorius.agent_bank_fees where agent_id = 24
	//$db->where("agent_id",$agent_id);
	//$agent_bank_feesdata = $db->get("agent_bank_fees");
	//---replaced above with below-->
	//combine merchant_id and agent_id to get names of merchants etc.
	//select * from agent_bank_fees join merchants on agent_bank_fees.merchant_id = merchants.idmerchants where agent_id = 24;
	$db->join("merchants m", "a.merchant_id=m.idmerchants", "LEFT");
	$db->where("a.agent_id", $agent_id);
	$agent_bank_feesdata = $db->get ("agent_bank_fees a");
	$agent_bank_feesdata = $db->rawQuery("SELECT  merchant_id, merchant_name, processor_name,  
													GROUP_CONCAT(agent_bank_fees.idagent_bank_fees SEPARATOR ', ') as idagent_bank_fees,
													GROUP_CONCAT(agent_bank_fees.transaction_fee SEPARATOR ', ') as transaction_fee, 													
													GROUP_CONCAT(agent_bank_fees.authorization_fee SEPARATOR ', ') as authorization_fee, 
													GROUP_CONCAT(agent_bank_fees.capture_fee SEPARATOR ', ') as capture_fee,
													GROUP_CONCAT(agent_bank_fees.sale_fee SEPARATOR ', ') as sale_fee,
													GROUP_CONCAT(agent_bank_fees.decline_fee SEPARATOR ', ') as decline_fee,
													GROUP_CONCAT(agent_bank_fees.refund_fee SEPARATOR ', ') as refund_fee,
													GROUP_CONCAT(agent_bank_fees.cb_fee_1 SEPARATOR ', ') as cb_fee_1,
													GROUP_CONCAT(agent_bank_fees.cb_fee_2 SEPARATOR ', ') as cb_fee_2,
													GROUP_CONCAT(agent_bank_fees.cb_threshold SEPARATOR ', ') as cb_threshold,
													GROUP_CONCAT(agent_bank_fees.discount_rate SEPARATOR ', ') as discount_rate,
													GROUP_CONCAT(agent_bank_fees.avs_premium SEPARATOR ', ') as avs_premium,
													GROUP_CONCAT(agent_bank_fees.cvv_premium SEPARATOR ', ') as cvv_premium,
													GROUP_CONCAT(agent_bank_fees.interregional_premium SEPARATOR ', ') as interregional_premium,
													GROUP_CONCAT(agent_bank_fees.wire_fee SEPARATOR ', ') as agent_bank_fees_wire_fee,
													GROUP_CONCAT(agent_bank_fees.reserve_rate SEPARATOR ', ') as reserve_rate,
													GROUP_CONCAT(agent_bank_fees.reserve_period_months SEPARATOR ', ') as reserve_period_months,
													GROUP_CONCAT(agent_bank_fees.initial_reserve SEPARATOR ', ') as initial_reserve,
													GROUP_CONCAT(agent_bank_fees.setup_fee SEPARATOR ', ') as setup_fee,
													GROUP_CONCAT(agent_bank_fees.monthly_fees SEPARATOR ', ') as monthly_fees,
													GROUP_CONCAT(agent_bank_fees.effective_date SEPARATOR ', ') as effective_date,
													GROUP_CONCAT(agent_bank_fees.last_effective_date SEPARATOR ', ') as last_effective_date
											FROM agent_bank_fees 
											JOIN merchants ON agent_bank_fees.merchant_id = merchants.idmerchants
											JOIN processors ON agent_bank_fees.processor_id = processors.p_id											
											WHERE agent_id = ".$agent_id."
											GROUP BY merchant_id, processor_id 
											ORDER BY idagent_bank_fees");
	$array = array();
	//var_dump($agent_bank_feesdata);die();
	$html = '<div class="panel panel-primary">
    <div class="panel-heading">
        Fee Schedule
    </div>
    <div class="panel-body">
        <div class="form-group">'; 
		if(empty($agent_bank_feesdata)){
			$html .= 'No fees initially set by the admin';
		}
									foreach ($agent_bank_feesdata as $abf) {
										$idagent_bank_fees = end(explode(", ", $abf["idagent_bank_fees"]));
										$transaction_fee = explode(", ", $abf["transaction_fee"]);
										$cost_transaction_fee = $transaction_fee[0];
										$price_transaction_fee = end($transaction_fee);
										$authorization_fee = explode(", ", $abf["authorization_fee"]);
										$cost_authorization_fee = $authorization_fee[0];
										$price_authorization_fee = end($authorization_fee);
										$capture_fee = explode(", ", $abf["capture_fee"]);
										$cost_capture_fee = $capture_fee[0];
										$price_capture_fee = end($capture_fee);
										$sale_fee = explode(", ", $abf["sale_fee"]);
										$cost_sale_fee = $sale_fee[0];
										$price_sale_fee = end($sale_fee);
										$decline_fee = explode(", ", $abf["decline_fee"]);
										$cost_decline_fee = $decline_fee[0];
										$price_decline_fee = end($decline_fee);
										$refund_fee = explode(", ", $abf["refund_fee"]);
										$cost_refund_fee = $refund_fee[0];
										$price_refund_fee = end($refund_fee);
										$cb_fee_1 = explode(", ", $abf["cb_fee_1"]);
										$cost_cb_fee_1 = $cb_fee_1[0];
										$price_cb_fee_1 = end($cb_fee_1);
										$cb_fee_2 = explode(", ", $abf["cb_fee_2"]);
										$cost_cb_fee_2 = $cb_fee_2[0];
										$price_cb_fee_2 = end($cb_fee_2);
										$cb_threshold = explode(", ", $abf["cb_threshold"]);
										$cost_cb_threshold = $cb_threshold[0];
										$price_cb_threshold = end($cb_threshold);
										$discount_rate = explode(", ", $abf["discount_rate"]);
										$cost_discount_rate = $discount_rate[0];
										$price_discount_rate = end($discount_rate);
										$avs_premium = explode(", ", $abf["avs_premium"]);
										$cost_avs_premium = $avs_premium[0];
										$price_avs_premium = end($avs_premium);
										$cvv_premium = explode(", ", $abf["cvv_premium"]);
										$cost_cvv_premium = $cvv_premium[0];
										$price_cvv_premium = end($cvv_premium);
										$interregional_premium = explode(", ", $abf["interregional_premium"]);
										$cost_interregional_premium = $interregional_premium[0];
										$price_interregional_premium = end($interregional_premium);
										$wire_fee = explode(", ", $abf["wire_fee"]);
										$cost_wire_fee = $wire_fee[0];
										$price_wire_fee = end($wire_fee);
										$reserve_rate = explode(", ", $abf["reserve_rate"]);
										$cost_reserve_rate = $reserve_rate[0];
										$price_reserve_rate = end($reserve_rate);
										$reserve_period_months = explode(", ", $abf["reserve_period_months"]);
										$cost_reserve_period_months = $reserve_period_months[0];
										$price_reserve_period_months = end($reserve_period_months);
										$initial_reserve = explode(", ", $abf["initial_reserve"]);
										$cost_initial_reserve = $initial_reserve[0];
										$price_initial_reserve = end($initial_reserve);
										$setup_fee = explode(", ", $abf["setup_fee"]);
										$cost_setup_fee = $setup_fee[0];
										$price_setup_fee = end($setup_fee);
										$monthly_fees = explode(", ", $abf["monthly_fees"]);
										$cost_monthly_fees = $monthly_fees[0];
										$price_monthly_fees = end($monthly_fees);
										$html .='<table border="0" width="100%" cellpadding="2" cellspacing="0" class="feeschedule table table-striped">
												<tbody>
													<!-- php loop start here -->
											<tr>
												<th width="80%"><b>'.$abf["merchant_name"].' ('.$abf["processor_name"].')</b></th>
												<th width="10%"><h4>Price</h4></th>
												<th width="10%"><h4>Cost</h4></th>
											</tr>
											<tr>
												<td>Transaction Fee</td>
												<td>$<input type="text" id="transaction_fee-'.$idagent_bank_fees.'" name="transaction_fee-'.$idagent_bank_fees.'" value="'.$price_transaction_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_transaction_fee.'</span></td>
											</tr>
											<tr>
												<td>Authorization Fee</td>
												<td>$<input type="text" id="authorization_fee-'.$idagent_bank_fees.'" name="authorization_fee-'.$idagent_bank_fees.'" value="'.$price_authorization_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_authorization_fee.'</span></td>
											</tr>
											<tr>
												<td>Capture Fee</td>
												<td>$<input type="text" id="capture_fee-'.$idagent_bank_fees.'" name="capture_fee-'.$idagent_bank_fees.'" value="'.$price_capture_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_capture_fee.'</span></td>
											</tr>
											<tr>
												<td>Sale Fee</td>
												<td>$<input type="text" id="sale_fee-'.$idagent_bank_fees.'" name="sale_fee-'.$idagent_bank_fees.'" value="'.$price_sale_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_sale_fee.'</span></td>
											</tr>
											<tr>
												<td>Decline Fee</td>
												<td>$<input type="text" id="decline_fee-'.$idagent_bank_fees.'" name="decline_fee-'.$idagent_bank_fees.'" value="'.$price_decline_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_decline_fee.'</span></td>
											</tr>
											<tr>
												<td>Refund Fee</td>
												<td>$<input type="text" id="refund_fee-'.$idagent_bank_fees.'" name="refund_fee-'.$idagent_bank_fees.'" value="'.$price_refund_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_refund_fee.'</span></td>
											</tr>
											<tr>
												<td>Chargeback 1</td>
												<td>$<input type="text" id="cb_fee_1-'.$idagent_bank_fees.'" name="cb_fee_1-'.$idagent_bank_fees.'" value="'.$price_cb_fee_1.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_cb_fee_1.'</span></td>
											</tr>
											<tr>
												<td>Chargeback 2</td>
												<td>$<input type="text" id="cb_fee_2-'.$idagent_bank_fees.'" name="cb_fee_2-'.$idagent_bank_fees.'" value="'.$price_cb_fee_2.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_cb_fee_2.'</span></td>
											</tr>
											<tr>
												<td>Chargeback Threshold</td>
												<td>$<input type="text" id="cb_threshold-'.$idagent_bank_fees.'" name="cb_threshold-'.$idagent_bank_fees.'" value="'.$price_cb_threshold.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_cb_threshold.'</span></td>
											</tr>
											<tr>
												<td>Discount Rate (%)</td>
												<td><input type="text" id="discount_rate-'.$idagent_bank_fees.'" name="discount_rate-'.$idagent_bank_fees.'" value="'.$price_discount_rate.'" class="form-control" disabled="disabled" />%</td>
												<td><span class="costfee">'.$cost_discount_rate.'%</span></td>
											</tr>
											<tr>
												<td>AVS Premium</td>
												<td>$<input type="text" id="avs_premium-'.$idagent_bank_fees.'" name="avs_premium-'.$idagent_bank_fees.'" value="'.$price_avs_premium.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_avs_premium.'</span></td>
											</tr>
											<tr>
												<td>CVV Premium</td>
												<td>$<input type="text" id="cvv_premium-'.$idagent_bank_fees.'" name="cvv_premium-'.$idagent_bank_fees.'" value="'.$price_cvv_premium.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_cvv_premium.'</span></td>
											</tr>
											<tr>
												<td>Interregional Premium</td>
												<td>$<input type="text" id="interregional_premium-'.$idagent_bank_fees.'" name="interregional_premium-'.$idagent_bank_fees.'" value="'.$price_interregional_premium.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_interregional_premium.'</span></td>
											</tr>
											<tr>
												<td>Wire Fee</td>
												<td>$<input type="text" id="wire_fee-'.$idagent_bank_fees.'" name="wire_fee-'.$idagent_bank_fees.'" value="'.$price_wire_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_wire_fee.'</span></td>
											</tr>
											<tr>
												<td>Reserve	rate (%)</td>
												<td><input type="text" id="reserve_rate-'.$idagent_bank_fees.'" name="reserve_rate-'.$idagent_bank_fees.'" value="'.$price_reserve_rate.'" class="form-control" disabled="disabled" />%</td>
												<td><span class="costfee">'.$cost_reserve_rate.'%</span></td>
											</tr>
											<tr>
												<td>Reserve period (months)</td>
												<td><input type="text" id="reserve_period_months-'.$idagent_bank_fees.'" name="reserve_period_months-'.$idagent_bank_fees.'" value="'.$price_reserve_period_months.'" class="form-control" disabled="disabled" />months</td>
												<td><span class="costfee">'.$cost_reserve_period_months.' mo.</span></td>
											</tr>
											<tr>
												<td>Initial Reserve</td>
												<td>$<input type="text" id="initial_reserve-'.$idagent_bank_fees.'" name="initial_reserve-'.$idagent_bank_fees.'" value="'.$price_initial_reserve.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_initial_reserve.'</span></td>
											</tr>
											<tr>
												<td>Set Up Fee</td>
												<td>$<input type="text" id="setup_fee-'.$idagent_bank_fees.'" name="setup_fee-'.$idagent_bank_fees.'" value="'.$price_setup_fee.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_setup_fee.'</span></td>
											</tr>
											<tr>
												<td>Monthly Fees</td>
											   <td>$<input type="text" id="monthly_fees-'.$idagent_bank_fees.'" name="monthly_fees-'.$idagent_bank_fees.'" value="'.$price_monthly_fees.'" class="form-control" disabled="disabled" /></td>
												<td><span class="costfee">$'.$cost_monthly_fees.'</span></td>
											</tr>
									<!-- php loop ends here -->
                                    <tr>
                                        <td colspan="3" align="RIGHT">
                                        ';
										//change below to != when going to production
										if($_SESSION['iid'] != $_SESSION['id']){
										$html .= '
												<button id="edit" name="edit" onclick="toggle_inputs()">Edit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
									   ';
									   }else{
									   $html .= '
									   <button id="edit" name="edit" onclick="toggle_inputs()">Edit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
									   ';
									   }
					   }
					   
					   return $html;
}
function editFee($iid, $agentid, $merchantid){
	echo 'fees saved';
}
function getAgentStatus($iid, $agentid, $merchantid){
	
	return '
  <div class="panel panel-primary">
    <div class="panel-heading">
       Agent Status
    </div>
    <div class="panel-body">
      
       <div class="form-group">
       <table style="border: 0px solid gray;" cellpadding="3" cellspacing="0" border="0" width="100%">
            <tbody><tr><td class="tableheader" style="border-bottom: 1px solid gray;"></td></tr>
            <tr><td align="left" class="mainarea" style="padding: 10px;">You can change the Agent status to affect the overall
                    behavior of the Agent account. For example, switching the
                    Agent to a restricted status will still allow the Agent to
                    log into their account, but they will not be able to create new
                    merchant accounts or change merchant information.<br><br><table border="0" cellspacing="0" width="100%">
					<form method="GET" action="/resellers/resellers.php"></form>
					<input type="HIDDEN" name="tid" value="ceaa1ea5fe12de891363612635f6c7fb">
					<input type="HIDDEN" name="reseller" value="3803">
					<input type="HIDDEN" name="Action" value="ChangeResellerStatus">
					<tbody><tr><td>Currently: <b>Active</b></td>
					<td align="RIGHT"><input type="SUBMIT" onclick="showAgent(editagentstatus)" value="Change">
					</td></tr></tbody></table></td></tr>
            </tbody></table>
			';
}
function editAgentStatus($iid, $agentid, $merchantid){
	
	return '
  <div class="panel panel-primary">
	<div class="panel-heading">Agent Status</div>
	<div class="panel-body">
		<div class="form-group">
			<table class="mainarea" id="form_ResellerStatus" border="0" cellspacing="0" cellpadding="3">
				<tbody>
					<tr id="ResellerStatus_0_row">
						<td colspan="3" class="tableheader">Agent Status</td>
					</tr>
					<tr id="ResellerStatus_1_row">
						<td colspan="3">
							<table width="400">
								<tbody>
									<tr>
										<td>The Sub-Agent status controls the overall behaviour of the Sub-Agent.
											<ul>
												<li>
													<b>Active</b> : The Sub-Agent can manage merchants and perform all Payment Gateway functions normally.
												</li>
												<li>
													<b>Restricted</b> : The Sub-Agent is able to login to generate reports and change options, but they cannot manage any merchant information.
												</li>
												<li>
													<b>Closed</b> : The Sub-Agent is unable to login. This status can only be set if there are no active or restricted merchants managed by this Sub-Agent.
												</li>
												<li>
													<b>Deleted</b> : The Sub-Agent is unable to login, and is omitted from all reports, except for commission. This status can also only be set if there are currently no active or restricted merchants managed by this Sub-Agent.
												</li>
											</ul>
											<table width="100%" align="RIGHT" cellpadding="0" cellspaceing="0">
												<tbody>
													<tr>
														<td>
															<table width="100%" cellpadding="0" cellspacing="0">
																<tbody>
																	<tr>
																		<td align="RIGHT" width="1">
																			<input align="RIGHT" name="status" id="Active" type="RADIO" value="active" checked="" />
																		</td>
																		<td>
																			<label for="Active " style=" ">Active</label>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100% " cellpadding="0 " cellspacing="0 ">
																<tbody>
																	<tr>
																		<td align="center " width="1 ">
																			<input align="CENTER" name="status" id="Restricted" type="RADIO" value="restricted" />
																		</td>
																		<td>
																			<label for="Restricted" style="">Restricted</label>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100%" cellpadding="0" cellspacing="0">
																<tbody>
																	<tr>
																		<td align="center" width="1">
																			<input align="CENTER" name="status" id="Closed" type="RADIO" value="closed" />
																		</td>
																		<td>
																			<label for="Closed " style=" ">Closed</label>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100% " cellpadding="0 " cellspacing="0 ">
																<tbody>
																	<tr>
																		<td align="center " width="1 ">
																			<input align="CENTER " name="status " id="Deleted " type="RADIO" value="deleted" />
																		</td>
																		<td>
																			<label for="Deleted" style="">Deleted</label>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
														<input type="hidden" name="reseller" value="3803" id="reseller" />
													<tr id="ResellerStatus_submit_button_row">
														<td align="RIGHT" colspan="2">
															<input type="submit" onclick="showAgent(agentstatus)" id="ResellerStatus_submit_button" name="ResellerStatus_submit_button" value="Change" />
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>';
}

function audittrails1($user_id, $event, $auditable_type, $new_values, $old_values,$url, $ip,$user_agent){

    //require_once('./php/MysqliDb.php');
//  $db = new Mysqlidb ('localhost', 'root', '', 'rebanx2');

    $data = Array (
        "user_id" => $user_id,
        "event" => $event,
        "auditable_type" => $auditable_type,
        "new_values" =>$new_values,
        "old_values" => $old_values,
        "url" => $url,
        "ip_address" => $ip,
        "user_agent" => $user_agent
    );	
	
	
	
    global $db;
    $db->insert('audits', $data);
    /*  $txt = "user id date";
    $myfile = file_put_contents('logs.log', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
      */
    date_default_timezone_set("Asia/Kolkata");

    $lmsg =date("d-M-Y H:i:sa") . "\n".
        "-----------------------------------" ."\n". "user_id=" . $user_id ."\n" ."event=" . $event ."\n" . "auditable_type=" . $auditable_type . "\n" ."new_values=" . $new_values ."\n" . "old_values=" . $old_values ."\n" . "&url=" . $url . "\n" ."ip_address=" . $ip ."\n" . "user_agent=" . $user_agent."\n" ;

//$logfile='log/log_'.date('d-M-Y') .'.log';
    $logfile='auditLog/auditLog.log';
    file_put_contents($logfile,$lmsg."\n", FILE_APPEND | LOCK_EX);

}

?>