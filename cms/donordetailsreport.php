<?
require_once("../configuration/dbconfig.php");
$pid = $_POST['pid'];
$act = $_POST['act'];
if ($act == 1) {
$iCountRecords2 = 0;
$participantsdetails = $oCampaign->participants_details3($pid);
$iCountRecords2 = count($participantsdetails);
if($iCountRecords2>0){
	for($l=0;$l<$iCountRecords2;$l++){
		
		$campaign_id = $participantsdetails[$l]['fld_campaign_id'];
		$campaign_title = $participantsdetails[$l]['fld_campaign_title'];
		$campaign_cname = $participantsdetails[$l]['fld_cname'];
		$campaign_donor_size = $participantsdetails[$l]['fld_donor_size'];
		$campaign_donor_upload = $donorssdetails[$l]['donoruploaded'];
		$campaign_donation_amt = $donorssdetails[$l]['donation_amount'];
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
$pid = $_POST['pid'];
$campid = $_POST['cid'];
$iCountRecords3 = 0;
$donorssdetails = $oCampaign->donors_details2($pid, $campid);
$iCountRecords3 = count($donorssdetails);
if($iCountRecords3>0){
	for($l=0;$l<$iCountRecords3;$l++){
		
		$donorfname = $donorssdetails[$l]['ufname'];
		$donorlname = $donorssdetails[$l]['ulname'];
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
?>