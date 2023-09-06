<? if (strtolower($_SERVER['HTTPS']) != 'on') {
    exit(header("location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}"));
}
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;

checkAndSetInArray($REQUEST, 'name', '');
checkAndSetInArray($REQUEST, 'redirecturl', '');
checkAndSetInArray($REQUEST, 'email', '');
checkAndSetInArray($REQUEST, 'password', '');
checkAndSetInArray($REQUEST, 'cid', '');
checkAndSetInArray($REQUEST, 'cuid', '');

// echo "<pre>"; print_r($REQUEST); die();

$error = '';
$action = '';
if ( isset($REQUEST['action']) && xss_clean($REQUEST['action']) == 'joincampaign' && isset($REQUEST['cid']) && isset($REQUEST['cuid'])) {
	$action = 'joincampaign';
	$cid = xss_clean($REQUEST['cid']);
	$cuid = xss_clean($REQUEST['cuid']);
}

if (isset($REQUEST['redirecturl']) && isset($REQUEST['cid']) && isset ($REQUEST['uid'])) {
	$refurl1 = xss_clean($REQUEST['redirecturl']);
	$refurl2 = xss_clean($REQUEST['cid']);
	$refurl3 = xss_clean($REQUEST['uid']);
	$redirecturl = 'cms/participant_build_team.php?cid='.$refurl2.'&uid='.$refurl3.'';
} else {
	$redirecturl = '';
}


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

	try
	{
		$chk_multi_credential = $oregister->user_chk_login($sEmail,$sPassword);
    // echo "<pre>"; var_dump($chk_multi_credential); die();
		if($chk_multi_credential > 0) { // Multi-Credentials
			$single_string_decoded = "sEmail=$sEmail&pLogin=$pLogin&allLogin=$allLogin&action=".$action."&cid=".$REQUEST['cid']."&cuid=".$REQUEST['cuid']."&redirecturl=$redirecturl"; //Create base64 encode
			$single_string_encoded = base64_encode($single_string_decoded);
			$oregister->redirect('chk_point.php?chkpoint='.$single_string_encoded.'');
		} else { //Single-Credential
			$chklogin = $oregister->user_login($sEmail,$sPassword,$pLogin,$allLogin);
      // echo "<pre>"; var_dump($chklogin); die();
			if($chklogin > 0)	
			{
				/*if (isset($redirecturl) && $redirecturl != '') {
					$oregister->redirect($redirecturl);
				} else {*/
				if ( $REQUEST['cid'] > 0 && $REQUEST['cuid'] > 0 ) {
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
			//}
			}else{
				$error = "Login details incorrect or account is not active.";
			}
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0031)<?php echo SITE_URL;?>contact-us/ -->
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
<!--<ul id="breadcrumbs" class="breadcrumbs"><li class="item-home"><a class="bread-link bread-home" href="<?php echo SITE_URL;?>" title="Homepage">Home</a></li><li class="separator separator-home"> &gt; </li><li class="item-current item-10"><strong class="bread-current bread-10"> Start New Campaign</strong></li></ul>  <div class="col-md-12 innerpageh1">
  
  
    <h1 class="h1styling">Sign In</h1>    
    <div style="width:100%; height:300px;background:#F1F1F1"></div>
  
    </div>-->
    <!----------mid_sec------------>

<div class="mid_sec2">
<div class="mid_secin">
<div class="row2">
<!--<div class="line_div">
</div>-->
<!--<div class="line">
</div>-->
<h3>Welcome to <?php echo sWEBSITENAME;?>!</h3>
<p> Don't have an account?  <a href="signup.php">Sign Up</a></p>
<? if($error){?><p align="center" style="color:#FF0000"><?=$error?></p><? }?>
</div>
<div class="formdiv-in2">
<p>Sign In</p>
<div class="divin">

   <form name="frmLogin" id="frmLogin" method="post" action="">
   
   <div class="colmd_12">
   <div class="label">
   <input type="email" placeholder="Email" class="formdivtext"  name="email" id="email"  required/>
   <span class="fa fa-envelope">
   </span>
 </div>
   </div>
   
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
   <? if( isset($REQUEST['action']) && $REQUEST['action'] == 'joincampaign' && isset($REQUEST['cid']) && isset($REQUEST['cuid'])) { ?>
   <input type="hidden" name="cid" id="cid" value="<?=$cid;?>" />
   <input type="hidden" name="cuid" id="cuid" value="<?=$cuid;?>" />
   <? } ?>
   <input type="submit" value="Sign In"  class="btn-2" /><span class="fa fa-chevron-right"></span>
    <!--<a href="javascript:void" class="btn-2" onclick="FormSubmit()">Sign In <span class="fa fa-chevron-right"></span></a>-->
    <a href="forgot-password.php">   <p class="newdiv" >Forgot  password?</p></a>
   
    </div>
   </form>
   </div>
   </div>
   <div align="center"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="images/Help icon.jpg" width="80px" /><br>Need help click here</a></div>
</div>
</div>
<!----------mid_sec------------>
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

</div>
</section>  

<!--<section class="lightgreyformbg">
  <div class="container">
  <div class="col-md-12 footer">
    <h2 class="footermainh2">A old quote by Benjamin Franklyn sums up why 99% of all fundraising efforts fail to reach their goals</h2>
	<h2 class="footersecondh2">"Failure to Plan is Planning to Fail"</h2>
    <h2 class="getintouchh2">Get in Touch With Us</h2>
  </div>
  </div>
</section>

<section class="lightgreyformbg">
<div class="container">
  <div class="col-md-offset-2"> 
<div class="col-md-6 formbox">  
<form role="form" action="<?php echo SITE_URL;?>wp-content/themes/customtheme/handlers/formContact.php" data-captcha="no" class="pi-contact-form">
<div class="pi-error-container"></div>

<div class="form-group col-md-6">
    <input type="text" class="form-control form-control-name input-lg" id="exampleInputName-11" placeholder="Your Name">
</div>
 
<div class="form-group col-md-6">
    <input type="email" class="form-control form-control-email input-lg" id="exampleInputEmail-11" placeholder="Email address">
</div>

<div class="form-group col-md-12">
<label for="Message" class="messagelabel">Message</label>
<textarea class="form-control form-control-comments" id="exampleInputMessage-11" placeholder="How can we help?" rows="4" style="height: 116px;"></textarea>
</div>
  
<div class="form-group col-md-4">
<button type="submit" class="btn btn-primary btn-block submitbtstyle">SUBMIT</button>
</div>   
</form>	
</div>
</div>
  <div class="col-md-4 address">
  <h2 class="addressh2">Address Info</h2>
  <p><i class="fa fa-phone"></i> 123-456-7890</p>
  <p><i class="fa fa-envelope"></i> <?php echo INFO_EMAIL;?></p>
  </div> 
</div>

</section>
-->
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