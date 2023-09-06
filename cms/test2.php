<?
require_once("../configuration/dbconfig.php");


if (isset($_GET['pid']) && isset($_GET['cid']) && isset($_GET['hash']) && isset($_GET['action']) && $_GET['action'] == 'sms_resent') {

	$hash = $_GET['hash'];
	$pid = $_GET['pid'];
	$cid = $_GET['cid'];
	$sms_sent_id = "";

	//Campaign Details
	$aCampaignDetail = $oCampaign->getcampaigndetail($cid);
	$ctitle = $aCampaignDetail['fld_campaign_title'];
	$cuid = $aCampaignDetail['fld_uid'];
	$cname = $aCampaignDetail['fld_cname'];
	$clname = $aCampaignDetail['fld_clname'];

	echo 'aCampaignDetail=<pre>'; print_r($aCampaignDetail); echo '</pre>';

	//Participant Details
	$aParticipantDetail = $oCampaign->getselectedparticipantdetails3($cid,$pid);
	echo 'aParticipantDetail=<pre>'; print_r($aParticipantDetail); echo '</pre>'; 
	die();
}
?>