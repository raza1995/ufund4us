<?
require_once("configuration/dbconfig.php");
$REQUEST = &$_REQUEST;

if(isset($REQUEST['_hidCheckSubmit']) == "1")
{
	include_once('classes/class.phpmailer.php');
	checkAndSetInArray($REQUEST, 'name', '');
	checkAndSetInArray($REQUEST, 'email', '');
	checkAndSetInArray($REQUEST, 'password', '');
	
	$sName = trim($REQUEST['name']);
	$sEmail = trim($REQUEST['email']);
	$sPassword = trim($REQUEST['password']);
	$sPassword = $oregister->encrypt($sPassword,sENC_KEY);
	
	try
	{
		$aData = $oregister->forgotpassword($sEmail);
		if($aData)	
		{
			$sPassword = $oregister->decrypt($aData['fld_password'],sENC_KEY);
			$fname = $aData['fld_name'];
			$lname = $aData['fld_lname'];
			//Start Emailing
			$to = $REQUEST['fld_user_email'];
			$mail = new phpmailer;
			$mail->CharSet = 'UTF-8';
			$mail->Mailer  = 'mail';
			$mail->AddReplyTo(INFO_EMAIL, sWEBSITENAME." Team");
			$mail->SetFrom(INFO_EMAIL, sWEBSITENAME.' Team');
			$mail->isHTML(true);
			$mail->Subject = sWEBSITENAME.' Password Recovery';
			$mail->AddAddress($sEmail);
			$mail->Body = '
			<html>
				<head>
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
					#invisibleIntroduction {display:none !important;}
	
					span[class=ios-color-hack] a {color:#275100!important;text-decoration:none!important;}
					span[class=ios-color-hack2] a {color:#205478!important;text-decoration:none!important;}
					span[class=ios-color-hack3] a {color:#8B8B8B!important;text-decoration:none!important;}
					.a[href^="tel"], a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:none!important;cursor:default!important;}
					.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:auto!important;cursor:default!important;}
				</style>
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
																					<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;"><img src="'.SITE_URL.'app/cms/emails/logo.png"  /></div>
																				</td>
																				<td width="43%" valign="top" class="textContent">
																					<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;">&nbsp;</div>
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
																								<h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Hi '.$fname.',</h3>
																								<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;line-height:135%;">Your '.sWEBSITENAME.' Account login details:</div>
																								<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;line-height:135%;">Email : '.$sEmail.'</div>
																								<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;line-height:135%;text-align:left;">Password : '.$sPassword.'</div>
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
																		<table border="0" cellpadding="30" cellspacing="0" width="100%">
																			<tr>
																				<td align="center" valign="top">
																					<table border="0" cellpadding="0" cellspacing="0" width="100%">
																						<tr>
																							<td valign="top" class="textContent">
																								<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">Thank You</div>
																								<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">The '.sWEBSITENAME.' Team</div>
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
																					<div style="padding:10px 10px"><img src="'.SITE_URL.'app/cms/emails/footer-logo.png" width="178" height="46"  /></div>
																				</td>
																				<td width="50%" valign="center" bgcolor="#2e2e2e">
																					<div style="padding:10px 10px; font-size:15px;color:gray">Copyright © <?php echo COPY_RIGHT_YEAR;?> | <a href="'.SITE_FULL_URL.'" style="color:#fcb514;">'.sWEBSITENAME.'</a>. All rights reserved.</div>
																				</td>
																				<td width="22%" valign="center" bgcolor="#2e2e2e">
																					<div style="padding:10px 10px; font-size:15px;color:gray">Powered by <a href="http://www.lyja.com/" style="color:#fcb514;">Lyja</a></div>
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
			$mail->Send();
			$oregister->redirect('thanks.php?m=1');
		}else{
			$error = "<div><b style='color:red'><i>Sorry!</i></b><br>We are not able to locate an account for that email address.<br>Please check your email address you enter.</div>";
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

<title>Forgot Password</title>
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
<? $error = isset($error) ? $error : null;
if($error){?><p align="center" style="color:#FF0000"><?=$error?></p><? }?>
</div>
<div class="formdiv-in2">
<p>Forgot Password</p>
<div class="divin">

   <form name="frmLogin" id="frmLogin" method="post" action="">
   
   <div class="colmd_12">
   <div class="label">
   <input type="email" placeholder="Email" class="formdivtext"  name="email" id="email"  required/>
   <span class="fa fa-envelope">
   </span>
 </div>
   </div>
   
   
   <div class="colmd-12">
    <input type="hidden" name="_hidCheckSubmit" id="_hidCheckSubmit" value="1" />
   <input type="submit" value="Forgot Password"  class="btn-2" /><span class="fa fa-chevron-right"></span>
    <!--<a href="javascript:void" class="btn-2" onclick="FormSubmit()">Sign In <span class="fa fa-chevron-right"></span></a>-->
    <p class="newdiv newdiv3" >Already have an account? <a href="sign-in.php">Click here to login</a></p>
   
    </div>
   </form>
   </div>
   </div>

</div>
</div>
<!----------mid_sec------------>


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

</body></html>