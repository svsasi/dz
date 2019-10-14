			<?php 
			include('init.php');

			$username = getUserdata2($_SESSION['iid']);

			if($username['terminal_id'] !='') {
				$db->where("idmerchants",$username['merchant_id']);
				$merchant = $db->getOne("merchants");
				if(isset($merchant['merchant_name'])){
					echo $merchant['merchant_name'];
				}
				$userName = $username['username'];
			} else {
				$merchant = getUserdata3($_SESSION['iid']);
				if(isset($merchant['merchant_name'])){
					echo $merchant['merchant_name'];
				} 
				$userName = $merchant['merchant_name'];	
			}
         	

			function active($currect_page){
				$url_array =  explode('/', $_SERVER['REQUEST_URI']) ;
				$url = end($url_array);

				$pageurl_first_exp = $url;
				$pageAppurl = explode("?", $pageurl_first_exp);
				$pageurl = $pageAppurl[0];  
				// echo $currect_page.'=>'.$url;
				if($currect_page == $pageurl){
					echo 'active'; //class name in css 
				} 
			}
			function expanded($currect_page){
				$url_array =  explode('/', $_SERVER['REQUEST_URI']) ;
				$url = end($url_array);

				$pageurl_first_exp = $url;
				$pageAppurl = explode("?", $pageurl_first_exp);
				$pageurl = $pageAppurl[0];  
				// echo $currect_page.'=>'.$url;
				if($currect_page == $pageurl){
					echo 'is-expanded'; //class name in css 
				} 
			}


            ?>

				<!--aside open-->
				<aside class="app-sidebar">
					<div class="app-sidebar__user">
						<div class="dropdown user-pro-body text-center">
							<div class="user-pic">
								<img src="<?php echo $public_path; ?>/assets/images/users/1.jpg" alt="user-img" class="avatar-xl rounded-circle mb-1">
							</div>
							<div class="user-info">

								

								<!-- <h6 class=" mb-0 font-weight-semibold">Paysez</h6>
								<span class="text-muted app-sidebar__user-name text-sm">Administrator</span> -->
								<?php 
								    	if(isset($username['user_type'])){

											 switch ($username['user_type']) {

												case 1:
													
													echo '<h6 class=" mb-0 font-weight-semibold"><font color=\"white\">Admin</font></h6>';
													//echo "<br>";
													echo ($username['username'] == "supremeuser") ? '<span class="text-muted app-sidebar__user-name text-sm">Supreme Administrator</span>' : '<span class="text-muted app-sidebar__user-name text-sm">Master Administrator</span>';

													break;

												case 2:

													echo "Agent";

													break;

												case 3:

													echo "Agent";

													break;

												case 4:

													echo "Merchant";

													break;

												case 5:
													

													echo '<h6 class=" mb-0 font-weight-semibold"><font color=\"white\">'.$userName.'</font></h6>';
												    //echo "<br>";
													echo '<span class="text-muted app-sidebar__user-name text-sm">Merchant User</span>';

													break;

												case 6:

													echo "Merchant";

													break;

												case 7:

													echo "Agent";

													break;

											}
										}
									    	

										?>
						
							</div>
						</div>
					</div>
					<ul class="side-menu">

						<?php 
						 if(isset($username['user_type'])){
						 	if ($username['user_type']==1) {
						 	
						?>
						

						

						<li class="slide <?php expanded('admindashboard.php');?>">


							<a class="side-menu__item <?php active('admindashboard.php');?>"  href="admindashboard.php?t=<?php echo urlencode($_GET['t']); ?>"><i class="side-menu__icon fe fe-monitor"></i><span class="side-menu__label">Dashboard</span><i class="angle fa fa-angle-right"></i></a>
						<!-- 	<ul class="slide-menu">
								<li><a class="slide-item"  href="transactions.php?t=<?php //echo urlencode($_GET['t']); ?>"><span>Transactions</span></a></li>
								<li><a class="slide-item" href="settlement.php?t=<?php //echo urlencode($_GET['t']); ?>"><span>Settlements</span></a></li>
								<li><a class="slide-item" href="refunds?t=<?php  //echo urlencode($_GET['t']); ?>"><span>Refunds</span></a></li>
 -->								<!--<li><a class="slide-item" href="index4.html"><span>Disputes</span></a></li>
								<li><a class="slide-item" href="index5.html"><span>Invoice</span></a></li>-->
							<!-- </ul> -->
						</li>
						<li class="slide <?php expanded('addmerchant.php');expanded('addterminal.php');expanded('merchantslist.php');expanded('terminallist.php');expanded('merchant_status_change.php');expanded('terminal_status_change.php');expanded('generateQR.php');expanded('Users.php');?>">
							<a class="side-menu__item <?php active('addmerchant.php');active('addterminal.php');active('merchantslist.php');active('terminallist.php');active('merchant_status_change.php');active('terminal_status_change.php');active('generateQR.php');active('Users.php');?> " data-toggle="slide" href="#"><i class="side-menu__icon fe fe-layers"></i><span class="side-menu__label">Merchants</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">

								<li class="<?php active('addmerchant.php');?>"><a href="addmerchant.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('addmerchant.php');?>"> Add Merchant</a></li>
								<li class="<?php active('addterminal.php');?>"><a href="addterminal.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('addterminal.php');?>"> Add Terminal</a></li>
								<li class="<?php active('merchantslist.php');?>" ><a href="merchantslist.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('merchantslist.php');?>">Merchant List</a></li>
								<li class="<?php active('terminallist.php');?>"><a href="terminallist.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('terminallist.php');?>">Terminal List</a></li>
								<li class="<?php active('merchant_status_change.php');?>"><a href="merchant_status_change.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('merchant_status_change.php');?>">Merchant Status</a></li>
								<li class="<?php active('terminal_status_change.php');?>"><a href="terminal_status_change.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('terminal_status_change.php');?>">Terminal Status</a></li>
								<li class="<?php active('generateQR.php');?>"><a href="generateQR.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('generateQR.php');?>"> Generate QR</a></li>
								<li class="<?php active('Users.php');?>"><a href="Users.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item  <?php active('Users.php');?>">Users</a></li>

							</ul>
						</li>
						
						<li class="slide <?php expanded('transaction_report.php');
						expanded('merchant_report.php');?>">
							<a class="side-menu__item <?php active('transaction_report.php');
						active('merchant_report.php');?>" data-toggle="slide" href="#"><i class="side-menu__icon fe fe-pie-chart"></i><span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<li class="<?php active('transaction_report.php');?>"><a href="transaction_report.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('transaction_report.php');?>">Trasaction Report</a></li>
								<!-- <li><a href="settlement_report.php?t=<?php //echo urlencode($_GET['t']); ?>" class="slide-item">Settlement </a></li>
								<li><a href="refund_report.php?t=<?php //echo urlencode($_GET['t']); ?>" class="slide-item">Refund</a></li> -->
								<li class="<?php active('merchant_report.php');?>"><a href="merchant_report.php?t=<?php echo urlencode($_GET['t']); ?>" class="slide-item <?php active('merchant_report.php');?>">Merchant Report</a></li>
							</ul>
						</li>
						<!-- <a href=""><li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="audittrails.php?t=<?php  // echo urlencode($_GET['t']); ?>"><i class="side-menu__icon fe fe-compass"></i><span class="side-menu__label">Audit Trails</span><i class="angle fa fa-angle-right"></i></a>
							
						</li></a> -->
						<li class="slide <?php expanded('audittrails.php');?>">
							<a class="side-menu__item <?php active('audittrails.php');?>"  href="audittrails.php?t=<?php echo urlencode($_GET['t']); ?>"><i class="side-menu__icon fe fe-compass"></i><span class="side-menu__label">Audit Trails</span><i class="angle fa fa-angle-right"></i></a>
						<li class="slide <?php expanded('currency_conversion.php');?>">
							<a class="side-menu__item <?php active('currency_conversion.php');?> " href="currency_conversion.php?t=<?php echo urlencode($_GET['t']); ?>"><i class="side-menu__icon fe fe-map-pin"></i><span class="side-menu__label">Currency Conversion</span></a>
						</li>

					<?php } else { ?>

						<li class="slide <?php expanded('dashboard.php');?>">
							<a class="side-menu__item sidemenu-icon  <?php active('dashboard.php');?>"  data-toggle="slide" href="dashboard.php?t=<?php echo urlencode($_GET['t']); ?>"><i class="side-menu__icon fe fe-monitor"></i><span class="side-menu__label">Dashboard</span></a>
							<!-- <ul class="slide-menu">
								<li><a class="slide-item"  href="index.html"><span>Transactions</span></a></li>
								<li><a class="slide-item" href="index2.html"><span>Settlements</span></a></li>
								<li><a class="slide-item" href="index3.html"><span>Refunds</span></a></li>
								<li><a class="slide-item" href="index4.html"><span>Disputes</span></a></li>
								<li><a class="slide-item" href="index5.html"><span>Invoice</span></a></li>-->
							<!--</ul> -->
						</li>

					<?php }  } ?>
						
					</ul>
				</aside>
				<!--aside closed-->