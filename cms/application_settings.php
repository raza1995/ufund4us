<?
require_once("../configuration/dbconfig.php");
require_once("../login_check.php");
//Its only allowed for super admin 
if ($_SESSION['role_id'] != 1) {
	$oregister->redirect('dashboard.php');
}


$REQUEST = &$_REQUEST;
checkSetInArrayAndReturn($REQUEST, 'action', '');

if($REQUEST['action'] == 'update_application_settings'){
	$updateRes = $application_settings->update('ADDRESS_FOR_RECEIVING_CHECK', checkSetInArrayAndReturn($REQUEST, 'ADDRESS_FOR_RECEIVING_CHECK', ''));
	
	$updateRes2 = $application_settings->update('CHECK_PAYABLE', checkSetInArrayAndReturn($REQUEST, 'CHECK_PAYABLE', ''));
	
	if( is_string($updateRes) ){
		echo $updateRes; die();
	}
}


$sPageName = '<li><a href="participants.php">Manage Participants</a></li> <li>Edit Participant</li>';
$sParticipantLink = 'style="color:#F3BE00"';
$sLeftApplicationSettings = 'active';

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
<title>Admin<?php echo sWEBSITENAME;?> - Check Address</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">


</head>
<body>
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
			  	<h1 class="h1styling">Check Address</h1>
			  	<div class="line3"></div>
			  	<!-- .white-box -->
	          	<div class="white-box">
	          		<?php 
	          		$ret_data = $application_settings->get();
	          		$as_rows = $ret_data['rows'];
	          		// echo 'ret_data: <pre>'; print_r($ret_data); echo '</pre>';
	          		?>
					<form method="post" action="">
						<input type="hidden" name="action" value="update_application_settings">
					  <?php 
					  foreach($as_rows as $row)
					  { ?>
						  <div class="form-group">
						    <label for="<?php echo $row['key'];?>"><?php echo $row['title'];?></label>
						    <textarea
							    id="<?php echo $row['key'];?>" 
							    name="<?php echo $row['key'];?>" 
							    aria-describedby="emailHelp" placeholder="Enter email" 
							    class="form-control" 
						    ><?php echo $row['value'];?></textarea>

						    <small id="<?php echo $row['key'];?>" class="form-text text-muted"><?php echo $row['help_msg'];?></small>
						  </div>
					  	<?php 
					   }
					   ?>

					  <button type="submit" class="btn btn-primary app-setting-btn-primary">Save</button>
					</form>		

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

	<!-- /#wrapper 2 -->
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
	<!--<script src="js/mask.js"></script>-->
	<!--Sparkline charts js -->
	<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
	<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
	<!-- jQuery for carousel -->
	<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
	<script src="bower_components/owl.carousel/owl.custom.js"></script>
	<script src="js/validator.js"></script>
	<script src="js/jquery.inputmask.js"></script>
</body>
</html>
<? include_once('bottom.php');?>