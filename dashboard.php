					<?php

					include('init.php');

					require_once('token_dashhead.php');
					

					if($finalTokenValue) {

						// echo 'token value is '.$finalTokenValue.'<br>';
						$data = getUserToken($finalTokenValue); 
						if($data) {

							$iid = $data['user_id'];
							$user_id= $data['user_id'];

							$_SESSION['iid'] = $data['user_id'];

							$userDetail = getUserDetail($user_id);
							// echo "<pre>";
							// print_r($userDetail);
							$usertype = $userDetail['user_type'];

							$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							$user_id=$_SESSION['iid'];
							$event="view";
							$auditable_type="CORE PHP AUDIT";
							$new_values="";
							$old_values="";
							$ip = $_SERVER['REMOTE_ADDR'];
							$user_agent= $_SERVER['HTTP_USER_AGENT'];
							audittrails($user_id, $event, $auditable_type, $new_values, $old_values,$url, $ip, $user_agent);
							//die();


							$search_type = 'trans';

							$db->where("id",$user_id);
							$userDet = $db->getOne("users");


							
							if( $usertype == 4 || $usertype == 5) {


								$omg ='';

								if(!empty($env) && $env == 0) {
									$omg = 'TEST MODE ENABLED';
								}
							
						
							

							include('layout/header.php');
							
							include('layout/sidemenu.php');


							$currentdate = date('Y-m-d');
							$currentdate_start = date('Y-m-d 00:00:00');
							$currentdate_end   = date('Y-m-d 23:59:59');


							$userDet = getUserdata2($_SESSION['iid']);
							if($userDet['terminal_id'] !='') {
								$terminal_id = $userDet['terminal_id'];
								$db->where("idmerchants",$userDet['merchant_id']);
								$merchantDet = $db->getOne("merchants");
							} else {
								$terminal_id = '';
								$merchantDet = getUserdata3($_SESSION['iid']);
							}

							if($merchantDet['currency_code'] == 'USD') {
								setlocale(LC_MONETARY, 'en_US');
								$ccode = '$';
							} else if($merchantDet['currency_code'] == 'LKR') {
								setlocale(LC_MONETARY, 'en_US');
								// setlocale(LC_MONETARY, 'en_IN');
								$ccode = 'Rs';
							}

			///averge Transaction Amount
		$sql_avg_sale = "SELECT date(transaction_alipay.trans_date) as trans_date
					     , COUNT(id_transaction_id) AS num_orders
					     , SUM(transaction_alipay.total_fee) AS daily_total
					  FROM merchants JOIN transaction_alipay ON transaction_alipay.merchant_id = merchants.mer_map_id AND merchants.userid= '$iid' WHERE transaction_alipay.trans_date=CURDATE()
					  GROUP BY date(transaction_alipay.trans_date)";
		//echo $sql_avg_sale;


		$transactions_avg = $db->rawQuery($sql_avg_sale);

		//print_r($transactions_avg['0']['daily_total']);

		$CurrdayTransamt_avg  = $transactions_avg[0]['daily_total']!='' ? $transactions_avg[0]['daily_total'] : '--';


				

		/**** Terminal and Merchant based Queries ****/
		if($terminal_id!='') {
			require_once('dash_terminal.php'); 
		} else {

			$db->where('idmerchants',$userDet['merchant_id']);
			$user_payment_access = $db->getone('merchants');
			/**D- dynamic qr , S -static qr, W - Webpay , C- card pay ,N - Netbank ,U -UPI *****/
		    $payment_access = $user_payment_access['payment_access'];

		    if ($payment_access!='') {
		    	# code...
		    	$pay_access_str = explode("~",$payment_access);
		    	// print_r ($pay_access_str);
		    	// die();
		    	if (in_array("W", $pay_access_str)) {
		    		// print_r(__LINE__);
		    		// die();
		    		require_once('dash_merchant_webpay.php');
		    	} else {
		    		// print_r(__LINE__);
		    		// die();
		    		require_once('dash_merchant.php');
		    	}
		   
		    } else {
		    	require_once('dash_merchant.php');
		    }
		    
			// $pay_access_str = $db->getone('merchants');
				// print_r($user_payment_access['payment_access']);

				//require_once('dash_merchant.php');

				if ($payment_access!='') {

					$pay_access_str = explode("~",$payment_access);
			    	// print_r ($pay_access_str);
			    	// die();
			    	if (in_array("W", $pay_access_str)) {

			    		$cancel_flag = 1;
			    	}

				} else {

					// $cancel_flag = 1;
				}

		}
							?>

							<style type="text/css">
							.daterangepicker.show-calendar .drp-buttons {
								display: none !important;
							}
							#button1 {color: #e8edef;
							    background-color: #44b547;}
							</style>
					<body class="app sidebar-mini">

					
					<div class="page">
				<div class="app-content">
					<div class="side-app leftmenu-icon">

					    <!--Page header-->
                        <div class="page-header">
                            <div class="page-leftheader">
                                <ol class="breadcrumb pl-0">
                                    <li class="breadcrumb-item"><a href="dashboard.php?t=<?php echo urlencode($_GET['t']); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                        <!--End Page header-->

						<!--Row-->
						<div class="row">
							<div class="col-md-12">
								<div class="card ">
									<div class="row">
										<div class=" col-xl-3 col-sm-6 d-flex border-right">
											<div class="card-body text-center">
											    <div class="d-flex justify-content-center">
												    <div class="mt-3">
														<i class="fe fe-pie-chart fs-30 text-primary mr-5"></i>
													</div>
													<div class=" text-center text-left">
														<p class="mb-1 text-left">Transactions Value (Today)</p>
														<h3 class="mb-0 text-left font-weight-semibold"><?php echo $CurrdayTransamount; ?></h3>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-sm-6 pr-0 pl-0 border-right">
											<div class="card-body text-center">
											    <div class="d-flex justify-content-center">
												    <div class="mt-3">
														<i class="fe fe-users fs-30 text-danger mr-5 "></i>
													</div>
													<div class=" text-center text-left">
														<p class="mb-1 text-left">TodayTransactions Count (Today)</p>
														<h3 class="mb-0 text-left font-weight-semibold"><?php echo $CurrdayTranscount; ?></h3>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-sm-6 pr-0 pl-0 border-right">
											<div class="card-body text-center">
											    <div class="d-flex justify-content-center">
												    <div class="mt-3">
														<i class="fe fe-bar-chart-2 fs-30 text-secondary mr-5 "></i>
													</div>
													<div class=" text-center text-left">
														<p class="mb-1 text-left">Avg Transcations</p>
														<h3 class="mb-0 text-left font-weight-semibold"> <?php echo $CurrdayTransamt_avg;?></h3>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-sm-6 pr-0 pl-0 ">
											<div class="card-body text-center">
											    <div class="d-flex justify-content-center">
												    <div class="mt-3">
														<i class="fe fe-layers fs-30 text-success mr-5 "></i>
													</div>
													<div class=" text-center text-left">
														<p class="mb-1 text-left">Refund Transactions</p>
														<h3 class="mb-0 text-left font-weight-semibold"><?php echo $CurrTodayTrans_refund_amount; ?></h3>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- End row -->

						<!-- Row-->
						<div class="row">
							
							<div class="col-xl-12 col-md-12 col-lg-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Daily Summary</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
									   <div class="row">
										   <div class="col-xl-4 col-lg-4 col-md-2">
												<div class="overflow-hidden  justify-content-center mx-auto text-center align-items-center">
													<div id="chart"></div>
												</div>
											</div>
											<div class="col-xl-8 col-lg-8 col-md-10">
											    <table class="table table-hover mb-0">
												<thead>
													<tr>
														<th class=""></th>
														<th class="">Transactions Type	</th>
														<th class="wd-15p">Number of Transaction</th>
														<th class="wd-20p">Total Amount</th>
														
														
													</tr>
												</thead>
													<tbody>
														<tr class="border-bottom">
															<td class="p-2"><div class="w-3 h-3 bg-primary mr-2 mt-1 brround"></div></td>
															<td class="p-2">Total Sale Transactions	</td>
															<td class="p-2"><?php echo $CurrTodayTranscount; ?></td>
															<td class="p-2"><?php echo $CurrTodayTransamount; ?></td>
														</tr>
														<tr class="border-bottom">
															<td class="p-2"><div class="w-3 h-3 bg-orange mr-2 mt-1 brround"></div></td>
															<td class="p-2">Total Refund Transactions</td>
															<td class="p-2"><?php echo $CurrTodayTrans_cancel_count; ?></td>
															<td class="p-2"><?php echo $CurrTodayTrans_refund_amount; ?></td>
														</tr>
														<tr class="border-bottom" <?php if($cancel_flag==1) { ?> style="display: none"<?php } ?>>
															<td class="p-2"><div class="w-3 h-3 bg-warning mr-2 mt-1 brround"></div></td>
															<td class="p-2">Total Cancel Transactions	</td>
															<td class="p-2"><?php echo $CurrTodayTrans_refund_count; ?></td>
															<td class="p-2"><?php echo $CurrTodayTrans_cancel_amount; ?></td>
														</tr>
														
													</tbody>
												</table>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
						<!-- End Row -->



						<!--Row-->
						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-7">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Analysis of transactions for the current year.</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body overflow-hidden">
										<!--<div id="flotContainer2" class="chart-style"></div>-->
										<div id="user_chart" style='height:270px;'></div>
									</div>
									
								</div>
							</div>
							
						</div>
						<!--End Row-->

						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-7">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title"> Transaction History</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body overflow-hidden">
										<div class="col-xs-5 col-sm-4 date-sec">
											

											<div class="input-group date datesummary">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												
												<input class="form-control" name="date2" id="date2" type="text"  value="" autocomplete="off" >
												<input type="hidden" name="date_1" id="date_1" value="<?php echo date('m/d/Y'); // echo date('m/d/Y',strtotime("-1 days")); ?>">
												<input type="hidden" name="merchantid" id="merchantid" value="<?php echo $merchantDet['mer_map_id']; ?>">
												<input type="hidden" name="terminalid" id="terminalid" value="<?php echo $terminal_id; ?>">
											</div>
											<br>

											<div class="btn-list">
												
												<input type="button" name="button" id="button1" class="btn btn-pill btn-success"  value="Submit">
										    </div>

										</div>

									</div>
									
								</div>
							</div>
							
						</div>

						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-7">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title"> Transaction Details</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											
										</div>
									</div>
									<div class="card-body overflow-hidden">

										<div id="reportresults"></div>

									</div>
									
								</div>
							</div>
							
						</div>

						<!-- Row-->
						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Transaction Details</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<div id="cbresults"></div>
										</div>
									</div>
									<!-- table-wrapper -->
								</div>
							</div>
						</div>
						<!-- End Row -->

					</div>
				</div><!-- end app-content-->
			</div>

			<!--Footer-->
			<!-- <footer class="footer">
				<div class="container">
					<div class="row align-items-center flex-row-reverse">
						<div class="col-md-12 col-sm-12 mt-3 mt-lg-0 text-center">
							Copyright © 2019 <a href="#">Aronox</a>. Designed by <a href="#">Spruko Technologies Pvt.Ltd</a> All rights reserved.
						</div>
					</div>
				</div>
			</footer> -->
			<!-- End Footer-->

		</div>


	</body>


		<?php 
		include('layout/footer.php');
		} 
			} else {
				echo '<h1>You are Unauthorized</h1>'; // echo "Hi1";
				header("location:/".SITEURLPATH);
			}
		} else {
		echo '<h1>You are Unauthorized</h1>'; // echo "Hi2";
		header("location:/".SITEURLPATH);
		}
		$total_sum_array=array();
		$each_month_sum="SELECT ROUND(sum(total_fee)) as total_amount FROM transaction_alipay JOIN merchants ON  merchants.mer_map_id = transaction_alipay.merchant_id  AND merchants.userid= '$iid' WHERE  YEAR(cst_trans_datetime) = YEAR(CURDATE()) AND transaction_type='1' AND result_code='SUCCESS' AND trade_status='TRADE_SUCCESS' group by year(`cst_trans_datetime`), month(`cst_trans_datetime`)";

		//echo $each_month_sum;
		$trans_sum_each_month = $db->rawQuery($each_month_sum);
		for($i=0;$i<count($trans_sum_each_month);$i++){
			array_push($total_sum_array,$trans_sum_each_month[$i]['total_amount']);
		}

		$curr_Saletransaction = ($CurrTodayTransamount>0) ? $CurrTodayTransamount : '0';
		$curr_Refundtransaction = ($CurrTodayTrans_refund_amount>0) ? $CurrTodayTrans_refund_amount : '0';
		$curr_Canceltransaction = ($CurrTodayTrans_cancel_amount>0) ? $CurrTodayTrans_cancel_amount : '0';

		// //$summary[] = 50;//$curr_Saletransaction;
		// $summary[] = 60;//$curr_Refundtransaction;
		// $summary[] = $curr_Canceltransaction;
		//print_r($summary);

		


		?>
</html>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="<?php echo $public_path; ?>/assets/plugins/flot/jquery.flot.js"></script>
<script type="text/javascript">


	$( document ).ready(function() {
		/**** Daily Summary Report for selecting date from picker ****/
		$('#date2').on("change", function () {

			// alert("Hiiiii");
			// alert("OnChange=> "+ $(this).val());
			//var selected_date = $(this).val();
			var t ='<?php echo urlencode($_GET['t']); ?>';
	        var selected_date = $('#date2').val();
	        var period_start_date = selected_date.slice(0, 16);
	        var period_end_date = selected_date.slice(19, 36);
	        var selected_merchantid = $('#merchantid').val();
			var selected_terminalid = $('#terminalid').val();
			if(selected_date!='') {
				$('.rlt_row').show();
			}

			var Trans='1';

			var session_id = <?php echo $iid; ?>


			/**** Total Transaction Amount with count ****/
	        // var postData = {start_date:period_start_date, end_date:period_end_date, currencies:'0', transaction_type:'0', from_dash: 1};

	        /**** Total Transaction Amount with count ****/
			if(selected_merchantid!='' && selected_terminalid!='') { // For Terminal Based User's Login

				var postData = {start_date:period_start_date, end_date:period_end_date, currencies:'0', transaction_type:'0', from_dash: 1,Trans:Trans,merchantid:selected_merchantid,terminalid:selected_terminalid,t:t};
				var postUrl = "php/inc_reporthutchmercht.php";

				var postData_T = {period_start_date1: period_start_date, period_end_date1:period_end_date, session_id:<?php echo $iid; ?>,merchantid:selected_merchantid,terminalid:selected_terminalid,t:t};
				var postData_TUrl = "php/inc_reporthutchmercht.php";

				var exportLink = "php/inc_reporthutchmercht.php?start_d="+period_start_date+"&end_d="+period_end_date+"&merchantid="+selected_merchantid+"&terminalid="+selected_terminalid;

			} else {                                                 // For Merchant Based User's Login
		    	var postData = {start_date:period_start_date, end_date:period_end_date, currencies:'0', transaction_type:'0', from_dash: 1,t:t};
		    	var postUrl = "php/inc_reportsearch1.php";

		    	var postData_T = {period_start_date1: period_start_date, period_end_date1:period_end_date, session_id:<?php echo $iid; ?>,t:t};
				var postData_TUrl = "php/inc_<?php echo $search_type; ?>search.php";

				var exportLink = "php/inc_transsearch.php?start_d="+period_start_date+"&end_d="+period_end_date+"&session_id="+session_id;
		    }

	        // $("#reports_form").serializeArray();
	        console.log(postData);
	        $.ajax({
	            method: "POST",
		        url: postUrl,
		        data: postData
	        })
	        .done(function( msg ) {
	            $("#reportresults").html(msg);
	        });
	        // END
	        console.log(postData_T);

	        /**** Transaction List ****/
			// alert(period_start_date+" => "+period_end_date);
			$.ajax({
				method: "POST",
				url: postData_TUrl,
				data: postData_T
			})
			.done(function( msg ) {

				// alert('Hiiiii');
				$("#cbresults").html(msg);
				console.log(msg);

				$('#example').dataTable({
					responsive:true
	              
	            });

				// $('.dataTables-example').dataTable({
				// 	"order": [[ 0, "asc" ]],
				// 	responsive: true,
				// 	"dom": 'T<"clear">lfrtip',
				// 	"tableTools": {
				// 		"sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
				// 	}
				// });
				// $('input:checkbox').change(function() { 
				// 	if($(this).attr('id') == 'selectall') {
				// 		jqCheckAll2( this.id);
				// 	}
				// });
				// function jqCheckAll2( id ) {
				// 	$("INPUT[type='checkbox']").attr('checked', $('#' + id).is(':checked'));
				// }

				var session_id = <?php echo $iid; ?>

				// $("#exportlink_date").attr("href", "php/inc_transsearch.php?start_d="+period_start_date+"&end_d="+period_end_date+"&session_id="+session_id);
				$("#exportlink_date").attr("href", exportLink);

				if(msg.slice(0,21) == 'No Transactions Found') {
		            $("#exportlink_date").hide();
		        } else {
		            $("#exportlink_date").show();
		        }
			});
			// END
		});
		
	});
	$(function() {
    $('input[name="date2"]').daterangepicker({
        timePicker: true,
        startDate: '<?php echo date('m/d/Y 00:00'); ?>',
        endDate: '<?php echo date('m/d/Y 23:59'); ?>',
        locale: {
            format: 'MM/DD/YYYY HH:mm'
        }
    });
});

				
</script>

<script>

$(function(e){
  'use strict'
	  var options = {
            chart: {
                width: 380,
				height:230,
                type: 'donut',
            },
            dataLabels: {
                enabled: false
            },
            series: [<?php echo $curr_Saletransaction; ?>,<?php echo $curr_Canceltransaction; ?>,<?php echo $curr_Refundtransaction; ?>],
			colors:['#4a32d4','#f7592d','#f7be2d'],
			/*labels: [
					"Total Sale Transactions",
					"Total Refund Transactions",
					"Total Cancel Transactions"
					
				],*/
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        show: false,
                    }
                }
            }],
           
        }
        var chart = new ApexCharts(
            document.querySelector("#chart"),
            options
        );
        chart.render()
});
</script>
<script>

				/* ---hightchart7----*/
				var chart = Highcharts.chart('user_chart', {
					chart: {
						backgroundColor: 'transparent',
					},
					title: {
						text: ''
					},
					subtitle: {
						//text: 'Plain'
					},
					exporting: {
						enabled: false
					},
					credits: {
						enabled: false
					},
					xAxis: {
						//gridLineColor: 'rgb(227, 226, 236,0.4)',
						categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
					},
					yAxis: {
						//gridLineColor: 'rgba(255,255,255,0.06)'
					},
					colors: ['#4a32d4', '#f72d66', '#ecb403', '#24CBE5', '#64E572', '#FF9655', '#f1c40f', '#592df7'],
					series: [{
						type: 'column',
						colorByPoint: true,
						data: [<?php echo implode(",",$total_sum_array); ?>],
						showInLegend: false
					}]
				});
				
</script>