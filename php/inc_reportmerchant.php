
<?php

include('./init.php');

if(file_get_contents("php://input")) {
	$json = json_decode(file_get_contents("php://input"));
    if($json->type == 'merchant_id') {
           $currentdate = date('Y-m-d');
           $lastmonthdate= date('Y-m-d', strtotime(date('Y-m')." -1 month")); // date('Y-m-d', strtotime("-30 days"));
           
            $merchant_id= $json->m_id;
        
        /** merchant Wise **/   
         function convert_number_merchant( $num, $precision = 1 ) {
               $last=substr(strtoupper(preg_replace("/[^a-zA-Z]/", '', $num)),0,1);
              $remaining=preg_replace("/[^0-9\.]/", '', $num);
              $remaining = (float)$remaining;
              if($last == 'K') {
                    $amount = number_format(($remaining*1000), $precision);
             } else if($last == 'M') {
                 $amount = number_format(($remaining*1000000), $precision);
             } else if($last == 'B') {
                  $amount = number_format(($remaining*1000000000), $precision);
               } else if($last == 'T')  {
                 $amount = number_format(($remaining*1000000000000), $precision);
             } else {
                   $amount = number_format($remaining, $precision);
             }
              return $amount;
             }

        function number_format_short( $n, $precision = 1 ) {
                if ($n < 900) {
                    // 0 - 900
                    $n_format = number_format($n, $precision);
                    $suffix = '';
                } else if ($n < 900000) {
                    // 0.9k-850k
                    $n_format = number_format($n / 1000, $precision);
                    $suffix = 'K';
                } else if ($n < 900000000) {
                    // 0.9m-850m
                    $n_format = number_format($n / 1000000, $precision);
                    $suffix = 'M';
                } else if ($n < 900000000000) {
                    // 0.9b-850b
                    $n_format = number_format($n / 1000000000, $precision);
                    $suffix = 'B';
                } else {
                    // 0.9t+
                    $n_format = number_format($n / 1000000000000, $precision);
                    $suffix = 'T';
                }
                // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
                // Intentionally does not affect partials, eg "1.50" -> "1.50"
                if ( $precision > 0 ) {
                    $dotzero = '.' . str_repeat( '0', $precision );
                    $n_format = str_replace( $dotzero, '', $n_format );
                }
                return $n_format . $suffix;
            }


            /**** Get the Merchant Wise  Month-wise "Monthly Recurring Revenue" ****/
       function get_merchant_sale( $currentdate,$merchant_id) {
                 global $db;
                 $M_Raw_query="SELECT merchants.currency_code,YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND transaction_alipay.transaction_type IN ('1','s1') AND transaction_alipay.merchant_id ='$merchant_id' AND transaction_alipay.result_code='SUCCESS' AND transaction_alipay.trade_status='TRADE_SUCCESS' AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') GROUP BY year, month, merchants.currency_code";
                 $M_transactions_Details = $db->rawQuery($M_Raw_query);
                 return $M_transactions_Details;    
            }
            
            //echo $lastmonthdate."&&&&&".$merchant_id;
            /**** Get the Merchant Wise Month-wise "Cancel Amount by Currency" ****/
             function get_merchant_cancel_by_ccode($ccode,$currentdate,$merchant_id) {
              global $db;
              $M_R_query="SELECT merchants.currency_code, YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.total_fee) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.currency_code= '$ccode' AND transaction_alipay.transaction_type IN ('4','s4')
                 AND transaction_alipay.merchant_id ='$merchant_id' AND  transaction_alipay.result_code='SUCCESS' AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') GROUP BY year, month";
              $transactionsDetails = $db->rawQuery($M_R_query);
              return $transactionsDetails;
            }

            /**** Get the Merchant Wise Month-wise "Refund Amount by Currency" ****/
            function get_merchant_refund_by_ccode($ccode,$currentdate,$merchant_id) {
                global $db;
               $M_R_query="SELECT merchants.currency_code, YEAR(transaction_alipay.trans_datetime) AS year, MONTH(transaction_alipay.trans_datetime) AS month, COUNT(DISTINCT transaction_alipay.id_transaction_id) AS transcount, SUM(transaction_alipay.refund_amount) AS transamount FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.currency_code= '$ccode' AND transaction_alipay.transaction_type IN ('2','s2') AND transaction_alipay.merchant_id = '$merchant_id' AND transaction_alipay.result_code='SUCCESS' AND MONTH(transaction_alipay.trans_datetime) = MONTH('$currentdate') GROUP BY year, month";
                 $transactionsDetails = $db->rawQuery($M_R_query);
                 return $transactionsDetails;
            }

           // print_r(get_merchant_sale_groupby_ccode($currentdate,$merchant_id));

            $transactions_Merchant_M_R_amt = '';
            $transactions_Merchant_M_R_Net_amt = '';

            $transactions_Merchant_M_R_Sale_amount = '0';
            $transactions_Merchant_M_R_Refd_amount = '0';
            $transactions_Merchant_M_R_Canl_amount = '0';
            $transactions_Merchant_M_R_Net_amount  = '0';

            $transactions_Merchant_M_R_Sale_amt1 = '';
            $transactions_Merchant_M_R_Sale_amt_lastlist = '';

            $transactions_Merchant_M_R_Net_amt1 = '';
            $transactions_Merchant_M_R_Net_amt_lastlist = '';
            $transactions_Merchant_M_R = get_merchant_sale($currentdate,$merchant_id);
             // Get the Current Month Sale
           foreach ($transactions_Merchant_M_R as $key => $trans) {
                $ccode = $trans['currency_code'];
                if($ccode == 'USD') {
                    setlocale(LC_MONETARY, 'en_US');
                 } else if($ccode == 'LKR') {
                    setlocale(LC_MONETARY, 'en_US');
                // setlocale(LC_MONETARY, 'en_IN');
                }

                $ccode_part = '<small>'.$ccode.'</small>';
                $transactions_Merchant_M_R_amt .= '<h1 class="no-margins">'.($trans['transamount']!='' ?number_format_short($trans['transamount'],2).' '.$ccode_part : '--').'</h1>';
                $transactions_Merchant_M_R_amt1 .= ($trans['transamount']!='' ?number_format_short($trans['transamount'],2).' '.$ccode_part : '--');
                $transactions_Merchant_M_R_Sale_amount = $trans['transamount'];

                $transactions_Merchant_M_R_Canl_Detail = '';

                $transactions_Merchant_M_R_Canl_Detail = get_merchant_cancel_by_ccode($ccode,$currentdate,$merchant_id);
                 // Get the Current Month Cancel
                $transactions_Merchant_M_R_Canl_amount = $transactions_Merchant_M_R_Canl_Detail[0]['transamount'];

                $transactions_Merchant_M_R_Refd_Detail = '';
                $transactions_Merchant_M_R_Refd_Detail = get_merchant_refund_by_ccode($ccode,$currentdate,$merchant_id); 
                // Get the Current Month Refund
                $transactions_Merchant_M_R_Refd_amount = $transactions_Merchant_M_R_Refd_Detail[0]['transamount'];
                
                $transactions_Merchant_M_R_Net_amount = $transactions_Merchant_M_R_Sale_amount - ($transactions_Merchant_M_R_Canl_amount+$transactions_Merchant_M_R_Refd_amount);
                $transactions_Merchant_M_R_Net_amt .= '<h1 class="no-margins">'.($transactions_Merchant_M_R_Net_amount!='' ?number_format_short($transactions_Merchant_M_R_Net_amount,2).' '.$ccode_part : '--').'</h1>';
                $transactions_Merchant_M_R_Net_amt1 .=($transactions_Merchant_M_R_Net_amount!='' ?number_format_short($transactions_Merchant_M_R_Net_amount,2).' '.$ccode_part : '--');

                    $transactions_Merchant_M_R_Sale_amt1 .=($transactions_Merchant_M_R_Sale_amount!='' ?number_format_short($transactions_Merchant_M_R_Sale_amount,2).' '.$ccode_part : '--');
                }  
                $transactions_Merchant_M_R_last = get_merchant_sale($lastmonthdate,$merchant_id); // Get the Previous Month Sale
                foreach ($transactions_Merchant_M_R_last as $key => $trans_last) {  
                    $ccode_last = $trans_last['currency_code'];
                    if($ccode_last == 'USD') {
                        setlocale(LC_MONETARY, 'en_US');
                    } else if($ccode_last == 'LKR') {
                        setlocale(LC_MONETARY, 'en_US');
                        // setlocale(LC_MONETARY, 'en_IN');
                    }
                    $ccode_part_last = '<small>'.$ccode_last.'</small>';
                    $transactions_Merchant_M_R_Sale_amount_lastlist.= ($trans_last['transamount']!='' ?number_format_short($trans_last['transamount'],2).' '.$ccode_part_last : '--');
                    $transactions_Merchant_M_R_Sale_last_amount = $trans_last['transamount'];

                    $transactions_Merchant_M_R_Canl_last_Detail = '';
                    $transactions_Merchant_M_R_Canl_last_Detail = get_merchant_cancel_by_ccode($ccode_part_last,$lastmonthdate,$merchant_id); 
                    // Get the Previous Month Cancel
                    $transactions_Merchant_M_R_Canl_last_amount = $transactions_Merchant_M_R_Canl_last_Detail[0]['transamount'];

                    $transactions_Merchant_M_R_Refd_last_Detail = '';
                    $transactions_Merchant_M_R_Refd_last_Detail = get_merchant_refund_by_ccode($ccode_part_last,$lastmonthdate,$merchant_id); 
                    // Get the Previous Month Refund
                    $transactions_Merchant_M_R_Refd_last_amount = $transactions_Merchant_M_R_Refd_last_Detail[0]['transamount'];
                    
                    $transactions_Merchant_M_R_Net_last_amount = $transactions_Merchant_M_R_Sale_last_amount - ($transactions_Merchant_M_R_Canl_last_amount+$transactions_Merchant_M_R_Refd_last_amount);
                    $transactions_Merchant_M_R_Net_amt_lastlist .= ($transactions_Merchant_M_R_Net_last_amount!='' ?number_format_short($transactions_Merchant_M_R_Net_last_amount,2).' '.$ccode_part_last : '--');

                    $transactions_Merchant_M_R_Sale_amt_lastlist .=($transactions_Merchant_M_R_Sale_last_amount!='' ?number_format_short($transactions_Merchant_M_R_Sale_last_amount,2).' '.$ccode_part_last : '--');
                }

                function get_content( $tag , $content ) {
                    preg_match("/<".$tag."[^>]*>(.*?)<\/$tag>/si", $content, $matches);
                    return $matches[1];
                }
                $tag="small";
                $content=$transactions_Merchant_M_R_amt1;
                $currency=get_content($tag,$content);
                if($currency=="USD") {
                    $USD_merchant_current_sale_amt =preg_replace("/[^0-9\.]/", '', convert_number_merchant(strip_tags($transactions_Merchant_M_R_amt1),2));
                    $USD_merchant_current_Net_amt = preg_replace("/[^0-9\.]/", '', convert_number_merchant(strip_tags($transactions_Merchant_M_R_Net_amt1),2));

                    $USD_merchant_previous_sale_amt =preg_replace("/[^0-9\.]/", '', convert_number_merchant(strip_tags($transactions_Merchant_M_R_Sale_amount_lastlist),2));
                    $USD_merchant_previous_Net_amt = preg_replace("/[^0-9\.]/", '',convert_number_merchant(strip_tags($transactions_Merchant_M_R_Net_amt_lastlist),2));
                          
                      $sale_merchant_divide_per_USD_LKR = ($USD_merchant_current_sale_amt / $USD_merchant_previous_sale_amt)*100;
                                     

                    $Net_merchant_divide_per_USD_LKR= ($USD_merchant_current_Net_amt / $USD_merchant_previous_Net_amt)*100;

                    $sale_merchant_variation_arrow = ($sale_merchant_divide_per_USD_LKR > 100) ? 'fa-chevron-up' : 'fa-chevron-down';
                    $net_merchant_variation_arrow = ($Net_merchant_divide_per_USD_LKR > 100) ? 'fa-chevron-up' : 'fa-chevron-down';


                }
                if($currency=="LKR"){
                     $LKR_merchant_current_sale_amt = preg_replace("/[^0-9\.]/", '',convert_number_merchant(strip_tags($transactions_Merchant_M_R_amt1),2));
                     $LKR_merchant_current_Net_amt = preg_replace("/[^0-9\.]/", '',convert_number_merchant(strip_tags($transactions_Merchant_M_R_Net_amt1),2));

                     $LKR_merchant_previous_sale_amt = preg_replace("/[^0-9\.]/", '',convert_number_merchant(strip_tags($transactions_Merchant_M_R_Sale_amount_lastlist),2));
                     $LKR_merchant_previous_Net_amt = preg_replace("/[^0-9\.]/", '',convert_number_merchant(strip_tags($transactions_Merchant_M_R_Net_amt_lastlist),2));


                     $sale_merchant_divide_per_USD_LKR = ($LKR_merchant_current_sale_amt / $LKR_merchant_previous_sale_amt)*100;
                     $Net_merchant_divide_per_USD_LKR = ($LKR_merchant_current_Net_amt / $LKR_merchant_previous_Net_amt)*100;
                         //echo $sale_merchant_divide_per_USD."&&&&&".$Net_merchant_divide_per_USD;

                     $sale_merchant_variation_arrow = ($sale_merchant_divide_per_USD_LKR > 100) ? 'fa-chevron-up' : 'fa-chevron-down';
                     $net_merchant_variation_arrow = ($Net_merchant_divide_per_USD_LKR > 100) ? 'fa-chevron-up' : 'fa-chevron-down';

                }
        }

        $sale= '<div class="ibox float-e-margins">
                <div class="ibox-content admin">
                    '.$transactions_Merchant_M_R_amt.'
                    <strong class="pull-right">
                    <i class="fa'.$sale_merchant_variation_arrow.' aria-hidden="true"></i>
                     '.number_format($sale_merchant_divide_per_USD_LKR,2)." %".'<br>
                    <span>Previous 30 Days</span>
                    </strong>
                </div>
                <div class="titleDet">
                    <h2 align="center">Monthly Recurring Revenue</h2>
                </div>
            </div>';

        $net='<div class="ibox float-e-margins">
                <div class="ibox-content admin">
                    '.$transactions_Merchant_M_R_Net_amt.'
                    <strong class="pull-right">
                        <i class="fa'.$net_merchant_variation_arrow.' aria-hidden="true"></i>
                        '.number_format($Net_merchant_divide_per_USD_LKR,2)." %".'<br>
                        <span>Previous 30 Days </span>
                    </strong>
                </div>
                <div class="titleDet">
                    <h2 align="center">Monthly Net Revenue</h2>
                </div>
            </div>';

        $array = array(
            "sale"=>$sale,
            "net"=>$net

         );


        $result_merchant=json_encode($array);
         echo $result_merchant;

}

?>