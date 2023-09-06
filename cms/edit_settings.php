<?
require_once("../configuration/dbconfig.php");
$sPageName = '<li><a href="">Settings</a></li><li>Edit Settings</li>';

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sModuleName = 'Users';
$id = $_GET['uid'];

if($_POST['fld_name']!='' and $_POST['fld_email']!='')
{
	
}

if(isset($_GET['m']) && $_GET['m'] == 'edit')
{
	$sSettingsData = $oregister->getsettingsdetail(1);
}

if($_POST['fld_distributor_com']!='' and $_POST['fld_rep_com']!='')
{
		$sDCom = $_POST['fld_distributor_com'];
		$sRepCom = $_POST['fld_rep_com'];
		$sCom = $_POST['fld_commision'];
		$sDLevel1 = $_POST['fld_donation_level1'];
		$sDLevelAmt1 = $_POST['fld_donation_level1_amt'];
		$sDLevel2 = $_POST['fld_donation_level2'];
		$sDLevelAmt2 = $_POST['fld_donation_level2_amt'];
		$sDLevel3 = $_POST['fld_donation_level3'];
		$sDLevelAmt3 = $_POST['fld_donation_level3_amt'];
		
		$oregister->update_settings($sDCom,$sRepCom,$sCom,$sDLevel1,$sDLevelAmt1,$sDLevel2,$sDLevelAmt2,$sDLevel3,$sDLevelAmt3);
		$oregister->redirect('settings.php?msg=4');
		
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
<title>Admin<?php echo sWEBSITENAME;?> - Edit Settings</title>
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
		  <h1 class="h1styling">Edit Settings</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		  <div class=" full-main">
			
   <form data-toggle="validator"  method="post">
	<div class="form-group col-sm-6">
		<label for="fld_distributor_com" class="control-label">Distributor Commision<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_distributor_com" name="fld_distributor_com" placeholder="Distributor Commision" value="<?=$sSettingsData['fld_distributor_com']?>" required>
		<div class="help-block with-errors"></div>
	</div>

	<div class="form-group col-sm-6">
		<label for="fld_rep_com" class="control-label">Representative Commision</label>
		<input type="text" class="form-control" id="fld_rep_com" name="fld_rep_com" placeholder="Representative Commision" value="<?=$sSettingsData['fld_rep_com']?>" required>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_commision" class="control-label">Commision</label>
		<input type="text" class="form-control" id="fld_commision" name="fld_commision" placeholder="fld_commision" value="<?=$sSettingsData['fld_commision']?>" required>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="col-md-12"><h2 class="page-header"><strong>Donation Details</strong></h2></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donation_level1" class="control-label">Donation Level1<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level1" name="fld_donation_level1" placeholder="Donation Level" value="<?=$sSettingsData['fld_donation_level1']?>" required>
		<div class="help-block with-errors"></div>
	</div>

	<div class="form-group col-sm-6">
		<label for="fld_donation_level1_amt" class="control-label">Donation Amount<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level1_amt" name="fld_donation_level1_amt" placeholder="Amount" value="<?=$sSettingsData['fld_donation_level1_amt']?>" required>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donation_level2" class="control-label">Donation Level2<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level2" name="fld_donation_level2" placeholder="Donation Level" value="<?=$sSettingsData['fld_donation_level2']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donation_level2_amt" class="control-label">Donation Amount<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level2_amt" name="fld_donation_level2_amt" placeholder="Amount" value="<?=$sSettingsData['fld_donation_level2_amt']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>

	<div class="form-group col-sm-6">
		<label for="fld_donation_level3" class="control-label">Donation Level3<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level3" name="fld_donation_level3" placeholder="Donation Level" value="<?=$sSettingsData['fld_donation_level3']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_donation_level3_amt" class="control-label">Donation Amount<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_donation_level3_amt" name="fld_donation_level3_amt" placeholder="Amount" value="<?=$sSettingsData['fld_donation_level3_amt']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>
   
   <div class="form-group">
		<input type="hidden" name="fld_gid" id="fld_gid" value="<?=$id?>">
        <input type="hidden" name="m" id="m" value="<?=$m?>">
    	<div class="col-sm-6" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='dashboard.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" type="submit">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div>   </div>
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
</div>
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
</body>
</html>
<? include_once('bottom.php');?>