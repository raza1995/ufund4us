<?
require_once("../configuration/dbconfig.php");
$REQUEST = $_REQUEST;

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	$user_details = $oregister->getuserdetail($_SESSION['uid']);
	if ($_SESSION['role_id'] == 3) {
		if ($user_details['fld_cname'] == "" || $user_details['fld_name'] == "" || $user_details['fld_lname'] == "" || $user_details['fld_address'] == "" || $user_details['fld_zip'] == "" || $user_details['fld_city'] == "" || $user_details['fld_state'] == "" || $user_details['fld_country'] == "") {
			$oregister->redirect('edit_profile.php?error=1');
		}
	} else {
		if ($user_details['fld_name'] == "" || $user_details['fld_lname'] == "" || $user_details['fld_address'] == "" || $user_details['fld_zip'] == "" || $user_details['fld_city'] == "" || $user_details['fld_state'] == "" || $user_details['fld_country'] == "") {
			$oregister->redirect('edit_profile.php?error=1');
		}
		if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
			$oregister->redirect('dashboard.php');
		}
	}
}
$sPageName = '<li>Generate Link</li>';
$sGenerateLink = 'active';

$linkedgenerate = 0;
function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$rid = base64_encode($_SESSION['role_id']);
$uid = base64_encode($_SESSION['uname']);
$generatedlink3 = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$generatedlink2 = str_replace('/cms', '', $generatedlink3);
$generatedlink1 = str_replace('generate_link', 'signup', $generatedlink2);
$generatedlink = '';
$thankyou = '';
$refferalasa = "";
if (array_key_exists('generatesubmit', $REQUEST)) {
	$generated_hash = generateRandomString();
	if ($REQUEST['fld_role_id'] == 2) {
		$refferalasa = 'Campaign Manager';
	} elseif ($REQUEST['fld_role_id'] == 3) {
		$refferalasa = 'Distributor';
	} elseif ($REQUEST['fld_role_id'] == 6) {
		$refferalasa = 'Representative';
	}
	$groleid = $REQUEST['fld_role_id'];
	$uid = $_SESSION['uid'];
	$uname = $_SESSION['uname'];
	$ulname = $_SESSION['ulname'];
	$oregister->generated_hash($uid, $uname, $ulname, $groleid, $generated_hash);
	$generatedlink3 = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$generatedlink2 = str_replace('/cms', '', $generatedlink3);
	$generatedlink1 = str_replace('generate_link', 'signup', $generatedlink2);
	$generatedlink = $generatedlink1.'?ref='.$generated_hash;
}

if (array_key_exists('emailsubmit', $REQUEST)) {
	include_once('classes/class.phpmailer.php');
	$iId = $_SESSION['uid'];
	$aUserDetail = $oregister->getuserdetail($iId);
	$user_name = $aUserDetail['fld_name'];
	$ufname = $aUserDetail['fld_name'];
	$ulname = $aUserDetail['fld_lname'];
	$uemail = $aUserDetail['fld_email'];
	$uphone = $aUserDetail['fld_phone'];
	
	$refferallink = $REQUEST['refferallink'];
	$linktype = $REQUEST['refferalasa'];
	$emailfname = $REQUEST['emailfname'];
	$emaillname = $REQUEST['emaillname'];
	$email_from = $REQUEST['emailfrom'];
	$email_to = $REQUEST['emailto'];
	$email_cc = $REQUEST['emailcc'];
	$email_msg = $REQUEST['emailmsg'];
	//$generatedlink = $refferallink;
	
	$chkgeneratelink = $oCampaign->generate_link($iId, $ufname, $ulname, $uemail, $refferallink, $linktype, $emailfname, $emaillname, $email_from, $email_to, $email_cc, $email_msg);
	
	if ($chkgeneratelink == 1) {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>You have successfully generated the refferal link and emailed...!
			</div>';
	} else {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Error: Message not sent...!
			</div>';
	}
	header("Location: generate_link.php");
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
<title>Admin<?php echo sWEBSITENAME;?> - Generate Link</title>
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
		 <h1 class="h1styling">Generate Link</h1>
		 <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box">
		  
			<form id="frmCampaign" name="frmCampaign" method="post" data-toggle="validator" >
			  <div class="form-group col-md-12">
				<div style="padding-left:0px;font-size:16px;font-weight:bold;padding-bottom:10px;color:#868484; font-family: 'Open Sans';margin-top: 8px;">Role</div>
				<select class="form-control colorMeBlue noValue" name="fld_role_id" id="fld_role_id" required>
				  <option value="">Select Role</option>
				  <?
				  if ($_SESSION['role_id'] == 1) { //Administrator
 					$getvalue = '2,3,6';
				  } elseif ($_SESSION['role_id'] == 3) { //Distributor
					$getvalue = '2,6';
				  } elseif ($_SESSION['role_id'] == 6) { //Representative
					$getvalue = '2';
				  }
				
				  $sRoleData = $oregister->getgeneratelink($getvalue);
				  $iCountRecords = count($sRoleData);
				  if($iCountRecords>0){
  					for($i=0;$i<$iCountRecords;$i++){
				  ?>
				  <option value="<?=$sRoleData[$i]['fld_role_id']?>" <? if(isset($REQUEST['fld_role_id']) && $sRoleData[$i]['fld_role_id'] == $REQUEST['fld_role_id']){?> selected<? }?>><?=$sRoleData[$i]['fld_role']?></option>
				  <? }}?>                  
			    </select>
				<div class="help-block with-errors"></div>
			  </div>
			  <div class="clearfix"></div>
			  <div class="col-lg-12">
			    <input type="hidden" name="refferallink" id="refferallink" value="<?=$generatedlink;?>">
				<div class="col-lg-6" style="padding-right:40px">
				  <button class="btn btn-primary waves-effect waves-light" name="generatesubmit" id="generatesubmit" type="submit"><span class="btn-label" ><i class="fa fa-check"></i></span>Generate Link</button>
				</div>
				<div class="col-lg-6">
				  <?php 
				  if (array_key_exists('generatesubmit', $REQUEST)) { 
					$linkedgenerate = 1;
				  ?>				  
				  <? } ?>
				  <button class="btn btn-success waves-effect waves-light emailtargeted" style="width:90%; display:none" type="button" data-toggle="modal" data-target="#myModal">Send Email <span class="btn-label forright-icon"><i class="fa fa-envelope-o"></i></span></button>
				</div>
			  </div>
			  <div class="col-lg-12">
			    <div><?=$thankyou;?></div>
		      </div>
			  <div class="clearfix"></div>
			</form>
		  </div>
		  <div style="clear:both"></div>
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
<form id="frmgenerateemail" name="frmgenerateemail" data-toggle="validator" method="post" action="">
			  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4 class="modal-title" id="myModalLabel">Send Email</h4>
					</div>
					<div class="modal-body">
					  <?php
					  $iId = $_SESSION['uid'];
					  $aUserDetail = $oregister->getuserdetail($iId);
					  $user_email = $aUserDetail['fld_email'];
					  ?>
					  <div class="form-group col-md-12">
						<label for="emailcc">Email From<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailfrom" name="emailfrom" placeholder="Enter Email From" value="<?=$user_email;?>" required>
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="emailfname">First Name<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailfname" name="emailfname" placeholder="Receiver's First Name" required>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="emaillname">Last Name</label>
						<input type="textbox" class="form-control" id="emaillname" name="emaillname" placeholder="Receiver's Last Name">
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-6">
						<label for="emailto">Email To<span style="color:#FF0000">*</span></label>
						<input type="textbox" class="form-control" id="emailto" name="emailto" placeholder="Enter Email To" required>
						<div class="help-block with-errors"></div>
					  </div> 
					  <div class="form-group col-md-6">
						<label for="emailcc">Email CC</label>
						<input type="textbox" class="form-control" id="emailcc" name="emailcc" placeholder="Enter Email CC">
						<div class="help-block with-errors"></div>
					  </div>
					  <div class="clearfix"></div>
					  <div class="form-group col-md-12">
						<label for="emailmsg">Message</label>
						<textarea class="form-control" id="emailmsg" rows="5" name="emailmsg"></textarea>
						<div class="help-block with-errors"></div>
					  </div>
					  <div style="clear:both"></div>
					  <div class="col-lg-12">
						<div>Generated Role Type: <?=$refferalasa;?></div>
						<div>Refferal Link: <a href="<?=$generatedlink;?>"><?=$generatedlink;?></a></div>
						<div><?=$thankyou;?></div>
					  </div>
					  <div class="clearfix"></div>
					</div>
					<div style="clear:both"></div>
					<div class="modal-footer">
					  <input type="hidden" name="refferallink" id="refferallink" value="<?=$generatedlink;?>">
					  <input type="hidden" name="refferalasa" id="refferalasa" value="<?=$refferalasa;?>">
					  <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal"><span class="btn-label"><i class="fa fa-chevron-left"></i></span> Close</button>
					  <button type="submit" name="emailsubmit" id="emailsubmit" class="btn btn-success waves-effect waves-light">Send message <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
					</div>
				  </div>
				</div>
			  </div>
			</form>
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
<script src="js/validator.js"></script>

<script type="text/javascript">
setTimeout(function(){ 
	var linkedgenerate = <?=$linkedgenerate;?>;
	if (linkedgenerate == 1) {
		//$('.emailtargeted').show();
		$('.emailtargeted').click();
	} else {
		$('.emailtargeted').hide();
	}
}, 500);

   $('#notifications').delay(3000).fadeOut('slow');
   $('select').on('change', function(){
    var $this = $(this);
    
    if (!$this.val()) {
        $this.addClass('noValue');
    } else {
        $this.removeClass('noValue');
    }
});
</script>
</body>
</html>
<? include_once('bottom.php');?>