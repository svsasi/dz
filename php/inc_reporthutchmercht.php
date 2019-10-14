<?php
include('../init.php');

// $iid = $_SESSION['iid'];

if(isset($_POST['merchantid'])){
    $iid=$_POST['merchantid'];
}

if(isset($_POST['S_Date']) || $_POST['S_Date']!='') { // Select the date from dashboard
	$sdate=$_POST['S_Date']. '00:00:00';
	$edate=$_POST['S_Date']. '23:59:59';
} else if(isset($_POST['period_start_date1']) || $_POST['period_end_date1']!='') { // Select the date from dashboard
	$sdate=$_POST['period_start_date1']. ':00';
	$edate=$_POST['period_end_date1']. ':59';

} else {
	$sdate=$_POST['period_start_date']. '00:00:00';
	$edate=$_POST['period_end_date']. '23:59:59';
}

$val1= date('Y-m-d H:i:s', strtotime($sdate));
$val2= date('Y-m-d H:i:s', strtotime($edate));

$iiiiiiiiid=$_POST['merchantid'];

// echo "<pre>";
// print_r($_POST);
// echo "<br>";
// echo $val1.'=>'.$val2;
// exit;

// echo "<pre>";
// print_r($_GET); exit;

/**** Get the Transaction List for Selected Date in Admin  Dashboard Merchant page ****/
if(isset($_POST['S_Date']) || $_POST['S_Date']!='' || isset($_GET['date']) || $_GET['date']!='') {

    $terminalid = $_POST['terminalid'];

    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid' ORDER BY transaction_alipay.trans_datetime DESC";

    $result = 'No Transactions Found';
    $transactions = $db->rawQuery($query);
    
    if(!isset($_GET['date'])) { // S.Nooo
        if(!empty($transactions)){
            $result = '<table class="table table-striped table-bordered table-hover dataTables-example">';
            $result .= '<thead>
                            <tr>
                                <th>S.No</th>
                                <th>Transaction Type</th>
                                <th>Out Trade Number</th>                       
                                <th>Trade Number</th>
                                <th>Buyer Phone</th>
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
                                <td>'.$tr["trade_no"].'</td>   
                                <td>'.$tr["res_field_1"].'</td>                         
                                <td>'.$sta.'</td>
                                <td>'.$tr["trans_datetime"].'</td>
                                <td>'.$tr["currency"].' '.$transaction_amount.'</td>                            
                                <td align="center"><a href="transactiondetails.php?t_id='.$tr["id_transaction_id"].'" title="Click To View Details"><i class="glyphicon glyphicon-plus-sign" style="font-size: 20px;"></i></a></td>
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

}

if($_POST['period_start_date1']!='' && $_POST['period_end_date1']!='') {

    $merchantid = $_POST['merchantid'];
    $terminalid = $_POST['terminalid'];

    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' WHERE transaction_alipay.merchant_id='$merchantid' AND transaction_alipay.terminal_id='$terminalid' ORDER BY transaction_alipay.trans_datetime DESC";

    // $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1' AND transaction_alipay.trans_datetime <= '$val2' ORDER BY transaction_alipay.trans_datetime DESC";

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
                                <th>S.No</th>
                                <th>Transaction<br>Type</th>
                                <th>Out Trade Number /<br>Terminal ID</th>
                                <th>Refund Org ID</th>                  
                                <th>Buyer Phone</th>
                                <th>Status</th>
                                <th>Transaction<br>Date</th>            
                                <th>Amount</th>
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
                                <td>'.$trans_out_trade_no.' /<br>'.$tr["terminal_id"].'</td>
                                <td>'.$trans_partner_trans_id.'</td>
                                <td>'.$tr["customer_phone"].'</td>                         
                                <td>'.$sta.'</td>
                                <td>'.$tr["trans_datetime"].'</td>
                                <td>'.$tr["currency"].' '.$transaction_amount.'</td>
                            </tr>';

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

        $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' WHERE transaction_alipay.merchant_id='$merchantid' AND transaction_alipay.terminal_id='$terminalid' ORDER BY transaction_alipay.trans_datetime DESC";

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

if( isset($_GET['start_d']) && isset($_GET['end_d']) ) {

    $merchantid = $_GET['merchantid'];
    $terminalid = $_GET['terminalid'];

    if($_GET['start_d']!='' && $_GET['end_d']!=''){
        $selectedDate1 = $_GET['start_d'];
        $selectedDate2 = $_GET['end_d'];
        $sdate_excel=$selectedDate1. ':00';
        $edate_excel=$selectedDate2. ':59';
        $val1_excel= date('Y-m-d H:i:s', strtotime($sdate_excel));
        $val2_excel= date('Y-m-d H:i:s', strtotime($edate_excel));
        $selectedDate = date('Y-m-d', strtotime($sdate_excel));
    } else {
        $selectedDate = $_GET['date'];
        $sdate_excel = $selectedDate . '00:00:00';
        $edate_excel = $selectedDate . '23:59:59';
        $val1_excel = date('Y-m-d H:i:s', strtotime($sdate_excel));
        $val2_excel = date('Y-m-d H:i:s', strtotime($edate_excel));
    }

    $query="SELECT * FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type NOT IN ('3','s3') AND transaction_alipay.trans_datetime >= '$val1_excel' AND transaction_alipay.trans_datetime <= '$val2_excel' WHERE transaction_alipay.merchant_id='$merchantid' AND transaction_alipay.terminal_id='$terminalid' ORDER BY transaction_alipay.trans_datetime DESC";

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
            <td width="15%"><div id="wt_txt" align="center"><b>Buyer Phone</b></div></td>
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
                <td align="center" id="lt_edit"><?php if($tr["customer_phone"]!="") { echo $tr["customer_phone"]; } else { echo "--"; } ?></td>
                <td align="center" id="lt_edit"><?php echo $sta; ?></td>
                <td align="center" id="lt_edit"><?php echo $tr["trans_datetime"]; ?></td>
                <td align="center" id="lt_edit"><?php echo $tr["currency"].' '.$transaction_amount; ?></td>
            </tr>
        <?php } ?>
    </TABLE>
    <?php
}


if(isset($_POST['Trans'])) {

    $date_timepicker_start = $_POST['start_date'];
    $date_timepicker_end   = $_POST['end_date'];

    if(isset($_POST['from_dash']) && $_POST['from_dash'] == 1) { // From Dashboard
        $sdate = date('Y-m-d H:i:s', strtotime($_POST['start_date']. ':00')); // $date_timepicker_start;
        $edate = date('Y-m-d H:i:s', strtotime($_POST['end_date']. ':59')); // $date_timepicker_end;
    } else {
        $sdate = $_POST['start_date']; // $date_timepicker_start;
        $edate = $_POST['end_date']; // $date_timepicker_end;
    }

    $currencies = $_POST['currencies'];
    $transaction_type = $_POST['transaction_type'];

    $terminalid = $_POST['terminalid'];

    // echo "<pre>";
    // print_r($_POST);
    // exit;
    // die();

    if($date_timepicker_start!='' && $date_timepicker_end!=''  && $currencies=='0' && $transaction_type=='0') {

    $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status IN('TRADE_SUCCESS','TRADE_FINISHED') AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
    $data1 = $db->rawQuery($que1);
    //print_r($data1);

    $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime >='$sdate' AND transaction_alipay.trans_datetime <='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
    $data2 = $db->rawQuery($que2);

    // echo "<pre>";
    // print_r($data2); exit;
    $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
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
            <tr scope="row">
                <td >Total Sale Transactions</td>              
                <td><?php if($total_count=='0'){ echo '0'; } else { echo $total_count; }?></td>
                <td><?php if($total_amount==''){ echo '0'; } else { echo money_format('%!i', $total_amount); }?></td>               
            </tr>
            <tr scope="row">
                <td >Total Refund Transactions</td>                
                <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                <td><?php if($refund_amount==''){ echo '0'; } else { echo "-".money_format('%!i', $refund_amount); } ?></td>                
            </tr>
            <tr  scope="row">
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
    } else if($date_timepicker_start!='' && $date_timepicker_end!=''&& $currencies=='0' && $transaction_type!='0') 
    {
        if($transaction_type == 'sale') {
            $que1 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
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
            $que2 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.refund_amount) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
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
                        <td >Total Refund Transactions</td>                
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
            $que3 ="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS countt, SUM(transaction_alipay.total_fee) AS total FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id  AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trans_datetime>='$sdate' AND transaction_alipay.trans_datetime<='$edate' WHERE transaction_alipay.merchant_id='$iid' AND transaction_alipay.terminal_id='$terminalid'";
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
                  <tr  scope="row">
                    <td >Total Sale Transactions</td>              
                        <td><?php echo $total_count; ?></td>
                        <td><?php echo $total_amount; ?></td>           
                  </tr>
                <tr scope="row">
                    <td >Total Refund Transactions</td>                
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>
                      <tbody>             
                <tr scope="row">
                    <td >Total Refund Transactions</td>                
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
                    <td >Total Cancel Transactions </td>               
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                   <tr scope="row">
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>       
                  <tr scope="row">
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
             <tbody>       
                  <tr scope="row">
                    <td class="sorting_1">Total Sale Transactions</td>              
                        <td><?php if($total_count=='0'){ echo '0'; } else{ echo $total_count; }?></td>
                        <td><?php if($total_amount==''){ echo '0'; } else{ echo $total_amount; }?></td>             
                  </tr>
                <tr scope="row">
                    <td class="sorting_1">Total Refund Transactions</td>                
                        <td><?php if($refund_count==''){ echo '0'; } else { echo $refund_count; } ?></td>
                        <td><?php if($refund_amount==''){ echo '0'; } else { echo $refund_amount; } ?></td>             
                  </tr>
                <tr scope="row">
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>       
                  <tr scope="row">
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
        <table class="table card-table table-vcenter text-nowrap">
              <thead>
                <tr><th>Transactions Type</th>        
                  <th>Number of Transaction</th>
                  <th>Total Amount</th>         
                </thead>        
              <tbody>  
                   <tr scope="row">
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