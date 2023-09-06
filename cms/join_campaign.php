<?
require_once("../configuration/dbconfig.php");
$sPageName = '<li>Join Campaign</li>';
$sLeftMenuJoinCampaign = 'active';
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 4) {
		$oregister->redirect('dashboard.php');
	}
}
$msg1 = '';
if(array_key_exists('joincampaign', $_POST))
{
	$camp_number = ltrim($_POST['fld_camp_number'], '0');
	$camp_id = $_POST['fld_camp_id'];
	if ($camp_number != '' && $camp_id != '') {
		$oregister->redirect('confirm_campaign.php?cid='.$camp_number.'&cno='.$camp_id.'');
	} else {
		$msg1 = '<div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error:</b> The Campaign you are trying to join is incorrect information...!
				</div>';
	}
}

if (isset($_GET['msg']) && $_GET['msg'] == 'error') {
	$msg1 = '<div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error:</b> The Campaign you are trying to join is incorrect information...!
				</div>';
} elseif (isset($_GET['msg']) && $_GET['msg'] == 'alreadyjoined') {
	$msg1 = '<div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error:</b> You have already joined this Campaign...!
					</div>';
} elseif (isset($_GET['msg']) && $_GET['msg'] == 'success') {
	$msg1 = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success:</b> You have joined this Campaign...!
					</div>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
<title>Admin<?php echo sWEBSITENAME;?> - Edit Profile</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
  <? include_once('header.php');?>
  <!-- Left side column. contains the logo and sidebar -->
  <? include_once('left_panel.php');?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <!--row -->
      <div class="row">
          <div class="col-sm-12">
		  <h1 class="h1styling">Join Campaign</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		   <div class=" full-main" style="width:75% !important">
			
   <form data-toggle="validator"  method="post">
	<div class="col-sm-12">
		<? 
		echo $msg1;
		?>
	</div>
	<div class="clearfix"></div>
	<div class="form-group col-sm-5">
		<label for="fld_camp_number" class="control-label">Enter Campaign # *</label>
		<input type="text" class="form-control" id="fld_camp_number" name="fld_camp_number" placeholder="e.g 000000003">
	</div>
	<div class="form-group col-sm-2">
		<div style="font-size:20px; margin-top:30px; text-align: center">AND</div>
	</div>
	<div class="form-group col-sm-5">
		<label for="fld_camp_id" class="control-label">Enter Campaign ID# *</label>
		<input type="text" class="form-control" id="fld_camp_id" name="fld_camp_id" placeholder="e.g 1234">
	</div>
	<div class="clearfix"></div>
   
   <div class="form-group">
    	<div class="col-sm-6">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='dashboard.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" name="joincampaign" type="submit">Join Campaign <span class="btn-label forright-icon" style="left:145px !important"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   
   </div>
   </div>
</div>
		  </div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
	<!-- #footer -->
    <? include_once('footer.php');?>
	<!-- /#footer -->
<!-- /#wrapper -->
<!-- jQuery -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
<!--Nice scroll JavaScript -->
<script src="js/jquery.nicescroll.js"></script>

<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/myadmin.js"></script>
<!--Counter js -->
<script src="bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="bower_components/counterup/jquery.counterup.min.js"></script>
<script src="js/mask.js"></script>
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="js/validator.js"></script>
<script>
</script>
</body>
</html>
<? include_once('bottom.php');?>