<?
require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
$uid = $_POST['uid'];
$cid = $_POST['cid'];
$query = $_POST['query'];

$aDonorDetail = $oCampaign->getdonorsearched($cid, $uid, $query);

$result['uid'] = $uid;
$result['cid'] = $cid;
$result['query'] = $query;
$result['searched'] = $aDonorDetail;
echo json_encode($result);

?>