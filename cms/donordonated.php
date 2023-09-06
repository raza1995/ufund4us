<?
require_once("../configuration/dbconfig.php");
$REQUEST = &$_REQUEST;
$did = isset($REQUEST['did']) ? $REQUEST['did'] : 0;
$result['donorsdetails'] = array();
$result['counter'] = 0;

if($did <= 0){
	$result["msg"] = "Please provide donor id!";
	echo json_encode($result);
	die();
}

$iCountRecords3 = 0;
$donorssdetails = $oCampaign->donors_donated($did);
$iCountRecords3 = count($donorssdetails);
if($iCountRecords3>0){
	for($l=0;$l<$iCountRecords3;$l++){
		
		$cname = $donorssdetails[$l]['fld_cname'];
		$campaign_title = $donorssdetails[$l]['fld_campaign_title'];
		$pname = $donorssdetails[$l]['pname'];
		$plname = $donorssdetails[$l]['plname'];
		$uname = $donorssdetails[$l]['uname'];
		$ulname = $donorssdetails[$l]['ulname'];
		$uemail = $donorssdetails[$l]['uemail'];
		$uphone = $donorssdetails[$l]['uphone'];
		//New Fields
		$donationfname = $donorssdetails[$l]['donationfname'];
		$donationlname = $donorssdetails[$l]['donationlname'];
		$donationemail = $donorssdetails[$l]['donationemail'];
		$pemail = $donorssdetails[$l]['pemail'];
		//New Fields
		$donation_amount = $donorssdetails[$l]['donation_amount'];
		$donationdate = $donorssdetails[$l]['donationdate'];
		$transactionno = str_pad($donorssdetails[$l]['transactionno'],10,"0",STR_PAD_LEFT);
		$arrayvalue[] = $cname."|".$campaign_title."|".$pname."|".$plname."|".$uname."|".$ulname."|".$uemail."|".$uphone."|".$donation_amount."|".$transactionno."|".$donationdate."|".$donationfname."|".$donationlname."|".$donationemail."|".$pemail;
	}
} 
else {
	$donorssdetails1 = $oCampaign->donors_donated1($did);
	$iCountRecords3 = count($donorssdetails1);
	if($iCountRecords3>0){
		for($l=0;$l<$iCountRecords3;$l++){
			$cname = $donorssdetails1[$l]['fld_cname'];
			$campaign_title = $donorssdetails1[$l]['fld_campaign_title'];
			$pname = $donorssdetails1[$l]['pname'];
			$plname = $donorssdetails1[$l]['plname'];
			$uname = $donorssdetails1[$l]['uname'];
			$ulname = $donorssdetails1[$l]['ulname'];
			$uemail = $donorssdetails1[$l]['uemail'];
			$uphone = $donorssdetails1[$l]['uphone'];
			//New Fields
			$donationfname = '';
			$donationlname = '';
			$donationemail = '';
			$pemail = $donorssdetails1[$l]['pemail'];
			//New Fields
			$donation_amount = '';
			$donationdate = '';
			$transactionno = '';
			$arrayvalue[] = $cname."|".$campaign_title."|".$pname."|".$plname."|".$uname."|".$ulname."|".$uemail."|".$uphone."|".$donation_amount."|".$transactionno."|".$donationdate."|".$donationfname."|".$donationlname."|".$donationemail."|".$pemail;
		}
	}
}
$result['donorsdetails'] = $arrayvalue;
$result['counter'] = $iCountRecords3;
echo json_encode($result);
?>