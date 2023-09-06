<?
// echo dirname(__FILE__)."/../login_check.php"; die();
require_once(dirname(__FILE__)."/../configuration/dbconfig.php");
require_once(dirname(__FILE__)."/../login_check.php");

$sPageName = '';
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
<title>Admin<?php echo sWEBSITENAME;?> - Dashboard</title>
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
		  <!-- .white-box -->
		  <h1 class="h1styling">Dashboard</h1>
		  <div class="line3"></div>
          <div class="white-box">
		  <div class="dashboard-box-main">
			<? if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) { ?>
			<div class="  dashboard-box" >
				<a href="start_campaign.php"><img src="dist/img/img2.png" class="dashboard-img" >
					<p>Start New Campaign</p><!--/.info-box-->			
				</a>
			</div>
			</div>
			<div class="dashboard-box-main">
			<div class="dashboard-box-2" >
				<a href="manage_campaign.php"><img src="dist/img/img3.png" class="dashboard-img" >
					<p>Manage Campaign</p><!--/.info-box-->			
				</a>
			</div>
			</div>
			<? } ?>
			<? if ($_SESSION['role_id'] == 5) { ?>
			<div class="dashboard-box-main">
			<div class="dashboard-box-2" >
				<a href="manage_campaign_participant.php"><img src="dist/img/img3.png" class="dashboard-img" >
					<p>Manage Campaign</p><!--/.info-box-->			
				</a>
			</div>
			<div class="dashboard-box-3">
				<a href="join_campaign.php"><img src="dist/img/img4.png" class="dashboard-img" >
					<p>Join Campaign</p><!--/.info-box-->			
				</a>
			</div>
			</div>
			<? } ?>
			<?php if(in_array("3",$acArray['view'])){?>
			<? if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 6) { ?>
			<div class="dashboard-box-main">
			<div class="dashboard-box-3">
				<a href="join_campaign.php"><img src="dist/img/img4.png" class="dashboard-img" >
					<p>Join Campaign</p><!--/.info-box-->			
				</a>
			</div>
			</div>
			<? } } ?>
			<div class="clearfix"></div>
		  </div>
		</div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
  </div>
</div>
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
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script type="text/javascript">
   jQuery(document).ready(function($) {
    $('.vcarousel').carousel({
     interval: 3000
   })
    $(".counter").counterUp({
        delay: 100,
        time: 1200
    });
    $(':checkbox:checked').prop('checked',false);
 });
</script>
</body>
</html>
<? include_once('bottom.php');?>