<?php

include('../init.php');

/**** Indian Currency Format *****/
setlocale(LC_MONETARY, 'en_US');


if(file_get_contents("php://input")) {
	$json = json_decode(file_get_contents("php://input"));
	
	if($json->type == 'getmerchant') {
		$db->where('mer_map_id', $json->m_id);
		$lastid = $db->getone("merchants");

		$cols = Array ("idmerchants", "mso_terminal_id", "active");
		$db->where('idmerchants', $lastid['idmerchants']);
		$db->orderBy("mso_terminal_id","ASC");
		$terminal_List = $db->get("terminal", null, $cols);
		// echo "<pre>";
		// print_r($terminal_List);
		$result = '';
		$result.='<option value="">-- Terminal ID --</option>';
		foreach ($terminal_List as $key => $value) {
			$result.= '<option value="'.$value['mso_terminal_id'].'">'.$value['mso_terminal_id'].'</option>';
		}
		echo $result;
	}

	if($json->type == 'getterminalQR') {
		$db->where('mso_terminal_id', $json->t_id);
		$terminal_List = $db->getone("terminal");
		echo $terminal_List['mso_ter_device_mac'];
	}

	/* Merchant registration post data Starts here*/
    if($json->type == 'gmportal') {
        $db->where('mer_map_id', $json->m_id);
        $mer_detail = $db->getone("merchants");
         $merchant_name=$mer_detail['merchant_name'];
         $address1=$mer_detail['address1'];
         $countrys=$mer_detail['country'];
         $country=substr($countrys,0,2);
         $mcc=$mer_detail['mcc'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $currency_code=$mer_detail['currency_code'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $am_status=$mer_detail['am_status'];
         $csemail=$mer_detail['csemail'];
         $csphone=$mer_detail['csphone'];

         $db->where('vendor','supreme');
         $cur_res = $db->getOne('alipay_config');

         // $patner_query="SELECT * FROM alipay_config WHERE merchant_id='$mer_map_id'";
         // $sec_mer_res2 = $db->rawQuery($patner_query);
         // $partner_id=$sec_mer_res2[0]['partner_id'];
         // $key_md5=$sec_mer_res2[0]['key_md5'];
         $partner_id = $cur_res['partner_id'];
         $key_md5 = $cur_res['key_md5'];
         /* Making sign as a SESSION variable ,use in MD5*/


         $array = array(
            "merchant_name"=>$merchant_name,
            "address1"=>$address1,
            "country"=>$country,
            "mcc"=>$mcc,
            "mer_map_id"=>$mer_map_id,
            "currency_code"=>$currency_code,
            "am_status"=>$am_status,
            "partner_id"=>$partner_id,
            "key_md5"=>$key_md5,
            "csemail"=>$csemail,
            "csphone"=>$csphone
         );
         $result=json_encode($array);
         echo $result;
        
    }

    if($json->type == 'offline_GMP_status') {
        $db->where('mer_map_id', $json->m_id);
        $mer_detail = $db->getone("merchants");
         $merchant_name=$mer_detail['merchant_name'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $am_status=$mer_detail['am_status'];
         

         $db->where('vendor','supreme');
         $cur_res = $db->getOne('alipay_config');

         // $patner_query="SELECT * FROM alipay_config WHERE merchant_id='$mer_map_id'";
         // $sec_mer_res2 = $db->rawQuery($patner_query);
         // $partner_id=$sec_mer_res2[0]['partner_id'];
         // $key_md5=$sec_mer_res2[0]['key_md5'];
         $partner_id = $cur_res['partner_id'];
         $key_md5 = $cur_res['key_md5'];
         /* Making sign as a SESSION variable ,use in MD5*/


         $array = array(
            "merchant_name"=>$merchant_name,
            "mer_map_id"=>$mer_map_id,
            "am_status"=>$am_status,
            "partner_id"=>$partner_id,
            "key_md5"=>$key_md5,
            
         );
         $result=json_encode($array);
         echo $result;
        
    }
    if($json->type == 'onlinegmportal') {
        $db->where('mer_map_id', $json->m_id);
        $mer_detail = $db->getone("merchants");
         $merchant_name=$mer_detail['merchant_name'];
         $address1=$mer_detail['address1'];
         $countrys=$mer_detail['country'];
         $country=substr($countrys,0,2);
         $mcc=$mer_detail['mcc'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $currency_code=$mer_detail['currency_code'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $am_status=$mer_detail['am_status'];
         

         $db->where('vendor','online');
         $cur_res = $db->getOne('alipay_config');

         // $patner_query="SELECT * FROM alipay_config WHERE merchant_id='$mer_map_id'";
         // $sec_mer_res2 = $db->rawQuery($patner_query);
         // $partner_id=$sec_mer_res2[0]['partner_id'];
         // $key_md5=$sec_mer_res2[0]['key_md5'];
         $partner_id = $cur_res['partner_id'];
         $key_md5 = $cur_res['key_md5'];
         /* Making sign as a SESSION variable ,use in MD5*/


         $array = array(
            "merchant_name"=>$merchant_name,
            "address1"=>$address1,
            "country"=>$country,
            "mcc"=>$mcc,
            "mer_map_id"=>$mer_map_id,
            "currency_code"=>$currency_code,
            "am_status"=>$am_status,
            "partner_id"=>$partner_id,
            "key_md5"=>$key_md5,
            
         );
         $result=json_encode($array);
         echo $result;
        
    }

    if($json->type == 'online_GMP_status') {
        $db->where('mer_map_id', $json->m_id);
        $mer_detail = $db->getone("merchants");
         $merchant_name=$mer_detail['merchant_name'];
         $mer_map_id=$mer_detail['mer_map_id'];
         $am_status=$mer_detail['am_status'];
         

         $db->where('vendor','online');
         $cur_res = $db->getOne('alipay_config');

         // $patner_query="SELECT * FROM alipay_config WHERE merchant_id='$mer_map_id'";
         // $sec_mer_res2 = $db->rawQuery($patner_query);
         // $partner_id=$sec_mer_res2[0]['partner_id'];
         // $key_md5=$sec_mer_res2[0]['key_md5'];
         $partner_id = $cur_res['partner_id'];
         $key_md5 = $cur_res['key_md5'];
         /* Making sign as a SESSION variable ,use in MD5*/


         $array = array(
            "merchant_name"=>$merchant_name,
            "mer_map_id"=>$mer_map_id,
            "am_status"=>$am_status,
            "partner_id"=>$partner_id,
            "key_md5"=>$key_md5,
            
         );
         $result=json_encode($array);
         echo $result;
        
    }


}


/*Merchant Status Change */
if($json->type == 'merchantstatus') {
        // echo $json->m_id;
        $merchants_query="SELECT * FROM merchants WHERE mer_map_id='$json->m_id'";
        $pre_merchant_status = $db->rawQuery($merchants_query);
         $merchant_status=$pre_merchant_status[0]['is_active'];
         echo $merchant_status;
    }

    if($json->type == 'terminalstatus') {
        $terminal_query="SELECT * FROM terminal WHERE mso_terminal_id='$json->t_id'";
        $pre_terminal_status = $db->rawQuery($terminal_query);
        $terminal_status=$pre_terminal_status[0]['active'];
        echo $terminal_status;
         //echo $ter_detail;
    }

	/* Merchant Registration post data Ends here*/




if(isset($_POST['searchtype']) && $_POST['searchtype'] == 'report') {
	// echo "<pre>";
	// print_r($_POST);
	// exit;

	$start_end = explode('-', $_POST['date2']);

	$start_date = (isset($_POST['date2']) && $start_end[0]!='') ? date('Y-m-d 00:00:00',strtotime($start_end[0])) : '';
	$end_date   = (isset($_POST['date2']) && $start_end[1]!='') ? date('Y-m-d 23:59:59',strtotime($start_end[1])) : '';

	// echo $start_date."=>".$end_date; exit;

	// $start_date = (isset($_POST['date_timepicker_start']) && $_POST['date_timepicker_start']!='') ? $_POST['date_timepicker_start'] : '';
	// $end_date   = (isset($_POST['date_timepicker_end']) && $_POST['date_timepicker_end']!='') ? $_POST['date_timepicker_end'] : '';
	$currencies = (isset($_POST['currencies']) && $_POST['currencies']!='') ? $_POST['currencies'] : '';
	$trans_type = (isset($_POST['transaction_type']) && $_POST['transaction_type']!='') ? $_POST['transaction_type'] : '';
	$merchants  = (isset($_POST['merchants']) && $_POST['merchants']!='') ? $_POST['merchants'] : '';
	$terminals  = (isset($_POST['terminal_id']) && $_POST['terminal_id']!='') ? $_POST['terminal_id'] : '';

	$query = "SELECT * FROM transaction_alipay WHERE trans_datetime >= '$start_date' AND trans_datetime <= '$end_date' AND transaction_type!='cb3'AND transaction_type!='3'AND transaction_type!='s3'";

	if($currencies!='') {
		$query.= " AND currency='$currencies'";
	}

	if($trans_type!='') {
		$query.= " AND transaction_type='$trans_type'";
	}

	$buyer_field = '';
	if($merchants!='') {
		$buyer_field = '<th>Buyer Phone</th>';
		$query.= " AND merchant_id='$merchants'";
	}

	if($terminals!='') {
		$query.= " AND terminal_id='$terminals'";
	}


    $query.= " ORDER BY trans_datetime DESC";
    $result = 'No Transactions Found';
    $transactions = $db->rawQuery($query);

    $t=$_POST['t'];
    $i = 0;
    if(!empty($transactions)){
        $result = '<table id="example" class="table table-striped table-bordered w-100">';  
        
        $result .= '<thead>
                        <tr>
                            <th class="wd-15p">S.No</th>
                            <th class="wd-15p">Transaction<br>Type</th>
                            <th class="wd-15p">Merchant Name<br>Out Trade Number</th>                      
                            <th class="wd-15p">Terminal ID</th>
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Transaction<br>Date</th>                
                            <th class="wd-15p">Amount(LKR)</th>
                            <th class="wd-15p">Amount(USD)</th>                
                            <th class="wd-15p">View</th>                
                            
                        </tr>
                    </thead>
                    <tbody>';
        foreach($transactions as $tr) {
            $i++;
            $t_id = $tr["id_transaction_id"]; // $_GET['t_id'];

            $transaction_type='';
            if($tr['transaction_type'] == 1) {
                $transaction_type = 'POS - SALE';
            } else if($tr['transaction_type'] == 2) {
                $transaction_type = 'POS - REFUND';
            } else if($tr['transaction_type'] == 3) {
                $transaction_type = 'POS - QUERY';
            } else if($tr['transaction_type'] == 4) {
                $transaction_type = 'POS - CANCEL';
            } else if($tr['transaction_type'] == 's1') {
                $transaction_type = 'QR - SALE';
            } else if($tr['transaction_type'] == 's2') {
                $transaction_type = 'QR - REFUND';
            } else if($tr['transaction_type'] == 's3') {
                $transaction_type = 'QR - QUERY';
            } else if($tr['transaction_type'] == 's4') {
                $transaction_type = 'QR - CANCEL';
            } else if($tr['transaction_type'] == 'cb1') {
                $transaction_type = 'CBP - SALE';
            } else if($tr['transaction_type'] == 'cb2') {
                $transaction_type =  'CBP - REFUND';
            } else if($tr['transaction_type'] == 'cb3') {
                $transaction_type =  'CBP - QUERY';
            }

            $transaction_amount='';
            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                $transaction_amount = number_format($tr["refund_amount"],2);
            } else if($tr['transaction_type'] == 'cb1') {
                $transaction_amount_LKR = number_format($tr["amount"],2);
                $transaction_amount_USD =number_format($tr["total_fee"],2);
            } 
            else if($tr['transaction_type'] == 'cb2') {
                $transaction_amount_LKR = number_format($tr["amount"],2);
                $transaction_amount_USD =number_format($tr["refund_amount"],2);
            } else {
                $transaction_amount = number_format($tr["total_fee"],2);    
            }

            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                if($tr['trade_status']!="") { 
                    $sta=$tr['trade_status']; 
                } else { 
                    $sta="ACK_NOT_RECEIVED"; 
                }
            }else if ($tr['transaction_type'] == 'cb1'){
               if($tr['trade_status'] =="TRADE_FINISHED" && $tr['result_code']=='SUCCESS') { 
                    $sta="Approved"; 
                } else { 
                    //$sta="Declined"; 
                    $sta="Awaiting completion"; 
                }
            }else if ($tr['transaction_type'] == 'cb2'){
               if($tr['refund_status'] =="REFUND_SUCCESS" && $tr['result_code']=='SUCCESS' || $tr['is_success']=='T') { 
                    $sta="Approved"; 
                } else { 
                    //$sta="Declined"; 
                    $sta="Awaiting completion";
                }
            } 
            else {
                $sta=$tr['result_code'];
            }

            if($tr['transaction_type'] == 'cb2'){ 
                $trans_out_trade_no= $tr["out_return_no"];
                } else {
                    $trans_out_trade_no= $tr['out_trade_no'];
                }

                if($tr['transaction_type'] == 'cb2') { 
                    $trans_partner_trans_id = $tr["out_trade_no"];
                }else{ 
                     $trans_partner_trans_id = $tr['partner_trans_id'];
                }

            if ($tr['transaction_type'] == 'cb1' || $tr['transaction_type'] == 'cb2') {
                
               $currency =  $tr["source_currency"];

            } else {

                $currency =  $tr["currency"];
            }

            $buyer_field_data = '';
            if($buyer_field!='') {
                $buyer_field_data = '<td>'.$tr["res_field_1"].'</td>';
            }

            $db->where("mer_map_id", $tr['merchant_id']);
		    $datacon =$db->getOne('merchants');
		  $merchant_name=$datacon['merchant_name'];
           //  $merchant_name = substr($datacon['merchant_name'],0,20);
            $trans_out_trade_no_digits  =  substr($trans_out_trade_no,0,12);
            $trans_out_trade_no_second_digits  =  substr($trans_out_trade_no,12);


            if($tr['transaction_type'] == 'cb1' || $tr['transaction_type'] == 'cb2' || $tr['transaction_type'] == 'cb3') {

            $result .= '<tr >
                            <td>'.$i.'</td>
                            <td>'.$transaction_type.'</td>
                            <td >'.$merchant_name.'<br>'.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits.'
                            </td>
                            <td>'.$tr["terminal_id"].'</td>                         
                            <td>'.$sta.'</td>
                            <td>'.$tr["trans_datetime"].'</td>
                            <td>'.'LKR '.$transaction_amount_LKR.'</td>     
                            <td>'.'USD '.$transaction_amount_USD.'</td>              
                            <td> <a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>

                        </tr>';
            } elseif($currency=="USD") {
                 $result .= '<tr >
                            <td>'.$i.'</td>
                            <td>'.$transaction_type.'</td>
                            <td>'.$merchant_name.'<br>'.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits.'</span>
                            </td>
                            <td>'.$tr["terminal_id"].'</td>                         
                            <td>'.$sta.'</td>
                            <td>'.$tr["trans_datetime"].'</td>
                            <td></td> 
                            <td>'.$currency.' '.$transaction_amount.'</td>                         
                           <td align="center"><a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                        </tr>';
            }else {
                 $result .= '<tr >
                            <td>'.$i.'</td>
                            <td>'.$transaction_type.'</td>
                            <td >'.$merchant_name.'<br>'.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits.'</span>
                            </td>
                            <td>'.$tr["terminal_id"].'</td>                         
                            <td>'.$sta.'</td>
                            <td>'.$tr["trans_datetime"].'</td>
                            <td>'.$currency.' '.$transaction_amount.'</td> 
                            <td></td>                         
                            <td align="center"><a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                        </tr>';
            }

        }
        $result .= '</tbody></table>';
    } else {
        $result = "No transactions History";
    }
    echo $result;

}

// Merchant id and/or Terminal id already exists or not
if($_POST['merchant_id']!="") {
	$db->where('mer_map_id', $_POST['merchant_id']);
    $lastid = $db->getone("merchants");
    // do check
	if ( $lastid['idmerchants']!= "" ) {

		// echo $_POST['terminal_id'] ? $_POST['terminal_id'] : 0;
		if(isset($_POST['terminal_id'])) {
			$db->where('idmerchants', $lastid['idmerchants']);
			$db->where('mso_terminal_id', $_POST['terminal_id']);
			$terminal_lastid = $db->getone("terminal");

			$db->where('mso_terminal_id', $_POST['terminal_id']);
			$terminal_idlast = $db->getone("terminal");

			if( ($terminal_lastid['id'] == "" && $terminal_idlast['id'] != "") || ($terminal_lastid['id'] != "" && $terminal_idlast['id'] != "") ) {
				$response->result = true;
			} else {
				$response->result = false;
			}
		} else {
			$response->result = true;
		}
	    
	} else {
	    $response->result = false;
	}
	echo json_encode($response);
}

function getCCType($CCNumber) {
	$creditcardTypes = array(
						array('Name'=>'American Express','cardLength'=>array(15),'cardPrefix'=>array('34', '37'))
						,array('Name'=>'Maestro','cardLength'=>array(12, 13, 14, 15, 16, 17, 18, 19),'cardPrefix'=>array('5018', '5020', '5038', '6304', '6759', '6761', '6763'))
						,array('Name'=>'Mastercard','cardLength'=>array(16),'cardPrefix'=>array('51', '52', '53', '54', '55'))
						,array('Name'=>'Visa','cardLength'=>array(13,16),'cardPrefix'=>array('4'))
						,array('Name'=>'JCB','cardLength'=>array(16),'cardPrefix'=>array('3528', '3529', '353', '354', '355', '356', '357', '358'))
						,array('Name'=>'Discover','cardLength'=>array(16),'cardPrefix'=>array('6011', '622126', '622127', '622128', '622129', '62213',
													'62214', '62215', '62216', '62217', '62218', '62219',
													'6222', '6223', '6224', '6225', '6226', '6227', '6228',
													'62290', '62291', '622920', '622921', '622922', '622923',
													'622924', '622925', '644', '645', '646', '647', '648',
													'649', '65'))
						,array('Name'=>'Solo','cardLength'=>array(16, 18, 19),'cardPrefix'=>array('6334', '6767'))
						,array('Name'=>'Unionpay','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('622126', '622127', '622128', '622129', '62213', '62214',
													'62215', '62216', '62217', '62218', '62219', '6222', '6223',
													'6224', '6225', '6226', '6227', '6228', '62290', '62291',
													'622920', '622921', '622922', '622923', '622924', '622925'))
						,array('Name'=>'Diners Club','cardLength'=>array(14),'cardPrefix'=>array('300', '301', '302', '303', '304', '305', '36'))
						,array('Name'=>'Diners Club US','cardLength'=>array(16),'cardPrefix'=>array('54', '55'))
						,array('Name'=>'Diners Club Carte Blanche','cardLength'=>array(14),'cardPrefix'=>array('300','305'))
						,array('Name'=>'Laser','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('6304', '6706', '6771', '6709'))
	);  
	$CCNumber= trim($CCNumber);
	$type='Unknown';
	foreach ($creditcardTypes as $card){
		if (! in_array(strlen($CCNumber),$card['cardLength'])) {
			continue;
		}
		$prefixes = '/^('.implode('|',$card['cardPrefix']).')/';            
		if(preg_match($prefixes,$CCNumber) == 1 ){
			$type= $card['Name'];
			break;
		}
	}
	return $type;
}

// function getUserType($id) {
// 	global $db;
// 	$db->where("id",$id);
//     $data = $db->getOne("users");
// 	return $data['user_type'];
// }

$iid = $_SESSION['iid'];

$usertype = getUserType($iid);

foreach ($_POST as $key => $value) {
	filter_input(INPUT_POST, $key);
	$$key = $_POST[$key];
	$key = $value;
}

$date_timepicker_start = $_POST['start_date']; // $date_timepicker_start;
$date_timepicker_end = $_POST['end_date']; // $date_timepicker_end;

if(isset($_POST['from_dash']) && $_POST['from_dash'] == 1) { // From Dashboard
	$sdate = date('Y-m-d H:i:s', strtotime($_POST['start_date']. ':00')); // $date_timepicker_start;
	$edate = date('Y-m-d H:i:s', strtotime($_POST['end_date']. ':59')); // $date_timepicker_end;
} else {
	$sdate = $_POST['start_date']; // $date_timepicker_start;
	$edate = $_POST['end_date']; // $date_timepicker_end;
}

$currencies = $_POST['currencies'];
$transaction_type = $_POST['transaction_type'];

// echo "<pre>";
// print_r($_POST); 
// echo "<br>";
// echo $sdate."=".$edate;
// exit;
// die();
$db->where('userid',$iid);
$merchants_details = $db->getone('merchants');

/**D- dynamic qr , S -static qr, W - Webpay , C- card pay ,N - Netbank ,U -UPI *****/
$payment_access = $merchants_details['payment_access'];

if($payment_access!='') {
        # code...
        $pay_access_str = explode("~",$payment_access);
        if (in_array("W", $pay_access_str)) {
            if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') {

                $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED' ) AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                $data1 = $db->rawQuery($que1);

                $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total ,SUM(transaction_alipay.amount) AS total_cbp FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime >='$sdate' AND transaction_alipay.trans_datetime <='$edate'";
                $data2 = $db->rawQuery($que2);

                // echo "<pre>";
                // print_r($data2); exit;
                $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                $data3 = $db->rawQuery($que3);

                if($data1) {
                    foreach($data1 as $var1){
                        $total_count = $var1['countt'];
                        $total_amount= $var1['total'];
                    }
                }
                if($data2) {
                    foreach($data2 as $var2){
                        $refund_count = $var2['countt'];
                        if($que2['total_cbp']!=''){
                            $refund_amount= $var2['total_cbp'];
                        }else{
                            $refund_amount= $var2['total'];
                    }
                    }
                }
                if($data3) {
                    foreach($data3 as $var3){
                        $cancel_count = $var3['countt'];
                        $cancel_amount= $var3['total'];
                    }
                }
                ?>
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Transactions Type</th>        
                            <th>Number of Transaction</th>
                            <th>Total Amount</th> 
                        </tr>        
                    </thead>        
                    <tbody>       
                        <tr scope="row">
                            <td>Total Sale Transactions</td>              
                            <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                            <td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>               
                        </tr>
                        <tr  scope="row">
                            <td>Total Refund Transactions</td>                
                            <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                            <td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>                
                        </tr>
                        <tr scope="row" style="display: none">
                            <td>Total Cancel Transactions </td>               
                            <td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
                            <td><?php if($cancel_amount==''){ echo '0';} else { echo "-".money_format('%!i', $cancel_amount); } ?></td>             
                        </tr>       
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th rowspan="1" colspan="1">Transactions</th>       
                            <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                            <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                        </tr>
                    </tfoot> -->
                </table>
                <?php

            } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type!='0') {
                    if($transaction_type == 'sale') {
                        $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data1 = $db->rawQuery($que1);
                        if($data1) {
                            foreach($data1 as $var1){
                                $total_count = $var1['countt'];
                                $total_amount= $var1['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                <th>Transactions Type</th>        
                                <th>Number of Transaction</th>
                                <th>Total Amount</th>
                                </tr>         
                            </thead>        
                        <tbody>       
                            <tr scope="row">
                                <td class="sorting_1">Total Sale Transactions</td>              
                                <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                                <td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>                   
                            </tr>        
                        </tbody>
                        <!-- <tfoot>
                        <tr>
                        <th rowspan="1" colspan="1">Transactions</th>  
                        <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                        <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                        </tr>
                        </tfoot> -->
                        </table>
                    <?php
                    } else if($transaction_type == 'refund') {
                        $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data2 = $db->rawQuery($que2);
                        if($data2) {
                            foreach($data2 as $var2){
                                $refund_count = $var2['countt'];
                                $refund_amount= $var2['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Transactions Type</th>        
                                    <th>Number of Transaction</th>
                                    <th>Total Amount</th>   
                                </tr>      
                            </thead>        
                            <tbody>  
                                <tr  scope="row">
                                    <td>Total Refund Transactions</td>                
                                    <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                                    <td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>            
                                </tr>       
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th rowspan="1" colspan="1">Transactions</th>      
                                    <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                                    <th tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                                </tr>
                            </tfoot> -->
                        </table> 
                        <?php
                    } else if($transaction_type == 'cancel') {
                        $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data3 = $db->rawQuery($que3);
                        if($data3) {
                            foreach($data3 as $var3){
                                $cancel_count = $var3['countt'];
                                $cancel_amount= $var3['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Transactions Type</th>        
                                    <th>Number of Transaction</th>
                                    <th>Total Amount</th>
                                </tr>         
                            </thead>        
                        <tbody> 
                            <tr scope="row" style="display: none">
                                <td class="sorting_1">Total Cancel Transactions </td>               
                                <td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
                                <td><?php if($cancel_amount==''){ echo '0';} else { echo "-".money_format('%!i', $cancel_amount); } ?></td> 
                            </tr>       
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th rowspan="1" colspan="1">Transactions</th>   
                                <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                                <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                            </tr>
                        </tfoot> -->
                        </table> 
                        <?php
                    }
            }
        } else {

           if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') {

                $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED' ) AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                $data1 = $db->rawQuery($que1);

                $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime >='$sdate' AND transaction_alipay.trans_datetime <='$edate'";
                $data2 = $db->rawQuery($que2);

                // echo "<pre>";
                // print_r($data2); exit;
                $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                $data3 = $db->rawQuery($que3);

                if($data1) {
                    foreach($data1 as $var1){
                        $total_count = $var1['countt'];
                        $total_amount= $var1['total'];
                    }
                }
                if($data2) {
                    foreach($data2 as $var2){
                        $refund_count = $var2['countt'];
                        $refund_amount= $var2['total'];
                    }
                }
                if($data3) {
                    foreach($data3 as $var3){
                        $cancel_count = $var3['countt'];
                        $cancel_amount= $var3['total'];
                    }
                }
                ?>
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Transactions Type</th>        
                            <th>Number of Transaction</th>
                            <th>Total Amount</th> 
                        </tr>        
                    </thead>        
                    <tbody>       
                        <tr>
                            <td>Total Sale Transactions</td>              
                            <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                            <td><?php if($total_amount==''){ echo '0'; } else { echo $total_amount; }?></td>               
                        </tr>
                        <tr>
                            <td>Total Refund Transactions</td>                
                            <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                            <td><?php if($refund_amount==''){ echo '0'; } else { echo "-". $refund_amount; } ?></td>                
                        </tr>
                        <tr scope="row">
                            <td>Total Cancel Transactions </td>               
                            <td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
                            <td><?php if($cancel_amount==''){ echo '0';} else { echo "-".$cancel_amount; } ?></td>             
                        </tr>       
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th rowspan="1" colspan="1">Transactions</th>       
                            <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                            <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                        </tr>
                    </tfoot> -->
                </table>
                <?php

            } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type!='0') {
                    if($transaction_type == 'sale') {
                        $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data1 = $db->rawQuery($que1);
                        if($data1) {
                            foreach($data1 as $var1){
                                $total_count = $var1['countt'];
                                $total_amount= $var1['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                <th>Transactions Type</th>        
                                <th>Number of Transaction</th>
                                <th>Total Amount</th>
                                </tr>         
                            </thead>        
                        <tbody>       
                            <tr scope="row">
                                <td>Total Sale Transactions</td>              
                                <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                                <td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>                   
                            </tr>        
                        </tbody>
                        <!-- <tfoot>
                        <tr>
                        <th rowspan="1" colspan="1">Transactions</th>  
                        <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                        <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                        </tr>
                        </tfoot> -->
                        </table>
                    <?php
                    } else if($transaction_type == 'refund') {
                        $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data2 = $db->rawQuery($que2);
                        if($data2) {
                            foreach($data2 as $var2){
                                $refund_count = $var2['countt'];
                                $refund_amount= $var2['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Transactions Type</th>        
                                    <th>Number of Transaction</th>
                                    <th>Total Amount</th>   
                                </tr>      
                            </thead>        
                            <tbody>  
                                <tr scope="row">
                                    <td>Total Refund Transactions</td>                
                                    <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                                    <td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>            
                                </tr>       
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th rowspan="1" colspan="1">Transactions</th>      
                                    <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                                    <th tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                                </tr>
                            </tfoot> -->
                        </table> 
                        <?php
                    } else if($transaction_type == 'cancel') {
                        $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
                        $data3 = $db->rawQuery($que3);
                        if($data3) {
                            foreach($data3 as $var3){
                                $cancel_count = $var3['countt'];
                                $cancel_amount= $var3['total'];
                            }
                        }
                        ?>
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Transactions Type</th>        
                                    <th>Number of Transaction</th>
                                    <th>Total Amount</th>
                                </tr>         
                            </thead>        
                        <tbody> 
                            <tr scope="row">
                                <td>Total Cancel Transactions </td>               
                                <td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
                                <td><?php if($cancel_amount==''){ echo '0';} else { echo "-".money_format('%!i', $cancel_amount); } ?></td> 
                            </tr>       
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th rowspan="1" colspan="1">Transactions</th>   
                                <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
                                <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
                            </tr>
                        </tfoot> -->
                        </table> 
                        <?php
                    }
            }
        }
   
} else {

    if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') {

    	$que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
    	$data1 = $db->rawQuery($que1);

    	$que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime >='$sdate' AND transaction_alipay.trans_datetime <='$edate'";
    	$data2 = $db->rawQuery($que2);

    	// echo "<pre>";
    	// print_r($data2); exit;
    	$que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
    	$data3 = $db->rawQuery($que3);

    	if($data1) {
    		foreach($data1 as $var1){
    			$total_count = $var1['countt'];
    			$total_amount= $var1['total'];
    		}
    	}
    	if($data2) {
    		foreach($data2 as $var2){
    			$refund_count = $var2['countt'];
    			$refund_amount= $var2['total'];
    		}
    	}
    	if($data3) {
    		foreach($data3 as $var3){
    			$cancel_count = $var3['countt'];
    			$cancel_amount= $var3['total'];
    		}
    	}

    	// if($total_amount == '') {
    	?>
    	<table class="table card-table table-vcenter text-nowrap">
    		<thead>
    			<tr>
    				<th>Transactions Type</th>        
    				<th>Number of Transaction</th>
    				<th>Total Amount</th> 
    			</tr>        
    		</thead>		
    		<tbody>       
    			<tr scope="row">
    				<td>Total Sale Transactions</td>				
    				<td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
    				<td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>				
    			</tr>
    			<tr scope="row">
    				<td>Total Refund Transactions</td>				
    				<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
    				<td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>				
    			</tr>
    			<tr scope="row">
    				<td>Total Cancel Transactions </td>				
    				<td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
    				<td><?php if($cancel_amount==''){ echo '0';} else { echo "-".money_format('%!i', $cancel_amount); } ?></td>				
    			</tr>		
    		</tbody>
    		<!-- <tfoot>
    			<tr>
    				<th rowspan="1" colspan="1">Transactions</th>       
    				<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
    				<th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
    			</tr>
    		</tfoot> -->
    	</table>
    	<?php

    } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type!='0') 
    {
    		if($transaction_type == 'sale') {
    			$que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
    			$data1 = $db->rawQuery($que1);
    			if($data1) {
    				foreach($data1 as $var1){
    					$total_count = $var1['countt'];
    					$total_amount= $var1['total'];
    				}
    			}
    			?>
    			<table class="table card-table table-vcenter text-nowrap">
    				<thead>
    					<tr>
    					<th>Transactions Type</th>        
    					<th>Number of Transaction</th>
    					<th>Total Amount</th>
    					</tr>         
    				</thead>		
    			<tbody>       
    				<tr scope="row">
    					<td>Total Sale Transactions</td>				
    					<td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
    					<td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>					
    				</tr>        
    			</tbody>
    			<!-- <tfoot>
    			<tr>
    			<th rowspan="1" colspan="1">Transactions</th>  
    			<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
    			<th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
    			</tr>
    			</tfoot> -->
    			</table>
    		<?php
    		} else if($transaction_type == 'refund') {
    			$que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
    			$data2 = $db->rawQuery($que2);
    			if($data2) {
    				foreach($data2 as $var2){
    					$refund_count = $var2['countt'];
    					$refund_amount= $var2['total'];
    				}
    			}
    			?>
    			<table class="table card-table table-vcenter text-nowrap">
    				<thead>
    					<tr>
    						<th>Transactions Type</th>        
    						<th>Number of Transaction</th>
    						<th>Total Amount</th>   
    					</tr>      
    				</thead>		
    				<tbody>  
    					<tr scope="row">
    						<td>Total Refund Transactions</td>				
    						<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
    						<td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>			
    					</tr>       
    				</tbody>
    				<!-- <tfoot>
    					<tr>
    						<th rowspan="1" colspan="1">Transactions</th>      
    						<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
    						<th tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
    					</tr>
    				</tfoot> -->
    			</table> 
    			<?php
    		} else if($transaction_type == 'cancel') {
    			$que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate'";
    			$data3 = $db->rawQuery($que3);
    			if($data3) {
    				foreach($data3 as $var3){
    					$cancel_count = $var3['countt'];
    					$cancel_amount= $var3['total'];
    				}
    			}
    			?>
    			<table class="table card-table table-vcenter text-nowrap">
    				<thead>
    					<tr>
    						<th>Transactions Type</th>        
    						<th>Number of Transaction</th>
    						<th>Total Amount</th>
    					</tr>         
    				</thead>		
    			<tbody> 
    				<tr scope="row">
    					<td>Total Cancel Transactions </td>				
    					<td><?php if($cancel_count=='') { echo '0';} else { echo $cancel_count; } ?></td>
    					<td><?php if($cancel_amount==''){ echo '0';} else { echo "-".money_format('%!i', $cancel_amount); } ?></td>	
    				</tr>		
    			</tbody>
    			<!-- <tfoot>
    				<tr>
    					<th rowspan="1" colspan="1">Transactions</th>   
    					<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
    					<th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
    				</tr>
    			</tfoot> -->
    			</table> 
    			<?php
    		}
    }

}
exit;

die();


if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='0') {

	$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' group by cancel_flag";
    $que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id Group By refund_flag";
    $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction_alipay";


						$data1 = $db->rawQuery($que1); 
						$data2 = $db->rawQuery($que2); 
						$data3 = $db->rawQuery($que3); 
							foreach($data1 as $var1){
								 $number_count = $var1['countc'];
								 $cancel_amount= $var1['total'];
							}
							foreach($data2 as $var2){
								 $refund_count = $var2['countr'];
								 $refund_amount= $var2['total'];
							}
							foreach($data3 as $var3){
								 $total_count = $var3['countt'];
								 $total_amount= $var3['total'];
							}
  ?>
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>       
          <tr scope="row">
			<td>Total Sale Transactions</td>				
				<td><?php echo $total_count; ?></td>
				<td><?php echo $total_amount; ?></td>			
		  </tr>
        <tr scope="row">
			<td>Total Refund Transactions</td>				
				<td><?php echo $refund_count; ?></td>
				<td><?php echo $refund_amount; ?></td>				
		  </tr>
        <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php echo $number_count; ?></td>
				<td><?php echo $cancel_amount; ?></td>				
		  </tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th> 
		<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
        <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
        </tr>
        </tfoot>
      </table>   
	  
<?php } else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='refund') { 
		$que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id Group By refund_flag";
				$data2 = $db->rawQuery($que2); 
				foreach($data2 as $var2){
					 $refund_count = $var2['countr'];
					 $refund_amount= $var2['total'];
							
				}
?>
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
		<tbody>
        <tr scope="row">
			<td>Total Refund Transactions</td>				
				<td><?php echo $refund_count; ?></td>
				<td><?php echo $refund_amount; ?></td>				
		  </tr>     
		</tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>     
			<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>  
<?php  }  else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='sale' ) { 
		$que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction";
				$data3 = $db->rawQuery($que3); 
					foreach($data3 as $var3){
						 $total_count = $var3['countt'];
						 $total_amount= $var3['total'];
								
					} 	 	
?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>
		      <tbody>             
        <tr scope="row">
			<td>Total Refund Transactions</td>				
				<td><?php echo $total_count; ?></td>
				<td><?php echo $total_amount; ?></td>				
		  </tr>   
		</tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>             
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>  

<?php  } else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='cancel') {  

		$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' group by cancel_flag";   
				$data1 = $db->rawQuery($que1);
				foreach($data1 as $var1){
					 $number_count = $var1['countc'];
					 $cancel_amount= $var1['total'];
							
} ?>
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>  
        <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php echo $number_count; ?></td>
				<td><?php echo $cancel_amount; ?></td>
			</tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>       
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>   
 
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') { 

	$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' AND  trans_datetime>='$sdate' AND trans_datetime<='$edate' group by cancel_flag ";
   $que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id where trans_datetime>='$sdate' AND trans_datetime<='$edate' Group By refund_flag ";
   $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction where trans_datetime>='$sdate' AND trans_datetime<='$edate'";
					$data1 = $db->rawQuery($que1); 
					$data2 = $db->rawQuery($que2); 
					$data3 = $db->rawQuery($que3); 
		foreach($data1 as $var1){
			 $number_count = $var1['countc'];
			 $cancel_amount= $var1['total'];
					
		}
		foreach($data2 as $var2){
			 $refund_count = $var2['countr'];
			 $refund_amount= $var2['total'];
					
		}
		foreach($data3 as $var3){
			 $total_count = $var3['countt'];
			 $total_amount= $var3['total'];
					
		}
 ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
     <tbody>       
          <tr scope="row">
			<td >Total Sale Transactions</td>				
				<td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
				<td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>				
		  </tr>
        <tr scope="row">
			<td>Total Refund Transactions</td>				
				<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
				<td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>				
		  </tr>
        <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php if($number_count=='') { echo '0';} else { echo $number_count; } ?></td>
				<td><?php if($cancel_amount==''){ echo '0';} else { echo $cancel_amount; } ?></td>				
		  </tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>       
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>   

<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='cancel'){ 
	$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' AND  trans_datetime>='$sdate' AND trans_datetime<='$edate' group by cancel_flag "; 
				$data1 = $db->rawQuery($que1); 
				foreach($data1 as $var1){
					 $number_count = $var1['countc'];
					 $cancel_amount= $var1['total'];
}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody> 
          <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php if($number_count=='') { echo '0';} else { echo $number_count; } ?></td>
				<td><?php if($cancel_amount==''){ echo '0';} else { echo $cancel_amount; } ?></td>	
				
		  </tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>   
			<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table> 
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='refund') {
   $que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id where trans_datetime>='$sdate' AND trans_datetime<='$edate' Group By refund_flag ";   
		$data2 = $db->rawQuery($que2); 
		foreach($data2 as $var2){
			 $refund_count = $var2['countr'];
			 $refund_amount= $var2['total'];					
		}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>  
           <tr  scope="row">
			<td >Total Refund Transactions</td>				
			<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
				<td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>			
		  </tr>       
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>      
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>  
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='sale') {
	   $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction where trans_datetime>='$sdate' AND trans_datetime<='$edate'";
  
			$data3 = $db->rawQuery($que3); 
			foreach($data3 as $var3){
				 $total_count = $var3['countt'];
				 $total_amount= $var3['total'];						
			}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>       
          <tr scope="row">
			<td>Total Sale Transactions</td>				
				<td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
				<td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>					
		  </tr>        
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>  
                <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>   


<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='USD' && $transaction_type=='0') { 

	$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' AND  trans_datetime>='$sdate' AND trans_datetime<='$edate' group by cancel_flag ";
   $que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id where trans_datetime>='$sdate' AND trans_datetime<='$edate' Group By refund_flag ";
   $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction where trans_datetime>='$sdate' AND trans_datetime<='$edate'";   
					 
					$data1 = $db->rawQuery($que1); 
					$data2 = $db->rawQuery($que2); 
					$data3 = $db->rawQuery($que3); 
							foreach($data1 as $var1){
								 $number_count = $var1['countc'];
								 $cancel_amount= $var1['total'];										
							}
							foreach($data2 as $var2){
								 $refund_count = $var2['countr'];
								 $refund_amount= $var2['total'];										
							}
							foreach($data3 as $var3){
								 $total_count = $var3['countt'];
								 $total_amount= $var3['total'];										
						}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
     <tbody>       
          <tr scope="row">
			<td>Total Sale Transactions</td>				
				<td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
				<td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>				
		  </tr>
        <tr scope="row">
			<td>Total Refund Transactions</td>				
				<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
				<td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>				
		  </tr>
        <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php if($number_count=='') { echo '0';} else { echo $number_count; } ?></td>
				<td><?php if($cancel_amount==''){ echo '0';} else { echo $cancel_amount; } ?></td>				
		  </tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>             
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>   
	
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='USD' && $transaction_type=='sale') {
	$que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction where trans_datetime>='$sdate' AND trans_datetime<='$edate'";
  
			$data3 = $db->rawQuery($que3); 
			foreach($data3 as $var3){
				 $total_count = $var3['countt'];
				 $total_amount= $var3['total'];						
			}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>       
          <tr scope="row">
			<td>Total Sale Transactions</td>				
				<td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
				<td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>			
		  </tr>        
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>  
                <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table> 
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='USD' && $transaction_type=='refund'){
$que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id where trans_datetime>='$sdate' AND trans_datetime<='$edate' Group By refund_flag ";   
		$data2 = $db->rawQuery($que2); 
		foreach($data2 as $var2){
			 $refund_count = $var2['countr'];
			 $refund_amount= $var2['total'];					
		}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>  
           <tr  scope="row">
			<td class="sorting_1">Total Refund Transactions</td>				
				<td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
				<td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>					
		  </tr>       
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>      
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>
<?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='USD' && $transaction_type=='cancel'){ 
$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' AND  trans_datetime>='$sdate' AND trans_datetime<='$edate' group by cancel_flag "; 
				$data1 = $db->rawQuery($que1); 
				foreach($data1 as $var1){
					 $number_count = $var1['countc'];
					 $cancel_amount= $var1['total'];
}
  ?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody> 
          <tr scope="row">
			<td>Total Cancel Transactions </td>				
				<td><?php if($number_count=='') { echo '0';} else { echo $number_count; } ?></td>
				<td><?php if($cancel_amount==''){ echo '0';} else { echo $cancel_amount; } ?></td>	
		  </tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>   
			<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>
<?php }  else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='USD' && $transaction_type=='refund') { 
		$que2="SELECT count(refund_flag) as countr,sum(r.refund_amount) as total FROM transaction t INNER JOIN refund r ON r.refund_id = t.refund_id Group By refund_flag";
				$data2 = $db->rawQuery($que2); 
				foreach($data2 as $var2){
					 $refund_count = $var2['countr'];
					 $refund_amount= $var2['total'];
							
				}
?>
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
		<tbody>
        <tr scope="row">
			<td class="sorting_1">Total Refund Transactions</td>				
				<td><?php echo $refund_count; ?></td>
				<td><?php echo $refund_amount; ?></td>				
		  </tr>     
		</tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>     
			<th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>  
<?php  }  else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='USD' && $transaction_type=='sale' ) { 
		$que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction";
				$data3 = $db->rawQuery($que3); 
					foreach($data3 as $var3){
						 $total_count = $var3['countt'];
						 $total_amount= $var3['total'];
								
					} 	 	
?>		
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>
		      <tbody>             
        <tr scope="row">
			<td class="sorting_1">Total Refund Transactions</td>				
				<td><?php echo $total_count; ?></td>
				<td><?php echo $total_amount; ?></td>				
		  </tr>   
		</tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>             
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>  

<?php  } else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='USD' && $transaction_type=='cancel') {  

		$que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' group by cancel_flag";   
				$data1 = $db->rawQuery($que1);
				foreach($data1 as $var1){
					 $number_count = $var1['countc'];
					 $cancel_amount= $var1['total'];
							
} ?>
<table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr><th>Transactions Type</th>        
          <th>Number of Transaction</th>
          <th>Total Amount</th>         
        </thead>		
      <tbody>  
        <tr scope="row">
			<td >Total Cancel Transactions </td>				
				<td><?php echo $number_count; ?></td>
				<td><?php echo $cancel_amount; ?></td>
			</tr>		
        </tbody>
      <tfoot>
        <tr><th rowspan="1" colspan="1">Transactions</th>       
          <th tabindex="0"  rowspan="1" colspan="1">Number of Transaction</th>
          <th  tabindex="0"  rowspan="1" colspan="1">Total Amount</th>
          </tr>
        </tfoot>
      </table>

<?php } else {
	echo "Please select Correct Options";
}
?>
<style type="text/css">
    .cell {
      max-width: 100px; /* tweak me please */
      white-space : nowrap;
      overflow : hidden;
    }

    .expand-small-on-hover:hover {
      max-width : 200px; 
      text-overflow: ellipsis;
      overflow: hidden;
      display: inline-block;
       position: relative;
      width: 100%;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
      vertical-align: top;
    }

    .expand-maximum-on-hover:hover {
      z-index: 1;
      max-width : initial; 
      overflow : hidden;
    }
</style>
