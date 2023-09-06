<?php
date_default_timezone_set('America/Los_Angeles'); //TimeZone
//date_default_timezone_set('Asia/Karachi'); //TimeZone
//set_time_limit(0);
//error_reporting(-1); //Reporting
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ini_set('memory_limit', '512M');
include_once('cms/classes/class.phpmailer.php');
//require_once("configuration/dbconfig.php");
require_once("configuration/dbconfig.php");
include("cms/php/dbconn.php");

//include("cms/php/dbconn2.php"); //test database
include("lib/vendor/autoload.php");
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
//$path = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF'])."/emails/"; // http://localhost:8080/ufundnew/cms/emails/
$current_date = date('Y-m-d'); // 2016-07-15
$generated_date = date('m/d/Y'); // 07/15/2016
$date = $current_date; //Todays Date
	
	//Get SparkPost Bounces Email
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.sparkpost.com/api/v1/suppression-list?cursor=initial&limit=10000&per_page=10000&page=10000");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Authorization: ".SPARK_POST_KEY;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result_bounce = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	$bademail[] = '';
	$array_bounce = json_decode($result_bounce, true);
	if( isset($array_bounce['results']) ){
		foreach ($array_bounce['results'] as $bounce) {
			$bademail[] = $bounce['recipient'];
		}
	}
	//print_r($array_bounce );
//End Get SparkPost Bounces Email

$x = 0;
$QueryCampaigns="SELECT c.*, tr.aid, tr.aname, tr.alname, tr.did, tr.dname, tr.dlname, tr.rid, tr.rname, tr.rlname, tr.uid, tr.uname, tr.ulname,
				(SELECT admin.fld_email FROM tbl_users admin WHERE admin.fld_uid = tr.aid) AS admin_email,
				(SELECT dist.fld_email FROM tbl_users dist WHERE dist.fld_uid = tr.did) AS dist_email,
				(SELECT rep.fld_email FROM tbl_users rep WHERE rep.fld_uid = tr.rid) AS rep_email,
				(SELECT cmanager.fld_email FROM tbl_users cmanager WHERE cmanager.fld_uid = tr.uid) AS cmanager_email
				FROM tbl_campaign c
				LEFT JOIN tbl_tree tr ON tr.uid = c.fld_uid
				WHERE c.fld_campaign_sdate <= '$date' AND c.fld_campaign_edate >= '$date' AND 
				c.fld_campaign_sdate NOT IN ('0000-00-00') AND c.fld_status = 1";
$ResultCampaigns = mysqli_query($conn1, $QueryCampaigns) or die("ERROR: Cannot fetch the campaign records...!");
$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
if ($ResultCampaignsRows > 0) {
	//Step 1 >> When Campaign Found
	while ($Rows = mysqli_fetch_array($ResultCampaigns, MYSQLI_ASSOC)) {
		// echo '<pre>'; print_r($Rows); die();
		$ParticipantMoneyRaisedTotal = 0.00;
		$x++;
		$cid = $Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
		$Campaign_Title = str_replace('/', '-', $Rows['fld_campaign_title']); //getting campaign title
		$Campaign_Logo = $Rows['fld_campaign_logo']; //getting campaign logo
		$Campaign_Organization = $Rows['fld_organization_name']; //getting campaign organization name
		$Campaign_HashKey = $Rows['fld_hashcamp']; //getting campaign hashkey
		$Campaign_Goal = $Rows['fld_campaign_goal']; //getting campaign goal
		$Participant_Goal = $Rows['fld_participant_goal']; //getting participant goal
		$Campaign_Donor_Required = $Rows['fld_donor_size']; //getting campaign goal
		//Getting all Roles
		//Administrator
		$aid = $Rows['aid']; //Administrator ID
		$afname = $Rows['aname']; //Administrator First Name
		$alname = $Rows['alname']; //Administrator Last Name
		$aemail = $Rows['admin_email']; //Administrator Email
		//Distributor
		$did = $Rows['did']; //Distributor ID
		$dfname = $Rows['dname']; //Distributor First Name
		$dlname = $Rows['dlname']; //Distributor Last Name
		$demail = $Rows['dist_email']; //Distributor Email
		//Representative
		$rid = $Rows['rid']; //Representative ID
		$rfname = $Rows['rname']; //Representative First Name
		$rlname = $Rows['rlname']; //Representative Last Name
		$remail = $Rows['rep_email']; //Representative Email
		//Campaign Manager
		$cmid = $Rows['uid']; //Campaign Manager ID
		$cmfname = $Rows['uname']; //Campaign Manager First Name
		$cmlname = $Rows['ulname']; //Campaign Manager Last Name
		$cmemail = $Rows['cmanager_email']; //Campaign Manager Email
		//End of Getting all Roles
		
		$QueryDonors="SELECT a.uid, u.fld_name, u.fld_lname, u.fld_email, u.fld_image,
					  (SELECT COUNT(d.uid) FROM tbl_donors_details d WHERE d.puid = a.uid AND d.cid = '$Campaign_Id') AS donors_uploaded,
					  (SELECT COUNT(z.uphone) FROM tbl_donors_details z WHERE z.uphone REGEXP '[0-9]{3}-[0-9]{3}-[0-9]{4}' AND z.puid = a.uid AND z.cid = '$Campaign_Id') AS phonenumberuploaded,
					  (SELECT SUM(da.donation_amount) FROM tbl_donations da WHERE da.refferal_by = a.uid AND da.mode = 1 AND da.cid = '$Campaign_Id') AS money_raised
					  FROM tbl_participants_details a
					  INNER JOIN tbl_users u ON u.fld_uid = a.uid
					  WHERE a.cid = '$Campaign_Id' ORDER BY donors_uploaded ASC";
		$ResultDonors = mysqli_query($conn1, $QueryDonors) or die ("Error: Participants Cannot fetch the data...!");
		$ResultDonorsRows = mysqli_num_rows($ResultDonors);
		$z = 0;
		$donor_campaign[$x] = $Campaign_Title;
		$donor_details[$x] = "";
			$header = '
			<div align="center" style="font-family:arial; margin-top:35px; font-size:14px"></b>'.$Campaign_Title.'</b>, <i>Dated: '.$generated_date.'</i></div>';
			$html = '<table width="100%" style="margin-top:5px; border-collapse: collapse;" border="1">
				<thead>
					<tr>
						<td width="30%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Participant Name</b></div></td>
						<td width="13%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Donors Required</b></div></td>
						<td width="13%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Donors Uploaded</b></div></td>
						<td width="13%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Phone # Uploaded</b></div></td>
						<td width="10%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Bad Emails</b></div></td>
						<td width="12%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Goal</b></div></td>
						<td width="12%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Money Raised</b></div></td>
						<td width="10%" align="center" style="background-color:#ccc; font-family:arial; font-size:14px;"><div style="font-family:arial; font-size:14px"><b>Image Uploaded</b></div></td>
					</tr>
				</thead>
			<tbody>';
		if ($ResultDonorsRows > 0) {
			//Step 2 >> When Donors Found Who Not Donated Yet
			while ($Rows2 = mysqli_fetch_assoc($ResultDonors)) {
				$ParticipantId = $Rows2['uid'];
				$ParticipantFName = $Rows2['fld_name'];
				$ParticipantLName = $Rows2['fld_lname'];
				$ParticipantEmail = $Rows2['fld_email'];
				$ParticipantImage = $Rows2['fld_image'];
				if ($ParticipantImage != '') {
					$ParticipantImage1 = 'Yes';
				} else {
					$ParticipantImage1 = 'No';
				}
				$BadEmailCounter = 0;
				$ParticipantDonorUploaded = $Rows2['donors_uploaded'];
				$PhoneNumberUploaded = $Rows2['phonenumberuploaded'];
				$ParticipantMoneyRaised = $Rows2['money_raised'];
				$ParticipantMoneyRaisedTotal += $ParticipantMoneyRaised;
				
				$QueryChkBadEmail="SELECT did, uemail FROM tbl_donors_details WHERE cid = '$Campaign_Id' AND puid = '$ParticipantId'";
				$ResultChkBadEmail = mysqli_query($conn1, $QueryChkBadEmail) or die ("Error: Donors Bad Emails Cannot fetch the data...!");
				$ResultChkBadEmailRows = mysqli_num_rows($ResultChkBadEmail);
				if ($ResultChkBadEmailRows > 0) {
					while ($Rows3 = mysqli_fetch_assoc($ResultChkBadEmail)) {
						$DonorId = $Rows3['did'];
						$DonorEmail = $Rows3['uemail'];
						if ($DonorEmail != "") {
							if (in_array($DonorEmail, $bademail)) {
								$BadEmailCounter++;
							}
						}
					}
				}
				if ($ParticipantImage1 == 'No' || $ParticipantDonorUploaded < $Campaign_Donor_Required || $BadEmailCounter > 0) {
					$stylecolor = 'style="font-family:arial; font-size:14px; color: red;"';
				} else {
					$stylecolor = 'style="font-family:arial; font-size:14px;"';
				}
				if ($ParticipantImage1 == 'No') {
					$stylecolor1 = 'style="font-family:arial; font-size:14px; color: red;"';
				} else {
					$stylecolor1 = 'style="font-family:arial; font-size:14px;"';
				}
				if ($ParticipantDonorUploaded < $Campaign_Donor_Required) {
					$stylecolor2 = 'style="font-family:arial; font-size:14px; color: red;"';
				} else {
					$stylecolor2 = 'style="font-family:arial; font-size:14px;"';
				}
				if ($BadEmailCounter > 0) {
					$stylecolor3 = 'style="font-family:arial; font-size:14px; color: red;"';
				} else {
					$stylecolor3 = 'style="font-family:arial; font-size:14px;"';
				}
				$html .= '
						<tr>
							<td align="center" '.$stylecolor.'>'.$ParticipantFName.' '.$ParticipantLName.'</td>
							<td align="center" style="font-family:arial; font-size:14px;">'.$Campaign_Donor_Required.'</td>
							<td align="center" '.$stylecolor2.'>'.$ParticipantDonorUploaded.'</td>
							<td align="center" '.$stylecolor2.'>'.$PhoneNumberUploaded.'</td>
							<td align="center" '.$stylecolor3.'>'.$BadEmailCounter.'</td>
							<td align="right" style="font-family:arial; font-size:14px;">$ '.number_format($Participant_Goal, 2, '.', ',').'</td>
							<td align="right" style="font-family:arial; font-size:14px">$ '.number_format($ParticipantMoneyRaised, 2, '.', ',').'</td>
							<td align="center" '.$stylecolor1.'>'.$ParticipantImage1.'</td>
						</tr>
					';				
			}			
		} else {
			$html .= '
			<tr>
				<td colspan="8" align="center" style="font-family:arial;">No Records Found...</td>
			</tr>
			';
		}

		$after_app_fee_percentage = 0.8;
		$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
		$after_app_fee_percentage = get_after_app_fee_percentage($aCampaignDetail);
		$after_app_fee_percentage_val = 100 - ($after_app_fee_percentage*100); 
		// echo 'aCampaignDetail-<pre>'; print_r(['cid'=>$cid, 'aCampaignDetail'=>$aCampaignDetail,'after_app_fee_percentage'=>$after_app_fee_percentage]);  die();

		if ($ParticipantMoneyRaisedTotal > 0) {
			$MoneyRaisedTotal = $ParticipantMoneyRaisedTotal;
			$ProfitTotal = $ParticipantMoneyRaisedTotal*$after_app_fee_percentage;
			// $ParticipantMoneyRaisedTotal*$after_app_fee_percentage;
		} else {
			$MoneyRaisedTotal = 0.00;
			$ProfitTotal = 0.00;
		}
		$html .= '</tbody>
				</table>
				<table style="display:table;margin:0 auto; text-align:center">
					<thead>
						<tr>
							<td style="font-family:arial; font-size:14px;">Money Raised</td>';
							// <td style="font-family:arial; font-size:14px;">Application Fee</td>
							
					$html .= '<td style="font-family:arial; font-size:14px;">Total Profit</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="right" style="font-family:arial; font-size:14px;">'.number_format($MoneyRaisedTotal, 2, '.', ',').'</td>';

							// <td align="right" style="font-family:arial; font-size:14px;">'.$after_app_fee_percentage_val.'%</td>
					$html .= '<td align="right" style="font-family:arial; font-size:14px;">'.number_format($ProfitTotal, 2, '.', ',');

					// $html .='<br/>Testing data: 
					// 		 <br/>MoneyRaisedTotal: '.$MoneyRaisedTotal.'
					// 		 <br/>after_app_fee_percentage:'.$after_app_fee_percentage.'
					// 		 <br/>ProfitTotal:'.$ProfitTotal;

					$html .='</td>
						</tr>
					</tbody>
				</table>
				<div align="center" style="font-family:arial; font-size:12px"><i>Red color denotes that not all information such as donor, images etc. exist for the participant</i></div>';
		
		//__construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
		$configForMpdf = setVariableInAssocArrayForMpdf('c','A4','','',10,10,15,0,10,5);
		$mpdf=new \Mpdf\Mpdf($configForMpdf);
		
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($html);
		$datetime = date("mdY").'-'.date("his");
		$mpdf->Output('cms/files/campaign-report-'.$Campaign_Title.'-'.$datetime.'.pdf', 'F');
		$attachedfile = ''.sHOMECMS.'cms/files/campaign-report-'.$Campaign_Title.'-'.$datetime.'.pdf';
		$filename = 'campaign-report-'.$Campaign_Title.'-'.$datetime.'.pdf';
		$path = ''.sHOMECMS.'cms/files/';
		
		//Email Setting
		$mail = new phpmailer;
		$mail->CharSet = 'UTF-8';
		$mail->Mailer  = 'mail';
		$mail->AddBCC(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
		$mail->addCC(KURT_EMAIL);
		$mail->addCC(CLIENT_2_NAME);

		$mail->ReturnPath = INFO_EMAIL;
		$mail->Sender = INFO_EMAIL;
		//$mail->From = sWEBSITENAME.' CRON <'.INFO_EMAIL.'>';
		$mail->AddReplyTo(NO_REPLY_EMAIL,sWEBSITENAME);
		$mail->SetFrom(NO_REPLY_EMAIL, sWEBSITENAME);
		$mail->isHTML(true);
		$mail->Subject = ''.$Campaign_Title.' '.sWEBSITENAME.' weekly report';
		if ($aemail != '') {
			$mail->AddAddress(trim($aemail), "$afname $alname");
		}
		if ($demail != '') {
			$mail->AddAddress(trim($demail), "$dfname $dlname");
		}
		if ($remail != '') {
			$mail->AddAddress(trim($remail), "$rfname $rlname");
		}
		if ($cmemail != '') {
			$mail->AddAddress(trim($cmemail), "$cmfname $cmlname");
		}
		$mail->AddAttachment('cms/files/'.$filename);      // attachment
		//$mail->SMTPDebug  = 1;
		//Email Setting
		
		 
		$html4MailBody = '
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
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Good Morning,</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Attached is your weekly '.sWEBSITENAME.' report.  This report will help guide your success by detailing participation levels, email status, and funds raised to date.  Please utilize this report to encourage each participants commitment to reach their goals!</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">If you need any additional information or help, please call us at '.SUPPORT_NUMBER_4_DISPLAY.'</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:20px;text-align:left;">Thank you,</div>
																						<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-top:0;margin-bottom:10px;text-align:left;">Your '.sWEBSITENAME.' Team</div>
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
																			<div style="padding:10px 10px; font-size:15px;color:gray">Copyright &copy; '.date('Y').' | <a href="'.SITE_FULL_URL.'" style="color:#fcb514;">'.sWEBSITENAME.'</a>. All rights reserved.</div>
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

		$file = 'cms/files/' . $filename;
		// echo $html4MailBody; die();
		$mail->Body = $html4MailBody;
		//https://ufund4us.com/app/cron2.php?PRINT_REPORTS=1
		//when its 1, will not send email to clients. Will show reports in browser
		$PRINT_REPORTS = isset($_REQUEST['PRINT_REPORTS']) ? $_REQUEST['PRINT_REPORTS'] : false;

		$PRINT_REPORTS = ($PRINT_REPORTS == 1 || $PRINT_REPORTS == true);

		if($PRINT_REPORTS == true){
			echo '<iframe src="'.$file.'" style="width:100%; height:100%;"></iframe>';	
			// die();
		}
		else{ //send email to clients
			if ($mail->Send()) {
				echo "Success: Email has been sent...!";
				if(file_exists($file)){
					unlink($file);
				}
			} else {
				echo "Failed: Email has not been sent...!";
			}	
		}	
		//$total_projected[$x] = $z." of Donors who not donated yet and email has been sent...[".$Campaign_Title."]";
	}//end of while loop
	
	echo $total_campaign = "Total Campaigns: ".$x." of campaigns found...!";
} else {
	echo "No Campaigns Found...!<br>";
}
?>