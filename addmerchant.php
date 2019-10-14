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
                            if($_POST) {
                                // echo "<pre>";
                                // print_r($_POST); 
                                // exit;
                                // require_once('api/alipaymerchantAPI.php');
                                // $results = merchantandterminalchecking($_POST);
                                $results = merchantaddupdatestatus($_POST, $_POST['pg_merchant_action'],'');
                                $results_enc = json_encode($results);
                                $results_dec = json_decode($results_enc);
                                echo $results_dec->ResponseDesc;
                                echo "<br><br>";
                                echo "<a href='merchant_add.php?t=".urlencode($_GET['t'])."'>CREATE ANOTHER MERCHANT</a>";
                            } else {
                            ?>
                            <style>
                                label {
                                    font-weight: bold;
                                }

                                .mrb-15 {
                                    margin-bottom: 15px;
                                }
                            </style>

				<div class="app-content">
					<div class="side-app leftmenu-icon">

                        <!--Page header-->
                        <div class="page-header">
                            <div class="page-leftheader">
                                <ol class="breadcrumb pl-0">
                                    <li class="breadcrumb-item"><a href="admindashboard.php?t=<?php echo urlencode($_GET['t']); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Merchant</li>
                                </ol>
                            </div>
                        </div>
                        <!--End Page header-->
						<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-12">
								<form class="card" onsubmit="return merchant()" method="POST" accept="#">
									<div class="card-header">
										<h3 class="card-title">ADD / CREATE MERCHANT</h3>
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
													<label class="form-label">Merchant ID</label>
													<input class="form-control" type="text" name="pg_merchant_id" id="pg_merchant_id" placeholder="Please Enter the Merchant ID...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Name</label>
													<input class="form-control" type="text" name="pg_merchant_name" id="pg_merchant_name" placeholder="Please Enter the Merchant Name...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Address1</label>
													<input class="form-control" type="text" name="pg_merchant_address1" id="pg_merchant_address1" placeholder="Please Enter the Merchant Address1...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Address2</label>
													<input class="form-control" type="text" name="pg_merchant_address2" id="pg_merchant_address2" placeholder="Please Enter the Merchant Address2...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant City</label>
													<input class="form-control" type="text" name="pg_merchant_city" id="pg_merchant_city" placeholder="Please Enter the Merchant City...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant State</label>
													<input class="form-control" type="text" name="pg_merchant_state" id="pg_merchant_state" placeholder="Please Enter the Merchant State...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Country</label>
													<input class="form-control" type="text" name="pg_merchant_country" id="pg_merchant_country" placeholder="Please Enter the Merchant Country...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Postalcode</label>
													<input class="form-control" type="text" name="pg_merchant_postalcode" id="pg_merchant_postalcode" placeholder="Please Enter the Merchant Postalcode...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Phone</label>
													<input class="form-control" type="text" name="pg_merchant_phone" id="pg_merchant_phone" placeholder="Please Enter the Merchant Phone...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Email</label>
													<input class="form-control" type="text" name="pg_merchant_email" id="pg_merchant_email" placeholder="Please Enter the Merchant Email...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant MCC</label>
													<input class="form-control" type="text" name="pg_merchant_mcc" id="pg_merchant_mcc" placeholder="Please Enter the Merchant Mcc...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant IFSCcode</label>
													<input class="form-control" type="text" name="pg_ifsccode" id="pg_ifsccode" placeholder="Please Enter the Merchant Ifsc_code...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant AccountNo</label>
													<input class="form-control" type="text" name="pg_accountno" id="pg_accountno" placeholder="Please Enter the Merchant Accountno...">
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Start Date</label>
													<input class="form-control" name="pg_merchant_start_date" id="pg_merchant_start_date" type="text" value="<?php echo date('m/d/Y'); ?>">
												</div>
											</div>
											
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Currency</label>
													<select name="pg_merchant_currency" class="form-control" id="pg_merchant_currency">
                                                <option value="0">Select</option>
                                                <option value="USD">USD</option>
                                                <option value="LKR">LKR</option>
                                            </select>
												</div>
											</div>
											<div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="form-label">Merchant Status</label>
													<select name="pg_merchant_status" class="form-control" id="pg_merchant_status">
                                                <option value="0">Status</option>
                                                <option value="1">Active</option>
                                                <option value="2">In-Active</option>
                                            </select>
                                            <input class="form-control" type="hidden" name="pg_merchant_action" id="pg_merchant_action" value="1">
												</div>
											</div>
											<div class="col-sm-4 mrb-15">
                                            <label>Merchant Permission</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="Permission1" value="1" checked>Purchase</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="Permission2" value="1" checked>Cancel</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="Permission3" value="1" checked>Query</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="Permission4" value="1" checked>Refund</label>
                                        </div>
										</div>
									</div>
									<div class="card-footer text-right">
										<input type="submit" id="btnSubmit" class="btn btn-primary" value="Submit">
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

						<!-- End Row-->

<?php

}

?>	
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
				<script>

					$("#pg_merchant_id").change(function() {

   var mer_id = $('#pg_merchant_id').val();

    
    $.ajax({
        url: "php/inc_reportsearch1.php",
        data: {
            'merchant_id' : mer_id
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            
            console.log(data);
            if(data.result == true) {
                swal('Merchant ID '+mer_id+' Already Exists');
                $("#pg_merchant_id").val("");
               
            } 
           
        },
        error: function(data){
            //error
        }
    });
});


function merchant() {
    var pg_merchant_id=document.getElementById('pg_merchant_id').value;
    var pg_merchant_name=document.getElementById('pg_merchant_name').value;
    var pg_merchant_start_date=document.getElementById('pg_merchant_start_date').value;
    var pg_merchant_address1=document.getElementById('pg_merchant_address1').value;
    var pg_merchant_city=document.getElementById('pg_merchant_city').value;
    var pg_merchant_state=document.getElementById('pg_merchant_state').value;
    var pg_merchant_country=document.getElementById('pg_merchant_country').value;
    var pg_merchant_postalcode=document.getElementById('pg_merchant_postalcode').value;
    var pg_merchant_phone=document.getElementById('pg_merchant_phone').value;
    var pg_merchant_email=document.getElementById('pg_merchant_email').value;
    var pg_merchant_mcc=document.getElementById('pg_merchant_mcc').value;
    var pg_ifsccode=document.getElementById('pg_ifsccode').value;
    var pg_accountno=document.getElementById('pg_accountno').value;
    
    var e = document.getElementById("pg_merchant_status");
    var pg_merchant_status = e.options[e.selectedIndex].value;
    

    var e2 = document.getElementById("pg_merchant_currency");
    var pg_merchant_currency=e2.options[e2.selectedIndex].value;
    
    if(pg_merchant_id == "") {
            swal('Please Enter the Merchant ID');
            return false;
    }

    if(pg_merchant_name == "") {
            swal('Please Enter the Merchant Name');
            return false;
    }   
    if(pg_merchant_start_date == "") {
            swal('Please Enter the Merchant Start Date');
            return false;
    }
    if(pg_merchant_address1 == "") {
            swal('Please Enter the Merchant Address1');
            return false;
    }
    if(pg_merchant_city == "") {
            swal('Please Enter the Merchant City');
            return false;
    } 

    if(!(/^[a-zA-Z]+$/.test(document.getElementById('pg_merchant_city').value))) {
        swal(" Merchant City contains only Alphabet!");
        return false;
    } 
    
    if(pg_merchant_state == "") {
            swal('Please Enter the Merchant State');
            return false;
    }
    if(!(/^[a-zA-Z]+$/.test(document.getElementById('pg_merchant_state').value))) {
        swal(" Merchant State contains only Alphabet!");
        return false;
    }   
    if(pg_merchant_country == "") {
            swal('Please Enter the Merchant Country');
            return false;
    }
    if(!(/^[a-zA-Z]+$/.test(document.getElementById('pg_merchant_country').value))) {
        swal(" Merchant Country contains only Alphabet!");
        return false;
    }  
    if(pg_merchant_postalcode == "") {
            swal('Please Enter the Merchant Postal Code');
            return false;
    }  
    
    if(!(/^[0-9]+$/.test(document.getElementById('pg_merchant_postalcode').value))) {
        swal(" Merchant Postal code contains only number!");
        return false;
    }

    if(pg_merchant_phone == "") {
        swal('Please Enter the  Merchant Phone');
        return false;
    }

    if(!(/^[0-9]+$/.test(document.getElementById('pg_merchant_phone').value))) {
        swal(" Merchant Phone contains only number!");
        return false;
    }

    if(pg_merchant_phone.length != 10) {
        swal(' Merchant Phone should be of length 10');
        return false;
    }  
    if(pg_merchant_email == "")  {
        swal('Please Enter the Merchant Email');
        return false;
    }  
    
    if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('pg_merchant_email').value))) {
        swal("invalid Merchant Mail format!");
        return false;
    }

    if(pg_merchant_mcc == "")  {
        swal('Please Enter the Merchant Mcc');
        return false;
    }

    if (!(/^[0-9]+$/.test(document.getElementById('pg_merchant_mcc').value)))   {
        swal(" Merchant Mcc contains only number!");
        return false;
    }

    if(pg_ifsccode == "")   {
            swal('Please Enter the Merchant Ifsc Code');
            return false;
    }   
    if( /[^a-zA-Z0-9]/.test( document.getElementById('pg_ifsccode').value)  ) {
      swal(" Merchant ifsc code contains only number and Alphabet!");
       return false;
    }
    if(pg_accountno == "")  {
            swal('Please Enter the Merchant Account No ');
            return false;
    }
    if(!(/^[0-9]+$/.test(document.getElementById('pg_accountno').value)))  {
        swal(" Merchant Account No  contains only number!");
        return false;
    }
   
    if(pg_merchant_currency == 0)  {
        swal('Please Select the Merchant Currency');
        return false;
    }


    if(pg_merchant_status == 0)  {
        swal('Please Enter the Merchant Status');
        return false;
    } 
 
    
     document.getElementById('btnSubmit').style.visibility = 'hidden';


}

</script>



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
			