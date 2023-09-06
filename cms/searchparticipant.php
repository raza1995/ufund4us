<?

require_once("../configuration/dbconfig.php");
if(!$_SESSION['uid'])
{
	$oregister->redirect('../sign-in.php');
}
$uid = $_POST['uid'];
$cid = $_POST['cid'];
$query = $_POST['query'];

$aParticipantDetail = $oCampaign->getparticipantsearched($cid, $uid, $query);

$result['uid'] = $uid;
$result['cid'] = $cid;
$result['query'] = $query;
$result['searched'] = $aParticipantDetail;
echo json_encode($result);

?>