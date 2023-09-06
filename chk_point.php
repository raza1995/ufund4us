<?php
if (strtolower($_SERVER['HTTPS']) != 'on') {
    exit(header("location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}"));
}
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
checkAndSetInArray($REQUEST, 'name', '');
checkAndSetInArray($REQUEST, 'redirecturl', '');
checkAndSetInArray($REQUEST, 'email', '');
checkAndSetInArray($REQUEST, 'uid', 0);
checkAndSetInArray($REQUEST, 'chkpoint', '');



if ($REQUEST['chkpoint'] == '') {
	$oregister->redirect('sign-in.php');
}

//Base64Decoded
$single_string = $REQUEST['chkpoint'];
$single_string_decoded = base64_decode($single_string);
parse_str($single_string_decoded, $query_string);

$error = '';
if (xss_clean($query_string['action']) == 'joincampaign' && isset($query_string['cid']) && isset($query_string['cuid'])) {
	$cid = xss_clean($query_string['cid']);
	$cuid = xss_clean($query_string['cuid']);
}

if (isset($query_string['redirecturl']) && isset($query_string['cid']) && isset ($query_string['uid'])) {
	$refurl1 = xss_clean($query_string['redirecturl']);
	$refurl2 = xss_clean($query_string['cid']);
	$refurl3 = xss_clean($query_string['uid']);
	$redirecturl = 'cms/participant_build_team.php?cid='.$refurl2.'&uid='.$refurl3.'';
} else {
	$redirecturl = '';
}
$email = $query_string['sEmail'];
$chkcredentials = $oregister->user_chk_credentials($email);

if(isset($REQUEST['_hidCheckSubmit']) == "1")
{
	$pLogin = 0;
	if (isset($REQUEST['plogin']) && $REQUEST['plogin'] == 1) {
		$pLogin = 1;
		$sPassword = xss_clean($REQUEST['password']);
	} else {
		//$pLogin = 0;
		$sPassword = xss_clean($REQUEST['password']);
		$sPassword = $oregister->encrypt($sPassword,sENC_KEY);
	}
	$allLogin = 0;
	if (isset($REQUEST['alllogin']) && $REQUEST['alllogin'] == 1) {
		$allLogin = 1;
		$sPassword = xss_clean($REQUEST['password']);
	} elseif ($pLogin == 0) {
		//$allLogin = 0;
		$sPassword = xss_clean($REQUEST['password']);
		$sPassword = $oregister->encrypt($sPassword,sENC_KEY);
	}
	
	$sName = xss_clean($REQUEST['name']);
	$redirecturl = xss_clean($REQUEST['redirecturl']);
	$sEmail = xss_clean($REQUEST['email']);
	$sUid = xss_clean($REQUEST['uid']);

	try
	{
		$chklogin = $oregister->user_uid_login($sUid,$sEmail,$sPassword,$pLogin,$allLogin);
		if($chklogin > 0)	
		{
			if (isset($REQUEST['cid']) && isset($REQUEST['cuid'])) {
				$uid = $_SESSION['uid'];
				$cid = xss_clean($REQUEST['cid']);
				$cuid = xss_clean($REQUEST['cuid']);
				$validate = $oregister->join_campaign($uid,$cid,$cuid,$sEmail);
				if ($validate == 0) { // success
					$oregister->redirect("cms/participant_build_team.php?cid=$cid&uid=$uid");
				} else { //already exists
					$oregister->redirect('join_campaign.php?msg=alreadyjoined');
				}
			} else {
				if (isset($chklogin['fld_ftime']) && $chklogin['fld_ftime'] == 1) {
					if (isset($redirecturl) && $redirecturl != '') {
						//$oregister->redirect($redirecturl);
						$oregister->update_ftime($chklogin['fld_uid']);
						$oregister->redirect('cms/edit_profile.php?cid='.$refurl2.'&uid='.$refurl3.'');
					} else {
						$oregister->update_ftime($chklogin['fld_uid']);
						$oregister->redirect('cms/edit_profile.php');
					}
				} else {
					$oregister->redirect('cms/dashboard.php');
				}
			}
		}else{
			$error = "Login details incorrect or account is not active.";
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Sign In</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>  
<link href="css/style.css" rel="stylesheet" type="text/css"> 

<link href="css/ninja-slider.css" rel="stylesheet" type="text/css">
<script src="js/ninja-slider.js" type="text/javascript"></script>
<script>
function FormSubmit(){
	document.frmLogin.submit();
}
</script>
</head>

<body>
<? include_once('header.php');?>
<section class="ipcontentsection">
<div class="container newContainer">
<div class="mid_sec2">
	<div class="mid_secin">
		<div class="row2">
			<h3>Welcome to <?php echo sWEBSITENAME; ?>!</h3>
			<p> Don't have an account?  <a href="signup.php">Sign Up</a></p>
			<? if($error){?><p align="center" style="color:#FF0000"><?=$error?></p><? }?>
		</div>
		<div class="formdiv-in2">
			<p>Sign In - Checkpoint</p>
			<div class="divin">
				<form name="frmLogin" id="frmLogin" method="post" action="">
					<?php foreach ($chkcredentials as $creddata) { 
						$role_name = '';
						if ($creddata['fld_role_id'] == 1) {
							$role_name = 'Administrator';
						} elseif ($creddata['fld_role_id'] == 2) {
							$role_name = 'Campaign Manager';
						} elseif ($creddata['fld_role_id'] == 3) {
							$role_name = 'Distributor';
						} elseif ($creddata['fld_role_id'] == 5) {
							$role_name = 'Participants';
						} elseif ($creddata['fld_role_id'] == 6) {
							$role_name = 'Representative';
						} elseif ($creddata['fld_role_id'] == 7) {
							$role_name = 'Demo';
						}
					?>
					<div class="colmd_12">
						<label class="label" for="uid<?=$creddata['fld_uid'];?>">
							<!--<input readonly type="text" class="formdivtext" style="float:right;"  name="fakeemail" id="fakeemail" value="<?=$creddata['fld_name']?> <?=$creddata['fld_lname']?> (<?=$role_name?>)" />-->
							<span class="formdivtext" style="border:none;padding:0px 12px; text-align:left; float:right;"><?=$creddata['fld_name']?> <?=$creddata['fld_lname']?> (<?=$role_name?>)</span>
							<span style="border-left:0px;border-right:1px solid #d9d6d6;padding: 0px 10px;">
								<input type="radio" style="margin-top: 11px;-ms-transform: scale(1.5);-webkit-transform: scale(1.5);" placeholder="Email" name="uid" id="uid<?=$creddata['fld_uid'];?>" value="<?=$creddata['fld_uid'];?>" />
							</span>
						</label>
					</div>
					<?php } ?>
					<div class="colmd_12">
						<div class="label">
							<input type="password" placeholder="Password" class="formdivtext"  name="password" id="password" required/>
							<span class="fa">
								<a href="javascript:void(0);" id="hideShowPwd"  style="font-size:20px;" alt="Hide Password" title="Hide Password" onClick="hideShowPassword()">
									<i class="fa fa-eye"></i>
								</a>
							</span>   
						</div>
					</div>
					<div class="colmd-12" style="position: relative;">
						<input type="hidden" name="_hidCheckSubmit" id="_hidCheckSubmit" value="1" />
						<input type="hidden" name="redirecturl" id="redirecturl" value="<?=$redirecturl;?>" />
						<input type="hidden" name="email" id="email" value="<?=$email;?>" />
						<? if ($query_string['action'] == 'joincampaign' && isset($query_string['cid']) && isset($query_string['cuid'])) { ?>
							<input type="hidden" name="cid" id="cid" value="<?=$cid;?>" />
							<input type="hidden" name="cuid" id="cuid" value="<?=$cuid;?>" />
						<? } ?>
						<input type="submit" value="Sign In"  class="btn-2" /><span class="fa fa-chevron-right"></span>
						<!--<a href="javascript:void" class="btn-2" onclick="FormSubmit()">Sign In <span class="fa fa-chevron-right"></span></a>-->
						<!--<a href="forgot-password.php">   <p class="newdiv" >Forgot  password?</p></a>-->
					</div>
					<div class="colmd-12" style="margin-top: 10px; position: relative;">
						<span class="fa fa-chevron-left"></span><input style="width:100%" type="button" value="Cancel" onclick="window.location.href='sign-in.php'" class="btn-2" />
						<!--<a href="javascript:void" class="btn-2" onclick="FormSubmit()">Sign In <span class="fa fa-chevron-right"></span></a>-->
						<a href="forgot-password.php">   <p class="newdiv" >Forgot  password?</p></a>
					</div>
				</form>
			</div>
		</div>
		<div align="center"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="images/Help icon.jpg" width="80px" /><br>Need help click here</a></div>
	</div>
</div>
<!-- Start Modal For Tutorial-->
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
      <!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" id="yt-player"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal For Tutorial-->
</div>
</section>  
<? include_once('footer.php');?>
<script>
function hideShowPassword() {
    var userPassword = $("#password");
    if(userPassword.val() != "") {
        if($(userPassword).attr('type') == "password") {
			$(userPassword).attr('type', 'text');
            $("#hideShowPwd").removeClass('green').addClass('red');
            $("#hideShowPwd").attr('alt', 'Hide Password');
            $("#hideShowPwd").attr('title', 'Hide Password');
        } else {
            $(userPassword).attr('type', 'password');
            $("#hideShowPwd").removeClass('red').addClass('green');
            $("#hideShowPwd").attr('alt', 'Show Password');
            $("#hideShowPwd").attr('title', 'Show Password');
        }
    } else {
        //$("#userPasswordError").css('color', 'red').text('First Enter Password');
    }
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
          videoId: 'xVExXw3VoKE',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
</script>
</body></html>