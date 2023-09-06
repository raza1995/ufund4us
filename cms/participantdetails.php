<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
$pid = isset($REQUEST['pid']) ? $REQUEST['pid'] : 0;
$cid = isset($REQUEST['cid']) ? $REQUEST['cid'] : 0;
$act = isset($REQUEST['act']) ? $REQUEST['act'] : 0;

//Declare variable required bellow
$donorfname = "";
$donorlname = "";
$donoremail = "";
$donorphone = "";
$donation_amount = "";
$arrayvalue2 = [];

$campaign_id  = "";
$campaign_title  = "";
$campaign_cname  = "";
$campaign_donor_size  = "";
$campaign_donor_upload  = "";
$campaign_donation_amt  = "";
$arrayvalue = [];

if ($act == 1) {
	$iCountRecords2 = 0;
	$participantsdetails = $oCampaign->participants_details3($pid);
	$iCountRecords2 = count($participantsdetails);
	if($iCountRecords2>0){
		for($l=0;$l<$iCountRecords2;$l++){
			
			$campaign_id = $participantsdetails[$l]['fld_campaign_id'];
			$campaign_title = str_replace(",", "&#44;", $participantsdetails[$l]['fld_campaign_title']);
			$campaign_cname = str_replace(",", "&#44;", $participantsdetails[$l]['fld_cname']);
			$campaign_donor_size = $participantsdetails[$l]['fld_donor_size'];
			$campaign_donor_upload = $participantsdetails[$l]['donoruploaded'];
			$campaign_donation_amt = $participantsdetails[$l]['moneyraised'];
			$arrayvalue[] = $campaign_id."|".$campaign_title."|".$campaign_cname."|".$campaign_donor_size."|".$campaign_donor_upload."|".$campaign_donation_amt;
		}
	}
	$result['campaign_id'] = $campaign_id;
	$result['campaign_title'] = $campaign_title;
	$result['campaign_cname'] = $campaign_cname;
	$result['campaign_donor_size'] = $campaign_donor_size;
	$result['campaign_donor_upload'] = $campaign_donor_upload;
	$result['campaign_donation_amt'] = $campaign_donation_amt;
	$result['participantdetails'] = $arrayvalue;
	$result['counter'] = $iCountRecords2;
	echo json_encode($result);
}

if ($act == 2) {
	$pid = $REQUEST['pid'];
	$campid = $REQUEST['cid'];

	$iCountRecords3 = 0;
	$donorssdetails = $oCampaign->donors_details2($pid, $campid);
	$iCountRecords3 = count($donorssdetails);
	if($iCountRecords3>0){
		for($l=0;$l<$iCountRecords3;$l++){
			
			$donorfname = str_replace(",", "&#44;", $donorssdetails[$l]['ufname']);
			$donorlname = str_replace(",", "&#44;", $donorssdetails[$l]['ulname']);
			$donoremail = $donorssdetails[$l]['uemail'];
			$donorphone = $donorssdetails[$l]['uphone'];
			$donation_amount = $donorssdetails[$l]['donation_amount'];
			$arrayvalue2[] = $donorfname."|".$donorlname."|".$donoremail."|".$donorphone."|".$donation_amount;
		}
	}
	$result['donorfname'] = $donorfname;
	$result['donorlname'] = $donorlname;
	$result['donoremail'] = $donoremail;
	$result['donorphone'] = $donorphone;
	$result['donation_amount'] = $donation_amount;
	$result['donordetails'] = $arrayvalue2;
	$result['counter2'] = $iCountRecords3;
	echo json_encode($result);
}

if ($act == 3) {
	$pid = $REQUEST['pid'];
	$campid = $REQUEST['cid'];
	$iCountRecords4 = 0;
	$donorssdetails = $oCampaign->participant_donors_details($pid, $campid);
	$iCountRecords4 = count($donorssdetails);
	if($iCountRecords4>0){
		for($l=0;$l<$iCountRecords4;$l++){
			
			$donorfname = str_replace(",", "&#44;", $donorssdetails[$l]['uname']);
			$donorlname = str_replace(",", "&#44;", $donorssdetails[$l]['ulname']);
			$donoremail = $donorssdetails[$l]['uemail'];
			$donorphone = $donorssdetails[$l]['uphone'];
			$donoremailsent = $donorssdetails[$l]['sent_email'];
			$donorread = $donorssdetails[$l]['is_read'];
			$donordate = $donorssdetails[$l]['creationdate'];
			$participantname = str_replace(",", "&#44;", $donorssdetails[$l]['participantname']);
			$participantlname = str_replace(",", "&#44;", $donorssdetails[$l]['participantlname']);
			$arrayvalue2[] = $donorfname."|".$donorlname."|".$donoremail."|".$donorphone."|".$donordate."|".$participantname."|".$participantlname."|".$donoremailsent."|".$donorread;
		}
	}
	$result['donordetails'] = $arrayvalue2;
	$result['counter3'] = $iCountRecords4;
	echo json_encode($result);
}

if ($act == 4) {
	$pid = $REQUEST['pid'];
	$campid = $REQUEST['cid'];
	$iCountRecords4 = 0;
	$donorssdetails = $oCampaign->participant_donation_details($pid, $campid);
	$iCountRecords4 = count($donorssdetails);
	if($iCountRecords4>0){
		for($l=0;$l<$iCountRecords4;$l++){
			
			$donorfname = str_replace(",", "&#44;", $donorssdetails[$l]['ufname']);
			$donorlname = str_replace(",", "&#44;", $donorssdetails[$l]['ulname']);
			$donoremail = $donorssdetails[$l]['uemail'];
			$donorphone = $donorssdetails[$l]['uphone'];
			$donation_amount = $donorssdetails[$l]['donation_amount'];
			$participantname = str_replace(",", "&#44;", $donorssdetails[$l]['participantname']);
			$participantlname = str_replace(",", "&#44;", $donorssdetails[$l]['participantlname']);
			$arrayvalue2[] = $donorfname."|".$donorlname."|".$donoremail."|".$donorphone."|".$donation_amount."|".$participantname."|".$participantlname;
		}
	}
	$result['donordetails'] = $arrayvalue2;
	$result['counter3'] = $iCountRecords4;
	echo json_encode($result);
}

if ($act == 5) {
	$iCountRecords2 = 0;
	$leaguedetails = $oCampaign->hierarchy_campaign_details($pid);
	$iCountRecords2 = count($leaguedetails);
	if($iCountRecords2>0){
		for($l=0;$l<$iCountRecords2;$l++){
			
			$league_id = $leaguedetails[$l]['fld_campaign_id'];
			$league_title = $leaguedetails[$l]['fld_campaign_title'];
			$league_admin_fname = $leaguedetails[$l]['fld_cname'];
			$league_admin_lname = $leaguedetails[$l]['fld_clname'];
			$league_start_date = date('m/d/Y', strtotime($leaguedetails[$l]['fld_campaign_sdate']));
			$league_end_date = date('m/d/Y', strtotime($leaguedetails[$l]['fld_campaign_edate']));
			$league_company_name = $leaguedetails[$l]['fld_cname'];
			$arrayvalue[] = $league_id."|".$league_title."|".$league_admin_fname." ".$league_admin_lname."|".$league_start_date."|".$league_end_date."|".$league_company_name;
		}
	}
	$result['leaguedetails'] = $arrayvalue;
	$result['counter'] = $iCountRecords2;
	echo json_encode($result);
}
?>