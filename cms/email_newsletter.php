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
$sPageName = '<li><a href="manage_newsletters.php">Manage Newsletters</a></li> <li>Email Newsletter</li>';
$sLeftMenuNewsletters = 'style="color:#F3BE00"';

$id = $_GET['id'];
$newsletter_data = $oCampaign->fetch_newsletter_content($id);
$ffld_label = $newsletter_data['label'];
$ffld_role = $newsletter_data['role'];
$ffld_content = $newsletter_data['content'];
$ffld_status = $newsletter_data['status'];

if(array_key_exists('btnSubmit', $_POST))
{
	/*include_once('classes/class.phpmailer.php');
	$mail = new phpmailer;
	$mail->CharSet = 'UTF-8';
	$mail->Mailer  = 'mail';
	if ($cc != '') {
		$mail->addCC($email_cc);
	}
	$mail->AddReplyTo(INFO_EMAIL,sWEBSITENAME);
	$mail->SetFrom(NO_REPLY_EMAIL, sWEBSITENAME);
	$mail->isHTML(true);
	$mail->Subject = $ffld_label;
	
	$fld_smode = $_POST['fld_smode'];
	foreach ($fld_smode as $key => $fld_smode1) {
		$checked1 = $fld_smode1;
	}
	if ($checked1 == 1) {
		$emails_dataset = $oCampaign->emails_newsletter($id);
		$iCountRecords = count($emails_dataset);
		if($iCountRecords>0){
			for($i=0;$i<$iCountRecords;$i++){
				$mail->AddAddress(trim($emails_dataset[$i]['fld_email']));
			}
		}
	} else {
		$fld_smode_test_email = $_POST['fld_smode_test_email'];
		$mail->AddAddress(trim($fld_smode_test_email));
	}
	
	
	$mail->AddBCC(EMAILS_EMAIL, EMAILS_NAME_IN_EMAIL);
	$mail->Body = $ffld_content;
	if ($mail->Send()) {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>You have successfully emailed...!
			</div>';
	} else {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Error: Message not sent...!
			</div>';
	}*/
	$fld_smode = $_POST['fld_smode'];
	$fld_smode_test_email = $_POST['fld_smode_test_email'];
	foreach ($fld_smode as $key => $fld_smode1) {
		$checked1 = $fld_smode1;
	}
	$emails_send = $oCampaign->emails_send_newsletter($checked1,$fld_smode_test_email,$id,$ffld_label,$ffld_role,$ffld_content,$ffld_status);
	$oregister->redirect('manage_newsletters.php');
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
<title>Admin<?php echo sWEBSITENAME;?> - Email Newsletter</title>
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
   readonly : 1,
   selector: "textarea#mymce",
   theme: "modern",
   height:300
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
			<h1 class="h1styling">Email Newsletter</h1>
			<div class="line3"></div>
			<!-- .white-box -->
			<div class="white-box" style="background: rgba(245, 245, 245, 0);border: 0px solid #d9d6d6;">
			<div class=" full-main">
				<form data-toggle="validator"  method="post">
					<div class="col-sm-12">
						<div class="form-group col-sm-6">
							<label for="fld_smode" class="control-label">Sending Mode<span style="color:#FF0000">*</span></label>
							<div class="col-sm-12">
								<div class="radio radio-warning col-sm-6" >
									<input type="radio" class="sendingmode" name="fld_smode[]" id="radio1" value="1" checked>
									<label for="radio1"> Production </label>
								</div>
								<div class="radio radio-warning col-sm-6" style="margin-top:10px;">
									<input type="radio" class="sendingmode" name="fld_smode[]" id="radio2" value="0">
									<label for="radio2"> Test </label>
								</div>
								<div class="clearfix"></div>
								<div class="help-block with-errors" style="margin-top:10px"></div>
							</div>
						</div>
						<div class="form-group col-sm-6 smodetest" style="display:none;">
							<label for="fld_smode_test_email" class="control-label">Test Email<span style="color:#FF0000">*</span></label>
							<input type="email" placeholder="Please Enter Email to Test" class="form-control" name="fld_smode_test_email" id="fld_smode_test_email" value="<?=$fld_smode_test_email;?>" />
							<div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-12">
						<div class="form-group col-sm-6">
							<label for="fld_label" class="control-label">Newsletter Label<span style="color:#FF0000">*</span></label>
							<input type="text" class="form-control" id="fld_label" name="fld_label" readonly placeholder="Enter Newsletter Label" value="<?=$ffld_label?>" required>
							<div class="help-block with-errors"></div>
						</div>
						
						<div class="form-group col-sm-6">
							<label for="fld_role" class="control-label">Roles<span style="color:#FF0000">*</span></label>
								<select class="form-control" id="fld_role" name="fld_role" disabled>
									<option <? if ($ffld_role == 2) {echo "selected";} ?> value="2">Campaign Managers</option>
									<option <? if ($ffld_role == 4) {echo "selected";} ?> value="4">Donors</option>
									<option <? if ($ffld_role == 5) {echo "selected";} ?> value="5">Participants</option>
								</select>
							<div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12">
						<textarea readonly id="mymce" name="fld_content"><?=$ffld_content;?></textarea>
					</div>
					<div class="clearfix"></div>
					<br>
					<div class="col-md-12">
						<div class="form-group">
							<input type="hidden" name="id" id="id" value="<?=$id?>">
							<input type="hidden" name="m" id="m" value="<?=$m?>">
							<div class="col-sm-6" align="left">
								<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='manage_newsletters.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
							</div>
			
							<div class="col-sm-6" align="right">
								<button class="btn btn-success waves-effect waves-light" name="btnSubmit" type="submit">Send <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>
			</div>
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
$(document).ready(function() {
	$('.sendingmode').on('click', function() {
		var checkedvalue1 = $(this).val();
		if (checkedvalue1 == 1) {
			$('.smodetest').hide();
			$('#fld_smode_test_email').prop('required',false);
		} else {
			$('.smodetest').show();
			$('#fld_smode_test_email').prop('required',true);
		}
	});
});
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