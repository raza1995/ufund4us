<?
require_once("../configuration/dbconfig.php");
$pid = $_POST['pid'];
$campid = $_POST['campid'];
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
		$arrayvalue[] = $donorfname."|".$donorlname."|".$donoremail."|".$donorphone."|".$donation_amount;
	}
}
$result['donorfname'] = $donorfname;
$result['donorlname'] = $donorlname;
$result['donoremail'] = $donoremail;
$result['donorphone'] = $donorphone;
$result['donation_amount'] = $donation_amount;
$result['donorssdetails'] = $arrayvalue;
$result['counter'] = $iCountRecords3;
echo json_encode($result);
?>