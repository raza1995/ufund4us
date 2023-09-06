<?
require_once("../configuration/dbconfig.php");
$REQUEST = $_REQUEST;
$sStartCampMenu = '';
$iId = $_SESSION['uid'];
// echo die($iId);
$error = 0;

//Declare basic variable
checkAndSetInArray($REQUEST, 'fld_cname', '');
checkAndSetInArray($REQUEST, 'fld_name', '');
checkAndSetInArray($REQUEST, 'fld_lname', '');
checkAndSetInArray($REQUEST, 'fld_phone', '');
checkAndSetInArray($REQUEST, 'fld_address', '');
checkAndSetInArray($REQUEST, 'fld_city', '');
checkAndSetInArray($REQUEST, 'fld_state', '');
checkAndSetInArray($REQUEST, 'fld_country', '');
checkAndSetInArray($REQUEST, 'fld_zip', '');
checkAndSetInArray($REQUEST, 'fld_email', '');
checkAndSetInArray($REQUEST, 'fld_password', '');





if (isset ($REQUEST['error'])) {
	$error = 1;
}

if (isset ($REQUEST['cid']) && isset ($REQUEST['uid'])) {
	$refurl2 = $REQUEST['cid'];
	$refurl3 = $REQUEST['uid'];
	$redirecturl = 'participant_build_team.php?cid='.$refurl2.'&uid='.$refurl3.'';
} else {
	$redirecturl = '';
}

require_once( '../lib/Facebook/FacebookSession.php');
require_once( '../lib/Facebook/FacebookRequest.php' );
require_once( '../lib/Facebook/FacebookResponse.php' );
require_once( '../lib/Facebook/FacebookSDKException.php' );
require_once( '../lib/Facebook/FacebookRequestException.php' );
require_once( '../lib/Facebook/FacebookRedirectLoginHelper.php');
require_once( '../lib/Facebook/FacebookAuthorizationException.php' );
require_once( '../lib/Facebook/GraphObject.php' );
require_once( '../lib/Facebook/GraphUser.php' );
require_once( '../lib/Facebook/GraphSessionInfo.php' );
require_once( '../lib/Facebook/Entities/AccessToken.php');
require_once( '../lib/Facebook/HttpClients/FacebookCurl.php' );
require_once( '../lib/Facebook/HttpClients/FacebookHttpable.php');
require_once( '../lib/Facebook/HttpClients/FacebookCurlHttpClient.php');
	
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookCurl;

//check if users wants to logout
if(isset($REQUEST['logout'])){
	unset($_SESSION['fb_token']);
}
//2.Use app id,secret and redirect url 
$app_id = FACEBOOK_APP_ID;
$app_secret = FACEBOOK_APP_SECRET;
$redirect_url=SITE_URL.'app/cms/edit_profile.php';
//3.Initialize application, create helper object and get fb sess
FacebookSession::setDefaultApplication($app_id,$app_secret);
$helper = new FacebookRedirectLoginHelper($redirect_url);
$sess = $helper->getSessionFromRedirect();
//check if facebook session exists
if(isset($_SESSION['fb_token'])){
 	$sess = new FacebookSession($_SESSION['fb_token']);
}
//logout
$logout = SITE_URL.'app/cms/logout.php';
//4. if fb sess exists echo name 
if(isset($sess)){
	//store the token in the php session
	$_SESSION['fb_token']=$sess->getToken();
	//create request object,execute and capture response
	$request = new FacebookRequest($sess,'GET','/me');
	// from response get graph object
	$response = $request->execute();
	$graph = $response->getGraphObject(GraphUser::classname());
	// use graph object methods to get user details
	$name = $graph->getName();
	$id = $graph->getId();
	$image = 'https://graph.facebook.com/'.$id.'/picture?width=300';
	$profiledirectory = 'uploads/profilelogo/';
	$email = $graph->getProperty('email');
	$data = file_get_contents($image);
	$fileName = $id.'.jpg';
	$file = fopen($profiledirectory.$fileName, 'w+');
	fputs($file, $data);
	fclose($file);
	include('php/class.thumbnail.php');
	$resized = makeThumbnails($profiledirectory, $profiledirectory, $fileName);
	$oregister->update_profile_image($fileName,$iId);
	$oregister->redirect('edit_profile.php');
	unset($_SESSION['fb_token']);
}else{
	//else echo login
	$loginwithfb = $helper->getLoginUrl(array('email'));
}

$sPageName = '<li>Edit Profile</li>';

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
	$sPageName = '<li>Edit Profile</li>';
	$aUserDetail = $oregister->getuserdetail($iId);
	
	checkAndSetInArray($user_details,'fld_image', '');

	checkAndSetInArray($aUserDetail, 'fld_image', '');
	//Logo Code Start
	$fld_campaign_logo = $aUserDetail['fld_image'];
	$directory = sHOMESCMS.'uploads/profilelogo/';
	$ext_logo = pathinfo($directory.$fld_campaign_logo, PATHINFO_EXTENSION);
	$size_logo = filesizeWithoutError('uploads/profilelogo/'.$fld_campaign_logo);
	$makelogolink1 = '{name: "'.$fld_campaign_logo.'",size: '.$size_logo.',type: "image/'.$ext_logo.'",file: "'.$directory.$fld_campaign_logo.'"}';
	$makelogolinkview = 'src: "'.$fld_campaign_logo.'"';
	if ($fld_campaign_logo != '') {
		$makelogolink = $makelogolink1;
	} else {
		$makelogolink = '';
	}
//Logo Code End
if($_SESSION['role_id'] == 3) { //Distributor
//Brand Logo Code Start
	$fld_brand_logo = $aUserDetail['fld_brand_logo_header'];
	$directory = sHOMESCMS.'uploads/brandlogo/';
	$ext_logo = pathinfo($directory.$fld_brand_logo, PATHINFO_EXTENSION);
	$size_logo = filesizeWithoutError('uploads/brandlogo/'.$fld_brand_logo);
	$makebrandlogolink1 = '{name: "'.$fld_brand_logo.'",size: '.$size_logo.',type: "image/'.$ext_logo.'",file: "'.$directory.$fld_brand_logo.'"}';
	//$makelogolinkview = 'src: "'.$fld_brand_logo.'"';
	if ($fld_brand_logo != '') {
		$makebrandlogolink = $makebrandlogolink1;
	} else {
		$makebrandlogolink = '';
	}
//Brand Logo Code End
}

checkAndSetInArray($aUserDetail, 'fld_state', '');
//$sState = 3919;
$sState1 = $aUserDetail['fld_state'];
if ($sState1 != '') {
	$sState = $sState1;
} else {
	$sState = '';
}

checkAndSetInArray($aUserDetail, 'fld_country', '');
$sCountry1 = $aUserDetail['fld_country'];
if ($sCountry1 != '') {
	$sCountry = $sCountry1;
} else {
	$sCountry = 'United States';
}
checkAndSetInArray($aUserDetail, 'fld_city', '');
checkAndSetInArray($aUserDetail, 'fld_city', '');
$sCity = $aUserDetail['fld_city'];
$sPassword = $oregister->decrypt($aUserDetail['fld_password'],sENC_KEY);
$curr_role = '';
if ($_SESSION['role_id'] == 1) {
	$curr_role = 'Administrator';
} elseif ($_SESSION['role_id'] == 2) {
	$curr_role = 'Campaign Manager';
} elseif ($_SESSION['role_id'] == 3) {
	$curr_role = 'Distributor';
} elseif ($_SESSION['role_id'] == 5) {
	$curr_role = 'Participants';
} elseif ($_SESSION['role_id'] == 6) {
	$curr_role = 'Representative';
} elseif ($_SESSION['role_id'] == 4) {
	$curr_role = 'Donors';
}

if (array_key_exists('savecontinue', $REQUEST)) 
{
	$sCName = $REQUEST['fld_cname'];
	$sName = $REQUEST['fld_name'];
	$sLName = $REQUEST['fld_lname'];
	$sPhone = $REQUEST['fld_phone'];

	if ($_SESSION['role_id'] == '4' && $_SESSION['role_id'] == '5') {
		$sAddress = "";
		$sCity = "";
		$sState = "";
		$sCountry = "";
		$sZipcode = "";
	} else {
		$sAddress = $REQUEST['fld_address'];
		$sCity = $REQUEST['fld_city'];
		$sState = $REQUEST['fld_state'];
		$sCountry = $REQUEST['fld_country'];
		$sZipcode = $REQUEST['fld_zip'];
	}

	$sEmail = $REQUEST['fld_email'];
	$sPassword = $oregister->encrypt($REQUEST['fld_password'],sENC_KEY);
	$iId = $REQUEST['fld_pid'];
	$oregister->chk_state($sState, $sCountry);
	$oregister->chk_city($sCity, $sState);
	$oregister->update_profile($sCName,$sName,$sLName,$sPhone,$sAddress,$sZipcode,$sCity,$sState,$sCountry,$sEmail,$sPassword,$iId);
	//$oregister->redirect('dashboard.php');
	if (isset($redirecturl) && $redirecturl != '') {
		$oregister->redirect($redirecturl);
	} else {
		$oregister->redirect('dashboard.php');
	}
}
if (array_key_exists('facebookimport', $REQUEST))
{
	if ($_SESSION['role_id'] == '3') {
		$sCName = $REQUEST['fld_cname'];
	} else {
		$sCName = '';
	}
	$sName = $REQUEST['fld_name'];
	$sLName = $REQUEST['fld_lname'];
	$sPhone = $REQUEST['fld_phone'];
	
	if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') {
		$sAddress = "";
		$sCity = "";
		$sState = "";
		$sCountry = "";
		$sZipcode = "";
	} else {
		$sAddress = $REQUEST['fld_address'];
		$sCity = $REQUEST['fld_city'];
		$sState = $REQUEST['fld_state'];
		$sCountry = $REQUEST['fld_country'];
		$sZipcode = $REQUEST['fld_zip'];
	}

	$sEmail = $REQUEST['fld_email'];
	$sPassword = $oregister->encrypt($REQUEST['fld_password'],sENC_KEY);
	$iId = $REQUEST['fld_pid'];
	//print_r($REQUEST);
	$oregister->chk_state($sState, $sCountry);
	$oregister->chk_city($sCity, $sState);
	$oregister->update_profile($sCName,$sName,$sLName,$sPhone,$sAddress,$sZipcode,$sCity,$sState,$sCountry,$sEmail,$sPassword,$iId);
	$oregister->redirect($loginwithfb);
	//$oregister->redirect('dashboard.php');
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
<title>Admin<?php echo sWEBSITENAME;?> - Edit Profile</title>
<!-- Bootstrap Core CSS -->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<link href="css/jquery.filer.css" type="text/css" rel="stylesheet" />
<link href="css/themes/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<style>
.rotatorleft_disable, .rotatorright_disable, jFiler-item-trash-action_disable {
	pointer-events: none;
    opacity: 0.4;
}
</style>
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
		  <div style="position: fixed; z-index: 10; width: 134px; height: 70px; top: 70px; right: 15px;text-align: center;"><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal"><img src="../images/Help icon.jpg" width="60px" /><br>Need help click here</a></div>
		  <h1 class="h1styling" style="width:100%">Edit Profile</h1>
		  <div class="line3"></div>
		  <!-- .white-box -->
          <div class="white-box" style="    background: rgba(245, 245, 245, 0);    border: 0px solid #d9d6d6;">
		   <div class=" full-main">
			
   <form data-toggle="validator"  method="post">
	<div class="col-md-12"><h2 class="page-header" style="margin-top: 0px !important; color: #868484; font-family: Open Sans; font-size: 24px;">Login Details</h2></div>
	<div class="form-group col-sm-6">
		<label for="fld_role" class="control-label">Role</label>
		<input type="text" readonly class="form-control" id="fld_role" name="fld_role" placeholder="Enter Role Name" value="<?=$curr_role;?>">
		<div class="help-block with-errors"></div>
	</div>
	<?php if($_SESSION['role_id'] == 3) { //Distributor ?>
	<div class="form-group col-sm-6">
		<label for="fld_cmname" class="control-label">Company/Brand Name<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_cmname" name="fld_cname" placeholder="Enter Company Name" value="<?=$aUserDetail['fld_cname']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<?php } ?>
	<div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_name" class="control-label">First Name<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_name" name="fld_name" placeholder="Enter First Name" value="<?=$aUserDetail['fld_name']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_lname" class="control-label">Last Name</label>
		<input type="text" class="form-control" id="fld_lname" name="fld_lname" placeholder="Enter Last Name" value="<?=$aUserDetail['fld_lname']?>">
		<div class="help-block with-errors"></div>
	</div>
	<div class="clearfix"></div>

	<div class="form-group col-sm-6">
		<label for="fld_phone" class="control-label">Phone<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_phone" name="fld_phone" value="<?=$aUserDetail['fld_phone']?>" required data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____">
		<div class="help-block with-errors"></div>
	</div>
   
	<div class="form-group col-sm-6">
		<? if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') { ?>
		<!--<label for="fld_address" class="control-label">Address</label>
		<input type="text" class="form-control" id="fld_address" name="fld_address" placeholder="Enter address" value="<?=$aUserDetail['fld_address']?>">-->
		<? } else { ?>
		<label for="fld_address" class="control-label">Address<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_address" name="fld_address" placeholder="Enter address" value="<?=$aUserDetail['fld_address']?>" required>
		<? } ?>
	</div>
	<div class="clearfix"></div>

	<? if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') { ?>
		<!--<label for="fld_zip" class="control-label">ZIP Code</label>
		<input type="text" class="form-control" id="fld_zip" name="fld_zip" placeholder="Enter zipcode" value="<?=$aUserDetail['fld_zip']?>">-->
	<? } else { ?>
	<div class="form-group col-sm-6">
		<label for="fld_zip" class="control-label">ZIP Code<span style="color:#FF0000">*</span></label>
		<input type="text" class="form-control" id="fld_zip" name="fld_zip" placeholder="Enter zipcode" value="<?=$aUserDetail['fld_zip']?>" required>		
		<div class="help-block with-errors"></div>
	</div>
	<? } ?>
	
	<? if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') { ?>
		<!--<label for="fld_city" class="control-label">City</label>
		<?
		$sCiData = $oregister->getcity($sState);
		$iCiRecords = count($sCiData);
		?>
		<div class="styled-select"  id="divCity">
		<select name="fld_city" id="fld_city" class="form-control colorMeBlue noValue">
                  <option value="" selected>Select city</option>
                  <?
                  for($ci=0;$ci<$iCiRecords;$ci++)
				  {
				  ?>
                  <option value="<?=$sCiData[$ci]['name']?>" <? if($aUserDetail['fld_city'] == $sCiData[$ci]['name']){?> selected<? }?>><?=$sCiData[$ci]['name']?></option>
                  <?
				  }
				  ?>
        </select>
		<input type="text" class="form-control" id="fld_city" name="fld_city" placeholder="Enter City" value="<?=$aUserDetail['fld_city'];?>">-->
	<? } else { ?>
	<div class="form-group col-sm-6">
		<label for="fld_city" class="control-label">City<span style="color:#FF0000">*</span></label>
		<?
		$sCiData = $oregister->getcity($sState);
		$iCiRecords = count($sCiData);
		?>
		<div class="styled-select"  id="divCity">
		<!--<select name="fld_city" id="fld_city" class="form-control colorMeBlue noValue" required>
                  <option value="" selected>Select city</option>
                  <?
                  for($ci=0;$ci<$iCiRecords;$ci++)
				  {
				  ?>
                  <option value="<?=$sCiData[$ci]['name']?>" <? if($aUserDetail['fld_city'] == $sCiData[$ci]['name']){?> selected<? }?>><?=$sCiData[$ci]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_city" name="fld_city" placeholder="Enter City" value="<?=$aUserDetail['fld_city'];?>" required>
		</div>
	</div>
	<? } ?>
	<div class="clearfix"></div>
	
	<? if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') { ?>
		<!--<label for="fld_state" class="control-label">State</label>
		<?
        $sSData = $oregister->getstate($sCountry);
		$iSRecords = count($sSData);
        ?>
		<div class="styled-select" id="divState">
        <select name="fld_state" id="fld_state"  class="form-control colorMeBlue noValue">
                  <option value="" selected>Select state</option>
                  <?
                  for($s=0;$s<$iSRecords;$s++)
				  {
				  ?>
                  <option value="<?=$sSData[$s]['name']?>" <? if($sState == $sSData[$s]['name']){?> selected<? }?>><?=$sSData[$s]['name']?></option>
                  <?
				  }
				  ?>
        </select>
		<input type="text" class="form-control" id="fld_state" name="fld_state" placeholder="Enter State" value="<?=$sState;?>">-->
	<? } else { ?>
	<div class="form-group col-sm-6">
		<label for="fld_state" class="control-label">State<span style="color:#FF0000">*</span></label>
		<?
        $sSData = $oregister->getstate($sCountry);
		$iSRecords = count($sSData);
        ?>
		<div class="styled-select" id="divState">
        <!--<select name="fld_state" id="fld_state"  class="form-control colorMeBlue noValue" required>
                  <option value="" selected>Select state</option>
                  <?
                  for($s=0;$s<$iSRecords;$s++)
				  {
				  ?>
                  <option value="<?=$sSData[$s]['name']?>" <? if($sState == $sSData[$s]['name']){?> selected<? }?>><?=$sSData[$s]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_state" name="fld_state" placeholder="Enter State" value="<?=$sState;?>" required>
		<div class="help-block with-errors"></div>		
		</div>
	</div>
	<? } ?>
	
	<? if ($_SESSION['role_id'] == '4' || $_SESSION['role_id'] == '5') { ?>
		<!--<label for="fld_country" class="control-label">Country</label>
		<?
        //$sCData = $oregister->getcountry();
		//$iCRecords = count($sCData);
        ?>
		<select name="fld_country" id="fld_country"  class="form-control colorMeBlue">
				  <option value="" selected>Select country</option>
				  <option value="United States" selected>United States</option>
                  <?
                  for($c=0;$c<$iCRecords;$c++)
				  {
				  ?>
					<option value="<?=$sCData[$c]['name']?>" <? if($sCountry == $sCData[$c]['name']){?> selected<? }?>><?=$sCData[$c]['name']?></option>
                  <?
				  }
				  ?>
        </select>
		<input type="text" class="form-control" id="fld_country" name="fld_country" placeholder="Enter Country" value="<?=$sCountry;?>">-->
	<? } else { ?>
	<div class="form-group col-sm-6">
		<label for="fld_country" class="control-label">Country<span style="color:#FF0000">*</span></label>
		<?
        $sCData = $oregister->getcountry();
		$iCRecords = count($sCData);
        ?>
		<!--<select name="fld_country" id="fld_country"  class="form-control colorMeBlue" required >
				  <option value="" selected>Select country</option>
				  <option value="United States" selected>United States</option>
                  <?
                  for($c=0;$c<$iCRecords;$c++)
				  {
				  ?>
					<option value="<?=$sCData[$c]['name']?>" <? if($sCountry == $sCData[$c]['name']){?> selected<? }?>><?=$sCData[$c]['name']?></option>
                  <?
				  }
				  ?>
        </select>-->
		<input type="text" class="form-control" id="fld_country" name="fld_country" placeholder="Enter Country" value="<?=$sCountry;?>" required>
		<div class="help-block with-errors"></div>
	</div>
	<? } ?>
   <div class="clearfix"></div>
   <div class="colmd_12" align="center" style="clear:both"><h3 class="head1">Profile Image</h3><div class="line_new"></div></div>
   <div align="center"><button class="btn btn-success waves-effect waves-light" name="facebookimport" type="submit">Import Image from Facebook</button></div>
	
	
	
	
    <div class="colmd_12" style="background-color:#FFFFFF;     margin-bottom: 19px;">
    <div id="content" style="padding:20px;" align="center" class="choose_btn"><input type="file" value="<?=$image;?>" name="logo" id="logo" ></div>
    <h4 align="center">Image should be no larger than 15MB</h4>
	</div>
	<?php if($_SESSION['role_id'] == 3) { //Distributor ?>
	<div class="colmd_12" align="center" style="clear:both">
		<h3 class="head1">Add Logo</h3>
		<div class="line_new"></div>
	</div>
	<div class="colmd_12" style="background-color:#FFFFFF;     margin-bottom: 19px;">
		<div id="content" style="padding:20px;" align="center" class="choose_btn"><input type="file" name="logo" id="logo2" ></div>
		<h4 align="center">Logo should be no larger than 15MB</h4>
	</div>
	<?php } ?>
   <div class="col-md-12"><h2 class="page-header" style="margin-top: 0px !important; color: #868484; font-family: Open Sans; font-size: 24px;">Personal Information</h2></div>
   
   <div class="form-group col-sm-6">
		<label for="fld_email" class="control-label">Email address<span style="color:#FF0000">*</span></label>
		<input type="email" class="form-control" id="fld_email" name="fld_email" placeholder="Enter email" value="<?=$aUserDetail['fld_email']?>" required>
		<div class="help-block with-errors"></div>
	</div>
	
	<div class="form-group col-sm-6">
		<label for="fld_password" class="control-label">Password<span style="color:#FF0000">*</span></label>
		<input type="password" class="form-control" id="fld_password" name="fld_password" placeholder="Password" value="<?=$sPassword?>" required data-rule-email="true">
        <a href="javascript:void(0);" id="hideShowPwd"  style="font-size:20px;" alt="Hide Password" title="Hide Password" onClick="hideShowPassword()">
            <i class="fa fa-eye"></i>
        </a>
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
	
	<div class="form-group col-sm-6">
		<label for="fld_cpassword" class="control-label">Confirm Password<span style="color:#FF0000">*</span></label>
		<input type="password" class="form-control" id="fld_cpassword" name="fld_cpassword" placeholder="Password" value="<?=$sPassword?>" required oninput="check(this)">
		<div class="help-block with-errors"></div>
	</div>
    <div class="clearfix"></div>
   
   <div class="form-group">
		<input type="hidden" name="fld_pid" id="fld_pid" value="<?=$iId?>">
    	<div class="col-sm-6" align="left">
			<button class="btn btn-primary waves-effect waves-light" type="button" onClick="window.location.href='dashboard.php'"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Cancel</button>
		</div>
		
		<div class="col-sm-6" align="right">
			<button class="btn btn-success waves-effect waves-light" type="submit" name="savecontinue">Save & Continue <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
		</div>
   </div>
   <div class="clearfix"></div>
   </form>
   </div></div>
</div>
		  </div>
		  </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /#page-wrapper -->
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
<script src="js/jquery.rotate.js"></script>
<script src="js/jquery.filer.min.js?v=1.0.5"></script>
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
<script src="bower_components/sweetalert2/sweetalert2.min.js"></script>
<script src="js/validator.js"></script>
<script>
$('.fa-check').css('color','transparent');
function check(input) {
    if (input.value != document.getElementById('fld_password').value) {
        input.setCustomValidity('Password and Confirm Password do not match!');
    } else {
        // input is valid -- reset the error message
        input.setCustomValidity('');
    }
}

$('select').on('change', function(){
    var $this = $(this);
    
    if (!$this.val()) {
        $this.addClass('noValue');
    } else {
        $this.removeClass('noValue');
    }
});
/*var fld_city = $("#fld_city").val();
if (fld_city != '') {	
	$("#fld_city").removeClass('noValue');
} 
var fld_state = $("#fld_state").val();
if (fld_state != '') {	
	$("#fld_state").removeClass('noValue');
} 
var fld_country = $("#fld_country").val();
if (fld_country != '') {	
	$("#fld_country").removeClass('noValue');
} 
$('#fld_zip').on('blur', function() {
	  $izipcode = this.value;
	  $.ajax({url: "showzipcode.php?zid="+$izipcode, success: function(result){
		var jdata = JSON.parse(result);
		$("#fld_country").removeClass('noValue');
		$("#fld_state").removeClass('noValue');
		$("#fld_city").removeClass('noValue');
		if (jdata.country == 'United States') {
			$("#fld_country option:selected").text(jdata.country).val(jdata.countryid);
			$("#fld_state option:selected").text(jdata.state).val(jdata.stateid);
			$("#fld_city option:selected").text(jdata.city).val(jdata.cityid);
			<? if ($_SESSION['role_id'] == '1' || $_SESSION['role_id'] == '2' || $_SESSION['role_id'] == '3' || $_SESSION['role_id'] == '6') { ?>
			document.getElementById('fld_zip').setCustomValidity("");
			$('#fld_zip').focusout();
			<? } ?>
		} else {
			<? if ($_SESSION['role_id'] == '1' || $_SESSION['role_id'] == '2' || $_SESSION['role_id'] == '3' || $_SESSION['role_id'] == '6') { ?>
			document.getElementById('fld_zip').setCustomValidity('This is Invalid Zipcode, Please enter a Valid Zipcode');
			$('#fld_zip').focusout();
			<? } ?>
		}
    }});
});*/
//$("[data-mask]").inputmask();
/*$('#fld_country').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  $iCountryId = this.value;
	  $.ajax({url: "showstate.php?cid="+$iCountryId, success: function(result){
        $("#divState").html(result);
    }});
});

$('#fld_state').on('change', function() {
	//  alert( this.value ); // or $(this).val()
	  $iStateId = this.value;
	  $.ajax({url: "showcity.php?sid="+$iStateId, success: function(result){
        $("#divCity").html(result);
    }});
});*/

function hideShowPassword() {
    //alert("test");
    var userPassword = $("#fld_password");
    var userCpassword = $("#fld_cpassword");
    if(userPassword.val() != "") {
        if($(userPassword).attr('type') == "password" && userCpassword.attr('type') == "password") {
            $(userPassword).attr('type', 'text');
            $(userCpassword).attr('type', 'text');
            $("#hideShowPwd").removeClass('green').addClass('red');
            $("#hideShowPwd").attr('alt', 'Hide Password');
            $("#hideShowPwd").attr('title', 'Hide Password');
        } else {
            $(userPassword).attr('type', 'password');
            $(userCpassword).attr('type', 'password');
            $("#hideShowPwd").removeClass('red').addClass('green');
            $("#hideShowPwd").attr('alt', 'Show Password');
            $("#hideShowPwd").attr('title', 'Show Password');
        }
    } else {
        //$("#userPasswordError").css('color', 'red').text('First Enter Password');
    }
}

/*$("#fld_phone").keyup(function() {
    var curchr = this.value.length;
    this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,'$1-$2-$3');
});*/
</script>
<script>
function chkfile(path){
    path = path.substring(path.lastIndexOf("/")+ 1);
    return (path.match(/[^.]+(\.[^?#]+)?/) || [])[0];
}
$(document).ready(function() {
	//Profile Image
     $("#logo").filer({
        limit: 1,
        maxSize: 15, //FileSize in MB
        extensions: ['jpg','gif','png','JPG','JPEG', 'jpeg','GIF','BMP','bmp','PNG'],
		
        changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">BROWSE FILES</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
								<div class="jFiler-item-thumb imgrotator">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a>\</li>\
                                        <li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a>\</li>\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb imgrotator">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div></a>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
											<li class="textsuccess"></li>\
										</ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a></li>\
											<li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a></li>\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_profilelogo.php",
			data:{uid:<?=$_SESSION['uid'];?>},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
				var imgprofilepic = jQuery.parseJSON(data);
				//alert(imgprofilepic);
				$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic);
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: function(){
				$('#alertbottom').hide();
				var value = 0
				$(".rotatorleft").rotate({
					bind:
					{
						click: function(){
							var uid = <?=$_SESSION['uid']?>;
							var filename = chkfile($(".profile-pic").find('img').attr('src'));
							var rotate = 'left';
							value -=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
							$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
							$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=3', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Successful;
									setTimeout(function(){ 
										$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										var dt = new Date();
										var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
										
										var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
										$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
				$(".rotatorright").rotate({
					bind:
					{
						click: function(){
							var uid = <?=$_SESSION['uid']?>;
							var filename = chkfile($(".profile-pic").find('img').attr('src'));
							var rotate = 'right';
							value +=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
							$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
							$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=3', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Successful;
									setTimeout(function(){ 
										$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										var dt = new Date();
										var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
										
										var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
										$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
			}
        },
        files: [<?=$makelogolink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var uid = <?=$_SESSION['uid'];?>;
            $.post('./php/remove_profilelogo.php', {file: file, uid: uid});
			$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/default-profile-pic.jpg');
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you’ve chosen are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
	var value = 0
	$(".rotatorleft").rotate({
	bind:
	{
		click: function(){
			var uid = <?=$_SESSION['uid']?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'left';
			value -=90;
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
			$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=3', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						//successful;
						var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
						var dt = new Date();
						var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
						$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	$(".rotatorright").rotate({
	bind:
	{
		click: function(){
			var uid = <?=$_SESSION['uid']?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'right';
			value +=90;
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(".jFiler-item-assets").find(" .rotatorleft ").attr('class', 'rotatorleft_disable');
			$(this).closest(".jFiler-item-assets").find(" .rotatorright ").attr('class', 'rotatorright_disable');
			$(this).closest(".jFiler-item-assets").find(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=3', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						//successful;
						var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
						var dt = new Date();
						var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
						$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	<?php if($_SESSION['role_id'] == 3) { //Distributor ?>
	//Brand Image
     $("#logo2").filer({
        limit: 1,
        maxSize: 15, //FileSize in MB
        extensions: ['jpg','gif','png','JPG','JPEG', 'jpeg','GIF','BMP','bmp','PNG'],
		
        changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">BROWSE FILES</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
								<div class="jFiler-item-thumb imgrotator">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a>\</li>\
                                        <li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a>\</li>\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb imgrotator">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div></a>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
											<li class="textsuccess"></li>\
										</ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="rotatorleft" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image"><i class="fa fa-undo"></i></a></li>\
											<li><a class="rotatorright" style="cursor:pointer;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image"><i class="fa fa-repeat"></i></a></li>\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: "./php/upload_profilebrandlogo.php",
			data:{uid:<?=$_SESSION['uid'];?>},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");    
                });
				var imgprofilepic = jQuery.parseJSON(data);
				//alert(imgprofilepic);
				//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic);
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: function(file, el){
				$('#alertbottom').hide();
				var value = 0
				//var imgprofilepic = $(".rotatorleft").closest(" .jFiler-item-title").text();
				//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic);
				//console.log(imgprofilepic);
				
				$(".rotatorleft").rotate({
					bind:
					{
						click: function(){
							$(".rotatorleft").attr('class', 'rotatorleft_disable');
							$(".rotatorright").attr('class', 'rotatorright_disable');
							$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							var uid = <?=$_SESSION['uid']?>;
							var filename = chkfile($(".profile-pic").find('img').attr('src'));
							var rotate = 'left';
							value -=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=6', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Successful;
									setTimeout(function(){ 
										$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										var dt = new Date();
										var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
										
										var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
										//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
				$(".rotatorright").rotate({
					bind:
					{
						click: function(){
							$(".rotatorleft").attr('class', 'rotatorleft_disable');
							$(".rotatorright").attr('class', 'rotatorright_disable');
							$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
							var uid = <?=$_SESSION['uid']?>;
							var filename1 = $(".profile-pic").find('img').attr('src');
							var filename = filename1.replace('?update', '');
							var rotate = 'right';
							value +=90;
							$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
							$(this).closest(" .jFiler-item-assets ").find(" .text-success ").text('Processing');
							$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=6', function (response) {
								var jdata = JSON.parse(response);
								if (jdata.is_success == 1) {
									//Successful;
									setTimeout(function(){ 
										$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Success');
										var dt = new Date();
										var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
										var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
										//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
										$(".rotatorleft_disable").attr('class', 'rotatorleft');
										$(".rotatorright_disable").attr('class', 'rotatorright');
										$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
									}, 3000);
								} else {
									//Failed
									$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .text-success ").text('Failed');
									$(".rotatorleft_disable").attr('class', 'rotatorleft');
									$(".rotatorright_disable").attr('class', 'rotatorright');
									$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
								}
							});
						}
					}
				});
			}
        },
        files: [<?=$makebrandlogolink;?>],
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            var file = file.name;
			var uid = <?=$_SESSION['uid'];?>;
            $.post('./php/remove_profilebrandlogo.php', {file: file, uid: uid});
			//$(".profile-pic").find('img').attr('src', 'uploads/brandlogo/default-profile-pic.jpg');
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only Images are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
	var value = 0
	$(".rotatorleft").rotate({
	bind:
	{
		click: function(){
			$(".rotatorleft").attr('class', 'rotatorleft_disable');
			$(".rotatorright").attr('class', 'rotatorright_disable');
			$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			var uid = <?=$_SESSION['uid']?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'left';
			value -=90;
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=6', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						//successful;
						var dt = new Date();
						var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
						var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
						//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorleft_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	$(".rotatorright").rotate({
	bind:
	{
		click: function(){
			$(".rotatorleft").attr('class', 'rotatorleft_disable');
			$(".rotatorright").attr('class', 'rotatorright_disable');
			$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action_disable');
			var uid = <?=$_SESSION['uid']?>;
			var filename = $(this).closest(" .jFiler-item-inner ").find(" img ").attr("src");
			var rotate = 'right';
			value +=90;
			$(this).closest(" .jFiler-item-inner ").find(" img ").rotate({ animateTo:value});
			$(this).closest(" .jFiler-item-assets ").find(" .textsuccess ").text('Processing');
			$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=6', function (response) {
				var jdata = JSON.parse(response);
				if (jdata.is_success == 1) {
					//Success
					setTimeout(function(){ 
						$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Success');
						//successful;
						var dt = new Date();
						var time = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();
						var imgprofilepic = chkfile($(".profile-pic").find('img').attr('src'));
						//$(".profile-pic").find('img').attr('src', 'uploads/profilelogo/'+imgprofilepic+'?'+time);
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
					}, 3000);
				} else {
					//Failed
					$(".rotatorright_disable").closest(".jFiler-item-assets ").find(" .textsuccess ").text('Failed');
					$(".rotatorleft_disable").attr('class', 'rotatorleft');
					$(".rotatorright_disable").attr('class', 'rotatorright');
					$(".icon-jfi-trash").attr('class', 'icon-jfi-trash jFiler-item-trash-action');
				}
			});
		}
	}
	});
	<?php } ?>
});

</script>
<script type="text/javascript">
	var error = <?=$error;?>;
	if (error == 1) {
		$(document).ready(function() {
			swal("Error", "Please fill out all required fields.");
		});
	}
	
	var tag = document.createElement('script');

  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('yt-player', {
          height: '390',
          width: '100%',
          videoId: 'uPT7FEMKUYQ',
		  playerVars: { 'rel': 0 },
        });
      }
    $('#myModal').on('hidden.bs.modal', function () {
		player.pauseVideo();
    });
	
</script>
<script src="js/jquery.inputmask.js"></script>
<script>
$("[data-mask]").inputmask();
</script>
</body>
</html>
<? include_once('bottom.php');?>