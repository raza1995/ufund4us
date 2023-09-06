<?
require_once("../configuration/dbconfig.php");

$REQUEST = &$_REQUEST;	
//Declare variable required bellow


if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li><a href="">Distributor Commission Settings</a></li> <li>Edit Distributor Commission Setting</li>';
$sSettingsLink = 'style="color:#F3BE00"';
$sLeftMenuMaintenance = 'active';
if(isset($REQUEST['m']) && $REQUEST['m'] == 'edit')
{
	$id = $REQUEST['id'];
	$aGlobalDetail = $oregister->get_settingsdetail($id);
	$uid = $aGlobalDetail['fld_userid'];
	$m = 'edit';
}else{
	$m = 'add';
}

if(isset($REQUEST['fld_gvalue']) && $REQUEST['fld_gvalue']!='')
{
	$sGID = $REQUEST['fld_gid'];
	$sValue= $REQUEST['fld_gvalue'];
	
	if($REQUEST['m'] == 'edit')
	{
		$oregister->update_gsettings($sGID,$sValue);
		$oregister->redirect('global_settings.php?msg=4');
	}
	/*else if($REQUEST['m'] == 'add')
	{
		
		$oregister->insert_gsettings($sTitle,$sCode,$sValue);				
		$oregister->redirect('global_settings.php?msg=3');
	}*/
	
	
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
<title>Admin - Edit Distributor Commission Settings</title>
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
		  <h1 class="h1styling">Edit Distributor Commission Settings</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box " style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		  <div class=" full-main">
			
   <form data-toggle="validator"  method="post">
	<div class="form-group col-sm-6">
		<label for="fld_gtitle" class="control-label">Distributor Name<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_gtitle" name="fld_gtitle" placeholder="Enter config title" readonly value="<?=$aGlobalDetail['fld_name']?> <?=$aGlobalDetail['fld_lname']?>" required>
		<div class="help-block with-errors"></div>
	</div>

	<div class="form-group col-sm-6">
		<label for="fld_gvalue" class="control-label">Distributor Commission level<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_gvalue" name="fld_gvalue" placeholder="Enter config value" value="<?=$aGlobalDetail['fld_gvalue']?>" required>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
   
   <div class="form-group">
		<input type="hidden" name="fld_gid" id="fld_gid" value="<?=$id?>">
		<input type="hidden" name="fld_uid" id="fld_uid" value="<?=$uid?>">
        <input type="hidden" name="m" id="m" value="<?=$m?>">
    	<div class="col-sm-6" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='global_settings.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" type="submit">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
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