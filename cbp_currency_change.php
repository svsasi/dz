<?php 
include('init.php');

date_default_timezone_set('Asia/Kolkata');

$log_path = "Logs/crossBorderPay.log";

function poslogs($log) {
   GLOBAL $log_path;
$myfile = file_put_contents($log_path, $log . PHP_EOL, FILE_APPEND | LOCK_EX);   
return $myfile;     
}

	$base_currency = $_POST['base_currency'];
	$exchangecurrency = $_POST['exchange_currency'];
	//$currency_exchange_rate = $_POST['currency_exchange_rate'];
	$created_date_time = date('Y-m-d H:i:s');
	$data = array(
		    "crncy_from" => $base_currency,
		    "crncy_to" => $exchangecurrency,
		    "currency_value"=>'1',
		    "created_date"=>  $created_date_time  
	    );
	    $currency_ins = $db->insert('currency_convert', $data);

	    $data += array("user" => $_SESSION['username']);
    	$data_Value_log = "Application Log CBP:Currency Create as:".date("Y-m-d H:i:s") . " New Currency Data Add:" .json_encode($data). " \n\n";
    	poslogs($data_Value_log);

		if($currency_ins){
			echo 'success';
		}else{
			echo 'fail';
		}
	// $sql = "INSERT INTO currency_convert( crncy_to, currency_value, crncy_from_value) 
	// // VALUES ('$currency','$currency_value','$currency_exchange_rate')";
	// if (mysqli_query($conn, $sql)) {
	// 	echo json_encode(array("statusCode"=>200));
	// } 
	// else {
	// 	echo json_encode(array("statusCode"=>201));
	// }
	//mysqli_close($conn);



?>