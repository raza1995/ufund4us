<?
require_once("../configuration/dbconfig.php");
$sPageName = '<li><a href="manage_newsletters.php">Manage Newsletters</a></li><li>Edit Newsletter</li>';

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5 || $_SESSION['role_id'] == 6) {
		$oregister->redirect('dashboard.php');
	}
}
$sPageName = '<li><a href="manage_newsletters.php">Manage Newsletters</a></li> <li>Edit Newsletter</li>';
$sLeftMenuNewsletters = 'style="color:#F3BE00"';
if(isset($_GET['m']) && $_GET['m'] == 'edit')
{
	$id = $_GET['id'];
	$newsletter_data = $oCampaign->fetch_newsletter_content($id);
	$ffld_label = $newsletter_data['label'];
	$ffld_role = $newsletter_data['role'];
	$ffld_content = $newsletter_data['content'];
	$ffld_status = $newsletter_data['status'];
	$m = 'edit';
}else{
	$m = 'add';
}

if(array_key_exists('btnSubmit', $_POST))
{
	if($_POST['m'] == 'edit')
	{
		$id = $_POST['id'];
		$fld_label = xss_clean($_POST['fld_label']);
		$fld_role = $_POST['fld_role'];
		$fld_content = xss_clean($_POST['fld_content']);
		$fld_status1 = $_POST['fld_status'];	
		foreach ($fld_status1 as $key => $fld_status2) {
			$checked = $fld_status2;
		}
		if ($checked == 1) {
			$fld_status = '1';
		} else {
			$fld_status = '0';
		}
		
		$oCampaign->update_newsletter_content($id,$fld_label,$fld_role,$fld_content,$fld_status);
		$oregister->redirect('manage_newsletters.php?msg=4');
	}else if($_POST['m'] == 'add')
	{	
		$fld_label = xss_clean($_POST['fld_label']);
		$fld_role = $_POST['fld_role'];
		$fld_content = xss_clean($_POST['fld_content']);
		$fld_status1 = $_POST['fld_status'];	
		foreach ($fld_status1 as $key => $fld_status2) {
			$checked = $fld_status2;
		}
		if ($checked == 1) {
			$fld_status = '1';
		} else {
			$fld_status = '0';
		}
		
		$oCampaign->insert_newsletter_content($fld_label,$fld_role,$fld_content,$fld_status);
		$oregister->redirect('manage_newsletters.php?msg=3');
	}
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
<title>Admin<?php echo sWEBSITENAME;?> - Edit Newsletter</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="plugins/iCheck/all.css">
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<!--<script src="bower_components/tinymce/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea' });</script>-->
<script src="bower_components/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
   forced_root_block : "",
   selector: "textarea#mymce",
   theme: "modern",
   height:300,
   plugins: [
   "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
   "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
   "save table contextmenu directionality emoticons template paste textcolor"
   ],
   toolbar: "code | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
 });
</script>
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
			<h1 class="h1styling">Edit Newsletter</h1>
			<div class="line3"></div>
			<!-- .white-box -->
			<div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
			<div class=" full-main">
				<form data-toggle="validator"  method="post">
					<div class="form-group col-sm-6">
						<label for="fld_label" class="control-label">Newsletter Label<span style="color:#FF0000">*</span></label>
						<input type="text" class="form-control" id="fld_label" name="fld_label" placeholder="Enter Newsletter Label" value="<?=$ffld_label?>" required>
						<div class="help-block with-errors"></div>
					</div>
					
					<div class="form-group col-sm-6">
						<label for="fld_role" class="control-label">Roles<span style="color:#FF0000">*</span></label>
							<select class="form-control" id="fld_role" name="fld_role" required>
								<option <? if ($ffld_role == 2) {echo "selected";} ?> value="2">Campaign Managers</option>
								<option <? if ($ffld_role == 4) {echo "selected";} ?> value="4">Donors</option>
								<option <? if ($ffld_role == 5) {echo "selected";} ?> value="5">Participants</option>
							</select>
						<div class="help-block with-errors"></div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12">
						<textarea id="mymce" name="fld_content"><?=$ffld_content;?></textarea>
					</div>
					<div class="clearfix"></div>
					<br>
					<div class="form-group col-sm-6">
						<label for="fld_status" class="control-label">Status<span style="color:#FF0000">*</span></label>
						<br>
						<div class="radio radio-warning col-sm-2" style="margin-right:10px">
							<input type="radio" class="pgoalyes" name="fld_status[]" id="radio1" value="1" <? if ($ffld_status == 1) {echo "checked";} ?> >
							<label for="radio1"> Yes </label>
						</div>
						<div class="radio radio-warning col-sm-2" style="margin-top:10px;margin-right:10px">
							<input type="radio" class="pgoalno" name="fld_status[]" id="radio2" value="0" <? if ($ffld_status == 0) {echo "checked";} ?> >
							<label for="radio2"> No </label>
						</div>
						<div class="help-block with-errors" style="margin-top:10px"></div>
					</div>
					<div class="clearfix"></div>
					<div class="form-group">
						<input type="hidden" name="id" id="id" value="<?=$id?>">
						<input type="hidden" name="m" id="m" value="<?=$m?>">
						<div class="col-sm-6" align="left">
							<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='manage_newsletters.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
						</div>
		
						<div class="col-sm-6" align="right">
							<button class="btn btn-success waves-effect waves-light" name="btnSubmit" type="submit">Save <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div></div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
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
<!--Sparkline charts js -->
<script src="bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>

<script src="plugins/fastclick/fastclick.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>

<script type="text/javascript">
function showAccessDiv(id){
	if(id == 1){
		document.getElementById('divAccess').style.display = 'none';	
	}else if(id == 2){
		document.getElementById('divAccess').style.display = 'block';	
	}
}
   jQuery(document).ready(function($) {
    $('.vcarousel').carousel({
     interval: 3000
   })
    $(".counter").counterUp({
        delay: 100,
        time: 1200
    });
 });
</script>
<script>
  $(function () {
    

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

  });
</script>
</body>
</html>
<? include_once('bottom.php');?>