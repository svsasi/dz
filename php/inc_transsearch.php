<?php
include('../init.php');

// $iid = $_SESSION['iid'];

if(isset($_POST['session_id']) && $_POST['session_id']!='') {
    $iid = $_POST['session_id']; // $_SESSION['iid'];
} else if(isset($_GET['session_id']) && $_GET['session_id']!='') {
    $iid = $_GET['session_id']; // $_SESSION['iid'];
} else {
    $iid = $_SESSION['iid'];  
}

//Credit Card Type
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


$usertype = getUserType($iid);

foreach ($_POST as $key => $value) {
	filter_input(INPUT_POST, $key);
	$$key = $_POST[$key];
	$key = $value;
}

$processor_id=$_POST['processorid'];
$merchantid=$_POST['merchantid'];

if($usertype['merchant_id']!='') {
    if(isset($_POST['period_start_date1']) || $_POST['period_end_date1']!='') {
        $merchantid = 0;
    } else {
        $merchantid = $usertype['merchant_id'];
    }
} else if($_POST['merchantid']!='') {
	$merchantid	= $_POST['merchantid'];
} else {
	$merchantid	= 0;
}

if(isset($_POST['S_Date']) || $_POST['S_Date']!='') { // Select the date from dashboard
	$sdate=$_POST['S_Date']. '00:00:00';
	$edate=$_POST['S_Date']. '23:59:59';
}else if(isset($_POST['period_start_date1']) || $_POST['period_end_date1']!='') { // Select the date from dashboard
	$sdate=$_POST['period_start_date1']. ':00';
	$edate=$_POST['period_end_date1']. ':59';

} else {
	$sdate=$_POST['period_start_date']. '00:00:00';
	$edate=$_POST['period_end_date']. '23:59:59';
}

$val1= date('Y-m-d H:i:s', strtotime($sdate));
$val2= date('Y-m-d H:i:s', strtotime($edate));

$iiiiid=$_POST['merchantid'];

// echo $val1.'=>'.$val2;
// exit;

if($_GET['start_d']!='') {

    if($_GET['start_d']!='' && $_GET['end_d']!=''){
        $selectedDate1 = $_GET['start_d'];
        $selectedDate2 = $_GET['end_d'];
        $sdate_excel=$selectedDate1. ':00';
        $edate_excel=$selectedDate2. ':59';
        $val1_excel= date('Y-m-d H:i:s', strtotime($sdate_excel));
        $val2_excel= date('Y-m-d H:i:s', strtotime($edate_excel));

        $excel_selected_date = date('Y-m-d', strtotime($sdate_excel))."_".date('H:i', strtotime($sdate_excel))."_To_".date('Y-m-d', strtotime($edate_excel))."_".date('H:i', strtotime($edate_excel));
    } else {
        $selectedDate = $_GET['date'];
        $sdate_excel = $selectedDate . '00:00:00';
        $edate_excel = $selectedDate . '23:59:59';
        $val1_excel = date('Y-m-d H:i:s', strtotime($sdate_excel));
        $val2_excel = date('Y-m-d H:i:s', strtotime($edate_excel));

        $excel_selected_date = $selectedDate;
    }
    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3','cb3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' ORDER BY transaction_alipay.trans_datetime DESC";

    $result = 'No Transactions Found';
    $transactions_Excel = $db->rawQuery($query);

    // print_r($transactions_Excel);
    // echo "<pre>";
    // print_r($transactions_Excel); exit;

    header("Content-type:application/vnd.msexcel");
    header("Content-Disposition:attachment;filename=Transaction_Results_On_$excel_selected_date.xls");
    ?>
    <div><center><b>Transactions on <?php echo $excel_selected_date; ?></b></center></div>

    <TABLE width="100%" cellpadding="1" border="1" cellspacing="0">
        <tr>
            <td width="5%"><div id="wt_txt" align="center"><b>S.No</b></div></td>
            <td><div id="wt_txt" align="center"><b>Transaction Type</b></div></td>
            <td width="15%"><div id="wt_txt" align="center"><b>Out Trade Number</b></div></td>
            <td width="15%"><div id="wt_txt" align="center"><b>Refund Org ID</b></div></td>
            <td width="15%"><div id="wt_txt" align="center"><b>Terminal ID</b></div></td>
            <td width="15%"><div id="wt_txt" align="center"><b>Mobile Number</b></div></td>
            <td width="15%"><div id="wt_txt" align="center"><b>Status</b></div></td>
            <td><div id="wt_txt" align="center"><b>Transaction Date</b></div></td>
            <td><div id="wt_txt" align="center"><b>Amount</b></div></td>

        </tr>
        <?php $i=0; foreach($transactions_Excel as $tr) { $i++; ?>
            <?php
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
            }else if($tr['transaction_type'] == 'cb2') {
                $transaction_type =  'CBP - REFUND';
            } else if($tr['transaction_type'] == 'cb3') {
                $transaction_type =  'CBP - QUERY';
            }

           $transaction_amount='';
            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                $transaction_amount = number_format($tr["amount"],2);   
            } else if($tr['transaction_type'] == 'cb3') {
                setlocale(LC_MONETARY,"en_US");
                $transaction_amount = number_format($tr["total_fee"],2);
            } else if($tr['transaction_type'] == 'cb1') {
                //setlocale(LC_MONETARY,"en_US");
                $transaction_amount = number_format($tr["amount"],2);
            } else {
                $transaction_amount = number_format($tr["total_fee"],2);
            }

            if ($tr['transaction_type'] == 'cb1') {
                $currency = $tr["source_currency"]; 
            }elseif ($tr['transaction_type'] == 'cb2') {
                $currency = $tr["source_currency"]; 
            } 
            else {
                $currency = $tr["currency"];
            }

            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                if($tr['trade_status']!="") { 
                    //$sta=$tr['trade_status'];
                    $sta="Approved";
                } else { 
                    $sta="Declined"; 
                }
            }else if($tr['transaction_type'] == 'cb1'){
                if($tr['trade_status'] =="TRADE_FINISHED" && $tr['result_code']=='SUCCESS') { 
                    $sta="Approved"; 
                } else { 
                    //$sta="Declined"; 
                    $sta="Awaiting completion"; 
                }
            }else if($tr['transaction_type'] == "cb2" ) {
                                           // $sta=$tr['refund_status'];
            if($tr['refund_status'] =="REFUND_SUCCESS" && $tr['result_code']=='SUCCESS' || $tr['is_success']=='T') { 
                    $sta="Approved"; 
                } else { 
                    //$sta="Declined"; 
                    $sta="Awaiting completion";
                }
            } else if($tr['transaction_type'] == "cb3" ) {
                $sta=$tr['trade_status'];
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
            ?>
            <tr>
                <td align="center"><?php echo $i; ?></td>
                <td align="center" id="lt_edit"><?php echo $transaction_type; ?></td>
                <td align="center" id="lt_edit"><?php echo $trans_out_trade_no; ?></td>
                <td align="center" id="lt_edit"><?php echo $trans_partner_trans_id; ?></td>
                <td align="center" id="lt_edit"><?php if($tr['terminal_id']!="") { echo $tr['terminal_id']; } else { echo "--"; }?></td>
                 <td align="center" id="lt_edit"><?php if($tr['customer_phone']!="") { echo $tr['customer_phone']; } else { echo "--"; }?></td>
                <td align="center" id="lt_edit"><?php echo $sta; ?></td>
                <td align="center" id="lt_edit"><?php echo $tr["trans_datetime"]; ?></td>
                <td align="center" id="lt_edit"><?php echo $currency.' '.$transaction_amount; ?></td>
            </tr>
            
        <?php } ?>
    </TABLE>
    <?php

}
else {
    if($merchantid==0)
    {
        // echo "<br>Hi1";

        if($val1!='' && $val2!='' && $merchantid!=0){

            // echo "<br>Hi11";
            $query="SELECT * FROM transaction_alipay WHERE 
            trans_datetime >= '$val1' AND trans_datetime <= '$val2'";


            $result = 'No Transactions Found';
            $transactions = $db->rawQuery($query);

            if(!empty($transactions)){
                $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                $result .= '<thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>						
                            <th>Status</th>
                            <th>Amount</th>
                            <th>First Name</th>
                            <th>Address</th>						
                            <th>ZIP/Postal Code</th>
                            <th>Email</th>
                            <th>RRN</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach($transactions as $tr)
                {
                    $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                    $result .= '<tr class="gradeX">
                                    <td>'.$tr["id_transaction_id"].'</td>
                                    <td>'.$tr["server_datetime_trans"].'</td>							
                                    <td>'.$tr["condition"].'</td>						
                                    <td>'.$tr["amount"].'</td>
                                    <td>'.$tr["first_name"].'</td>							
                                    <td>'.$tr["address1"].'</td>							
                                    <td>'.$tr["postal_code"].'</td>
                                    <td>'.$tr["email"].'</td>
                                    <td>'.$tr["retrvl_refno"].'</td>
                                    <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                </tr>';
                }
                $result .= '</tbody></table>';
            }

            echo $result;

        } else {

            // echo "<br>Hi12";
            /**** Get the Month-wise Transaction List ****/
            if(isset($_POST['M_Date']) || $_POST['M_Date']!='') {

                // echo "<br>Hi12_1";
                $timestamp    = $_POST['M_Date'];
                $first_date = date('Y-m-01 00:00:00', $timestamp);
                $last_date  = date('Y-m-t 23:59:59', $timestamp); // A leap year!

                // echo strtotime('April 2018');
                // echo "<br>";
                // echo $first_date.' = '.$last_date;
                // echo "<br>";

                $query="SELECT transaction_alipay.id_transaction_id, transaction_alipay.merchant_id, transaction_alipay.out_trade_no, transaction_alipay.trade_no, transaction_alipay.trade_status, transaction_alipay.currency, transaction_alipay.total_fee, transaction_alipay.trans_datetime FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.trans_datetime >= '$first_date' AND transaction_alipay.trans_datetime <= '$last_date'";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);

                if(!isset($_GET['date'])) {
                    if(!empty($transactions)){

                        $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                        $result .= '<thead>
                                        <tr>
                                            <th>Transaction ID <br>with Type</th>
                                            <th>Out Trade Number</th>						
                                            <th>Terminal ID</th>
                                            <th>Status</th>
                                            <th>Date</th>				
                                            <th>Amount</th>				
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                        foreach($transactions as $tr) {

                            $t_id = $tr["id_transaction_id"]; // $_GET['t_id'];

                            $processor_part = '';

                            if($tr['transaction_type'] == 1) { 
                                if($tr['trade_status']!="") { 
                                    $sta=$tr['trade_status']; 
                                } else { 
                                    $sta="ACK_NOT_RECEIVED"; 
                                }
                            } else {
                                $sta=$tr['result_code'];
                            }

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
                            } 

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);
                            } else {
                                $transaction_amount = number_format($tr["total_fee"],2);    
                            }

                            // $result .= '<tr class="gradeX">
                            // 				<td>'.$tr["id_transaction_id"].'</td>
                            // 				<td>'.$tr["out_trade_no"].'</td>
                            // 				<td>'.$tr["trade_no"].'</td>
                            // 				<td>'.$sta.'</td>
                            // 				<td>'.$tr["trans_datetime"].'</td>
                            // 				<td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>
                            // 				<td align="center"><a href="transactiondetails_popup.php?t_id='.$tr["id_transaction_id"].'" class="load-modal" data-toggle="modal" data-target="#myModal2'.$tr["id_transaction_id"].'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td>
                            // 			</tr>';

                            $result .= '<tr class="gradeX">
                                            <td>'.$tr["id_transaction_id"].' / '.$transaction_type.'</td>
                                            <td>'.$tr["out_trade_no"].'</td>
                                            <td>'.$tr["terminal_id"].'</td>							
                                            <td>'.$sta.'</td>
                                            <td>'.$tr["trans_datetime"].'</td>
                                            <td>'.$tr["currency"].' '.$transaction_amount.'</td>							
                                            <td align="center"><a href="transactiondetails_popup.php?t_id='.$tr["id_transaction_id"].'" class="load-modal" data-toggle="modal" data-target="#myModal2'.$tr["id_transaction_id"].'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td>
                                        </tr>';

                            $result .= '<div id="myModal2'.$tr["id_transaction_id"].'" class="modal fade">
                                          <div class="modal-dialog modal-lg transaction_detail">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h3 id="termsLabel" class="modal-title">TERMS AND CONDITIONS</h3>
                                              </div>
                                              <div class="modal-body">
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>';
                        }
                    }
                }

                $result .= '</tbody></table>';
                echo $result;
            }

            /**** Get the Transaction List for Selected Date in Dashboard page ****/
            if(isset($_POST['S_Date']) || $_POST['S_Date']!='' || isset($_GET['date']) || $_GET['date']!='') {

                // echo "<br>Hi12_2";
                $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2'";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);

                // echo "<pre>";
                // print_r($transactions);
                // exit;
                $i = 0;
                if(!isset($_GET['date'])) {
                    if(!empty($transactions)){
                        $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                        $result .= '<thead>
                                        <tr>
                                            <th>S.No0</th>
                                            <th>Transaction Type</th>
                                            <th>Out Trade Number</th>						
                                            <th>Trade Number</th>
                                            <th>Status</th>
                                            <th>Date</th>				
                                            <th>Amount</th>				
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        foreach($transactions as $tr) {

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
                            }

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);
                            } else {
                                $transaction_amount = number_format($tr["total_fee"],2);    
                            }

                            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                                if($tr['trade_status']!="") { 
                                    $sta=$tr['trade_status']; 
                                } else { 
                                    $sta="ACK_NOT_RECEIVED"; 
                                }
                            } else {
                                $sta=$tr['result_code'];
                            }   

                            $result .= '<tr class="gradeX">
                                            <td style="text-align:center;">'.$i.'</td>
                                            <td>'.$transaction_type.'</td>
                                            <td>'.$tr["out_trade_no"].'</td>
                                            <td>'.$tr["trade_no"].'</td>							
                                            <td>'.$sta.'</td>
                                            <td>'.$tr["trans_datetime"].'</td>
                                            <td>'.$tr["currency"].' '.$transaction_amount.'</td>							
                                            <td align="center"><a href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td>
                                        </tr>';

                            // $result .= '<div id="myModal2'.$tr["id_transaction_id"].'" class="modal fade">
                            //               <div class="modal-dialog modal-lg transaction_detail">
                            //                 <div class="modal-content">
                            //                   <div class="modal-header">
                            //                     <h3 id="termsLabel" class="modal-title">TERMS AND CONDITIONS</h3>
                            //                   </div>
                            //                   <div class="modal-body">
                            //                   </div>
                            //                   <div class="modal-footer">
                            //                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            //                   </div>
                            //                 </div>
                            //               </div>
                            //             </div>';

                        }
                        $result .= '</tbody></table>';
                    }
                    echo $result;

                } else {

                    $selectedDate = $_GET['date'];
                    $sdate_excel=$selectedDate. '00:00:00';
                    $edate_excel=$selectedDate. '23:59:59';
                    $val1_excel= date('Y-m-d H:i:s', strtotime($sdate_excel));
                    $val2_excel= date('Y-m-d H:i:s', strtotime($edate_excel));

                    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' ORDER BY transaction_alipay.trans_datetime DESC";

                    $result = 'No Transactions Found';
                    $transactions_Excel = $db->rawQuery($query);

                    header("Content-type:application/vnd.msexcel");
                    header("Content-Disposition:attachment;filename=Transaction_Results_On_$selectedDate.xls");
                    ?>
                    <div><center><b>Transactions on <?php echo date('d-m-Y', strtotime($selectedDate)); ?></b></center></div>

                    <TABLE width="100%" cellpadding="1" border="1" cellspacing="0">
                        <tr>
                            <td width="5%"><div id="wt_txt" align="center"><b>S.No</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Transaction Type</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Out Trade Number</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Refund Org ID</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Terminal ID</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Status</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Transaction Date</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Amount</b></div></td>
                        </tr>
                        <?php $i=0; foreach($transactions_Excel as $tr) { $i++; ?>

                        <?php
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
                        }

                        $transaction_amount='';
                        if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                            $transaction_amount = number_format($tr["refund_amount"],2);
                        } else {
                            $transaction_amount = number_format($tr["total_fee"],2);    
                        }

                        if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                            if($tr['trade_status']!="") { 
                                $sta=$tr['trade_status']; 
                            } else { 
                                $sta="ACK_NOT_RECEIVED"; 
                            }
                        } else {
                            $sta=$tr['result_code'];
                        }
                        ?>
                        <tr>
                            <td align="center"><?php echo $i; ?></td>
                            <td align="center" id="lt_edit"><?php echo $transaction_type; ?></td>
                            <td align="center" id="lt_edit"><?php echo $tr["out_trade_no"]; ?></td>
                            <td align="center" id="lt_edit"><?php echo $tr["partner_trans_id"]; ?></td>
                            <td align="center" id="lt_edit"><?php if($tr['terminal_id']!="") { echo $tr['terminal_id']; } else { echo "--"; }?></td>
                            <td align="center" id="lt_edit"><?php echo $sta; ?></td>
                            <td align="center" id="lt_edit"><?php echo $tr["trans_datetime"]; ?></td>
                            <td align="center" id="lt_edit"><?php echo $tr["currency"].' '.$transaction_amount; ?></td>
                        </tr>
                        <?php } ?>
                    </TABLE>
                    <?php
                }

            } else if($_POST['period_start_date1']!='' && $_POST['period_end_date1']!='' && $merchantid=='0') {

                // echo "<br>Hi12_2";
                $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3','cb3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' ORDER BY transaction_alipay.trans_datetime DESC";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);

                // echo "<pre>";
                // print_r($transactions);
                // exit;
                $i = 0;
                if(!isset($_GET['date'])) {

                    if(!empty($transactions)){

                        $db->where('userid',$iid);
                        $merchant_details_payment = $db->getone('merchants');

                        $t = $_POST['t'];
                         //print_r($merchant_details_payment['payment_access']);
                        $payment_access_details = $merchant_details_payment['payment_access'];
                        $payment_access = array();
                        $payment_access = explode('~',$payment_access_details);
                        if ($payment_access_details !='' ) {

                          if (in_array("W", $payment_access)) {

                                $result = '<table id="example" class="table table-striped table-bordered w-100">';
                                $result .= '<thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Transaction<br>Type</th>
                                                <th>Out Trade Number</th>
                                                <th style="width: 242px;">Refund Org ID</th>                      
                                                <th>Status</th>
                                                <th>Transaction<br>Date</th>                
                                                <th>Amount</th>             
                                                <th>View</th>
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
                                        }  else if($tr['transaction_type'] == 'cb1') {
                                            $transaction_type = 'CBP - SALE';
                                        }  else if($tr['transaction_type'] == 'cb2') {
                                            $transaction_type =  'CBP - REFUND';
                                        }  else if($tr['transaction_type'] == 'cb3') {
                                            $transaction_type =  'CBP - QUERY';
                                        }

                                        $transaction_amount='';
                                        if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                                            $transaction_amount = number_format($tr["amount"],2);   
                                        } else if($tr['transaction_type'] == 'cb3') {
                                            setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        } else if($tr['transaction_type'] == 'cb1') {
                                            //setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["amount"],2);
                                        } else {
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        }

                                        if ($tr['transaction_type'] == 'cb1') {
                                            $currency = $tr["source_currency"]; 
                                        }elseif ($tr['transaction_type'] == 'cb2') {
                                            $currency = $tr["source_currency"]; 
                                        } 
                                        else {
                                            $currency = $tr["currency"];
                                        }

                                        if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                                            if($tr['trade_status']!="") { 
                                                //$sta=$tr['trade_status'];
                                                $sta="Approved";
                                            } else { 
                                                $sta="Declined"; 
                                            }
                                        } else if($tr['transaction_type'] == 'cb1'){
                                            if($tr['trade_status'] =="TRADE_FINISHED" && $tr['result_code']=='SUCCESS') { 
                                                $sta="Approved"; 
                                            } else { 
                                                //$sta="Declined"; 
                                                $sta="Awaiting completion"; 
                                            }
                                        }else if($tr['transaction_type'] == "cb2" ) {
                                           // $sta=$tr['refund_status'];
                                            if($tr['refund_status'] =="REFUND_SUCCESS" && $tr['result_code']=='SUCCESS' || $tr['is_success']=='T') { 
                                                $sta="Approved"; 
                                            } else { 
                                                //$sta="Declined"; 
                                                $sta="Awaiting completion";
                                            }
                                        }else if($tr['transaction_type'] == "cb3" ) {
                                            $sta=$tr['trade_status'];
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
                                        
                                        $result .= '<tr>
                                                        <td style="text-align:center;">'.$i.'</td>
                                                        <td>'.$transaction_type.'</td>
                                                        <td class="cell expand-maximum-on-hover"><span>'.$trans_out_trade_no.'</span></td>
                                                        <td class="expand-small-on-hover"><span>'.$trans_partner_trans_id.'</span></td>                        
                                                        <td>'.$sta.'</td>
                                                        <td>'.$tr["trans_datetime"].'</td>
                                                        <td>'.$currency.' '.$transaction_amount.'</td>                          
                                                       
                                                        <td> <a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                                                    </tr>';

                                }


                          } else {
                                 $result = '<table id="example" class="table table-striped table-bordered w-100">';
                                    $result .= '<thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Transaction<br>Type</th>
                                                <th>Out Trade Number</th>
                                                <th>Refund Org ID</th>                      
                                                <th>Terminal ID</th>
                                                <th>Status</th>
                                                <th>Transaction<br>Date</th>                
                                                <th>Amount</th>             
                                                <th>View</th>
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
                                        }  else if($tr['transaction_type'] == 'cb1') {
                                            $transaction_type = 'CBP - SALE';
                                        }  else if($tr['transaction_type'] == 'cb2') {
                                            $transaction_type =  'CBP - REFUND';
                                        }  else if($tr['transaction_type'] == 'cb3') {
                                            $transaction_type =  'CBP - QUERY';
                                        }

                                        $transaction_amount='';
                                        if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                                            $transaction_amount = number_format($tr["amount"],2);   
                                        } else if($tr['transaction_type'] == 'cb3') {
                                            setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        } else if($tr['transaction_type'] == 'cb1') {
                                            //setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["amount"],2);
                                        } else {
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        }

                                        if ($tr['transaction_type'] == 'cb1') {
                                            $currency = $tr["source_currency"]; 
                                        }elseif ($tr['transaction_type'] == 'cb2') {
                                            $currency = $tr["source_currency"]; 
                                        } 
                                        else {
                                            $currency = $tr["currency"];
                                        }

                                        if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
                                            if($tr['trade_status']!="") { 
                                                //$sta=$tr['trade_status'];
                                                $sta="Approved";
                                            } else { 
                                                $sta="Declined"; 
                                            }
                                        }else if($tr['transaction_type'] == "cb2" ) {
                                           // $sta=$tr['refund_status'];
                                            if($tr['refund_status']="REFUND_SUCCESS")
                                            {
                                                $sta="Approved";
                                            }
                                            else
                                            {
                                                $sta="Declined";
                                            }
                                        }else if($tr['transaction_type'] == "cb3" ) {
                                            $sta=$tr['trade_status'];
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
                                        
                                        $result .= '<tr>
                                                        <td>'.$i.'</td>
                                                        <td>'.$transaction_type.'</td>
                                                        <td class="expand-small-on-hover"><span>'.$trans_out_trade_no.'</span></td>
                                                        <td class="expand-small-on-hover"><span>'.$trans_partner_trans_id.'</span></td>
                                                        <td>'.$tr["terminal_id"].'</td>                         
                                                        <td>'.$sta.'</td>
                                                        <td>'.$tr["trans_datetime"].'</td>
                                                        <td>'.$currency.' '.$transaction_amount.'</td>                          
                                                       <td> <a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                                                    </tr>';

                                    }
                          }

                          
                        } else {
                                    $result = '<table id="example" class="table table-striped table-bordered w-100">';
                                    $result .= '<thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Transaction<br>Type</th>
                                                <th>Out Trade Number</th>
                                                <th>Refund Org ID</th>						
                                                <th>Terminal ID</th>
                                                <th>Status</th>
                                                <th>Transaction<br>Date</th>				
                                                <th>Amount</th>				
                                                <th>View</th>
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
                                        }  else if($tr['transaction_type'] == 'cb1') {
                                            $transaction_type = 'CBP - SALE';
                                        }  else if($tr['transaction_type'] == 'cb2') {
                                            $transaction_type =  'CBP - REFUND';
                                        }  else if($tr['transaction_type'] == 'cb3') {
                                            $transaction_type =  'CBP - QUERY';
                                        }

                                        $transaction_amount='';
                                        if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                                            $transaction_amount = number_format($tr["amount"],2);   
                                        } else if($tr['transaction_type'] == 'cb3') {
                                            setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        } else if($tr['transaction_type'] == 'cb1') {
                                            //setlocale(LC_MONETARY,"en_US");
                                            $transaction_amount = number_format($tr["amount"],2);
                                        } else {
                                            $transaction_amount = number_format($tr["total_fee"],2);
                                        }

                                        if ($tr['transaction_type'] == 'cb1') {
                                            $currency = $tr["source_currency"]; 
                                        }elseif ($tr['transaction_type'] == 'cb2') {
                                            $currency = $tr["source_currency"]; 
                                        } 
                                        else {
                                            $currency = $tr["currency"];
                                        }

                                        if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
                                            if($tr['trade_status']!="") { 
                                                //$sta=$tr['trade_status'];
                                                $sta="Approved";
                                            } else { 
                                                $sta="Declined"; 
                                            }
                                        }else if($tr['transaction_type'] == "cb2" ) {
                                           // $sta=$tr['refund_status'];
                                            if($tr['refund_status']="REFUND_SUCCESS")
                                            {
                                                $sta="Approved";
                                            }
                                            else
                                            {
                                                $sta="Declined";
                                            }
                                        }else if($tr['transaction_type'] == "cb3" ) {
                                            $sta=$tr['trade_status'];
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
                                        
                                        $result .= '<tr>
                                                        <td >'.$i.'</td>
                                                        <td>'.$transaction_type.'</td>
                                                        <td class="cell expand-maximum-on-hover"><span>'.$trans_out_trade_no.'</span></td>
                                                        <td class="cell expand-maximum-on-hover"><span>'.$trans_partner_trans_id.'</span></td>
                                                        <td>'.$tr["terminal_id"].'</td>                         
                                                        <td>'.$sta.'</td>
                                                        <td>'.$tr["trans_datetime"].'</td>
                                                        <td>'.$currency.' '.$transaction_amount.'</td>                          
                                                        <td> <a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                                                    </tr>';
                                    }
                        }
                        $result .= '</tbody></table>';
                    }
                    echo $result;

                } else {

                    if($_GET['start_d']!='' && $_GET['end_d']!=''){
                        $selectedDate1 = $_GET['start_d'];
                        $selectedDate2 = $_GET['end_d'];
                        $sdate_excel=$selectedDate1. ':00';
                        $edate_excel=$selectedDate2. ':59';
                        $val1_excel= date('Y-m-d H:i:s', strtotime($sdate_excel));
                        $val2_excel= date('Y-m-d H:i:s', strtotime($edate_excel));

                    } else {
                        $selectedDate = $_GET['date'];
                        $sdate_excel = $selectedDate . '00:00:00';
                        $edate_excel = $selectedDate . '23:59:59';
                        $val1_excel = date('Y-m-d H:i:s', strtotime($sdate_excel));
                        $val2_excel = date('Y-m-d H:i:s', strtotime($edate_excel));
                    }
                    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' ORDER BY transaction_alipay.trans_datetime DESC";

                    $result = 'No Transactions Found';
                    $transactions_Excel = $db->rawQuery($query);

                    header("Content-type:application/vnd.msexcel");
                    header("Content-Disposition:attachment;filename=Transaction_Results_On_$selectedDate.xls");
                    ?>
                    <div><center><b>Transactions on <?php echo date('d-m-Y', strtotime($selectedDate)); ?></b></center></div>

                    <TABLE width="100%" cellpadding="1" border="1" cellspacing="0">
                        <tr>
                            <td width="5%"><div id="wt_txt" align="center"><b>S.No</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Transaction Type</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Out Trade Number</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Refund Org ID</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Terminal ID</b></div></td>
                            <td width="15%"><div id="wt_txt" align="center"><b>Status</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Transaction Date</b></div></td>
                            <td><div id="wt_txt" align="center"><b>Amount</b></div></td>
                        </tr>
                        <?php $i=0; foreach($transactions_Excel as $tr) { $i++; ?>

                            <?php
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
                            }

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);
                            } else {
                                $transaction_amount = number_format($tr["total_fee"],2);    
                            }

                            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1') { 
                                if($tr['trade_status']!="") { 
                                    $sta=$tr['trade_status']; 
                                } else { 
                                    $sta="ACK_NOT_RECEIVED"; 
                                }
                            } else {
                                $sta=$tr['result_code'];
                            }
                            ?>
                            <tr>
                                <td align="center"><?php echo $i; ?></td>
                                <td align="center" id="lt_edit"><?php echo $transaction_type; ?></td>
                                <td align="center" id="lt_edit"><?php echo $tr["out_trade_no"]; ?></td>
                                <td align="center" id="lt_edit"><?php echo $tr["partner_trans_id"]; ?></td>
                                <td align="center" id="lt_edit"><?php if($tr['terminal_id']!="") { echo $tr['terminal_id']; } else { echo "--"; }?></td>
                                <td align="center" id="lt_edit"><?php echo $sta; ?></td>
                                <td align="center" id="lt_edit"><?php echo $tr["trans_datetime"]; ?></td>
                                <td align="center" id="lt_edit"><?php echo $tr["currency"].' '.$transaction_amount; ?></td>
                            </tr>
                        <?php } ?>
                    </TABLE>
                    <?php

                }

            }
            /**** Get the All Transaction List (If any dates are not available) ****/
            else if($period_start_date=='' && $period_end_date=='' && $merchantid=='0') {

                 echo "<br>Hi12_3";
                if($usertype[0]['user_type']==1){
                    $query="SELECT * FROM transaction_alipay";
                } else {
                    $query="SELECT * FROM transaction_alipay";
                    //print_r($query);
                }
                // $query="SELECT * FROM transactions WHERE
                // server_datetime_trans >= '$val1' AND server_datetime_trans <= '$val2'";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);

                if(!empty($transactions)){


                    $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                    $result .= '<thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Out Trade Number</th>						
                                <th>Terminal ID</th>
                                <th>Status</th>
                                <th>Date</th>				
                                <th>Amount</th>				
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach($transactions as $tr) {
                        //$tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                        //if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                        $result .= '<tr class="gradeX">
                                            <td>'.$tr["id_transaction_id"].'</td>
                                            <td>'.$tr["out_trade_no"].'</td>
                                            <td>'.$tr["terminal_id"].'</td>							
                                            <td>'.$tr["trade_status"].'</td>
                                            <td>'.$tr["trans_datetime"].'</td>
                                            <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
                                            <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                        </tr>';
                    }
                    $result .= '</tbody></table>';
                }
                echo $result;
            }

            else if($period_start_date=='' && $period_end_date=='' && $merchantid!=0)
            {
            // echo "<br>merchant selected";
                    $merchantid=$_POST['merchantid'];
                    $query="SELECT * FROM merchants where idmerchants='$merchantid'";
                    $tran = $db->rawQuery($query);
                    $transaction1=$tran[0]['idmerchants'];
                    // print_r($transactions);
                    $query1="SELECT * FROM transaction_alipay where RIGHT(merchant_id, 3)='$transaction1'";
                    $result = 'No Transactions Found';
                    $transactions = $db->rawQuery($query1);

                        if(!empty($transactions)){
                            $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                            $result .= '<thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Out Trade Number</th>						
                                        <th>Terminal ID</th>
                                        <th>Status</th>
                                        <th>Date</th>				
                                        <th>Amount</th>				
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>';
                                foreach($transactions as $tr)
                                {
                                    $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                                    if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                                    $result .= '<tr class="gradeX">
                                                    <td>'.$tr["id_transaction_id"].'</td>
                                                    <td>'.$tr["out_trade_no"].'</td>
                                                    <td>'.$tr["terminal_id"].'</td>							
                                                    <td>'.$sta.'</td>
                                                    <td>'.$tr["trans_datetime"].'</td>
                                                    <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
                                                    <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                                </tr>';
                                }
                            $result .= '</tbody></table>';
            }

            echo $result;

            }
            else if($period_start_date!='' && $period_end_date!='' && $min_amount_range=='' && $max_amount_range=='' && $merchantid=='0'){


                    // print_r($transactions);
                    $query1="SELECT * FROM transaction_alipay where trans_datetime>='$period_start_date' AND period_start_date<='$period_start_date'";
                    $result = 'No Transactions Found';
                    $transactions = $db->rawQuery($query1);

                        if(!empty($transactions)){
                            $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                            $result .= '<thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Out Trade Number</th>						
                                        <th>Terminal ID</th>
                                        <th>Status</th>
                                        <th>Date</th>				
                                        <th>Amount</th>				
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>';
                                foreach($transactions as $tr)
                                {
                                    $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                                    if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                                    $result .= '<tr class="gradeX">
                                                    <td>'.$tr["id_transaction_id"].'</td>
                                                    <td>'.$tr["out_trade_no"].'</td>
                                                    <td>'.$tr["terminal_id"].'</td>							
                                                    <td>'.$sta.'</td>
                                                    <td>'.$tr["trans_datetime"].'</td>
                                                    <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
                                                    <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                                </tr>';
                                }
                            $result .= '</tbody></table>';
            }

            echo $result;

            }
            else if($period_start_date!='' && $period_end_date!='' && $min_amount_range=='' && $max_amount_range=='' && $merchantid!='0'){
            $merchantid=$_POST['merchantid'];
            $query="SELECT * FROM merchants where idmerchants='$merchantid'";
            $tran = $db->rawQuery($query);
            $transaction1=$tran[0]['idmerchants'];

            // print_r($transactions);

            $query1="SELECT * FROM transaction_alipay where RIGHT(merchant_id, 3)='$transaction1' AND trans_datetime='$period_start_date' AND trans_datetime='$period_end_date'";

            $result = 'No Transactions Found';
            $transactions = $db->rawQuery($query1);

            if(!empty($transactions)){
                $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                $result .= '<thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Out Trade Number</th>						
                            <th>Trade Number</th>
                            <th>Status</th>
                            <th>Date</th>				
                            <th>Amount</th>				
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($transactions as $tr)
                    {
                        $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                        if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                        $result .= '<tr class="gradeX">
                                        <td>'.$tr["id_transaction_id"].'</td>
                                        <td>'.$tr["out_trade_no"].'</td>
                                        <td>'.$tr["trade_no"].'</td>							
                                        <td>'.$sta.'</td>
                                        <td>'.$tr["trans_datetime"].'</td>
                                        <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
                                        <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                    </tr>';
                    }
                $result .= '</tbody></table>';
            }

            echo $result;

            }
            else if($period_start_date!='' && $period_end_date!='' && $min_amount_range!='' && $max_amount_range!='' && $merchantid=='0'){

    echo "testing";

            }
            else if($period_start_date!='' && $period_end_date!='' && $min_amount_range!='' && $max_amount_range!='' && $merchantid!='0'){

    echo "test part";

            }
            else{

                echo "Test part";
            }
        }

    } else {

        // echo "<br>Hi2";
        if($val1=='' && $val2=='' && $merchantid==0 ){

            //echo "<br>Hi21";
            $merchantid=$_POST['merchantid'];

            $querys="SELECT * FROM merchants where idmerchants='$merchantid'";
            $tran1 = $db->rawQuery($querys);
            $transaction2=$tran1[0]['mer_map_id'];


            $query="SELECT * FROM transaction_alipay WHERE merchant_id='$transaction2' AND trans_datetime >= '$val1' AND trans_datetime <= '$val2'";

            $result = 'No Transactions Found';
            $transactions = $db->rawQuery($query);

            if(!empty($transactions)){
                $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                $result .= '<thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>						
                            <th>Status</th>
                            <th>Amount</th>
                            <th>First Name</th>
                            <th>Address</th>						
                            <th>ZIP/Postal Code</th>
                            <th>Email</th>
                            <th>RRN</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach($transactions as $tr)
                {
                    $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                    //if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                    $result .= '<tr class="gradeX">
                                    <td>'.$tr["id_transaction_id"].'</td>
                                    <td>'.$tr["server_datetime_trans"].'</td>							
                                    <td>'.$tr["condition"].'</td>						
                                    <td>'.$tr["amount"].'</td>
                                    <td>'.$tr["first_name"].'</td>							
                                    <td>'.$tr["address1"].'</td>							
                                    <td>'.$tr["postal_code"].'</td>
                                    <td>'.$tr["email"].'</td>
                                    <td>'.$tr["retrvl_refno"].'</td>
                                    <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                </tr>';
                }
                $result .= '</tbody></table>';
            }

            echo $result;
        } else {

            // echo "<br>Hi22";
            $merchantid=$_POST['merchantid'];

            $query="SELECT * FROM merchants where idmerchants='$merchantid'";
            $tran = $db->rawQuery($query);
            $transaction1=$tran[0]['mer_map_id'];

            // print_r($transactions);

            $query1="SELECT * FROM transaction_alipay where RIGHT(merchant_id, 3)='$transaction1'";

            $result = 'No Transactions Found';
            $transactions = $db->rawQuery($query1);

            if(!empty($transactions)){
                $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                $result .= '<thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Out Trade Number</th>						
                            <th>Trade Number</th>
                            <th>Status</th>
                            <th>Date</th>				
                            <th>Amount</th>				
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($transactions as $tr)
                    {
                        $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
                        if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
                        $result .= '<tr class="gradeX">
                                        <td>'.$tr["id_transaction_id"].'</td>
                                        <td>'.$tr["out_trade_no"].'</td>
                                        <td>'.$tr["trade_no"].'</td>							
                                        <td>'.$sta.'</td>
                                        <td>'.$tr["trans_datetime"].'</td>
                                        <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
                                        <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
                                    </tr>';
                    }
                $result .= '</tbody></table>';
            }

            echo $result;
        }

    }
}
	
?>



<script type="text/javascript">

    $(document).ready(function() {

		// $('.load-modal').on('click', function(e){
		//     e.preventDefault();
		//     var url = $(this).attr('href');
		//     alert(url);

		//     $.ajax({
		//        url: url,
		//        type: 'post',
		//        async: false,
		//        success: function(response){
		//             // $('#ajax-response').html(response);
		//             $('#myModal').modal('show');
		//        }
		//     })
		// });

		$('a[data-toggle="modal"]').on('click', function(e) {

            // Remove saved data from sessionStorage
            sessionStorage.removeItem('modal_id');

		    // From the clicked element, get the data-target arrtibute
		    // which BS3 uses to determine the target modal
		    var target_modal = $(e.currentTarget).data('target'); 
            console.log(target_modal);

            //Save data to sessionStorage
            sessionStorage.setItem('modal_id', target_modal);

		    // also get the remote content's URL
		    var remote_content = e.currentTarget.href;

		    // Find the target modal in the DOM
		    var modal = $(target_modal);
		    // Find the modal's <div class="modal-body"> so we can populate it
		    var modalBody = $(target_modal + ' .modal-body');

		    // Capture BS3's show.bs.modal which is fires
		    // immediately when, you guessed it, the show instance method
		    // for the modal is called
		    modal.on('show.bs.modal', function () {
	            // use your remote content URL to load the modal body
	            modalBody.load(remote_content);
	        }).modal();
	        // and show the modal

            // modal.on('hidden.bs.modal', function () {
            //     window.location.reload();
            // });

            // e.preventDefault();

		    // Now return a false (negating the link action) to prevent Bootstrap's JS 3.1.1
		    // from throwing a 'preventDefault' error due to us overriding the anchor usage.
		    return false;
		});

        // Get saved data from sessionStorage
        var data_id = sessionStorage.getItem('modal_id');
        console.log(data_id);

        $('#myModal21094').on('hidden.bs.modal', function () {
            window.location.reload();
        });

        // $('#myModal21069').on('hidden.bs.modal', function () {
        //     window.location.reload();
        // });


	});
</script>