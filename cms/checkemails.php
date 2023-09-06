<?
require_once("../configuration/dbconfig.php");
$email = $_POST['email'];
$campid = $_POST['cid'];
$uid = $_POST['uid'];
$act = $_POST['act'];

if ($act == 1) { //Participants Check
	$iCountRecords3 = 0;
	$chkemaildetails = $oregister->checkparticipantemail($email, $campid, $uid);
	$iCountRecords = count($chkemaildetails);
	if($iCountRecords>0){
		$iCountRecords3 = 1;
	} else {
		$iCountRecords3 = 0;
	}
	$result['get_results'] = $iCountRecords3;
	echo json_encode($result);
}
if ($act == 2) { //Donors Check
	$iCountRecords3 = 0;
	$chkemaildetails = $oregister->checkdonoremail($email, $campid, $uid);
	$iCountRecords = count($chkemaildetails);
	if($iCountRecords>0){
		$iCountRecords3 = 1;
	} else {
		$iCountRecords3 = 0;
	}
	$result['get_results'] = $iCountRecords3;
	echo json_encode($result);
}
?>