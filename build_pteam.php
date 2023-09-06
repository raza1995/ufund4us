<?
require_once("../configuration/dbconfig.php");
function phoneIsValid($phone)
{
	$isValid = false; 
	if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
		// $phone is valid
		$isValid = true; 
	}
	return $isValid;
}
function emailIsValid($email)  
{ 
	$isValid = true; 
	$atIndex = strrpos($email, "@"); 
	if (is_bool($atIndex) && !$atIndex) 
	{ 
		$isValid = false; 
	} 
	else 
	{ 
		$domain    = substr($email, $atIndex+1); 
		$local     = substr($email, 0, $atIndex); 
		$localLen  = strlen($local); 
		$domainLen = strlen($domain); 
		if ($localLen < 1 || $localLen > 64) 
		{ 
			$isValid = false; 
		} 
		else if ($domainLen < 1 || $domainLen > 255) 
		{ 
			$isValid = false; 
		} 
		else if ($local[0] == '.' || $local[$localLen-1] == '.') 
		{ 
			$isValid = false; 
		} 
		else if (preg_match('/\\.\\./', $local)) 
		{ 
			$isValid = false; 
		} 
		else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) 
		{ 
			$isValid = false; 
		} 
		else if (preg_match('/\\.\\./', $domain)) 
		{ 
			$isValid = false; 
		} 
		else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) 
		{ 
			if (!preg_match('/^"(\\\\"|[^"])+"$/', 
				str_replace("\\\\","",$local))) 
			{ 
				$isValid = false; 
			} 
		} 
		if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) 
		{ 
			$isValid = false; 
		} 
	} 
	return $isValid; 
}

if (isset($_GET['cid'])) {
$cid = $_GET['cid'];
$uid = $_GET['uid'];
$link = $_GET['linkref'];
$chk_participant = $oCampaign->chk_validate_link($cid, $uid, $link);
if ($chk_participant > 0) {
$jslink = 'golive.php?cid='.$_GET['cid'].'';
$csvenable = false;
$appliedclass = 'class="empty"';
$start_campaign = 'start_campaign.php?m=e&cid='.$_GET['cid'].'';
$basic_information = 'basic_information.php?cid='.$_GET['cid'].'';
$sPageName = '<li><a href="manage_donors.php">Manage Donors</a></li><li>Enter Your Donors</li>';

if (isset($_GET['pid']) && isset($_GET['did']) && $_GET['action'] == 'adddonor') {
	$pid = $_GET['pid'];
	$cid = $_GET['cid'];
	$did = $_GET['did'];
	$cmid = $_GET['uid'];
	$aParticipantDetail = $oregister->getuserdetail($pid);
	$pfname = $aParticipantDetail['fld_name'];
	$plname = $aParticipantDetail['fld_lname'];
	$participantname = $pfname.' '.$plname;
	$aUserDetail = $oregister->getuserdetail($did);
	$dfname = $aUserDetail['fld_name'];
	$dlname = $aUserDetail['fld_lname'];
	$demail = $aUserDetail['fld_email'];
	$dphone = $aUserDetail['fld_phone'];
	$oCampaign->insert_campaign_donors2($cid, $did, $pid, $dfname, $dlname, $demail, $dphone, 'Previous Donor', $pid, $participantname);
	$linkref = $_GET['linkref'];
	$uid = $_GET['uid'];
	$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
}

if (isset($_GET['did']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$aDonorDetailEdit = $oCampaign->getdonordetailedit($cid, $pid, $did);
	$donor_email = $aDonorDetailEdit['uemail'];
	$donor_name = $aDonorDetailEdit['uname'];
	$donor_lname = $aDonorDetailEdit['ulname'];
	$donor_phone = $aDonorDetailEdit['uphone'];
}

if (array_key_exists('donoredited', $_POST)) {
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$pname = $_POST['pname'];
	$plname = $_POST['plname'];
	$pemail = $_POST['pemail'];
	$pphone = $_POST['pphone'];
	$invalidemails[] = '';
	if (emailIsValid($pemail) || phoneIsValid($pphone)) {
		$oCampaign->donor_update($cid, $pid, $did, sanitize($pname), sanitize($plname), $pemail, $pphone);
		//SpartPost Send
		if (emailIsValid($pemail)) {
			if ($oCampaign->send_email_to_updatedonor($cid, $pid, $did)) {
				//End SpartPost Send
				$linkref = $_GET['linkref'];
				$uid = $_GET['uid'];
			}
		}
		$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
	} else {
		if ($pemail != '') {
			$data['fname'] = $pname; 
			$data['lname'] = $plname; 
			$data['email'] = $pemail; 
			$data['phone'] = $pphone; 
			array_push($invalidemails, $data);
		}
	}
}

if (isset($_GET['did']) && isset($_GET['action']) && $_GET['action'] == 'resent') {
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$oCampaign->resent_donors($cid, $pid, $did);
	$linkref = $_GET['linkref'];
	$uid = $_GET['uid'];
	$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
}

if (isset($_GET['pid']) && isset($_GET['cid']) && isset($_GET['did']) && isset($_GET['hash']) && isset($_GET['action']) && $_GET['action'] == 'sms_resent') {
	$hash = $_GET['hash'];
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$uid = $_GET['uid'];
	if ($uid == '') {
		$uid = $pid;
	}
	$linkref = $_GET['linkref'];
	$sms_sent_id = "";
			
	//Donors Details
	$aDonorDetailEdit = $oCampaign->getdonordetailedit($cid, $uid, $did);
	$donor_fname = $aDonorDetailEdit['uname'];
	$donor_lname = $aDonorDetailEdit['ulname'];
	$donor_phone = $aDonorDetailEdit['uphone'];
	$donor_email = $aDonorDetailEdit['uemail'];
	//Participant Details
	$aParticipantDetailEdit = $oCampaign->getparticipantdetails($cid, $uid);
	$participantid = $aParticipantDetailEdit['uid'];
	$participantname = $aParticipantDetailEdit['uname']." ".$_SESSION['ulname'];
	
	$generate_short_link = sHOME.'l.php?v='.$hash.'&u='.$uid.'&d='.$did.'';
	$ParticipantFullName = trim($participantname);
	$body = "Hi! It is $ParticipantFullName. Please take a second to view a fundraiser that I am participating in by clicking the link below.\n";
	$body .= "".$generate_short_link."\n";
	$body .= "Thank You!";
	
	if ($donor_phone != "" && $donor_phone != "000-000-0000" && $donor_phone != "___-___-____") {
		if ($sent_sms = send_sms(str_replace("-","",$donor_phone),utf8_encode($body))) {
			$sms_status = 1;
			$sms_sent_id = $sent_sms['sid'];
			if ($sms_sent_id != '') {
				$sms_date_created = $sent_sms['date_created'];
				$sms_message = $sent_sms['message'];
				$sms_details = $sms_message;
							
				$tz = new DateTimeZone('America/Los_Angeles');
				$sms_date = new DateTime($sms_date_created);
				$sms_date->setTimezone($tz);
				$sms_date_created = $sms_date->format('Y-m-d h:i:s');
				
				$oCampaign->sms_resend_donors($cid, $uid, $did, $donor_fname, $donor_lname, $donor_phone, $donor_email, $participantid, $participantname, $sms_sent_id, $sms_date_created, $sms_details);
			}
		} else {
			//Error when sending SMS
		}
	} else {
		//Invalid Number
	}
	$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');

}

if (isset($_GET['did']) && isset($_GET['action']) && $_GET['action'] == 'remove') {
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$oCampaign->delete_donors_details($cid, $pid, $did);
	$linkref = $_GET['linkref'];
	$uid = $_GET['uid'];
	$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
}

if (isset($_GET['pmid']) && isset($_GET['action']) && $_GET['action'] == 'delete') {
	$pid = $_GET['pid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$oCampaign->delete_donors($cid, $pid, $did);
	$linkref = $_GET['linkref'];
	$uid = $_GET['uid'];
	$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
}
if (array_key_exists('addtolist3', $_POST)) {
	$pname = $_POST['pname'];
	$plname = $_POST['plname'];
	$pemail = $_POST['pemail'];
	$phone = $_POST['pphone'];
	$uid = $_GET['uid'];
	$cid = $_GET['cid'];
	$using = 'Single';
	$aUserDetail = $oregister->getuserdetail($uid);
	$participantid = $uid;
	$participantname = $aUserDetail['fld_name']." ".$aUserDetail['fld_lname'];
	$invalidemails[] = '';
	foreach( $pname as $key => $name ) {
		if ($pname[$key] != '' && (emailIsValid($pemail[$key]) || phoneIsValid($phone[$key]))) {
			$aUserDetail = $oregister->getuserdetailbyemailfordonor($pemail[$key],$phone[$key]);
			$getid = $aUserDetail['fld_uid'];
			if ($getid != '') {
				$oCampaign->insert_campaign_adddonors2($cid, $getid, $uid, $name, $plname[$key], $pemail[$key], $phone[$key], $using, $participantid, $participantname);
			} else {
				$generatepasshash = $oregister->generatepasshash(10);
				$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
				$oCampaign->insert_campaign_adddonors1($cid, $uid, sanitize($name), sanitize($plname[$key]), $pemail[$key], $phone[$key], $Password, $using, $participantid, $participantname);
			}
		} else {
			if ($pname[$key] != '') {
				$data['fname'] = $pname[$key]; 
				$data['lname'] = $plname[$key]; 
				$data['email'] = $pemail[$key]; 
				$data['phone'] = $phone[$key]; 
				array_push($invalidemails, $data);
			}
		}
	}
	$chkdate = $oCampaign->chkcampaign($cid, $uid);
	$camphash = $oregister->generatepasshash(20);
	$camp_status = $chkdate['fld_status'];
	$start_date = $chkdate['daysstart'];
	$end_date = $chkdate['daysend'];
	$startend_date = $chkdate['startenddate'];
	if ($start_date >= 0 && $start_date <= $startend_date && $camp_status == 1) {
		$oCampaign->send_email_to_donors($cid, $uid);
		$oCampaign->makeitlive($cid, '', '1', $camphash);
		
	} else {
		$oCampaign->makeitlive($cid, '', '0', $camphash);
	}
	echo '<div id="alerttop" class="myadmin-alert myadmin-alert-icon myadmin-alert-click alert6 myadmin-alert-top" style="display: block;"> <i class="ti-user"></i> Donor list has been successfully updated and emailed.</div>';
}

if (array_key_exists('golive', $_POST)) {
	//$pid = $_GET['uid'];
	$pid = $_GET['uid'];
	$did = $_GET['did'];
	$cid = $_GET['cid'];
	$id = $_POST['id'];
	$name = $_POST['name'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$uid = $_GET['uid'];
	$using = 'CSV';
	$aUserDetail = $oregister->getuserdetail($uid);
	$participantid = $uid;
	$participantname = $aUserDetail['fld_name']." ".$aUserDetail['fld_lname'];
	$invalidemails[] = '';
	foreach( $id as $key => $n ) {
		if ($name[$key] != '' && (emailIsValid($email[$key]) || phoneIsValid($phone[$key]))) {
			$aUserDetail = $oregister->getuserdetailbyemailfordonor($email[$key],$phone[$key]);
			$getid = $aUserDetail['fld_uid'];
			if ($getid != '') {
				$oCampaign->insert_campaign_donors2($cid, $getid, $pid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key], $using, $participantid, $participantname);
			} else {
				$generatepasshash = $oregister->generatepasshash(10);
				$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
				$oCampaign->insert_campaign_donors1($cid, $pid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key], $Password, $using, $participantid, $participantname);
			}
		} else {
			if ($email[$key] != '') {
				$data['fname'] = $name[$key]; 
				$data['lname'] = $lname[$key]; 
				$data['email'] = $email[$key]; 
				$data['phone'] = $phone[$key]; 
				array_push($invalidemails, $data);
			}
		}
	}
	$chkdate = $oCampaign->chkcampaign($cid, $uid);
	$camphash = $oregister->generatepasshash(20);
	$camp_status = $chkdate['fld_status'];
	$start_date = $chkdate['daysstart'];
	$end_date = $chkdate['daysend'];
	$startend_date = $chkdate['startenddate'];
	if ($start_date >= 0 && $start_date <= $startend_date && $camp_status == 1) {
		$oCampaign->send_email_to_donors($cid, $pid);
		$oCampaign->makeitlive($cid, '', '1', $camphash);
		
	} else {
		$oCampaign->makeitlive($cid, '', '0', $camphash);
	}
	$linkref = $_GET['linkref'];
	$uid = $_GET['uid'];
	echo "<script>window.close();</script>";
	//$oregister->redirect('build_pteam.php?cid='.$cid.'&uid='.$uid.'&linkref='.$linkref.'');
	//echo '<div id="alerttop" class="myadmin-alert myadmin-alert-icon myadmin-alert-click alert6 myadmin-alert-top" style="display: block;"> <i class="ti-user"></i> Donor list has been successfully updated and emailed.</div>';
	
}

if (array_key_exists('upload', $_POST)) {
	$file = $_POST['csvfile'];
	$filename = "csv_".rand(0000000001,9999999999).".csv";
    if ( 0 < $_FILES['csvfile']['error'] ) {
        //echo 'Error: ' . $_FILES['csvfile']['error'] . '<br>';
    }
    else {
        move_uploaded_file($_FILES['csvfile']['tmp_name'], 'uploads/' . $filename);
    }
	
	$file = fopen("uploads/$filename","r");
	$head = fgetcsv($file, 4096, ',', '"');
	$invalidemails[] = '';
	while($column = fgetcsv($file, 4096, ',', '"'))
	{
		if ($DB_con->getAttribute(PDO::ATTR_SERVER_INFO)=='MySQL server has gone away')
		{
			try {
				$DB_con = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
				$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e) {
				//echo $e->getMessage();
			}
		}
		$column = array_combine($head, $column);
		if ($column['Email Address'] != '') {
			$csv0 = $column['Email Address'];
		} else {
			$csv0 = $column[' Email Address'];
		}
		if ($column['Cell Phone'] != '') {
			$csv4 = $column['Cell Phone'];
		} else {
			$csv4 = $column[' Cell Phone'];
		}
		if (emailIsValid($csv0) || phoneIsValid($csv4)) {
			if ($column['First Name'] != '') {
				$csv1 = $column['First Name'];
			} else {
				$csv1 = $column[' First Name'];
			}
			if ($column['Last Name'] != '') {
				$csv2 = $column['Last Name'];
			} else {
				$csv2 = $column[' Last Name'];
			}
			if ($column['Email Address'] != '') {
				$csv3 = $column['Email Address'];
			} else {
				$csv3 = $column[' Email Address'];
			}
			if ($column['Cell Phone'] != '') {
				$csv4 = $column['Cell Phone'];
			} else {
				$csv4 = $column[' Cell Phone'];
			}
			if ($csv4 == '') {
				$csv4 = '___-___-____';
			}
			//$csvarray[] = "$csv1,$csv2,$csv3,$csv4";
			//$pid = $_GET['uid'];
			$pid = $_GET['uid'];
			$cid = $_GET['cid'];
			$using = 'CSV';
			$aUserDetail = $oregister->getuserdetail($pid);
			$participantid = $pid;
			$participantname = $aUserDetail['fld_name']." ".$aUserDetail['fld_lname'];
			if ($csv1 != '') {
				$aUserDetail = $oregister->getuserdetailbyemailfordonor($csv3,$csv4);
				$getid = $aUserDetail['fld_uid'];
				if ($getid != '') {
					$oCampaign->insert_campaign_donors2($cid, $getid, $pid, sanitize($csv1), sanitize($csv2), $csv3, $csv4, $using, $participantid, $participantname);
				} else {
					$generatepasshash = $oregister->generatepasshash(10);
					$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
					$oCampaign->insert_campaign_donors1($cid, $pid, sanitize($csv1), sanitize($csv2), $csv3, $csv4, $Password, $using, $participantid, $participantname);
				}
			}
			$oCampaign->send_email_to_donors($cid, $pid);
		} else {
			$data['fname'] = $column['First Name']; 
			$data['lname'] = $column['Last Name']; 
			$data['email'] = $column['Email Address']; 
			$data['phone'] = $column['Cell Phone']; 
			array_push($invalidemails, $data);
		}
	}
	$csvenable = true;
	$appliedclass = '';
}

if($_GET['cid'] > 0)
{
	$cid = $_GET['cid'];
	$uid = $_GET['uid'];
	$pid = $_GET['uid'];
	if ($pid != '') {
		$participant_available = true;
	} else {
		$participant_available = false;
	}
	$aUserDetail = $oregister->getuserdetail($uid);
	$user_fname = $aUserDetail['fld_name'];
	$user_lname = $aUserDetail['fld_lname'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$aDonorDetail = $oCampaign->getdonordetail($cid, $uid);
	$aDonorDetailSelected = $oCampaign->getdonordetailselected($cid, $uid);
	$fld_campaign_hashkey = $aCampaignDetail['fld_campaign_hashkey'];
	$fld_text_messaging = $aCampaignDetail['fld_text_messaging'];
	$fld_hashcamp = $aCampaignDetail['fld_hashcamp'];
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	$fld_organization_name = $aCampaignDetail['fld_organization_name'];
	$fld_team_name = $aCampaignDetail['fld_team_name'];
	$fld_team_size = $aCampaignDetail['fld_team_size'];
	$fld_campaign_sdate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_sdate']));
	$fld_campaign_edate = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_edate']));
	$fld_campaign_goal = $aCampaignDetail['fld_campaign_goal'];
	$fld_participant_goal = $aCampaignDetail['fld_participant_goal'];	
	$fld_desc1 = $aCampaignDetail['fld_desc1'];	
	$fld_desc2 = $aCampaignDetail['fld_desc2'];	
	$fld_donation_level1 = $aCampaignDetail['fld_donation_level1'];	
	$fld_donation_level2 = $aCampaignDetail['fld_donation_level2'];	
	$fld_donation_level3 = $aCampaignDetail['fld_donation_level3'];
	$fld_live = $aCampaignDetail['fld_live'];
	$fld_enddate = date('Ymd',strtotime($aCampaignDetail['fld_campaign_edate']));
	$fld_startdate = date('Ymd',strtotime($aCampaignDetail['fld_campaign_sdate']));
	$camp_starts = date('m/d/Y',strtotime($aCampaignDetail['fld_campaign_sdate']));
	$current_date = date("Ymd");
	$fld_comdate = $fld_enddate - $current_date;
	
	$aCampaignGraphParticipant = $oCampaign->getcampaigngraphparticipant2($cid, $pid);
	$participant_goal = $aCampaignGraphParticipant['participant_goal'];
	$participant_raised = $aCampaignGraphParticipant['participant_raised'];
	$participant_graph_total_per = ($participant_raised / $participant_goal) * 100;
	
	$aCampaignGraphTotal = $oCampaign->getcampaigngraphtotal2($cid);
	$campaign_goal = $aCampaignGraphTotal['campaign_goal'];


	$app_fee_percentage = DEFAULT_APP_FEE;//20%
	if( isset($aCampaignDetail['app_fee_percentage']) ){
	    $app_fee_percentage = $aCampaignDetail['app_fee_percentage'];
	}
	$after_app_fee_percentage = 1;
	if($app_fee_percentage > 0){
		$after_app_fee_percentage = 1 - $app_fee_percentage/100; 
	}

	$campaign_raised = $aCampaignGraphTotal['campaign_raised']*$after_app_fee_percentage;
	$campaign_graph_total_per = (($campaign_raised / $campaign_goal) * 100)*$after_app_fee_percentage;
	$url = sHOME.'campaign.php?cid='.$fld_hashcamp.'!'.$cid.'!'.$uid.'';
	$text = 'Please take a moment to review '.$fld_campaign_title.'. Your support is greatly appreciated! ';
	$email_msg = "Hi%0D%0A%0D%0APlease take a moment and consider supporting $fld_campaign_title. Your generous donation will help make a difference.%0D%0A%0D%0A";
	$email_msg .= "$url%0D%0A%0D%0A";
	$email_msg .= "Thank you for your consideration.";
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
	$array_bounce = json_decode($result_bounce, true);
	//print_r($array_bounce );
	/*foreach ($array_bounce [results] as $bounce) {
		if ($bounce[source] == 'Bounce Rule') {
			echo $bounce[recipient];
		}
	}*/
	//End Get SparkPost Bounces Email
}else{
	$oregister->redirect('manage_donors.php');
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
<title>Admin<?php echo sWEBSITENAME;?> - Enter Email Address</title>
<!-- Bootstrap Core CSS -->
<link href="../bars/bars.css" rel="stylesheet" type="text/css">
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!--My admin Custom CSS -->
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!--alerts CSS -->
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<link rel="stylesheet" href="css/style_table.css">
<script src="js/jquery-1.10.2.min.js"></script> 
<link rel="stylesheet" href="css/smk-accordion.css">
<script type="text/javascript" src="js/smk-accordion.js"></script> 
<script type="text/javascript">
		jQuery(document).ready(function($){

			


			$(".accordion_example4").smk_Accordion({
				closeAble: true, //boolean
				closeOther: false, //boolean
			});

			$(".accordion_example5").smk_Accordion({closeAble: true});

			$(".accordion_example6").smk_Accordion();
			
			$(".accordion_example7").smk_Accordion({
				activeIndex: 2 //second section open
			});
			$(".accordion_example8, .accordion_example9").smk_Accordion();


			
			
		});
	</script> 

<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> 
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
.btn-s1 {
	background: #FCB514!important;
	border-radius: 5px;
	border-color: transparent;
	margin-right: 5px;
	color:#fff !important;
	font-weight:600;
}
.btn-s1:hover {
	border-color: transparent;
}
.btn-c1 {
	background: #aba8a8!important;
	border-radius: 5px;
	border-color: transparent;
	color:#fff !important;
	font-weight:600;
}
.btn-c1:hover {
	border-color: transparent;
}
.editable-css {
	font-size: 11px;
	text-decoration-line: underline;
	text-decoration-style: dotted;
	color: #337ab7;
}
.editable-css:hover {
	font-size: 11px;
	text-decoration-line: underline;
	text-decoration-style: dotted;
	color: #23527c;
}
</style>
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
		<div class="col-sm-12 white-box">
		  <!-- .white-box -->
		  <h1 class="h1styling">Enter Email Address</h1>
		  <div class="line3"></div>
		  <h3 style="text-align: center; font-size:30px; font-weight: bolder;"><?=$user_fname;?> <?=$user_lname;?></h3>
		  <h2 class="h1styling"><?=$fld_campaign_title;?></h2>
          <div class="">
		    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 banner-graph" style="margin-left:12%">
                  <div class='wrap_right'>
                    <? if ($participant_available == true) { ?>
					<div class='bar_group' style="margin-bottom:15%">
					  <div class='bar_group__bar thick' value='100'>
                        <div class="b_tooltip" style="top:220%;left:100%">
                          <span>Participant Goal<br>$ <?=number_format($participant_goal,2,'.',',');?></span>
                            <div class="b_tooltip--tri" style="top:-44px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transform: rotate(180deg);"></div>
							
                        </div>
						
                      </div>
                      <div class='bar_group__bar thick' value='<?=$participant_graph_total_per;?>'>
                        <div class="b_tooltip" style="top:-33%;left:100%">
                          <div class="b_tooltip--tri"></div>
                          <span>Participant Raised<br>$ <?=number_format($participant_raised,2,'.',',');?></span>
                        </div>
                      </div>
                    </div>
					<? } ?>
						  
					<div class='bar_group' <?=$styleapplied;?>>
                      <div class='bar_group__bar thick' value='100'>
                        <div class="b_tooltip" style="top:220%;left:100%">
                          <span>Campaign Goal<br>$ <?=number_format($campaign_goal,2,'.',',');?></span>
                          <div class="b_tooltip--tri" style="top:-44px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transform: rotate(180deg);"></div>
                        </div>
						
                      </div>
                      <div class='bar_group__bar thick' value='<?=$campaign_graph_total_per;?>'>
                        <div class="b_tooltip" style="top:-33%;left:100%">
                          <div class="b_tooltip--tri"></div>
                          <span>Campaign Profit<br>$ <?=number_format($campaign_raised,2,'.',',');?></span>
                        </div>
                      </div>
                    </div>
				  </div>
				</div>
		    <div class=" Campaign_in">
			<?php
			  $invalidemailscount = count($invalidemails);
			  if ($invalidemailscount-1 > 0) { 
			?>
				<div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<div>
					<table border="0">
						<tr>
							<td>
								<span class="fa fa-times" style="color:red;" aria-hidden="true"></span>
								<span style="color:black;">You currently have <?=$invalidemailscount-1;?> invalid emails, Please select</span>
							</td>
							<td>
								<span class="fa fa-pencil" style="margin: 0 10px 0 0;" aria-hidden="true"></span>
								<span style="color:black;">edit and correct the email. Once you have corrected the email it will automatically be sent out.</span>
							</td>
						</tr>
					</table>
				</div>
				<?php
				  $invalidcounter = 0;
				  foreach ($invalidemails as $emailserror) {
					if ($invalidcounter > 0) {
					  echo "First Name: ".$emailserror['fname']." ,Last Name: ".$emailserror['lname']." ,Email: ".$emailserror['email']." ,Phone: ".$emailserror['phone']."<br>";
					}
					$invalidcounter++;
				  }
				?>
				</div>
			<?php } ?>
			<?
			  if (isset($_GET['did']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
			?>
			<div class="formdiv-in div_width">
				<div class="colmd_12a">
				  <div class="accordion_example4">
					<form name="input" action="" class="aj" method="POST">
                  <div class="accordion_in acc_active">
                    <div class="acc_head">Enter Names</div>
                    <div class="acc_content div5">
                      <div class="tabdiv">
                        <div class="rowdiv">
                          <table class="tab2">
                            <thead>
                              <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                              </tr>
                            </thead>
                            <tbody id="inputadd3">
                              <tr>
                                <td><input type="text" name="pname" id="pname" class="formdivtext5" value="<?=html_entity_decode($donor_name);?>" placeholder="Donor First Name"></td>
                                <td><input type="text" name="plname" id="plname" class="formdivtext5" value="<?=html_entity_decode($donor_lname);?>" placeholder="Donor Last Name"></td>
                                <td><input type="text" name="pemail" id="pemail" class="formdivtext5" value="<?=$donor_email;?>" placeholder="Donor Email"></td>
                                <td><input type="text" name="pphone" id="pphone" class="formdivtext5" value="<?=$donor_phone;?>" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                            </tbody>
                          </table>
						  <div class="Enter-Names-but-main">
						   <div class="col-md-6"><button type="submit" class="btn-4" style="width: 100%;" id="donoredited" name="donoredited">Update</button> </div>
						  </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  </form>
				  </div>
				</div>
			  <?
			  } else {
			  ?>
              <div class="colmd_12a">
                <div class="accordion_example4"> 
                  <!-- Section 1 -->
				  <form action="" method="POST">
                  <div class="accordion_in acc_active div7" style="    padding-bottom: 22px;">
                    <div class="acc_head">Donors List</div>
                    <div class="acc_content">
                      <div class="scrol">
                        <div class="tabdiv">
                          <div class="rowdiv">
                            <table class="tab3">
                              <thead>
                                <tr>
                                  <th>First Name</th>
								  <th>Last Name</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th>Receipt Email</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
							  <?php
							  if($iCountRecords>0){
								$appliedclass1 = '';
							  } else {
								$appliedclass1 = $appliedclass;
							  }
							  ?>
                              <tbody id="tbodypartlist" <?=$appliedclass1;?>>
								<?
								$iCountRecords = count($aDonorDetailSelected);
								$pid = $_GET['uid'];
								if($iCountRecords>0){
									for($i=0;$i<$iCountRecords;$i++){
									$onetime = 0;
									$pid = $_GET['uid'];
									$donor_sel_name = $aDonorDetailSelected[$i]['uname'];	
									$donor_sel_lname = $aDonorDetailSelected[$i]['ulname'];	
									$donor_sel_email = $aDonorDetailSelected[$i]['uemail'];	
									$donor_sel_phone = $aDonorDetailSelected[$i]['uphone'];	
									$donor_sel_id = $aDonorDetailSelected[$i]['uid'];
									$sent_email = $aDonorDetailSelected[$i]['sent_email'];
									$is_read = $aDonorDetailSelected[$i]['is_read'];
									//$donor_id = $aDonorDetailSelected[$i]['id'];
									$is_unsubscribe = $aDonorDetailSelected[$i]['is_unsubscribe'];
									$sms_sent_id = $aDonorDetailSelected[$i]['sms_sent_id'];
									$aDonorDonationsDetail = $oCampaign->getdonordonations($cid, $donor_sel_id, $pid, $donor_sel_email);
									$DonorDonationsDetailCount = count($aDonorDonationsDetail);
									$donation_amount = $aDonorDonationsDetail['donation_amount'];
									$donation_creationdate = $aDonorDonationsDetail['creationdate'];
									$receiptemail = $aDonorDonationsDetail['receiptemail'];
									$donordetails = 'fa-star-o';
									if ($DonorDonationsDetailCount > 0 && $donation_amount > 0) {
										$donordetails = 'fa-star';
										$styleapply = 'yellow';
										$titledonordetails = 'This donor makes '.$donation_amount.' donation @ '.$donation_creationdate.'';
									} else {
										$donordetails = 'fa-star-o';
										$styleapply = 'white';
										$titledonordetails = 'No donation made';
									}
									if($is_read == 1){
										$onlineoffline = 'fa-check-circle';
										$titleonlineoffline = 'Donor read the email & visited';
										$greenapply = 'color:green';
									} else {
										$onlineoffline = 'fa-circle-o';
										$titleonlineoffline = 'Donor hasn’t read the email';
										$greenapply = 'color:white';
									}
								?>
								<tr class="itemRow">
								  <td><?=html_entity_decode($donor_sel_name);?></td>
								  <td><?=html_entity_decode($donor_sel_lname);?></td>
								  <td><?=$donor_sel_email;?></td>
								  <!--<td><?=$donor_sel_phone;?></td>-->
								  <td class="phone_<?=$donor_sel_id;?>">
								    <? if ($donor_sel_phone != '') { ?>
									  <?=$donor_sel_phone;?>
									<? } else { ?>
									  Empty
									<? } ?>
									<br>
									<? if ($donor_sel_phone != "" && $donor_sel_phone != "000-000-0000" && $donor_sel_phone != "___-___-____") { ?>
									  <a class="editable-phone-number editable-css" id="<?=$donor_sel_id;?>" phone="<?=$donor_sel_phone;?>" data-test="yep" role="button" tabindex="0" title="Click here to enter phone#">Enter Phone#</a>
									<? } else { ?>
									  <a class="editable-phone-number editable-css" id="<?=$donor_sel_id;?>" phone="<?=$donor_sel_phone;?>" data-test="yep" role="button" tabindex="0" title="Click here to enter phone#">Enter Phone#</a>
									<? } ?>
								  </td>
								  <td><?=$receiptemail;?></td>
								  <td>
									<a>
									  <? if ($is_read == 1) { ?>
									  <span class="fa <?=$onlineoffline;?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$greenapply;?> !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titleonlineoffline;?>" title=""></span>
									  <? } else { ?>
									  <span class="fa <?=$onlineoffline;?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$greenapply;?> !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titleonlineoffline;?>" title=""></span>
									  <? } ?>
									</a>
									<? if ($fld_text_messaging == 1) { ?>
                                    <a>
										<?if ($sms_sent_id != "") {?>
                                        <img src="images/icon-sms-red.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Sent" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
										<?} else {?>
										<img src="images/icon-sms-unread.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Pending" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
										<?}?>
                                    </a>
									<? } ?>
									<a>
									  <span class="fa <?=$donordetails;?>" style="background: #9e9e9e;padding: 7px;color: <?=$styleapply;?>;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titledonordetails;?>" title=""></span>
									</a>
									<? 
									foreach ($array_bounce['results'] as $bounce) {
									//if ($bounce['source'] == 'Bounce Rule') {
									if ($bounce['recipient'] == $donor_sel_email && $is_read == 0 && $onetime == 0) {
									$onetime = 1;
									?> 
									<a href="?cid=<?=$cid;?>&pid=<?=$_GET['uid'];?>&did=<?=$donor_sel_id;?>&action=edit">
									  <span class="fa fa-pencil" style="margin: 0 10px 0 0;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="Edit this donor"></span>
									</a>
									<a>
									  <span class="fa fa-times" style="color:red;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="Invalid Email<br><?=$bounce['description'];?>"></span>
									</a>
									<? } } //} ?>
									<? if ($fld_text_messaging == 1) { ?>
									<a href="?cid=<?=$cid;?>&pid=<?=$_GET['uid'];?>&did=<?=$donor_sel_id;?>&hash=<?=$fld_campaign_hashkey;?>&number=<?=$donor_sel_phone;?>&action=sms_resent">
										<img src="images/sms-resend.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Re-Send" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
                                    </a>
									<? } ?>
									<? if ($is_unsubscribe == 1) { ?>
									<a>
									  <img src="dist/img/DoNotMail30x30.png" style="width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" data-toggle="tooltip" data-placement="top" data-original-title="Unsubscribed" />
									</a>
									<? } ?>
								  </td>
								</tr>
								<? } } else {
									if ($csvenable == true) { } else {
								?>
								<tr class="itemRow empty">
								  <td colspan="5" align="center">There is no any Donor</td>
								</tr>
								<? } } ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
				  <button class="btn btn-success waves-effect waves-light" type="submit" name="submitform" id="submitform" style="display:none"></button>
				  <button class="btn btn-success waves-effect waves-light" type="submit" name="golive" id="golive" style="display:none"></button>
				  <input type="hidden" name="fld_campaign_id" id="fld_campaign_id" value="<?=$_GET['cid']?>">
				  </form>
                  <!-- Section 1 --> 
				  <?php
					$invalidemailscount = count($invalidemails);
					$donorscount = count($aDonorDetailSelected);
					if ($invalidemailscount-1 == 0 || $donorscount > 0) { 
				  ?>
					<div id="notifications" class="alert alert-success alert-dismissable" style="padding: 6px 15px !important">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<h4 align="center">All emails will be sent automatically starting <?=$camp_starts;?></h4>
					</div>
				  <?php } ?>
				  <!-- Section 2 -->
				  <div id="donorssection">
				  <form name="input" action="" class="aj" method="POST">
                  <div class="accordion_in acc_active">
                    <div class="acc_head">Enter Names</div>
                    <div class="acc_content div5">
                      <div class="tabdiv">
                        <div class="rowdiv">
                          <table class="tab2">
                            <thead>
                              <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                              </tr>
                            </thead>
                            <tbody id="inputadd3">
                              <tr>
                                <td><input type="text" name="pname[]" id="pname" class="formdivtext5" placeholder="Donor First Name"></td>
                                <td><input type="text" name="plname[]" id="plname" class="formdivtext5" placeholder="Donor Last Name"></td>
                                <td><input type="email" name="pemail[]" id="pemail" class="formdivtext5 checkemail" placeholder="Donor Email"></td>
                                <td><input type="text" name="pphone[]" id="pphone" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                              <tr>
                                <td><input type="text" name="pname[]" id="pname" class="formdivtext5" placeholder="Donor First Name"></td>
								<td><input type="text" name="plname[]" id="plname" class="formdivtext5" placeholder="Donor Last Name"></td>
                                <td><input type="email" name="pemail[]" id="pemail" class="formdivtext5 checkemail" placeholder="Donor Email"></td>
                                <td><input type="text" name="pphone[]" id="pphone" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                              <tr>
                                <td><input type="text" name="pname[]" id="pname" class="formdivtext5" placeholder="Donor First Name"></td>
								<td><input type="text" name="plname[]" id="plname" class="formdivtext5" placeholder="Donor Last Name"></td>
                                <td><input type="email" name="pemail[]" id="pemail" class="formdivtext5 checkemail" placeholder="Donor Email"></td>
                                <td><input type="text" name="pphone[]" id="pphone" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                            </tbody>
                          </table>
						  <div class="Enter-Names-but-main">
						   <div class="col-md-6"><button type="submit" class="btn-4" style="width: 100%;" id="addtolist3" name="addtolist3">Add Donors<span class="fa fa-plus"></span></button> </div>
						   <div class="col-md-6"> <button type="button" class="btn-4" style="width: 100%;" id="addmore3">Add More<span class="fa fa-plus"></span></button> </div>
						  </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  </form>
				  </div>
                  <!-- Section 2 --> 
				  <!-- <div align="center" style="margin-bottom:40px">
					<h3>After adding donor's, please scroll down and click on "Update" button.</h3>
				  </div> -->
				  <!--<div align="center" style="margin-bottom:40px">
					<h1>HELP US SPREAD THE WORD!</h1>
					<div class="fleft">    
					  <div class="post_social">
						<a href="javascript:void(0)" id="facebookpopup" class="icon-fb" title="Facebook Share"><img src="images/fb.png"/></a>
						<a href="javascript:void(0)" class="icon-tw" onclick="javascript:genericSocialShare('http://twitter.com/share?text=<?=$text;?>;url=<?=$url;?>')" title="Twitter Share"><img src="images/tw.png"/></a>
						<a href="mailto:?subject=Join me in supporting <?=$fld_campaign_title;?>&body=<?=$email_msg;?>" class="icon-tw" title="Email Share"><img src="images/mail.png"/></a>
					  </div>
					  <script type="text/javascript" async >
						function genericSocialShare(url){
						  window.open(url,'sharer','toolbar=0,status=0,width=648,height=395');
						  return true;
						}
					  </script>
				    </div>
				  </div>-->
				  
                  <!-- Section 3 -->
				  <form name="input" action="" class="aj" method="POST" enctype="multipart/form-data">
                  <div class="accordion_in acc_active">
                    <div class="acc_head">Upload File</div>
                    <div class="acc_content">
                      <div class="scrol">
                        <div class="tabdiv">
                          <div class="rowdiv">
                            <div class="col-md-6 _6_3">
							  <input type="text" class="formdivtext4" readonly placeholder="File Name" id="filename">
                            </div>
                            <div class="col-md-2 col-sm-6 col-xs-6 ">
                              <div class="fileUpload btn btn-primary"> <span>Browse</span>
								<input type="file"  name="csvfile" class="upload" id="csvfile" onchange="updateFileName()"/>
                              </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-6 ">
                              <div> <button type="submit" class="btn-4" href="#" name="upload" id="upload">Upload<img src="images/img11.png"></button></div>
                            </div>
                            <p style="clear:both;"><a href="sample.csv">The file must be in CSV format. Sample file can be downloaded from here</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  </form>
                  <!-- Section 3 --> 
                </div>
              </div>
			  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" style="width:75%">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h4 class="modal-title" id="myModalLabel">Lifeline Instruction Video</h4>
                    </div>
                    <div class="modal-body">
                      <iframe width="100%" height="800" src="https://www.youtube.com/embed/UkNPFeYK5SM?rel=1"> </iframe>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <div class="col-lg-12 ">
			  <div class="col-sm-6 basic-but-left " align="left">
				<!--<button class="btn btn-primary waves-effect waves-light" type="button" onClick="step2(<?=$_GET['cid']?>)"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Back</button>
				<button class="btn btn-primary waves-effect waves-light" type="button" onclick="window.location.href='manage_campaign.php'"><span class="btn-label"><i class="fa fa-times"></i></span>Cancel</button>-->
			  </div>
			  <div class="col-sm-6 basic-but-right" align="right">
				<button class="btn btn-success waves-effect waves-light" type="button" id="preview" name="preview" onClick="window.open('../campaign.php?cid=<?=$fld_hashcamp;?>|<?=$cid;?>|<?=$pid;?>','Campaign Preview','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1366,height=768');">Preview </button>
				<!--<button class="btn btn-success waves-effect waves-light" type="button" id="submitform1" name="submitform1">Save <span class="btn-label forright-icon"><i class="fa fa-floppy-o"></i></span></button>-->
				<button class="btn btn-success waves-effect waves-light" type="button" id="golive1" name="golive1">Close <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
			  </div>
            </div>
          </div>
			<div class="clearfix"></div>
		  </div><? } ?></div>
		</div>
    </div>
    <!-- /.container-fluid -->
  </div></div>
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
<!--<script src="js/mask.js"></script>-->
<script src="bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!-- jQuery for carousel -->
<script src="bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="bower_components/owl.carousel/owl.custom.js"></script>
<script src="bower_components/sweetalert2/sweetalert2.min.js"></script>
<!--<script src="bower_components/sweetalert/jquery.sweet-alert.custom.js"></script>-->
<script language="javascript">
function step2(id){
	window.location.href = 'basic_information.php?cid='+id;
}

</script> 
<script src="js/jquery.inputmask.js"></script>
<script>
$("[data-mask]").inputmask("999-999-9999");
</script>
<script type="text/javascript" src="js/accounting.js"></script>
<script>
$('#submitform1').click(function(){
	$('#submitform').click();
});	

$('#golive1').click(function(){
	if ($("#previousdonors input:checkbox:checked").length > 0)
	{
		swal('ERROR','You have select pervious donors to add to your campaign, please select Add to List before you select update. If you dont wish to add pervious donors you must unselect them.');
	}
	else
	{
		swal(
			'Thank You!',
			'Your emails have been added.',
			'success'
		);
		$('#golive').click();
	}
});	


function updateFileName() {
    var csvfile = document.getElementById('csvfile');
    var filename = document.getElementById('filename');
    var fileNameIndex = csvfile.value.lastIndexOf("\\");
    filename.value = csvfile.value.substring(fileNameIndex + 1);
}

function addCommas(x,txtname) {

	var mval = accounting.formatMoney(x); 
	mval = mval.replace('$', '');
	document.getElementById(txtname).value = mval;
}

var counter = $('.checkemail').length;
$(".checkemail").blur(function() {
	//alert($(this).val());
	if ($(this).val() != '') {
	var getid = $(this).attr('id');
	var textbox = $(this).val().replace(/\s+/g, '');
	$(this).filter(function(){
        var emil=$(this).val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( emil ) ) {
			$('#'+getid+'').get(0).setCustomValidity("Please enter a valid email...!");
            swal('ERROR','Please enter a valid email...!');
        } else {
			$('#'+getid+'').get(0).setCustomValidity("");
        }
    });
	var cid = <?=$_GET['cid'];?>;
	var uid = <?=$_GET['uid'];?>;
	var thelink = 'checkemails.php';
	var formData = "act=2&email="+textbox+"&cid="+cid+"&uid="+uid+"";  //Name value Pair
	$.ajax({
		url : thelink,
		type: "POST",
		data : formData,
		success: function(data)
		{
			var jdata = JSON.parse(data);
			if (jdata.get_results > 0) {
				swal('ERROR','Email Already Exists...!');
				$('#'+getid+'').get(0).setCustomValidity("Email Already Exists");
				$('#'+getid+'').focusout();
			} else {
				//alert('Email Not Exists...!');
				$('#'+getid+'').get(0).setCustomValidity("");
				$('#'+getid+'').focusout();
			}
		},
		error: function (data)
		{
	
		}
	});
	}
});

function validatePhone(txtPhone) {
	var filter = /^(\d{3})-(\d{3})-(\d{4})$/;
	if (filter.test(txtPhone)) {
		return true;
	} else {
		return false;
	}
}
			
$(".checkphone").blur(function() {
	//alert($(this).val());
	if ($(this).val() != '') {
	var getid = $(this).attr('id');
	var textbox = $(this).val().replace(/\s+/g, '');
	if (validatePhone(textbox)) {
		$('#'+getid+'').get(0).setCustomValidity("");
	} else {
		$('#'+getid+'').get(0).setCustomValidity("Please enter a valid phone #...!");
		swal('ERROR','Please enter a valid phone #...!');
	}
	var cid = <?=$_GET['cid'];?>;
	var uid = <?=$_GET['uid'];?>;
	var thelink = 'checkphone.php';
	var formData = "act=2&phone="+textbox+"&cid="+cid+"&uid="+uid+"";  //Name value Pair
	$.ajax({
		url : thelink,
		type: "POST",
		data : formData,
		success: function(data)
		{
			var jdata = JSON.parse(data);
			if (jdata.get_results > 0) {
				swal('ERROR','Phone # Already Exists...!');
				$('#'+getid+'').get(0).setCustomValidity("Phone # Already Exists");
				$('#'+getid+'').focusout();
			} else {
				//alert('Email Not Exists...!');
				$('#'+getid+'').get(0).setCustomValidity("");
				$('#'+getid+'').focusout();
			}
		},
		error: function (data)
		{
		}
	});
	}
});

$(document).on('click', '.addtolist1', function() {
	if ($(".empty")[0]){
		$("#tbodypartlist").empty();
		$("#tbodypartlist").removeClass("empty");
		var selected = [];
		$('.selectcheck input:checked').each(function() {
			var innerselected = '<tr class="itemRow"><input type="hidden" name="id[]" value="'+$(this).attr('value')+'"><input type="hidden" name="name[]" value="'+$(this).attr('name')+'"><input type="hidden" name="lname[]" value="'+$(this).attr('lname')+'"><input type="hidden" name="email[]" value="'+$(this).attr('email')+'"><input type="hidden" name="phone[]" value="'+$(this).attr('phone')+'"><td>'+$(this).attr('name')+'</td><td>'+$(this).attr('lname')+'</td><td>'+$(this).attr('email')+'</td><td>'+$(this).attr('phone')+'</td><td><a class="removetr" href="#"><span class="fa fa-times" aria-hidden="true"></span></a></td></tr>';
			$(innerselected).appendTo("#tbodypartlist");
			$(this).attr('name');
		});
	} else {
		var selected = [];
		$('.selectcheck input:checked').each(function() {
			var innerselected = '<tr class="itemRow"><input type="hidden" name="id[]" value="'+$(this).attr('value')+'"><input type="hidden" name="name[]" value="'+$(this).attr('name')+'"><input type="hidden" name="lname[]" value="'+$(this).attr('lname')+'"><input type="hidden" name="email[]" value="'+$(this).attr('email')+'"><input type="hidden" name="phone[]" value="'+$(this).attr('phone')+'"><td>'+$(this).attr('name')+'</td><td>'+$(this).attr('lname')+'</td><td>'+$(this).attr('email')+'</td><td>'+$(this).attr('phone')+'</td><td><a class="removetr" href="#"><span class="fa fa-times" aria-hidden="true"></span></a></td></tr>';
			$(innerselected).appendTo("#tbodypartlist");
			$(this).attr('name');
		});
	}
	$('#golive').click();
});
$(document).on('click', '.removetr', function() {
    var $tr = $(this).closest('tr');
    if ($tr.attr('class') == 'itemRow') {
        $tr.nextUntil('tr[class=itemRow]').andSelf().remove();
    }
    else {
        $tr.remove();
    }
		$("#tbodypartlist").AddClass("empty");
		var innerselected = '<tr class="itemRow"><td colspan="5" align="center">There is no any Donor</td></tr>';
		$(innerselected).appendTo("#tbodypartlist");
});
$("#checkAll").change(function () {
    $(".checkboxes").prop('checked', $(this).prop("checked"));
});
$(document).on('click', '#addmore3', function() {
	var masked = "'mask': ['999-999-9999', '999-999-9999']";
	var insertnewfield = '<tr><td><input type="text" name="pname[]" id="pname" class="formdivtext5" placeholder="Donor First Name"></td><td><input type="text" name="plname[]" id="plname" class="formdivtext5" placeholder="Donor Last Name"></td><td><input type="email" name="pemail[]" id="pemail" class="formdivtext5 checkemail" placeholder="Donor Email"></td><td><input type="text" name="pphone[]" id="pphone" class="formdivtext5 checkphone" data-inputmask="'+masked+'" data-mask="" placeholder="___-___-____"></td></tr>';
	$(insertnewfield).appendTo("#inputadd3");
	$("[data-mask]").inputmask("999-999-9999");
});

$('#searched').click(function() {
    var searchtext = $('#fld_search').val();
	var cid = <?=$_GET['cid'];?>;
	var uid = <?=$_GET['uid'];?>;
	var formData = "cid="+cid+"&uid="+uid+"&query="+searchtext+"";  //Name value Pair
	$.ajax({
		url : "searchdonor.php",
		type: "POST",
		data : formData,
		success: function(data)
		{
			//alert('Searched...!');
			var jdata = JSON.parse(data);
			var searched = jdata.searched;
			$('.addedlist').empty();
			for(var searchedcount = 0; searchedcount < searched.length; searchedcount++) {
				var suid = searched[searchedcount]["uid"];
				var suname = searched[searchedcount]["uname"];
				var sulname = searched[searchedcount]["ulname"];
				var suemail = searched[searchedcount]["uemail"];
				var suphone = searched[searchedcount]["uphone"];
				$('<tr class="selectcheck"><td><input type="checkbox" value="'+suid+'" name="'+suname+'" lname="'+sulname+'" email="'+suemail+'" phone="'+suphone+'" class="checkbox_div checkboxes"></td><td>'+suname+'</td><td>'+sulname+'</td><td>'+suemail+'</td><td>'+suphone+'</td><td><a href="?cid=<?=$_GET['cid'];?>&pmid='+suid+'&action=delete"><span class="fa fa-times" aria-hidden="true"></span></a></td></tr>').appendTo('.addedlist');
			}
		},
		error: function (data)
		{
	
		}
	});
});

//Popup for Facebook
$(document).on('click', '#facebookpopup', function(){
	var textfacebook = "<span style='margin-top:12px; display:block; text-align:left'>Facebook may prompt you to add a donation button as seen below.<br><b style='color:red'><i>PLEASE DO NOT SELECT NONPROFIT.</b></i> If this message appears simply close the window by selecting the X in the upper right corner.<br><b><i><?php echo sWEBSITENAME;?> has no control over donations made through Facebook.</i></b><br><img src='<?=sHOMES?>images/facebook_popup.jpg' width='100%' /></span>";
	var titlefacebook = "Facebook post submission";
	swal({
		text: textfacebook,
		showCancelButton: false,
		showCloseButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: "Close Message Box",
		closeOnConfirm: false
	}).then(function () {
		genericSocialShare('http://www.facebook.com/sharer.php?u=<?=$url;?>');
	}, function (dismiss) {
		if (dismiss === 'cancel') {
			swal(
				'Facebook post submission',
				'Cancelled',
				'error'
			);
		}
	});
});

</script>
<script src="../bars/bars.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jeditable.js/2.0.6/jquery.jeditable.js"></script> 
<script src="js/jquery.jeditable.masked.js" type="text/javascript" ></script>
<script type="text/javascript" src="js/jquery.maskedinput.js"></script>
<script>
$(document).ready(function() {
	/* data that will be sent along */
	var submitdata = {}
	/* this will make the save.php script take a long time so you can see the spinner ;) */
	submitdata['slow'] = true;
	submitdata['mode'] = 2;
	submitdata['cid'] = <?=$cid;?>;
	submitdata['pid'] = <?=$uid;?>;
	var pid = '';
	$(".editable-phone-number").editable("savenumber.php", {
		indicator : "<img src='img/spinner.svg' />",
		//type : "text",
					
		// only limit to three letters example
		//pattern: "[A-Za-z]{3}",
		onedit : function() { 
			did = $(this).attr('id');
			var phone = $(this).attr('phone');
			submitdata['did'] = did;
			return true;
		},
		//onblur  : function() { return true },
		before : function() { console.log('Triggered before form appears')},
		callback : function(result, settings, submitdata) {
			$('.phone_'+did).html(result);
			//console.log('Triggered after submit');
			//console.log('Result: ' + result);
			//console.log('Settings.width: ' + settings.width);
			//console.log('Submitdata: ' + submitdata.pwet);
		},
		cancel : 'Cancel',
		cssclass : '',
		cancelcssclass : 'btn btn-c1',
		submitcssclass : 'btn btn-s1',
		maxlength : 200,
		// select all text
		select : true,
		//label : 'This is a label',
		onreset : function() { console.log('Triggered before reset') },
		onsubmit : function() { console.log('Triggered before submit') },
		showfn : function(elem) { elem.fadeIn('slow') },
		submit : 'Save',
		submitdata : submitdata,
		/* submitdata as a function example
		submitdata : function(revert, settings, submitdata) {
		console.log("Revert text: " + revert);
		console.log(settings);
		console.log("User submitted text: " + submitdata.value);
		},
		*/
		//type : "masked",
		//mask : "___-___-____",
		tooltip : "Click here to enter phone#",
		inputcssclass : 'formdivtext5',
		name : 'uphone',
		width : '100%'
	});
	/* target as a function example
	$(".editable-text-full").editable(function(input, settings, elem) {
		console.log(input);
		console.log(settings);
		console.log($(elem).data('test'));
	}, {});
	*/
});
</script>
<?php if (isset($_GET['video']) && $_GET['video'] == 1) { ?>
<script>
$( document ).ready(function() {
	$("#myModal").modal('show');
});
</script>
<?php } ?>
</body>
</html>
<? } else {
	$oregister->redirect('expired_token.php');
} } ?>
<? include_once('bottom.php');?>
<script>
$( document ).ready(function() {
    if (window.location.hash.substr(1)) {
		var type = window.location.hash.substr(1);
		var scrollTop     = $(window).scrollTop(),
		elementOffset = $('#'+type+'').offset().top,
		distance      = (elementOffset - scrollTop);
		$('html,body').animate({ scrollTop: elementOffset }, 'slow');
	}
	$('#myModal').on('hidden.bs.modal', function () {
		var video = $("iframe").attr("src");
		$("iframe").attr("src","");
		$("iframe").attr("src",video);
	});
});
</script>