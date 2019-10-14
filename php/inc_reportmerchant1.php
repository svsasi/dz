<?php

include('../init.php');
//$iid = $_SESSION['iid'];

if(isset($_POST['merchantid'])){
    $iid=$_POST['merchantid'];
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

$iiiiiiiiid=$_POST['merchantid'];


if ($_POST['type']!=''&&isset($_POST['type'])&&$_POST['type']=='Delete') {
   
   $db->where('mer_map_id',$_POST['mer_map_id']);
   $db->delete();

}

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
    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' ORDER BY transaction_alipay.trans_datetime DESC";

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
            <td width="15%"><div id="wt_txt" align="center"><b>Status</b></div></td>
            <td><div id="wt_txt" align="center"><b>Transaction Date</b></div></td>
            <td><div id="wt_txt" align="center"><b>Amount</b></div></td>

        </tr>
        <?php 
        $i=0; 
        foreach($transactions_Excel as $tr) { 
            $i++; 
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
            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2' ||$tr['transaction_type'] == 'cb2') {
                $transaction_amount = number_format($tr["refund_amount"],2);
            } else {
                $transaction_amount = number_format($tr["total_fee"],2);    
            }

            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
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
else {
    if($merchantid==0)
    {
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
            /**** Get the Transaction List for Selected Date in Admin  Dashboard Merchant page ****/
            if(isset($_POST['S_Date']) || $_POST['S_Date']!='' || isset($_GET['date']) || $_GET['date']!='') {

                $t = $_POST['t'];

                $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' WHERE transaction_alipay.merchant_id='$iid'ORDER BY transaction_alipay.trans_datetime DESC";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);
                
                if(!isset($_GET['date'])) {
                    if(!empty($transactions)){
                        $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
                        $result .= '<thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Transaction Type</th>
                                            <th>Out Trade Number</th>						
                                            <th>Refund Org ID</th>
                                            <th>Status</th>
                                            <th>Date</th>				
                                            <th>Amount</th>				
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                         $i = 0;
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
                            }else if($tr['transaction_type'] == 'cb2') {
                                $transaction_type =  'CBP - REFUND';
                            } else if($tr['transaction_type'] == 'cb3') {
                                $transaction_type =  'CBP - QUERY';
                            }

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2' ||$tr['transaction_type'] == 'cb2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);
                            } else {
                                $transaction_amount = number_format($tr["total_fee"],2);    
                            }

                            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1' ||$tr['transaction_type'] == 'cb1') { 
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
                                            <td>'.$tr["partner_trans_id"].'</td>							
                                            <td>'.$sta.'</td>
                                            <td>'.$tr["trans_datetime"].'</td>
                                            <td>'.$tr["currency"].' '.$transaction_amount.'</td>							
                                            <td align="center"><a href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td>
                                        </tr>';

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
                        } else if($tr['transaction_type'] == 'cb1') {
                            $transaction_type = 'CBP - SALE';
                        }else if($tr['transaction_type'] == 'cb2') {
                            $transaction_type =  'CBP - REFUND';
                        } else if($tr['transaction_type'] == 'cb3') {
                            $transaction_type =  'CBP - QUERY';
                        }

                        $transaction_amount='';
                        if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                            $transaction_amount = number_format($tr["refund_amount"],2);
                        } else {
                            $transaction_amount = number_format($tr["total_fee"],2);    
                        }

                        if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
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

                //echo "<br>Hi12_2";
                print_r($_POST);
                die();
                exit;
                $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' ORDER BY transaction_alipay.trans_datetime DESC";

                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);

                // echo "<pre>";
                // print_r($transactions);
                // exit;
                $i = 0;
                if(!isset($_GET['date'])) {
                    if(!empty($transactions)){
                        $result = '<table id="example" class="table table-striped table-bordered w-100">';
                        $result .= '<thead>
                                        <tr>
                                            <th>S.No</th>
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
                            } else if($tr['transaction_type'] == 'cb1') {
                                $transaction_type = 'CBP - SALE';
                            }else if($tr['transaction_type'] == 'cb2') {
                                $transaction_type =  'CBP - REFUND';
                            } else if($tr['transaction_type'] == 'cb3') {
                                $transaction_type =  'CBP - QUERY';
                            }

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);   
                            } else if($tr['transaction_type'] == 'cb3') {
                                setlocale(LC_MONETARY,"en_US");
                                $transaction_amount = number_format($tr["total_fee"],2);
                            } else{
                                $transaction_amount = number_format($tr["total_fee"],2);
                            }

                            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
                                if($tr['trade_status']!="") { 
                                    $sta=$tr['trade_status']; 
                                } else { 
                                    $sta="ACK_NOT_RECEIVED"; 
                                }
                            }else if($tr['transaction_type'] == "cb2" ) {
                                $sta=$tr['refund_status'];
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
                            
                            $result .= '<tr class="gradeX">
                                            <td style="text-align:center;">'.$i.'</td>
                                            <td>'.$transaction_type.'</td>
                                            <td>'.$trans_out_trade_no.'</td>
                                            <td>'.$trans_partner_trans_id.'</td>
                                            <td>'.$tr["terminal_id"].'</td>							
                                            <td>'.$sta.'</td>
                                            <td>'.$tr["trans_datetime"].'</td>
                                            <td>'.$tr["currency"].' '.$transaction_amount.'</td>							
                                           <td align="center"><a href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td
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
                    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' WHERE  merchants.merchant_id= '$iid' ORDER BY transaction_alipay.trans_datetime DESC";

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
                            } else if($tr['transaction_type'] == 'cb1') {
                                $transaction_type = 'CBP - SALE';
                            }else if($tr['transaction_type'] == 'cb2') {
                                $transaction_type =  'CBP - REFUND';
                            } else if($tr['transaction_type'] == 'cb3') {
                                $transaction_type =  'CBP - QUERY';
                            }

                            $transaction_amount='';
                            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2'||$tr['transaction_type'] == 'cb2') {
                                $transaction_amount = number_format($tr["refund_amount"],2);
                            } else {
                                $transaction_amount = number_format($tr["total_fee"],2);    
                            }

                            if($tr['transaction_type'] == 1 || $tr['transaction_type'] == 's1'||$tr['transaction_type'] == 'cb1') { 
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
            // else if($period_start_date=='' && $period_end_date=='' && $merchantid=='0') {

            //      // echo "<br>Hi12_3";
            //     if($usertype[0]['user_type']==1){
            //         $query="SELECT * FROM transaction_alipay";
            //     } else {
            //         $query="SELECT * FROM transaction_alipay";
            //         //print_r($query);
            //     }
            //     // $query="SELECT * FROM transactions WHERE
            //     // server_datetime_trans >= '$val1' AND server_datetime_trans <= '$val2'";

            //     $result = 'No Transactions Found';
            //     $transactions = $db->rawQuery($query);

            //     if(!empty($transactions)){


            //         $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
            //         $result .= '<thead>
            //                 <tr>
            //                     <th>Transaction ID</th>
            //                     <th>Out Trade Number</th>						
            //                     <th>Terminal ID</th>
            //                     <th>Status</th>
            //                     <th>Date</th>				
            //                     <th>Amount</th>				
            //                     <th></th>
            //                 </tr>
            //             </thead>
            //             <tbody>';
            //         foreach($transactions as $tr) {
            //             //$tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
            //             //if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
            //             $result .= '<tr class="gradeX">
            //                                 <td>'.$tr["id_transaction_id"].'</td>
            //                                 <td>'.$tr["out_trade_no"].'</td>
            //                                 <td>'.$tr["terminal_id"].'</td>							
            //                                 <td>'.$tr["trade_status"].'</td>
            //                                 <td>'.$tr["trans_datetime"].'</td>
            //                                 <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
            //                                 <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
            //                             </tr>';
            //         }
            //         $result .= '</tbody></table>';
            //     }
            //     echo $result;
            // }

            // else if($period_start_date=='' && $period_end_date=='' && $merchantid!=0){
            // // echo "<br>merchant selected";
            //         $merchantid=$_POST['merchantid'];
            //         $query="SELECT * FROM merchants where idmerchants='$merchantid'";
            //         $tran = $db->rawQuery($query);
            //         $transaction1=$tran[0]['idmerchants'];
            //         // print_r($transactions);
            //         $query1="SELECT * FROM transaction_alipay where RIGHT(merchant_id, 3)='$transaction1'";
            //         $result = 'No Transactions Found';
            //         $transactions = $db->rawQuery($query1);

            //             if(!empty($transactions)){
            //                 $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
            //                 $result .= '<thead>
            //                         <tr>
            //                             <th>Transaction ID</th>
            //                             <th>Out Trade Number</th>						
            //                             <th>Terminal ID</th>
            //                             <th>Status</th>
            //                             <th>Date</th>				
            //                             <th>Amount</th>				
            //                             <th></th>
            //                         </tr>
            //                     </thead>
            //                     <tbody>';
            //                     foreach($transactions as $tr)
            //                     {
            //                         $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
            //                         if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
            //                         $result .= '<tr class="gradeX">
            //                                         <td>'.$tr["id_transaction_id"].'</td>
            //                                         <td>'.$tr["out_trade_no"].'</td>
            //                                         <td>'.$tr["terminal_id"].'</td>							
            //                                         <td>'.$sta.'</td>
            //                                         <td>'.$tr["trans_datetime"].'</td>
            //                                         <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
            //                                         <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
            //                                     </tr>';
            //                     }
            //                 $result .= '</tbody></table>';
            // }

            // echo $result;

            // }
            // else if($period_start_date!='' && $period_end_date!='' && $min_amount_range=='' && $max_amount_range=='' && $merchantid=='0'){


            //         // print_r($transactions);
            //         $query1="SELECT * FROM transaction_alipay where trans_datetime>='$period_start_date' AND period_start_date<='$period_start_date'";
            //         $result = 'No Transactions Found';
            //         $transactions = $db->rawQuery($query1);

            //             if(!empty($transactions)){
            //                 $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
            //                 $result .= '<thead>
            //                         <tr>
            //                             <th>Transaction ID</th>
            //                             <th>Out Trade Number</th>						
            //                             <th>Terminal ID</th>
            //                             <th>Status</th>
            //                             <th>Date</th>				
            //                             <th>Amount</th>				
            //                             <th></th>
            //                         </tr>
            //                     </thead>
            //                     <tbody>';
            //                     foreach($transactions as $tr)
            //                     {
            //                         $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
            //                         if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
            //                         $result .= '<tr class="gradeX">
            //                                         <td>'.$tr["id_transaction_id"].'</td>
            //                                         <td>'.$tr["out_trade_no"].'</td>
            //                                         <td>'.$tr["terminal_id"].'</td>							
            //                                         <td>'.$sta.'</td>
            //                                         <td>'.$tr["trans_datetime"].'</td>
            //                                         <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
            //                                         <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
            //                                     </tr>';
            //                     }
            //                 $result .= '</tbody></table>';
            // }

            // echo $result;

            // }
            // else if($period_start_date!='' && $period_end_date!='' && $min_amount_range=='' && $max_amount_range=='' && $merchantid!='0'){
            // $merchantid=$_POST['merchantid'];
            // $query="SELECT * FROM merchants where idmerchants='$merchantid'";
            // $tran = $db->rawQuery($query);
            // $transaction1=$tran[0]['idmerchants'];

            // // print_r($transactions);

            // $query1="SELECT * FROM transaction_alipay where RIGHT(merchant_id, 3)='$transaction1' AND trans_datetime='$period_start_date' AND trans_datetime='$period_end_date'";

            // $result = 'No Transactions Found';
            // $transactions = $db->rawQuery($query1);

            // if(!empty($transactions)){
            //     $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
            //     $result .= '<thead>
            //             <tr>
            //                 <th>Transaction ID</th>
            //                 <th>Out Trade Number</th>						
            //                 <th>Trade Number</th>
            //                 <th>Status</th>
            //                 <th>Date</th>				
            //                 <th>Amount</th>				
            //                 <th></th>
            //             </tr>
            //         </thead>
            //         <tbody>';
            //         foreach($transactions as $tr)
            //         {
            //             $tstatus = ($tr["status"] == 0 ? 'failed' : 'completed');
            //             if($tr['trade_status']!="") $sta=$tr['trade_status']; else $sta="Failed";
            //             $result .= '<tr class="gradeX">
            //                             <td>'.$tr["id_transaction_id"].'</td>
            //                             <td>'.$tr["out_trade_no"].'</td>
            //                             <td>'.$tr["trade_no"].'</td>							
            //                             <td>'.$sta.'</td>
            //                             <td>'.$tr["trans_datetime"].'</td>
            //                             <td>'.$tr["currency"].' '.number_format($tr["total_fee"],2).'</td>							
            //                             <td><a target="_blank" href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'"><i class="fa fa-folder-open fa-2x"></i></a></td>
            //                         </tr>';
            //         }
            //     $result .= '</tbody></table>';
            // }

            // echo $result;

            // }
            else if(isset($_POST['searchtype']) && $_POST['searchtype'] == 'report'){
                // print_r($_POST);
                $start_end = explode('-', $_POST['date2']);

                $t=$_POST['t'];

                $start_date = (isset($_POST['date2']) && $start_end[0]!='') ? date('Y-m-d 00:00:00',strtotime($start_end[0])) : '';
                $end_date   = (isset($_POST['date2']) && $start_end[1]!='') ? date('Y-m-d 23:59:59',strtotime($start_end[1])) : '';

                // // echo $start_date."=>".$end_date; exit;

                // // $start_date = (isset($_POST['date_timepicker_start']) && $_POST['date_timepicker_start']!='') ? $_POST['date_timepicker_start'] : '';
                // // $end_date   = (isset($_POST['date_timepicker_end']) && $_POST['date_timepicker_end']!='') ? $_POST['date_timepicker_end'] : '';
                $currencies = (isset($_POST['currencies']) && $_POST['currencies']!='') ? $_POST['currencies'] : '';
                $trans_type = (isset($_POST['transaction_type']) && $_POST['transaction_type']!='') ? $_POST['transaction_type'] : '';
                // print_r($start_date);
                // print_r($end_date);
               // $merchants  = (isset($_POST['merchants']) && $_POST['merchants']!='') ? $_POST['merchants'] : '';

                $query = "SELECT * FROM transaction_alipay WHERE trans_datetime >= '$start_date' AND trans_datetime <= '$end_date' AND merchant_id='$iid'  AND transaction_type!='cb3'AND transaction_type!='3'AND transaction_type!='s3'";
                // print_r($start_end);
                // echo $query;
                // die();

                if($currencies!='') {
                    $query.= " AND currency='$currencies'";
                }

                if($trans_type!='') {
                    $query.= " AND transaction_type='$trans_type'";
                }
                //print_r($trans_type);

                $query.= " ORDER BY trans_datetime ASC";
                $result = 'No Transactions Found';
                $transactions = $db->rawQuery($query);
                // print_r($transactions);
                // die();

                
                if(!empty($transactions)){
                    $result = '<table id="example" class="table table-striped table-bordered w-100">';
                    $result .= '<thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Transaction<br>Type</th>
                                        <th>Out Trade Number</th>                   
                                        <th>Terminal ID</th>
                                        <th>Status</th>
                                        <th>Transaction<br>Date</th>                
                                        <th>Amount</th>             
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    $i = 0;
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
                            $transaction_type = 'CBP - REFUND';
                        } else if($tr['transaction_type'] == 'cb3') {
                            $transaction_type = 'CBP - QUERY';
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
                        
                        $result .= '<tr>
                                        <td style="text-align:center;">'.$i.'</td>
                                        <td>'.$transaction_type.'</td>
                                        <td>'.$tr["out_trade_no"].'</td>
                                        <td>'.$tr["terminal_id"].'</td>                         
                                        <td>'.$sta.'</td>
                                        <td>'.$tr["trans_datetime"].'</td>
                                        <td>'.$tr["currency"].' '.$transaction_amount.'</td>                            
                                        <td><a class="btn btn-sm btn-info"  href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'&t='.$t.'"><i class="fa fa-info-circle"></i> Details</a></td>
                                    </tr>';

                    }
                               // echo "<pre>";
                    
                    $result .= '</tbody></table>';
                    // print_r($result);
                    // die();
                }
                echo $result;
            }
    //         else if($period_start_date!='' && $period_end_date!='' && $min_amount_range!='' && $max_amount_range!='' && $merchantid!='0'){

    // echo "test part";

    //         }
            else {
                // print_r($_POST);
                // echo "Test part";
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
if(isset($_POST['Trans']))
{

    $date_timepicker_start = $_POST['start_date']; // $date_timepicker_start;
    $date_timepicker_end = $_POST['end_date'];

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
    // exit;
    // die();

    if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') {

    $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status IN('TRADE_SUCCESS','TRADE_FINISHED') AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid'";
    $data1 = $db->rawQuery($que1);
    //print_r($data1);

    $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime >='$sdate' AND transaction_alipay.trans_datetime <='$edate' WHERE transaction_alipay.merchant_id='$iid'";
    $data2 = $db->rawQuery($que2);

    // echo "<pre>";
    // print_r($data2); exit;
    $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' ";
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
    <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
        <thead>
            <tr>
                <th>Transactions Type</th>        
                <th>Number of Transaction</th>
                <th>Total Amount</th> 
            </tr>        
        </thead>        
        <tbody>       
            <tr class="gradeA odd" role="row">
                <td class="sorting_1">Total Sale Transactions</td>              
                <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                <td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>               
            </tr>
            <tr class="gradeA even" role="row">
                <td class="sorting_1">Total Refund Transactions</td>                
                <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                <td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>                
            </tr>
            <tr class="gradeA odd" role="row">
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
    } else if($date_timepicker_start!='' && $date_timepicker_end!=''&& $currencies=='0' && $transaction_type!='0') 
    {
        if($transaction_type == 'sale') {
            $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' ";
            $data1 = $db->rawQuery($que1);
            if($data1) {
                foreach($data1 as $var1){
                    $total_count = $var1['countt'];
                    $total_amount= $var1['total'];
                }
            }
            ?>
            <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
                <thead>
                    <tr>
                    <th>Transactions Type</th>        
                    <th>Number of Transaction</th>
                    <th>Total Amount</th>
                    </tr>         
                </thead>        
            <tbody>       
                <tr class="gradeA odd" role="row">
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
            $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid'";
            $data2 = $db->rawQuery($que2);
            if($data2) {
                foreach($data2 as $var2){
                    $refund_count = $var2['countt'];
                    $refund_amount= $var2['total'];
                }
            }
            ?>
            <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
                <thead>
                    <tr>
                        <th>Transactions Type</th>        
                        <th>Number of Transaction</th>
                        <th>Total Amount</th>   
                    </tr>      
                </thead>        
                <tbody>  
                    <tr class="gradeA even" role="row">
                        <td class="sorting_1">Total Refund Transactions</td>                
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
            $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid'";
            $data3 = $db->rawQuery($que3);
            if($data3) {
                foreach($data3 as $var3){
                    $cancel_count = $var3['countt'];
                    $cancel_amount= $var3['total'];
                }
            }
            ?>
            <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
                <thead>
                    <tr>
                        <th>Transactions Type</th>        
                        <th>Number of Transaction</th>
                        <th>Total Amount</th>
                    </tr>         
                </thead>        
            <tbody> 
                <tr class="gradeA odd" role="row">
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>       
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
                        <td><?php echo $total_count; ?></td>
                        <td><?php echo $total_amount; ?></td>           
                  </tr>
                <tr class="gradeA even" role="row">
                    <td class="sorting_1">Total Refund Transactions</td>                
                        <td><?php echo $refund_count; ?></td>
                        <td><?php echo $refund_amount; ?></td>              
                  </tr>
                <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
                <tbody>
                <tr class="gradeA even" role="row">
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
        <?php  }  else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='sale' ) { 
                $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction";
                        $data3 = $db->rawQuery($que3); 
                            foreach($data3 as $var3){
                                 $total_count = $var3['countt'];
                                 $total_amount= $var3['total'];
                                        
                            }       
        ?>      
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>
                      <tbody>             
                <tr class="gradeA even" role="row">
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

        <?php  } else if($date_timepicker_start=='' && $date_timepicker_end==''  && $currencies=='0' && $transaction_type=='cancel') {  

                $que1="select count(cancel_flag) as countc,sum(total_fee) as total from transaction where cancel_flag='1' group by cancel_flag";   
                        $data1 = $db->rawQuery($que1);
                        foreach($data1 as $var1){
                             $number_count = $var1['countc'];
                             $cancel_amount= $var1['total'];
                                    
        } ?>
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
             <tbody>       
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
                        <td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
                        <td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>             
                  </tr>
                <tr class="gradeA even" role="row">
                    <td class="sorting_1">Total Refund Transactions</td>                
                        <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                        <td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>             
                  </tr>
                <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody> 
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                   <tr class="gradeA even" role="row">
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
        <?php } else if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='sale') {
               $que3="select count(id_transaction_id) as countt,sum(total_fee) as total from transaction where trans_datetime>='$sdate' AND trans_datetime<='$edate'";
          
                    $data3 = $db->rawQuery($que3); 
                    foreach($data3 as $var3){
                         $total_count = $var3['countt'];
                         $total_amount= $var3['total'];                     
                    }
          ?>        
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>       
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
             <tbody>       
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
                        <td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
                        <td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>             
                  </tr>
                <tr class="gradeA even" role="row">
                    <td class="sorting_1">Total Refund Transactions</td>                
                        <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                        <td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>             
                  </tr>
                <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>       
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                   <tr class="gradeA even" role="row">
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody> 
                  <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
                <tbody>
                <tr class="gradeA even" role="row">
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>
                      <tbody>             
                <tr class="gradeA even" role="row">
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
        <table class="table table-striped table-bordered table-hover" role="grid" aria-describedby="editable_info">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                <tr class="gradeA odd" role="row">
                    <td class="sorting_1">Total Cancel Transactions </td>               
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