<?php
$merchantname = '310 Nutrition LLC';
$merchantaddress1 = '11 Anywhere';
$merchantaddress2 = '';
$merchantcity = 'tampa';
$merchantstate = 'FL';
$merchantzip = '23423';
$Month = 'October';
$Year = '1832';
$VisaSalesCount = '25';
$MCSalesCount = '30';
$AmexSalesCount = '40';
$DiscoverSalesCount = '0';
$VisaSalesAmount = '7300.50';
$MCSalesAmount = '100.00';
$AmexSalesAmount = '99.50';
$DiscoverSalesAmount = '0.00';
$VisaRefundsCount = '6';
$MCRefundsCount = '7';
$AmexRefundsCount = '8';
$DiscoverRefundsCount = '0';
$VisaRefundsAmount = '40.55';
$MCRefundsAmount = '0.45';
$AmexRefundsAmount = '10.00';
$DiscoverRefundsAmount = '0.00';
$VisaChargebacksCount = '645';
$MCChargebacksCount = '654';
$AmexChargebacksCount = '456';
$DiscoverChargebacksCount = '123';
$VisaChargebacksAmount = '0.00';
$MCChargebacksAmount = '0.00';
$AmexChargebacksAmount = '0.00';
$DiscoverChargebacksAmount = '0.00';
$VisaFeesAmount = '.25';
$MCFeesAmount = '.25';
$AmexFeesAmount = '.25';
$DiscoverFeesAmount = '3';
 
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');
 
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
 
date_default_timezone_set('Europe/London');
mt_srand(1234567890);
 
/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
 
 
// List functions
echo date('H:i:s') , " List implemented functions" , EOL;
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());
 
// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();
 $objWorkSheet = $objPHPExcel->createSheet();
 $objPHPExcel->setActiveSheetIndex(0); 
// Add some data, we will use some formulas here
echo date('H:i:s') , " Add some data and formulas" , EOL;
$objPHPExcel->getActiveSheet()
->setTitle('jimname Me')
->setCellValue('B2', $merchantname)
->setCellValue('B3', $merchantaddress1)
->setCellValue('B4', $merchantaddress2)
->setCellValue('B5', $merchantcity.','.$merchantstate.','.$merchantzip)
->setCellValue('b15', 'Visa')
->setCellValue('b16', 'MasterCard')
->setCellValue('b17', 'American Express')
->setCellValue('b18', 'Discover')
->setCellValue('B21', 'Total')
->setCellValue('I2', 'For the month of ')
->setCellValue('K2', $Month)
->setCellValue('L2', $Year)
 
->setCellValue('C12', 'Sales')
->mergeCells('C12:D12')
->setCellValue('e12', 'Refunds')
->mergeCells('e12:f12')
->mergeCells('g12:h12')
->setCellValue('g12', 'Net')
->mergeCells('i12:j12')
->setCellValue('i12', 'Chargebacks')
->setCellValue('k12', 'Fees')
->setCellValue('c13', 'Count')
->setCellValue('d13', 'Amount')
->setCellValue('e13', 'Count')
->setCellValue('f13', 'Amount')
->setCellValue('g13', 'Count')
->setCellValue('h13', 'Amount')
->setCellValue('i13', 'Count')
->setCellValue('j13', 'Amount')
->setCellValue('k13', 'Amount')
->setCellValue('c15', $VisaSalesCount)
->setCellValue('c16', $MCSalesCount)
->setCellValue('c17', $AmexSalesCount)
->setCellValue('c18', $DiscoverSalesCount)
->setCellValue('d15', $VisaSalesAmount)
->setCellValue('d16', $MCSalesAmount)
->setCellValue('d17', $AmexSalesAmount)
->setCellValue('d18', $DiscoverSalesAmount)
->setCellValue('e15', $VisaRefundsCount)
->setCellValue('e16', $MCRefundsCount)
->setCellValue('e17', $AmexRefundsCount)
->setCellValue('e18', $DiscoverRefundsCount)
->setCellValue('f15', $VisaRefundsAmount)
->setCellValue('f16', $MCRefundsAmount)
->setCellValue('f17', $AmexRefundsAmount)
->setCellValue('f18', $DiscoverRefundsAmount)
->setCellValue('G15', '=c15+e15')
->setCellValue('G16', '=c16+e16')
->setCellValue('G17', '=c17+e17')
->setCellValue('G18', '=c18+e18')
->setCellValue('H15', '=d15-f15')
->setCellValue('H16', '=d16-f16')
->setCellValue('H17', '=d17-f17')
->setCellValue('H18', '=d18-f18')
->setCellValue('I15', $VisaChargebacksCount)
->setCellValue('I16', $MCChargebacksCount)
->setCellValue('I17', $AmexChargebacksCount)
->setCellValue('I18', $DiscoverChargebacksCount)
->setCellValue('I15', $VisaChargebacksAmount)
->setCellValue('I16', $MCChargebacksAmount)
->setCellValue('I17', $AmexChargebacksAmount)
->setCellValue('I18', $DiscoverChargebacksAmount)
->setCellValue('K15', $VisaFeesAmount)
->setCellValue('K16', $MCFeesAmount)
->setCellValue('K17', $AmexFeesAmount)
->setCellValue('K18', $DiscoverFeesAmount)
->setCellValue('c21', '=SUM(c15:c18)')
->setCellValue('d21', '=SUM(d15:d18)')
->setCellValue('e21', '=SUM(e15:e18)')
->setCellValue('f21', '=SUM(f15:f18)')
->setCellValue('g21', '=SUM(g15:g18)')
->setCellValue('h21', '=SUM(h15:h18)')
->setCellValue('i21', '=SUM(i15:i18)')
->setCellValue('j21', '=SUM(j15:j18)')
->setCellValue('k21', '=SUM(k15:k18)')
;
 
 
 $objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getStyle('D15:D18')->getNumberFormat()->applyFromArray(
         array(
             'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
         )
 );
$objPHPExcel->getActiveSheet()->getStyle('F15:F18')->getNumberFormat()->applyFromArray(
         array(
             'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
         )
 );
$objPHPExcel->getActiveSheet()->getStyle('H15:H18')->getNumberFormat()->applyFromArray(
         array(
             'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
         )
 );
 $objPHPExcel->getActiveSheet()->getStyle('k15:k18')->getNumberFormat()->applyFromArray(
         array(
             'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
         )
 );
 $objPHPExcel->getActiveSheet()->getStyle('j15:j18')->getNumberFormat()->applyFromArray(
         array(
             'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
         )
 );
 

  $objPHPExcel->setActiveSheetIndex(1); 
$objPHPExcel->getActiveSheet() 
->setTitle('Fees')
->setCellValue('B2', $merchantname)
->setCellValue('B3', $merchantaddress1)
->setCellValue('B4', $merchantaddress2)
;

// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);
 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
 
//
//  If we set Pre Calculated Formulas to true then PHPExcel will calculate all formulae in the
//    workbook before saving. This adds time and memory overhead, and can cause some problems with formulae
//    using functions or features (such as array formulae) that aren't yet supported by the calculation engine
//  If the value is false (the default) for the Excel2007 Writer, then MS Excel (or the application used to
//    open the file) will need to recalculate values itself to guarantee that the correct results are available.
//
//$objWriter->setPreCalculateFormulas(true);
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
 
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
 
 
// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
 
// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;


?>