<?
require_once("../configuration/dbconfig.php");
$phone = $_POST['phone'];
$campid = $_POST['cid'];
$uid = $_POST['uid'];
$act = $_POST['act'];

if ($act == 1) { //Participants Check
	$iCountRecords3 = 0;
	$chkphonedetails = $oregister->checkparticipantphone($phone, $campid, $uid);
	$iCountRecords = count($chkphonedetails);
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
	$chkphonedetails = $oregister->checkdonorphone($phone, $campid, $uid);
	$iCountRecords = count($chkphonedetails);
	if($iCountRecords>0){
		$iCountRecords3 = 1;
	} else {
		$iCountRecords3 = 0;
	}
	$result['get_results'] = $iCountRecords3;
	echo json_encode($result);
}
?>