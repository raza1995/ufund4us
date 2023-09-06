<?
require_once("../configuration/dbconfig.php");
$email = $_POST['email'];
$campid = $_POST['cid'];
$uid = $_POST['uid'];
$iCountRecords3 = 0;
$chkemaildetails = $oregister->checkemail2($email, $campid, $uid);
$iCountRecords = count($chkemaildetails);
if($iCountRecords>0){
	$iCountRecords3 = 1;
} else {
	$iCountRecords3 = 0;
}
$result['get_results'] = $iCountRecords3;
echo json_encode($result);
?>