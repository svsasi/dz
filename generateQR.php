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

    			
    			 
                   
                                $iid = $_SESSION['iid'];

                                $query="SELECT YEAR(transaction.trans_datetime) AS year, MONTH(transaction.trans_datetime) AS month, COUNT(DISTINCT transaction.id_transaction_id) AS transcount, SUM(transaction.total_fee) AS transamount FROM merchants JOIN transaction ON transaction.merchant_id = merchants.idmerchants AND merchants.userid= '$iid' AND YEAR(transaction.trans_datetime) = YEAR(CURDATE()) GROUP BY year, month";
                                $transactions_Curryear = $db->rawQuery($query);

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
                                
                                 ?>

                            <div class="app-content">
            					<div class="side-app leftmenu-icon">

                                    <!--Page header-->
                                    <div class="page-header">
                                        <div class="page-leftheader">
                                            <ol class="breadcrumb pl-0">
                                                <li class="breadcrumb-item"><a href="admindashboard.php?t=<?php echo urlencode($_GET['t']); ?>">Home</a></li>
                                                <li class="breadcrumb-item active" aria-current="page">GenerateQR</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <!--End Page header-->
            						<div class="row">
            							
                                        <div class="col-md-12">
                                            <div class="card">
                                                
                    								<form  action="#" method="POST">
                    									<div class="card-header">
                    										<h3 class="card-title">Merchant QR creation</h3>
                    										<div class="card-options ">
                    											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                    											<!-- <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a> -->
                    										</div>
                    									</div>
                    									<?php
                                                        include "phpqrcode/qrlib.php";

                                                        if($_POST['merchantid']!="") {
                                                          
                                                            $db->where("mer_map_id",$_POST['merchantid']);
                                                            $dgot = $db->getOne("merchants");

                                                            if($dgot['idmerchants'] == "") {
                                                                echo "<br><br>";
                                                                echo "<center><h4>Merchant ID not found</h4><br><br><a href='merchantQR.php'>Back to CREATE QR</a></center>";
                                                            } else {
                                                                $merchantid = $_POST['merchantid'];
                                                                $terminalid = $_POST['terminalid'];

                                                                $db->where("merchant_id",$dgot['idmerchants']);
                                                                $merchant_bank_det = $db->getOne("merchant_processors_mid");
                                                                $ifsc = $merchant_bank_det['ifsccode'];

                                                                $currency = $dgot['currency_code'];
                                                                $mcc = $dgot['mcc'];
                                                                $merchantname = $dgot['merchant_name'];
                                                                $merchantname_exp = explode(" ", $dgot['merchant_name']);
                                                                $store_name = $merchantname_exp[0];
                                                                $address = $dgot['address'];
                                                                $city = $dgot['city'];
                                                                $state = $dgot['state'];
                                                                $country = $dgot['country'];
                                                                $pin = $dgot['zippostalcode'];

                                                                $path = "merchQR/qrimg" .$merchantid.$terminalid. ".png";
                                            
                                                                $qstring = "merchantid=" . $merchantid . "&terminalid=" . $terminalid . "&currency=" . $currency . "&ifsc=" . $ifsc . "&mcc=" . $mcc . "&merchantname=" . $merchantname . "&city=" . $city . "&pin=" . $pin . "&store_name=" . $store_name;
                               
                                                                $qstring = base64_encode($qstring);


                                                                // QRcode::png("http://169.38.91.246/Spaysez/alipay_en.php?qstring=" . $qstring, $path, "L", 5, 5);
                                                                QRcode::png("https://paymentgateway.test.credopay.in/Spaysez/alipay_en.php?qstring=" . $qstring, $path, "L", 4.75, 4.75);


                                                                //Adding image center to an QRcode starts
                                                                $imgname=$path;
                                                                $logo="merchQR/sha.png";
                                                                $QR = imagecreatefrompng($imgname);
                                                                $logopng = imagecreatefrompng($logo);
                                                                $QR_width = imagesx($QR);
                                                                $QR_height = imagesy($QR);
                                                                $logo_width = imagesx($logopng);
                                                                $logo_height = imagesy($logopng);

                                                                list($newwidth, $newheight) = getimagesize($logo);
                                                                $out = imagecreatetruecolor($QR_width, $QR_width);
                                                                imagecopyresampled($out, $QR, 0, 0, 0, 0, $QR_width, $QR_height, $QR_width, $QR_height);
                                                                imagecopyresampled($out, $logopng, $QR_width/2.65, $QR_height/2.65, 0, 0, $QR_width/4, $QR_height/4, $newwidth, $newheight);
                                                                imagepng($out,$imgname);
                                                                imagedestroy($out);

                                                                if (file_exists($path)) {

                                                                    $text_logo="merchTXT/text_".$merchantid.$terminalid.".png";
                                                                
                                                                    $text_QR = imagecreatefrompng($imgname);
                                                                    $text_logopng = imagecreatefrompng($text_logo);
                                                                    $text_QR_width = imagesx($text_QR);
                                                                    $text_QR_height = imagesy($text_QR);
                                                                    $text_logo_width = 200; // imagesx($text_logopng);
                                                                    $text_logo_height = 15; // imagesy($text_logopng);

                                                                    list($newwidth, $newheight) = getimagesize($text_logo);
                                                                    $text_out = imagecreatetruecolor($text_QR_width, $text_QR_width);
                                                                    imagecopyresampled($text_out, $text_QR, 0, 0, 0, 0, $text_QR_width, $text_QR_height, $text_QR_width, $text_QR_height);
                                                                    imagecopyresampled($text_out, $text_logopng, $text_QR_width - (($text_QR_width/2)+83), $text_QR_height - (($text_QR_height/2)-147), 0, 0, ($text_QR_width/2)+15, ($text_QR_height/2)-147, $newwidth, $newheight);
                                                                    imagepng($text_out,$imgname);
                                                                    imagedestroy($text_out);
                             
                                                                }

                                                         
                                                                $ddata = array(
                                                                    "mso_ter_device_mac" => $path
                                                                );
                                                                $db->where('idmerchants', $dgot['idmerchants']);
                                                                $db->where('mso_terminal_id', $terminalid);
                                                                $val = $db->update('terminal', $ddata);

                                                                if($val == TRUE) {
                                                                    echo "<b>QR image generated successfully";
                                                                    echo "<br><br>";
                                                                    
                                                                    echo "Path:</b> https://paymentgateway.test.credopay.in/Spaysez/" . $path;
                                                                    echo "<br><br>";
                                                                    echo "<a href='merchantQR.php?t=".urlencode($_GET['t'])."'>CREATE ANOTHER</a>&nbsp;&nbsp;||&nbsp;&nbsp;<a target='_blank' href='" . $path . "'>VIEW QR</a>&nbsp;&nbsp;||&nbsp;&nbsp;<a target='_blank' href='download.php?file=".$path."'>DOWNLOAD QR</a>";
                                                                }
                                                            }

                                                                } else {
                                                            ?>
                                                            <style>
                                                                label {
                                                                    font-weight: bold;
                                                                }
                                                            </style>
                                                           
                                                            <?php
                                                            if($userDet['username'] == "hutchadminuser") {
                                                                $cols = array("idmerchants", "merchant_name", "mer_map_id", "is_active");
                                                                $db->where("idmerchants", $userDet['merchant_id']);
                                                                $db->where("is_active",1);
                                                                $db->orderBy("mer_map_id","asc");
                                                                $merchantDet = $db->get("merchants", null, $cols);

                                                            } else {
                                                                $cols = array("idmerchants", "merchant_name", "mer_map_id", "is_active");
                                                                $db->where("is_active",1);
                                                                $db->orderBy("mer_map_id","asc");
                                                                $merchantDet = $db->get("merchants", null, $cols);
                                                            }
                                                            ?>
                                                            
                                                               <div class="card-body">
                            										<div class="row">
                            											<div class="col-sm-6 col-md-3">
                            												<div class="form-group">
                                                                            <label>Merchant ID</label><BR>
                                                                            
                                                                            <input list="merchant_id" name="merchantid" class="form-control" id="merchantid">
                                                                            <datalist id="merchant_id">
                                                                                <option value="">Select</option>
                                                                                <?php
                                                                                foreach ($merchantDet as $key => $value) {
                                                                                    echo '<option value="'.$value['mer_map_id'].'">'.$value['mer_map_id'].' - '.$value['merchant_name'].'</option>';
                                                                                }
                                                                                ?>
                                                                            </datalist>
                                                                        </div>
                                                                    </div>
                                                                       <div class="col-sm-6 col-md-3">
                            												<div class="form-group">
                                                                            <label>Terminal ID</label>
                                                                            <select class="form-control m-b" name="terminalid" id="terminal_id">
                                                                                <option value="">-- Terminal ID --</option>
                                                                            </select>
                                                                        </div>
                                                                       
                                                                    </div>
                                                                </div>
                                                                &nbsp; &nbsp; 
                                                                <div class="row">
                                                                    <div class="col-sm-12" id="qrcode">
                                                                    	<div class="form-group">
                                                                        <b>QR Code Already Generated, Please click below to View (OR) Download QR Code</b><br>
                                                                        <a id="link1" target='_blank' href=''>VIEW QR</a>&nbsp;&nbsp;||&nbsp;&nbsp;<a id="link2" target='_blank' href=''>DOWNLOAD QR</a>
                                                                    </div>
                                                                    <div class="col-sm-12" id="qrcode_btn">
                                                                        <input type="submit" class="btn btn-warning" value="Generate QR">
                                                                    </div>
                                                                </div>
                                                    </form>
                                                    <?php  } ?>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                                    

                                <?php

                        }
                        include('layout/footer.php');
                            } else {
                                echo '<h1>You are Unauthorized</h1>'; // echo "Hi1";
                                header("location:/".SITEURLPATH);
                            }
                        } else {
                            echo '<h1>You are Unauthorized</h1>'; // echo "Hi2";
                            header("location:/".SITEURLPATH);
                        }

?>



<!-- <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript" src="js/plugins/tabelizer/jquery.tabelizer.js"></script>

<link rel="stylesheet" href="css/plugins/tabelizer/tabelizer.css">
<script src="js/plugins/jquery-ui/jquery-ui.min.js"></script> -->

<!-- <script type="text/javascript" src="js/plugins/tabelizer/jquery.tabelizer.js"></script>

<link rel="stylesheet" href="css/plugins/tabelizer/tabelizer.css">
 -->


<!-- Data picker -->

<!-- <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script> -->

<script>
    // function callQueryapi() {
    //     $.ajax({
    //         method: "POST",
    //         url: "alipayapi.php",
    //         data: {action: '7' }
    //     })
    //         .done(function (msg) {
    //             if(msg==1)
    //                 location.reload();
    //             else
    //                 alert("All Transactions are up to date");
    //         });
    // }
    $(document).ready(function(){
        var obj;
        $("#merchantid").change(function () {
            //alert($(this).val());

            var obj = $("#merchant_id").find("option[value='" + $(this).val() + "']").attr('value');
           // alert(obj);
            //var obj = $("#ModelName").find("option[value='" + modelname + "']");
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

        //if (obj=="undefined"||obj=="") {

          //  alert('success');
            $("#qrcode").hide();
            $("#qrcode_btn").hide();
        //}
        $("#terminal_id").change(function () {
            // alert($(this).val());
            console.log($(this).val());
            if($(this).val()){
                $.ajax({
                    type: 'POST',
                    url: 'php/inc_reportsearch1.php',
                    data: JSON.stringify({'t_id': $(this).val(), 'type':'getterminalQR'})
                })
                .done(function( msg ) {
                    console.log(msg);
                    if(msg!=''){
                        $("#qrcode_btn").hide();
                        $("#qrcode").show();
                        $("a#link1").attr("href",msg);
                        $("a#link2").attr("href","download.php?file="+msg);
                    } else {
                        $("#qrcode_btn").show();
                        $("#qrcode").hide();
                    }
                });
            } else {
                $("#qrcode_btn").hide();
                $("#qrcode").hide();
            }
        });


        // $('.date-sec .input-group.date').datepicker({
        //     todayBtn: "linked",
        //     keyboardNavigation: false,
        //     forceParse: false,
        //     calendarWeeks: true,
        //     dateFormat: 'yyyy-mm-dd',
        //     autoclose: true
        // });

        // var mid_1 = '<?php // echo $mid; ?>';
        // alert(mid_1);

        // $.ajax({

        //     method: "POST",

        //     url: "php/inc_dailyreport.php",

        //     data: { mid: mid_1 }

        // })

        //     .done(function( msg ) {

        //         $("#dailyreport").html(msg);

        //     });

        // $('#date').datepicker({

        //     todayBtn: "linked",

        //     keyboardNavigation: false,

        //     forceParse: false,

        //     calendarWeeks: true,

        //     autoclose: true

        // });

        // $("#date").change(function () {

        //     $.ajax({

        //         method: "POST",

        //         url: "php/inc_dailyreport.php",

        //         data: { date: $(this).val() }

        //     })

        //         .done(function( msg ) {

        //             $("#dailyreport").html(msg);

        //             $("#exportlink").attr("href", "phpexcel/report.php?date="+$("#date").val());

        //         });

        // });

        // var table1 = $('#table1').tabelize({

        //     /*onRowClick : function(){

        //      alert('test');

        //      }*/

        //     fullRowClickable : true,

        //     onReady : function(){

        //         console.log('ready');

        //     },

        //     onBeforeRowClick :  function(){

        //         console.log('onBeforeRowClick');

        //     },

        //     onAfterRowClick :  function(){

        //         console.log('onAfterRowClick');

        //     },

        // });



        //$('#table1 tr').removeClass('contracted').addClass('expanded l1-first');

    });

</script>



<!-- <script type="text/javascript" src="js/plugins/treegrid/jquery.treegrid.js"></script>

<link rel="stylesheet" href="css/plugins/treegrid/jquery.treegrid.css"> -->



<script type="text/javascript">

   

</script>



<!-- ChartJS-->
<!-- 
<script src="js/plugins/chartJs/Chart.min.js"></script> -->

<script type="text/javascript">

    // $(function () {
    // $(document).ready(function(){


    //     /**** Displaying the number of transactions per month in Line graph format ****/

    //     var lineData = {

    //         labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],

    //         datasets: [

    //             {

    //                 label: "Example dataset",

    //                 fillColor: "rgba(26,179,148,0.5)",

    //                 strokeColor: "rgba(26,179,148,0.7)",

    //                 pointColor: "rgba(26,179,148,1)",

    //                 pointStrokeColor: "#fff",

    //                 pointHighlightFill: "#fff",

    //                 pointHighlightStroke: "rgba(26,179,148,1)",

    //                 data: [<?php echo $transCnts; // echo $transactions_data; ?>]

    //             },

    //             {

    //                 label: "Example dataset",

    //                 fillColor: "rgba(255,133,0,0.5)",

    //                 strokeColor: "rgba(255,133,0,1)",

    //                 pointColor: "rgba(255,133,0,1)",

    //                 pointStrokeColor: "#fff",

    //                 pointHighlightFill: "#fff",

    //                 pointHighlightStroke: "rgba(255,133,0,1)",

    //                 data: [<?php // echo $transAmts; // echo $chargebacks_data; ?>]

    //             }

    //         ]

    //     };



    //     var lineOptions = {

    //         scaleShowGridLines: true,

    //         scaleGridLineColor: "rgba(0,0,0,.05)",

    //         scaleGridLineWidth: 1,

    //         bezierCurve: true,

    //         bezierCurveTension: 0.4,

    //         pointDot: true,

    //         pointDotRadius: 4,

    //         pointDotStrokeWidth: 1,

    //         pointHitDetectionRadius: 20,

    //         datasetStroke: true,

    //         datasetStrokeWidth: 2,

    //         datasetFill: true,

    //         responsive: true,

    //     };

    //     var ctx = document.getElementById("lineChart").getContext("2d");

    //     var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

    // });

    /**** Daily Summary Report for selecting date from picker ****/
    // $('#date').on("change", function () {
    //     // alert($(this).val());
    //     var selected_date = $(this).val();
    //     if(selected_date!='') {
    //         $('.rlt_row').show();
    //     }

    //     $.ajax({
    //         method: "POST",
    //         url: "php/inc_<?php echo $search_type; ?>search.php",
    //         data: {S_Date: selected_date}
    //     })
    //         .done(function( msg ) {
    //             $("#cbresults").html(msg);
    //             $('.dataTables-example').dataTable({
    //                 "order": [[ 1, "desc" ]],
    //                 responsive: true,
    //                 "dom": 'T<"clear">lfrtip',
    //                 "tableTools": {
    //                     "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
    //                 }
    //             });
    //             $('input:checkbox').change(function() {
    //                 if($(this).attr('id') == 'selectall') {
    //                     jqCheckAll2( this.id);
    //                 }
    //             });
    //             function jqCheckAll2( id ) {
    //                 $("INPUT[type='checkbox']").attr('checked', $('#' + id).is(':checked'));
    //             }

    //             $("#exportlink_date").attr("href", "php/inc_transsearch.php?date="+selected_date);
    //         });

    // });

    /**** Sparkline Graph jquery ****/
    // $('.sparkline').sparkline('html', { enableTagOptions: true });

    // $('.sparkline_1').sparkline('html', { enableTagOptions: true });

    // $(window).on('resize', function() {
    // 	$('.sparkline').sparkline('html', { enableTagOptions: true });
    // });

</script>

