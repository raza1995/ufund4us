<?php
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;

checkAndSetInArray($REQUEST, 'action', '');
checkAndSetInArray($REQUEST, 'message', '');
checkAndSetInArray($REQUEST, 'did', 0);

 

//basic declaration of required variables
$invalidemails =  [];
$styleapplied = "";
$aParticipantDetailSelected = []; 
$aParticipantDetailSelected = [];
$iCountRecords = 0;

if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
} else {
	if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 5) {
		$oregister->redirect('dashboard.php');
	}
}
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

$sStartCampMenu = 'active';
$jslink = 'golive.php?cid='.$REQUEST['cid'].'';
$csvenable = false;
$appliedclass = 'class="empty"';
$start_campaign = 'start_campaign.php?m=e&cid='.$REQUEST['cid'].'';
$basic_information = 'basic_information.php?cid='.$REQUEST['cid'].'';
$sPageName = '<li><a href="start_campaign.php?m=e&cid='.$REQUEST['cid'].'">Start New Campaign</a></li> <li><a href="basic_information.php?cid='.$REQUEST['cid'].'">Basic Information</a></li> <li>Build Your Team</li>';
/*if($REQUEST['fld_campaign_id'] > 0)
{
	//echo '<pre>';
	//print_r($REQUEST);
	
	$iCid = $REQUEST['fld_campaign_id'];
	$sTitle = $REQUEST['fld_campaign_title'];
	$sOrgName = $REQUEST['fld_organization_name'];
	$sTeamName = $REQUEST['fld_team_name'];
	$sTeamSize = $REQUEST['fld_team_size'];
	$sStartDate = date('Y-m-d',strtotime($REQUEST['fld_campaign_sdate']));
	$sEndDate = date('Y-m-d',strtotime($REQUEST['fld_campaign_edate']));
	$sCGoal = $REQUEST['fld_campaign_goal'];
	$sPGoal = $REQUEST['fld_participant_goal'];	
	$sDesc1 = $REQUEST['fld_desc1'];	
	$sDesc2 = $REQUEST['fld_desc2'];	
	$sDonationLevel1 = $REQUEST['fld_donation_level1'];	
	$sDonationLevel2 = $REQUEST['fld_donation_level2'];	
	$sDonationLevel3 = $REQUEST['fld_donation_level3'];	
	
	$oCampaign->update_campaign($iCid,$sTitle,$sOrgName,$sTeamName,$sTeamSize,$sStartDate,$sEndDate,$sCGoal,$sPGoal,$sDesc1,$sDesc2,$sDonationLevel1,$sDonationLevel2,$sDonationLevel3);
	if($iCid > 0)
	{
		$oregister->redirect('build_team.php?cid='.$iCid);
	}
}
*/
//default
$after_app_fee_percentage = 0.8;

$camp_inform = '';
$basic_inform = '';
$build_team = '';
$go_live = '';
if (isset($REQUEST['cid'])) {
	$ciddd = $REQUEST['cid'];
	$camp_inform = 'href="start_campaign.php?m=e&cid='.$ciddd.'"';
	$basic_inform = 'href="basic_information.php?cid='.$ciddd.'"';
	$build_team = 'href="build_team.php?cid='.$ciddd.'"';
	$go_live = 'href="golive.php?cid='.$ciddd.'"';
}

if (isset($REQUEST['pid']) && isset($REQUEST['action']) && $REQUEST['action'] == 'edit') {
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	$aDonorDetailEdit = $oCampaign->getparticipantdetailedit($cid, $pid);
	$donor_email = $aDonorDetailEdit['uemail'];
	$donor_name = $aDonorDetailEdit['uname'];
	$donor_lname = $aDonorDetailEdit['ulname'];
	$donor_phone = $aDonorDetailEdit['uphone'];
}

if (isset($REQUEST['pid']) && $REQUEST['action'] == 'addparticipant') {
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	$cmid = $_SESSION['uid'];
	$aUserDetail = $oregister->getuserdetail($pid);
	$pfname = $aUserDetail['fld_name'];
	$plname = $aUserDetail['fld_lname'];
	$pemail = $aUserDetail['fld_email'];
	$pphone = $aUserDetail['fld_phone'];
	$oCampaign->insert_campaign_participants2($cid, $pid, $cmid, $pfname, $plname, $pemail, $pphone);
	$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (isset($REQUEST['pid']) && isset($REQUEST['cid']) && isset($REQUEST['hash']) && isset($REQUEST['action']) && $REQUEST['action'] == 'sms_resent') {
	$hash = $REQUEST['hash'];
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	$sms_sent_id = "";
	//Campaign Details
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$ctitle = $aCampaignDetail['fld_campaign_title'];
	$cuid = $aCampaignDetail['fld_uid'];
	$cname = $aCampaignDetail['fld_cname'];
	$clname = $aCampaignDetail['fld_clname'];
	
	//Participant Details
	$aParticipantDetail = $oCampaign->getselectedparticipantdetails2($cid,$cuid,$pid);
	$participant_pid = $aParticipantDetail['pid'];
	$participant_fname = $aParticipantDetail['uname'];
	$participant_lname = $aParticipantDetail['ulname'];
	$participant_phone = $aParticipantDetail['uphone'];
	$participant_email = $aParticipantDetail['uemail'];
	//Campaign Manager Details
	$campaign_manager_id = $cuid;
	$campaign_manager_name = $cname." ".$clname;
	
	$generate_short_link = sHOME.'l.php?v='.$hash.'&u='.$participant_pid.'&m=1';
	$ParticipantFullName = trim($participant_fname." ".$participant_lname);
	$body = "Hi! It’s $ParticipantFullName.\n";
	$body = "You are being invited to join $ctitle by $campaign_manager_name. Please click on the link below to join this campaign.\n";
	$body .= "".$generate_short_link."\n";
	$body .= "Thank You!";
	
	if ($participant_phone != "" && $participant_phone != "000-000-0000" && $participant_phone != "___-___-____") {
		if ($sent_sms = send_sms(str_replace("-","",$participant_phone),utf8_encode($body))) {
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
					
				$oCampaign->sms_resend_participants2($cid, $pid, $participant_fname, $participant_lname, $participant_email, $participant_phone, $campaign_manager_name, $sms_sent_id, $sms_date_created, $sms_details);
			}
		} else {
			//Error when sending SMS
		}
	} else {
		//Invalid Number
	}
	$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (isset($REQUEST['pid']) && $REQUEST['action'] == 'resent') {
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	$oCampaign->resent_participants_details($cid, $pid);
	$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (isset($REQUEST['pid']) && $REQUEST['action'] == 'delete') {
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	$oCampaign->delete_participants_details($cid, $pid);
	$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (isset($REQUEST['pemail']) && $REQUEST['action'] == 'delete') {
	$pemail = $REQUEST['pemail'];
	$cid = $REQUEST['cid'];
	$oCampaign->delete_participants($cid, $pemail);
	$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (array_key_exists('donoredited', $REQUEST)) {
	$pname = $REQUEST['pname'];
	$plname = $REQUEST['plname'];
	$pemail = $REQUEST['pemail'];
	$phone = $REQUEST['pphone'];
	$uid = $_SESSION['uid'];
	$pid = $REQUEST['pid'];
	$cid = $REQUEST['cid'];
	if (emailIsValid($pemail)) {
		if ($oCampaign->update_participant($cid, $pid, $uid, $pname, $plname, $pemail, $phone))
		{
			$oregister->redirect('build_team.php?cid='.$cid.'');
		}
	}
}

if (array_key_exists('addtolist3', $REQUEST)) {
	$pname = $REQUEST['pname'];
	$plname = $REQUEST['plname'];
	$pemail = $REQUEST['pemail'];
	$phone = $REQUEST['pphone'];
	$uid = $_SESSION['uid'];
	$cid = $REQUEST['cid'];
	$invalidemails = [];
	foreach( $pname as $key => $name ) {
		if (!empty($name) && (emailIsValid($pemail[$key]) || phoneIsValid($phone[$key]))) {
			$aUserDetail = $oregister->getuserdetailbyemailforparticipant($pemail[$key],$phone[$key]);
			$getid = isset($aUserDetail['fld_uid']) ?  $aUserDetail['fld_uid'] : "";
			if ($getid != '') {
				$oCampaign->insert_campaign_addparticipants2($cid, $getid, $uid, sanitize($name), sanitize($plname[$key]), $pemail[$key], $phone[$key]);
			} else {
				$generatepasshash = $oregister->generatepasshash(10);
				$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
				$oCampaign->insert_campaign_addparticipants1($cid, $uid, sanitize($name), sanitize($plname[$key]), $pemail[$key], $phone[$key], $Password);
			}
		} else {
			if ($pemail[$key] != '') {
				$data['fname'] = $pname[$key]; 
				$data['lname'] = $plname[$key]; 
				$data['email'] = $pemail[$key]; 
				$data['phone'] = $phone[$key]; 
				array_push($invalidemails, $data);
			}
		}
	}
	$oCampaign->send_email_to_participants($cid, $uid);
}

if (array_key_exists('submitform', $REQUEST)) {
	$cid = $REQUEST['cid'];
	$id = $REQUEST['id'];
	$name = $REQUEST['name'];
	$lname = $REQUEST['lname'];
	$email = $REQUEST['email'];
	$phone = $REQUEST['phone'];
	$uid = $_SESSION['uid'];
	$invalidemails = [];
	foreach( $id as $key => $n ) {
		if ($name[$key] != '' && (emailIsValid($email[$key]) || phoneIsValid($phone[$key]))) {
			$aUserDetail = $oregister->getuserdetailbyemailforparticipant($email[$key],$phone[$key]);
			$getid = $aUserDetail['fld_uid'];
			if ($getid != '') {
				$oCampaign->insert_campaign_participants2($cid, $getid, $uid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key]);
			} else {
				$generatepasshash = $oregister->generatepasshash(10);
				$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
				$oCampaign->insert_campaign_participants1($cid, $uid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key], $Password);
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
		//$oCampaign->insert_campaign_participants($cid, $n, $uid, $name[$key], $email[$key], $phone[$key]);
	}
	//$oregister->redirect('build_team.php?cid='.$cid.'');
}

if (array_key_exists('golive1', $REQUEST)) {
	$cid = $REQUEST['cid'];
	$uid = $_SESSION['uid'];
	//$oCampaign->makeitlive($cid, $uid);
	//$oCampaign->send_email_to_manager($cid, $uid);
	$oCampaign->send_email_to_participants($cid, $uid);
	if( 	isset($REQUEST['id'])
		&&	isset($REQUEST['name'])
		&&	isset($REQUEST['lname'])
		&&	isset($REQUEST['email'])
		&&	isset($REQUEST['phone'])
		
	 ){

		$id = $REQUEST['id'];
		$name = $REQUEST['name'];
		$lname = $REQUEST['lname'];
		$email = $REQUEST['email'];
		$phone = $REQUEST['phone'];
		$invalidemails = [];
		foreach( $id as $key => $n ) {
			if ($name[$key] != '' && (emailIsValid($email[$key]) || phoneIsValid($phone[$key]))) {
				$aUserDetail = $oregister->getuserdetailbyemailforparticipant($email[$key],$phone[$key]);
				$getid = $aUserDetail['fld_uid'];
				if ($getid != '') {
					$oCampaign->insert_campaign_participants2($cid, $getid, $uid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key]);
				} else {
					$generatepasshash = $oregister->generatepasshash(10);
					$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
					$oCampaign->insert_campaign_participants1($cid, $uid, sanitize($name[$key]), sanitize($lname[$key]), $email[$key], $phone[$key], $Password);
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
	}
	$oregister->redirect('confirmation.php?cid='.$cid.'');
}

if (array_key_exists('upload', $REQUEST)) {
	$file = $REQUEST['csvfile'];
	$filename = "csv_".rand(0000000001,9999999999).".csv";
    if ( 0 < $_FILES['csvfile']['error'] ) {
        //echo 'Error: ' . $_FILES['csvfile']['error'] . '<br>';
    }
    else {
        move_uploaded_file($_FILES['csvfile']['tmp_name'], 'uploads/' . $filename);
    }
	
	$file = fopen("uploads/$filename","r");
	$head = fgetcsv($file, 4096, ',', '"');
	$cid = $REQUEST['cid'];
	$uid = $_SESSION['uid'];
	$roleid = $_SESSION['role_id'];
	$invalidemails = [];
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
		if (emailIsValid($csv0) || phoneIsValid($csv4)) {
			$aParticipantDetailSelected = $oCampaign->getparticipantdetailselectedbycsv($cid, $uid, $roleid, $csv0, $csv4);
			if ($aParticipantDetailSelected['uemail'] != $csv0) {		
				//$csvarray[] = "$csv1,$csv2,$csv3,$csv4"; 
				$cid = $REQUEST['cid'];
				$uid = $_SESSION['uid'];
				if ($csv1 != '') {
					$aUserDetail = $oregister->getuserdetailbyemailforparticipant($csv3,$csv4);
					$getid = $aUserDetail['fld_uid'];
					if ($getid != '') {
						$oCampaign->insert_campaign_participants2($cid, $getid, $uid, sanitize($csv1), sanitize($csv2), $csv3, $csv4);
					} else {
						$generatepasshash = $oregister->generatepasshash(10);
						$Password = $oregister->encrypt($generatepasshash,sENC_KEY);
						$oCampaign->insert_campaign_participants1($cid, $uid, sanitize($csv1), sanitize($csv2), $csv3, $csv4, $Password);
					}
				}
				$oCampaign->send_email_to_participants($cid, $uid);
			}
		} else {
			$data['fname'] = $csv1; 
			$data['lname'] = $csv2; 
			$data['email'] = $csv3; 
			$data['phone'] = $csv4; 
			array_push($invalidemails, $data);
		}
	}
	$csvenable = true;
	$appliedclass = '';
}

if($REQUEST['cid'] > 0)
{
	$cid = $REQUEST['cid'];
	$uid = $_SESSION['uid'];
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$after_app_fee_percentage = get_after_app_fee_percentage($aCampaignDetail);

	$aParticipantDetail = $oCampaign->getparticipantdetail($cid, $uid);
	$roleid = $_SESSION['role_id'];
	// $aParticipantDetailSelected = $oCampaign->getparticipantdetailselected($cid, $uid, $roleid);
	$aParticipantDetailSelected = $oCampaign->getparticipantdetailselected($cid, $uid, 1);//1 role id of admin; we are showing all part. to admin and Kurt want to show all part for camp manager and all
	// echo 'aParticipantDetailSelected<pre>'; print_r([$cid, $uid, $roleid, $aParticipantDetailSelected]); die();
	$fld_campaign_hashkey = $aCampaignDetail['fld_campaign_hashkey'];
	$cuid = $aCampaignDetail['fld_uid'];
	$fld_text_messaging = $aCampaignDetail['fld_text_messaging'];
	$fld_campaign_title = $aCampaignDetail['fld_campaign_title'];
	$fld_cemail = $aCampaignDetail['fld_cemail'];
	$fld_campaign_logo1 = $aCampaignDetail['fld_campaign_logo'];
	$fld_organization_name = $aCampaignDetail['fld_organization_name'];
	$fld_team_name = $aCampaignDetail['fld_team_name'];
	$fld_team_size = $aCampaignDetail['fld_team_size'];
	$fld_donor_size = $aCampaignDetail['fld_donor_size'];
	$fld_pin = $aCampaignDetail['fld_pin'];
	$fld_campaign_id = str_pad($aCampaignDetail['fld_campaign_id'], 7, "0", STR_PAD_LEFT);
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
	$fld_accid = $aCampaignDetail['fld_ac'];
	
	$fld_show_participant_goal = $aCampaignDetail['fld_show_participant_goal'];
	$fld_ab1575_pupil_fee = $aCampaignDetail['fld_ab1575_pupil_fee'];
	
	
	$campaign_edate = date('Ymd',strtotime($aCampaignDetail['fld_campaign_edate']));
	$status = $aCampaignDetail['fld_status'];
	$current_date = date("Ymd");
	
	$aCampaignGraphTotal = $oCampaign->getcampaigngraphtotal2($cid);
	$campaign_goal = $aCampaignGraphTotal['campaign_goal'];
	$campaign_raised = $aCampaignGraphTotal['campaign_raised'];
	$campaign_graph_total_per = 0;
	if($campaign_goal > 0){ //it will safe us from divisible by zero error
		$campaign_graph_total_per = ($campaign_raised / $campaign_goal) * 100;
	}
	if ($fld_campaign_logo1 != '') {
		$fld_campaign_logo = '<img src="uploads/logo/'.$fld_campaign_logo1.'" height="15%" />';
	} else {
		$fld_campaign_logo = '';
	}
	
	

	$getBrandData = $oregister->getbranddetail('2', $cuid);
	if (   is_array($getBrandData) 
		&& count($getBrandData) > 0 
		&& isset($getBrandData['fld_brand_logo_header']) && $getBrandData['fld_brand_logo_header'] != ''
	) {
		$brand_logo = $getBrandData['fld_brand_logo_header'];
		$brand_logo = '<img src="uploads/brandlogo/'.$brand_logo.'" width="35%" height="15%" />';
	} else {
		$brand_logo = '<img src="emails/logo.png" width="35%" height="15%" />';
	}

	$header = '
	<table width="100%" border="0">
		<tbody>
			<tr>
				<td width="30%" align="left">'.$fld_campaign_logo.'</td>
				<td width="40%" style="padding: 0px 20px; text-align:center;font-family:arial; font-size:16px">
					<h2 style="font-family:arial; font-family:arial; font-size:17px"><b><u><i>Instructions to Join <br>'.$fld_campaign_title.'</i></u></b></h2>
					<h2 align="center" style="font-family:arial; font-size:17px"><b>Campaign starts '.$fld_campaign_sdate.'<br>Campaign ends '.$fld_campaign_edate.'</b></h2>
				</td>
				<td width="30%" align="right">'.$brand_logo.'</td>
			</tr>
		</tbody>
	</table>';
	$html = '
	<style>
	.arrow {
 	   width:120px;
	}

	.line {
	    margin-top:14px;
	    width:90px;
	    background:blue;
	    height:10px;
	    float:left;
	}
	
	.point {    
    	width: 0;
    	height: 0; 
    	border-top: 20px solid transparent;
    	border-bottom: 20px solid transparent;
    	border-left: 30px solid blue;
    	float:right !important;
	}
	.myfixed1 { position: absolute;
 		overflow: visible;
 		left: 0;
 		bottom: 0;
 		border: 1px solid #880000;
 		background-color: #FFEEDD;
 		background-gradient: linear #dec7cd #fff0f2 0 1 0 0.5;
 		padding: 1.5em;
 		font-family:sans;
 		margin: 0;
	}
	.myfixed2 { 
		position: fixed;
 		overflow: auto;
 		bottom:29mm;
 		
	}
	</style>
	';
	//$html .= '';
	$html .= '<br/><br/><div align="left" style="font-family:arial; margin-top:40px; font-size:16px"><b>Step by step instructions to become a participant:</b></div>';
	$html .= '<table width="100%" border="0" style="border-collapse: collapse;font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">
				<tbody>
				   <tr>
					  <td width="40%" style="border-right:solid 2px #000000">
						<b>First Time Participants:</b>
					  </td>
					  <td width="60%" style="padding-left:10%">
						<b>Existing Participants:</b>
					  </td>
				   </tr>
				   <tr>
					  <td width="40%" style="border-right:solid 2px #000000">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Log onto '.SITE_DOMAIN.'</div>
					  </td>
					  <td width="60%" style="padding-left:10%">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Log onto '.SITE_DOMAIN.'</div>
					  </td>
				   </tr>
				   <tr>
					  <td width="40%" style="border-right:solid 2px #000000">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Select Join Campaign</div>
					  </td>
					  <td width="60%" style="padding-left:10%">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Select Sign In</div>
					  </td>
				   </tr>
				   <tr>
					  <td width="40%" style="border-right:solid 2px #000000">
						&nbsp;
					  </td>
					  <td width="60%" style="padding-left:10%">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Enter your Email address and password</div>
					  </td>
				   </tr>
				   <tr>
					  <td width="40%" style="border-right:solid 2px #000000">
						&nbsp;
					  </td>
					  <td width="60%" style="padding-left:10%">
						<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Select Join Campaign</div>
					  </td>
				   </tr>
				</tbody>
			  </table>';
	$html .= '<div align="center" style="font-family:arial; margin-top:3px; font-size:16px">Enter campaign # <b><u>'.$fld_campaign_id.'</u></b> & campaign ID # <b><u>'.$fld_pin.'</u></b></div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:10px; font-size:16px"><b>Create an account (follow the on screen instructions):</b></div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">After creating your account, you will be prompted to enter your email and password again.<br>This will take you to edit your profile.</div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">If you already have an account login as shown on the page.</div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:10px; font-size:16px"><b>Enter your profile:</b></div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">Upload your profile picture (this will be used to personalize your campaign)</div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:100px; font-size:16px"><b><u>Save and continue</u></b></div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:10px; font-size:16px"><b>Enter your donors:</b></div>';
	$html .= '<table width="100%" border="0">
		<tbody>
			<tr>
				<td width="40%" align="left" style="font-family:arial; font-size:16px; padding-left:50px"><div align="left" style="font-family:arial; margin-top:15px; margin-left:100px; font-size:16px">We are asking you to enter '.str_pad($fld_donor_size, 2, "0", STR_PAD_LEFT).' email addresses.<br>Enter first name, last name and email address. Phone number is optional. Enter 3 email addresses and then select “Add Donors.” This will allow you to continue to add new email addresses.</td>
				<td width="60%" align="right"><img src="images/donors_screen.png" width="60%" height="23%" /></td>
			</tr>
		</tbody>
	</table>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:50px; font-size:16px">When you login into your account for a second time follow the steps below:</div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:100px; font-size:16px">Log onto '.SITE_DOMAIN.'</div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:3px; margin-left:150px; font-size:16px">Select Manage Campaign, then Select your campaign name. This will take you to your donor email page.<br>You can review your donation page by selecting “Campaign Link.”</div>';
	$html .= '<div align="left" class="myfixed2" style="font-family:arial; font-size:16px"><img src="images/manage_campaign.png" width="100%" height="30%" /></div>';
	$html .= '<br/><div align="center" style="font-family:arial; margin-top:130px; font-size:16px"><b><u>Thank you for joining '.$fld_campaign_title.'<u></b></div>';
	$html .= '<div align="left" style="font-family:arial; margin-top:10px; margin-left:150px; font-size:15px"><b><i>"If you fail to plan, you are planning to fail."</i></b></div>';
	$html .= '<div align="left" style="font-family:new times roman; margin-top:-8px; margin-left:530px; font-size:14px"><i>by Benjamin Franklin</i></div>';
	$html .= '<div align="left" style="font-family:new times roman; margin-top:3px; margin-left:200px; font-size:14px">Sums up why 99% of all fundraising efforts fail to reach their goals.</div>';

	$datetime = date("mdY").'-'.date("his");
	
	if($REQUEST['action'] == 'show_instructions_sheet'){
		//__construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
		$configForMpdf = setVariableInAssocArrayForMpdf('c','A4','','',10,10,40,10,10,5);
		$mpdf=new \Mpdf\Mpdf($configForMpdf);
		// echo $header.$html; die();
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($html);
		// $mpdf->Output(); die();//if you want to test html in browser, un comment this line it will show html on every page load
		$mpdf->Output('files/instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf', 'F');
		$attachedfile = ''.sHOMECMS.'files/instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf';
		$oregister->redirect($attachedfile);
	}

	$attachedfile = sHOMECMS.'build_team.php?action=show_instructions_sheet&cid='.$REQUEST['cid'];
	// $filename = 'instructionsheet-'.$_SESSION['uname'].'-'.$datetime.'.pdf';
	// $path = ''.sHOMECMS.'files/';
		
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
	/*
	In error case, $array_bounce has. So we need to set results manually
	Array
	(
	    [errors] => Array
	        (
	            [0] => Array
	                (
	                    [message] => Result set exceeds 10,000, please use cursor based paging instead
	                )

	        )

	)
	*/
	// print_r($array_bounce );
	if( isset($array_bounce['errors']) ){
		$array_bounce['results'] = [];
	}

	//End Get SparkPost Bounces Email
}
else{
	$oregister->redirect('manage_campaign.php');
}



if( isset($REQUEST['action']) && $REQUEST['action'] == 'sendMassMessage') {
	//Following roles can send message message
	if (
			$_SESSION['role_id'] == 1 //Administrator
		||  $_SESSION['role_id'] == 2 //Campaign Manager
	) {
		$fld_desc1 = $REQUEST['fld_desc1'];
		//value of variable --> $aParticipantDetailSelected already selected in cid > 0 check
		$iCountRecords = count($aParticipantDetailSelected);
		// echo "687--aParticipantDetailSelected<pre>"; print_r($aParticipantDetailSelected); die();
		if($iCountRecords>0){
		    for($i=0;$i<$iCountRecords;$i++){
		        $participant_sel_name = $aParticipantDetailSelected[$i]['uname'];
		        $participant_phone = $aParticipantDetailSelected[$i]['uphone'];

		        if ($participant_phone != "" && $participant_phone != "000-000-0000" && $participant_phone != "___-___-____") {
					if ($sent_sms = send_sms(str_replace("-","",$participant_phone),utf8_encode($fld_desc1))) {

					}
					else {
						//Error when sending SMS
					}
				}
				else {
					//Invalid Number
				}
		    }//end of for loop
		}//when record count greater then 0
	}//end of if of role check
}//end of if of sendMassMsg

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
<title>Admin<?php echo sWEBSITENAME;?> - Build Your Team</title>
<!-- Bootstrap Core CSS -->
<link href="../bars/bars.css" rel="stylesheet" type="text/css">
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="bower_components/gallery/css/animated-masonry-gallery.css"/>
<link rel="stylesheet" type="text/css" href="bower_components/fancybox/ekko-lightbox.min.css"/>
<!--My admin Custom CSS -->
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href="bower_components/owl.carousel/owl.carousel.min.css" rel="stylesheet" type="text/css" />
<link href="bower_components/owl.carousel/owl.theme.default.css" rel="stylesheet" type="text/css" />
<!--alerts CSS -->
<link href="bower_components/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css">
<!-- Custom CSS -->
<link href="css/style.css" rel="stylesheet">
<link rel="stylesheet" href="css/style_table.css">
<script src="js/jquery-1.10.2.min.js"></script> 
<link rel="stylesheet" href="css/smk-accordion.css">
<style>
.acc_content ul {
    padding: 0 !important;
    margin: 0 !important;
}
ol, ul {
    margin-top: 0 !important;
    margin-bottom: 0px !important;
}
.acc_content ul li {
    border-bottom: 0 !important;
    line-height: 0px !important;
}
.help-block {
    display: block;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
    color: #737373;
}

.rotatorleft_disable, .rotatorright_disable, deleteimage_disable {
	pointer-events: none;
    opacity: 0.4;
}
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
<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> -->
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
		  <!-- .white-box -->
		  <h1 class="h1styling">Build Your Team</h1>
		  <div class="line3"></div>
		  <? if ($fld_campaign_title != '') { ?>
		  <h4 class="h4styling" ><?=$fld_campaign_title;?></h4>
		  <div class="line3"></div>
		  <? } ?>
          <div class="white-box">
		    <div class=" Campaign_in">
			<?
			  if (isset($REQUEST['pid']) && isset($REQUEST['action']) && $REQUEST['action'] == 'edit') {
				?>
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
                                <th>Thumbnail</th>
                              </tr>
                            </thead>
                            <tbody id="inputadd3">
                              <tr>
                                <td><input type="text" name="pname" id="pname" class="formdivtext5" value="<?=html_entity_decode($donor_name);?>" placeholder="Donor First Name"></td>
                                <td><input type="text" name="plname" id="plname" class="formdivtext5" value="<?=html_entity_decode($donor_lname);?>" placeholder="Donor Last Name"></td>
                                <td><input type="text" name="pemail" id="pemail" class="formdivtext5" value="<?=$donor_email;?>" placeholder="Donor Email"></td>
                                <td><input type="text" name="pphone" id="pphone" class="formdivtext5" value="<?=$donor_phone;?>" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                                <td></td>
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
			  } 
			  else {
			  ?>
			<div class="div_ul">
            <ul>
              <li class="select_no alreadyvisited borderright"><a <?=$camp_inform;?> style="color: #fff !important;">START YOUR CAMPAIGN</a></li>
              <li class="selected start-back "><a <?=$basic_inform;?> style="color: #fff !important;">BASIC INFORMATION</a></li>
              <li class="select_no basic-back Build-back"><a <?=$build_team;?> style="color: #fff !important;">BUILD YOUR TEAM</a></li>
              <li class="select_no3 Build-back"><a <?=$go_live;?>>FINISH</a></li>
            </ul>
          </div>
		  <?php
			$invalidemailscount = count($invalidemails);
			if ($invalidemailscount-1 > 0) { 
		  ?>
			  <div id="notifications" class="alert alert-danger alert-dismissable" style="margin: 22px; padding: 6px 15px !important">
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
		  <? if ($fld_accid == '') { ?>
		  <div class="formdiv-in build_team">
		  <div id="notifications" class="alert alert-danger alert-dismissable" style="padding: 6px 15px !important">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Account not created, please goto <a href="start_campaign.php?m=e&cid=<?=$cid;?>">start your campaign</a> and click save button.</b>
		  </div>
		  </div>
		  <? } 

		  	
		  	$camp_profit = number_format($campaign_raised*$after_app_fee_percentage,2,'.',',');
			$camp_money_raised = number_format($campaign_raised,2,'.',',');
			$campaign_goal_value = number_format($campaign_goal,2,'.',',');

		  ?>
		  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 banner-graph" style="margin-left:12%; margin-top:2%;margin-bottom:3%">
            <div class='wrap_right'>
			  <div class='bar_group' <?=$styleapplied;?>>
                <div class='bar_group__bar thick' value='100'>
                  <div class="b_tooltip" style="top:220%;left:100%">
                    <span>Campaign Goal<br>$ <?=$campaign_goal_value;?></span>
                    <div class="b_tooltip--tri" style="top:-44px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transform: rotate(180deg);"></div>
                  </div>
                </div>
                <div class='bar_group__bar thick' value='<?=$campaign_graph_total_per;?>'>
                  <div class="b_tooltip" style="top:-33%;left:100%">
                    <div class="b_tooltip--tri"></div>
                    <span>Campaign Profit<br>$ <?php echo $camp_profit; ?></span>
                  </div>
                </div>
              </div>
			</div>
		  </div>
		  
		  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8" style="margin-left:16%;">
			<div class="col-md-5">
				<div class="col-md-8">Money Raised<div style="float:right">$</div></div>
				<div class="col-md-4"><?php echo $camp_money_raised; ?></div>
			</div>
			<div class="col-md-5">
				<div class="col-md-8">Campaign Profit<div style="float:right">$</div></div>
				<div class="col-md-4"><?=$camp_profit;?></div>
			</div>
		  </div>
          <div class="formdiv-in build_team div_width" style="width:92% !important">
              <div class="colmd_12a">
                <div class="accordion_example4">                 	
				  <?php
				  /*
				  echo "fld_ab1575_pupil_fee=".$fld_ab1575_pupil_fee;
				  echo "_SESSION roleid=".$_SESSION['role_id'];
				  echo "<br/>";
				  */
				  //Show participant list to every one, kurt told me this
				  $showListToEveryOn = true;
				if ($showListToEveryOn != true 
				  		&& ($fld_ab1575_pupil_fee == 1 && $_SESSION['role_id'] != 1) 
				) 
				{	  
					$participantenrolleddetails = $oCampaign->participant_enrolled($cid);
					$iCountRecords3 = count($participantenrolleddetails);
					if($iCountRecords3>0){
					  $participant_count = 0;
					  $unsubscribe_count = 0;

					  $donorrequire = 0;
					  $donorupload = 0;
					  $participantgoal = 0;
					  $moneyraised = 0;
					  $BadEmails = 0;

					  for($l=0;$l<$iCountRecords3;$l++){
						$uid = $participantenrolleddetails[$l]['uid'];
						//Get Donors Details
						$BadEmailCounter = 0;
						$BadEmailDetail = $oCampaign->getbademails($uid, $cid);
						$BadEmailDetailCount = count($BadEmailDetail);
						foreach ($array_bounce['results'] as $bounce) {
						  //if ($bounce['source'] == 'Bounce Rule') {
						  $bademail = $bounce['recipient'];
						  for ($zz=0; $zz < $BadEmailDetailCount; $zz++) {
							if ($bademail == $BadEmailDetail[$zz]['uemail'] && $BadEmailDetail[$zz]['is_read'] == 0) {
							  $BadEmailCounter++;
							}
						  }
						  //}
						}
						$participant_count++;
						$donorrequire = $participantenrolleddetails[$l]['donorrequire'];
						$donorupload += $participantenrolleddetails[$l]['donorupload'];
						$BadEmails += $BadEmailCounter;
						//New Field
						$is_unsubscribe = $participantenrolleddetails[$l]['donorunsubscribe'];
						if ($is_unsubscribe == 1) {
						  $unsubscribe_count++;
						}
						if ($participantenrolleddetails[$l]['participantgoal'] != '') {
						  $participantgoal += $participantenrolleddetails[$l]['participantgoal'];
						}
						if ($participantenrolleddetails[$l]['sumofdonations'] != '') {
						  $moneyraised += $participantenrolleddetails[$l]['sumofdonations'];
						}
					  }
					  $donorsemailgoal = $participant_count * $donorrequire;
					  $participentdonorupload = round(($donorupload / $donorsemailgoal) * 100);
					}
				  	?>
					  <table class="tab3">
	                    <thead>
	                      <tr>
	                        <th># Participants</th>
							<th>Donor Email Goal</th>
	                        <th># of Donors Emails Uploaded</th>
	                        <th>Bad Emails</th>
	                        <th>Campaign Profit</th>
	                      </tr>
	                    </thead>
						<tbody>
						  <tr>
	                        <td><?=$participant_count;?></td>
	                        <td><?=$donorsemailgoal;?></td>
	                        <td><?=$donorupload;?></td>
	                        <td><?=$BadEmails;?></td>
	                        <td>$ <?=number_format($campaign_raised*.8, 2, '.', ',');?></td>
	                      </tr>
						</tbody>
					  </table>
				  	  <br/>
				  	<?php
				} 
				else {
				  ?>
                  <!-- Section 1 -->
				  <form action="" method="POST">
                  <div class="accordion_in acc_active div7" style="    padding-bottom: 22px; margin-bottom:12px !important;">
                    <div class="acc_head">Participants List</div>
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
                                  <th>Thumbnail</th>
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
								$aParticipantDetailSelected = isset($aParticipantDetailSelected) ? $aParticipantDetailSelected : [];
								$iCountRecords = count($aParticipantDetailSelected);
								if($iCountRecords>0){
									for($i=0;$i<$iCountRecords;$i++){
									$participant_sel_name = $aParticipantDetailSelected[$i]['uname'];
									$participant_sel_lname = $aParticipantDetailSelected[$i]['ulname'];
									$participant_sel_email = $aParticipantDetailSelected[$i]['uemail'];
									$participant_sel_phone = $aParticipantDetailSelected[$i]['uphone'];
									$participant_sel_id = $aParticipantDetailSelected[$i]['uid'];
									$participant_sel_ids = $aParticipantDetailSelected[$i]['id'];
									$participant_sel_pid = $aParticipantDetailSelected[$i]['pid'];
									$participant_moneyraised = $aParticipantDetailSelected[$i]['moneyraised'];
									$is_unsubscribe = $aParticipantDetailSelected[$i]['is_unsubscribe'];
									$fld_image = $aParticipantDetailSelected[$i]['fld_image'];
									$sms_sent_id = $aParticipantDetailSelected[$i]['sms_sent_id'];
									if ($fld_image != '') {
										$user_image = "thumb_".$fld_image;
										$linking = '
										<div class="successerrordiv" style="float:left;text-align:left;width:70%">
										</div>
										<div style="float:right;text-align:right;width:30%">
										<a class="rotatorleft" style="cursor:pointer;padding-right:10px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Left rotate the image" pimage="'.$fld_image.'" pid="'.$participant_sel_id.'"><i class="fa fa-undo"></i></a>
										<a class="rotatorright" style="cursor:pointer;padding-right:10px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right rotate the image" pimage="'.$fld_image.'" pid="'.$participant_sel_id.'"><i class="fa fa-repeat"></i></a>
										<a class="deleteimage" style="cursor:pointer;padding-right:10px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete this image" pimage="'.$fld_image.'" pid="'.$participant_sel_id.'"><i class="fa fa-trash-o"></i></a>
										</div>
										';
										$image_url = "<a href='".sHOMESCMS."uploads/profilelogo/".$fld_image."' data-toggle='lightbox' data-gallery='multiimages' data-footer='".$linking."' data-title='".$participant_sel_name." ".$participant_sel_lname." (".$participant_sel_email.")'><img src='".sHOMESCMS."uploads/profilelogo/".$user_image."' width='30px' height='30px' /> </a>";
									} else {
										$user_image = '';
										$image_url = "";
									}
									if ($participant_moneyraised > 0) {
										$greencolorapplied = 'color:green';
									} else {
										$greencolorapplied = 'color:white';
									}
									//Get Activity Details
									$ActivityDetail = $oregister->getuseronline($participant_sel_id);
									// echo '<pre>'; print_r(['participant_sel_id'=>$participant_sel_id, 'ActivityDetail'=>$ActivityDetail]);die();
									$lastactivity = count($ActivityDetail);
									if($lastactivity > 0){
										$onlineoffline = 'fa-check-circle';
										$titleonlineoffline = 'Participant has joined';
										$greenapply = 'color:green';
									} else {
										$onlineoffline = 'fa-circle-o';
										$titleonlineoffline = 'Participant has not joined';
										$greenapply = 'color:white';
									}
									//Get Donors Details
									$BadEmailCounter = 0;
									$BadEmailDetail = $oCampaign->getbademails($participant_sel_id, $cid);
									$BadEmailDetailCount = count($BadEmailDetail);
									foreach ($array_bounce['results'] as $bounce) {
										//if ($bounce['source'] == 'Bounce Rule') {
											$bademail = $bounce['recipient'];
											for ($zz=0; $zz < $BadEmailDetailCount; $zz++) {
												if ($bademail == $BadEmailDetail[$zz]['uemail'] && $BadEmailDetail[$zz]['is_read'] == 0) {
													$BadEmailCounter++;
												}
											}
										//}
									}
									
									$DonorDetail = $oCampaign->getdonordetailsbyparticipants($participant_sel_id, $cid);
									$curr_donors = $DonorDetail['curr_donors'];
									$req_donors = $DonorDetail['req_donors'];
									$donordetails = 'fa-star-o';
									if ($curr_donors > 0) {
										$donordetails = 'fa-star';
										$titledonordetails = 'This participant uploaded '.$curr_donors.' of '.$req_donors.' donors';
										$yellowwhiteapply = 'color:white';
										if ($curr_donors >= $req_donors) { 
											$yellowwhiteapply = 'color:yellow';
										}
									} else {
										$donordetails = 'fa-star-o';
										$titledonordetails = 'This participant uploaded '.$curr_donors.' of '.$req_donors.' donors';
										$yellowwhiteapply = 'color:white';
									}
								?>
								<tr class="itemRow">
								  <td><?=html_entity_decode($participant_sel_name);?></td>
								  <td><?=html_entity_decode($participant_sel_lname);?></td>
								  <td><?=$participant_sel_email;?></td>
								  <!--<td><?=$participant_sel_phone;?></td>-->
								  <td class="phone_<?=$participant_sel_pid;?>">
									<? if ($participant_sel_phone != '') { ?>
										<?=$participant_sel_phone;?>
									<? } else { ?>
										Empty
									<? } ?>
									<br>
									<? 
									//When its Empty
									if ($participant_sel_phone != "" && $participant_sel_phone != "000-000-0000" && $participant_sel_phone != "___-___-____") { ?>
										<a class="editable-phone-number edit_already_entered_number editable-css" id="<?=$participant_sel_pid;?>" phone="<?=$participant_sel_phone;?>" data-test="yep" role="button" tabindex="0" title="Click here to change phone#">Change Phone#</a>
									<? } 
									//When its not Empty
									else { ?>
										<a class="editable-phone-number edit_already_entered_number editable-css" id="<?=$participant_sel_pid;?>" phone="<?=$participant_sel_phone;?>" data-test="yep" role="button" tabindex="0" title="Click here to change phone#">Change Phone#</a>
									<? } ?>
								  </td>
								  <td><?=$image_url;?></td>
								  <td>
									<? if ($campaign_edate >= $current_date && $status == 1) { ?>
									<? if ($curr_donors == 0) { ?>
									<a href="?cid=<?=$cid;?>&pid=<?=$participant_sel_ids;?>&action=delete">
									  <img style="padding-right: 10px;margin-top: -3px;" src="images/forbidden.png" data-toggle="tooltip" data-placement="top" data-original-title="Remove participant from this campaign" aria-hidden="true" />
									</a>
									<? } ?>
									<? } ?>
									<a>
									  <? 
									  $lastuid = isset($lastuid) ? $lastuid : "";
									  if ($lastuid == $participant_sel_id) { ?>
									  <span class="fa <?=$onlineoffline;?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$greenapply;?> !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titleonlineoffline;?>" title=""></span>
									  <? } else { ?>
									  <span class="fa <?=$onlineoffline;?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$greenapply;?> !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titleonlineoffline;?>" title=""></span>
									  <? } ?>
									</a>
									<? if ($fld_text_messaging == 1) { ?>
									<a>
										<? if ($sms_sent_id != "") { ?>
                                        <img src="images/icon-sms-red.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Sent" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
										<?} else {?>
										<img src="images/icon-sms-unread.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Pending" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
										<? } ?>
                                    </a>
									<? } ?>
									<a>
									  <span class="fa <?=$donordetails;?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$yellowwhiteapply;?> !important;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="<?=$titledonordetails;?>" title=""></span>
									</a>
									<? if ($campaign_edate >= $current_date && $status == 1) { ?>
									<? if ($curr_donors == 0) { ?>
									<a href="?cid=<?=$cid;?>&pid=<?=$participant_sel_ids;?>&action=resent">
									  <span class="fa icon-envelope" data-toggle="tooltip" data-placement="top" data-original-title="Resend invitation email to participant" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true"></span>
									</a>
									<? } ?>
									<? if ($fld_text_messaging == 1) { ?>
                                    <a href="?cid=<?=$cid;?>&pid=<?=$participant_sel_ids;?>&hash=<?=$fld_campaign_hashkey;?>&action=sms_resent">
										<img src="images/sms-resend.png" data-toggle="tooltip" data-placement="top" data-original-title="SMS Re-Send" style="background: #9e9e9e;padding: 3px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" />
                                    </a>
									<? } ?>
									<? } ?>
									<a class="moneyraisedshow">
									  <span class="fa fa-usd" data-toggle="tooltip" data-placement="top" data-original-title="Money Raised $ <?=number_format($participant_moneyraised,2,'.',',');?>" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0; <?=$greencolorapplied;?> !important;" aria-hidden="true"></span>
									</a>
									<? if ($BadEmailCounter > 0) { ?>
									<a>
									  <span class="fa fa-times" style="color:red;" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="Invalid Email: <?=$BadEmailCounter;?>"></span>
									</a>
									<? } ?>
									<? if ($campaign_edate >= $current_date && $status == 1) { ?>
									<? if($ActivityDetail == 0){ ?>
									<a href="?cid=<?=$cid;?>&pid=<?=$participant_sel_pid;?>&action=edit">
									  <span class="fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-original-title="Edit this participant"></span>
									</a>
									<? } ?>
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
								  <td colspan="5" align="center">There are no participants in the list</td>
								</tr>
								<? } } ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
				  <button class="btn btn-success waves-effect waves-light" type="submit" id="golive1" name="golive1" style="display:none"></button>
				  <button class="btn btn-success waves-effect waves-light" type="submit" name="submitform" id="submitform" style="display:none"></button>
				  <input type="hidden" name="fld_campaign_id" id="fld_campaign_id" value="<?=$REQUEST['cid']?>">
				  </form>
                  <!-- Section 1 --> 
				  <?php } ?>
				  <div align="center">
					<button class="btn btn-success waves-effect waves-light" type="button" id="downloadpdf" name="downloadpdf" onClick="parent.open('<?=$attachedfile;?>')" >Join Campaign Instruction Sheet</button>
				  </div>
				  <!-- Section 2 -->
				  <? if ($campaign_edate >= $current_date && $status == 1) { ?>
				  <form name="input" data-toggle="validator"  action="" class="aj" method="POST">
                  <div class="accordion_in acc_active" style="margin-top:12px !important;">
                    <div class="acc_head"><? if ($fld_ab1575_pupil_fee == 1 && $_SESSION['role_id'] != 1) {	echo "Invite Participant to Join Campaign"; } else { echo "Enter Names"; } ?></div>
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
                                <td><input type="text" name="pname[]" id="pname1" class="formdivtext5" placeholder="Participant First Name"></td>
                                <td><input type="text" name="plname[]" id="plname1" class="formdivtext5" placeholder="Participant Last Name"></td>
                                <td class="form-group"><input type="email" name="pemail[]" id="pemail1" class="formdivtext5 checkemail"  placeholder="Participant Email"><div class="help-block with-errors"></div></td>
                                <td><input type="text" name="pphone[]" id="pphone1" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                              <tr>
                                <td><input type="text" name="pname[]" id="pname2" class="formdivtext5" placeholder="Participant Name"></td>
								<td><input type="text" name="plname[]" id="plname2" class="formdivtext5" placeholder="Participant Last Name"></td>
                                <td class="form-group"><input type="email" name="pemail[]" id="pemail2" class="formdivtext5 checkemail"  placeholder="Participant Email"><div class="help-block with-errors"></div></td>
                                <td><input type="text" name="pphone[]" id="pphone2" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                              <tr>
                                <td><input type="text" name="pname[]" id="pname3" class="formdivtext5" placeholder="Participant Name"></td>
								<td><input type="text" name="plname[]" id="plname3" class="formdivtext5" placeholder="Participant Last Name"></td>
                                <td class="form-group"><input type="email" name="pemail[]" id="pemail3" class="formdivtext5 checkemail"  placeholder="Participant Email"><div class="help-block with-errors"></div></td>
                                <td><input type="text" name="pphone[]" id="pphone3" class="formdivtext5 checkphone" data-inputmask="'mask': ['999-999-9999', '999-999-9999']" data-mask="" placeholder="___-___-____"></td>
                              </tr>
                            </tbody>
                          </table>
						  <div class="Enter-Names-but-main">
						   <div class="col-md-6"><button type="submit" class="btn-4" style="width: 100%;" id="addtolist3" name="addtolist3">Add to List<span class="fa fa-plus"></span></button> </div>
						   <div class="col-md-6"> <button type="button" class="btn-4" style="width: 100%;" id="addmore3">Add More<span class="fa fa-plus"></span></button> </div>
						  </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  </form>
                  <!-- Section 2 --> 
				  
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
                            <p style="clear:both;"><a href="sample.csv">A sample file can be downloaded from <u><b>here</b>. The file must be in a CSV format.</u></a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  </form>
                  <!-- Section 3 --> 

                <?php if( $_SESSION['role_id'] == 1
                	   || $_SESSION['role_id'] == 2 
            		  )
            	{?>
                  <!-- Send message to participants | start -->
                <form name="input" action="" class="aj" method="POST">
					<input type="hidden" name="action" value="sendMassMessage">
                  <div class="accordion_in acc_active" id="sendMessageDiv" >
	                    <div class="acc_head">Send message to participants</div>
	                    <div class="acc_content">
	                      <div class="scrol">
	                        <div class="tabdiv">
		                        <div class="rowdiv">
		                            <div class="form-group col-sm-12">
										<br/>
										<textarea type="text" placeholder="Enter message for all participants" class="form-control" style="height:150px;" required="" name="fld_desc1" id="fld_desc1"></textarea>
										<div class="help-block with-errors"></div>
									</div>
		                            <div class="form-group col-sm-12">
										<button class="btn btn-success waves-effect waves-light" type="submit" id="sendMessageToAllParticipants" name="sendMessageToAllParticipants">Send</button>
									</div>
								</div>
		                    </div>
	                      </div>
	                    </div>
                  </div>
				</form>
                  <!-- Send message to participants | End -->
                <?php 
            	}?>
                  <!-- Section 4 -->
                  <div class="accordion_in acc_active divpad">
                    <div class="acc_head">Select Previous Participants</div>
                    <div class="acc_content">
                      <div class="scrol">
                        <div class="tabdiv">
                          <div class="rowdiv">
                            <div class="div8 col-lg-12 col-md-12 col-xs-12">
                              <div class="col-md-4">
                                <input type="text" class="formdivtext4" id="fld_search" name="fld_search" placeholder="Participant Search">
                              </div>
                              <div class="col-md-3   col-sm-6 col-xs-6"> <a id="searched" class="btn-4"><img src="images/img10.png" />SEARCH</a> </div>
							  <div class="col-md-3  col-sm-6 col-xs-6 div_8"> <a class="btn-4 addtolist1"> ADD TO LIST<span class="fa fa-plus"></span></a> </div>
                            </div>
                            <table class="tab1">
                              <thead>
                                <tr>
                                  <th align="center"><input type="checkbox" id="checkAll" title="Select All"/></th>
                                  <th>First Name</th>
                                  <th>Last Name</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody class="addedlist">
								<?
								$iCountRecords = count($aParticipantDetail);
								if($iCountRecords>0){
									for($i=0;$i<$iCountRecords;$i++){
									$participant_name = $aParticipantDetail[$i]['uname'];	
									$participant_lname = $aParticipantDetail[$i]['ulname'];	
									$participant_email = $aParticipantDetail[$i]['uemail'];	
									$participant_phone = $aParticipantDetail[$i]['uphone'];	
									$participant_id = $aParticipantDetail[$i]['uid'];	
									$participants_id = $aParticipantDetail[$i]['id'];	
								?>
                                <tr class="selectcheck">
                                  <td><input type="checkbox" value="<?=$participant_id;?>" name="<?=$participant_name;?>" lname="<?=$participant_lname;?>" email="<?=$participant_email;?>" phone="<?=$participant_phone;?>" class="checkbox_div checkboxes" /></td>
                                  <td><?=html_entity_decode($participant_name);?></td>
                                  <td><?=html_entity_decode($participant_lname);?></td>
                                  <td><?=$participant_email;?></td>
                                  <td><?=$participant_phone;?></td>
                                  <td>
									<a href="?cid=<?=$REQUEST['cid'];?>&pemail=<?=$participant_email;?>&action=delete">
									  <img style="padding-right: 10px;margin-top: -3px;" src="images/forbidden.png" />
									</a>
									<a href="?cid=<?=$REQUEST['cid'];?>&pid=<?=$participant_id;?>&action=addparticipant">
									  <span class="fa fa-chevron-right" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" title="Add to participant"></span>
									</a>
									<a>
									  <span class="fa fa-info model_img img-responsive" style="background: #9e9e9e;padding: 7px;color: #FFF;border-radius: 50%;font-size: 15px;width: 30px;height: 30px;text-align: center;margin: 0 10px 0 0;" aria-hidden="true" data-toggle="modal" data-target="#myModal_<?=$participant_id;?>" title="Information about participant"></span>
									</a>
								  </td>
                                </tr>
								<? } } else { ?>
								<tr>
								  <td colspan="6" align="center">There are no previous participants in the list.</td>
								</tr>
								<? } ?>
                              </tbody>
                            </table>
                            <div class="col-md-3  col-sm-6 col-xs-6 div_8"> <a class="btn-4 addtolist1"> ADD TO LIST<span class="fa fa-plus"></span></a> </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
				  <? } ?>
                  <!-- Section 4 --> 
                </div>
              </div>
			  <?
			  $iCountRecords = count($aParticipantDetail);
			  if($iCountRecords>0){
			  	$tempPdBackup = [];
				for($i=0;$i<$iCountRecords;$i++){
				  $cid = $REQUEST['cid'];
				  $participant_name = $aParticipantDetail[$i]['uname'];	
				  $participant_lname = $aParticipantDetail[$i]['ulname'];	
				  $participant_email = $aParticipantDetail[$i]['uemail'];	
				  $participant_phone = $aParticipantDetail[$i]['uphone'];	
				  $participant_id = $aParticipantDetail[$i]['uid'];	
				  $participantsdetails = [];
				  //query optimization, don't load records if are already queried
				  $tempPdKey = $participant_id.'__'.$cid;
				  if( isset($tempPdBackup[$tempPdKey]) ){
				  	$participantsdetails = $tempPdBackup[$tempPdKey];
				  }
				  else if($participant_id > 0){
				  	$participantsdetails = $oCampaign->participants_details2($participant_id, $cid);
				  	$tempPdBackup[$tempPdKey] = $participantsdetails;
				  }
				  
				  $iCountRecords2 = count($participantsdetails);
			  ?>
				  <!-- Start Modal --> 
				  <div class="modal fade" id="myModal_<?=$participant_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog modal-lg" role="document">
					  <div class="modal-content">
						<div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <h4 class="modal-title" id="myModalLabel">Participant Details (<?=html_entity_decode($participant_name).' '.html_entity_decode($participant_lname);?>)</h4>
						</div>
						<div class="modal-body">
						  <table class="tab1">
	                        <thead>
	                          <tr>
	                            <th style="font-size:13px !important; width:8%;">Camp. #</th>
	                            <th style="font-size:13px !important; width:30%;">Campaign Name</th>
	                            <th style="font-size:13px !important; width:20%;">Campaign Manager</th>
	                            <th style="font-size:13px !important; width:13%;">Donors Required</th>
	                            <th style="font-size:13px !important; width:14%;">Donors Uploaded</th>
	                            <th style="font-size:13px !important; width:12%;">Money Raised</th>
	                          </tr>
	                        </thead>
	                        <tbody>
							  <?
							  if($iCountRecords2>0){
								for($k=0;$k<$iCountRecords2;$k++){
								  $camp_id = $participantsdetails[$k]['fld_campaign_id'];	
								  $camp_name = $participantsdetails[$k]['fld_campaign_title'];	
								  $camp_manager_name = $participantsdetails[$k]['fld_cname'];	
								  $donorrequire = $participantsdetails[$k]['fld_donor_size'];	
								  $donoruploaded = $participantsdetails[$k]['donoruploaded'];	
								  $moneyraised = $participantsdetails[$k]['moneyraised'];

							  ?>
							  <tr>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$camp_id;?></a></td>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$camp_name;?></a></td>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$camp_manager_name;?></a></td>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$donorrequire;?></a></td>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$donoruploaded;?></a></td>
								<td class="tester"><a pid="<?=$participant_id;?>" campid="<?=$camp_id;?>" campname="<?=$camp_name;?>" managername="<?=$camp_manager_name;?>"><?=$moneyraised;?></a></td>
							  </tr>
							  <? } } ?>
							</tbody>
						  </table>
						  <div class="clearfix"></div>
						</div>
						<div style="clear:both"></div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal"><span class="btn-label"><i class="fa fa-chevron-left"></i></span> Close</button>
						</div>
					  </div>
					</div>
				  </div>
				  <!-- End Modal --> 
			  <? }
			  }
			  // echo "After for"; die();
			  ?>			  
			  <script>
			  $(".tester a").on('click',function(e){
				var pid = $(this).attr('pid');
				var campid = $(this).attr('campid');
				var campname = $(this).attr('campname');
				var managername = $(this).attr('managername');
				$.post('donordetails.php', 'pid=' + pid + '&campid=' + campid, function (response) {
				  var jdata = JSON.parse(response);
				  var donorssdetails1 = ''+jdata.donorssdetails+'';
				  //alert(jdata.donorssdetails);
				  var row1 = donorssdetails1.split(",");
				  var popuptable = '<table class="tab1">';
					    popuptable += '<thead>';
						  popuptable += '<tr>';
							popuptable += '<th style="font-size:13px !important; width:20%;">First Name</th>';
							popuptable += '<th style="font-size:13px !important; width:20%;">Last Name</th>';
							popuptable += '<th style="font-size:13px !important; width:20%;">Email Address</th>';
							popuptable += '<th style="font-size:13px !important; width:16%;">Phone #</th>';
							popuptable += '<th style="font-size:13px !important; width:16%;">Money Raised</th>';
						  popuptable += '</tr>';
						popuptable += '</thead>';
						popuptable += '<tbody>';
						  if (jdata.counter > 0) {
						  for(var i = 0; i < row1.length; i++) {
							var row2 = row1[i].split("|");
						  popuptable += '<tr>';
							popuptable += '<td>'+row2[0]+'</td>';
							popuptable += '<td>'+row2[1]+'</td>';
							popuptable += '<td>'+row2[2]+'</td>';
							popuptable += '<td>'+row2[3]+'</td>';
							popuptable += '<td>'+row2[4]+'</td>';
						  popuptable += '</tr>';
						  }
						  } else {
						  popuptable += '<tr>';
							popuptable += '<td colspan="5" align="center">No any information yet...!</td>';
						  popuptable += '</tr>';
						  }
						popuptable += '</tbody>';
					  popuptable += '</table>';
					  swal({title: ''+campname+' ('+managername+')',width: 600, text: ''+popuptable+''});
				});
			  });
			  </script>
			  <? if ($campaign_edate >= $current_date && $status == 1) { ?>
              <div class="col-lg-12 ">
			  <div class="col-sm-6 basic-but-left " align="left">
				<button class="btn btn-primary waves-effect waves-light" type="button" onClick="step2(<?=$REQUEST['cid']?>)"><span class="btn-label"><i class="fa fa-chevron-left"></i></span>Back</button>
				<button class="btn btn-primary waves-effect waves-light" type="button" onclick="window.location.href='manage_campaign.php'"><span class="btn-label"><i class="fa fa-times"></i></span>Cancel</button>
			  </div>
			  <div class="col-sm-6 basic-but-right" align="right">
				<button class="btn btn-success waves-effect waves-light" type="button" id="submitform1" name="submitform1">Save <span class="btn-label forright-icon"><i class="fa fa-floppy-o"></i></span></button>
				<button class="btn btn-success waves-effect waves-light build_team-but" style="padding-left: 8px;width: 170px;" type="submit" id="golive" name="golive"><? if ($fld_live == 1) {echo "Update your Team";} else {echo "Save your Team";} ?> <span class="btn-label forright-icon"><i class="fa fa-chevron-right"></i></span></button>
			  </div>
            </div>
			  <? } ?>
          </div>
			  <div class="clearfix"></div><? } ?>
		  </div></div>
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
<!--<script src="js/validator.js"></script>-->
<script src="js/jquery.inputmask.js"></script>
<script>
//must include inputmask.js on page for masking
function reInitMaskingForAllInputFields(){
	$("[data-mask]").inputmask("999-999-9999");
}

var tempGlobalPhone = null;
function setPhoneNumberWhenClickOnEditPhoneNumber( $this, phone ){
	tempGlobalPhone = phone.trim();
	console.log( $this );
	// console.log( 'phone', tempGlobalPhone, 'html', $this.html() );
	setTimeout(function(){
		// console.log( 'phone', tempGlobalPhone, 'html', $this.html() );
		
		var tempEditInputFieldObj = $this.find('input');
		// console.log('tempEditInputFieldObj.length='+tempEditInputFieldObj.length);
		
		//Set masking
		tempEditInputFieldObj.val(tempGlobalPhone);
		tempEditInputFieldObj.attr("data-inputmask", "'mask': ['999-999-9999', '999-999-9999']");
		tempEditInputFieldObj.attr("data-mask", "");
		tempEditInputFieldObj.attr("placeholder", "___-___-____");
		
		reInitMaskingForAllInputFields();
		
	},100);
}

</script>
<script language="javascript">
$(document).ready(function ($) {
reInitMaskingForAllInputFields();
function step2(id){
	window.location.href = 'basic_information.php?cid='+id;
}
var counter = $('.checkemail').length;
/*$(".checkemail").blur(function() {
	//alert($(this).val());
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
	var cid = <?=$REQUEST['cid'];?>;
	var uid = <?=$_SESSION['uid'];?>;
	var cemail = '<?=$fld_cemail;?>';
	var thelink = 'checkemail.php';
	var formData = "email="+textbox+"&cid="+cid+"&uid="+uid+"&cemail="+cemail+"";  //Name value Pair
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
});*/

$(".checkemail").blur(function() {
	//alert($(this).val());
	var thisEmail = $(this).val().trim();
	if ( thisEmail != '') {
	var getid = $(this).attr('id');
    var textbox = thisEmail.replace(/\s+/g, '');
    $(this).filter(function(){
        var emil=thisEmail;
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( emil ) ) {
			$('#'+getid+'').get(0).setCustomValidity("Please enter a valid email...!");
            swal('ERROR','Please enter a valid email...!');
        } else {
			$('#'+getid+'').get(0).setCustomValidity("");
        }
    });
	var cid = <?=$REQUEST['cid'];?>;
	var uid = <?=$_SESSION['uid'];?>;
    var thelink = 'checkemails.php';
    var formData = "act=1&email="+textbox+"&cid="+cid+"&uid="+uid+"";  //Name value Pair
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
				// $('#'+getid+'').data('is_valid', '0');
				$('#'+getid+'').val('');
				console.log('set Email of getid-'+getid);
			} else {
				//alert('Email Not Exists...!');
				$('#'+getid+'').get(0).setCustomValidity("");
				$('#'+getid+'').focusout();
				// $('#'+getid+'').data('is_valid', '1');
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
	var thisPhoneNumber = $(this).val().trim();
	if (thisPhoneNumber != '') {
	var getid = $(this).attr('id');
    var textbox = thisPhoneNumber.replace(/\s+/g, '');
    if (validatePhone(textbox)) {
		$('#'+getid+'').get(0).setCustomValidity("");
	} else {
		$('#'+getid+'').get(0).setCustomValidity("Please enter a valid phone #...!");
		swal('ERROR','Please enter a valid phone #...!');
	}
	var cid = <?=$REQUEST['cid'];?>;
	var uid = <?=$_SESSION['uid'];?>;
    var thelink = 'checkphone.php';
    var formData = "act=1&phone="+textbox+"&cid="+cid+"&uid="+uid+"";  //Name value Pair
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
				$('#'+getid+'').val('');
				console.log('set phone of getid-'+getid);
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

$(document).on('click', '#addmore3', function() {
	counter++;
	var masked = "'mask': ['999-999-9999', '999-999-9999']";
	var insertnewfield = '<tr><td><input type="text" name="pname[]" id="pname'+counter+'" class="formdivtext5" placeholder="Participant First Name"></td><td><input type="text" name="plname[]" id="plname'+counter+'" class="formdivtext5" placeholder="Participant Last Name"></td><td class="form-group"><input type="email" name="pemail[]" id="pemail'+counter+'" class="formdivtext5 checkemail" placeholder="Participant Email"><div class="help-block with-errors"></div></td><td><input type="text" name="pphone[]" id="pphone'+counter+'" class="formdivtext5 checkphone" data-inputmask="'+masked+'" data-mask="" placeholder="___-___-____"  ></td></tr>';
	$(insertnewfield).appendTo("#inputadd3");
	reInitMaskingForAllInputFields();
});
});
</script> 
<script type="text/javascript" src="js/accounting.js"></script>
<script>
$('#submitform1').click(function(){
	$('#submitform').click();
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

/*var selected = [];
$('.checkboxes input:checked').each(function() {
    selected.push($(this).attr('name'));
	alert(selected);
});*/

$(document).on('click', '.addtolist1', function() {
	/*var checkedValues = $('.checkboxes').map(function() {
		return $(this).attr('name');
	}).get();*/
	if ($(".empty")[0]){
		$("#tbodypartlist").empty();
		$("#tbodypartlist").removeClass("empty");
		var selected = [];
		$('.selectcheck input:checked').each(function() {
			var innerselected = '<tr class="itemRow"><input type="hidden" name="id[]" value="'+$(this).attr('value')+'"><input type="hidden" name="name[]" value="'+$(this).attr('name')+'"><input type="hidden" name="lname[]" value="'+$(this).attr('lname')+'"><input type="hidden" name="email[]" value="'+$(this).attr('email')+'"><input type="hidden" name="phone[]" value="'+$(this).attr('phone')+'"><td>'+$(this).attr('name')+'</td><td>'+$(this).attr('lname')+'</td><td>'+$(this).attr('email')+'</td><td>'+$(this).attr('phone')+'</td><td><a class="removetr" href="#"><img style="padding-right: 10px;margin-top: -3px;" src="images/forbidden.png"  /></a></td></tr>';
			$(innerselected).appendTo("#tbodypartlist");
			$(this).attr('name');
		});
	} else {
		var selected = [];
		$('.selectcheck input:checked').each(function() {
			var innerselected = '<tr class="itemRow"><input type="hidden" name="id[]" value="'+$(this).attr('value')+'"><input type="hidden" name="name[]" value="'+$(this).attr('name')+'"><input type="hidden" name="lname[]" value="'+$(this).attr('lname')+'"><input type="hidden" name="email[]" value="'+$(this).attr('email')+'"><input type="hidden" name="phone[]" value="'+$(this).attr('phone')+'"><td>'+$(this).attr('name')+'</td><td>'+$(this).attr('lname')+'</td><td>'+$(this).attr('email')+'</td><td>'+$(this).attr('phone')+'</td><td><a class="removetr" href="#"><img style="padding-right: 10px;margin-top: -3px;" src="images/forbidden.png" /></a></td></tr>';
			$(innerselected).appendTo("#tbodypartlist");
			$(this).attr('name');
		});
	}
});
$(document).on('click', '.removetr', function() {
    var $tr = $(this).closest('tr');
    if ($tr.attr('class') == 'itemRow') {
        $tr.nextUntil('tr[class=itemRow]').andSelf().remove();
    }
    else {
        $tr.remove();
    }
	//var tbody = $('#tbodypartlist').children().length;
	//if (tbody == 0) {
		$("#tbodypartlist").AddClass("empty");
		var innerselected = '<tr class="itemRow"><td colspan="5" align="center">There are no participants in the list</td></tr>';
		$(innerselected).appendTo("#tbodypartlist");
	//}
});
$("#checkAll").change(function () {
    $(".checkboxes").prop('checked', $(this).prop("checked"));
	//$("#tbodypartlist").AddClass("empty");
		//var innerselected = '<tr class="itemRow"><td colspan="5" align="center">There are no participants in the list</td></tr>';
		//$(innerselected).appendTo("#tbodypartlist");
});
$('#searched').click(function() {
    var searchtext = $('#fld_search').val();
	var cid = <?=$REQUEST['cid'];?>;
	var uid = <?=$_SESSION['uid'];?>;
	var formData = "cid="+cid+"&uid="+uid+"&query="+searchtext+"";  //Name value Pair
	$.ajax({
		url : "searchparticipant.php",
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
				$('<tr class="selectcheck"><td><input type="checkbox" value="'+suid+'" name="'+suname+'" lname="'+sulname+'" email="'+suemail+'" phone="'+suphone+'" class="checkbox_div checkboxes"></td><td>'+suname+'</td><td>'+sulname+'</td><td>'+suemail+'</td><td>'+suphone+'</td><td><a href="?cid=<?=$REQUEST['cid'];?>&pemail='+suemail+'&action=delete"><img style="padding-right: 10px;margin-top: -3px;" src="images/forbidden.png" /></a></td></tr>').appendTo('.addedlist');
			}
		},
		error: function (data)
		{
	
		}
	});
});


$('#golive').click(function(){
	// if( $('#golive1').length > 0){
		$('#golive1').click();
	// }
	// else{
		// window.location.href = "golive.php?cid=<?php echo $REQUEST['cid'];?>";
	// }
});
/*
$('#golive').click(function(){
	var textlive = '<? if ($fld_live == 1) {echo "An email will be sent to the new participants you have just added to your campaign";} else {echo "This will make the campaign live and information about the campaign will be sent to the participants";} ?>';
    swal({   
        title: "Do you want to continue?",   
        text: textlive,   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#FCB514",   
        confirmButtonText: "Yes",
		cancelButtonText: "No",
        closeOnConfirm: false 
    }, function(){
		var timer1 = 3000;
		swal({title: "Please Wait...!",   text: "Sending Emails and make the compaign live.",  type: "success",   timer: timer1,   showConfirmButton: false });
		//var jslink = '<?=$jslink;?>';
		//setTimeout(function(){ window.location.href = jslink; }, timer1);
		setTimeout(function(){ $('#golive1').click(); }, timer1);
	});
});
*/
</script>
<script src="../bars/bars.js"></script>
<script type="text/javascript" src="bower_components/gallery/js/animated-masonry-gallery.js"></script>
<script type="text/javascript" src="bower_components/gallery/js/jquery.isotope.min.js"></script>
<script type="text/javascript" src="bower_components/fancybox/ekko-lightbox.min.js"></script>
<script src="js/jquery.rotate.js"></script>
<script type="text/javascript">
			function filenameonly(path){
				path = path.substring(path.lastIndexOf("/")+ 1);
				return (path.match(/[^.]+(\.[^?#]+)?/) || [])[0];
			}
            $(document).ready(function ($) {
                // delegate calls to data-toggle="lightbox"
                $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
                    event.preventDefault();
                    return $(this).ekkoLightbox({
                        onShown: function() {
                            if (window.console) {
                                return console.log('Checking our the events huh?');
                            }
                        },
            onNavigate: function(direction, itemIndex) {
                            if (window.console) {
                                return console.log('Navigating '+direction+'. Current item: '+itemIndex);
                            }
							
            }
                    });
                });

                //Programatically call
                $('#open-image').click(function (e) {
                    e.preventDefault();
                    $(this).ekkoLightbox();
                });
                $('#open-youtube').click(function (e) {
                    e.preventDefault();
                    $(this).ekkoLightbox();
                });

        // navigateTo
                $(document).delegate('*[data-gallery="navigateTo"]', 'click', function(event) {
                    event.preventDefault();

                    var lb;
                    return $(this).ekkoLightbox({
                        onShown: function() {

                            lb = this;

              $(lb.modal_content).on('click', '.modal-footer a', function(e) {

                e.preventDefault();
                lb.navigateTo(2);

              });

                        }
                    });
                });


            });

			var value = 0
			$(document).on('click', '.rotatorleft', function() { 
				var uid = $(this).attr("pid");
				var filename = filenameonly($(this).attr("pimage"));
				var rotate = 'left';
				value -=90;
				$(this).closest(".modal-content").find("img").rotate({ animateTo:value});
				$(".rotatorleft").attr('class', 'rotatorleft_disable');
				$(".rotatorright").attr('class', 'rotatorright_disable');
				$(".deleteimage").attr('class', 'deleteimage_disable');
				$(".successerrordiv").text("Processing...!");
				
				$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=4', function (response) {
					var jdata = JSON.parse(response);
					if (jdata.is_success == 1) {
						//Success
						setTimeout(function(){ 
							$(".successerrordiv").text("Success...!");
							$(".rotatorleft_disable").attr('class', 'rotatorleft');
							$(".rotatorright_disable").attr('class', 'rotatorright');
							$(".deleteimage_disable").attr('class', 'deleteimage');
							var newfilename = '<?php echo SITE_URL;?>app/cms/uploads/profilelogo/'+filename+'?'+$.now();
							var newfilename_thumb = '<?php echo SITE_URL;?>app/cms/uploads/profilelogo/thumb_'+filename+'?'+$.now();
							$('#tbodypartlist tr td a[href*="'+filename+'"]').attr("href", newfilename);
							$('#tbodypartlist tr td a img[src*="thumb_'+filename+'"]').attr("src", newfilename_thumb);
						}, 3000);
					} else {
						//Failed
						$(".successerrordiv").text("Failed...!");
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".deleteimage_disable").attr('class', 'deleteimage');
					}
				});
			});
			$(document).on('click', '.rotatorright', function() { 
				var uid = $(this).attr("pid");
				var filename = filenameonly($(this).attr("pimage"));
				var rotate = 'right';
				value +=90;
				$(this).closest(".modal-content").find("img").rotate({ animateTo:value});
				$(".rotatorleft").attr('class', 'rotatorleft_disable');
				$(".rotatorright").attr('class', 'rotatorright_disable');
				$(".deleteimage").attr('class', 'deleteimage_disable');
				$(".successerrordiv").text("Processing...!");
				
				$.post('img_controller.php', 'uid=' + uid + '&file=' + filename + '&rotate=' + rotate + '&act=4', function (response) {
					var jdata = JSON.parse(response);
					if (jdata.is_success == 1) {
						//Success
						setTimeout(function(){ 
							$(".successerrordiv").text("Success...!");
							$(".rotatorleft_disable").attr('class', 'rotatorleft');
							$(".rotatorright_disable").attr('class', 'rotatorright');
							$(".deleteimage_disable").attr('class', 'deleteimage');
							var newfilename = '<?php echo SITE_URL;?>app/cms/uploads/profilelogo/'+filename+'?'+$.now();
							var newfilename_thumb = '<?php echo SITE_URL;?>app/cms/uploads/profilelogo/thumb_'+filename+'?'+$.now();
							$('#tbodypartlist tr td a[href*="'+filename+'"]').attr("href", newfilename);
							$('#tbodypartlist tr td a img[src*="thumb_'+filename+'"]').attr("src", newfilename_thumb);
						}, 3000);
					} else {
						//Failed
						$(".successerrordiv").text("Failed...!");
						$(".rotatorleft_disable").attr('class', 'rotatorleft');
						$(".rotatorright_disable").attr('class', 'rotatorright');
						$(".deleteimage_disable").attr('class', 'deleteimage');
					}
				});
			});
			$(document).on('click', '.deleteimage', function() { 
				var uid = $(this).attr("pid");
				var filename = $(this).attr("pimage");
				$.post('php/remove_profilelogo.php', 'uid=' + uid + '&file=' + filename + '', function (response) {
					$('#tbodypartlist tr td a[href*="'+filename+'"]').replaceWith($('#tbodypartlist tr td a[href*="'+filename+'"]').text());
					$('#tbodypartlist tr td a img[src*="thumb_'+filename+'"]').replaceWith($('#tbodypartlist tr td a img[src*="thumb_'+filename+'"]').text());
					$( ".modal-header button" ).trigger( "click" );
				});
			});
        </script>
		<!--<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script> -->
		<!--<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
		<!-- x-editable (bootstrap version) -->
		<!--<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet"/>
		<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/js/bootstrap-editable.min.js"></script>-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jeditable.js/2.0.6/jquery.jeditable.js"></script> 
		<script src="js/jquery.jeditable.masked.js" type="text/javascript" ></script>
		<script type="text/javascript" src="js/jquery.maskedinput.js"></script>
		<script>
			$(document).ready(function() {
				/* data that will be sent along */
				var submitdata = {}
				/* this will make the save.php script take a long time so you can see the spinner ;) */
				submitdata['slow'] = true;
				submitdata['mode'] = 1;
				submitdata['cid'] = <?=$cid;?>;
				submitdata['cuid'] = <?=$cuid;?>;
				var pid = '';
				$(".editable-phone-number").editable("savenumber.php", {
					indicator : "<img src='img/spinner.svg' />",
					//type : "text",
					
					// only limit to three letters example
					//pattern: "[A-Za-z]{3}",
					onedit : function() { 
						pid = $(this).attr('id');
						var phone = $(this).attr('phone');
						setPhoneNumberWhenClickOnEditPhoneNumber( $(this), phone );
						submitdata['pid'] = pid;
						return true;
					},
					//onblur  : function() { return true },
					before : function() { 
						console.log('Triggered before form appears');
					},
					callback : function(result, settings, submitdata) {
						$('.phone_'+pid).html(result);
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
					tooltip : "Click here to change phone#",
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
</body>
</html>
<? include_once('bottom.php');?>