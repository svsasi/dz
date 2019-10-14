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


							if( $usertype == 4 || $usertype == 5) {

								$omg ='';

								if(!empty($env) && $env == 0) {
									$omg = 'TEST MODE ENABLED';
								}
							} else {
					
							include('layout/header.php');
							
							include('layout/sidemenu.php');

					
					//Total Transaction count
					$T_transaction_count_data="SELECT COUNT(*) AS id_transaction_id FROM transaction_alipay WHERE transaction_type='1' AND result_code='SUCCESS'";
					$T_transaction_count=$db->rawQuery($T_transaction_count_data);

					//Total Merchant Count
					$T_merchant_count_data="SELECT COUNT(*) AS idmerchants FROM merchants";
					$T_merchant_count=$db->rawQuery($T_merchant_count_data);

					//Total Success Transaction amount
					$T_transaction_amount_data= "SELECT sum(total_fee) AS total_fee FROM transaction_alipay WHERE transaction_type='1' AND result_code='SUCCESS' AND trade_status='TRADE_SUCCESS'";
					$T_transaction_amount=$db->rawQuery($T_transaction_amount_data);

					//Refund Transaction
					$T_count_Refunds_data= "SELECT COUNT(*) AS refund_amount FROM transaction_alipay WHERE transaction_type IN ('2','s2','cb2')";
					$T_count_Refunds=$db->rawQuery($T_count_Refunds_data);
					$T_amount_Refunds_data= "SELECT sum(refund_amount) AS refund_amount FROM transaction_alipay WHERE transaction_type IN ('2','s2','cb2') AND result_code='SUCCESS'";
					$T_amount_Refunds=$db->rawQuery($T_amount_Refunds_data);
					$T_count_void_data= "SELECT COUNT(*) AS refund_amount FROM transaction_alipay WHERE transaction_type ='2v'";
					$T_count_void=$db->rawQuery($T_count_void_data);
	

					//Yearly Transaction Reports
					$Y_transaction_amount_data= "SELECT sum(total_fee) AS total_fee FROM transaction_alipay WHERE transaction_type='1' AND result_code='SUCCESS' AND trade_status='TRADE_SUCCESS' AND trans_date= YEAR('2019-06-15')";
					$Y_transaction_amount=$db->rawQuery($Y_transaction_amount_data);

							$iid = $_SESSION['iid'];

							$iid = $_SESSION['iid'];
							$db->where("id",$iid);
							$userDet = $db->getOne('users');
							$username = $userDet['username'];


					//Last 7 Transaction

					$Last_7_Transaction_data="SELECT merchant_id,total_fee,result_code FROM transaction_alipay WHERE trans_date > DATE(NOW()) - INTERVAL 7 DAY ORDER BY trans_date DESC limit 0,8";
					
					//echo count($Last_7_Transaction);
					 // echo "<pre>";
					 // print_r($Last_7_Transaction);
					 // exit;
						

					//var_dump($db);

					//$currentdate = date('Y-m-d');
					$currentdate = '2019-09-10';
					$currenttrans_sql = "SELECT * FROM transaction_alipay WHERE  DATE(trans_datetime) = '$currentdate'";
					$transactions_Currday = $db->rawQuery($currenttrans_sql);
					
					
					$total_sum_array=array();
					$each_month_sum="SELECT ROUND(sum(total_fee)) as total_amount FROM transaction_alipay 
									WHERE  YEAR(cst_trans_datetime) = YEAR(CURDATE()) AND transaction_type='1' AND result_code='SUCCESS' AND trade_status='TRADE_SUCCESS' 
									group by year(`cst_trans_datetime`), month(`cst_trans_datetime`)";
					$trans_sum_each_month = $db->rawQuery($each_month_sum);
					for($i=0;$i<count($trans_sum_each_month);$i++){
						array_push($total_sum_array,$trans_sum_each_month[$i]['total_amount']);
					}
					
					$sql_query = "SELECT * FROM audits";

					$Details_Currday = $db->rawQuery($sql_query);


					



					
				?>
				<div class="app-content">
					<div class="side-app leftmenu-icon">

					
					
						<!--Page header-->
						<div class="page-header">
							<div class="page-leftheader">
								<ol class="breadcrumb pl-0">
									<li class="breadcrumb-item"><a href="#">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
								</ol>
							</div>
						</div>
						<!--End Page header-->
						<!--Row-->
						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-12">
								<div class="card">
                                    <div class="card-body">
									
										<div class="row">
											<div class=" col-xl-3 col-sm-6 d-flex mb-5 mb-xl-0">
												<div class="feature">
													<i class="si si-briefcase primary feature-icon bg-primary"></i>
												</div>
												<div class="ml-3">
													<small class=" mb-0">Total Transactions</small><br>
													<h3 class="font-weight-semibold mb-0"><?php print_r($T_transaction_count[0]['id_transaction_id']); ?></h3>
													
												</div>
											</div>
											<div class=" col-xl-3 col-sm-6 d-flex mb-5 mb-xl-0">
												<div class="feature">
													<i class="si si-layers danger feature-icon bg-danger"></i>
												</div>
												<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Total Total Merchant</small>
													<h3 class="font-weight-semibold mb-0"><?php print_r($T_merchant_count[0]['idmerchants']); ?></h3>
													
												</div>
											</div>
											<div class=" col-xl-3 col-sm-6 d-flex  mb-5 mb-sm-0">
												<div class="feature">
													<i class="si si-note secondary feature-icon bg-secondary"></i>
													<!--<i class="fe fe-dollar-sign  bg-secondary" style="font-size:67px;color:#ffffff;"></i>-->
													<!--<i class="fa fa-money" style="font-size:48px;color:#0099ff;" style="font-size:48px;color:#0099ff;" aria-hidden="true"></i>-->

												</div>
												<div class=" d-flex flex-column ml-3"> <small class=" mb-0">Total profits</small>
													<h3 class="font-weight-semibold mb-0">12,863</h3>
													
												</div>
											</div>
											<div class=" col-xl-3 col-sm-6 d-flex">
												<div class="feature">
													<i class="si si-basket-loaded success feature-icon bg-success"></i>
												</div>
												<div class=" d-flex flex-column  ml-3"> <small class=" mb-0">Total Refunds</small>
													<h3 class="font-weight-semibold mb-0"><?php print_r(number_format($T_amount_Refunds[0]['refund_amount'])); ?></h3>
													
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--End row-->

						<!--Row-->
						<div class="row">
							<div class="col-xl-5 col-md-12 col-lg-12">
								<div class="card">
								    <div class="card-header">
									    <h3 class="card-title mb-0">Refund Transactions</h3>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-md-4 text-center mb-4 mb-md-0">
												<p class="data-attributes mb-0">
													<span class="donut" data-peity='{ "fill": ["#4a32d4", "#e5e9f2"]}'>4/5</span>
												</p>
												

												<h4 class=" mb-0 font-weight-semibold"><?php print_r($T_count_Refunds[0]['refund_amount']); ?></h4>
												<p class="mb-0 text-muted">Total No.Of Refunds</p>
											</div>
											<div class="col-md-4 text-center mb-4 mb-md-0">
												<p class="data-attributes mb-0">
													<span class="donut" data-peity='{ "fill": ["#fb1c52", "#e5e9f2"]}'>226/360</span>
												</p>
												<h4 class=" mb-0 font-weight-semibold"><?php print_r(number_format($T_amount_Refunds[0]['refund_amount'])); ?></h4>
												<p class="mb-0 text-muted">Total Amount in refunds</p>
											</div>
											<div class="col-md-4 text-center">
												<p class="data-attributes mb-0">
													<span class="donut" data-peity='{ "fill": ["#f7be2d", "#e5e9f2"]}'>4,4</span>
												</p>
												<h4 class=" mb-0 font-weight-semibold"><?php print_r($T_count_void[0]['refund_amount']); ?></h4>
												<p class="mb-0 text-muted">Total No.Of Void</p>
											</div>
										</div>
									</div>
								</div>
								<div class="card">
									<div class="card-body">
										<div class=" ">
											<h5>Yearly Transaction Reports</h5>
										</div>
										<h2 class="mb-2 font-weight-semibold"><?php print_r(number_format($Y_transaction_amount[0]['total_fee'])); ?><span class="sparkline_bar31 float-right"></span></h2>
									</div>
								</div>
							</div>
							<div class="col-xl-7 col-md-12 col-lg-12">
								<div class="card overflow-hidden">
									<div class="card-header">
										<h3 class="card-title">Total Transactions Report</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-xl-4 col-lg-4 col-md-12 mb-5">
												<p class=" mb-0 "> Total Transactions</p>
												<h2 class="mb-0 font-weight-semibold"><?php print_r($T_transaction_count[0]['id_transaction_id']); ?></h2>
											</div>
											<div class="col-xl-4 col-lg-4 col-md-12 mb-5">
												<p class=" mb-0 ">Total Income</p>
												<h2 class="mb-0 font-weight-semibold"><?php print_r(number_format($T_transaction_amount[0]['total_fee'])); ?></h2>
											</div>
											<div class="col-xl-4 col-lg-4 col-md-12 mb-5">
												<p class=" mb-0 "> Total Profits</p>
												<h2 class="mb-0 font-weight-semibold"></h2>
											</div>
										</div>
										<div class="chart-wrapper123">
											
											<div id="highchart7" style='height:190px;'></div>
									    </div>
									</div>
								</div>
							</div>
						</div>
						<!--End row-->

						<!--Row-->
						<div class="row">
							<div class="col-xl-4 col-md-12 col-lg-6">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Today Activity</div>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body sm-p-0">
										<div class="list-group projects-list border pt-0 pb-0 pl-0 pr-0">
											<a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-0">
												<div class="d-flex w-100 justify-content-between">
													<h6 class="mb-1 font-weight-semibold"> Admin has New QR Code Generated</h6>
													<h6 class="mb-0 font-weight-semibold"></h6>
												</div>
												<div class="d-flex w-100 justify-content-between">
													<span class="text-muted">--</span>
													<span class="text-muted">Sept 17,2019</span>
												</div>
											</a>
											<a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-bottom-0  border-left-0 border-right-0 border-top">
												<div class="d-flex w-100 justify-content-between">
													<h6 class="mb-1 font-weight-semibold">Admin has Transaction Report Genertaed</h6>
													<h6 class=" mb-0 font-weight-semibold "></h6>
												</div>
												<div class="d-flex w-100 justify-content-between">
													<span class="text-muted"><i class="fe fe-arrow-down text-danger "></i></span>
													<span class="text-muted">Sept 17,2019</span>
												</div>
											</a>
											<a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-bottom-0  border-left-0 border-right-0 border-top">
												<div class="d-flex w-100 justify-content-between">
													<h6 class="mb-1 font-weight-semibold ">Admin has initiate Refund Transactions</h6>
													<h6 class="mb-0 font-weight-semibold "></h6>
												</div>
												<div class="d-flex w-100 justify-content-between">
													<span class="text-muted"><i class="fe fe-arrow-up text-success"></i> </span>
													<span class="text-muted">Sept 17,2019</span>
												</div>
											</a>
											<a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-bottom-0  border-left-0 border-right-0 border-top">
												<div class="d-flex w-100 justify-content-between">
													<h6 class="mb-1 font-weight-semibold ">Admin has Added New Merchant</h6>
													<h6 class=" mb-0 font-weight-semibold"></h6>
												</div>
												<div class="d-flex w-100 justify-content-between">
													<span class="text-muted"><i class="fe fe-arrow-down text-danger "></i> </span>
													<span class="text-muted">Sept 17,2019</span>
												</div>
											</a>
											<a href="#" class="list-group-item list-group-item-action flex-column align-items-start border-bottom-0  border-left-0 border-right-0 border-top">
												<div class="d-flex w-100 justify-content-between">
													<h6 class="mb-1 font-weight-semibold">Others</h6>
													<h6 class="mb-0 font-weight-semibold"></h6>
												</div>
												<div class="d-flex w-100 justify-content-between">
													<span class="text-muted"><i class="fe fe-arrow-up text-success "></i> </span>
													<span class="text-muted">Sept 17,2019</span>
												</div>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-8 col-md-12 col-lg-6">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Last 7 Day Transactions</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="table-responsive">

										<table class="table card-table table-vcenter text-nowrap">
											<thead>
												<tr>
													<th>ID</th>
													<th>Merchant</th>
													<th>Status</th>
													<th>Amount</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$Last_7_Transaction=$db->rawQuery($Last_7_Transaction_data);
												if (!empty($Last_7_Transaction)) {
												
												$i=1;
												foreach($Last_7_Transaction as $result){
												$db->where("mer_map_id", $result['merchant_id']);
											    $datacon =$db->getOne('merchants');
											  $merchant_name=substr($datacon['merchant_name'],0,50);
													
													?>									
												<tr>
													<th scope="row"><?php echo $i++; ?> </th>
													<td><?php echo $merchant_name;?></td>
													<?php if($result['result_code']="SUCCESS")
													{ ?>
													<td style='color:green;'><?php echo $result['result_code'];
													} ?> </td>
													<?php if($result['result_code'] != "SUCCESS")
													{ ?>
													<td style='color:red;'><?php echo $result['result_code'];
													} ?> </td>
													<td><?php echo $result['total_fee']; ?></td>
												</tr>
												<?php 
												} 
											} else{ 
											?>

											</tbody>

										</table>
										<div style="display: flex;align-items: center;height: 100%;">
										<svg style=" margin: 0 auto;display: block;" xmlns="http://www.w3.org/2000/svg"  shape-rendering="geometricPrecision" width="100" height="330" viewBox="0 0 24 24"><defs><path id="atransactionIcon" d="M0 0h24v24H0V0z"></path><mask id="btransactionIcon" width="24" height="24" x="0" y="0" fill="#fff"><use xlink:href="#atransactionIcon"></use></mask><path id="ctransactionIcon" d="M6.26 11.667L2.291 7.689a1 1 0 0 1 0-1.412L6.26 2.295a.999.999 0 0 1 1.414-.003c.391.39.392 1.023.003 1.415L5.372 6.02H21a1 1 0 1 1 0 2H5.446l2.23 2.235a1 1 0 1 1-1.417 1.412zm11.482.664l3.966 3.977c.389.389.389 1.02.001 1.412l-3.967 3.983a.999.999 0 0 1-1.414.003 1.002 1.002 0 0 1-.003-1.415l2.304-2.313H3a1 1 0 1 1 0-2h15.555l-2.229-2.235a1 1 0 1 1 1.416-1.412z"></path></defs><g fill="none" fill-rule="evenodd"><mask id="dtransactionIcon" fill="#fff"><use xlink:href="#ctransactionIcon"></use></mask><use fill="#536e92" xlink:href="#ctransactionIcon"></use><g fill="none" mask="url(#dtransactionIcon)"><path d="M0 0h24v24H0z"></path></g></g></svg>
													<?php
											} ?>

										</div>
										<div>
											<h4 style="text-align: center; color: grey;">No Transaction in Last 7 Days</h4>
										</div>
									</div>
									<!-- table-responsive -->
								</div>
							</div>
							
						</div>
						<!--End row-->


						<div class="row">
							<div class="col-md-12 col-lg-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Recent Transaction Details</div>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="table-responsive">

											<table id="example" class="table table-striped table-bordered table-hover dataTables-example">
										<?php	if(!empty($transactions_Currday)){ ?>
												<thead>


													<tr>

													<th class="wd-15p">S.No</th>
							                        <th class="wd-15p">Txn<br>Type</th>
							                        <th class="wd-15p">Out Trade Number</th>
							                        <th class="wd-15p">Refund Org ID</th>                      
							                        <th class="wd-15p">Terminal ID</th>
							                        <th class="wd-15p">Status</th>
							                        <th class="wd-15p">Txn<br>Date</th>                
							                        <th class="wd-15p">Amt(LKR)</th>
							                        <th class="wd-15p">Amt(USD)</th>                
							                        
													</tr>
												</thead>
											   <?php 
											   $i=0;
												foreach($transactions_Currday as $tr) {
									            $i++;
									            $t_id = $tr["id_transaction_id"];
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

									           ?>
												<tbody>

												<?php	
												 if($tr['transaction_type'] == 'cb1' || $tr['transaction_type'] == 'cb2' || $tr['transaction_type'] == 'cb3') { ?>

												 	<tr  role="row">
							                            <td><?php echo $i;?></td>
							                            <td><?php echo $transaction_type;?></td>
							                            <td><?php echo $trans_out_trade_no_digits.$trans_out_trade_no_second_digits
							                            ;?></td>
							                            <td><?php echo $trans_partner_trans_id;?></td>
							                            <td><?php echo $tr["terminal_id"];?></td>                         
							                            <td><?php echo $sta;?></td>
							                            <td><?php echo $tr["trans_datetime"];?></td>
							                            <td><?php echo 'LKR '.$transaction_amount_LKR;?></td>     
							                            <td><?php echo 'USD '.$transaction_amount_USD;?></td>              
                            
                        							</tr>
									                 <?php  } elseif($currency=="USD") { ?>
									                        <tr role="row">
									                            <td><?php echo $i;?></td>
									                            <td><?php echo $transaction_type;?></td>
									                            <td><?php echo $trans_out_trade_no_digits.$trans_out_trade_no_second_digits
									                            ;?></td>
									                            <td><?php echo $trans_partner_trans_id;?></td>
									                            <td><?php echo $tr["terminal_id"];?></td>                         
									                            <td><?php echo $sta;?></td>
									                            <td><?php echo $tr["trans_datetime"];?></td>
									                            <td></td> 
									                            <td><?php echo $currency.' '.$transaction_amount;?></td>  
									                        </tr>                       
									                          
									           <?php  } else { ?>
									                          <tr role="row">
									                            <td><?php echo $i;?></td>
									                            <td><?php echo $transaction_type;?></td>
									                            <td><?php echo $trans_out_trade_no_digits.$trans_out_trade_no_second_digits
									                            ;?></td>
									                            <td><?php echo $trans_partner_trans_id;?></td>
									                            <td><?php echo $tr["terminal_id"];?></td>                         
									                            <td><?php echo $sta;?></td>
									                            <td><?php echo $tr["trans_datetime"];?></td>
									                            <td><?php echo $currency.' '.$transaction_amount;?></td> 
									                            <td></td>                         
									                            
									                        </tr>
												<?php } } } else {
													echo "Transaction Not Found";

												}	?>		
												</tbody>
												</table>
										
										</div>
									</div>
									<!-- table-wrapper -->
								</div>
								<!-- section-wrapper -->

							</div>
						</div>

					</div>
				</div><!-- end app-content-->
						<!-- High-Charts js-->
		
		
				
				<?php 
				include('layout/footer.php');
				require_once('inc_login.php');
				} 
					} else {
						echo '<h1>You are Unauthorized</h1>'; // echo "Hi1";
						header("location:/".SITEURLPATH);
					}
				} else {
					echo '<h1>You are Unauthorized</h1>'; // echo "Hi2";
					header("location:/".SITEURLPATH);
				}

				?>
				<script>
				/* ---hightchart7----*/
				var chart = Highcharts.chart('highchart7', {
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