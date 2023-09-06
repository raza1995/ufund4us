<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
$cid = checkAndReturnOnly($REQUEST, 'cid');
$act = checkAndReturnOnly($REQUEST, 'act');

$arrayvalue = [];

if ($act == 1) {
  $iCountRecords3 = 0;
  $donationdetails = $oCampaign->donations_list($cid);
  $iCountRecords3 = count($donationdetails);
  if($iCountRecords3>0){
  	  for($l=0;$l<$iCountRecords3;$l++){
		$campaign_title = str_replace(",", "&#44;", $donationdetails[$l]['fld_campaign_title']);
		$donorfname = str_replace(",", "&#44;", $donationdetails[$l]['donorfname']);
		$donorlname = str_replace(",", "&#44;", $donationdetails[$l]['donorlname']);
		$donoremail = $donationdetails[$l]['donoremail'];
		$participantfname = str_replace(",", "&#44;", $donationdetails[$l]['participantfname']);
		$participantlname = str_replace(",", "&#44;", $donationdetails[$l]['participantlname']);
		$participantemail = $donationdetails[$l]['participantemail'];
		$donation_amount = $donationdetails[$l]['donation_amount'];
		$transactionnumber = str_pad($donationdetails[$l]['transactionnumber'],10,"0",STR_PAD_LEFT);
		$tid = $donationdetails[$l]['tid'];
		$creationdate = date('m/d/Y h:i:s',strtotime($donationdetails[$l]['creationdate']));
		$arrayvalue[] = $campaign_title."|".$donorfname."|".$donorlname."|".$donoremail."|".$participantfname."|".$participantlname."|".$participantemail."|".$donation_amount."|".$transactionnumber."|".$creationdate."|".$tid;
	  }
  }
  $result['donationdetails'] = $arrayvalue;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}
if ($act == 2) {
  $iCountRecords3 = 0;
  $donorparticipantdetails = $oCampaign->donorparticipant_list($cid);
  $iCountRecords3 = count($donorparticipantdetails);
  if($iCountRecords3>0){
  	  for($l=0;$l<$iCountRecords3;$l++){
		$campaign_title = str_replace(",", "&#44;", $donorparticipantdetails[$l]['fld_campaign_title']);
		$donorfname = str_replace(",", "&#44;", $donorparticipantdetails[$l]['donorfname']);
		$donorlname = str_replace(",", "&#44;", $donorparticipantdetails[$l]['donorlname']);
		$donoremail = $donorparticipantdetails[$l]['donoremail'];
		$donorsentemail = $donorparticipantdetails[$l]['donorsentemail'];
		$donorread = $donorparticipantdetails[$l]['donorread'];
		$participantfname = str_replace(",", "&#44;", $donorparticipantdetails[$l]['participantfname']);
		$participantlname = str_replace(",", "&#44;", $donorparticipantdetails[$l]['participantlname']);
		$participantemail = $donorparticipantdetails[$l]['participantemail'];
		$arrayvalue[] = $campaign_title."|".$donorfname."|".$donorlname."|".$donoremail."|".$donorsentemail."|".$donorread."|".$participantfname."|".$participantlname."|".$participantemail;
	  }
  }
  $result['participantdonordetails'] = $arrayvalue;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}
if ($act == 3) {
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
  //End Get SparkPost Bounces Email
  $iCountRecords3 = 0;
  $participantenrolleddetails = $oCampaign->participant_enrolled2($cid);
  $iCountRecords3 = count($participantenrolleddetails);
  if($iCountRecords3>0){
  	  for($l=0;$l<$iCountRecords3;$l++){
		$uid = $participantenrolleddetails[$l]['uid'];
		//Get Donors Details
		$BadEmailCounter = 0;
		$BadEmailDetail = $oCampaign->getbademails($uid, $cid);
		$BadEmailDetailCount = count($BadEmailDetail);
		if( isset($array_bounce['results']) ){
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
		}
		$participantname1 = $participantenrolleddetails[$l]['uname']." ".$participantenrolleddetails[$l]['ulname'];
		$participantname = str_replace(",", "&#44;", $participantname1);
		$donorrequire = $participantenrolleddetails[$l]['donorrequire'];
		$donorupload = $participantenrolleddetails[$l]['donorupload'];
		//$donorpercentage = ($donorupload / $donorrequire) * 100;
		$BadEmails = $BadEmailCounter;
		//New Field
		$is_unsubscribe = $participantenrolleddetails[$l]['is_unsubscribe'];
		$donorunsubscribe = $participantenrolleddetails[$l]['donorunsubscribe'];
		//New Field
		$noofdonations = $participantenrolleddetails[$l]['donation_amount'];
		$totaldonations = $participantenrolleddetails[$l]['sumofdonations'];
		$donationpercentage = 0;
		if($totaldonations != 0){
			$donationpercentage = $totaldonations / $noofdonations;
		}
		$participantgoal = $participantenrolleddetails[$l]['participantgoal'];
		$moneyraised = number_format($participantenrolleddetails[$l]['sumofdonations'], 2, '.', '');
		$participentgoalpercentage = number_format(($moneyraised / $participantgoal) * 100, 1, '.', '');
		$fld_image = $participantenrolleddetails[$l]['fld_image'];
		$arrayvalue[] = $participantname."|".$donorrequire."|".$donorupload."|".$BadEmails."|".$noofdonations."|".$donationpercentage."|".$participantgoal."|".$moneyraised."|".$fld_image."|".$is_unsubscribe."|".$donorunsubscribe;
	  }
  }
  $result['participantenrolleddetails'] = $arrayvalue;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}

if ($act == 4) { //Unsubscribed
  $iCountRecords3 = 0;
  $unsubscribed_donors_details = $oCampaign->unsubscribed_donors($cid);
  $iCountRecords3 = count($unsubscribed_donors_details);
  if($iCountRecords3>0){
  	  for($l=0;$l<$iCountRecords3;$l++){
		$campaign_title = str_replace(",", "&#44;", $unsubscribed_donors_details[$l]['fld_campaign_title']);
		$donorfname = str_replace(",", "&#44;", $unsubscribed_donors_details[$l]['donorfname']);
		$donorlname = str_replace(",", "&#44;", $unsubscribed_donors_details[$l]['donorlname']);
		$donoremail = $unsubscribed_donors_details[$l]['donoremail'];
		$donorsentemail = $unsubscribed_donors_details[$l]['donorsentemail'];
		$donorread = $unsubscribed_donors_details[$l]['donorread'];
		$participantfname = str_replace(",", "&#44;", $unsubscribed_donors_details[$l]['participantfname']);
		$participantlname = str_replace(",", "&#44;", $unsubscribed_donors_details[$l]['participantlname']);
		$participantemail = $unsubscribed_donors_details[$l]['participantemail'];
		//New Field
		$is_unsubscribe = $unsubscribed_donors_details[$l]['is_unsubscribe'];
		$is_unsubscribe_date = $unsubscribed_donors_details[$l]['unsubscribe_date'];
		$arrayvalue[] = $campaign_title."|".$donorfname."|".$donorlname."|".$donoremail."|".$donorsentemail."|".$donorread."|".$participantfname."|".$participantlname."|".$participantemail."|".$is_unsubscribe."|".$is_unsubscribe_date;
	  }
  }
  $result['unsubscribeddonordetails'] = $arrayvalue;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}

if ($act == 5) { //# of Participants Enrolled for AB1575
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
  //End Get SparkPost Bounces Email
  $iCountRecords3 = 0;
  $participantenrolleddetails = $oCampaign->participant_enrolled($cid);
  $iCountRecords3 = count($participantenrolleddetails);
  if($iCountRecords3>0){
	  $participant_count = 0;
	  $unsubscribe_count = 0;
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
			$participantgoal += number_format($participantenrolleddetails[$l]['participantgoal'], 2, '.', '');
		}
		if ($participantenrolleddetails[$l]['sumofdonations'] != '') {
			$moneyraised += number_format($participantenrolleddetails[$l]['sumofdonations'], 2, '.', '');
		}
	  }
	  $donorsemailgoal = $participant_count * $donorrequire;
	  $participentdonorupload = round(($donorupload / $donorsemailgoal) * 100);
	  $arrayvalue[] = $participant_count."|".$donorrequire."|".$donorsemailgoal."|".$donorupload."|".$participentdonorupload."|".$unsubscribe_count."|".$BadEmails."|".$participantgoal."|".$moneyraised;
  }
  $result['participantenrolleddetails'] = $arrayvalue;
  $result['counter'] = $iCountRecords3;
  echo json_encode($result);
}
?>