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
							} else {
					
							include('layout/header.php');
							
							include('layout/sidemenu.php');

			
				?>

			    <body>
			    	
			   <!--  <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet" />
    			<link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" />

    			<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script> -->


    			<!-- <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>

				<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
				<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> -->
				
				<script type="https://code.jquery.com/jquery-3.4.1.js"></script>
			    <style type="text/css">
				.daterangepicker.show-calendar .drp-buttons {
					display: none !important;
				}
				#example_wrapper #example_filter{
					display: none!important;
				}
				</style>
				<!--aside closed-->

				<?php

				//$current_date = date('Y-m-d h:i:s');
				//$previous_7_Date = date('Y-m-d h:i:s',strtotime('-7  ago'));
				// echo $current_date;
				// echo $previous_7_Date;
				// die();
				$sql_query = "SELECT * FROM audits WHERE created_at > DATE(NOW()) - INTERVAL 7 DAY ORDER BY created_at DESC";
				
				$audit = $db->rawQuery($sql_query);
                // if(in_array($userDet_name, ['digitaladmin', 'digitalmerchadmin', 'saintangeladmin'])) {
                //   $db->where("mer_map_id","E01100000000012");
                //   $db->orderBy("mer_map_id","Asc");
                //   $usersofuser = $db->get('merchants');
                // } else {
                //   $db->orderBy("mer_map_id","Asc");
                //   $usersofuser = $db->get('merchants');
                // }
                ?>

				<div class="app-content">
					<div class="side-app leftmenu-icon">
				
						<!--Page header-->
						<div class="page-header">
							<div class="page-leftheader">
								<ol class="breadcrumb pl-0">
									<li class="breadcrumb-item"><a href="#">Home</a></li>
									
									<li class="breadcrumb-item active" aria-current="page">Audit</li>
								</ol>
							</div>
						</div>
						<!--End Page header-->
					


						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header ">
										<h3 class="card-title ">Audit Report</h3>
										<!-- <div class="card-options">
											<button id="add__new__list" type="button" class="btn btn-sm btn-success " data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-plus"></i> Add a new Project</button>
										</div> -->
									</div>
									<div class="card-body">
										<div class="table-responsive" style="text-align:left;" id="already">
					                      <table id="example" class="table table-striped table-bordered table-hover   dataTables-example">
					                        <thead >
					                          <tr>

					                            <th><span class="notice"><b>User_ID</b></span></th>

					                            <th><b>Event</b></th>

					                            <th><b>Url</b></th>

					                            <th><b>Ip Address </b></th>

					                            <th><b>User Agent</b></th>

					                            <th><b>Created At</b></th>
					                                      
					                           </tr>
					                        </thead>
					                        <tbody>

					                            <?php
					                            foreach($audit as $row0){ 

					                                   if($row0['user_id'])  {

					                                     ?>

					                              <tr  data-level="1" id="level_1_<?php echo $row0['user_id']; ?>" class="agentuser">
					                               <td class="data"> <?php echo $row0['user_id']; ?></td>

					                               <td class="data"> <?php echo $row0['event']; ?> </td>

					                               <td class="data"> <?php $data = explode('?',$row0['url']);
					                               echo $data[0];
					                                ?> </td>

					                               <td class="data">  <?php echo $row0['ip_address']; ?></td>
					                                                         
					                               <td class="data"><?php echo $row0['user_agent']; ?></td>

					                               <td class="data"> <?php echo $row0['created_at']; ?></td>  
					                                
					                            
					                                 

					                                 
					                                
					                                
					                                
					                              <!--  <button type="button" class = "btn btn-primary btn-xs" value="Details" onclick="window.location.href='viewagent.php?merchantid=<?php //echo $row0['idmerchants']; ?>&t=<?php // echo urlencode($_GET['t']) ?>'"  /><i class="fa fa-eye"></i></button> -->

					                              <!--  <button type="button" class = "btn btn-primary btn-xs" value="Edit" onclick="window.location.href='merchant_Editdetails.php?m_id=<?php //echo base64_encode( json_encode($row0['mer_map_id']) ) ?>&t=<?php // echo urlencode($_GET['t']) ?>'" /><i class="fe fe-edit"></i></button> -->

					                                <!--  <button type="button" class = "btn btn-primary btn-xs" class="test" data-toggle = "modal" id="<?php // echo $row0['merchant_name']; ?>" onclick="showdetails(this);" data-target="#confirm-submit" value="<?php// echo $row0['mer_map_id']; ?>"<?php // if($active == "Active") { ?> disabled <?php // } ?>   ><i class="fe fe-trash"></i> </button> <?php //if($active == "Active") //{ ?> <div class="help"><a class='help-button'   data-toggle="tooltip" data-placement="bottom" title=" You active Merchant so You will not be able to Delete from Merchant Account.">[?]</a></div> <?php // } ?> --> 
													
													        
					                              </tr>
					                             <?php } } ?> 
					                        </tbody>
					                      </table>
					                    </div>
									</div>
								</div>
							</div>
						</div>
						<!-- Row -->
						<!-- <div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header ">
										<h3 class="card-title ">Results</h3>
										
										//</div> 
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a id="exportlink_date" href="php/inc_reportadmin.php?date=<?php //echo date('m/d/Y');?>" target="_bank"><i class="fa fa-file-excel-o"></i> Export</a>
											
											
										</div>
									</div>

									<div class="card-body">
										<div class="table-responsive">
										
										<div id="filterresult"></div>
									</div>
								</div>
								</div>
							</div>
						</div> -->
						<!-- End row -->

					
				   </div><!-- end app-content-->
			</div>

			<!--Footer-->
			
			<!-- End Footer-->

		</div>

			    </body>
			<!-- 	<div class="app-content">
					<div class="side-app leftmenu-icon">
							<div class="row wrapper border-bottom white-bg page-heading">
					<div class="col-lg-10">
						<h2>Reports</h2>
						
					</div>
				</div> -->

		
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

				?>
				<script>
				/* ---hightchart7----*/
				
				</script>

				<!-- <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

				<script type="text/javascript" src="js/plugins/tabelizer/jquery.tabelizer.js"></script> -->

				<!-- <link rel="stylesheet" href="css/plugins/tabelizer/tabelizer.css"> -->

			
				<!-- Data picker -->

				<!-- <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script> -->

				<!-- <script src="js/plugins/dataTables/jquery.dataTables.js"></script> -->
				<!-- <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script> -->
				<!-- <script src="js/plugins/dataTables/dataTables.responsive.js"></script>
				<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> -->
				<!-- script src="<?php //echo $public_path; ?>/assets/plugins/datatable/jquery.dataTables.min.js"></script>
				<script src="<?php //echo $public_path; ?>/assets/plugins/datatable/dataTables.bootstrap4.min.js"></script>
				<script src="<?php //echo $public_path; ?>/assets/js/datatables.js"></script> -->
<!-- 
				<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>

		        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
		        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> -->

				<script type="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


				<script type="text/javascript">

					 function codeAddress() {
         
				            $('.dataTables-example').dataTable({
				              destroy: true,
				              responsive: true,
				              "dom": 'T<"clear">lfrtip',
				              "tableTools": {
				                "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
				              }
				            });
				            $('input:checkbox').change(function(){ 
				              if($(this).attr('id') == 'selectall') 
				              {
				                jqCheckAll2( this.id);
				              }
				            });

				            var s = '';
				            var num = isNaN(parseInt(s)) ? 0 : parseInt(s);
				        }
				      window.onload = codeAddress;
				      
					$(document).ready(function(){

							$("#exportlink_date").hide();


						    /**** Daily Summary Report for selecting date from picker ****/
							$(".reportsearch").click(function () {

								var selected_date = $('#date2').val();
						        var period_start_date = selected_date.slice(0, 16);
						        var period_end_date = selected_date.slice(19, 36);

						        // var selected_merchantid = $('#merchants').val();

						        $( "#filterresult" ).html('Bulding the report. Please wait..');
						        var postData = $("#reports_form").serializeArray();
						        var queryString = $.param(postData);
						        console.log(postData);

						        $.ajax({
						            method: "POST",
						            url: "php/inc_reportsearch1.php",
						            data: postData
						        })
						        .done(function( msg ) {
						            $("#filterresult").html(msg);
						            console.log(msg.slice(0,21));

						            if(msg.slice(0,21) == 'No Transactions Found') {
										$("#exportlink_date").hide();
									} else {
										$("#exportlink_date").show();
									}

									$('#example').dataTable({
										responsive:true
						              
						            });


						           

									// var exportLink = "php/inc_transsearch.php?start_d="+period_start_date+"&end_d="+period_end_date;
									// // var exportLink = "php/inc_transsearch.php?"+queryString;
									// $("#exportlink_date").attr("href", exportLink);

									// if(msg.slice(0,21) == 'No Transactions Found') {
									// $("#exportlink_date").hide();
									// } else {
									// $("#exportlink_date").show();
									// }
									

						        		var arrStr = JSON.stringify(postData);
						        	// $("#exportlink_date").attr("href", "php/inc_reportadmin.php?start_d="+period_start_date+"&end_d="+period_end_date);
						     			$('#exportlink_date').attr({ href: 'php/inc_reportadmin.php?array=' + arrStr });
									});
						        	

						        });

						 
						    if($("#merchants").val()!=''){
								$.ajax({
									type: 'POST',
									// contentType: 'application/json',
									// dataType: 'json',
									url: 'php/inc_reportsearch1.php',
									data: JSON.stringify({'m_id': $("#merchants").val(), 'type':'getmerchant'})
								})
								.done(function( msg ) {
									// console.log(msg);
									$("#terminal_id").html(msg);
								});
							} else {
								// alert("Hi");
								var msg = '<option value="">-- Terminal ID --</option>';
								$("#terminal_id").html(msg);
							}

							$("#merchants").change(function () {
								// alert($(this).val());
								if($(this).val()){
									$.ajax({
										type: 'POST',
										// contentType: 'application/json',
										// dataType: 'json',
										url: 'php/inc_reportsearch1.php',
										data: JSON.stringify({'m_id': $(this).val(), 'type':'getmerchant'})
									})
									.done(function( msg ) {
										console.log(msg);
										$("#terminal_id").html(msg);
									});
								} else {
									// alert("Hi");
									var msg = '<option value="">-- Terminal ID --</option>';
									$("#terminal_id").html(msg);
								}
							});

						});

						// $(document).ready(function () {
						//   $('#example').DataTable();
						//   $('.dataTables_length').addClass('bs-select');
						// });
					</script>

					<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
					<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
					<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

					<script type="text/javascript">
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