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
							require_once('merchant_addupdate_api.php');
						
                    // $db->where('gp_status', "1");
                     $merchant_det = $db->get('merchants');
                    if($_POST) {
                        // echo "<pre>";
                        // print_r($_POST); exit;
                        // include_once ('api/alipaymerchantAPI.php');
                        // merchantaddupdatestatus($_POST, $_POST['pg_merchant_action']);
                        $db->where('mer_map_id', $_POST['pg_merchant_id']);
                        $lastid = $db->getone("merchants");

                        // echo $lastid;
                        // die();

                        $results = terminaladdupdatestatus($_POST, $_POST['pg_terminal_action'],$lastid['idmerchants']);
                        $results_enc = json_encode($results);
                        $results_dec = json_decode($results_enc);
                        
                        echo "<script>";

                        // echo "swal({title: 'Good job', text: '".$results_dec->ResponseDesc."', type: 'success'},
                        //        function(){ 
                        //         location.reload();
                        //     })";
                        echo "swal('".$results_dec->ResponseDesc."')";
                        echo "</script>";
                        
                        echo "<h2>If you Create Another Terminal below link Click</h2>";
                        echo "<br><br>";
                        echo "<a href='terminal_add.php?t=".urlencode($_GET['t'])."'>CREATE ANOTHER TERMINAL</a>";
                    } else {
                    ?>
                    <style>
                        label {
                            font-weight: bold;
                        }

                        .mrb-15 {
                            margin-bottom: 15px;
                        }#color{
                            color:red;
                        }
                    </style>
                    <div class="app-content">
					<div class="side-app leftmenu-icon">

                        <!--Page header-->
                        <div class="page-header">
                            <div class="page-leftheader">
                                <ol class="breadcrumb pl-0">
                                    <li class="breadcrumb-item"><a href="admindashboard.php?t=<?php echo urlencode($_GET['t']); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Terminal</li>
                                </ol>
                            </div>
                        </div>
                        <!--End Page header-->
						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-12">
								<form class="card" onsubmit="return terminal()" autocomplete="off" method="POST" name="myForm">
									<div class="card-header">
										<h3 class="card-title">ADD / CREATE TERMINAL</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
										<div class="row">
										<div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                    		<label class="form-label">Merchant ID <span id="color">*</span> </label>
                                    			<input list="pg_merchant_id_new"  name="pg_merchant_id" class="form-control" id="pg_merchant_id" placeholder="Please Select Merchant ID">
                                    		<datalist  name="pg_merchant_id" id="pg_merchant_id_new" >
                                        <option value="">Select</option>
	                                    <?php
	                                        foreach ($merchant_det as $key => $value) {
	                                                echo '<option value="'.$value['mer_map_id'].'" id="'.$value['mer_map_id'].'" >'.$value['merchant_name'].' - '.$value['mer_map_id'].'</option>';
	                                        }
	                                    ?>
                                    		</datalist >
                                		</div>
                                    </div>
                                <!-- <div class="col-sm-3  mrb-15" id="grabpay" style="display: none">
                                    <label>Grab_Terminal ID <span id="color">*</span> </label><BR>
                                    <input class="form-control" type="text" name="grab_terminal_id" id="grab_terminal_id">
                                </div> -->
                                <div class="col-sm-6 col-md-3">
                                	<div class="form-group">
                                    <label class="form-label">Terminal ID <span id="color">*</span> </label>
                                    <input class="form-control" type="text" name="pg_terminal_id" id="pg_terminal_id" placeholder="Please Enter Terminal ID....">
                                	</div>
                               	</div>
                                 <div class="col-sm-6 col-md-3">
                                 	<div class="form-group">
                                    <label class="form-label">Terminal Address <span id="color">*</span></label>
                                    <input class="form-control" type="text" name="pg_terminal_address" id="pg_terminal_address"placeholder="Please Enter Terminal Address...." >
                                	</div>
                                </div>
                                 <div class="col-sm-6 col-md-3">
                                 	<div class="form-group">
                                    <label class="form-label">Terminal State <span id="color">*</span></label>
                                    <input class="form-control" type="text" name="pg_terminal_state"id="pg_terminal_state" placeholder="Please Enter Terminal State....">
                                    </div>
                                </div>
                                 <div class="col-sm-6 col-md-3">
                                 	<div class="form-group">
                                    <label class="form-label">Terminal City<span id="color">*</span> </label>
                                    <input class="form-control" type="text" name="pg_terminal_city" id="pg_terminal_city" placeholder="Please Enter Terminal City....">
                                	</div>
                                </div>

                                 <div class="col-sm-6 col-md-3">
                                 	<div class="form-group">
                                    <label class="form-label">Terminal Pincode <span id="color">*</span></label>
                                    <input class="form-control" type="text" name="pg_terminal_pincode" id="pg_terminal_pincode" placeholder="Please Enter Terminal Pincode....">
                                	</div>
                                </div>
                                
                                <div class="col-sm-3  mrb-15">
                                	<div class="form-group">
                                    <label class="form-label">Terminal Status <span id="color">*</span></label>
                                    <select name="pg_terminal_status" class="form-control" id="pg_terminal_status" >
                                        <option value="0" selected="selected">Status</option>
                                        <option value="1">Active</option>
                                        <option value="2">In-Active</option>
                                    </select>
                                    <input class="form-control" type="hidden" name="pg_terminal_action" id="pg_terminal_action" value="1">
                                	</div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                            <input type="submit" id="btnSubmit" class="btn btn-primary" value="Submit">
								</div>
							</div>
                        </div>
                    </form>
                </div>
				<?php } ?>
			</div>
        </div>
    </div>
<?php } ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

	function terminal() {
    
    var pg_merchant_id = document.getElementById('pg_merchant_id').value;    

    var obj = $("#pg_merchant_id_new").find("option[value='" + pg_merchant_id + "']").attr('value');
    var pg_terminal_id =document.getElementById('pg_terminal_id').value;
    var pg_terminal_address = document.getElementById('pg_terminal_address').value;
    var pg_terminal_state   = document.getElementById('pg_terminal_state').value;
    var pg_terminal_city=document.getElementById('pg_terminal_city').value;
    var pg_terminal_pincode=document.getElementById('pg_terminal_pincode').value;
    

    var e = document.getElementById("pg_terminal_status");
    var pg_terminal_status = e.options[e.selectedIndex].value;

    
//var e1 = document.getElementById("pg_terminal_action");
    // var pg_terminal_action = e1.options[e1.selectedIndex].value;

    if(obj ==="") {
         // document.getElementById('pg_merchant_id').focus();
        swal('Please Select the Merchant Id');
        return false;
    }
    if (typeof obj==="undefined") {
        swal('Please Select the  Vaild Merchant Id');
        return false;
    }

    if(pg_terminal_id =="") {
        
        swal('Please Enter the Terminal Id');
        return false;
    }

if(pg_terminal_address =="") {
        swal('Please Enter the Terminal Address');
        return false;
    }

    if(pg_terminal_state =="") {
        swal('Please Enter the Terminal State');
        return false;
    }
    
    if(!(/^[a-zA-Z/s0-9]+$/.test(document.getElementById('pg_terminal_state').value))) {
        swal(" Terminal State contains only Alpha Numerical!");
        return false;
    }

    if(pg_terminal_city =="") {
        swal('Please Enter the Terminal City');
        return false;
    }
    if(!(/^[a-zA-Z/s0-9]+$/.test(document.getElementById('pg_terminal_city').value))) {
        swal(" Terminal City contains only Alpha Numerical!");
        return false;
    }
    if(pg_terminal_pincode == "") {
        swal('Please Enter the Terminal pincode');
        return false;
    }
    if(!(/^[0-9]+$/.test(document.getElementById('pg_terminal_pincode').value))) {
        swal(" Terminal Pincode No contains only number!");
        return false;
    }

    if(pg_terminal_status == 0) {
        swal('Please Enter the Terminal Status');
        return false;
    }

    }
$('#pg_terminal_id').on('change', function() {
    
    var mer_id = $('#pg_merchant_id').val();
    var ter_id = $('#pg_terminal_id').val();
    console.log(mer_id+"=>"+ter_id);
    $.ajax({
        url: "php/inc_reportsearch1.php",
        data: {
            'merchant_id' : mer_id,
            'terminal_id' : ter_id
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            
            console.log(data.result);
            if(data.result == true) {
                swal('Terminal ID '+ter_id+' Already Exists');
                $("#pg_terminal_id").val("");
            }
        },
        error: function(data){
            
        }
    });
});


</script>


<
<?php
include('layout/footer.php');
				//} 
					} else {
						echo '<h1>You are Unauthorized</h1>'; // echo "Hi1";
						header("location:/".SITEURLPATH);
					}
				} else {
					echo '<h1>You are Unauthorized</h1>'; // echo "Hi2";
					header("location:/".SITEURLPATH);
				}

				?>
				