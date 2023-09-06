<?
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ini_set('memory_limit', '512M');
include("php/dbconn.php");
include('classes/class.phpmailer.php');
include("../lib/vendor/autoload.php");
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$REQUEST = &$_REQUEST;	
//Declare variable required bellow

$REQUEST['cid'] = isset($REQUEST['cid']) ? $REQUEST['cid'] : 0;
$REQUEST['act'] = isset($REQUEST['act']) ? $REQUEST['act'] : 0;
$REQUEST['uid'] = isset($REQUEST['uid']) ? $REQUEST['uid'] : 0;
$REQUEST['actionid'] = isset($REQUEST['actionid']) ? $REQUEST['actionid'] : 0;

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
$is_SMS_Allow = true;

function xss_clean($data)
{
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;', ','), array('&amp;amp;','&amp;lt;','&amp;gt;', ''), $data);
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

date_default_timezone_set('America/Los_Angeles'); //TimeZone
$cid = $_REQUEST['cid'];
$act = $_REQUEST['act'];
$uid = base64_decode($_REQUEST['uid']);
$actionid = $_REQUEST['actionid'];

$QueryGetUser = "SELECT * FROM tbl_users WHERE fld_uid = '$uid'";
$ResultGetUser = mysqli_query( $con, $QueryGetUser);
$Rows = mysqli_fetch_assoc($ResultGetUser);
$user_id = $Rows['fld_uid'];
$full_name = $Rows['fld_name']." ".$Rows['fld_lname'];

if ($act == 1 && $cid != '') { //Participants Detail
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	$generated_date = date('m/d/Y'); // 07/15/2016
	$QueryCampaigns="SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$cid' AND fld_status = 1";
	$ResultCampaigns = mysqli_query( $con, $QueryCampaigns);
	$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
	$z = 0;

	$arrayvalue = [];
	$Campaign_Title = "";

	if ($ResultCampaignsRows > 0) {
		//echo "abc";
		while ($Rows = mysqli_fetch_assoc($ResultCampaigns)) {
			$Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
			$Campaign_Title = xss_clean($Rows['fld_campaign_title']); //getting campaign title
			$Campaign_Manager_FName = xss_clean($Rows['fld_cname']); //getting campaign manager first name
			$Campaign_Manager_LName = xss_clean($Rows['fld_clname']); //getting campaign manager last name
			$Campaign_Logo = $Rows['fld_campaign_logo']; //getting campaign logo
			$Campaign_Organization = xss_clean($Rows['fld_organization_name']); //getting campaign organization name
			$Campaign_HashKey = $Rows['fld_hashcamp']; //getting campaign hashkey
			if ($actionid == 0) {
				$QueryParticipants="SELECT a.uid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_participants_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0";
			} else {
				$QueryParticipants="SELECT a.uid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_participants_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 AND a.sent_date <= '$current_date' - INTERVAL 5 DAY";
			}
			$ResultParticipants = mysqli_query( $con, $QueryParticipants);
			$ResultParticipantsRows = mysqli_num_rows($ResultParticipants);
			if ($ResultParticipantsRows > 0) {
				while ($Rows2 = mysqli_fetch_assoc($ResultParticipants)) {
					$ParticipantId = $Rows2['uid'];
					$ParticipantFName = xss_clean($Rows2['uname']);
					$ParticipantLName = xss_clean($Rows2['ulname']);
					$ParticipantEmail = $Rows2['uemail'];
					$ParticipantPhone = $Rows2['uphone'];
					$arrayvalue[] = $Campaign_Id."|".$Campaign_Title."|".$Campaign_Manager_FName."|".$Campaign_Manager_LName."|".$ParticipantId."|".$ParticipantFName."|".$ParticipantLName."|".$ParticipantEmail;
				}
			}
		}
	}
	$result['participantview'] = $arrayvalue;
	$result['campaigntitle'] = $Campaign_Title;
	$result['counter'] = $ResultParticipantsRows;
	echo json_encode($result);
}

if ($act == 2 && $cid != '') { //Donors Detail
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	$generated_date = date('m/d/Y'); // 07/15/2016
	
	$QueryCampaigns="SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$cid' AND fld_status = 1";
	$ResultCampaigns = mysqli_query( $con, $QueryCampaigns);
	$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
	$z = 0;
	if ($ResultCampaignsRows > 0) {
		//echo "abc";
		while ($Rows = mysqli_fetch_assoc($ResultCampaigns)) {
			$Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
			$Campaign_Title = xss_clean($Rows['fld_campaign_title']); //getting campaign title
			$Campaign_Logo = $Rows['fld_campaign_logo']; //getting campaign logo
			$Campaign_Organization = xss_clean($Rows['fld_organization_name']); //getting campaign organization name
			$Campaign_HashKey = $Rows['fld_hashcamp']; //getting campaign hashkey
			if ($actionid == 0) { //All Donors
				$QueryDonors="SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_donors_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 AND NOT EXISTS 
						(SELECT NULL FROM tbl_donations b WHERE b.cid = '$Campaign_Id' AND b.uid = a.uid AND a.puid = b.refferal_by)";
			} else { //Sent Remaining Donors within a week
				$QueryDonors="SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_donors_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 
						AND a.sent_date <= '$current_date' - INTERVAL 5 DAY 
						AND NOT EXISTS (SELECT NULL FROM tbl_donations b WHERE b.cid = '$Campaign_Id' AND b.uid = a.uid AND a.puid = b.refferal_by)";
			}
			$ResultDonors = mysqli_query( $con, $QueryDonors);
			$ResultDonorsRows = mysqli_num_rows($ResultDonors);
			if ($ResultDonorsRows > 0) {
				while ($Rows2 = mysqli_fetch_assoc($ResultDonors)) {
					$DonorId = $Rows2['uid'];
					$ParticipantId = $Rows2['puid'];
					$DonorFName = xss_clean($Rows2['uname']);
					$DonorLName = xss_clean($Rows2['ulname']);
					$DonorEmail = $Rows2['uemail'];
					$DonorPhone = $Rows2['uphone'];
					$QueryParticipants="SELECT fld_name AS pname, fld_lname AS plname, fld_email AS pemail, fld_phone AS pphone, fld_image FROM tbl_users WHERE fld_uid = '$ParticipantId'";
					$ResultParticipants = mysqli_query( $con, $QueryParticipants);
					while ($Rows3 = mysqli_fetch_assoc($ResultParticipants)) {
						$z++;
						$ParticipantFName = xss_clean($Rows3['pname']);
						$ParticipantLName = xss_clean($Rows3['plname']);
						$ParticipantEmail = $Rows3['pemail'];
						$ParticipantPhone = $Rows3['pphone'];
						$ParticipantImage = $Rows3['fld_image'];
						//echo $z.". Campaign Title: $Campaign_Title || Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail<br>";
						$arrayvalue[] = $Campaign_Id."|".$Campaign_Title."|".$DonorId."|".$DonorFName."|".$DonorLName."|".$DonorEmail."|".$ParticipantId."|".$ParticipantFName."|".$ParticipantLName."|".$ParticipantEmail;
					}
				}
			}
		}
	}
	$result['donorview'] = $arrayvalue;
	$result['campaigntitle'] = $Campaign_Title;
	$result['counter'] = $ResultDonorsRows;
	echo json_encode($result);
}

if ($act == 3 && $cid != '') {
	date_default_timezone_set('America/Los_Angeles'); //TimeZone
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	$generated_date = date('m/d/Y'); // 07/15/2016
	
	//Email Setting
	$to = CLIENT_EMAIL_1;
	$mail = new phpmailer;
	$mail->CharSet = 'UTF-8';
	$mail->Mailer  = 'mail';
	$mail->AddBCC(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
	$mail->AddBCC(KURT_EMAIL, KURT_NAME_IN_EMAIL);
	$mail->AddReplyTo(INFO_EMAIL, sWEBSITENAME." CRON");
	$mail->SetFrom(INFO_EMAIL, sWEBSITENAME." CRON");
	$mail->isHTML(true);
	$mail->Subject = sWEBSITENAME.' Participant Manually CRON ['.$generated_date.']';
	$mail->AddAddress(trim($to));
	//Email Setting
	$x = 0;
	$z = 0;
	$errors = 0;
	$success = 0;
	$snparticipant = 0;
	$snerrorparticipant = 0;
	
	//SMS
	$sms_success_count = 0;
	$sms_fails_count = 0;
	$sms_errors_count = 0;
	
	$getCode = '';
	$getMessage = '';
	$successparticipant_details = '';
	$errorparticipant_details = '';
	
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	
	$QueryUnsubscribe="SELECT COUNT(is_unsubscribe) AS unsubscribed FROM tbl_participants_details WHERE cid = '".$cid."' AND is_unsubscribe = '1'";
	$ResultUnsubscribe = mysqli_query($conn1, $QueryUnsubscribe) or die("ERROR: Cannot fetch the unsubscribe records...!");
	$ResultUnsubscribeRows = mysqli_num_rows($ResultUnsubscribe);
	if ($ResultUnsubscribeRows > 0) {
		$ResultUnsubscribeRow = mysqli_fetch_assoc($ResultUnsubscribe);
		$unsubscribe_count = $ResultUnsubscribeRow['unsubscribed'];
	} else {
		$unsubscribe_count = 0;
	}
	
	if ($actionid == 0) {
		$QueryCampaigns="SELECT a.id, a.cid, a.uid, a.pid, a.uname, a.ulname, a.uemail, a.uphone, 
			CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_hashcamp, b.fld_campaign_id, b.fld_campaign_hashkey, b.fld_text_messaging  
			FROM tbl_participants_details a 
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.is_unsubscribe = 0";
	} else {
		$QueryCampaigns="SELECT a.id, a.cid, a.uid, a.pid, a.uname, a.ulname, a.uemail, a.uphone, 
			CONCAT(b.fld_cname,' ',IFNULL(b.fld_clname,'')) AS fld_cname, b.fld_cemail, b.fld_cphone, b.fld_campaign_title, b.fld_campaign_logo, b.fld_hashcamp, b.fld_campaign_id, b.fld_campaign_hashkey, b.fld_text_messaging 
			FROM tbl_participants_details a 
			INNER JOIN tbl_campaign b ON a.cid = b.fld_campaign_id
			WHERE a.cid = '$cid' AND a.is_unsubscribe = 0 AND a.sent_date <= '$current_date' - INTERVAL 5 DAY";
	}
	$ResultCampaigns = mysqli_query($conn1, $QueryCampaigns);
	$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
	if ($ResultCampaignsRows > 0) {
		while ($Rows = mysqli_fetch_assoc($ResultCampaigns)) {
			$z++;
			$id = $Rows['id'];
			$uid = $Rows['pid'];
			$uid2 = $Rows['uid'];
			$uname = xss_clean($Rows['uname']);
			$ulname = xss_clean($Rows['ulname']);
			$uemail = $Rows['uemail'];
			$uphone = $Rows['uphone'];
			$cname = xss_clean($Rows['fld_cname']);
			$cemail = $Rows['fld_cemail'];
			$cphone = $Rows['fld_cphone'];
			$ctitle = xss_clean($Rows['fld_campaign_title']);
			$clogo = $Rows['fld_campaign_logo'];
			$hashcamp = $Rows['fld_hashcamp'];
			$campid = $Rows['fld_campaign_id'];
			$Text_Messaging = $Rows['fld_text_messaging'];
			$Short_Hash = $Rows['fld_campaign_hashkey']; //getting campaign hashkey for short link
					
			if ($clogo != '') {
				$shlogo = '<img src="'.sHOMECMS.'uploads/logo/thumb_'.$clogo.'" style="width:173px !important; height:66px !important;"';
				$is_clogo = true;
			} else {
				$shlogo = $ctitle;
				$is_clogo = false;
			}
					
			//$linkcreate = sHOME.'signup.php?cid='.$campid.'&refferalid='.$uid.'';
			//$linkcreate2 = ''.sHOME.'unsubscribe.php?cid='.$campid.'&pid='.$uid.'';
			//Sparkpost Participant Template
			//$oCampaign->email_manage_post_participants($id,$uid,$uname,$ulname,$uemail,$uphone,$cname,$cemail,$cphone,$ctitle,$is_clogo,$clogo,$hashcamp,$campid);
			
			$generate_short_link = sHOME.'l.php?v='.$Short_Hash.'&u='.$uid.'&m=1';
			$ParticipantFullName = trim($uname." ".$ulname);
			$body = "Hi! It is $ParticipantFullName.\n";
			$body = "You are being invited to join $ctitle by $cname. Please click on the link below to join this campaign.\n";
			$body .= "".$generate_short_link."\n";
			$body .= "Thank You!";
			if ($is_SMS_Allow == true && $Text_Messaging == 1) {
				if ($uphone != "" && $uphone != "000-000-0000" && $uphone != "___-___-____") {
					//if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $uphone)) {
						if ($sent_sms = send_sms(str_replace("-","",$uphone),utf8_encode($body))) {
							$sms_status = 1;
							$sms_sent_id = $sent_sms['sid'];
							$sms_date_created = $sent_sms['date_created'];
							$sms_message = $sent_sms['message'];
							$sms_details = $sms_message;
							$sms_success_count++;
									
							$tz = new DateTimeZone('America/Los_Angeles');
							$sms_date = new DateTime($sms_date_created);
							$sms_date->setTimezone($tz);
							$sms_date_created = $sms_date->format('Y-m-d h:i:s');
									
							$QuerySMSUpdate="UPDATE tbl_participants_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$campid' AND uid = '$uid2'";
							$ResultSMSUpdate = mysqli_query($conn1, $QuerySMSUpdate);
						} else {
							//Error when sending SMS
							$sms_message = $sent_sms['message'];
							$sms_status = 2;
							$sms_details = $sms_message;
							$sms_fails_count++;
						}
					/*} else {
						//Invalid Number
						$error_sms_count++;
						$error_sms_count_row[$x] = $error_sms_count;
					}*/
				} else {
					//Invalid Number
					$sms_status = 0;
					$sms_details = "Error: Invalid Number";
					$sms_errors_count++;
				}
				$QuerySMSLog="INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$campid','$uid2','$uid2','$uname','$ulname','$uemail','$uphone',NOW(),'$uname $ulname','5','$sms_sent_id','$sms_date_created','$sms_status','$sms_details')";
				$ResultSMSLog = mysqli_query($conn1, $QuerySMSLog);
			}
			
			try {
				//SparkPost Init
				$httpClient = new GuzzleAdapter(new Client());
				$sparky = new SparkPost($httpClient, ['key'=>SPARK_POST_KEY]);
				//SparkPost Init
				$sparky->setOptions(['async' => false]);
				$campaign_id_subject = "$ctitle ($ctitle and $uname $ulname needs your help.)";
				if ($z == 1) {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'participants-template'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"id" => $id,
							"uid" => $uid,
							"uname" => $uname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"uphone" => $uphone,
							"cname" => $cname,
							"cemail" => $cemail,
							"cphone" => $cphone,
							"ctitle" => $ctitle,
							"is_clogo" => $is_clogo,
							"clogo" => $clogo,
							"hashcamp" => $hashcamp,
							"campid" => $campid
						],
						'description' => $ctitle,
						'metadata' => [
							'Campaign_ID' => "$campid",
							'Campaign_Name' => "$ctitle",
							'Subject' => "Campaign Join Confirmation"
						],
						'recipients' => [
							[
								'address' => [
									'name' => $uname.' '.$ulname,
									'email' => $uemail,
								],
							],
						],
						'bcc' => [
							[
								'address' => [
									'name' => SITE_TITLE_WITH_ADMIN,
									'email' => INFO_EMAIL,
								],
								'address' => [
									'name' => CLIENT_1_NAME,
									'email' => CLIENT_EMAIL_1,
								],
							],
						],
					]);
				} else {
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'participants-template'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOME,
							"id" => $id,
							"uid" => $uid,
							"uname" => $uname,
							"ulname" => $ulname,
							"uemail" => $uemail,
							"uphone" => $uphone,
							"cname" => $cname,
							"cemail" => $cemail,
							"cphone" => $cphone,
							"ctitle" => $ctitle,
							"is_clogo" => $is_clogo,
							"clogo" => $clogo,
							"hashcamp" => $hashcamp,
							"campid" => $campid
						],
						'description' => $ctitle,
						'metadata' => [
							'Campaign_ID' => "$campid",
							'Campaign_Name' => "$ctitle",
							'Subject' => "Campaign Join Confirmation"
						],
						'recipients' => [
							[
								'address' => [
									'name' => $uname.' '.$ulname,
									'email' => $uemail,
								],
							],
						],
					]);
				}
				$transmissionid = $promise->getBody()[results][id];
				$QueryParticipantUpdate="UPDATE tbl_participants_details SET sent_email = 1, sent_id = '$transmissionid', sent_date = NOW() WHERE cid = '$cid' AND id = '$id'";
				$ResultParticipantUpdate = mysqli_query($conn1, $QueryParticipantUpdate);
				$success++;
				$snparticipant++;
				$successparticipant_details .= $snparticipant.". Participant ID: $uid2 || Participant Name: $uname $ulname || Participant Email: $uemail || Marked as Clean and Sent...!<br>";
			} catch (\Exception $e) {
				$errors++;
				$snerrorparticipant++;
				$getCode .= $e->getCode()."\n";
				$getMessage .= $e->getMessage()."\n";
				$errorparticipant_details .= $snerrorparticipant.". Participant ID: $uid2 || Participant Name: $uname $ulname || Participant Email: $uemail || Marked as Error and not Sent...!<br>";
			}
		}
	} else {
		echo "No Campaigns Found...!";
	}
	
	$missing_sent = $ResultCampaignsRows - ($success + $errors);	
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
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0;margin-bottom:15px;text-align:left;">Campaign Title: '.$ctitle.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:5px;margin-bottom:15px;text-align:left;"><u>Successful Emails</u></div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$successparticipant_details.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:5px;margin-bottom:15px;text-align:left;"><u>Unsuccessful Emails</u></div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$errorparticipant_details.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">1 Campaign Found...!</div>';																					
																					if ($getMessage != '') {
																					$mail->Body .= '<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;"><u>Error Message:</u></div>';
																					$mail->Body .= '<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;">'.$getMessage.'</div>';
																					}
																					$mail->Body .= '
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;">Report...!</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Total Projected: '.$ResultCampaignsRows.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Success: '.$success.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Errors: '.$errors.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Missings: '.$missing_sent.'</div>
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
	$QueryLastInsert = "INSERT INTO tbl_email_report (cid,ctitle,email_projected,email_sent,email_error,email_error_details,email_missing,email_unsubscribe,email_type,email_method,email_submitted_by_id,email_submitted_by_name,email_date) VALUES 
	('".$Campaign_Id."','".$Campaign_Title."','".$ResultCampaignsRows."','".$success."','".$errors."','".$getMessage."','".$missing_sent."','".$unsubscribe_count."','1','Manually','".$user_id."','".$full_name."',NOW())";
	$ResultLastInsert = mysqli_query( $con, $QueryLastInsert);

	if ($mail->Send()) {
		$result['emailmsg'] = "Success: Email has been sent...!";
	} else {
		$result['emailmsg'] = "Failed: Email has not been sent...!";
	}
	
	$result['totalcounter'] = $ResultCampaignsRows;
	$result['sentcounter'] = $z;
	echo json_encode($result);
}

if ($act == 4 && $cid != '') { //Donors Send Email
	date_default_timezone_set('America/Los_Angeles'); //TimeZone
	$current_date = date('Y-m-d'); //2016-07-15
	$date = $current_date; //Todays Date
	$generated_date = date('m/d/Y'); // 07/15/2016
	
	//Email Setting
	$to = CLIENT_EMAIL_1;
	$mail = new phpmailer;
	$mail->CharSet = 'UTF-8';
	$mail->Mailer  = 'mail';
	$mail->AddBCC(TEST_EMAIL_1, TEST_NAEM_OF_EMAIL_1);
	$mail->AddBCC(KURT_EMAIL, KURT_NAME_IN_EMAIL);
	$mail->AddReplyTo(INFO_EMAIL, sWEBSITENAME." CRON");
	$mail->SetFrom(INFO_EMAIL, sWEBSITENAME." CRON");
	$mail->isHTML(true);
	$mail->Subject = sWEBSITENAME.' Donors Manually CRON ['.$generated_date.']';
	$mail->AddAddress(trim($to));
	//Email Setting
	$x = 0;
	$z = 0;
	$errors = 0;
	$success = 0;
	$sndonors = 0;
	$snerrordonors = 0;
	
	//SMS
	$sms_errors_count = 0;
	$sms_success_count = 0;
	$sms_fails_count = 0;
	
	$getCode = '';
	$getMessage = '';
	$successdonor_details = '';
	$errordonor_details = '';
	
	
	$QueryCampaigns="SELECT * FROM tbl_campaign WHERE fld_campaign_id = '$cid' AND fld_status = 1";
	$ResultCampaigns = mysqli_query($conn1, $QueryCampaigns);
	$ResultCampaignsRows = mysqli_num_rows($ResultCampaigns);
	
	if ($ResultCampaignsRows > 0) {
		while ($Rows = mysqli_fetch_assoc($ResultCampaigns)) {
			$x++;
			$Campaign_Id = $Rows['fld_campaign_id']; //getting campaign id
			$Campaign_Title = xss_clean($Rows['fld_campaign_title']); //getting campaign title
			$Campaign_Logo = $Rows['fld_campaign_logo']; //getting campaign logo
			$Campaign_Organization = xss_clean($Rows['fld_organization_name']); //getting campaign organization name
			$Campaign_HashKey = $Rows['fld_hashcamp']; //getting campaign hashkey
			$Short_Hash = $Rows['fld_campaign_hashkey']; //getting campaign hashkey for short link
			$Text_Messaging = $Rows['fld_text_messaging']; //SMS allow
			
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
			
			if ($actionid == 0) {
				$QueryDonors="SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_donors_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 AND NOT EXISTS 
						(SELECT NULL FROM tbl_donations b WHERE b.cid = '$Campaign_Id' AND b.uid = a.uid AND a.puid = b.refferal_by)";
			} else {
				$QueryDonors="SELECT a.uid, a.puid, a.uname, a.ulname, a.uemail, a.uphone 
						FROM tbl_donors_details a
						WHERE a.cid = '$Campaign_Id' AND a.is_unsubscribe = 0 
						AND a.sent_date <= '$current_date' - INTERVAL 5 DAY 
						AND NOT EXISTS (SELECT NULL FROM tbl_donations b WHERE b.cid = '$Campaign_Id' AND b.uid = a.uid AND a.puid = b.refferal_by)";
			}
			$ResultDonors = mysqli_query($conn1, $QueryDonors);
			$ResultDonorsRows = mysqli_num_rows($ResultDonors);
			if ($ResultDonorsRows > 0) {
				while ($Rows2 = mysqli_fetch_assoc($ResultDonors)) {
					$DonorId = $Rows2['uid'];
					$ParticipantId = $Rows2['puid'];
					$DonorFName = xss_clean($Rows2['uname']);
					$DonorLName = xss_clean($Rows2['ulname']);
					$DonorEmail = $Rows2['uemail'];
					$DonorPhone = $Rows2['uphone'];
					$QueryParticipants="SELECT fld_name AS pname, fld_lname AS plname, fld_email AS pemail, fld_phone AS pphone, fld_image FROM tbl_users WHERE fld_uid = '$ParticipantId'";
					$ResultParticipants = mysqli_query($conn1, $QueryParticipants);
					while ($Rows3 = mysqli_fetch_assoc($ResultParticipants)) {
						$z++;
						$ParticipantFName = xss_clean($Rows3['pname']);
						$ParticipantLName = xss_clean($Rows3['plname']);
						$ParticipantEmail = $Rows3['pemail'];
						$ParticipantPhone = $Rows3['pphone'];
						$ParticipantImage = $Rows3['fld_image'];
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
						//echo $z.". Campaign Title: $Campaign_Title || Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail<br>";						
						//$oCampaign->email_manage_post_donors($Is_Campaign_Logo,$Campaign_Logo,$Is_ParticipantImage,$ParticipantImage,$DonorId,$DonorFName,$DonorLName,$Campaign_Id,$Campaign_HashKey,$Campaign_Title,$Campaign_Organization,$ParticipantId,$ParticipantFName,$ParticipantLName,$DonorEmail);
						
						$generate_short_link = sHOME.'l.php?v='.$Short_Hash.'&u='.$ParticipantId.'&d='.$DonorId.'';
						$ParticipantFullName = trim($ParticipantFName." ".$ParticipantLName);
						$body = "Hi! It is $ParticipantFullName. Please take a second to view a fundraiser that I am participating in by clicking the link below.\n";
						$body .= "".$generate_short_link."\n";
						$body .= "Thank You!";
						if ($is_SMS_Allow == true && $Text_Messaging == 1) {
							if ($DonorPhone != "" && $DonorPhone != "000-000-0000" && $DonorPhone != "___-___-____") {
							//if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $DonorPhone)) {
								if ($sent_sms = send_sms(str_replace("-","",$DonorPhone),utf8_encode($body))) {
									$sms_status = 1;
									$sms_sent_id = $sent_sms['sid'];
									$sms_date_created = $sent_sms['date_created'];
									$sms_message = $sent_sms['message'];
									$sms_details = $sms_message;
									$sms_success_count++;
									
									$tz = new DateTimeZone('America/Los_Angeles');
									$sms_date = new DateTime($sms_date_created);
									$sms_date->setTimezone($tz);
									$sms_date_created = $sms_date->format('Y-m-d h:i:s');
									
									$QuerySMSUpdate="UPDATE tbl_donors_details SET sms_sent_id = '$sms_sent_id', sms_sent_date = '$sms_date_created' WHERE cid = '$Campaign_Id' AND puid = '$ParticipantId' AND uid = '$DonorId'";
									$ResultSMSUpdate = mysqli_query($conn1, $QuerySMSUpdate);
								} else {
									//Error when sending SMS
									$sms_message = $sent_sms['message'];
									$sms_status = 2;
									$sms_details = $sms_message;
									$sms_fails_count++;
								}
							/*} else {
								//Invalid Number
								$error_sms_count++;
								$error_sms_count_row[$x] = $error_sms_count;
							}*/
							} else {
								//Invalid Number
								$sms_status = 0;
								$sms_details = "Error: Invalid Number";
								$sms_errors_count++;
							}
							$QuerySMSLog="INSERT INTO tbl_sms_log (cid,did,pid,dfname,dlname,demail,dphone,creationdate,participantname,likeas,sms_sent_id,sms_sent_date,status,details) VALUES ('$Campaign_Id','$DonorId','$ParticipantId','$DonorFName','$DonorLName','$DonorEmail','$DonorPhone',NOW(),'$ParticipantFName $ParticipantLName','2','$sms_sent_id','$sms_date_created','$sms_status','$sms_details')";
							$ResultSMSLog = mysqli_query($conn1, $QuerySMSLog);
						}
						
						try {
							//SparkPost Init
							$httpClient = new GuzzleAdapter(new Client());
							$sparky = new SparkPost($httpClient, ['key'=>SPARK_POST_KEY]);
							//SparkPost Init
							$sparky->setOptions(['async' => false]);
							if ($z == 1) {
								$promise = $sparky->transmissions->post([
									'content' => ['template_id' => 'donors-template'],
									'substitution_data' => [
										'sHOMECMS' => sHOMECMS,
										'sHOME' => sHOME,
										'Is_Campaign_Logo' => $Is_Campaign_Logo,
										'Campaign_Logo' => $Campaign_Logo,
										'Is_ParticipantImage' => $Is_ParticipantImage,
										'ParticipantImage' => $ParticipantImage,
										'DonorId' => $DonorId,
										'DonorFName' => $DonorFName,
										'DonorLName'=> $DonorLName,
										'Campaign_Id' => $Campaign_Id,
										'Campaign_HashKey' => $Campaign_HashKey,
										'Campaign_Title' => $Campaign_Title,
										'Campaign_Organization' => $Campaign_Organization,
										'ParticipantId' => $ParticipantId,
										'ParticipantFName' => $ParticipantFName,
										'ParticipantLName' => $ParticipantLName,
										'TimeLeft' => $TimeLeft,
									],
									'description' => $Campaign_Title,
									'metadata' => [
										'Campaign_ID' => "$Campaign_Id",
										'Campaign_Name' => "$Campaign_Title",
										'Subject' => "$Campaign_Title and $ParticipantFName $ParticipantLName needs your help"
									],
									'recipients' => [
										[
											'address' => [
												'name' => $DonorFName." ".$DonorLName,
												'email' => $DonorEmail,
											],
										],
									],
									'bcc' => [
										[
											'address' => [
												'name' => SITE_TITLE_WITH_ADMIN,
												'email' => INFO_EMAIL,
											],
											'address' => [
												'name' => CLIENT_1_NAME,
												'email' => CLIENT_EMAIL_1,
											],
										],
									],
								]);
							} else {
								$promise = $sparky->transmissions->post([
									'content' => ['template_id' => 'donors-template'],
									'substitution_data' => [
										'sHOMECMS' => sHOMECMS,
										'sHOME' => sHOME,
										'Is_Campaign_Logo' => $Is_Campaign_Logo,
										'Campaign_Logo' => $Campaign_Logo,
										'Is_ParticipantImage' => $Is_ParticipantImage,
										'ParticipantImage' => $ParticipantImage,
										'DonorId' => $DonorId,
										'DonorFName' => $DonorFName,
										'DonorLName'=> $DonorLName,
										'Campaign_Id' => $Campaign_Id,
										'Campaign_HashKey' => $Campaign_HashKey,
										'Campaign_Title' => $Campaign_Title,
										'Campaign_Organization' => $Campaign_Organization,
										'ParticipantId' => $ParticipantId,
										'ParticipantFName' => $ParticipantFName,
										'ParticipantLName' => $ParticipantLName,
										'TimeLeft' => $TimeLeft,
									],
									'description' => $Campaign_Title,
									'metadata' => [
										'Campaign_ID' => "$Campaign_Id",
										'Campaign_Name' => "$Campaign_Title",
										'Subject' => "$Campaign_Title and $ParticipantFName $ParticipantLName needs your help"
									],
									'recipients' => [
										[
											'address' => [
												'name' => $DonorFName." ".$DonorLName,
												'email' => $DonorEmail,
											],
										],
									],
								]);
							}
							$TransmissionID = $promise->getBody()[results][id];
							$QueryDonorUpdate="UPDATE tbl_donors_details SET sent_email = 1, sent_id = '$TransmissionID', sent_date = NOW() WHERE cid = '$Campaign_Id' AND puid = '$ParticipantId' AND uid = '$DonorId'";
							$ResultDonorUpdate = mysqli_query($conn1, $QueryDonorUpdate);
							$sndonors++;
							$successdonor_details .= $sndonors.". Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail || Marked as Clean and Sent...!<br>";
							$success++;
						} catch (\Exception $e) {
							$errors++;
							$getCode .= $e->getCode()."\n";
							$getMessage .= $e->getMessage()."\n";
							$snerrordonors++;
							$errordonor_details .= $snerrordonors.". Donor ID: $DonorId || Donor Name: $DonorFName $DonorLName || Donor Email: $DonorEmail || Participant ID: $ParticipantId || Participant Name: $ParticipantFName $ParticipantLName || Participant Email: $ParticipantEmail || Marked as Error and not Sent...!<br>";
						}
					}
				}
			}
			/*$QueryLastUpdate = "UPDATE tbl_campaign SET fld_last_updated = NOW() WHERE fld_campaign_id = '$Campaign_Id'";
			$ResultLastUpdate = mysqli_query( $con, $QueryLastUpdate);*/
		}
	} else {
		echo "No Campaigns Found...!";
	}
	
	$missing_sent = $ResultDonorsRows - ($success + $errors);	
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
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0;margin-bottom:15px;text-align:left;">Campaign Title: '.$Campaign_Title.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:5px;margin-bottom:15px;text-align:left;"><u>Successful Emails</u></div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$successdonor_details.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:5px;margin-bottom:15px;text-align:left;"><u>Unsuccessful Emails</u></div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:0px;margin-bottom:2px;text-align:left;">'.$errordonor_details.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">1 Campaign Found...!</div>';
																					if ($getMessage != '') {
																					$mail->Body .= '<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;"><u>Error Message:</u></div>';
																					$mail->Body .= '<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:4px;margin-bottom:12px;text-align:left;">'.$getMessage.'</div>';
																					}
																					$mail->Body .= '
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;">Email Report...!</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Total Projected: '.$ResultDonorsRows.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Success: '.$success.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Errors: '.$errors.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Missing: '.$missing_sent.'</div>
																					
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:12px;margin-bottom:12px;text-align:left;">SMS Report...!</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Total Projected: '.($sms_success_count+$sms_fails_count+$sms_errors_count).'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Success: '.$sms_success_count.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Failed: '.$sms_fails_count.'</div>
																					<div style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;margin-top:2px;margin-bottom:12px;text-align:left;">Errors: '.$sms_errors_count.'</div>
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
	$sms_projection = $sms_success_count + $sms_fails_count + $sms_errors_count;
	$QueryLastInsert = "INSERT INTO tbl_email_report (cid,ctitle,email_projected,email_sent,email_error,email_error_details,email_missing,email_unsubscribe,email_type,email_method,email_submitted_by_id,email_submitted_by_name,email_date,sms_projected,sms_sent,sms_error,sms_missing,sms_type,sms_method,sms_submitted_by_id,sms_submitted_by_name,sms_date) VALUES 
	('".$Campaign_Id."','".$Campaign_Title."','".$ResultDonorsRows."','".$success."','".$errors."','".$getMessage."','".$missing_sent."','".$unsubscribe_count."','0','Manually','".$user_id."','".$full_name."',NOW(),'".$sms_projection."','".$sms_success_count."','".$sms_fails_count."','".$sms_errors_count."','0','Manually','".$user_id."','".$full_name."',NOW())";
	$ResultLastInsert = mysqli_query( $con, $QueryLastInsert);

	if ($mail->Send()) {
		$result['emailmsg'] = "Success: Email has been sent...!";
	} else {
		$result['emailmsg'] = "Failed: Email has not been sent...!";
	}
	
	$result['totalcounter'] = $ResultDonorsRows;
	$result['sentcounter'] = $z;
	echo json_encode($result);
}

if ($act == 5) {
  $current_date = date('Y-m-d'); //2016-07-15
  $iCountRecords5 = 0;
  $ab1575_pupil = 0;
  $QueryGetRecords = "SELECT fld_campaign_id, fld_campaign_title, fld_ab1575_pupil_fee, DATE_FORMAT(fld_campaign_sdate, '%m/%d/%Y') AS sdate, DATE_FORMAT(fld_campaign_edate, '%m/%d/%Y') AS edate, DATE_FORMAT(fld_last_updated, '%m/%d/%Y') AS fld_last_updated 
						FROM tbl_campaign 
						WHERE fld_campaign_id = '$cid' AND fld_campaign_edate >= '$date' AND fld_campaign_sdate NOT IN ('0000-00-00') AND fld_status = 1";
  $ResultGetRecords = mysqli_query($conn1, $QueryGetRecords);
  $ResultGetRecordsRows = mysqli_num_rows($ResultGetRecords);
  $scheduledetails = mysqli_fetch_assoc($ResultGetRecords);

  $iCountRecords5 = count($scheduledetails);
  if ($iCountRecords5 == 6) {
	  $iCountRecords5 = 1;
  } else {
	  $iCountRecords5 = 0;
  }
  $camp_id = $scheduledetails['fld_campaign_id'];
  $camp_title = $scheduledetails['fld_campaign_title'];
  $sdate = $scheduledetails['sdate'];
  $edate = $scheduledetails['edate'];
  $ab1575_pupil = $scheduledetails['fld_ab1575_pupil_fee'];

  $last_updated = $scheduledetails['fld_last_updated'];
  if ($last_updated == '00/00/0000') {
	$next_date = strtotime($sdate);
    $next_date = strtotime("+5 day", $next_date);
	$next_sent = date('m/d/Y', $next_date);  
  } else {
	$next_date = strtotime($last_updated);
    $next_date = strtotime("+5 day", $next_date);
	$next_sent = date('m/d/Y', $next_date);  
  }
  $result['camp_id'] = $camp_id;
  $result['camp_title'] = $camp_title;
  $result['sdate'] = $sdate;
  $result['edate'] = $edate;
  $result['ab1575_pupil'] = $ab1575_pupil;
  $result['last_updated'] = $last_updated;
  $result['next_sent'] = $next_sent;
  $result['counter'] = $iCountRecords5;
  echo json_encode($result);
}

if ($act == 6) {
  $current_date = date('Y-m-d'); //2016-07-15
  $iCountRecords5 = 0;
  $QueryGetRecords = "SELECT *, DATE_FORMAT(email_date, '%m/%d/%Y') AS edate
					  FROM tbl_email_report 
					  WHERE cid = '$cid'";
  $ResultGetRecords = mysqli_query($conn1, $QueryGetRecords);
  $ResultGetRecordsRows = mysqli_num_rows($ResultGetRecords);
  while ($emaildetails = mysqli_fetch_assoc($ResultGetRecords)) {
	  $cid = $emaildetails['cid'];
	  $ctitle = $emaildetails['ctitle'];
	  $email_projected = $emaildetails['email_projected'];
	  $email_sent = $emaildetails['email_sent'];
	  $email_error = $emaildetails['email_error'];
	  $email_missing = $emaildetails['email_missing'];
	  $email_type1 = $emaildetails['email_type'];
	  if ($email_type1 == '0') {
		  $email_type = 'Donors';
	  } else {
		  $email_type = 'Participants';
	  
	  }
	  $email_submitted_by_id = $emaildetails['email_submitted_by_id'];
	  $email_submitted_by_name = $emaildetails['email_submitted_by_name'];
	  $edate = $emaildetails['edate'];
	  
	  $email_records[] = $cid."|".$ctitle."|".$email_projected."|".$email_sent."|".$email_error."|".$email_missing."|".$email_type."|".$email_submitted_by_id."|".$email_submitted_by_name."|".$edate;
  }
  if ($ResultGetRecordsRows > 0) {
	  $iCountRecords5 = $ResultGetRecordsRows;
  } 
  
  $result['emailreport'] = $email_records;
  $result['counter'] = $iCountRecords5;
  echo json_encode($result);
}
?>