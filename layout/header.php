

<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>

		<!-- Meta data -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta content="Aronox – Admin Bootstrap4 Responsive Webapp Dashboard Templat" name="description">
		<meta content="Spruko Technologies Private Limited" name="author">
		<meta name="keywords" content="admin site template, html admin template,responsive admin template, admin panel template, bootstrap admin panel template, admin template, admin panel template, bootstrap simple admin template premium, simple bootstrap admin template, best bootstrap admin template, simple bootstrap admin template, admin panel template,responsive admin template, bootstrap simple admin template premium"/>

		<!-- Title -->
		<title>Paysez | Dashboard</title>
		<?php 
		    include('init.php');
			

			$username = getUserdata2($_SESSION['iid']);
			if($username['terminal_id'] !='') {
				$db->where("idmerchants",$username['merchant_id']);
				$merchant = $db->getOne("merchants");
				if(isset($merchant['merchant_name'])){
					//echo $merchant['merchant_name'];
				}
				$userName = $username['username'];
			} else {
				$merchant = getUserdata3($_SESSION['iid']);
				if(isset($merchant['merchant_name'])){
					//echo $merchant['merchant_name'];
				} 
				$userName = $merchant['merchant_name'];	
			}
                                    //echo $count_val1['COUNT(cbp_status)'];
		?>


		<!--Favicon -->
		<link rel="icon" href="<?php echo $public_path; ?>/assets/images/brand/favicon.ico" type="image/x-icon"/>

		
		<!-- Style css -->
		<link href="<?php echo $public_path; ?>/assets/css/style.css" rel="stylesheet" />

		<!--Sidemenu css -->
        <link href="<?php echo $public_path; ?>/assets/plugins/toggle-menu/sidemenu.css" rel="stylesheet">

		<!-- P-scroll bar css-->
		<link href="<?php echo $public_path; ?>/assets/plugins/p-scroll/perfect-scrollbar.css" rel="stylesheet" />

		<!---Icons css-->
		<link href="<?php echo $public_path; ?>/assets/plugins/iconfonts/icons.css" rel="stylesheet" />
		<link href="<?php echo $public_path; ?>/assets/plugins/iconfonts/font-awesome/font-awesome.min.css" rel="stylesheet">
		<link href="<?php echo $public_path; ?>/assets/plugins/iconfonts/plugin.css" rel="stylesheet" />

		<!-- Skin css-->
		<link id="theme" rel="stylesheet" type="text/css" media="all" href="<?php echo $public_path; ?>/assets/skins/hor-skin/leftmenu-icon-default.css" />



		<link rel="stylesheet" href="<?php echo $public_path; ?>/assets/skins/demo.css"/>

		<link href="<?php echo $public_path; ?>/assets/plugins/datatable/dataTables.bootstrap4.min.css" rel="stylesheet" />


		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>



	</head>

	<body class="app sidebar-mini">

		<!---Global-loader-->
		<div id="global-loader" >
			<img src="<?php echo $public_path; ?>/assets/images/svgs/loader.svg" alt="loader">
		</div>

		<div class="page">
			<div class="page-main">
				<div class="app-header header top-header">
					<div class="container-fluid">
						<div class="d-flex">
							
								<!--<img src="<?php //echo $public_path; ?>/assets/images/brand/logo-dark.png" class="header-brand-img dark-version" alt="Aronox logo">-->

								<?php if($username['user_type'] == 1) { ?>
					            <a  class="header-brand" href="admindashboard.php?t=<?php echo urlencode($_GET['t']) ?>">
					                <!-- <img src="img/spimg/Logo-Transparent 2.png" alt="logo" height="45px" style="margin:10px;" /> -->
					                <!-- <img src="img/spimg/CREDOPAY-LOGO.png" alt="logo" height="60px" style="margin:0 10px;" /> -->
					                <img src="<?php echo $public_path; ?>/assets/img/spimg/Logo-Transparent.png" class="header-brand-img desktop-lgo" alt="Paysez"/>
					            </a>
					            <?php } else { ?>
					            <a  class="header-brand" href="dashboard.php?t=<?php echo urlencode($_GET['t']) ?>">
					                <!-- <img src="img/spimg/Logo-Transparent 2.png" alt="logo" height="45px" style="margin:10px;" /> -->
					                <!-- <img src="img/spimg/CREDOPAY-LOGO.png" alt="logo" height="60px" style="margin:0 10px;" /> -->
					                <img src="<?php echo $public_path; ?>/assets/img/spimg/Logo-Transparent.png" class="header-brand-img desktop-lgo" alt="Paysez"/>
					                <!-- <img src="img/spimg/Paysez_Logo_11.jpeg" alt="logo" height="60px" style="margin:0 10px;" /> -->
					            </a>
					            <?php } ?>
								
								<!--<img src="<?php //echo $public_path; ?>/assets/images/brand/favicon.png" class="header-brand-img mobile-logo" alt="Aronox logo">-->
							
							<a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
							<form class="form-inline">
									<div class="search-element">
										<input type="search" class="form-control header-search" placeholder="Search…" aria-label="Search" tabindex="1">
										<button class="btn btn-primary-color" type="submit"><i class="fa fa-search"></i></button>
									</div>
								</form>
							<div class="dropdown  header-setting">

								<div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow">
									<a class="dropdown-item" href="#">
										Multi Pages
									</a>
									<a class="dropdown-item" href="#">
										Mail Settings
									</a>
									<a class="dropdown-item" href="#">
										Default Settings
									</a>
									<a class="dropdown-item" href="#">
										Documentation
									</a>
									<div class=" text-center p-2 border-top mt-3">
										<a href="#" class="">updated</a>
									</div>
								</div>
							</div>
							<div class="d-flex order-lg-2 ml-auto">
								<a href="#" data-toggle="search" class="nav-link nav-link-lg d-md-none navsearch"><i class="fa fa-search"></i></a>
								<div class="dropdown   header-fullscreen">
									<a class="nav-link icon full-screen-link" id="fullscreen-button">
										<i class="mdi mdi-arrow-collapse"></i>
									</a>
								</div>
								<!-- <div class="dropdown header-notify">
									<a class="nav-link icon text-center" data-toggle="dropdown">
										<i class="mdi mdi-email-outline"></i>
										<span class="nav-unread bg-danger pulse"></span>
									</a>
									<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow w-300  pt-0">
									    <div class="dropdown-header mt-0 pt-0 border-bottom p-4">
											<h5 class="dropdown-title mb-1 font-weight-semibold text-drak">Messages</h5>
											<p class="dropdown-title-text subtext mb-0 pb-0 ">You have 4 unread messages</p>
										</div>
										<a href="#" class="dropdown-item d-flex pb-4 pt-4">
											<div class="avatar avatar-md  mr-3 d-block cover-image border-radius-4" data-image-src="../assets/images/users/5.jpg">
												<span class="avatar-status bg-green"></span>
											</div>
											<div>
											    <small class="dropdown-text">Madeleine</small>
												<p class="mb-0 fs-13 text-muted">Hey! there I' am available</p>
											</div>
										</a>
										<a href="#" class="dropdown-item d-flex pb-4 pt-4">
											<div class="avatar avatar-md  mr-3 d-block cover-image border-radius-4" data-image-src="../assets/images/users/8.jpg">
												<span class="avatar-status bg-red"></span>
											</div>
											<div>
												<small class="dropdown-text">Anthony</small>
												<p class="mb-0 fs-13 text-muted ">New product Launching</p>
											</div>
										</a>
										<a href="#" class="dropdown-item d-flex pb-4 pt-4">
											<div class="avatar avatar-md  mr-3 d-block cover-image border-radius-4" data-image-src="../assets/images/users/11.jpg">
												<span class="avatar-status bg-yellow"></span>
											</div>
											<div>
												<small class="dropdown-text">Olivia</small>
												<p class="mb-0 fs-13 text-muted">New Schedule Realease</p>
											</div>
										</a>
										<div class="dropdown-divider mt-0"></div>
										<a href="#" class="dropdown-item text-center">See all Messages</a>
									</div> -->
								<!--</div> MESSAGE-BOX -->
								<!--<div class="dropdown d-md-flex message">
									<a class="nav-link icon" data-toggle="dropdown">
										<i class="mdi mdi-bell-outline"></i>
										<span class=" bg-success pulse-success "></span>
									</a>
									<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow pt-0">
									  <div class="dropdown-header border-bottom p-4 pt-0 mb-3 w-270">
									        <div class="d-flex">
												<h5 class="dropdown-title float-left mb-1 font-weight-semibold text-drak">Notifications</h5>
												<a href="#" class="fe fe-align-justify text-right float-right ml-auto text-muted"></a>
											</div>
										</div>
										<a href="#" class="dropdown-item d-flex pb-2 pt-2">
											<div class="card box-shadow-0 mb-0">
												<div class="card-body p-3">
													<div class="notifyimg bg-gradient-primary border-radius-4">
														<i class="mdi mdi-email-outline"></i>
													</div>
													<div>
														<div>Message Sent.</div>
														<div class="small text-muted">3 hours ago</div>
													</div>
												</div>
											</div>
										</a>
										<a href="#" class="dropdown-item d-flex  pb-2">
											<div class="card box-shadow-0 mb-0 ">
												<div class="card-body p-3">
													<div class="notifyimg bg-gradient-danger border-radius-4 bg-danger">
														<i class="fe fe-shopping-cart"></i>
													</div>
													<div>
														<div> Order Placed</div>
														<div class="small text-muted">5  hour ago</div>
													</div>
												</div>
											</div>
										</a>
										<a href="#" class="dropdown-item d-flex pb-2">
											<div class="card box-shadow-0 mb-0">
												<div class="card-body p-3">
													<div class="notifyimg bg-gradient-success  border-radius-4 bg-success mr-2">
														<i class="fe fe-airplay"></i>
													</div>
													<div>
														<div>Your Admin launched</div>
														<div class="small text-muted">1 daya ago</div>
													</div>
												</div>
											</div>
										</a>
										<div class=" text-center p-2 border-top mt-3">
											<a href="#" class="">View All Notifications</a>
										</div>
									</div>-->
								<!--</div>-->
								<div class="dropdown ">
									<a class="nav-link pr-0 leading-none" href="#" data-toggle="dropdown" aria-expanded="false">
									    <div class="profile-details mt-2">
									    	<?php 
									    	if(isset($username['user_type'])){

												 switch ($username['user_type']) {

													case 1:
														
														echo '<span class="mr-3 font-weight-semibold"><font color=\"white\">Admin</font></span>';
														//echo "<br>";
														echo ($username['username'] == "supremeuser") ? '<small class="text-muted mr-3">Supreme Administrator</small>' : '<small class="text-muted mr-3">Master Administrator</small>';

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
														

														echo '<span class="mr-3 font-weight-semibold"><font color=\"white\">'.$userName.'</font></span>';
													    //echo "<br>";
														echo '<small class="text-muted mr-3">Merchant User</small>';

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
										<img class="avatar avatar-md brround" src="<?php echo $public_path; ?>/assets/images/users/1.jpg" alt="image">
									 </a>
									<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow w-250">
										<div class="user-profile border-bottom p-3">
											<!-- <div class="user-image"><img class="user-images" src="../assets/images/users/1.jpg" alt="image"></div> -->
											<div class="user-details">
												<h4><?php echo $username['username'];?></h4>
												<p class="mb-1 fs-13 text-muted"></p>
											</div>
										</div>
										<a href="#" class="dropdown-item pt-3 pb-3"><i class="dropdown-icon mdi mdi-account-outline text-primary "></i> My Profile</a>
										<!-- <a href="#" class="dropdown-item pt-3 pb-3"><i class="dropdown-icon mdi  mdi-message-outline text-primary"></i> Messages <span class="badge badge-pill badge-success">41</span></a>
										<a href="#" class="dropdown-item pt-3 pb-3"><i class="dropdown-icon  mdi mdi-settings text-primary"></i> Setting</a>
										<a href="#" class="dropdown-item pt-3 pb-3"><i class="dropdown-icon mdi mdi-help-circle-outline text-primary"></i> FAQ</a> -->
										 
										<a href="login.php?logout=true&t=<?php echo urlencode($_GET['t']); ?>" class="dropdown-item pt-3 pb-3"><i class="dropdown-icon  mdi  mdi-logout-variant text-primary"></i>Sign Out</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>