<?
require_once("../configuration/dbconfig.php");


if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
$sPageName = '<li>Generate Link</li>';
$sGenerateLink = 'active';

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

if (array_key_exists('generatesubmit', $_POST)) {
	$generated_hash = generateRandomString();
	if ($_POST['fld_role_id'] == 2) {
		$refferalasa = 'Campaign Manager';
	} elseif ($_POST['fld_role_id'] == 3) {
		$refferalasa = 'Distributor';
	} elseif ($_POST['fld_role_id'] == 6) {
		$refferalasa = 'Representative';
	}
	$groleid = $_POST['fld_role_id'];
	$uid = $_SESSION['uid'];
	$uname = $_SESSION['uname'];
	$oregister->generated_hash($uid, $uname, $groleid, $generated_hash);
	$generatedlink3 = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$generatedlink2 = str_replace('/cms', '', $generatedlink3);
	$generatedlink1 = str_replace('generate_link', 'signup', $generatedlink2);
	$generatedlink = $generatedlink1.'?ref='.$generated_hash;
}

if (array_key_exists('emailsubmit', $_POST)) {
	$iId = $_SESSION['uid'];
	$aUserDetail = $oregister->getuserdetail($iId);
	$user_name = $aUserDetail['fld_name'];
	$ufname = $aUserDetail['fld_name'];
	$ulname = $aUserDetail['fld_lname'];
	$uemail = $aUserDetail['fld_email'];
	$uphone = $aUserDetail['fld_phone'];
	
	$refferallink = $_POST['refferallink'];
	$emailfname = $_POST['emailfname'];
	$emaillname = $_POST['emaillname'];
	$email_from = $_POST['emailfrom'];
	$email_to = $_POST['emailto'];
	$email_cc = $_POST['emailcc'];
	$email_msg = $_POST['emailmsg'];
	
	
	$to = $email_to;
	$cc = $email_cc;
	$subject = sWEBSITENAME.' Start Your Campaign invitation';
	$message = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="format-detection" content="telephone=no" />
		<title>'.sWEBSITENAME.' Start Your Campaign invitation</title>
		<style type="text/css">
			html { background-color:#E1E1E1; margin:0; padding:0; }
			body, #bodyTable, #bodyCell, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important;font-family:Helvetica, Arial, "Lucida Grande", sans-serif;}
			table{border-collapse:collapse;}
			table[id=bodyTable] {width:100%!important;margin:auto;max-width:700px!important;color:#7A7A7A;font-weight:normal;}
			img, a img{border:0; outline:none; text-decoration:none;height:auto; line-height:100%;}
			a {text-decoration:none !important;border-bottom: 1px solid;}
			h1, h2, h3, h4, h5, h6{color:#5F5F5F; font-weight:normal; font-family:Helvetica; font-size:20px; line-height:125%; text-align:Left; letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;}

			.ReadMsgBody{width:100%;} .ExternalClass{width:100%;} 
			.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} 
			table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} 
			#outlook a{padding:0;} 
			img{-ms-interpolation-mode: bicubic;display:block;outline:none; text-decoration:none;} 
			body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%; font-weight:normal!important;} 
			.ExternalClass td[class="ecxflexibleContainerBox"] h3 {padding-top: 10px !important;} 

			h1{display:block;font-size:26px;font-style:normal;font-weight:normal;line-height:100%;}
			h2{display:block;font-size:20px;font-style:normal;font-weight:normal;line-height:120%;}
			h3{display:block;font-size:17px;font-style:normal;font-weight:normal;line-height:110%;}
			h4{display:block;font-size:18px;font-style:italic;font-weight:normal;line-height:100%;}
			.flexibleImage{height:auto;}
			.linkRemoveBorder{border-bottom:0 !important;}
			table[class=flexibleContainerCellDivider] {padding-bottom:0 !important;padding-top:0 !important;}

			body, #bodyTable{background-color:#E1E1E1;}
			#emailHeader{background-color:#E1E1E1;}
			#emailBody{background-color:#FFFFFF;}
			#emailFooter{background-color:#E1E1E1;}
			.nestedContainer{background-color:#F8F8F8; border:1px solid #CCCCCC;}
			.emailButton{background-color:#205478; border-collapse:separate; border-radius: 35px}
			.buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
			.buttonContent a{color:#FFFFFF; display:block; text-decoration:none!important; border:0!important;}
			.emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
			.emailCalendarMonth{background-color:#205478; color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
			.emailCalendarDay{color:#205478; font-family:Helvetica, Arial, sans-serif; font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
			.imageContentText {margin-top: 10px;line-height:0;}
			.imageContentText a {line-height:0;}
			#invisibleIntroduction {display:none !important;} /* Removing the introduction text from the view */

			span[class=ios-color-hack] a {color:#275100!important;text-decoration:none!important;} /* Remove all link colors in IOS (below are duplicates based on the color preference) */
			span[class=ios-color-hack2] a {color:#205478!important;text-decoration:none!important;}
			span[class=ios-color-hack3] a {color:#8B8B8B!important;text-decoration:none!important;}
			.a[href^="tel"], a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:none!important;cursor:default!important;}
			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:auto!important;cursor:default!important;}


			@media only screen and (max-width: 480px){
				body{width:100% !important; min-width:100% !important;} /* Force iOS Mail to render the email at full width. */

				table[id="emailHeader"],
				table[id="emailBody"],
				table[id="emailFooter"],
				table[class="flexibleContainer"],
				td[class="flexibleContainerCell"] {width:100% !important;}
				td[class="flexibleContainerBox"], td[class="flexibleContainerBox"] table {display: block;width: 100%;text-align: left;}
				td[class="imageContent"] img {height:auto !important; width:100% !important; max-width:100% !important; }
				img[class="flexibleImage"]{height:auto !important; width:100% !important;max-width:100% !important;}
				img[class="flexibleImageSmall"]{height:auto !important; width:auto !important;}
				table[class="flexibleContainerBoxNext"]{padding-top: 10px !important;}

				table[class="emailButton"]{width:100% !important;}
				td[class="buttonContent"]{padding:0 !important;}
				td[class="buttonContent"] a{padding:15px !important;}

			}

			@media only screen and (-webkit-device-pixel-ratio:.75){
			}

			@media only screen and (-webkit-device-pixel-ratio:1){
			}

			@media only screen and (-webkit-device-pixel-ratio:1.5){
			}
			@media only screen and (min-device-width : 320px) and (max-device-width:568px) {

			}
		</style>
		<!--[if mso 12]>
			<style type="text/css">
				.flexibleContainer{display:block !important; width:100% !important;}
			</style>
		<![endif]-->
		<!--[if mso 14]>
			<style type="text/css">
				.flexibleContainer{display:block !important; width:100% !important;}
			</style>
		<![endif]-->
	</head>
	<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<center style="background-color:#E1E1E1;">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;">
				<tr>
					<td align="center" valign="top" id="bodyCell">
						<table bgcolor="#FFFFFF"  border="0" cellpadding="0" cellspacing="0" width="700" id="emailBody">
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#FFFFFF;" bgcolor="#f5f5f5">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td width="28%" height="86" valign="top" class="textContent">
																		<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;"><img src="http://lyjashack.com/ufundnew/cms/emails/logo.png"  /></div>
																	</td>
                                                                    <td width="43%" valign="top" class="textContent">
																	  <div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;">&nbsp;</div>
																	</td>
                                                                    <td width="29%" valign="top" class="textContent">
																		<div style="padding-top:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:black; font-style: italic;">Cell: 123.123.1234</div>
                                                                        <div style="padding-top:5px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:black; font-style: italic;">Email: <a href="mailto:'.INFO_EMAIL.'" style="color:#fcb514">'.INFO_EMAIL.'</a></div>
                                                                        <div style="padding-top:5px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:black; font-style: italic;">Web: <a href="'.SITE_FULL_URL.'" style="color:#fcb514">'.SITE_DOMAIN.'</a></div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top">
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td valign="top" class="textContent">
																					<h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Hi '.$emailfname.',</h3>
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;line-height:135%;">Thank you for taking the time to speak with me today about '.sWEBSITENAME.'. The link below will allow you to start your campaign. I will call you once you have logged on to start your campaign.</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr style="padding-top:0;">
											<td align="center" valign="top">
												<table border="0" cellpadding="30" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td style="padding-top:0;" align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="0" cellspacing="0" width="40%" class="emailButton" style="background-color: #FCB514;">
																<tr>
																	<td align="center" valign="middle" class="buttonContent" style="padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;">
																		<a style="color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:14px; font-weight:bolder; line-height:135%; color:black;" href="'.$generatedlink.'" target="_blank">START YOUR CAMPAIGN NOW</a>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							';
				if ($email_msg != '') {
				$message .= '
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top">
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td valign="top" class="textContent">
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;line-height:135%;">'.$email_msg.'</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
				';}
				$message .= '
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top">
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td valign="top" class="textContent">
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">'.$ufname.' '.$ulname.'</div>
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">Cell '.$uphone.'</div>
                                                                                    <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">Email '.$uemail.'</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table class="flexibleContainerCellDivider" border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top" style="padding-top:0px;padding-bottom:0px;">
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td align="center" valign="top" style="border-top:1px solid #C8C8C8;"></td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="700" id="emailFooter">
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="700" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="700" class="flexibleContainerCell">
															<table border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td width="28%" valign="center" bgcolor="#2e2e2e">
                                                                    	<div style="padding:10px 10px"><img src="http://lyjashack.com/ufundnew/cms/emails/footer-logo.png" width="178" height="46"  /></div>
                                                                    </td>
                                                                    <td width="50%" valign="center" bgcolor="#2e2e2e">
                                                                    	<div style="padding:10px 10px; font-size:15px">Copyright © <?php echo COPY_RIGHT_YEAR;?> | <a href="'.SITE_FULL_URL.'" style="color:#fcb514;">'.sWEBSITENAME.'</a>. All rights reserved.</div>
                                                                    </td>
                                                                    <td width="22%" valign="center" bgcolor="#2e2e2e">
                                                                    	<div style="padding:10px 10px; font-size:15px">Powered by <a href="http://www.lyja.com/" style="color:#fcb514;">Lyja</a></div>
                                                                    </td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>
	';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: '.sWEBSITENAME.'. <'.$email_from.'>' . "\r\n";
	if (!empty($cc)) {
		$headers .= 'Cc: <'.$cc.'>' . "\r\n";
	}
	if (mail($to, $subject, $message, $headers, "-f ".$email_from."")) {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>You have successfully generated the refferal link and emailed...!
			</div>';
	} else {
		$thankyou = '<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Error: Message not sent...!
			</div>';
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
				  <option value="<?=$sRoleData[$i]['fld_role_id']?>" <? if($sRoleData[$i]['fld_role_id'] == $_SESSION['role_id']){?> selected<? }?>><?=$sRoleData[$i]['fld_role']?></option>
				  <? }}?>                  
			    </select>
				<div class="help-block with-errors"></div>
			  </div>
			  <div class="clearfix"></div>
			  <div class="col-lg-12" style="padding-left:0px!important;padding-right:0px!important;">
			    <input type="hidden" name="refferallink" id="refferallink" value="<?=$generatedlink;?>">
				<div class="col-lg-6">
				  <button class="btn btn-primary waves-effect waves-light" name="generatesubmit" id="generatesubmit" type="submit"><span class="btn-label" style="background: rgb(230, 160, 3) !important;"><i class="fa fa-check"></i></span>Generate Link</button>
				</div>
				<div class="col-lg-6">
				  <?php if ($_POST) { ?>
				  <button class="btn btn-success waves-effect waves-light" style="width:69%" type="button" data-toggle="modal" data-target="#myModal">Send Email <span class="btn-label forright-icon"><i class="fa fa-envelope-o"></i></span></button>
				  <? } ?>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <br>
			  <div class="col-lg-12" style="padding-left:0px!important;padding-right:0px!important;">
			    <div>Generated Role Type: <?=$refferalasa;?></div>
				<div>Refferal Link: <a href="<?=$generatedlink;?>"><?=$generatedlink;?></a></div>
				<div><?=$thankyou;?></div>
			  </div>
			  <div class="clearfix"></div>
			</form>
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
						<textarea class="form-control" id="emailmsg" name="emailmsg"></textarea>
						<div class="help-block with-errors"></div>
					  </div>
					  <div style="clear:both"></div>
					</div>
					<div style="clear:both"></div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal"><span class="btn-label"><i class="fa fa-chevron-left"></i></span> Close</button>
					  <button type="submit" name="emailsubmit" id="emailsubmit" class="btn btn-success waves-effect waves-light">Send message <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
					</div>
				  </div>
				</div>
			  </div>
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
