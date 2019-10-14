<?php

/**** Get the Current Day Transaction List ****/
$D_query="SELECT YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED') AND DATE(transaction_alipay.trans_datetime) = '$currentdate' AND transaction_alipay.terminal_id='$terminal_id' GROUP BY year, month";
$transactions_Currday = $db->rawQuery($D_query);

// Current Day Transaction Amount and Count
$CurrdayTransamount = $transactions_Currday[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_Currday[0]['transamount']) : '--';
$CurrdayTranscount  = $transactions_Currday[0]['transcount']!='' ? $transactions_Currday[0]['transcount'] : '--';

/**** Get the Current Month Transaction List ****/
$M_query="SELECT YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED') AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') AND transaction_alipay.terminal_id='$terminal_id' GROUP BY year, month";
$transactions_Currmonth = $db->rawQuery($M_query);

// Current Month Transaction/Sales Amount and Count
$CurrMonthTransamount = $transactions_Currmonth[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_Currmonth[0]['transamount']) : '--';
$CurrMonthTranscount  = $transactions_Currmonth[0]['transcount']!='' ? $transactions_Currmonth[0]['transcount'] : '--';

/**** Get the Current Month Transaction Cancel List ****/
$MC_query="SELECT YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') AND transaction_alipay.terminal_id='$terminal_id' GROUP BY year, month";
$transactions_cancel_Currmonth = $db->rawQuery($MC_query);

// Current Month Transaction Cancel Amount and Count
$CurrMonthTrans_cancel_amount = $transactions_cancel_Currmonth[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_cancel_Currmonth[0]['transamount']) : '--';
$CurrMonthTrans_cancel_count  = $transactions_cancel_Currmonth[0]['transcount']!='' ? $transactions_cancel_Currmonth[0]['transcount'] : '--';

/**** Get the Current Month Transaction Refund List ****/
$MC_query="SELECT YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount, SUM(transaction_alipay.refund_amount) AS refundamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') AND transaction_alipay.terminal_id='$terminal_id' GROUP BY year, month";
$transactions_refund_Currmonth = $db->rawQuery($MC_query);

// Current Month Transaction Refund Amount
$CurrMonthTrans_refund_amount = $transactions_refund_Currmonth[0]['refundamount']!='' ? $ccode.' '.money_format('%!i', $transactions_refund_Currmonth[0]['refundamount']) : '--';

/**** Get the Current Year Transaction List ****/
$Y_query="SELECT YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED') AND YEAR(transaction_alipay.trans_datetime) = YEAR('$currentdate') AND transaction_alipay.terminal_id='$terminal_id' GROUP BY year, month";
$transactions_Curryear = $db->rawQuery($Y_query);

// echo "<pre>";
// print_r($transactions_Curryear); exit;

$totTransamount = 0;
$transCntval = 0;
$transCntarr = [];
$transAmtarr = []; 
$i = 1;
foreach ($transactions_Curryear as $key) {
	while($i <= $key['month']) {
		if($i == $key['month']) {
			$transCntarr[] = $key['transcount'];

			$transAmtarr[] = $key['transamount'];
			$totTransamount+= $key['transamount'];
		} else {
			$transCntarr[] = 0;

			$transAmtarr[] = 0;
			$totTransamount+= 0;
		}
		$i++; 
	}
}
$transCnts = implode(',', $transCntarr);

$transCntval = array_sum($transCntarr);

$transAmts = implode(',', $transAmtarr);

//This week code
date_default_timezone_set('Asia/kolkata');
$first_day_of_the_week = 'Sunday';
$start_of_the_week     = strtotime("Last $first_day_of_the_week");
if ( strtolower(date('l')) === strtolower($first_day_of_the_week) ) {
    $start_of_the_week = strtotime('today');
}
$end_of_the_week = $start_of_the_week + (60 * 60 * 24 * 7) - 1;
$date_format =  'Y-m-d H:i:s';
$week_start=date($date_format, $start_of_the_week);
$week_end=date($date_format, $end_of_the_week);

/**** Get the Current Week Transaction List ****/
$W_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED') AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$week_end."' AND transaction_alipay.trans_datetime>='".$week_start."'";
$transactions_Currweek = $db->rawQuery($W_query);

// Current Week Transaction/Sales Amount and Count
$CurrWeekTransamount = $transactions_Currweek[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_Currweek[0]['transamount']) : '--';
$CurrWeekTranscount  = $transactions_Currweek[0]['transcount']!='' ? $transactions_Currweek[0]['transcount'] : '--';

/**** Get the Current Week Transaction Cancel List ****/
$WC_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$week_end."' AND transaction_alipay.trans_datetime>='".$week_start."'";
$transactions_cancel_Currweek = $db->rawQuery($WC_query);

// Current Week Transaction Cancel Amount and Count
$CurrWeekTrans_cancel_amount = $transactions_cancel_Currweek[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_cancel_Currweek[0]['transamount']) : '--';
$CurrWeekTrans_cancel_count  = $transactions_cancel_Currweek[0]['transcount']!='' ? $transactions_cancel_Currweek[0]['transcount'] : '--';

/**** Get the Current Week Transaction Refund List ****/
$WR_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount, SUM(transaction_alipay.refund_amount) AS refundamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$week_end."' AND transaction_alipay.trans_datetime>='".$week_start."'";
$transactions_refund_Currweek = $db->rawQuery($WR_query);

// Current Week Transaction Refund Amount
$CurrWeekTrans_refund_amount = $transactions_refund_Currweek[0]['refundamount']!='' ? $ccode.' '.money_format('%!i', $transactions_refund_Currweek[0]['refundamount']) : '--';

$Today_start=date("Y-m-d 00:00:00");
$Today_end=date("Y-m-d 23:59:59");
/**** Get the Current Week Transaction List ****/
$T_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1','cb1') AND transaction_alipay.result_code='SUCCESS' AND (transaction_alipay.trade_status='TRADE_SUCCESS' OR transaction_alipay.trade_status='TRADE_FINISHED') AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$Today_end."' AND transaction_alipay.trans_datetime>='".$Today_start."'";
$transactions_CurrToday = $db->rawQuery($T_query);

// Current Week Transaction/Sales Amount and Count
$CurrTodayTransamount = $transactions_CurrToday[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_CurrToday[0]['transamount']) : '--';
$CurrTodayTranscount  = $transactions_CurrToday[0]['transcount']!='' ? $transactions_CurrToday[0]['transcount'] : '--';

/**** Get the Current Week Transaction Cancel List ****/
$TC_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('4','s4') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$Today_end."' AND transaction_alipay.trans_datetime>='".$Today_start."'";
$transactions_cancel_CurrToday = $db->rawQuery($TC_query);

// Current Week Transaction Cancel Amount and Count
$CurrTodayTrans_cancel_amount = $transactions_cancel_CurrToday[0]['transamount']!='' ? $ccode.' '.money_format('%!i', $transactions_cancel_CurrToday[0]['transamount']) : '--';
$CurrTodayTrans_cancel_count  = $transactions_cancel_CurrToday[0]['transcount']!='' ? $transactions_cancel_CurrToday[0]['transcount'] : '--';

/**** Get the Current Week Transaction Refund List ****/
$TR_query="SELECT COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount, SUM(transaction_alipay.refund_amount) AS refundamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('2','s2','cb2') AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.terminal_id='$terminal_id' AND transaction_alipay.trans_datetime<='".$Today_end."' AND transaction_alipay.trans_datetime>='".$Today_start."'";
$transactions_refund_CurrToday = $db->rawQuery($TR_query);

// echo "<pre>";
// print_r($transactions_refund_CurrToday); exit;

// Current Week Transaction Refund Amount
$CurrTodayTrans_refund_amount = $transactions_refund_CurrToday[0]['refundamount']!='' ? $ccode.' '.money_format('%!i', $transactions_refund_CurrToday[0]['refundamount']) : '--';

?>