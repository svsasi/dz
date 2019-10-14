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

					

				?>
				<div class="app-content">
					<div class="side-app leftmenu-icon">
						<div class="row">

							                        <!--Page header-->
                        <div class="page-header">
                            <div class="page-leftheader">
                                <ol class="breadcrumb pl-0">
                                    <li class="breadcrumb-item"><a href="admindashboard.php?t=<?php echo urlencode($_GET['t']); ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Currency Conversion</li>
                                </ol>
                            </div>
                        </div>
                        <!--End Page header-->
            <style type="text/css">
            /*#myModal {
              -webkit-transform: translate3d(0, 0, 0);
            }*/
            .table-wrapper {
                width: 929px;
                margin: -4px auto;
                background: #fff;
                padding: 9px;
                position: relative;
                box-shadow: 0 1px 1px rgba(0,0,0,.05);
                right: 36px;
               /* //overflow: auto;*/
            }
            .table-title {
                padding-bottom: 10px;
                margin: 0 0 10px;
            }
            .table-title h2 {
                margin: 6px 0 0;
                font-size: 22px;
            }
            .table-title .add-new {
                float: right;
                height: 30px;
                font-weight: bold;
                font-size: 12px;
                text-shadow: none;
                min-width: 100px;
                border-radius: 50px;
                line-height: 13px;
            }
            .table-title .add-new i {
                margin-right: 4px;
            }
            table.table {
                table-layout: fixed;
            }
            table.table tr th, table.table tr td {
                border-color: #e9e9e9;
            }
            table.table th i {
                font-size: 13px;
                margin: 0 5px;
                cursor: pointer;
            }
            table.table th:last-child {
                width: 100px;
            }
            table.table td a {
                cursor: pointer;
                display: inline-block;
                margin: 0 5px;
                min-width: 24px;
            }    
            table.table td a.add {
                color: #27C46B;
            }
            table.table td a.edit {
                color: #FFC107;
            }
            table.table td a.delete {
                color: #E34724;
            }
            table.table td i {
                font-size: 19px;
            }
            table.table td a.add i {
                font-size: 24px;
                margin-right: -1px;
                position: relative;
                top: 3px;
            }    
            table.table .form-control {
                height: 32px;
                line-height: 32px;
                box-shadow: none;
                border-radius: 2px;
            }
            table.table .form-control.error {
                border-color: #f50000;
            }
            table.table td .add {
                display: none;
            }
            </style>

             <?php

            	$currency_details = $db->get('currency_convert');
             
            ?>
            			<div class="row">
							<div class="col-lg-12 col-md-12">
								
						   <div class="card">

							<form  method="post"  onsubmit="return merchant()" autocomplete="off">
									<div class="card-header">
										<h3 class="card-title">Curency Conversion</h3>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card1-collapse"><i class="fe fe-chevron-up"></i></a>
											<!-- <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a> -->
										</div>
									</div>
									<div class="card-body ">
										<div class="form-group ">
											<label class="form-label">Base Currency</label>

											 <select name="basecurrency" id="basecurrency" class="form-control select2 custom-select" data-placeholder="Choose one">
                                                <option  label="Choose one" value="0">Select</option>
                                               <!--  <option value="USD">USD</option> -->
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                                <option value="LKR">LKR</option>
                                            </select>
											<!-- <select class="form-control select2 custom-select" data-placeholder="Choose one">
												<option label="Choose one">
												</option>
												<option value="1">Chuck Testa</option>
												<option value="2">Sage Cattabriga-Alosa</option>
												<option value="3">Nikola Tesla</option>
												<option value="4">Cattabriga-Alosa</option>
												<option value="5">Nikola Alosa</option>
											</select> -->
										</div>
										<div class="form-group">
											<label class="form-label">Conversion Currency</label>
											
                                            <select name="exchangecurrency" id="exchangecurrency" class="form-control select2" data-placeholder="Choose one">
                                                <option  label="Choose one" value="0">Select</option>
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                                 <option value="LKR">LKR</option>
                                                 <option value="PHP">PHP</option>
                                               <!--  <option value="USD">USD</option> -->
                                               <!--  <option value="USD">USD</option>
                                                <option value="EUR">EUR</option> -->
                                               <!--  <option value="LKR">LKR</option> -->
                                            </select>
										</div>

										<div class="form-group">

											<input type="submit" <?php if(!empty($currency_details)) { ?> disabled <?php } ?> class="btn btn-primary" value="Submit">
										</div>
										
										
									</div>
								</form>
							</div>
					     	</div>
							<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Currency List </div>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											
										</div>
									</div>
									<div class="card-body">
										 <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <!-- <th>S.No</th> -->
                                        <th>Base CCY </th>
                                        <th>Stlmt CCY</th>
                                        <th>XCHG Rate Base CCY</th>
                                        <th>Base CCY Value</th>
                                        <th>XCHG Rate Stlmt CCY</th>
                                        <th>Mark-up (%)</th>
                                        <th>Marked-up XCHG rate-Stlmt CCY</th>
                                        <th>Last Updated Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php 
                                     
                                    foreach ($currency_details as $data) { 
                                    if (empty($currency_details)) {
                                         continue;
                                    } else {
                                        $xchng_stl_ccy = $data['currency_value'] / $data['crncy_to_value'];

                                        $markedUp_xchng_calc = ($xchng_stl_ccy * $data['cbp_mer_percent']) / 100;
                                        $markedUp_xchng_value = $xchng_stl_ccy + $markedUp_xchng_calc;

                                        ?>

                                            <tr>
                                                <td><?php echo $data['crncy_from']; ?></td>
                                                <td class="id"><?php echo $data['crncy_to'];  ?></td>
                                                <td><?php echo $data['crncy_to_value']; ?></td>
                                                <td><?php echo $data['currency_value']; ?></td>
                                                <td><?php echo number_format($data['crncy_from_value'],8);?></td>
                                                
                                                <td><?php echo $data['cbp_mer_percent']; ?></td>
                                                <td><?php echo number_format($data['crncy_markup_xchg_rate'],8);; ?></td>
                                                <td><?php echo $data['updated_date']; ?></td>

                                                <td>
                                                     <a class="add" title="modify" data-toggle="tooltip"  data-id="<?php echo $data['cid']; ?>"><i class="material-icons">&#xE03B;</i></a>
                                                    <a class="edit" title="Edit" data-toggle="tooltip" data-id="<?php echo $data['cid']; ?>"><i class="fe fe-edit"></i></a>
                                                    <!-- <a class="undo" title="undo" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a> -->
                                                    <a class="delete" title="Delete" data-toggle="tooltip" data-id="<?php echo $data['cid']; ?>"  class="btn btn-icon btn-primary btn-danger"><i class="fe fe-trash"></i></a>
                                                   <!--  <button type="button" class="btn btn-icon btn-primary btn-danger"><i class="fe fe-trash"></i></button> -->
                                                </td>
                                            </tr>
                                        <?php } } ?>     
                                </tbody>
                            </table>
									</div>
								</div>
							</div>
							
						</div>
       
</div>
					</div>
				</div><!-- end app-content-->
						<!-- High-Charts js-->
			

		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
		
				
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

				<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
				<script>


	$(document).ready(function(){ 
	
	$(document).ready(function(){

    // Add row on add button click
    $(document).on("click", ".add", function(){
        var empty = false;
        var input = $(this).parents("tr").find('input[type="text"]');
       //alert(input);
        var res ='' ;
        var id = $(this).data("id");
        var currency_value;
        var currency_percentage;
        // alert($(this).val());
        // return false;

        var count = 0 ;
        input.each(function(){
            //alert($(this).val());

        if (count==2) {
            currency_value = $(this).val();
        } else if(count ==4) {
            exc_rate_Stlmt_CCY = $(this).val();
        } else if(count ==5) {
            currency_percentage = $(this).val();
        } else if(count ==6) {
            Marked_up_XCHG_rate_Stlmt_CCY = $(this).val();
        } else {
            //alert('no');
        }
        //alert($(this).val());
        count ++;
            
        });
        //alert(currency_value);
        //alert(currency_percentage);
        //break;
        //var output = arr.split(/[, ]+/).pop();

        //alert(output);
        //var fields = res.split(',');
        // var Currency_from = fields[1];
        // var Currency_to = fields[2];
        // var Currency_value = fields[3];
        // var currency_value = fields[3];
        // var Currency_to = fields[4];
        // var currency_percentage = fields[6];
        // alert(currency_value);
        // alert(Currency_to);
        // alert(currency_percentage);
        if (currency_value <= 0 || currency_value=='' ) {
            // Do Something
            alert('Exchange Rate should be greater than Zero');
            return false;
        }

       if (currency_percentage <= 0 || currency_percentage=='' ) {
            // Do Something
            alert('Mark-up should be greater than Zero');
            return false;
        }
        //$('#exampleModal').modal('show'); 
        // $(window).on('shown.bs.modal', function() { 
        //     $('#exampleModal').modal('show');
        //     alert('shown');
        // });
        // alert(crncy_from_value);
        // alert(Currency_to);
        // alert(currency_percentage);
        // var currency_percentage = fields[5];
        //alert(currency_percentage);
        // var currency_percentage = currency_percentage_temp.split('%');
        // alert(currency_percentage[0]);

        // if (exc_rate_Stlmt_CCY<=0&& Marked_up_XCHG_rate_Stlmt_CCY<=0) {



        var data = {'currency_req':'view','cid':id,'crncy_to_value':exc_rate_Stlmt_CCY,'currency_value':currency_value,'cbp_mer_percent':currency_percentage}; 

        //alert(id);

        //alert(data);

        $.ajax({
            method: "POST",
            url: "currency_detail.php",
            data: data
        })
        .done(function( msg ) {
            //alert(msg);
            value = JSON.parse(msg);
             exchange_rate_settlemet =  value.xchng_stl_ccy;
            Marked_up_rate_settlemet =  value.markedUp_xchng_value;

        //     var str="Task1Name          :"+    +"\n" +
        // "Task2NameLonger    :    Failed   :   statusCode\n";
             swal({
                title: "Do You want to change value",
                //allowOutsideClick: "true",
                //showLoaderOnConfirm: true, 
                text: "Exchange Rate Base Currency : "+currency_value+"\n"+"Mark-up Percentage: "+currency_percentage+"\n\n"+"Old Exchange_rate_settlemet : "+exc_rate_Stlmt_CCY+"\n"+
                " Old Marked_up_rate_settlemet : "+Marked_up_XCHG_rate_Stlmt_CCY+"\n\n"+  "New Exchange_rate_settlemet : "+exchange_rate_settlemet+"\n"+ "New Marked_up_rate_settlemet :" +Marked_up_rate_settlemet ,
                //icon: "warning",

                buttons: {
                    confirm : {text:'Ok',className:'sweet-warning'},
                    cancel : 'Cancel'
                },
            }).then((will)=>{
                if(will){
                    //alert('ok');
                   // alert('ok');
                  //var id = $(this).data("id");

                  //var data1 = {'currency_req':'edit','cid':id,'crncy_to_value':exc_rate_Stlmt_CCY,'currency_value':currency_value,'cbp_mer_percent':currency_percentage}; 
                   var data1 = {'currency_req':'edit','cid':id,'crncy_to_value':currency_value,'cbp_mer_percent':currency_percentage}; 
                        //alert(id);
                    $.ajax({
                        method: "POST",
                        url: "currency_detail.php",
                        data: data1
                    })
                    .done(function( msg ) {
                  //alert(msg);
                  location.reload();
                    
                    });
                        // $(".onoffswitch-checkbox").prop('checked',false);
                } else {
                    //swal("Cancelled", "Your not Refresh is safe :)", "error");
                    $("#all_petugas").click();
                     //alert('fail');
                     location.reload();
                }

           // location.reload();
        });
        
        });

     // }
        $(this).parents("tr").find(".error").first().focus();
        if(!empty){
            input.each(function(){
                $(this).parent("td").html($(this).val());
            });
        }         
            $(this).parents("tr").find(".add, .edit").toggle();
            $(".add-new").removeAttr("disabled");
        // }       
    });
    // Edit row on edit button click
    $(document).on("click", ".edit", function(){

            var id=0;        
            $(this).parents("tr").find("td:not(:last-child)").each(function(){

            //alert(id);
           
            if (id==2) {
                //alert('2')
                $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id="base_exchange_rate" />');
            } 
            else if (id==3) {
                $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id="base_currency_value" readonly />');
                //alert('3')
            } else if (id==5) {
                $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id="markup_value" name="markup_value" />');
                //alert('3')
            } else if(id==4){
                $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id="exchange_rate_settlemet"  readonly/>');
           }  else if(id == 6) {
                    $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id="markedup-exchange_rate_settlemet"  readonly/>');
           } else {
                 $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '" id=""  readonly/>');
           }
            id++;
            
    });     
        $(this).parents("tr").find(".add, .edit").toggle();

        //alert($(this));

        $(".add-new").attr("disabled", "disabled");
    });
    // Delete row on delete button click
    $(document).on("click", ".delete", function(){
        //$(this).parents("tr").remove();

         // swal({
         //    title: "Are you sure?",
         //    text: "You will not be able to recover this imaginary file!",
         //    type: "warning",
         //    showCancelButton: true,
         //    confirmButtonColor: '#DD6B55',
         //    confirmButtonText: 'Yes, I am sure!',
         //    cancelButtonText: "No, cancel it!",
         //    closeOnConfirm: false,
         //    closeOnCancel: false
         // },
         // function(isConfirm){

         //   if (isConfirm){

         //      var id = $(this).data("id");

         //      var data1 = {'currency_req':'Delete','cid':id}; 
         //      alert('success');

         //        // //alert(id);
         //        // $.ajax({
         //        //     method: "POST",
         //        //     url: "currency_detail.php",
         //        //     data: data1
         //        // })
         //        // .done(function( msg ) {
         //        // //alert(msg);

                
         //        // });
         //         // swal("Shortlisted!", "Candidates are successfully shortlisted!", "success");

         //        } else {
         //          swal("Cancelled", "Your imaginary file is safe :)", "error");
         //        }
         // });
            swal({
                title: "Are you sure?",
                text: "You want to Delete",
                icon: "warning",
                buttons: {
                    confirm : {text:'Ok',className:'sweet-warning'},
                    cancel : 'Cancel'
                },
            }).then((will)=>{
                if(will){
                    //alert('ok');
                   // alert('ok');
                  var id = $(this).data("id");

                  var data1 = {'currency_req':'Delete','cid':id}; 
                     // alert('success');

                        //alert(id);
                    $.ajax({
                        method: "POST",
                        url: "currency_detail.php",
                        data: data1
                    })
                    .done(function( msg ) {
                    location.reload();

                    
                    });
                    // window.location.href = "https://paymentgateway.test.credopay.in/testspaysez/grabpay/admin";
                    // $(".onoffswitch-checkbox").prop('checked',false);
                }else{
                    //swal("Cancelled", "Your not Refresh is safe :)", "error");
                    $("#all_petugas").click();
                     //alert('fail');
                }
             });
        // };
      
        $(".add-new").removeAttr("disabled");
    });
});
});


			function merchant() {
	//var basecurrency =document.getElementById('basecurrency').value;
	//var exchangecurrency =document.getElementById('exchangecurrency').value;
	//var currency_exchange_rate =document.getElementById('currency_exchange_rate').value;
	
	//var pg_merchant_status=document.getElementById('pg_merchant_status').value;

    // var e1 = document.getElementById("pg_merchant_action");
    // var pg_merchant_action = e1.options[e1.selectedIndex].value;

    var bc2 = document.getElementById("basecurrency");
    var base_currency_choose = bc2.options[bc2.selectedIndex].value;

    var ec2 = document.getElementById("exchangecurrency");
    var exchange_currency_choose = ec2.options[ec2.selectedIndex].value;
	
	if(base_currency_choose == 0)  {
		swal('Please choose the Base currency');
		return false
    }
    if(exchange_currency_choose == 0)  {
        swal('Please choose the Exchange currency');
        return false
    } 
  //alert(base_currency_choose)

    if(base_currency_choose != "") {
        $.ajax({
            url: "cbp_currency_change.php",
            data: {
                'base_currency' : base_currency_choose,
                'exchange_currency': exchange_currency_choose
            },
            type: 'POST',
            //dataType: 'json',
            success: function(data) {
                // console.log(data);
                console.log(data.result);
                if(data == 'success') {
                    e.preventDefault(); 
                    swal('Currency changes Done');
                    // window.location.href= "cbp_crncy_conv_reg.php";

                    // swal({
                    //   title: "Success!",
                    //   text: "Redirecting in 2 seconds.",
                    //   type: "success",
                    //   showConfirmButton: false
                    // }, function(){
                    //       window.location.href = "cbp_crncy_conv_reg.php";
                    // });
                }
                location.reload();
            },
            error: function(data){
                //swal('Currency changes Not Done');
                swal({
                        title: "error!",
                        text: "Currency changes Not Done!",
                        type: "faild"
                    }).then(function() {
                        window.location = "currency_conversion.php?t=<?php echo urlencode($_GET['t']); ?>";
                    });
                //window.location.href= "cbp_crncy_conv_reg.php";
            }
        });
        // return false;
    }

	
    // if(pg_merchant_action == 0)  {
    // swal('Please Enter the Merchant_Action');
    // return false
    // }

}
				</script>