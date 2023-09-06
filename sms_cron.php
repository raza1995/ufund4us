<?php
date_default_timezone_set('America/Los_Angeles'); //TimeZone
//date_default_timezone_set('Asia/Karachi'); //TimeZone
//set_time_limit(0);
//error_reporting(-1); //Reporting
ini_set('max_execution_time', 999999); //7200 seconds = 120 minutes
ini_set('memory_limit', '1536M');
include("cms/php/dbconn.php");
include_once('cms/classes/class.phpmailer.php');
include("lib/vendor/autoload.php");

//Twilio Init Function
function send_sms($number,$body)
{
    $ID = TWILIO_ID;
	$token = TWILIO_TOKEN;
	$twilio_number = TWILIO_PHONE_NUMBER;
	$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $ID . '/Messages.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

    curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD,$ID . ':' . $token);

    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        'To=' . rawurlencode('+1' . $number) .
        '&From=' . rawurlencode($twilio_number) .
        '&Body=' . rawurlencode($body));

    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp,true);
}
//Twilio Init Function
//SMS
$is_SMS_Allow = 1;

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
//$path = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF'])."/emails/"; // http://localhost:8080/ufundnew/cms/emails/
$current_date = date('Y-m-d'); // 2016-07-15
$generated_date = date('m/d/Y'); // 07/15/2016
$date = $current_date; //Todays Date
function recallmysql() {
	global $conn1;
	global $DB_HOST1;
	global $DB_USER1;
	global $DB_PASS1;
	if (!mysqli_ping($conn1)) {
		$conn1 = mysqli_connect($DB_HOST1, $DB_USER1, $DB_PASS1) or die("<div>MySQL Error: Oops! UNABLE to CONNECT to the DATABASE!</div>");
		mysqli_select_db($conn1, $DB_NAME1) or die("<div>MYSQL ERROR: Oops! Database access FAILED!</div>");
		mysqli_set_charset($conn1, 'utf8') or die("<div>UNABLE to SET database connection ENCODING!</div>");
	} 
}
function xss_clean($data)
{
// Fix &entity\n;
$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

do
{
    // Remove really unwanted tags
    $old_data = $data;
    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
}
while ($old_data !== $data);

// we are done...
$returndata = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
return $data;
}

//Email Setting
$to = KURT_EMAIL;
$cc = TEST_EMAIL_1;
$mail = new phpmailer;
$mail->CharSet = 'UTF-8';
$mail->Mailer  = 'mail';
if ($cc != '') {
	$mail->addCC($cc);
}
//$mail->AddBCC(KURT_EMAIL, 'Kurt Gairing');
$mail->addCC(CLIENT_EMAIL_1);
$mail->AddReplyTo(INFO_EMAIL, sWEBSITENAME." CRON");
$mail->ReturnPath = INFO_EMAIL;
$mail->Sender = INFO_EMAIL;
//$mail->From = sWEBSITENAME.' CRON <'.INFO_EMAIL.'>';
$mail->SetFrom(INFO_EMAIL, sWEBSITENAME." CRON");
$mail->isHTML(true);
$mail->Subject = sWEBSITENAME.' CRON ['.$generated_date.']';
$mail->AddAddress(trim($to));
//Email Setting
$x = 0;

$QueryCampaigns="SELECT *, IFNULL(FLOOR(ABS(DATEDIFF(fld_last_updated, fld_campaign_edate) / 5)),DATEDIFF(fld_campaign_edate, fld_campaign_sdate)) AS remain_email   
				 FROM tbl_campaign 
				 WHERE fld_campaign_sdate <= '$date' AND fld_campaign_edate >= '$date' AND fld_campaign_sdate NOT IN ('0000-00-00') AND fld_status = 1";
$ResultCampaigns = mysqli_query($conn1, $QueryCampaigns) or die("ERROR: Cannot fetch the campaign records...!");
$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
if ($ResultCampaignsRows > 0) {
	//echo "abc";
	//Step 1 >> When Campaign Found
	while ($Rows = mysqli_fetch_array($ResultCampaigns, MYSQLI_ASSOC)) {
		$x++;
		recallmysql();
		$Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
		$Campaign_Title = $Rows['fld_campaign_title']; //getting campaign title
		$Campaign_Logo = $Rows['fld_campaign_logo']; //getting campaign logo
		$Campaign_Organization = $Rows['fld_organization_name']; //getting campaign organization name
		$Campaign_HashKey = $Rows['fld_hashcamp']; //getting campaign hashkey
		$Emails_Remaining = $Rows['remain_email']; //getting number of emails remaining to sent out
		$Short_Hash = $Rows['fld_campaign_hashkey']; //getting campaign hashkey for short link
		$Text_Messaging = $Rows['fld_text_messaging']; //getting campaign text messaging allow
		
		$fld_enddate1 = date('Y-m-d',strtotime($Rows['fld_campaign_edate']));
		$current_date1 = date("Y-m-d H:i:s");
		$from=date_create($fld_enddate1." 23:59:59");
		$to=date_create($current_date1);
		$diff=date_diff($to,$from);
		$TimeLeft = $diff->format('%a Days, %H Hours');
		
		$QueryUnsubscribe="SELECT COUNT(is_unsubscribe) AS unsubscribed FROM tbl_donors_details WHERE cid = '".$Campaign_Id."' AND is_unsubscribe = '1'";
		$ResultUnsubscribe = mysqli_query($conn1, $QueryUnsubscribe) or die("ERROR: Cannot fetch the unsubscribe records...!");
		$ResultUnsubscribeRows = mysqli_num_rows($ResultUnsubscribe);
		if ($ResultUnsubscribeRows > 0) {
			$ResultUnsubscribeRow = mysqli_fetch_assoc($ResultUnsubscribe);
			$unsubscribe_count = $ResultUnsubscribeRow['unsubscribed'];
		} else {
			$unsubscribe_count = 0;
		}
		
		$QueryDonors="SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone, a.is_read,
					  u.fld_name AS pname, u.fld_lname AS plname, u.fld_email AS pemail, u.fld_phone AS pphone, u.fld_image 
					  FROM tbl_donors_details a
					  INNER JOIN tbl_users u ON u.fld_uid = a.puid
					  WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 AND NOT EXISTS 
					  (SELECT NULL FROM tbl_donations b WHERE b.cid = '$Campaign_Id' AND b.uid = a.uid AND a.puid = b.refferal_by)";
		$ResultDonors = mysqli_query($conn1, $QueryDonors) or die ("Error: Donors Cannot fetch the data...!");
		$ResultDonorsRows = mysqli_num_rows($ResultDonors);
		$z = 0;
		$success_count = 0;
		$success_count_row[$x] = "";
		$failed_count = 0;
		$failed_count_row[$x] = "";
		$bounce_count = 0;
		$bounce_count_row[$x] = "";
		
		$success_sms_count = 0;
		$success_sms_count_row[$x] = "";
		$failed_sms_count = 0;
		$failed_sms_count_row[$x] = "";
		$error_sms_count = 0;
		$error_sms_count_row[$x] = "";
		
		$getCode[$x] = "";
		$getMessage[$x] = "";
		$donor_campaign[$x] = $Campaign_Title;
		$donor_campaignid[$x] = $Campaign_Id;
		$unsubscribe[$x] = $unsubscribe_count;
		$donor_details[$x] = "";
		$donor_details_success[$x] = "";
		$donor_details_fail[$x] = "";
		$donor_details_bounce[$x] = "";
		if ($ResultDonorsRows > 0) {
			//Step 2 >> When Donors Found Who Not Donated Yet
			while ($Rows2 = mysqli_fetch_assoc($ResultDonors)) {
				recallmysql();
				$DonorId = $Rows2['uid'];
				$ParticipantId = $Rows2['puid'];
				$DonorFName = $Rows2['uname'];
				$DonorLName = $Rows2['ulname'];
				$DonorEmail = $Rows2['uemail'];
				$DonorPhone = $Rows2['uphone'];
				$DonorRead = $Rows2['is_read'];
				$ParticipantFName = $Rows2['pname'];
				$ParticipantLName = $Rows2['plname'];
				$ParticipantEmail = $Rows2['pemail'];
				$ParticipantPhone = $Rows2['pphone'];
				$ParticipantImage = $Rows2['fld_image'];
				$linkcreate = ''.sHOME.'campaign.php?cid='.$Campaign_HashKey.'|'.$Campaign_Id.'|'.$ParticipantId.'&hashid='.$DonorId.'';
				$linkcreate2 = ''.sHOME.'unsubscribe.php?cid='.$Campaign_Id.'&pid='.$ParticipantId.'&did='.$DonorId.'';
				if ($Campaign_Logo != '') {
					$shlogo = '<img src="'.sHOMECMS.'uploads/logo/thumb_'.$Campaign_Logo.'" style="width:173px !important; height:66px !important;"';
					$Is_Campaign_Logo = true;
				} else {
					$shlogo = $Campaign_Title;
					$Is_Campaign_Logo = false;
				}
				if ($ParticipantImage != '') {
					$uhlogo = '<img src="'.sHOMECMS.'uploads/profilelogo/thumb_'.$ParticipantImage.'" style="width:173px !important; height:66px !important;"';
					$Is_ParticipantImage = true;
				} else {
					$uhlogo = '';
					$Is_ParticipantImage = false;
				}
					$generate_short_link = ''.str_replace('app/','',sHOME).'l.php?v='.$Short_Hash.'&u='.$ParticipantId.'&d='.$DonorId.'';
					$ParticipantFullName = trim($ParticipantFName." ".$ParticipantLName);
					$body = "Hi! It is $ParticipantFullName. Please take a second to view a fundraiser that I am participating in by clicking the link below.\n";
					$body .= "".$generate_short_link."\n";
					$body .= "Thank You!";
					if ($is_SMS_Allow == 1 && $Text_Messaging == 1) {
					if ($DonorPhone != "" && $DonorPhone != "000-000-0000" && $DonorPhone != "___-___-____") {
						//if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $DonorPhone)) {
							$DonorPhone = str_replace(array("-"," "), array("",""), $DonorPhone);
							$body = utf8_encode($body);
							if ($sent_sms = send_sms($DonorPhone,$body)) {
								$sms_status = 1;
								$sms_sent_id = $sent_sms['sid'];
								$sms_date_created = $sent_sms['date_created'];
								$sms_message = $sent_sms['message'];
								$sms_details = $sms_message;
								
								$tz = new DateTimeZone('America/Los_Angeles');
								$sms_date = new DateTime($sms_date_created);
								$sms_date->setTimezone($tz);
								$sms_date_created = $sms_date->format('Y-m-d h:i:s');
								
								$success_count++;
								$success_count_row[$x] = $success_count;
								$donor_details_success[$x] .= $success_count.". Campaign Title: $Campaign_Title || Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail || Marked as Clean and Sent...!<br>";
								
								$success_sms_count++;
								$success_sms_count_row[$x] = $success_sms_count;
								
								$QuerySMSUpdate="UPDATE tbl_donors_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$Campaign_Id' AND puid = '$ParticipantId' AND uid = '$DonorId'";
								$ResultSMSUpdate = mysqli_query($conn1, $QuerySMSUpdate);
								$z++;
							} else {
								//Error when sending SMS
								$failed_count++;
								$failed_count_row[$x] = $failed_count;
								$donor_details_fail[$x] .= $failed_count.". Campaign Title: $Campaign_Title || Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail || Marked as Failed and not Sent...!<br>";
								
								$sms_message = $sent_sms['message'];
								$sms_status = 2;
								$sms_details = $sms_message;
								$failed_sms_count++;
								$failed_sms_count_row[$x] = $failed_sms_count;
							}
					} else {
						//Invalid Number
						$sms_status = 0;
						$sms_details = "Error: Invalid Number";
						$error_sms_count++;
						$error_sms_count_row[$x] = $error_sms_count;
					}
					$QuerySMSLog="INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$Campaign_Id','$DonorId','$ParticipantId','$DonorFName','$DonorLName','$DonorEmail','$DonorPhone',NOW(),'$ParticipantFName $ParticipantLName','1','$sms_sent_id','$sms_date_created','$sms_status','$sms_details')";
					$ResultSMSLog = mysqli_query($conn1, $QuerySMSLog);
					}
			}			
		}
		$total_projected[$x] = $z." of Donors who not donated yet and sms has been sent...[".$Campaign_Title."]";
	}
	
	echo $total_campaign = "Total Campaigns: ".$x." of campaigns found...!";
	echo "<br><br>";
	for ($cr=1; $cr <= $ResultCampaignsRows; $cr++) {
		echo $total_projected[$cr];
		echo "<br>";
		echo $donor_details[$cr];
	}
} else {
	echo "No Campaigns Found...!<br>";
}
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
																				<td valign="top" class="textContent">';
																				if ($ResultCampaignsRows > 0) {	
																					$mail->Body .= '
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:bolder;margin-top:0;margin-bottom:8px;text-align:left;">CRON Report Dated: '.$generated_date.'</div>';
																					for ($cr=1; $cr <= $ResultCampaignsRows; $cr++) {
																						$mail->Body .= '
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:4px;text-align:left;">Campaign Title: <b>'.$donor_campaign[$cr].'<b></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:6px;margin-bottom:8px;text-align:left;"><u>Emails</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:6px;margin-bottom:8px;text-align:left;"><u>Successful Emails</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$donor_details_success[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Bounce Emails</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$donor_details_bounce[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Failed Emails</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$donor_details_fail[$cr].'<br>'.$getMessage[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Reports</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Projection: '.($failed_count_row[$cr] + $success_count_row[$cr] + $bounce_count_row[$cr]).' Donors Email</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Success: '.$success_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Bounces: '.$bounce_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Failed: '.$failed_count_row[$cr].'</div>
																							
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:16px;margin-bottom:8px;text-align:left;"><u>SMS</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:6px;margin-bottom:8px;text-align:left;"><u>Successful SMS</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$success_sms_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Failed SMS</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$failed_sms_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Invalid SMS</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$error_sms_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:8px;margin-bottom:8px;text-align:left;"><u>Reports</u></div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Projection: '.($success_sms_count_row[$cr] + $failed_sms_count_row[$cr] + $error_sms_count_row[$cr]).' Donors SMS</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Success: '.$success_sms_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Failed: '.$failed_sms_count_row[$cr].'</div>
																							<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:8px;text-align:left;">Invalid: '.$error_sms_count_row[$cr].'</div>
																						';
																					}
																					$mail->Body .= '
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">'.$total_campaign.'</div>';
																				} else {
																					$mail->Body .= '
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:bolder;margin-top:0;margin-bottom:8px;text-align:left;">CRON Report Dated: '.$generated_date.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">No Campaigns Found...!</div>';
																				}
																				$mail->Body .= '
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
	
	if ($ResultCampaignsRows > 0) {	
		for ($cr=1; $cr <= $ResultCampaignsRows; $cr++) {
			recallmysql();
			$projection = $failed_count_row[$cr] + $success_count_row[$cr] + $bounce_count_row[$cr];
			$sms_projection = $failed_sms_count_row[$cr] + $success_sms_count_row[$cr] + $error_sms_count_row[$cr];
			$QueryLastInsert = "INSERT INTO tbl_email_report (cid,ctitle,email_projected,email_sent,email_error,email_error_details,email_missing,email_unsubscribe,email_type,email_method,email_submitted_by_id,email_submitted_by_name,email_date,sms_projected,sms_sent,sms_error,sms_missing,sms_type,sms_method,sms_submitted_by_id,sms_submitted_by_name,sms_date) VALUES 
			('".$donor_campaignid[$cr]."','".xss_clean($donor_campaign[$cr])."','".$projection."','".$success_count_row[$cr]."','".$failed_count_row[$cr]."','".xss_clean($getMessage[$cr])."','".$bounce_count_row[$cr]."','".$unsubscribe[$cr]."','0','CRON','0','Site Automation',NOW(),'".$sms_projection."','".$success_sms_count_row[$cr]."','".$error_sms_count_row[$cr]."','".$failed_sms_count_row[$cr]."','0','CRON','0','Site Automation',NOW())";
			$ResultLastInsert = mysqli_query($conn1, $QueryLastInsert);
			
		}
	}
	
	if ($mail->Send()) {
		echo "Success: Email has been sent...!";
	} else {
		echo "Failed: Email has not been sent...!";
	}
?>