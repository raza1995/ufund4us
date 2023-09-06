<?
if (strtolower($_SERVER['HTTPS']) != 'on') {
    exit(header("location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}"));
}
require_once("configuration/dbconfig.php");
$msg1 = '';
// Google Recaptcha
require('lib/src/autoload.php');
$siteKey = CAPTCH_SITE_KEY;
$secret = CAPTCH_SECRET;
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
// Google Recaptcha
if (isset($_GET['ref'])) {
	$ref = xss_clean($_GET['ref']);
} else {
	$ref = '';
}
if (isset($_GET['refferalid'])) {
	$refferalid = xss_clean($_GET['refferalid']);
} else {
	$refferalid = '';
}
if (isset($_GET['cid'])) {
	$cid = xss_clean($_GET['cid']);
} else {
	$cid = '';
}
$emailexists = 0;
if(array_key_exists('btnsubmit', $_POST))
{
	$sName = xss_clean($_POST['name']);
	$sLName = xss_clean($_POST['lname']);
	$sEmail = xss_clean($_POST['email']);
	$sPassword = xss_clean($_POST['password']);
	$sRoleId = 2; //Campaign Manager as a Default
	$sPassword = $oregister->encrypt($sPassword,sENC_KEY);
	$gRecaptchaResponse = $_POST['g-recaptcha-response']; //google captcha post data
	$remoteIp = $_SERVER['REMOTE_ADDR']; //to get user's ip
	$recaptchaErrors = ''; // blank varible to store error
	$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp); //method to verify captcha
	if ($resp->isSuccess()) {
		$userexists = $oregister->getuserdetailemail($sEmail);
		if ($userexists['userexists'] == 0) {
			$emailexists = 0;
			try
			{
				if ($sName != '' && $sEmail != '' && $sPassword != '' && $sLName != '') {
					if (isset($_GET['refferalid']) && isset($_GET['cid'])) {
						$sRoleId = 5; //for participants
						if($oregister->register2($sName,$sLName,$sEmail,$sPassword,$sRoleId,$refferalid,$cid))
						{
							if ($refferalid != '' && $cid != '') {
								$userredirect = $oCampaign->user_redirect($refferalid,$cid);
								$uid = $userredirect['uid'];
								$oregister->redirect('sign-in.php?redirecturl=cms/participant_build_team.php&cid='.$cid.'&uid='.$uid.'');
							} else {
								$oregister->redirect('thanks.php?m=2');
							}
						}
					} else {
						if ($ref != '') {
							if($oregister->register($sName,$sLName,$sEmail,$sPassword,$sRoleId,$ref))	
							{
								if ($ref != '') {
									$oregister->redirect('sign-in.php');
								} else {
									$oregister->redirect('thanks.php?m=2');
								}
							}
						} else {
							$chk_emailsent = $oregister->emailtoadmin($sName,$sLName,$sEmail,$sPassword,$sRoleId);
							if($chk_emailsent == 1)	
							{
								if ($ref != '') {
									$oregister->redirect('sign-in.php');
								} else {
									$oregister->redirect('thanks.php?m=10');
								}
							}
						}
					}
				} 
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		} else {
			$emailexists = 1;
			$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Please review the form</p>';
		}
	} else {
		$recaptchaErrors = $resp->getErrorCodes(); // set the error in varible
		if(isset($recaptchaErrors[0])) {
			$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Invalid captcha, please verify the captcha</p>';
		}
	}
	if (isset($_GET['msg'])) {
		$msg1 = '<p style="font-size:14px; text-align:center"><b>Error:</b> Please review the form</p>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Sign Up</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<link href="cms/bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script><style></style>
<script src='https://www.google.com/recaptcha/api.js'></script>
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
			<? if ($msg1 != '') { ?>
			<div class="colmd_12 form-group">
				<?=$msg1;?>
			</div>
			<? } 

			$sName = isset($sName) ? $sName : '';
			$sLName = isset($sLName) ? $sLName : '';
			$sEmail = isset($sEmail) ? $sEmail : '';

			?>
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
			  
			  <div class="colmd_12 form-group">
				  <div class="g-recaptcha" data-sitekey="<?php echo CAPTCH_SITE_KEY;?>"></div>
			  </div>
			  
			  <div class="colmd-12"style="position: relative;">
				<!--<a href="#" class="btn-2" >Sign Up <span class="fa fa-chevron-right"></span></a>-->
				<input type="hidden" name="_hidCheckSubmit" id="_hidCheckSubmit" value="1" />
				<input type="submit" name="btnsubmit" value="Sign Up"  class="btn-2" /><span class="fa fa-chevron-right"></span>
				<p class="newdiv newdiv3" >Already have an account? <a href="sign-in.php">Click here to login</a></p>
			  </div>
			</form>
		  </div>
		</div>
		<div align="center"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="images/Help icon.jpg" width="80px" /><br>Need help click here</a></div>
	  </div>
	</div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="yt-player">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
<script type="text/javascript">
	var tag = document.createElement('script');

  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('yt-player', {
          height: '390',
          width: '100%',
          videoId: 'PJF14HzgTEc',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
</script>
</body></html>