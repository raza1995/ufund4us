<?
require_once("../configuration/dbconfig.php");
$mode = $_POST['mode'];
$cid = $_POST['cid'];
$cuid = $_POST['cuid'];
$pid = $_POST['pid'];
$did = $_POST['did'];
$uphone = $_POST['uphone'];

if ($mode == 1) //Participants
{ //Participants
	if ($cid > 0 && $pid > 0 && $cuid > 0 && ($uphone != '' && $uphone != '___-___-____' && $uphone != '000-000-0000'))
	{
		$Participants_Exec = $oCampaign->update_participant_details($cid,$cuid,$pid,$uphone);
		if ($Participants_Exec) {
			$result['status'] = 1;
			echo $uphone;
			echo "<br>";
			echo '<a class="editable-phone-number editable-css" id="'.$pid.'" phone="'.$uphone.'" data-test="yep" role="button" tabindex="0" title="Click here to enter phone#">Enter Phone#</a>';
		} else {
			$result['status'] = 0;
		}
	} else {
		$result['status'] = 0;
	}	
}
if ($mode == 2) //Donors
{ //Participants
	if ($cid > 0 && $pid > 0 && $did > 0 && ($uphone != '' && $uphone != '___-___-____' && $uphone != '000-000-0000'))
	{
		$Donors_Exec = $oCampaign->update_donors_details($cid,$pid,$did,$uphone);
		if ($Donors_Exec) {
			$result['status'] = 1;
			echo $uphone;
			echo "<br>";
			echo '<a class="editable-phone-number editable-css" id="'.$did.'" phone="'.$uphone.'" data-test="yep" role="button" tabindex="0" title="Click here to enter phone#">Enter Phone#</a>';
		} else {
			$result['status'] = 0;
		}
	} else {
		$result['status'] = 0;
	}	
}
//echo json_encode($result);

?>