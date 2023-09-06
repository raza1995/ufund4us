<?
require_once("configuration/dbconfig.php");
$cuid = $_GET['cuid'];
$cid = $_GET['cid'];
if ($cid != '' && $cuid != '') {
	
} else {
	$oregister->redirect('join_campaign.php');
}
//$emailexists = 1;
if(isset($_POST['_hidCheckSubmit']) == "1")
{
	$sName = trim($_POST['name']);
	$sLName = trim($_POST['lname']);
	$sEmail = trim($_POST['email']);
	$sPassword = trim($_POST['password']);
	$cid = trim($_POST['cid']);
	$cuid = trim($_POST['cuid']);
	$sRoleId = 5; //Campaign Manager as a Default
	$sPassword = $oregister->encrypt($sPassword,sENC_KEY);
	
	$userexists = $oregister->getuserdetailemail($sEmail);
	if ($userexists['userexists'] == 0) {
		$emailexists = 0;
		try
		{
			if($uid = $oregister->register3($sName,$sLName,$sEmail,$sPassword,$sRoleId,$cid,$cuid))
			{
				//$lastid
				$oregister->redirect('sign-in.php?redirecturl=cms/participant_build_team.php&cid='.$cid.'&uid='.$uid.'');
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	} else {
		$emailexists = 1;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Create User</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<link href="cms/bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script><style></style>

</head>

<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
  <div class="container newContainer">
	<div class="mid_sec2">
	  <div class="mid_secin">
		<div class="row2">
		  <!--<div class="line">
		  </div>-->
		  <h3>Welcome to <?php echo sWEBSITENAME;?>!</h3>
		  <p>Create your account, it takes less than a minute.</p>
		</div>
		<div class="formdiv-in2">
		  <p>Create an account</p>
		  <div class="divin">
			<form method="post" name="frmRegister" id="frmRegister" data-toggle="validator" action="">
			  <div class="colmd_12 form-group">
				<div class="label input-labal">
				  <input type="text" placeholder="First Name" class="formdivtext" required  name="name" id="name" value="<?=$sName;?>" />
				  <span class="fa fa-user"></span>
				</div>
				<div class="help-block with-errors"></div>
			  </div>
			  
			  <div class="colmd_12 form-group">
				<div class="label input-labal">
				  <input type="text" placeholder="Last Name" class="formdivtext" name="lname" id="lname" value="<?=$sLName;?>" />
				  <span class="fa fa-user"></span>
				</div>
				<div class="help-block with-errors"></div>
			  </div>
			  
			  <div class="colmd_12 form-group">
				<div align="center" id="emailerror" style="display:none">Email Address has already in Use</div>
				<div class="label input-labal">
				  <input type="email" placeholder="Email" class="formdivtext" required name="email" id="email" value="<?=$sEmail;?>" />
				  <span class="fa fa-envelope"></span>
				</div>
				<div class="help-block with-errors"></div>
			  </div>
			  
			  <div class="colmd_12 form-group">
				<div class="label input-labal">
				  <input type="password" placeholder="Password" class="formdivtext"  required  name="password" id="password"  />
				  <span class="fa fa-lock"></span>
				</div>
				<div class="help-block with-errors"></div>
			  </div>
			  
			  <div class="colmd_12 form-group">
				<div class="label input-labal">
				  <input type="password" placeholder="Confirm Password" class="formdivtext"  required data-match="#password" data-match-error="Whoops, password doesnt match" name="cpassword" id="cpassword" />
				  <span class="fa fa-lock"></span>
				</div>
				<div class="help-block with-errors"></div>
			  </div>
			  
			  <div class="colmd-12"style="position: relative;">
				<!--<a href="#" class="btn-2" >Sign Up <span class="fa fa-chevron-right"></span></a>-->
				<input type="hidden" name="_hidCheckSubmit" id="_hidCheckSubmit" value="1" />
				<input type="hidden" name="cuid" id="cuid" value="<?=$cuid;?>" />
				<input type="hidden" name="cid" id="cid" value="<?=$cid;?>" />
				<input type="submit" value="Sign Up"  class="btn-2" /><span class="fa fa-chevron-right"></span>
				<p class="newdiv newdiv3" >Already have an account? <a href="sign-in.php?cuid=<?=$cuid;?>&cid=<?=$cid;?>&action=joincampaign">Click here to login</a></p>
			  </div>
			</form>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</section>  
<? include_once('footer.php');?>
<script src="cms/bower_components/sweetalert/sweetalert.min.js"></script>
<script src="cms/bower_components/sweetalert/jquery.sweet-alert.custom.js"></script>
<script src="cms/js/validator.js"></script>
<script>
 var emailexists = <?=$emailexists;?>;
 if (emailexists == 0) {
	 $("#emailerror").hide();
 } else {
	 //swal("Email Address Error", "The email address already in use, Please type another email.");
	 $("#emailerror").show();
	 
 }
</script>
</body></html>