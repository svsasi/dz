<?php

include('init.php');


$id = $_POST['cid'];

//$check = $_POST['currency_req'];

/* china time call in function */
date_default_timezone_set('Asia/Kolkata');

$log_path = "Logs/crossBorderPay.log";

function poslogs($log) {
   GLOBAL $log_path;
$myfile = file_put_contents($log_path, $log . PHP_EOL, FILE_APPEND | LOCK_EX);   
return $myfile;     
}
// function conversionfrom_indiatochinatime($datetime) {
//     $given_cncl = new DateTime($datetime);
//     $given_cncl->setTimezone(new DateTimeZone("Asia/kolkata"));
//     $updated_datetime_cncl = $given_cncl->format("Y-m-d H:i:s");
//     return $updated_datetime_cncl;
// }

if ($_POST['currency_req']=='view') {

	// $Currency_from = $_POST['Currency_from'];
	// $Currency_to = $_POST['Currency_to'];
	// $Currency_value = $_POST['Currency_value'];
	// $crncy_from_value = $_POST['crncy_from_value'];
	// $crncy_percen = $_POST['currency_percentage'];
	

    //print_r($_POST);
	//();
	//crncy_to_value
	$crncy_to_value = 1;
	$currency_value = $_POST['currency_value'];
	$cbp_mer_percent = $_POST['cbp_mer_percent'];

	$xchng_stl_ccy_temp = $crncy_to_value / $_POST['currency_value'];

    $markedUp_xchng_calc = ($xchng_stl_ccy_temp * $_POST['cbp_mer_percent']) / 100;
    $markedUp_xchng_value_temp = $xchng_stl_ccy_temp + $markedUp_xchng_calc;
    //$update_date_time = conversionfrom_indiatochinatime(date('Y-m-d H:i:s'));
    $xchng_stl_ccy = number_format($xchng_stl_ccy_temp,8);
    $markedUp_xchng_value = number_format($markedUp_xchng_value_temp,8);


    $arr = array('xchng_stl_ccy'=>$xchng_stl_ccy,'markedUp_xchng_value'=>$markedUp_xchng_value);

    echo json_encode($arr);
	

} 

if ($_POST['currency_req']=='edit') {

	// $Currency_from = $_POST['Currency_from'];
	// $Currency_to = $_POST['Currency_to'];
	// $Currency_value = $_POST['Currency_value'];
	// $crncy_from_value = $_POST['crncy_from_value'];
	// $crncy_percen = $_POST['currency_percentage'];
	//print_r($_POST);
	//die();
	//crncy_to_value
	// $crncy_to_value = 1;
	// $currency_value = $_POST['currency_value'];
	// $cbp_mer_percent = $_POST['cbp_mer_percent'];

	// $xchng_stl_ccy = $crncy_to_value / $_POST['currency_value'];

 //    $markedUp_xchng_calc = ($xchng_stl_ccy * $_POST['cbp_mer_percent']) / 100;
 //    $markedUp_xchng_value = $xchng_stl_ccy + $markedUp_xchng_calc;
    $update_date_time = date('Y-m-d H:i:s');
//echo $update_date_time;exit;
    $currency_value = 1;

	$currency_from_value = $_POST['crncy_to_value'];
	// echo ;
	$cbp_mer_percent = $_POST['cbp_mer_percent'];

	$xchng_stl_ccy = $currency_value / $currency_from_value;

    $markedUp_xchng_calc = ($xchng_stl_ccy * $cbp_mer_percent) / 100;
    $markedUp_xchng_value = $xchng_stl_ccy + $markedUp_xchng_calc;
    //$update_date_time = conversionfrom_indiatochinatime(date('Y-m-d H:i:s'));
    //$xchng_stl_ccy = number_format($xchng_stl_ccy_temp,8);
    //$markedUp_xchng_value = number_format($markedUp_xchng_value_temp,8);

    // print_r($_POST);
    // die();
    $db->where('cid',$id);
    $existed_Value = $db->getone('currency_convert');
    $existed_Value += array("user" => $_SESSION['username']);
    $existed_Value_log = "Application Log CBP:Currency Changes as:".date("Y-m-d H:i:s") . " Exist Values :" .json_encode($existed_Value). " \n\n";
    poslogs($existed_Value_log);

	$sql_edit_query = "UPDATE currency_convert SET cbp_mer_percent='$cbp_mer_percent',crncy_to_value ='$currency_from_value',crncy_from_value='$xchng_stl_ccy',crncy_markup_xchg_rate='$markedUp_xchng_value',updated_date='$update_date_time' WHERE cid='$id'";

	//  echo $sql_edit_query;
	$data_edit = $db->rawQuery($sql_edit_query);

	$db->where('cid',$id);
    $existed_Value_new = $db->getone('currency_convert');
    $existed_Value_new += array("user" => $_SESSION['username']);
    $existed_Value_log_new = "Application Log CBP:Currency Changes as:".date("Y-m-d H:i:s") . "New Value :" .json_encode($existed_Value_new). " \n\n";
    poslogs($existed_Value_log_new);


	// echo "edit";
	// print_r($_POST);
 //    die();
	

} 

if ($_POST['currency_req']=='Delete') {
	# code...
	$db->where('cid',$id);
    $existed_Value_detle = $db->getone('currency_convert');
    $existed_Value_detle = "Application Log CBP:Currency Changes as:".date("Y-m-d H:i:s") . " Delete Record  Values :" .json_encode($existed_Value_detle). " \n\n";
    poslogs($existed_Value_detle);

	$sql_detele = "DELETE FROM currency_convert WHERE cid='$id'";
	// echo $sql_detele;
	$data_detele = $db->rawQuery($sql_detele);

	
	// echo "delete";
	// print_r($_POST);
 //    die();

}

// ob_end_clean();





?>