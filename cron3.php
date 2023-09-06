<?
date_default_timezone_set('America/Los_Angeles'); //TimeZone
//date_default_timezone_set('Asia/Karachi'); //TimeZone


ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ini_set('memory_limit', '512M');
include("cms/php/dbconn.php");
include_once('cms/classes/class.phpmailer.php');

$current_date = date('Y-m-d'); // 2016-07-15
$generated_date = date('m/d/Y'); // 07/15/2016
$date = $current_date; //Todays Date

$i = 0;
$j = 0;
$k = 0;
$l = 0;

// (Production)
$files = glob('cms/files/*');
$now   = time();
foreach ($files as $file) {
	if (is_file($file)) {
		if ($now - filemtime($file) >= 60 * 60 * 24 * 3) { // 3 days
			$i++;
			unlink($file);
		}
    }
}
//  (Production)

// UFund4Kids (Production)
$files = glob('../ufund4kids/app/cms/files/*');
$now   = time();
foreach ($files as $file) {
	if (is_file($file)) {
		if ($now - filemtime($file) >= 60 * 60 * 24 * 3) { // 3 days
			$j++;
			unlink($file);
		}
    }
}
// UFund4Kids (Production)

$total_files = $i + $j + $k + $l;

//Email Setting
$to = CLIENT_EMAIL_2;
$mail = new phpmailer;
$mail->CharSet = 'UTF-8';
$mail->Mailer  = 'mail';
$mail->AddBCC(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
$mail->AddBCC(KURT_EMAIL, KURT_NAME_IN_EMAIL);
$mail->AddReplyTo(INFO_EMAIL,sWEBSITENAME." CRON");
$mail->SetFrom(INFO_EMAIL, sWEBSITENAME." CRON");
$mail->isHTML(true);
$mail->Subject = 'CRON for Unused files @ ['.$generated_date.']';
$mail->AddAddress(trim($to));
//Email Setting
// Emailing
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
																		<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;"><img src="'.SITE_FULL_URL.'app/cms/emails/logo.png"  /></div>
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
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:bolder;margin-top:0;margin-bottom:8px;text-align:left;">CRON Report Dated: '.$generated_date.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">'.sWEBSITENAME.' Production: <b>'.$i.'<b> files has been deleted...!</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">UFund4Kids Production: <b>'.$j.'<b> files has been deleted...!</div>
																					
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">'.sWEBSITENAME.' Development: <b>'.$k.'<b> files has been deleted...!</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">UFund4Kids Development: <b>'.$l.'<b> files has been deleted...!</div>
																						
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Total Files: <b>'.$total_files.'<b> files has been deleted...!</div>
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
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#5F5F5F;">Generated on '.$generated_date.'</div>
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
                                                                    	<div style="padding:10px 10px"><img src="'.SITE_FULL_URL.'app/cms/emails/footer-logo.png" width="178" height="46"  /></div>
                                                                    </td>
                                                                    <td width="50%" valign="center" bgcolor="#2e2e2e">
                                                                    	<div style="padding:10px 10px; font-size:15px;color:gray">Copyright &copy; 2016 | <a href="'.SITE_FULL_URL.'" style="color:#fcb514;">'.sWEBSITENAME.'</a>. All rights reserved.</div>
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
	if ($mail->Send()) {
		echo "Success: Email has been sent...!";
	} else {
		echo "Failed: Email has not been sent...!";
	}
// Emailing
?>