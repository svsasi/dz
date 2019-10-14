<?php
include('../init.php');
//ini_set('precision', '15');

require_once('../phpexcel/Classes/PHPExcel.php');
require_once('../phpexcel/Classes/PHPExcel/IOFactory.php');

$iid = $_POST['userid'];


        // $query = "SELECT * FROM transaction_alipay WHERE trans_datetime >= '$sdate' AND trans_datetime <= '$edate' AND transaction_type!='cb3'AND transaction_type!='3'AND 

function number_point($value) {
    $myAngloSaxonianNumber = number_format($value, 2, '.', ','); // -> 5,678.90 
    return $myAngloSaxonianNumber;
}

$usertype = getUserType($iid);



if($_GET['start_d']!=''|| $_GET['end_d']!='') { // Select the date from dashboard
	$sdate=$_GET['start_d'];
	$edate=$_GET['end_d'];
}

$sdate = date('Y-m-d H:i:s', strtotime($sdate));
$edate = date('Y-m-d H:i:s', strtotime($edate));

if($sdate!='' && $edate!=''){

    // if (isset($_GET['m_id']) && $_GET['m_id']!='') {

    //     $merchant_id = $_GET['m_id'];
    //     $query = "SELECT * FROM transaction_alipay WHERE merchant_id = '$merchant_id' AND trans_datetime >= '$sdate' AND trans_datetime <= '$edate' AND transaction_type!='cb3'AND transaction_type!='3'AND transaction_type!='s3' ORDER BY cst_trans_datetime DESC ";
    // } else {



        $Data = json_decode($_GET['array'],TRUE);

        // echo "<pre>";
        // print_r($Data);
        // die();
        

        $start_end = explode('-', $Data[0]['value']);




        $start_date = (isset($Data[0]['value']) && $start_end[0]!='') ? date('Y-m-d 00:00:00',strtotime($start_end[0])) : '';
        $end_date   = (isset($Data[0]['value']) && $start_end[1]!='') ? date('Y-m-d 23:59:59',strtotime($start_end[1])) : '';


        // $start_date = (isset($_POST['date_timepicker_start']) && $_POST['date_timepicker_start']!='') ? $_POST['date_timepicker_start'] : '';
        // $end_date   = (isset($_POST['date_timepicker_end']) && $_POST['date_timepicker_end']!='') ? $_POST['date_timepicker_end'] : '';
        $currencies = (isset($Data[4]['value']) && $Data[4]['value']!='') ? $Data[4]['value'] : '';
        $trans_type = (isset($Data[5]['value']) && $Data[5]['value']!='') ? $Data[5]['value'] : '';
        $merchants  = (isset($Data[6]['value']) && $Data[6]['value']!='') ? $Data[6]['value'] : '';
        $terminals  = (isset($Data[7]['value']) && $Data[7]['value']!='') ? $Data[7]['value'] : '';

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
        // $query = "SELECT * FROM transaction_alipay WHERE trans_datetime >= '$sdate' AND trans_datetime <= '$edate' AND transaction_type!='cb3'AND transaction_type!='3'AND transaction_type!='s3' ORDER BY cst_trans_datetime DESC ";
    
   
    $transactions = $db->rawQuery($query);
    // print_r(__LINE__);
    // die();

    $objPHPExcel = new PHPExcel();

    //$objPHPExcel->getActiveSheet()->setTitle("Transaction_ CardPayment_Details");

    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:J1');

    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Transactions Details on".'('.$start_date.'-'.$end_date.')');
   // $objPHPExcel->getActiveSheet()->getStyle("A1:J1")->getFont()->setBold(true);
     $styleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FF0000'),
                    'size'  => 10,
                    'name'  => 'Verdana'
                ));
    // $objPHPExcel->getActiveSheet()->getStyle("A".$i.":K".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->createSheet();

    $objPHPExcel->getActiveSheet()->getStyle("A3:C3")->getFont()->setBold(true);
     $objPHPExcel->getActiveSheet()->getStyle("A4:C4")->getFont()->setBold(true);

    $i = 6;

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("5");
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("90");
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("35");
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("25");
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("25");


    $objPHPExcel->getActiveSheet()->getStyle("A".$i.":L".$i)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A'.$i, 'S.No')
                 ->setCellValue('B'.$i, 'Transaction Type')
                 ->setCellValue('C'.$i, 'Merchant Name / Out Trade Number')
                 ->setCellValue('D'.$i, 'Refund Org ID')
                 ->setCellValue('E'.$i, 'Merchant ID')
                 ->setCellValue('F'.$i, 'Terminal ID')
                 ->setCellValue('G'.$i, 'Status')
                 ->setCellValue('H'.$i, 'Transaction Date')
                 ->setCellValue('I'.$i, 'Amount(LKR)')
                 ->setCellValue('J'.$i, 'Amount(USD)');

    $objPHPExcel->getActiveSheet()->getStyle("A".$i.":L".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


    $result = 'No Transactions Found';
    $transactions_Excel = $db->rawQuery($query);

    // echo "<pre>";
    // print_r($transactions_Excel);
    // die();

    $r=1;
    $i++;
	
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
            } else if($tr['transaction_type'] == 'cb1') {
                $transaction_type = 'CBP - SALE';
            } else if($tr['transaction_type'] == 'cb2') {
                $transaction_type =  'CBP - REFUND';
            } else if($tr['transaction_type'] == 'cb3') {
                $transaction_type =  'CBP - QUERY';
            }

            $transaction_amount='';
            if($tr['transaction_type'] == 2 || $tr['transaction_type'] == 's2') {
                $transaction_amount = "-".number_format($tr["refund_amount"],2);
            } else if($tr['transaction_type'] == 'cb1') {
                $transaction_amount_LKR = number_format($tr["amount"],2);
                $transaction_amount_USD =number_format($tr["total_fee"],2);
            } 
            else if($tr['transaction_type'] == 'cb2') {
                $transaction_amount_LKR = "-".number_format($tr["amount"],2);
                $transaction_amount_USD = "-".number_format($tr["refund_amount"],2);
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
	//print_r(__LINE__);
	//die();
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
		
		$merchant_id = '';
                $terminal_id = '';
                $trans_datetime = '';

            if($buyer_field!='') {
                $buyer_field_data = '<td>'.$tr["res_field_1"].'</td>';
            }
	//print_r(__LINE__);
		//die();
            $db->where("mer_map_id", $tr['merchant_id']);
		    $datacon =$db->getOne('merchants');
		    $merchant_name=$datacon['merchant_name'];
           //  $merchant_name = substr($datacon['merchant_name'],0,20);
            $trans_out_trade_no_digits  =  substr($trans_out_trade_no,0,12);
            $trans_out_trade_no_second_digits  =  substr($trans_out_trade_no,12);
		//print_r(__LINE__);
		//die();

            $objPHPExcel ->getActiveSheet()
                         ->getStyle('A'.$i.':L'.$i)
                         ->getAlignment()
                         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

              
           

           if($tr['transaction_type'] == 'cb1' || $tr['transaction_type'] == 'cb2' || $tr['transaction_type'] == 'cb3') {

	//print_r(__LINE__);
	//die();
		$merchant_id = isset($tr['merchant_id']) ? $tr['merchant_id'] :'';
		$terminal_id = isset($tr['terminal_id'])? $tr['terminal_id']:'';
		$trans_datetime = isset($tr['trans_datetime']) ? $tr['trans_datetime'] : '';
            	$objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $r)
                        ->setCellValue('B'.$i, $transaction_type)
                        ->setCellValue('C'.$i, $merchant_name.PHP_EOL.' '.'/'.' '.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits)
                        ->setCellValueExplicit('D'.$i, $trans_partner_trans_id, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('E'.$i, $merchant_id)
                        ->setCellValue('F'.$i, $terminal_id)
                        ->setCellValue('G'.$i, $sta)
                        ->setCellValue('H'.$i, $trans_datetime)
                        ->setCellValue('I'.$i,'LKR '.$transaction_amount_LKR)
                        ->setCellValue('J'.$i, 'USD '.$transaction_amount_USD);
         // print_r(__LINE__);
	//echo '1';
	//die();           
            } elseif($currency=="USD") {
			
		//print_r(__LINE__);
		//die();
		$merchant_id = isset($tr['merchant_id']) ? $tr['merchant_id'] :'';
                $terminal_id = isset($tr['terminal_id'])? $tr['terminal_id']:'';
                $trans_datetime = isset($tr['trans_datetime']) ? $tr['trans_datetime'] : '';

            	$objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $r)
                        ->setCellValue('B'.$i, $transaction_type)
                        ->setCellValue('C'.$i, $merchant_name.PHP_EOL.' '.'/'.' '.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits)
                        ->setCellValueExplicit('D'.$i, $trans_partner_trans_id, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('E'.$i, $merchant_id)
                        ->setCellValue('F'.$i, $terminal_id)
                        ->setCellValue('G'.$i, $sta)
                        ->setCellValue('H'.$i, $trans_datetime)
                        ->setCellValue('I'.$i,'')
                        ->setCellValue('J'.$i, $currency.' '.$transaction_amount);
                     
               
            }else {
		$merchant_id = isset($tr['merchant_id']) ? $tr['merchant_id'] :'';
                $terminal_id = isset($tr['terminal_id'])? $tr['terminal_id']:'';
                $trans_datetime = isset($tr['trans_datetime']) ? $tr['trans_datetime'] : '';

            	$objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $r)
                        ->setCellValue('B'.$i, $transaction_type)
                        ->setCellValue('C'.$i, $merchant_name.PHP_EOL.' '.'/'.' '.$trans_out_trade_no_digits.$trans_out_trade_no_second_digits)
                        ->setCellValueExplicit('D'.$i, $trans_partner_trans_id, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('E'.$i, $merchant_id)
                        ->setCellValue('F'.$i, $terminal_id)
                        ->setCellValue('G'.$i, $sta)
                        ->setCellValue('H'.$i, $trans_datetime)
                        ->setCellValue('I'.$i, $currency.' '.$transaction_amount)
                        ->setCellValue('J'.$i, '');
            }

            $i++;            
            $r++;

        }


    //$i = $i+4;
    // $objPHPExcel->setActiveSheetIndex(0)
    //             ->setCellValue('A'.$i,'*This is a system generated statement.');           
    //$objPHPExcel->getActiveSheet()->setTitle("Transaction_ CardPayment_Details");
     // $objPHPExcel->getActiveSheet()->setTitle('Transaction_ CardPayment_Results');

       header('Content-Type: application/vnd.ms-excel');
       header('Content-Disposition: attachment;filename="Transaction_ Details_Results_On.('.$start_date.'-'.$end_date.').xls"');
       header('Cache-Control: max-age=0');
       $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
       $objWriter->save('php://output');

        //header("Content-Disposition:attachment;filename=Transaction_ CardPayment_Results_On_$excel_selected_date.xls");

}
